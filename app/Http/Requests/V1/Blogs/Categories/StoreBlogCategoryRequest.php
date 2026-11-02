<?php

namespace App\Http\Requests\V1\Blogs\Categories;

use App\Enums\Blogs\Categories\BlogCategoryStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

/**
 * Requête de validation pour la création d'une catégorie de blog.
 *
 * Valide les données entrantes pour la création d'une catégorie (blog_categories)
 * avec ses traductions associées (blog_category_translations).
 */
class StoreBlogCategoryRequest extends FormRequest
{
    /**
     * Chemin de base pour les traductions des messages de validation.
     */
    protected string $langPath = 'v1/backend/views/pages/blogs/categories/messages/categories-messages-validation.categories';

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
     * ne peuvent PAS être écrites en syntaxe statique de type :
     *   'unique:blog_category_translations,name,NULL,id,lang_id,{translations.*.lang_id}'
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
            // sort_order doit être unique sur toute la table blog_categories
            // (⚠️ à surveiller : si le default(0) en migration n'est jamais écrasé,
            // un deuxième insert sans sort_order explicite provoquera une violation)
            'sort_order' => ['required', 'integer', 'min:0', 'unique:blog_categories,sort_order'],

            'status' => ['required', Rule::in(BlogCategoryStatus::values())],

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
        // Génération dynamique des règles "unique par langue",
        // basées sur les données réellement envoyées dans la requête
        // (accessible via $this->input()).
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
            //   qui ont le même lang_id que CETTE ligne précise.
            $rules["translations.{$index}.name"] = [
                'required',
                'string',
                'max:255',
                Rule::unique('blog_category_translations', 'name')
                    ->where(fn ($query) => $query->where('lang_id', $langId))
                    // Ignore les lignes soft-deleted : un nom "libéré" par une
                    // suppression logique ne doit pas bloquer une recréation.
                    // (cohérent avec le SoftDeletes utilisé sur cette table)
                    ->whereNull('deleted_at'),
            ];

        }

        return $rules;
    }

    /**
     * Messages de validation personnalisés.
     *
     * NOTE : Comme les règles dynamiques ci-dessus utilisent des clés avec
     * un index numérique réel (translations.0.name, translations.1.name, ...)
     * plutôt que le joker "*", Laravel sait normalement toujours faire
     * correspondre ces messages génériques "translations.*.name.unique" —
     * le framework résout "*" pour matcher n'importe quel index numérique.
     * Si jamais un message ne s'affichait pas pour un index précis, il est
     * possible de le dupliquer explicitement (ex: 'translations.0.name.unique').
     */
    public function messages(): array
    {
        return [
            // sort_order
            'sort_order.required' => __("{$this->langPath}.sort_order.required"),
            'sort_order.integer' => __("{$this->langPath}.sort_order.integer"),
            'sort_order.min' => __("{$this->langPath}.sort_order.min"),
            'sort_order.unique' => __("{$this->langPath}.sort_order.unique"),

            // status
            'status.required' => __("{$this->langPath}.status.required"),
            'status.in' => __("{$this->langPath}.status.in"),

            // cloud_platform_uuid
            'cloud_platform_uuid.required' => __("{$this->langPath}.cloud_platform_uuid.required"),
            'cloud_platform_uuid.exists' => __("{$this->langPath}.cloud_platform_uuid.exists"),

            // image
            'image.required' => __("{$this->langPath}.image.required"),
            'image.image' => __("{$this->langPath}.image.image"),
            'image.mimes' => __("{$this->langPath}.image.mimes"),
            'image.max' => __("{$this->langPath}.image.max"),

            // translations
            'translations.required' => __("{$this->langPath}.translations.required"),
            'translations.array' => __("{$this->langPath}.translations.array"),

            // translations.*.lang_id
            'translations.*.lang_id.required' => __("{$this->langPath}.translations.lang_id.required"),
            'translations.*.lang_id.exists' => __("{$this->langPath}.translations.lang_id.exists"),

            // translations.*.name
            'translations.*.name.required' => __("{$this->langPath}.translations.name.required"),
            'translations.*.name.string' => __("{$this->langPath}.translations.name.string"),
            'translations.*.name.max' => __("{$this->langPath}.translations.name.max"),
            'translations.*.name.unique' => __("{$this->langPath}.translations.name.unique"),

            // translations.*.description
            'translations.*.description.required' => __("{$this->langPath}.translations.description.required"),
            'translations.*.description.string' => __("{$this->langPath}.translations.description.string"),
            'translations.*.description.max' => __("{$this->langPath}.translations.description.max"),
        ];
    }
}
