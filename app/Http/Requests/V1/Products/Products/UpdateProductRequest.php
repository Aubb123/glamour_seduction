<?php

namespace App\Http\Requests\V1\Products\Products;

use App\Enums\Products\Products\ProductStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

class UpdateProductRequest extends FormRequest
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
            'category_id' => ['sometimes', 'required', 'integer', 'exists:product_categories,id'],
            'brand_id' => ['sometimes', 'nullable', 'integer', 'exists:product_brands,id'],
            'status' => ['sometimes', 'required', Rule::in(ProductStatus::values())],
            'is_featured' => ['sometimes', 'boolean'],
            'requires_discreet_packaging' => ['sometimes', 'boolean'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:99999.99'],
            'base_price' => ['sometimes', 'required', 'numeric', 'min:0.01', 'max:99999999.99'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0.01', 'max:99999999.99'],
            'tags' => ['sometimes', 'array'],
            'tags.*' => ['integer', 'exists:product_tags,id'],
            'translations' => ['sometimes', 'required', 'array'],
            'translations.*.lang_id' => ['required', 'exists:langs,id'],
            'translations.*.short_description' => ['required', 'string', 'max:500'],
            'translations.*.description' => ['required', 'string'],
        ];

        // ────────────────────────────────────────────
        // Génération dynamique des règles "unique par langue"
        // pour "name" et "slug" — ignore la propre traduction.
        // NB : le slug est auto-généré par le trait HasSlug — la règle
        // n'est appliquée que s'il est explicitement fourni.
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
                    ->whereNull('deleted_at')
                    ->ignore($translationId),
            ];

            if (array_key_exists('slug', $translation)) {
                $rules["translations.{$index}.slug"] = [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('product_translations', 'slug')
                        ->where(fn ($query) => $query->where('lang_id', $langId))
                        ->whereNull('deleted_at')
                        ->ignore($translationId),
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
            'category_id.exists' => __("{$this->langPath}.category_id.exists"),
            'brand_id.exists' => __("{$this->langPath}.brand_id.exists"),
            'status.required' => __("{$this->langPath}.status.required"),
            'status.in' => __("{$this->langPath}.status.in"),
            'is_featured.boolean' => __("{$this->langPath}.is_featured.boolean"),
            'requires_discreet_packaging.boolean' => __("{$this->langPath}.requires_discreet_packaging.boolean"),
            'weight.numeric' => __("{$this->langPath}.weight.numeric"),
            'base_price.required' => __("{$this->langPath}.base_price.required"),
            'base_price.numeric' => __("{$this->langPath}.base_price.numeric"),
            'base_price.min' => __("{$this->langPath}.base_price.min"),
            'compare_at_price.numeric' => __("{$this->langPath}.compare_at_price.numeric"),
            'tags.*.exists' => __("{$this->langPath}.tags.exists"),
            'translations.*.lang_id.exists' => __("{$this->langPath}.translations.lang_id.exists"),
            'translations.*.name.required' => __("{$this->langPath}.translations.name.required"),
            'translations.*.name.unique' => __("{$this->langPath}.translations.name.unique"),
            'translations.*.slug.required' => __("{$this->langPath}.translations.slug.required"),
            'translations.*.slug.unique' => __("{$this->langPath}.translations.slug.unique"),
            'translations.*.short_description.max' => __("{$this->langPath}.translations.short_description.max"),
        ];
    }
}