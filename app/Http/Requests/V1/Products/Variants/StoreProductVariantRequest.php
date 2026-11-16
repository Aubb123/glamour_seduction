<?php

namespace App\Http\Requests\V1\Products\Variants;

use App\Enums\Products\Variants\ProductVariantStatus;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

class StoreProductVariantRequest extends FormRequest
{
    protected string $backendLangPath = 'v1/backend/views/pages/products/variants/messages/variants-messages-validation.productVariants';

    public function authorize(): bool
    {
        return true; // access controlled by middleware
    }

    public function rules(): array
    {
        return [
            'sku' => ['nullable', 'string', 'max:100', 'unique:product_variants,sku'],
            'barcode' => ['nullable', 'string', 'max:100', 'unique:product_variants,barcode'],
            'price' => ['nullable', 'numeric', 'min:0.01', 'max:99999999.99'],
            'status' => ['required', Rule::in(ProductVariantStatus::values())],
            'is_default' => ['sometimes', 'boolean'],
            'attribute_value_ids' => [
                'sometimes',
                'array',
                // Une même combinaison de valeurs ne peut exister qu'une fois par produit.
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $product = Product::where('uuid', $this->query('product'))->first();

                    if (! $product) {
                        return; // Produit introuvable : la résolution est gérée par le contrôleur.
                    }

                    if (ProductVariant::attributeCombinationExists($product->id, (array) $value)) {
                        $fail(__("{$this->backendLangPath}.attribute_value_ids.duplicate_combination"));
                    }
                },
            ],
            'attribute_value_ids.*' => ['integer', 'exists:product_attribute_values,id'],
            'cloud_platform_uuid' => ['nullable', 'required_with:files', 'exists:cloud_platforms,uuid'],
            'files' => ['nullable', 'array', 'max:4'],
            'files.*' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'sku.string' => __("{$this->backendLangPath}.sku.string"),
            'sku.max' => __("{$this->backendLangPath}.sku.max"),
            'sku.unique' => __("{$this->backendLangPath}.sku.unique"),
            'barcode.string' => __("{$this->backendLangPath}.barcode.string"),
            'barcode.max' => __("{$this->backendLangPath}.barcode.max"),
            'barcode.unique' => __("{$this->backendLangPath}.barcode.unique"),
            'price.numeric' => __("{$this->backendLangPath}.price.numeric"),
            'price.min' => __("{$this->backendLangPath}.price.min"),
            'price.max' => __("{$this->backendLangPath}.price.max"),
            'status.required' => __("{$this->backendLangPath}.status.required"),
            'status.in' => __("{$this->backendLangPath}.status.in"),
            'is_default.boolean' => __("{$this->backendLangPath}.is_default.boolean"),
            'attribute_value_ids.array' => __("{$this->backendLangPath}.attribute_value_ids.array"),
            'attribute_value_ids.duplicate_combination' => __("{$this->backendLangPath}.attribute_value_ids.duplicate_combination"),
            'attribute_value_ids.*.exists' => __("{$this->backendLangPath}.attribute_value_ids.exists"),
            'cloud_platform_uuid.required_with' => __("{$this->backendLangPath}.cloud_platform_uuid.required_with"),
            'cloud_platform_uuid.exists' => __("{$this->backendLangPath}.cloud_platform_uuid.exists"),
            'files.array' => __("{$this->backendLangPath}.files.array"),
            'files.max' => __("{$this->backendLangPath}.files.max"),
            'files.*.file' => __("{$this->backendLangPath}.files.file"),
            'files.*.mimes' => __("{$this->backendLangPath}.files.mimes"),
            'files.*.max' => __("{$this->backendLangPath}.files.max_file"),
        ];
    }
}
