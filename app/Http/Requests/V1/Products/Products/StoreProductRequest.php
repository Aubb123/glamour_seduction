<?php

namespace App\Http\Requests\V1\Products\Products;

use App\Enums\Products\Products\ProductStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

/**
 * Requête de validation pour la création d'un produit.
 *
 * Valide les données entrantes pour la création d'un produit (products)
 * avec ses traductions associées (product_translations).
 */
class StoreProductRequest extends FormRequest
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
            'category_id' => ['required', 'integer', 'exists:product_categories,id'],
            'brand_id' => ['nullable', 'integer', 'exists:product_brands,id'],
            'status' => ['required', Rule::in(ProductStatus::values())],
            'is_featured' => ['sometimes', 'boolean'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:99999.99'],
            'base_price' => ['required', 'numeric', 'min:0.01', 'max:99999999.99'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0.01', 'max:99999999.99'],
            'tags' => ['sometimes', 'array'],
            'tags.*' => ['integer', 'exists:product_tags,id'],

            // Images principales du produit (jusqu'à 4 images, pattern woononvi driver_documents)
            'cloud_platform_uuid' => ['required', 'exists:cloud_platforms,uuid'],
            'files' => ['required', 'array', 'min:1', 'max:4'],
            'files.*' => ['required', 'file', 'mimes:jpg,png,webp', 'max:2048'],

            'translations' => ['required', 'array'],
            'translations.*.lang_id' => ['required', 'exists:langs,id'],
            'translations.*.short_description' => ['required', 'string', 'max:500'],
            'translations.*.description' => ['required', 'string'],
        ];

        // ────────────────────────────────────────────
        // Génération dynamique des règles "unique par langue"
        // pour "name", basées sur les données envoyées.
        // ────────────────────────────────────────────
        $translations = $this->input('translations', []);

        foreach ($translations as $index => $translation) {
            $langId = $translation['lang_id'] ?? null;

            $rules["translations.{$index}.name"] = [
                'required',
                'string',
                'max:255',
                Rule::unique('product_translations', 'name')
                    ->where(fn ($query) => $query->where('lang_id', $langId)),
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
            'category_id.integer' => __("{$this->backendLangPath}.category_id.integer"),
            'category_id.exists' => __("{$this->backendLangPath}.category_id.exists"),

            'brand_id.integer' => __("{$this->backendLangPath}.brand_id.integer"),
            'brand_id.exists' => __("{$this->backendLangPath}.brand_id.exists"),

            'status.required' => __("{$this->backendLangPath}.status.required"),
            'status.in' => __("{$this->backendLangPath}.status.in"),

            'is_featured.boolean' => __("{$this->backendLangPath}.is_featured.boolean"),

            'weight.numeric' => __("{$this->backendLangPath}.weight.numeric"),
            'weight.min' => __("{$this->backendLangPath}.weight.min"),
            'weight.max' => __("{$this->backendLangPath}.weight.max"),

            'base_price.required' => __("{$this->backendLangPath}.base_price.required"),
            'base_price.numeric' => __("{$this->backendLangPath}.base_price.numeric"),
            'base_price.min' => __("{$this->backendLangPath}.base_price.min"),
            'base_price.max' => __("{$this->backendLangPath}.base_price.max"),

            'compare_at_price.numeric' => __("{$this->backendLangPath}.compare_at_price.numeric"),
            'compare_at_price.min' => __("{$this->backendLangPath}.compare_at_price.min"),
            'compare_at_price.max' => __("{$this->backendLangPath}.compare_at_price.max"),

            'tags.array' => __("{$this->backendLangPath}.tags.array"),
            'tags.*.exists' => __("{$this->backendLangPath}.tags.exists"),

            'cloud_platform_uuid.required' => __("{$this->backendLangPath}.cloud_platform_uuid.required"),
            'cloud_platform_uuid.exists' => __("{$this->backendLangPath}.cloud_platform_uuid.exists"),

            'files.required' => __("{$this->backendLangPath}.files.required"),
            'files.array' => __("{$this->backendLangPath}.files.array"),
            'files.min' => __("{$this->backendLangPath}.files.min"),
            'files.max' => __("{$this->backendLangPath}.files.max"),
            'files.*.required' => __("{$this->backendLangPath}.files.required_file"),
            'files.*.file' => __("{$this->backendLangPath}.files.file"),
            'files.*.mimes' => __("{$this->backendLangPath}.files.mimes"),
            'files.*.max' => __("{$this->backendLangPath}.files.max_file"),

            'translations.required' => __("{$this->backendLangPath}.translations.required"),
            'translations.array' => __("{$this->backendLangPath}.translations.array"),

            'translations.*.lang_id.required' => __("{$this->backendLangPath}.translations.lang_id.required"),
            'translations.*.lang_id.exists' => __("{$this->backendLangPath}.translations.lang_id.exists"),

            'translations.*.name.required' => __("{$this->backendLangPath}.translations.name.required"),
            'translations.*.name.string' => __("{$this->backendLangPath}.translations.name.string"),
            'translations.*.name.max' => __("{$this->backendLangPath}.translations.name.max"),
            'translations.*.name.unique' => __("{$this->backendLangPath}.translations.name.unique"),

            'translations.*.short_description.required' => __("{$this->backendLangPath}.translations.short_description.required"),
            'translations.*.short_description.string' => __("{$this->backendLangPath}.translations.short_description.string"),
            'translations.*.short_description.max' => __("{$this->backendLangPath}.translations.short_description.max"),

            'translations.*.description.required' => __("{$this->backendLangPath}.translations.description.required"),
            'translations.*.description.string' => __("{$this->backendLangPath}.translations.description.string"),
        ];
    }
}
