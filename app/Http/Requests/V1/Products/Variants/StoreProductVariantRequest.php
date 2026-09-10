<?php

namespace App\Http\Requests\V1\Products\Variants;

use App\Enums\Products\Variants\ProductVariantStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

class StoreProductVariantRequest extends FormRequest
{
    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/products/variants/messages/variants-messages-validation.productVariants';

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
        return [
            // Le produit parent est obligatoire et doit exister
            'product_id' => ['required', 'integer', 'exists:products,id'],

            // SKU obligatoire et unique (contrainte DB + validation)
            'sku' => ['required', 'string', 'max:100', 'unique:product_variants,sku'],

            // Code-barres optionnel
            'barcode' => ['nullable', 'string', 'max:100'],

            // Prix optionnel : si fourni, doit être > 0 (sinon hérite de base_price du produit)
            'price' => ['nullable', 'numeric', 'min:0.01', 'max:99999999.99'],

            'status' => ['required', Rule::in(ProductVariantStatus::values())],

            'is_default' => ['sometimes', 'boolean'],

            // Valeurs d'attributs optionnelles (pivot product_variant_attribute_value)
            'attribute_value_ids' => ['sometimes', 'array'],
            'attribute_value_ids.*' => ['integer', 'exists:product_attribute_values,id'],
        ];
    }

    /**
     * Messages de validation personnalisés.
     */
    public function messages(): array
    {
        return [
            'product_id.required' => __("{$this->langPath}.product_id.required"),
            'product_id.integer' => __("{$this->langPath}.product_id.integer"),
            'product_id.exists' => __("{$this->langPath}.product_id.exists"),

            'sku.required' => __("{$this->langPath}.sku.required"),
            'sku.string' => __("{$this->langPath}.sku.string"),
            'sku.max' => __("{$this->langPath}.sku.max"),
            'sku.unique' => __("{$this->langPath}.sku.unique"),

            'barcode.string' => __("{$this->langPath}.barcode.string"),
            'barcode.max' => __("{$this->langPath}.barcode.max"),

            'price.numeric' => __("{$this->langPath}.price.numeric"),
            'price.min' => __("{$this->langPath}.price.min"),
            'price.max' => __("{$this->langPath}.price.max"),

            'status.required' => __("{$this->langPath}.status.required"),
            'status.in' => __("{$this->langPath}.status.in"),

            'is_default.boolean' => __("{$this->langPath}.is_default.boolean"),

            'attribute_value_ids.array' => __("{$this->langPath}.attribute_value_ids.array"),
            'attribute_value_ids.*.exists' => __("{$this->langPath}.attribute_value_ids.exists"),
        ];
    }
}