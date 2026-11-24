<?php

namespace App\Http\Requests\V1\Products\WarehouseLocations;

use App\Enums\Products\WarehouseLocations\ProductWarehouseLocationStatus;
use App\Enums\Products\WarehouseLocations\ProductWarehouseLocationType;
use App\Models\ProductWarehouseLocation;
use App\Models\ProductWarehouseLocationTranslation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

/**
 * Requête de validation pour la création d'un emplacement d'entrepôt.
 *
 * Règles métier :
 *  - `code` et `sort_order` sont uniques PAR ENTREPÔT (corbeille incluse) ;
 *  - le `name` d'une traduction est unique PAR ENTREPÔT ET PAR LANGUE (insensible à la casse) ;
 *  - `parent_id` doit appartenir au même entrepôt et ne pas être supprimé ;
 *  - un emplacement `active` exige un entrepôt `active`.
 */
class StoreProductWarehouseLocationRequest extends FormRequest
{
    /**
     * Chemin de base pour les traductions des messages de validation.
     */
    protected string $backendLangPath = 'v1/backend/views/pages/products/warehouse-locations/messages/warehouse-locations-messages-validation.warehouseLocations';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // ← géré par middleware
    }

    public function rules(): array
    {
        // Entrepôt visé (sert de périmètre aux unicités code / sort_order)
        $warehouseId = (int) $this->input('warehouse_id');

        return [
            // L'entrepôt de rattachement est obligatoire
            'warehouse_id' => ['required', 'integer', 'exists:product_warehouses,id'],

            // Emplacement parent optionnel (null = emplacement racine)
            'parent_id' => ['nullable', 'integer', 'exists:product_warehouse_locations,id'],

            'type' => ['required', Rule::in(ProductWarehouseLocationType::values())],
            'status' => ['required', Rule::in(ProductWarehouseLocationStatus::values())],

            // Le code est unique dans l'entrepôt (corbeille incluse)
            'code' => ['required', 'string', 'max:30', 'regex:/^[A-Z0-9_-]+$/', Rule::unique('product_warehouse_locations', 'code')->where(fn ($query) => $query->where('warehouse_id', $warehouseId))],

            // L'ordre d'affichage est unique dans l'entrepôt (corbeille incluse)
            'sort_order' => ['required', 'integer', 'min:0', Rule::unique('product_warehouse_locations', 'sort_order')->where(fn ($query) => $query->where('warehouse_id', $warehouseId))],

            // Limite métier pilotée par la table settings (aucune valeur codée en dur)
            'notes' => ['nullable', 'string', 'max:'.(int) getRequiredSettingValue('max_product_warehouse_notes_length', app()->getLocale())],

            // Traductions obligatoires
            'translations' => ['required', 'array'],
            'translations.*.lang_id' => ['required', 'exists:langs,id'],
            'translations.*.name' => ['required', 'string', 'max:150'],
            'translations.*.description' => ['required', 'string'],
        ];
    }

    /**
     * Configure le validateur avec des vérifications complémentaires.
     */
    public function withValidator($validator): void
    {
        // Vérifications supplémentaires après les règles de base
        $validator->after(function ($validator): void {

            // Récupère les traductions soumises (ou tableau vide si absent)
            $translations = $this->input('translations', []);

            // Si les traductions ne sont pas un tableau, on ne peut pas faire de vérifications supplémentaires
            if (! is_array($translations)) {
                return;
            }

            // $warehouseId contient l'identifiant de l'entrepôt soumis (sert de périmètre aux vérifications)
            $warehouseId = (int) $this->input('warehouse_id');

            // Une langue ne peut pas apparaître plusieurs fois dans les traductions
            $langIds = collect($translations)->pluck('lang_id')->filter();

            if ($langIds->count() !== $langIds->unique()->count()) {
                $validator->errors()->add('translations', __("{$this->backendLangPath}.translations.lang_id.duplicate"));
            }

            // Parent du même entrepôt, non supprimé, sans cycle
            $this->validateParent($validator, $warehouseId, null);

            // Unicité du nom par entrepôt et par langue (corbeille incluse)
            $this->validateTranslationNames($validator, $translations, $warehouseId, null);
        });
    }

    /**
     * Vérifie que le parent proposé appartient au même entrepôt, n'est pas en corbeille
     * et ne crée pas de cycle (self / descendant).
     */
    private function validateParent($validator, int $warehouseId, ?int $locationId): void
    {
        // $parentId contient l'identifiant de l'emplacement parent soumis (ou null si absent)
        $parentId = $this->input('parent_id');

        // Si aucun parent n'est soumis, on ne fait pas de vérification supplémentaire
        if ($parentId === null || $parentId === '') {
            return;
        }

        // $parent contient l'emplacement parent soumis (ou null si introuvable)
        $parent = ProductWarehouseLocation::withTrashed()->find((int) $parentId);

        // Si le parent est introuvable, on ajoute une erreur de validation (la règle exists() ne suffit pas à cause de la corbeille)
        if (! $parent) {
            $validator->errors()->add('parent_id', __("{$this->backendLangPath}.parent_id.exists"));

            return;
        }

        // Si le parent est en corbeille, on ajoute une erreur de validation
        if ($parent->trashed()) {
            $validator->errors()->add('parent_id', __("{$this->backendLangPath}.parent_id.trashed"));

            return;
        }

        // Si le parent appartient à un entrepôt différent, on ajoute une erreur de validation
        if ((int) $parent->warehouse_id !== $warehouseId) {
            $validator->errors()->add('parent_id', __("{$this->backendLangPath}.parent_id.same_warehouse"));

            return;
        }

        // Si l'emplacement courant est soumis, on vérifie qu'il n'est pas le parent proposé
        if ($locationId === null) {
            return;
        }

        // Si l'emplacement courant est soumis et qu'il correspond au parent proposé, on ajoute une erreur de validation
        if ((int) $parent->id === $locationId) {
            $validator->errors()->add('parent_id', __("{$this->backendLangPath}.parent_id.self"));

            return;
        }

        // Remontée de la chaîne des parents : si on retombe sur l'emplacement courant,
        // le parent proposé est l'un de ses descendants (cycle).
        $ancestor = $parent;
        $guard = 0;

        // Profondeur maximale de remontée paramétrée dans la table settings
        $maxDepth = (int) getRequiredSettingValue('max_product_warehouse_location_parent_depth', app()->getLocale());

        while ($ancestor !== null && $guard < $maxDepth) {

            // Si l'ancêtre courant est l'emplacement courant, on a un cycle
            if ((int) $ancestor->id === $locationId) {

                $validator->errors()->add('parent_id', __("{$this->backendLangPath}.parent_id.cycle"));

                return;
            }

            // On remonte d'un niveau dans la hiérarchie
            $ancestor = $ancestor->parent_id ? ProductWarehouseLocation::withTrashed()->find($ancestor->parent_id) : null;

            $guard++;
        }
    }

    /**
     * Refuse un nom déjà utilisé dans le même entrepôt pour la même langue
     * (comparaison insensible à la casse, corbeille incluse).
     */
    private function validateTranslationNames($validator, array $translations, int $warehouseId, ?int $locationId): void
    {
        // On utilise un tableau pour détecter les doublons dans les traductions soumises (même langue, même nom)
        $seen = [];

        foreach ($translations as $key => $translation) {

            if (! is_array($translation)) {
                continue;
            }

            $langId = $translation['lang_id'] ?? null;
            $name = $translation['name'] ?? null;

            if (! $langId || $name === null || $name === '') {
                continue;
            }

            // $normalized contient le nom normalisé (minuscules, trim) pour la comparaison insensible à la casse
            $normalized = mb_strtolower(trim((string) $name));

            // $fingerprint contient la combinaison langue + nom normalisé pour détecter les doublons
            $fingerprint = (string) $langId.'|'.$normalized;

            // Si on a déjà vu cette combinaison langue + nom normalisé dans les traductions soumises, on ajoute une erreur de validation
            if (isset($seen[$fingerprint])) {
                $validator->errors()->add("translations.{$key}.name", __("{$this->backendLangPath}.translations.name.unique_in_warehouse"));

                continue;
            }

            // On marque cette combinaison langue + nom normalisé comme vue pour les prochaines itérations
            $seen[$fingerprint] = true;

            // Vérification dans la base de données : si un nom identique existe déjà pour la même langue et le même entrepôt (corbeille incluse)
            $exists = ProductWarehouseLocationTranslation::withTrashed()
                ->where('lang_id', $langId)
                ->whereRaw('LOWER(name) = ?', [$normalized])
                ->whereHas('location', fn ($query) => $query->withTrashed()->where('warehouse_id', $warehouseId))
                ->when($locationId !== null, fn ($query) => $query->where('location_id', '!=', $locationId))
                ->exists();

            // Si un nom identique existe déjà, on ajoute une erreur de validation
            if ($exists) {
                $validator->errors()->add("translations.{$key}.name", __("{$this->backendLangPath}.translations.name.unique_in_warehouse"));
            }
        }
    }

    public function messages(): array
    {
        return [
            'warehouse_id.required' => __("{$this->backendLangPath}.warehouse_id.required"),
            'warehouse_id.integer' => __("{$this->backendLangPath}.warehouse_id.integer"),
            'warehouse_id.exists' => __("{$this->backendLangPath}.warehouse_id.exists"),

            'parent_id.integer' => __("{$this->backendLangPath}.parent_id.integer"),
            'parent_id.exists' => __("{$this->backendLangPath}.parent_id.exists"),

            'type.required' => __("{$this->backendLangPath}.type.required"),
            'type.in' => __("{$this->backendLangPath}.type.in"),

            'status.required' => __("{$this->backendLangPath}.status.required"),
            'status.in' => __("{$this->backendLangPath}.status.in"),

            'code.required' => __("{$this->backendLangPath}.code.required"),
            'code.string' => __("{$this->backendLangPath}.code.string"),
            'code.max' => __("{$this->backendLangPath}.code.max"),
            'code.regex' => __("{$this->backendLangPath}.code.regex"),
            'code.unique' => __("{$this->backendLangPath}.code.unique"),

            'sort_order.required' => __("{$this->backendLangPath}.sort_order.required"),
            'sort_order.integer' => __("{$this->backendLangPath}.sort_order.integer"),
            'sort_order.min' => __("{$this->backendLangPath}.sort_order.min"),
            'sort_order.unique' => __("{$this->backendLangPath}.sort_order.unique"),

            'notes.string' => __("{$this->backendLangPath}.notes.string"),
            'notes.max' => __("{$this->backendLangPath}.notes.max", ['max' => (int) getRequiredSettingValue('max_product_warehouse_notes_length', app()->getLocale())]),

            'translations.required' => __("{$this->backendLangPath}.translations.required"),
            'translations.array' => __("{$this->backendLangPath}.translations.array"),

            'translations.*.lang_id.required' => __("{$this->backendLangPath}.translations.lang_id.required"),
            'translations.*.lang_id.exists' => __("{$this->backendLangPath}.translations.lang_id.exists"),

            'translations.*.name.required' => __("{$this->backendLangPath}.translations.name.required"),
            'translations.*.name.string' => __("{$this->backendLangPath}.translations.name.string"),
            'translations.*.name.max' => __("{$this->backendLangPath}.translations.name.max"),
            'translations.*.name.unique' => __("{$this->backendLangPath}.translations.name.unique_in_warehouse"),

            'translations.*.description.required' => __("{$this->backendLangPath}.translations.description.required"),
            'translations.*.description.string' => __("{$this->backendLangPath}.translations.description.string"),
        ];
    }
}
