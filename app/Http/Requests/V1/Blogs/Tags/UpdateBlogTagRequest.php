<?php

namespace App\Http\Requests\V1\Blogs\Tags;

use App\Enums\Blogs\Tags\BlogTagStatus;
use App\Traits\LogActivity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

/**
 * Requête de validation pour la mise à jour d'un tag de blog.
 *
 * Valide les données entrantes pour la modification d'un tag (blog_tags)
 * avec ses traductions associées (blog_tag_translations).
 * Gère l'unicité de sort_order en excluant le tag en cours de modification.
 */
class UpdateBlogTagRequest extends FormRequest
{
    use LogActivity;

    /**
     * Chemin de base pour les traductions des messages de validation.
     */
    protected string $backendLangPath = 'v1/backend/views/pages/blogs/tags/messages/tags-messages-validation.tags';

    // ID du tag résolu une seule fois par requête
    protected ?int $blogTagId = null;

    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     * L'autorisation est gérée par le middleware de permissions.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Résout et met en cache l'ID du tag à partir du binding implicite de route.
     *
     * NB : le paramètre de route est `{blogTag}` (lié par UUID), PAS `{uuid}`.
     * Utiliser `$this->route('uuid')` renvoyait null et déclenchait à tort un
     * abort(404) à chaque mise à jour.
     */
    protected function getBlogTagId(): ?int
    {
        if ($this->blogTagId === null) {
            $this->blogTagId = $this->route('blogTag')?->getKey();
        }

        return $this->blogTagId;
    }

    /**
     * Règles de validation pour la mise à jour d'un tag.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $blogTagId = $this->getBlogTagId();

        // Si le tag n'est pas trouvé, on log l'erreur et on abort
        if (! $blogTagId) {

            $this->logActivity(type: 'error', action: 'backend.blog-tags.update', context: ['blog_tag_uuid' => $this->route('blogTag')?->uuid], message: 'BlogTag not found for update.', activity: __("{$this->backendLangPath}.not_found"), is_success: false);

            abort(404, __("{$this->backendLangPath}.not_found"));
        }

        $rules = [
            // ── Champs de la table blog_tags ──
            // 'sort_order' doit être unique SAUF pour le tag en cours de modification (exclu par son ID)
            'sort_order' => ['required', 'integer', 'min:0', 'unique:blog_tags,sort_order,'.$blogTagId],
            'status' => ['required', Rule::in(BlogTagStatus::values())],

            // ── Traductions ──
            'translations' => ['required', 'array'],

            // Chaque traduction doit référencer une langue existante, sans doublon de langue
            'translations.*.lang_id' => ['required', 'distinct', 'exists:langs,id'],
        ];

        // ────────────────────────────────────────────────────────────
        // Construite dynamiquement (le lang_id réel de chaque ligne est injecté
        // dans un closure) et en excluant les traductions du tag courant via
        // `->ignore()`.
        // ────────────────────────────────────────────────────────────
        $translations = $this->input('translations', []);

        foreach ($translations as $index => $translation) {
            $langId = $translation['lang_id'] ?? null;

            $rules["translations.{$index}.name"] = [
                'required',
                'string',
                'max:255',
                Rule::unique('blog_tag_translations', 'name')
                    ->where(fn ($query) => $query->where('lang_id', $langId))
                    ->whereNull('deleted_at')
                    ->ignore($blogTagId, 'tag_id'),
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
