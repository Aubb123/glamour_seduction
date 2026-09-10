<?php

namespace App\Http\Requests\V1\Products\Warehouses;

use App\Enums\Products\Warehouses\ProductWarehouseStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

class StoreProductWarehouseRequest extends FormRequest
{
    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/products/warehouses/messages/warehouses-messages-validation.productWarehouses';

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
            // Code obligatoire et unique (contrainte DB + validation)
            'code' => ['required', 'string', 'max:20', 'unique:product_warehouses,code'],

            // Nom obligatoire (non traduit, donnée de gestion interne)
            'name' => ['required', 'string', 'max:255'],

            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],

            'status' => ['required', Rule::in(ProductWarehouseStatus::values())],

            'is_default' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Messages de validation personnalisés.
     */
    public function messages(): array
    {
        return [
            'code.required' => __("{$this->langPath}.code.required"),
            'code.string' => __("{$this->langPath}.code.string"),
            'code.max' => __("{$this->langPath}.code.max"),
            'code.unique' => __("{$this->langPath}.code.unique"),

            'name.required' => __("{$this->langPath}.name.required"),
            'name.string' => __("{$this->langPath}.name.string"),
            'name.max' => __("{$this->langPath}.name.max"),

            'address.string' => __("{$this->langPath}.address.string"),
            'address.max' => __("{$this->langPath}.address.max"),
            'city.string' => __("{$this->langPath}.city.string"),
            'city.max' => __("{$this->langPath}.city.max"),
            'phone.string' => __("{$this->langPath}.phone.string"),
            'phone.max' => __("{$this->langPath}.phone.max"),

            'status.required' => __("{$this->langPath}.status.required"),
            'status.in' => __("{$this->langPath}.status.in"),

            'is_default.boolean' => __("{$this->langPath}.is_default.boolean"),
        ];
    }
}