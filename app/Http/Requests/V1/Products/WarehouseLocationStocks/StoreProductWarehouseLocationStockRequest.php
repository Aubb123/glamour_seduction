<?php

namespace App\Http\Requests\V1\Products\WarehouseLocationStocks;

use Illuminate\Foundation\Http\FormRequest;

use function __;

/**
 * Requête de validation pour la création d'un solde de stock par emplacement.
 *
 * Règles métier :
 *  - la quantité ne peut pas être négative ;
 *  - un entrepôt en mode « warehouse » refuse un stock par emplacement (vérifié dans le service) ;
 *  - l'emplacement doit appartenir à l'entrepôt concerné (vérifié dans le service) ;
 *  - un couple (emplacement, variante) ne peut exister qu'une seule fois (contrainte en base).
 */
class StoreProductWarehouseLocationStockRequest extends FormRequest
{
    /**
     * Chemin de base pour les traductions des messages de validation.
     */
    protected string $backendLangPath = 'v1/backend/views/pages/products/warehouse-location-stocks/messages/warehouse-location-stocks-messages-validation.warehouseLocationStocks';

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
            'location_id' => ['required', 'integer', 'exists:product_warehouse_locations,id'],
            'product_variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'location_id.required' => __("{$this->backendLangPath}.location_id.required"),
            'location_id.integer' => __("{$this->backendLangPath}.location_id.integer"),
            'location_id.exists' => __("{$this->backendLangPath}.location_id.exists"),

            'product_variant_id.required' => __("{$this->backendLangPath}.product_variant_id.required"),
            'product_variant_id.integer' => __("{$this->backendLangPath}.product_variant_id.integer"),
            'product_variant_id.exists' => __("{$this->backendLangPath}.product_variant_id.exists"),

            'quantity.required' => __("{$this->backendLangPath}.quantity.required"),
            'quantity.integer' => __("{$this->backendLangPath}.quantity.integer"),
            'quantity.min' => __("{$this->backendLangPath}.quantity.min"),
        ];
    }
}
