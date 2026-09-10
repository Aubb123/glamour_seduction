<?php

namespace App\Http\Requests\V1\Products\Products;

use App\Enums\Products\Products\ProductStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

class StoreProductRequest extends FormRequest
{
    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/products/products/messages/products-messages-validation.products';

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
            'requires_discreet_packaging' => ['sometimes', 'boolean'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:99999.99'],
            'base_price' => ['required', 'numeric', 'min:0.01', 'max:99999999.99'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0.01', 'max:99999999.99'],
            'tags' => ['sometimes', 'array'],
            'tags.*' => ['integer', 'exists:product_tags,id'],
            'translations' => ['required', 'array'],
            'translations.*.lang_id' => ['required', 'exists:langs,id'],
            'translations.*.short_description' => ['required', 'string', 'max:500'],
            'translations.*.description' => ['required', 'string'],
        ];

        // Règles "unique par langue" dynamiques pour name/slug
        $translations = $this->input('translations', []);

        foreach ($translations as $index => $translation) {
            $langId = $translation['lang_id'] ?? null;

            $rules["translations.{$index}.name"] = [
                'required',
                'string',
                'max:255',
                Rule::unique('product_translations', 'name')
                    ->where(fn ($query) => $query->where('lang_id', $langId))
                    ->whereNull('deleted_at'),
            ];

            if (array_key_exists('slug', $translation)) {
                $rules["translations.{$index}.slug"] = [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('product_translations', 'slug')
                        ->where(fn ($query) => $query->where('lang_id', $langId))
                        ->whereNull('deleted_at'),
                ];
            }
        }

        return $rules;
    } 

    /**
     * Messages de validation personnalisés.
     */
    public function messages(): array
    {
        return [
            'category_id.required' => __("{$this->langPath}.category_id.required"),
            'category_id.integer' => __("{$this->langPath}.category_id.integer"),
            'category_id.exists' => __("{$this->langPath}.category_id.exists"),

            'brand_id.integer' => __("{$this->langPath}.brand_id.integer"),
            'brand_id.exists' => __("{$this->langPath}.brand_id.exists"),

            'status.required' => __("{$this->langPath}.status.required"),
            'status.in' => __("{$this->langPath}.status.in"),

            'is_featured.boolean' => __("{$this->langPath}.is_featured.boolean"),
            'requires_discreet_packaging.boolean' => __("{$this->langPath}.requires_discreet_packaging.boolean"),

            'weight.numeric' => __("{$this->langPath}.weight.numeric"),
            'weight.min' => __("{$this->langPath}.weight.min"),
            'weight.max' => __("{$this->langPath}.weight.max"),

            'base_price.required' => __("{$this->langPath}.base_price.required"),
            'base_price.numeric' => __("{$this->langPath}.base_price.numeric"),
            'base_price.min' => __("{$this->langPath}.base_price.min"),
            'base_price.max' => __("{$this->langPath}.base_price.max"),

            'compare_at_price.numeric' => __("{$this->langPath}.compare_at_price.numeric"),
            'compare_at_price.min' => __("{$this->langPath}.compare_at_price.min"),
            'compare_at_price.max' => __("{$this->langPath}.compare_at_price.max"),

            'tags.array' => __("{$this->langPath}.tags.array"),
            'tags.*.exists' => __("{$this->langPath}.tags.exists"),

            'translations.required' => __("{$this->langPath}.translations.required"),
            'translations.array' => __("{$this->langPath}.translations.array"),

            'translations.*.lang_id.required' => __("{$this->langPath}.translations.lang_id.required"),
            'translations.*.lang_id.exists' => __("{$this->langPath}.translations.lang_id.exists"),

            'translations.*.name.required' => __("{$this->langPath}.translations.name.required"),
            'translations.*.name.string' => __("{$this->langPath}.translations.name.string"),
            'translations.*.name.max' => __("{$this->langPath}.translations.name.max"),
            'translations.*.name.unique' => __("{$this->langPath}.translations.name.unique"),

            'translations.*.slug.required' => __("{$this->langPath}.translations.slug.required"),
            'translations.*.slug.string' => __("{$this->langPath}.translations.slug.string"),
            'translations.*.slug.max' => __("{$this->langPath}.translations.slug.max"),
            'translations.*.slug.unique' => __("{$this->langPath}.translations.slug.unique"),

            'translations.*.short_description.required' => __("{$this->langPath}.translations.short_description.required"),
            'translations.*.short_description.string' => __("{$this->langPath}.translations.short_description.string"),
            'translations.*.short_description.max' => __("{$this->langPath}.translations.short_description.max"),

            'translations.*.description.required' => __("{$this->langPath}.translations.description.required"),
            'translations.*.description.string' => __("{$this->langPath}.translations.description.string"),
        ];
    }
}