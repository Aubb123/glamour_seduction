<?php

namespace App\Http\Requests\V1\Permission;

use Illuminate\Foundation\Http\FormRequest;

use function __;

class SyncRolesPermissionRequest extends FormRequest
{
    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/permissions/messages/permission-messages-validation.permissions';

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
            'role_ids' => ['nullable', 'array'],
            'role_ids.*' => ['exists:roles,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'role_ids.array' => __("{$this->langPath}.roles.array"),
            'role_ids.*.exists' => __("{$this->langPath}.roles.exists"),
        ];
    }
}
