<?php

namespace App\Http\Requests\V1\Products\Warehouses;

use App\Enums\Products\Warehouses\ProductWarehouseStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

class StoreProductWarehouseRequest extends FormRequest
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/products/warehouses/messages/warehouses-messages-validation.productWarehouses';

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
            'code.required' => __("{$this->backendLangPath}.code.required"),
            'code.string' => __("{$this->backendLangPath}.code.string"),
            'code.max' => __("{$this->backendLangPath}.code.max"),
            'code.unique' => __("{$this->backendLangPath}.code.unique"),

            'name.required' => __("{$this->backendLangPath}.name.required"),
            'name.string' => __("{$this->backendLangPath}.name.string"),
            'name.max' => __("{$this->backendLangPath}.name.max"),

            'address.string' => __("{$this->backendLangPath}.address.string"),
            'address.max' => __("{$this->backendLangPath}.address.max"),
            'city.string' => __("{$this->backendLangPath}.city.string"),
            'city.max' => __("{$this->backendLangPath}.city.max"),
            'phone.string' => __("{$this->backendLangPath}.phone.string"),
            'phone.max' => __("{$this->backendLangPath}.phone.max"),

            'status.required' => __("{$this->backendLangPath}.status.required"),
            'status.in' => __("{$this->backendLangPath}.status.in"),

            'is_default.boolean' => __("{$this->backendLangPath}.is_default.boolean"),
        ];
    }
}
