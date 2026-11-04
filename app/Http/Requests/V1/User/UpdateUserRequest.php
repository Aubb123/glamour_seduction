<?php

namespace App\Http\Requests\V1\User;

use App\Enums\Users\UserStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

class UpdateUserRequest extends FormRequest
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/users/messages/user-messages-validation.users';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'prohibited', // Bloque toute tentative d'injection du champ email
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'status' => ['required', Rule::in(UserStatus::values())],
            'country_id' => 'required|exists:geo_countries,id',
            'password' => 'nullable|string|min:8',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
            'cloud_platform_uuid' => 'nullable|string|exists:cloud_platforms,uuid',
            'image' => 'nullable|image|mimes:jpg,png,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'email.prohibited' => __("{$this->backendLangPath}.email.prohibited"),

            'first_name.required' => __("{$this->backendLangPath}.first_name.required"),
            'first_name.string' => __("{$this->backendLangPath}.first_name.string"),
            'first_name.max' => __("{$this->backendLangPath}.first_name.max"),

            'last_name.required' => __("{$this->backendLangPath}.last_name.required"),
            'last_name.string' => __("{$this->backendLangPath}.last_name.string"),
            'last_name.max' => __("{$this->backendLangPath}.last_name.max"),

            'gender.required' => __("{$this->backendLangPath}.gender.required"),
            'gender.in' => __("{$this->backendLangPath}.gender.in"),

            'status.required' => __("{$this->backendLangPath}.status.required"),
            'status.in' => __("{$this->backendLangPath}.status.in"),

            'country_id.required' => __("{$this->backendLangPath}.country_id.required"),
            'country_id.exists' => __("{$this->backendLangPath}.country_id.exists"),

            'password.min' => __("{$this->backendLangPath}.password.min"),
            'password.string' => __("{$this->backendLangPath}.password.string"),

            'roles.required' => __("{$this->backendLangPath}.roles.required"),
            'roles.array' => __("{$this->backendLangPath}.roles.array"),
            'roles.*.exists' => __("{$this->backendLangPath}.roles.exists"),

            'cloud_platform_uuid.string' => __("{$this->backendLangPath}.cloud_platform_uuid.string"),
            'cloud_platform_uuid.exists' => __("{$this->backendLangPath}.cloud_platform_uuid.exists"),

            'image.image' => __("{$this->backendLangPath}.image.image"),
            'image.mimes' => __("{$this->backendLangPath}.image.mimes"),
            'image.max' => __("{$this->backendLangPath}.image.max"),
        ];
    }
}
