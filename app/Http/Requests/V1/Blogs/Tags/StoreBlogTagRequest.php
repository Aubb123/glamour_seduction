<?php

namespace App\Http\Requests\V1\Blogs\Tags;

use App\Enums\Blogs\Tags\BlogTagStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

/**
 * Requête de validation pour la création d'un tag de blog.
 *
 * Valide les données entrantes pour la création d'un tag (blog_tags)
 * avec ses traductions associées (blog_tag_translations).
 */
class StoreBlogTagRequest extends FormRequest
{
    /**
     * Chemin de base pour les traductions des messages de validation.
     */
    protected string $backendLangPath = 'v1/backend/views/pages/blogs/tags/messages/tags-messages-validation.tags';

    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     * L'autorisation est gérée par le middleware de permissions.
     */
    public function authorize(): bool
    {
        return true; // ← géré par middleware
    }

    public function rules(): array
    {
        $rules = [
            // ── Champs de la table blog_tags ──
            // 'sort_order' doit être unique dans la table blog_tags (pas de doublon d'ordre)
            'sort_order' => ['required', 'integer', 'min:0', 'unique:blog_tags,sort_order'],
            'status' => ['required', Rule::in(BlogTagStatus::values())],

            // ── Traductions obligatoires ──
            // Tableau de traductions, au moins une traduction est requise
            'translations' => ['required', 'array'],

            // Chaque traduction doit référencer une langue existante, sans doublon de langue
            'translations.*.lang_id' => ['required', 'distinct', 'exists:langs,id'],
        ];

        // ────────────────────────────────────────────────────────────
        // Construite dynamiquement car Laravel ne sait pas interpréter une
        // référence `{translations.*.lang_id}` en syntaxe statique (elle est
        // traitée comme une chaîne littérale).
        // ────────────────────────────────────────────────────────────
        $translations = $this->input('translations', []);

        foreach ($translations as $index => $translation) {
            $langId = $translation['lang_id'] ?? null;

            // Règle d'unicité sur le champ 'name' pour chaque traduction, filtrée par lang_id
            // et en incluant les enregistrements soft-deleted (deleted_at IS NULL)
            $rules["translations.{$index}.name"] = [
                'required',
                'string',
                'max:255',
                Rule::unique('blog_tag_translations', 'name')->where(fn ($query) => $query->where('lang_id', $langId)),
            ];
        }

        return $rules;
    }

    /**
     * Messages d'erreur personnalisés pour les règles de validation.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // sort_order
            'sort_order.required' => __("{$this->backendLangPath}.sort_order.required"),
            'sort_order.integer' => __("{$this->backendLangPath}.sort_order.integer"),
            'sort_order.min' => __("{$this->backendLangPath}.sort_order.min"),
            'sort_order.unique' => __("{$this->backendLangPath}.sort_order.unique"),

            // status
            'status.required' => __("{$this->backendLangPath}.status.required"),
            'status.in' => __("{$this->backendLangPath}.status.in"),

            // translations
            'translations.required' => __("{$this->backendLangPath}.translations.required"),
            'translations.array' => __("{$this->backendLangPath}.translations.array"),

            // translations.*.lang_id
            'translations.*.lang_id.required' => __("{$this->backendLangPath}.translations.lang_id.required"),
            'translations.*.lang_id.distinct' => __("{$this->backendLangPath}.translations.lang_id.distinct"),
            'translations.*.lang_id.exists' => __("{$this->backendLangPath}.translations.lang_id.exists"),

            // translations.*.name
            'translations.*.name.required' => __("{$this->backendLangPath}.translations.name.required"),
            'translations.*.name.string' => __("{$this->backendLangPath}.translations.name.string"),
            'translations.*.name.max' => __("{$this->backendLangPath}.translations.name.max"),
            'translations.*.name.unique' => __("{$this->backendLangPath}.translations.name.unique"),
        ];
    }
}
