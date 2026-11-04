<?php

namespace App\Http\Requests\V1\Products\Variants;

use App\Enums\Products\Variants\ProductVariantStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

class StoreProductVariantRequest extends FormRequest
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/products/variants/messages/variants-messages-validation.productVariants';

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
            'product_id.required' => __("{$this->backendLangPath}.product_id.required"),
            'product_id.integer' => __("{$this->backendLangPath}.product_id.integer"),
            'product_id.exists' => __("{$this->backendLangPath}.product_id.exists"),

            'sku.required' => __("{$this->backendLangPath}.sku.required"),
            'sku.string' => __("{$this->backendLangPath}.sku.string"),
            'sku.max' => __("{$this->backendLangPath}.sku.max"),
            'sku.unique' => __("{$this->backendLangPath}.sku.unique"),

            'barcode.string' => __("{$this->backendLangPath}.barcode.string"),
            'barcode.max' => __("{$this->backendLangPath}.barcode.max"),

            'price.numeric' => __("{$this->backendLangPath}.price.numeric"),
            'price.min' => __("{$this->backendLangPath}.price.min"),
            'price.max' => __("{$this->backendLangPath}.price.max"),

            'status.required' => __("{$this->backendLangPath}.status.required"),
            'status.in' => __("{$this->backendLangPath}.status.in"),

            'is_default.boolean' => __("{$this->backendLangPath}.is_default.boolean"),

            'attribute_value_ids.array' => __("{$this->backendLangPath}.attribute_value_ids.array"),
            'attribute_value_ids.*.exists' => __("{$this->backendLangPath}.attribute_value_ids.exists"),
        ];
    }
}
