<?php

namespace App\Http\Requests\V1\Products\Variants;

use App\Enums\Products\Variants\ProductVariantStatus;
use App\Models\ProductVariant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

class UpdateProductVariantRequest extends FormRequest
{
    protected string $backendLangPath = 'v1/backend/views/pages/products/variants/messages/variants-messages-validation.productVariants';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Limites métier pilotées par la table settings (aucune valeur codée en dur).
        $maxVariantImages = (int) getRequiredSettingValue('max_product_variant_images', app()->getLocale());
        $maxProductPrice = getRequiredSettingValue('max_product_price', app()->getLocale());

        return [
            'sku' => ['sometimes', 'nullable', 'string', 'max:100', Rule::unique('product_variants', 'sku')->ignore($this->route('productVariant'))],
            'barcode' => ['nullable', 'string', 'max:100', Rule::unique('product_variants', 'barcode')->ignore($this->route('productVariant'))],
            'price' => ['nullable', 'numeric', 'min:0.01', 'max:'.$maxProductPrice],
            'status' => ['sometimes', 'required', Rule::in(ProductVariantStatus::values())],
            'is_default' => ['sometimes', 'boolean'],
            'attribute_value_ids' => [
                'sometimes',
                'array',
                // Une même combinaison de valeurs ne peut exister qu'une fois par produit (hors variante courante).
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $variant = $this->route('productVariant');

                    if (! $variant instanceof ProductVariant) {
                        return; // Résolution gérée par le model binding / le contrôleur.
                    }

                    if (ProductVariant::attributeCombinationExists($variant->product_id, (array) $value, $variant->id)) {
                        $fail(__("{$this->backendLangPath}.attribute_value_ids.duplicate_combination"));
                    }
                },
            ],
            'attribute_value_ids.*' => ['integer', 'exists:product_attribute_values,id'],
            'cloud_platform_uuid' => ['nullable', 'required_with:files', 'exists:cloud_platforms,uuid'],
            'files' => ['nullable', 'array', 'max:'.$maxVariantImages],
            'files.*' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:'.getMaxImageSizeKb()],
            'remove_cloud_file_uuids' => ['sometimes', 'array', 'max:'.$maxVariantImages],
            'remove_cloud_file_uuids.*' => ['required', 'uuid', 'distinct'],
        ];
    }

    public function messages(): array
    {
        $maxVariantImages = (int) getRequiredSettingValue('max_product_variant_images', app()->getLocale());
        $maxProductPrice = getRequiredSettingValue('max_product_price', app()->getLocale());

        return [
            'sku.string' => __("{$this->backendLangPath}.sku.string"),
            'sku.max' => __("{$this->backendLangPath}.sku.max"),
            'sku.unique' => __("{$this->backendLangPath}.sku.unique"),
            'barcode.string' => __("{$this->backendLangPath}.barcode.string"),
            'barcode.max' => __("{$this->backendLangPath}.barcode.max"),
            'barcode.unique' => __("{$this->backendLangPath}.barcode.unique"),
            'price.numeric' => __("{$this->backendLangPath}.price.numeric"),
            'price.min' => __("{$this->backendLangPath}.price.min"),
            'price.max' => __("{$this->backendLangPath}.price.max", ['max' => $maxProductPrice]),
            'status.required' => __("{$this->backendLangPath}.status.required"),
            'status.in' => __("{$this->backendLangPath}.status.in"),
            'is_default.boolean' => __("{$this->backendLangPath}.is_default.boolean"),
            'attribute_value_ids.array' => __("{$this->backendLangPath}.attribute_value_ids.array"),
            'attribute_value_ids.duplicate_combination' => __("{$this->backendLangPath}.attribute_value_ids.duplicate_combination"),
            'attribute_value_ids.*.exists' => __("{$this->backendLangPath}.attribute_value_ids.exists"),
            'cloud_platform_uuid.required_with' => __("{$this->backendLangPath}.cloud_platform_uuid.required_with"),
            'cloud_platform_uuid.exists' => __("{$this->backendLangPath}.cloud_platform_uuid.exists"),
            'files.array' => __("{$this->backendLangPath}.files.array"),
            'files.max' => __("{$this->backendLangPath}.files.max", ['max' => $maxVariantImages]),
            'files.*.file' => __("{$this->backendLangPath}.files.file"),
            'files.*.mimes' => __("{$this->backendLangPath}.files.mimes"),
            'files.*.max' => __("{$this->backendLangPath}.files.max_file", ['max' => getMaxImageSizeMb()]),
            'remove_cloud_file_uuids.array' => __("{$this->backendLangPath}.remove_cloud_file_uuids.array"),
            'remove_cloud_file_uuids.max' => __("{$this->backendLangPath}.remove_cloud_file_uuids.max", ['max' => $maxVariantImages]),
            'remove_cloud_file_uuids.*.uuid' => __("{$this->backendLangPath}.remove_cloud_file_uuids.uuid"),
            'remove_cloud_file_uuids.*.distinct' => __("{$this->backendLangPath}.remove_cloud_file_uuids.distinct"),
        ];
    }
}
