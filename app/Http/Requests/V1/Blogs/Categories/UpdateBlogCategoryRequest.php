<?php

namespace App\Http\Requests\V1\Blogs\Categories;

use App\Enums\Blogs\Categories\BlogCategoryStatus;
use App\Models\BlogCategory;
use App\Traits\LogActivity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

/**
 * Requête de validation pour la mise à jour d'une catégorie de blog.
 *
 * Valide les données entrantes pour la modification d'une catégorie (blog_categories)
 * avec ses traductions associées (blog_category_translations).
 * Gère l'unicité de sort_order en excluant la catégorie en cours de modification.
 */
class UpdateBlogCategoryRequest extends FormRequest
{
    use LogActivity;

    /**
     * Chemin de base pour les traductions des messages de validation.
     */
    protected string $backendLangPath = 'v1/backend/views/pages/blogs/categories/messages/categories-messages-validation.categories';

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation.
     *
     * NOTE IMPORTANTE (même bug que dans StoreBlogCategoryRequest) :
     * La syntaxe statique 'unique:...,lang_id,{translations.*.lang_id}' ne
     * fonctionne PAS — Laravel traite "{translations.*.lang_id}" comme une
     * chaîne littérale, pas comme une référence dynamique au champ frère.
     * Le filtre par langue était donc silencieusement ignoré.
     *
     * On reconstruit ces règles dynamiquement en PHP, avec un vrai closure
     * qui reçoit la vraie valeur de lang_id de chaque ligne de traduction.
     */
    public function rules(): array
    {
        // Récupère l'ID interne de la catégorie à partir de l'UUID de la route.
        $blogCategoryId = BlogCategory::where('uuid', $this->route('uuid'))->value('id');

        if (! $blogCategoryId) {

            // Log de l'erreur si nécessaire
            $this->logActivity(type: 'error', action: 'backend.blog-categories.update', context: ['blog_category_uuid' => $this->route('uuid')], message: 'BlogCategory not found for update.', activity: __("{$this->backendLangPath}.not_found"), is_success: false);

            // Retourne une réponse 404 avec un message d'erreur
            abort(404, __("{$this->backendLangPath}.not_found"));
        }

        $rules = [
            // sort_order doit être unique, sauf pour LA catégorie qu'on modifie
            // elle-même (sinon impossible de sauvegarder sans changer sa valeur).
            'sort_order' => ['required', 'integer', 'min:0', 'unique:blog_categories,sort_order,'.$blogCategoryId],

            'status' => ['required', Rule::in(BlogCategoryStatus::values())],

            'cloud_platform_uuid' => ['nullable', 'exists:cloud_platforms,uuid'],

            // 'nullable' ici car en update, on ne réenvoie pas forcément une
            // nouvelle image si l'utilisateur ne veut pas la changer.
            'image' => ['nullable', 'image', 'mimes:jpg,png,webp', 'max:2048'], // 2 Mo

            'translations' => ['required', 'array'],
            'translations.*.lang_id' => ['required', 'exists:langs,id'],
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
            //   catégorie qu'on est en train de modifier (via blog_category_id).
            //   Comme (category_id, lang_id) est unique par construction dans
            //   la table, exclure "blog_category_id = $blogCategoryId" revient
            //   à exclure précisément la traduction courante pour cette langue.
            $rules["translations.{$index}.name"] = [
                'required',
                'string',
                'max:255',
                Rule::unique('blog_category_translations', 'name')
                    ->where(fn ($query) => $query->where('lang_id', $langId))
                    ->ignore($blogCategoryId, 'category_id'),
            ];

        }

        return $rules;
    }

    /**
     * Messages de validation personnalisés.
     *
     * NOTE : comme pour StoreBlogCategoryRequest, Laravel résout
     * normalement "translations.*.name.unique" même si les règles ont été
     * générées avec des index numériques réels (translations.0.name, ...).
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

            'cloud_platform_uuid.required' => __("{$this->backendLangPath}.cloud_platform_uuid.required"),
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
