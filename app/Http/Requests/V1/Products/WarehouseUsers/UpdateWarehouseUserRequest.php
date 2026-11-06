<?php

namespace App\Http\Requests\V1\Products\WarehouseUsers;

use Illuminate\Foundation\Http\FormRequest;

use function __;

class UpdateWarehouseUserRequest extends FormRequest
{
    // Chemin de base pour les traductions des messages de validation
    protected string $backendLangPath = 'v1/backend/views/pages/products/warehouses/messages/warehouses-messages-validation.warehouseUsers';

    public function authorize(): bool
    {
        return true; // ← géré par middleware
    }

    public function rules(): array
    {
        return [
            'position_uuid' => ['nullable', 'exists:positions,uuid'],
            'is_manager' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'position_uuid.exists' => __("{$this->backendLangPath}.position_uuid.exists"),
            'is_manager.boolean' => __("{$this->backendLangPath}.is_manager.boolean"),
        ];
    }
}
