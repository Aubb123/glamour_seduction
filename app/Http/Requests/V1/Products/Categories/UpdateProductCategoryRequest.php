<?php

namespace App\Http\Requests\V1\Products\Categories;

use App\Enums\Products\Categories\ProductCategoryStatus;
use App\Models\ProductCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

/**
 * Requête de validation pour la mise à jour d'une catégorie de produit.
 *
 * Valide les données entrantes pour la modification d'une catégorie (product_categories)
 * avec ses traductions associées (product_category_translations).
 * Gère l'unicité de sort_order en excluant la catégorie en cours de modification.
 */
class UpdateProductCategoryRequest extends FormRequest
{
    /**
     * Chemin de base pour les traductions des messages de validation.
     */
    protected string $backendLangPath = 'v1/backend/views/pages/products/categories/messages/categories-messages-validation.productCategories';

    /**
     * ID résolu de la catégorie à partir de l'UUID de la route.
     * Utilisé pour exclure la catégorie courante des règles d'unicité.
     */
    protected ?int $productCategoryId = null;

    /**
     * Résolution de l'ID de la catégorie avant la validation.
     * La catégorie est déjà résolue par le binding implicite de la route {productCategory}
     * (clé = uuid via ProductCategory::getRouteKeyName()).
     */
    protected function prepareForValidation(): void
    {
        /** @var ProductCategory|null $productCategory */
        $productCategory = $this->route('productCategory');

        // Sécurité : si le paramètre n'a pas été injecté comme modèle, on renvoie une 404.
        if (! $productCategory instanceof ProductCategory) {
            abort(404);
        }

        $this->productCategoryId = $productCategory->id;
    }

    public function authorize(): bool
    {
        return true; // ← géré par middleware
    }

    /**
     * Règles de validation.
     *
     * NOTE IMPORTANTE (même approche que StoreProductCategoryRequest) :
     * La syntaxe statique 'unique:...,lang_id,{translations.*.lang_id}' ne
     * fonctionne PAS — Laravel traite "{translations.*.lang_id}" comme une
     * chaîne littérale, pas comme une référence dynamique au champ frère.
     * On reconstruit ces règles dynamiquement en PHP, avec un vrai closure
     * qui reçoit la vraie valeur de lang_id de chaque ligne de traduction.
     */
    public function rules(): array
    {
        $productCategoryId = $this->productCategoryId;

        $rules = [
            // Catégorie parente — optionnelle : null = catégorie racine.
            // L'auto-parentage et les cycles sont refusés au niveau du service
            // (assertValidParent) avec des messages métier explicites.
            'parent_id' => ['nullable', 'integer', 'exists:product_categories,id'],

            // sort_order doit être unique, sauf pour LA catégorie qu'on modifie
            // elle-même (sinon impossible de sauvegarder sans changer sa valeur).
            'sort_order' => ['required', 'integer', 'min:0', 'unique:product_categories,sort_order,'.$productCategoryId],

            // Le statut doit être l'une des valeurs définies dans l'énumération ProductCategoryStatus
            'status' => ['required', Rule::in(ProductCategoryStatus::values())],

            // La catégorie doit être associée à une plateforme cloud existante
            'cloud_platform_uuid' => ['nullable', 'exists:cloud_platforms,uuid'],

            // 'nullable' ici car en update, on ne réenvoie pas forcément une
            // nouvelle image si l'utilisateur ne veut pas la changer.
            'image' => ['nullable', 'image', 'mimes:jpg,png,webp', 'max:2048'], // 2 Mo

            // Le tableau de traductions lui-même
            'translations' => ['required', 'array'],

            // Chaque traduction doit référencer une langue existante
            'translations.*.lang_id' => ['required', 'exists:langs,id'],

            // La description reste une règle simple et statique (pas d'unicité à gérer)
            'translations.*.description' => ['required', 'string', 'max:1000'],
        ];

        // ────────────────────────────────────────────────────────────
        // Génération dynamique des règles "unique par langue", en
        // excluant la catégorie courante de la vérification.
        // ────────────────────────────────────────────────────────────
        $translations = $this->input('translations', []);

        foreach ($translations as $index => $translation) {
            // lang_id RÉEL de cette ligne précise (pas un placeholder texte).
            $langId = $translation['lang_id'] ?? null;

            // Règle unique dynamique pour "name" :
            // → unique parmi les traductions ayant le MÊME lang_id que cette
            //   ligne, MAIS en excluant celles qui appartiennent déjà à la
            //   catégorie en cours de modification.
            $rules["translations.{$index}.name"] = [
                'required',
                'string',
                'max:255',
                Rule::unique('product_category_translations', 'name')
                    ->where(fn ($query) => $query->where('lang_id', $langId))
                    ->ignore($productCategoryId, 'category_id')
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
            'parent_id.integer' => __("{$this->backendLangPath}.parent_id.integer"),
            'parent_id.exists' => __("{$this->backendLangPath}.parent_id.exists"),

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
