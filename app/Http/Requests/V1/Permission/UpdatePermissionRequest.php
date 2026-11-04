<?php

namespace App\Http\Requests\V1\Permission;

use App\Enums\Permissions\PermissionStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

class UpdatePermissionRequest extends FormRequest
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/permissions/messages/permission-messages-validation.permissions';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // ← géré par middleware
    }

    public function rules(): array
    {
        $uuid = $this->route('uuid');
        $guardName = $this->input('guard_name', 'web');

        return [
            'name' => [
                'sometimes',
                'string',
                'max:255',
                'regex:/^[a-z0-9\.\-\_]+$/',
                Rule::unique('permissions', 'name')
                    ->ignore($uuid, 'uuid')
                    ->where(fn ($query) => $query->where('guard_name', $guardName)),
            ],
            'guard_name' => ['sometimes', 'string', 'max:255'],
            'status' => ['required', Rule::in(PermissionStatus::values())],

            // Rôles optionnels
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __("{$this->backendLangPath}.name.required"),
            'name.string' => __("{$this->backendLangPath}.name.string"),
            'name.max' => __("{$this->backendLangPath}.name.max"),
            'name.unique' => __("{$this->backendLangPath}.name.unique"),
            'name.regex' => __("{$this->backendLangPath}.name.regex"),
            'guard_name.required' => __("{$this->backendLangPath}.guard_name.required"),
            'guard_name.string' => __("{$this->backendLangPath}.guard_name.string"),
            'status.required' => __("{$this->backendLangPath}.status.required"),
            'status.in' => __("{$this->backendLangPath}.status.in"),
            'roles.array' => __("{$this->backendLangPath}.roles.array"),
            'roles.*.exists' => __("{$this->backendLangPath}.roles.exists"),
        ];
    }
}
