<?php

namespace App\Http\Requests\V1\Products\WarehouseStocks;

use Illuminate\Foundation\Http\FormRequest;

use function __;

/**
 * Requête de validation pour la création d'un solde de stock par entrepôt.
 *
 * Règles métier :
 *  - la quantité ne peut pas être négative ;
 *  - un entrepôt en mode « location » refuse un stock global (vérifié dans le service) ;
 *  - un couple (entrepôt, variante) ne peut exister qu'une seule fois (contrainte en base).
 */
class StoreProductWarehouseStockRequest extends FormRequest
{
    /**
     * Chemin de base pour les traductions des messages de validation.
     */
    protected string $backendLangPath = 'v1/backend/views/pages/products/warehouse-stocks/messages/warehouse-stocks-messages-validation.warehouseStocks';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // ← géré par middleware
    }

    public function rules(): array
    {
        return [
            'warehouse_id' => ['required', 'integer', 'exists:product_warehouses,id'],
            'product_variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:0'],
        ];
    }

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
        ];
    }
}
