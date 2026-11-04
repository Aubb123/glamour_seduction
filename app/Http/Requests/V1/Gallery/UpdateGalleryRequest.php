<?php

namespace App\Http\Requests\V1\Gallery;

use App\Enums\Galleries\GalleryStatus;
use App\Models\Gallery;
use App\Models\GalleryTranslation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

class UpdateGalleryRequest extends FormRequest
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/galleries/messages/galleries-messages-validation.galleries';

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Résout la galerie courante via le binding implicite de la route ({gallery}).
     * Le binding est déjà garanti par le contrôleur, on évite ainsi toute résolution
     * SQL et de abort() dans les règles de validation.
     */
    protected function gallery(): ?Gallery
    {
        return $this->route('gallery') instanceof Gallery ? $this->route('gallery') : null;
    }

    public function rules(): array
    {
        $galleryId = $this->gallery()?->id;

        $sortOrderRules = $galleryId
            ? ['required', 'integer', 'min:0', "unique:galleries,sort_order,{$galleryId}"]
            : ['required', 'integer', 'min:0', 'unique:galleries,sort_order'];

        return [
            'sort_order' => $sortOrderRules,
            'status' => ['required', Rule::in(GalleryStatus::values())],

            'cloud_platform_uuid' => ['nullable', 'exists:cloud_platforms,uuid'],
            'image' => ['nullable', 'image', 'mimes:jpg,png,webp', 'max:2048'],

            // Traductions obligatoires
            'translations' => ['required', 'array'],
            'translations.*.lang_id' => ['required', 'exists:langs,id'],
            'translations.*.title' => ['required', 'string', 'max:255'],
            'translations.*.description' => ['required', 'string'],
        ];
    }

    /**
     * Configure the validator instance with custom after validation hook.
     */
    public function withValidator($validator): void
    {
        $galleryId = $this->gallery()?->id;

        if (! $galleryId) {
            return;
        }

        $validator->after(function ($validator) use ($galleryId) {
            $translations = $this->input('translations', []);

            foreach ($translations as $key => $translation) {
                $langId = $translation['lang_id'] ?? null;
                $title = $translation['title'] ?? null;

                if (! $langId || ! $title) {
                    continue;
                }

                // Vérifie l'unicité du (title, lang_id) en excluant la galerie courante
                $exists = GalleryTranslation::where('title', $title)
                    ->where('lang_id', $langId)
                    ->where('gallery_id', '!=', $galleryId)
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

            'translations.*.title.required' => __("{$this->backendLangPath}.translations.title.required"),
            'translations.*.title.string' => __("{$this->backendLangPath}.translations.title.string"),
            'translations.*.title.max' => __("{$this->backendLangPath}.translations.title.max"),

            'translations.*.description.required' => __("{$this->backendLangPath}.translations.description.required"),
            'translations.*.description.string' => __("{$this->backendLangPath}.translations.description.string"),
        ];
    }
}
