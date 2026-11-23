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
 * Requête de validation pour la mise à jour d'un emplacement d'entrepôt.
 *
 * NOTE : `warehouse_id` et `code` sont volontairement absents des règles — ils ne peuvent
 * plus être modifiés après la création (identifiant métier de l'emplacement).
 */
class UpdateProductWarehouseLocationRequest extends FormRequest
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

    /**
     * Emplacement courant résolu par le model binding (route {productWarehouseLocation}).
     */
    protected function getLocationId(): ?int
    {
        return $this->route('productWarehouseLocation')?->id;
    }

    public function rules(): array
    {
        $locationId = $this->getLocationId();
        $warehouseId = (int) $this->route('productWarehouseLocation')?->warehouse_id;

        return [
            // Emplacement parent optionnel (null = emplacement racine)
            'parent_id' => ['nullable', 'integer', 'exists:product_warehouse_locations,id'],

            'type' => ['required', Rule::in(ProductWarehouseLocationType::values())],
            'status' => ['required', Rule::in(ProductWarehouseLocationStatus::values())],

            // L'ordre d'affichage reste unique dans l'entrepôt, en excluant l'emplacement courant
            'sort_order' => ['required', 'integer', 'min:0', Rule::unique('product_warehouse_locations', 'sort_order')->where(fn ($query) => $query->where('warehouse_id', $warehouseId))->ignore($locationId)],

            // Limite métier pilotée par la table settings (aucune valeur codée en dur)
            'notes' => ['nullable', 'string', 'max:'.(int) getRequiredSettingValue('max_product_warehouse_notes_length', app()->getLocale())],

            // Traductions obligatoires
            'translations' => ['required', 'array'],
            'translations.*.lang_id' => ['required', 'exists:langs,id'],
            'translations.*.name' => ['required', 'string', 'max:150'],
            'translations.*.description' => ['nullable', 'string'],
        ];
    }

    /**
     * Configure le validateur avec des vérifications complémentaires.
     */
    public function withValidator($validator): void
    {
        $locationId = $this->getLocationId();
        $warehouseId = (int) $this->route('productWarehouseLocation')?->warehouse_id;

        $validator->after(function ($validator) use ($locationId, $warehouseId): void {

            // Récupération des traductions soumises (tableau d'objets)
            $translations = $this->input('translations', []);

            if (! is_array($translations)) {
                return;
            }

            // Une langue ne peut pas apparaître plusieurs fois dans les traductions
            $langIds = collect($translations)->pluck('lang_id')->filter();

            if ($langIds->count() !== $langIds->unique()->count()) {
                $validator->errors()->add('translations', __("{$this->backendLangPath}.translations.lang_id.duplicate"));
            }

            // Parent du même entrepôt, non supprimé, sans cycle
            $this->validateParent($validator, $warehouseId, $locationId);

            // Unicité du nom par entrepôt et par langue (corbeille incluse, hors emplacement courant)
            $this->validateTranslationNames($validator, $translations, $warehouseId, $locationId);
        });
    }

    /**
     * Vérifie que le parent proposé appartient au même entrepôt, qu'il n'est pas
     * en corbeille et que le rattachement ne crée pas de cycle (self / descendant).
     */
    private function validateParent($validator, int $warehouseId, ?int $locationId): void
    {
        $parentId = $this->input('parent_id');

        if ($parentId === null || $parentId === '') {
            return;
        }

        $parent = ProductWarehouseLocation::withTrashed()->find((int) $parentId);

        if (! $parent) {
            $validator->errors()->add('parent_id', __("{$this->backendLangPath}.parent_id.exists"));

            return;
        }

        if ($parent->trashed()) {
            $validator->errors()->add('parent_id', __("{$this->backendLangPath}.parent_id.trashed"));

            return;
        }

        if ((int) $parent->warehouse_id !== $warehouseId) {
            $validator->errors()->add('parent_id', __("{$this->backendLangPath}.parent_id.same_warehouse"));

            return;
        }

        if ($locationId === null) {
            return;
        }

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
            if ((int) $ancestor->id === $locationId) {
                $validator->errors()->add('parent_id', __("{$this->backendLangPath}.parent_id.cycle"));

                return;
            }

            $ancestor = $ancestor->parent_id
                ? ProductWarehouseLocation::withTrashed()->find($ancestor->parent_id)
                : null;

            $guard++;
        }
    }

    /**
     * Refuse un nom déjà utilisé dans le même entrepôt pour la même langue
     * (comparaison insensible à la casse, corbeille incluse, hors emplacement courant).
     */
    private function validateTranslationNames($validator, array $translations, int $warehouseId, ?int $locationId): void
    {
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

            $normalized = mb_strtolower(trim((string) $name));
            $fingerprint = (string) $langId.'|'.$normalized;

            if (isset($seen[$fingerprint])) {
                $validator->errors()->add(
                    "translations.{$key}.name",
                    __("{$this->backendLangPath}.translations.name.unique_in_warehouse"),
                );

                continue;
            }

            $seen[$fingerprint] = true;

            $exists = ProductWarehouseLocationTranslation::withTrashed()
                ->where('lang_id', $langId)
                ->whereRaw('LOWER(name) = ?', [$normalized])
                ->whereHas('location', fn ($query) => $query->withTrashed()->where('warehouse_id', $warehouseId))
                ->when($locationId !== null, fn ($query) => $query->where('location_id', '!=', $locationId))
                ->exists();

            if ($exists) {
                $validator->errors()->add(
                    "translations.{$key}.name",
                    __("{$this->backendLangPath}.translations.name.unique_in_warehouse"),
                );
            }
        }
    }

    public function messages(): array
    {
        return [
            'parent_id.integer' => __("{$this->backendLangPath}.parent_id.integer"),
            'parent_id.exists' => __("{$this->backendLangPath}.parent_id.exists"),

            'type.required' => __("{$this->backendLangPath}.type.required"),
            'type.in' => __("{$this->backendLangPath}.type.in"),

            'status.required' => __("{$this->backendLangPath}.status.required"),
            'status.in' => __("{$this->backendLangPath}.status.in"),

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

            'translations.*.description.string' => __("{$this->backendLangPath}.translations.description.string"),
        ];
    }
}
