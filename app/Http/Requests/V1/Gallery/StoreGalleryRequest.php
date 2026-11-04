<?php

namespace App\Http\Requests\V1\Gallery;

use App\Enums\Galleries\GalleryStatus;
use App\Models\GalleryTranslation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

class StoreGalleryRequest extends FormRequest
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/galleries/messages/galleries-messages-validation.galleries';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // ← géré par middleware
    }

    public function rules(): array
    {
        return [
            'sort_order' => ['required', 'integer', 'min:0', 'unique:galleries,sort_order'],
            'status' => ['required', Rule::in(GalleryStatus::values())],

            'cloud_platform_uuid' => ['required', 'exists:cloud_platforms,uuid'],
            'image' => ['required', 'image', 'mimes:jpg,png,webp', 'max:2048'],

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
        $validator->after(function ($validator) {

            $translations = $this->input('translations', []);

            foreach ($translations as $key => $translation) {

                $langId = $translation['lang_id'] ?? null;
                $title = $translation['title'] ?? null;

                if (! $langId || ! $title) {
                    continue;
                }

                // Vérifie unicité du (title, lang_id)
                $exists = GalleryTranslation::where('title', $title)
                    ->where('lang_id', $langId)
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

            'cloud_platform_uuid.required' => __("{$this->backendLangPath}.cloud_platform_uuid.required"),
            'cloud_platform_uuid.exists' => __("{$this->backendLangPath}.cloud_platform_uuid.exists"),

            'image.required' => __("{$this->backendLangPath}.image.required"),
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
