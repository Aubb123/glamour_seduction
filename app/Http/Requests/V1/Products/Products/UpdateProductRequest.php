<?php

namespace App\Http\Requests\V1\Products\Products;

use App\Enums\Products\Products\ProductStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

/**
 * Requête de validation pour la mise à jour d'un produit.
 *
 * Valide les données entrantes pour la modification d'un produit (products)
 * avec ses traductions associées (product_translations).
 */
class UpdateProductRequest extends FormRequest
{
    /**
     * Chemin de base pour les traductions des messages de validation.
     */
    protected string $backendLangPath = 'v1/backend/views/pages/products/products/messages/products-messages-validation.products';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // ← géré par middleware
    }

    /**
     * Règles de validation.
     */
    public function rules(): array
    {
        $rules = [
            'category_id' => ['sometimes', 'required', 'integer', 'exists:product_categories,id'],
            'brand_id' => ['sometimes', 'nullable', 'integer', 'exists:product_brands,id'],
            'status' => ['sometimes', 'required', Rule::in(ProductStatus::values())],
            'is_featured' => ['sometimes', 'boolean'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:99999.99'],
            'base_price' => ['sometimes', 'required', 'numeric', 'min:0.01', 'max:99999999.99'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0.01', 'max:99999999.99'],
            'tags' => ['sometimes', 'array'],
            'tags.*' => ['integer', 'exists:product_tags,id'],

            // Images principales du produit (jusqu'à 4 images, pattern woononvi driver_documents)
            'cloud_platform_uuid' => ['nullable', 'required_with:files', 'exists:cloud_platforms,uuid'],
            'files' => ['nullable', 'array', 'max:4'],
            'files.*' => ['required', 'file', 'mimes:jpg,png,webp', 'max:2048'],

            'translations' => ['sometimes', 'required', 'array'],
            'translations.*.lang_id' => ['sometimes', 'required', 'exists:langs,id'],
            'translations.*.short_description' => ['sometimes', 'required', 'string', 'max:500'],
            'translations.*.description' => ['sometimes', 'required', 'string'],
        ];

        // ────────────────────────────────────────────
        // Génération dynamique des règles "unique par langue"
        // pour "name", en ignorant la propre traduction.
        // ────────────────────────────────────────────
        $translations = $this->input('translations', []);

        foreach ($translations as $index => $translation) {
            $langId = $translation['lang_id'] ?? null;
            $translationId = $translation['id'] ?? null;

            $rules["translations.{$index}.name"] = [
                'required',
                'string',
                'max:255',
                Rule::unique('product_translations', 'name')
                    ->where(fn ($query) => $query->where('lang_id', $langId))
                    ->ignore($translationId),
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
            'category_id.required' => __("{$this->backendLangPath}.category_id.required"),
            'category_id.exists' => __("{$this->backendLangPath}.category_id.exists"),
            'brand_id.exists' => __("{$this->backendLangPath}.brand_id.exists"),
            'status.required' => __("{$this->backendLangPath}.status.required"),
            'status.in' => __("{$this->backendLangPath}.status.in"),
            'is_featured.boolean' => __("{$this->backendLangPath}.is_featured.boolean"),
            'weight.numeric' => __("{$this->backendLangPath}.weight.numeric"),
            'base_price.required' => __("{$this->backendLangPath}.base_price.required"),
            'base_price.numeric' => __("{$this->backendLangPath}.base_price.numeric"),
            'base_price.min' => __("{$this->backendLangPath}.base_price.min"),
            'compare_at_price.numeric' => __("{$this->backendLangPath}.compare_at_price.numeric"),
            'tags.*.exists' => __("{$this->backendLangPath}.tags.exists"),

            'cloud_platform_uuid.required_with' => __("{$this->backendLangPath}.cloud_platform_uuid.required_with"),
            'cloud_platform_uuid.exists' => __("{$this->backendLangPath}.cloud_platform_uuid.exists"),

            'files.array' => __("{$this->backendLangPath}.files.array"),
            'files.max' => __("{$this->backendLangPath}.files.max"),
            'files.*.required' => __("{$this->backendLangPath}.files.required_file"),
            'files.*.file' => __("{$this->backendLangPath}.files.file"),
            'files.*.mimes' => __("{$this->backendLangPath}.files.mimes"),
            'files.*.max' => __("{$this->backendLangPath}.files.max_file"),

            'translations.*.lang_id.exists' => __("{$this->backendLangPath}.translations.lang_id.exists"),
            'translations.*.name.required' => __("{$this->backendLangPath}.translations.name.required"),
            'translations.*.name.unique' => __("{$this->backendLangPath}.translations.name.unique"),
            'translations.*.short_description.max' => __("{$this->backendLangPath}.translations.short_description.max"),
        ];
    }
}
