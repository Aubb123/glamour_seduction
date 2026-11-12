<?php

namespace App\Http\Requests\V1\Products\Products;

use App\Enums\Products\Products\ProductStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

/** Validation de la création d'un produit et de ses traductions. */
class StoreProductRequest extends FormRequest
{
    protected string $backendLangPath = 'v1/backend/views/pages/products/products/messages/products-messages-validation.products';

    public function authorize(): bool
    {
        return true; // L'accès est contrôlé par les middlewares de route.
    }

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
            'cloud_platform_uuid' => ['required', 'exists:cloud_platforms,uuid'],
            'files' => ['required', 'array', 'min:1', 'max:4'],
            'files.*' => ['required', 'file', 'mimes:jpg,png,webp', 'max:2048'],
            'translations' => ['required', 'array'],
            'translations.*.lang_id' => ['required', 'exists:langs,id'],
            'translations.*.short_description' => ['required', 'string', 'max:500'],
            'translations.*.description' => ['required', 'string'],
        ];

        foreach ($this->input('translations', []) as $index => $translation) {
            $langId = $translation['lang_id'] ?? null;
            $rules["translations.{$index}.name"] = [
                'required',
                'string',
                'max:255',
                Rule::unique('product_translations', 'name')->where(fn ($query) => $query->where('lang_id', $langId)),
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        $message = fn (string $key): string => __("{$this->backendLangPath}.{$key}");

        return [
            'category_id.required' => $message('category_id.required'),
            'category_id.integer' => $message('category_id.integer'),
            'category_id.exists' => $message('category_id.exists'),
            'brand_id.integer' => $message('brand_id.integer'),
            'brand_id.exists' => $message('brand_id.exists'),
            'status.required' => $message('status.required'),
            'status.in' => $message('status.in'),
            'is_featured.boolean' => $message('is_featured.boolean'),
            'weight.numeric' => $message('weight.numeric'),
            'weight.min' => $message('weight.min'),
            'weight.max' => $message('weight.max'),
            'base_price.required' => $message('base_price.required'),
            'base_price.numeric' => $message('base_price.numeric'),
            'base_price.min' => $message('base_price.min'),
            'base_price.max' => $message('base_price.max'),
            'compare_at_price.numeric' => $message('compare_at_price.numeric'),
            'compare_at_price.min' => $message('compare_at_price.min'),
            'compare_at_price.max' => $message('compare_at_price.max'),
            'tags.array' => $message('tags.array'),
            'tags.*.integer' => $message('tags.integer'),
            'tags.*.exists' => $message('tags.exists'),
            'cloud_platform_uuid.required' => $message('cloud_platform_uuid.required'),
            'cloud_platform_uuid.exists' => $message('cloud_platform_uuid.exists'),
            'files.required' => $message('files.required'),
            'files.array' => $message('files.array'),
            'files.min' => $message('files.min'),
            'files.max' => $message('files.max'),
            'files.*.required' => $message('files.required_file'),
            'files.*.file' => $message('files.file'),
            'files.*.mimes' => $message('files.mimes'),
            'files.*.max' => $message('files.max_file'),
            'translations.required' => $message('translations.required'),
            'translations.array' => $message('translations.array'),
            'translations.*.lang_id.required' => $message('translations.lang_id.required'),
            'translations.*.lang_id.exists' => $message('translations.lang_id.exists'),
            'translations.*.name.required' => $message('translations.name.required'),
            'translations.*.name.string' => $message('translations.name.string'),
            'translations.*.name.max' => $message('translations.name.max'),
            'translations.*.name.unique' => $message('translations.name.unique'),
            'translations.*.short_description.required' => $message('translations.short_description.required'),
            'translations.*.short_description.string' => $message('translations.short_description.string'),
            'translations.*.short_description.max' => $message('translations.short_description.max'),
            'translations.*.description.required' => $message('translations.description.required'),
            'translations.*.description.string' => $message('translations.description.string'),
        ];
    }
}
