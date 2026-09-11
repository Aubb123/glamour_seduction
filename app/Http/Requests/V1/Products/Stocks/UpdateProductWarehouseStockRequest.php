<?php

namespace App\Http\Requests\V1\Products\Stocks;

use App\Models\ProductWarehouseStock;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

use function __;

class UpdateProductWarehouseStockRequest extends FormRequest
{
    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/products/stocks/messages/stocks-messages-validation.productWarehouseStocks';

    /** Identifiant du stock en cours d'édition (règles d'unicité). */
    protected ?int $stockId = null;

    /**
     * Retrouve le stock à partir du binding implicite de la route {productWarehouseStock}.
     */
    protected function prepareForValidation(): void
    {
        $stock = $this->route('productWarehouseStock');

        if (! $stock instanceof ProductWarehouseStock) {
            abort(404);
        }

        $this->stockId = $stock->id;
    }

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
                        __("{$this->langPath}.reserved_quantity.exceeds_quantity", ['quantity' => $quantity])
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
            'warehouse_id.required' => __("{$this->langPath}.warehouse_id.required"),
            'warehouse_id.integer' => __("{$this->langPath}.warehouse_id.integer"),
            'warehouse_id.exists' => __("{$this->langPath}.warehouse_id.exists"),
            'product_variant_id.required' => __("{$this->langPath}.product_variant_id.required"),
            'product_variant_id.integer' => __("{$this->langPath}.product_variant_id.integer"),
            'product_variant_id.exists' => __("{$this->langPath}.product_variant_id.exists"),
            'quantity.required' => __("{$this->langPath}.quantity.required"),
            'quantity.integer' => __("{$this->langPath}.quantity.integer"),
            'quantity.min' => __("{$this->langPath}.quantity.min"),
            'reserved_quantity.integer' => __("{$this->langPath}.reserved_quantity.integer"),
            'reserved_quantity.min' => __("{$this->langPath}.reserved_quantity.min"),
            'low_stock_threshold.required' => __("{$this->langPath}.low_stock_threshold.required"),
            'low_stock_threshold.integer' => __("{$this->langPath}.low_stock_threshold.integer"),
            'low_stock_threshold.min' => __("{$this->langPath}.low_stock_threshold.min"),
        ];
    }
}