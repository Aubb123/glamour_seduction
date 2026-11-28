<?php

namespace App\Http\Requests\V1\Products\WarehouseLocationStocks;

use Illuminate\Foundation\Http\FormRequest;

use function __;

/**
 * Requête de validation pour la mise à jour d'un solde de stock par emplacement.
 *
 * NB : `location_id` et `product_variant_id` ne sont jamais modifiés après création
 * (immuables). Seule la quantité peut évoluer.
 */
class UpdateProductWarehouseLocationStockRequest extends FormRequest
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
            'quantity' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'quantity.required' => __("{$this->backendLangPath}.quantity.required"),
            'quantity.integer' => __("{$this->backendLangPath}.quantity.integer"),
            'quantity.min' => __("{$this->backendLangPath}.quantity.min"),
        ];
    }
}
