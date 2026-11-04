<?php

namespace App\Http\Requests\V1\Blogs\Posts;

use App\Enums\Blogs\Posts\BlogPostStatus;
use App\Models\BlogPostTranslation;
use App\Traits\LogActivity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

class UpdateBlogPostRequest extends FormRequest
{
    use LogActivity;

    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/blogs/posts/messages/posts-messages-validation.posts';

    // ID du post résolu une seule fois par requête
    protected ?int $blogPostId = null;

    public function authorize(): bool
    {
        return true;
    }

    // Résout et met en cache l'ID du post pour éviter une double requête
    protected function getBlogPostId(): ?int
    {
        if ($this->blogPostId === null) {
            $this->blogPostId = $this->route('blogPost')?->id;
        }

        return $this->blogPostId;
    }

    public function rules(): array
    {
        if (! $this->getBlogPostId()) {

            $this->logActivity(type: 'error', action: 'backend.blog-posts.update', context: ['blog_post_uuid' => $this->route('blogPost')?->uuid], message: 'BlogPost not found for update.', activity: __("{$this->backendLangPath}.not_found"), is_success: false);

            abort(404, __("{$this->backendLangPath}.not_found"));
        }

        return [
            'category_id' => ['required', 'exists:blog_categories,id'],
            'status' => ['required', Rule::in(BlogPostStatus::values())],
            'sort_order' => ['required', 'integer', 'min:0'],
            'published_at' => ['nullable', 'date', 'required_if:status,scheduled', 'after_or_equal:now'],
            'tags' => ['required', 'array'],
            'tags.*' => ['exists:blog_tags,id'],

            'cloud_platform_uuid' => ['nullable', 'exists:cloud_platforms,uuid'],
            'image' => ['nullable', 'image', 'mimes:jpg,png,webp', 'max:2048'],

            // Traductions obligatoires
            'translations' => ['required', 'array'],
            'translations.*.lang_id' => ['required', 'exists:langs,id'],
            'translations.*.title' => ['required', 'string', 'max:255'],
            'translations.*.content' => ['required', 'string'],
        ];
    }

    /**
     * Configure the validator instance with custom after validation hook.
     */
    public function withValidator($validator): void
    {
        $postId = $this->getBlogPostId();

        if (! $postId) {
            return;
        }

        $validator->after(function ($validator) use ($postId) {

            $translations = $this->input('translations', []);

            foreach ($translations as $key => $translation) {

                $langId = $translation['lang_id'] ?? null;
                $title = $translation['title'] ?? null;

                if (! $langId || ! $title) {
                    continue;
                }

                // Vérifie unicité du (title, lang_id) en excluant le post courant
                $exists = BlogPostTranslation::where('title', $title)
                    ->where('lang_id', $langId)
                    ->where('post_id', '!=', $postId)
                    ->exists();

                if ($exists) {
                    $validator->errors()->add(
                        "translations.{$key}.title",
                        __("{$this->backendLangPath}.translations.unique_title")
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'category_id.required' => __("{$this->backendLangPath}.category_id.required"),
            'category_id.exists' => __("{$this->backendLangPath}.category_id.exists"),

            'status.required' => __("{$this->backendLangPath}.status.required"),
            'status.in' => __("{$this->backendLangPath}.status.in"),

            'sort_order.required' => __("{$this->backendLangPath}.sort_order.required"),
            'sort_order.integer' => __("{$this->backendLangPath}.sort_order.integer"),
            'sort_order.min' => __("{$this->backendLangPath}.sort_order.min"),

            'published_at.date' => __("{$this->backendLangPath}.published_at.date"),
            'published_at.required_if' => __("{$this->backendLangPath}.published_at.required_if"),
            'published_at.after' => __("{$this->backendLangPath}.published_at.after"),
            'published_at.after_or_equal' => __("{$this->backendLangPath}.published_at.after"),

            'tags.array' => __("{$this->backendLangPath}.tags.array"),
            'tags.*.exists' => __("{$this->backendLangPath}.tags.exists"),

            'cloud_platform_uuid.exists' => __("{$this->backendLangPath}.cloud_platform_uuid.exists"),

            'image.image' => __("{$this->backendLangPath}.image.image"),
            'image.mimes' => __("{$this->backendLangPath}.image.mimes"),
            'image.max' => __("{$this->backendLangPath}.image.max"),

            'translations.required' => __("{$this->backendLangPath}.translations.required"),
            'translations.array' => __("{$this->backendLangPath}.translations.array"),

            'translations.*.lang_id.required' => __("{$this->backendLangPath}.translations.lang_id.required"),
            'translations.*.lang_id.exists' => __("{$this->backendLangPath}.translations.lang_id.exists"),

            'translations.*.title.required' => __("{$this->backendLangPath}.translations.title.required"),
            'translations.*.title.string' => __("{$this->backendLangPath}.translations.title.string"),
            'translations.*.title.max' => __("{$this->backendLangPath}.translations.title.max"),

            'translations.*.content.required' => __("{$this->backendLangPath}.translations.content.required"),
            'translations.*.content.string' => __("{$this->backendLangPath}.translations.content.string"),
        ];
    }
}
