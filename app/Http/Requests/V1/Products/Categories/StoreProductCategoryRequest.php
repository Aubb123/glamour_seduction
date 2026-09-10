<?php

namespace App\Http\Requests\V1\Products\Categories;

use App\Enums\Products\Categories\ProductCategoryStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

class StoreProductCategoryRequest extends FormRequest
{
    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/products/categories/messages/categories-messages-validation.productCategories';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // ← géré par middleware
    }

    /**
     * Règles de validation.
     *
     * NOTE IMPORTANTE :
     * Les règles "unique" sur translations.*.name et translations.*.slug
     * ne peuvent PAS être écrites en syntaxe statique de type :
     *   'unique:product_category_translations,name,NULL,id,lang_id,{translations.*.lang_id}'
     * car Laravel n'interprète PAS "{translations.*.lang_id}" comme une référence
     * dynamique — il le traite comme une chaîne littérale. Résultat : le filtre
     * where("lang_id", "{translations.*.lang_id}") ne correspond à aucune ligne réelle,
     * et la contrainte d'unicité "par langue" ne fonctionne pas du tout.
     *
     * La solution est de construire ces règles dynamiquement en PHP, en itérant
     * sur les traductions réellement envoyées dans la requête, pour injecter
     * la vraie valeur de lang_id de chaque ligne dans un vrai closure.
     */
    public function rules(): array
    {
        $rules = [
            // Catégorie parente (hiérarchie) — optionnelle : null = catégorie racine
            'parent_id' => ['nullable', 'integer', 'exists:product_categories,id'],

            // sort_order doit être unique sur toute la table product_categories
            'sort_order' => ['required', 'integer', 'min:0', 'unique:product_categories,sort_order'],

            'status' => ['required', Rule::in(ProductCategoryStatus::values())],

            'cloud_platform_uuid' => ['required', 'exists:cloud_platforms,uuid'],

            'image' => ['required', 'image', 'mimes:jpg,png,webp', 'max:2048'], // 2 Mo

            // Le tableau de traductions lui-même
            'translations' => ['required', 'array'],

            // Chaque traduction doit référencer une langue existante
            'translations.*.lang_id' => ['required', 'exists:langs,id'],

            // La description reste une règle simple et statique (pas d'unicité à gérer)
            'translations.*.description' => ['required', 'string', 'max:1000'],
        ];

        // ────────────────────────────────────────────────────────────
        // Génération dynamique des règles "unique par langue"
        // pour "name" et "slug", basées sur les données réellement
        // envoyées dans la requête (accessible via $this->input()).
        // ────────────────────────────────────────────────────────────
        $translations = $this->input('translations', []);

        foreach ($translations as $index => $translation) {
            // Récupère le lang_id RÉEL de cette ligne précise de traduction.
            // Si lang_id est absent/invalide, la règle 'exists' définie plus haut
            // se chargera de le signaler séparément — ici on se contente de
            // sécuriser l'accès pour éviter une erreur PHP.
            $langId = $translation['lang_id'] ?? null;

            // Règle unique dynamique pour le "name" :
            // → le nom doit être unique, mais seulement PARMI les traductions
            //   de la MÊME langue (lang_id identique), et uniquement parmi les
            //   lignes actives (non soft-deleted).
            $rules["translations.{$index}.name"] = [
                'required',
                'string',
                'max:255',
                Rule::unique('product_category_translations', 'name')
                    ->where(fn ($query) => $query->where('lang_id', $langId))
                    ->whereNull('deleted_at'),
            ];

            // Même logique pour "slug", si ce champ est envoyé.
            if (array_key_exists('slug', $translation)) {
                $rules["translations.{$index}.slug"] = [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('product_category_translations', 'slug')
                        ->where(fn ($query) => $query->where('lang_id', $langId))
                        ->whereNull('deleted_at'),
                ];
            }
        }

        return $rules;
    }

    /**
     * Messages de validation personnalisés.
     */
    public function messages(): array
    {
        return [
            'parent_id.integer' => __("{$this->langPath}.parent_id.integer"),
            'parent_id.exists' => __("{$this->langPath}.parent_id.exists"),

            'sort_order.required' => __("{$this->langPath}.sort_order.required"),
            'sort_order.integer' => __("{$this->langPath}.sort_order.integer"),
            'sort_order.min' => __("{$this->langPath}.sort_order.min"),
            'sort_order.unique' => __("{$this->langPath}.sort_order.unique"),

            'status.required' => __("{$this->langPath}.status.required"),
            'status.in' => __("{$this->langPath}.status.in"),

            'cloud_platform_uuid.required' => __("{$this->langPath}.cloud_platform_uuid.required"),
            'cloud_platform_uuid.exists' => __("{$this->langPath}.cloud_platform_uuid.exists"),

            'image.required' => __("{$this->langPath}.image.required"),
            'image.image' => __("{$this->langPath}.image.image"),
            'image.mimes' => __("{$this->langPath}.image.mimes"),
            'image.max' => __("{$this->langPath}.image.max"),

            'translations.required' => __("{$this->langPath}.translations.required"),
            'translations.array' => __("{$this->langPath}.translations.array"),

            'translations.*.lang_id.required' => __("{$this->langPath}.translations.lang_id.required"),
            'translations.*.lang_id.exists' => __("{$this->langPath}.translations.lang_id.exists"),

            'translations.*.name.required' => __("{$this->langPath}.translations.name.required"),
            'translations.*.name.string' => __("{$this->langPath}.translations.name.string"),
            'translations.*.name.max' => __("{$this->langPath}.translations.name.max"),
            'translations.*.name.unique' => __("{$this->langPath}.translations.name.unique"),

            'translations.*.slug.required' => __("{$this->langPath}.translations.slug.required"),
            'translations.*.slug.string' => __("{$this->langPath}.translations.slug.string"),
            'translations.*.slug.max' => __("{$this->langPath}.translations.slug.max"),
            'translations.*.slug.unique' => __("{$this->langPath}.translations.slug.unique"),

            'translations.*.description.required' => __("{$this->langPath}.translations.description.required"),
            'translations.*.description.string' => __("{$this->langPath}.translations.description.string"),
            'translations.*.description.max' => __("{$this->langPath}.translations.description.max"),
        ];
    }
}
