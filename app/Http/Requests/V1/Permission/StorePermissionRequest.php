<?php

namespace App\Http\Requests\V1\Permission;

use App\Enums\Permissions\PermissionStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

class StorePermissionRequest extends FormRequest
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
        $guardName = $this->input('guard_name', 'web');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9\.\-\_]+$/',
                Rule::unique('permissions', 'name')
                    ->where(fn ($query) => $query->where('guard_name', $guardName)),
            ],
            'guard_name' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(PermissionStatus::values())],

            // Rôles optionnels
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,id'],
        ];
    }

    public function messages(): array
    {
        return [
            // Name
            'name.required' => __("{$this->backendLangPath}.name.required"),
            'name.string' => __("{$this->backendLangPath}.name.string"),
            'name.max' => __("{$this->backendLangPath}.name.max"),
            'name.unique' => __("{$this->backendLangPath}.name.unique"),
            'name.regex' => __("{$this->backendLangPath}.name.regex"),

            // Guard name
            'guard_name.required' => __("{$this->backendLangPath}.guard_name.required"),
            'guard_name.string' => __("{$this->backendLangPath}.guard_name.string"),

            // Is active
            'status.required' => __("{$this->backendLangPath}.status.required"),
            'status.in' => __("{$this->backendLangPath}.status.in"),

            // Roles
            'roles.array' => __("{$this->backendLangPath}.roles.array"),
            'roles.*.exists' => __("{$this->backendLangPath}.roles.exists"),
        ];
    }
}
