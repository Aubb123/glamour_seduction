<?php

namespace App\Http\Requests\V1\Products\Brands;

use App\Enums\Products\Brands\ProductBrandStatus;
use App\Models\ProductBrand;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

/**
 * Requête de validation pour la mise à jour d'une marque de produit.
 *
 * Valide les données entrantes pour la modification d'une marque (product_brands)
 * avec ses traductions associées (product_brand_translations).
 * Gère l'unicité de sort_order en excluant la marque en cours de modification.
 */
class UpdateProductBrandRequest extends FormRequest
{
    /**
     * Chemin de base pour les traductions des messages de validation.
     */
    protected string $backendLangPath = 'v1/backend/views/pages/products/brands/messages/brands-messages-validation.productBrands';

    /**
     * ID résolu de la marque à partir de l'UUID de la route.
     * Utilisé pour exclure la marque courante des règles d'unicité.
     */
    protected ?int $productBrandId = null;

    /**
     * Résolution de l'ID de la marque avant la validation.
     * La marque est déjà résolue par le binding implicite de la route {productBrand}
     * (clé = uuid via ProductBrand::getRouteKeyName()).
     */
    protected function prepareForValidation(): void
    {
        /** @var ProductBrand|null $productBrand */
        $productBrand = $this->route('productBrand');

        // Sécurité : si le paramètre n'a pas été injecté comme modèle, on renvoie une 404.
        if (! $productBrand instanceof ProductBrand) {
            abort(404);
        }

        // On stocke l'ID de la marque pour l'utiliser dans les règles d'unicité.
        $this->productBrandId = $productBrand->id;
    }

    public function authorize(): bool
    {
        return true; // ← géré par middleware
    }

    /**
     * Règles de validation.
     *
     * NOTE IMPORTANTE (même approche que StoreProductBrandRequest) :
     * La syntaxe statique 'unique:...,lang_id,{translations.*.lang_id}' ne
     * fonctionne PAS — Laravel traite "{translations.*.lang_id}" comme une
     * chaîne littérale, pas comme une référence dynamique au champ frère.
     * On reconstruit ces règles dynamiquement en PHP, avec un vrai closure
     * qui reçoit la vraie valeur de lang_id de chaque ligne de traduction.
     */
    public function rules(): array
    {
        $productBrandId = $this->productBrandId;

        $rules = [
            // sort_order doit être unique, sauf pour LA marque qu'on modifie
            // elle-même (sinon impossible de sauvegarder sans changer sa valeur).
            'sort_order' => ['required', 'integer', 'min:0', 'unique:product_brands,sort_order,'.$productBrandId],

            // status doit être l'une des valeurs définies dans l'énumération ProductBrandStatus
            'status' => ['required', Rule::in(ProductBrandStatus::values())],

            // cloud_platform_uuid est optionnel, mais s'il est fourni, il doit exister dans la table cloud_platforms
            'cloud_platform_uuid' => ['nullable', 'exists:cloud_platforms,uuid'],

            // 'nullable' ici car en update, on ne réenvoie pas forcément une
            // nouvelle image si l'utilisateur ne veut pas la changer.
            'image' => ['nullable', 'image', 'mimes:jpg,png,webp', 'max:2048'], // 2 Mo

            // Traductions : tableau obligatoire, chaque élément doit avoir un lang_id existant et une description valide
            'translations' => ['required', 'array'],

            // Chaque traduction doit avoir un lang_id existant et une description valide
            'translations.*.lang_id' => ['required', 'exists:langs,id'],

            // Chaque traduction doit avoir une description valide (obligatoire, chaîne de caractères, max 1000 caractères)
            'translations.*.description' => ['required', 'string', 'max:1000'],
        ];

        // ────────────────────────────────────────────────────────────
        // Génération dynamique des règles "unique par langue", en
        // excluant la marque courante de la vérification.
        // ────────────────────────────────────────────────────────────
        $translations = $this->input('translations', []);

        foreach ($translations as $index => $translation) {
            // lang_id RÉEL de cette ligne précise (pas un placeholder texte).
            $langId = $translation['lang_id'] ?? null;

            // Règle unique dynamique pour "name" :
            // → unique parmi les traductions ayant le MÊME lang_id que cette
            //   ligne, MAIS en excluant celles qui appartiennent déjà à la
            //   marque en cours de modification.
            $rules["translations.{$index}.name"] = [
                'required',
                'string',
                'max:255',
                Rule::unique('product_brand_translations', 'name')
                    ->where(fn ($query) => $query->where('lang_id', $langId))
                    ->ignore($productBrandId, 'brand_id')
                    // Ignore aussi les lignes soft-deleted (cohérent avec le
                    // SoftDeletes de la table) : un nom "libéré" par une
                    // suppression logique ne doit pas bloquer la sauvegarde.
                    ->whereNull('deleted_at'),
            ];

        }

        return $rules;
    }

    /**
     * Messages de validation personnalisés.
     */
    public function messages(): array
    {
        return [
            'sort_order.required' => __("{$this->backendLangPath}.sort_order.required"),
            'sort_order.integer' => __("{$this->backendLangPath}.sort_order.integer"),
            'sort_order.min' => __("{$this->backendLangPath}.sort_order.min"),
            'sort_order.unique' => __("{$this->backendLangPath}.sort_order.unique"),

            'status.required' => __("{$this->backendLangPath}.status.required"),
            'status.in' => __("{$this->backendLangPath}.status.in"),

            'cloud_platform_uuid.exists' => __("{$this->backendLangPath}.cloud_platform_uuid.exists"),

            'image.image' => __("{$this->backendLangPath}.image.image"),
            'image.mimes' => __("{$this->backendLangPath}.image.mimes"),
            'image.max' => __("{$this->backendLangPath}.image.max"),

            'translations.required' => __("{$this->backendLangPath}.translations.required"),
            'translations.array' => __("{$this->backendLangPath}.translations.array"),

            'translations.*.lang_id.required' => __("{$this->backendLangPath}.translations.lang_id.required"),
            'translations.*.lang_id.exists' => __("{$this->backendLangPath}.translations.lang_id.exists"),

            'translations.*.name.required' => __("{$this->backendLangPath}.translations.name.required"),
            'translations.*.name.string' => __("{$this->backendLangPath}.translations.name.string"),
            'translations.*.name.max' => __("{$this->backendLangPath}.translations.name.max"),
            'translations.*.name.unique' => __("{$this->backendLangPath}.translations.name.unique"),

            'translations.*.description.required' => __("{$this->backendLangPath}.translations.description.required"),
            'translations.*.description.string' => __("{$this->backendLangPath}.translations.description.string"),
            'translations.*.description.max' => __("{$this->backendLangPath}.translations.description.max"),
        ];
    }
}
