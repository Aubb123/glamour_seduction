<?php

namespace App\Http\Requests\V1\Products\Variants;

use App\Enums\Products\Variants\ProductVariantStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

class StoreProductVariantRequest extends FormRequest
{
    protected string $backendLangPath = 'v1/backend/views/pages/products/variants/messages/variants-messages-validation.productVariants';

    public function authorize(): bool
    {
        return true; // géré par middleware
    }

    public function rules(): array
    {
        return [
            'sku' => ['required', 'string', 'max:100', 'unique:product_variants,sku'],
            'barcode' => ['nullable', 'string', 'max:100'],
            'price' => ['nullable', 'numeric', 'min:0.01', 'max:99999999.99'],
            'status' => ['required', Rule::in(ProductVariantStatus::values())],
            'is_default' => ['sometimes', 'boolean'],
            'attribute_value_ids' => ['sometimes', 'array'],
            'attribute_value_ids.*' => ['integer', 'exists:product_attribute_values,id'],
            // Galerie facultative, maximum quatre images par variante.
            'cloud_platform_uuid' => ['nullable', 'required_with:files', 'exists:cloud_platforms,uuid'],
            'files' => ['nullable', 'array', 'max:4'],
            'files.*' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
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
            'cloud_platform_uuid.required_with' => __('Sélectionnez une plateforme cloud pour téléverser les images.'),
            'cloud_platform_uuid.exists' => __('La plateforme cloud sélectionnée est introuvable.'),
            'files.array' => __('Les images doivent être envoyées sous forme de liste.'),
            'files.max' => __('Une variante ne peut pas avoir plus de 4 images.'),
            'files.*.mimes' => __('Formats acceptés : JPG, JPEG, PNG, WEBP et GIF.'),
            'files.*.max' => __('Chaque image ne doit pas dépasser 2 Mo.'),
        ];
    }
}
