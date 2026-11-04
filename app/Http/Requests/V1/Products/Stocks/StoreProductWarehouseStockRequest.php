<?php

namespace App\Http\Requests\V1\Products\Stocks;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

use function __;

class StoreProductWarehouseStockRequest extends FormRequest
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/products/stocks/messages/stocks-messages-validation.productWarehouseStocks';

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
        // Vérifie que le couple (entrepôt, variante) n'existe pas déjà (contrainte unique DB)
        $existsRule = Rule::unique('product_warehouse_stocks')
            ->where(fn ($query) => $query
                ->where('warehouse_id', $this->input('warehouse_id'))
                ->where('product_variant_id', $this->input('product_variant_id')));

        return [
            'warehouse_id' => ['required', 'integer', 'exists:product_warehouses,id'],
            'product_variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:0'],
            'reserved_quantity' => ['nullable', 'integer', 'min:0'],
            'low_stock_threshold' => ['required', 'integer', 'min:0'],
        ];
    }

    /**
     * Validation conditionnelle : `reserved_quantity` ne peut pas dépasser `quantity`.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $quantity = (int) $this->input('quantity', 0);
                $reserved = (int) ($this->input('reserved_quantity') ?? 0);

                if ($reserved > $quantity) {
                    $validator->errors()->add(
                        'reserved_quantity',
                        __("{$this->backendLangPath}.reserved_quantity.exceeds_quantity", ['quantity' => $quantity])
                    );
                }
            },
        ];
    }

    /**
     * Messages de validation personnalisés.
     */
    public function messages(): array
    {
        return [
            'warehouse_id.required' => __("{$this->backendLangPath}.warehouse_id.required"),
            'warehouse_id.integer' => __("{$this->backendLangPath}.warehouse_id.integer"),
            'warehouse_id.exists' => __("{$this->backendLangPath}.warehouse_id.exists"),
            'product_variant_id.required' => __("{$this->backendLangPath}.product_variant_id.required"),
            'product_variant_id.integer' => __("{$this->backendLangPath}.product_variant_id.integer"),
            'product_variant_id.exists' => __("{$this->backendLangPath}.product_variant_id.exists"),
            'quantity.required' => __("{$this->backendLangPath}.quantity.required"),
            'quantity.integer' => __("{$this->backendLangPath}.quantity.integer"),
            'quantity.min' => __("{$this->backendLangPath}.quantity.min"),
            'reserved_quantity.integer' => __("{$this->backendLangPath}.reserved_quantity.integer"),
            'reserved_quantity.min' => __("{$this->backendLangPath}.reserved_quantity.min"),
            'low_stock_threshold.required' => __("{$this->backendLangPath}.low_stock_threshold.required"),
            'low_stock_threshold.integer' => __("{$this->backendLangPath}.low_stock_threshold.integer"),
            'low_stock_threshold.min' => __("{$this->backendLangPath}.low_stock_threshold.min"),
        ];
    }
}
