<?php

namespace App\Http\Requests\V1\User;

use App\Enums\Users\UserStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

class UpdateUserRequest extends FormRequest
{
    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/users/messages/user-messages-validation.users';

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
            'email.prohibited' => __("{$this->langPath}.email.prohibited"),

            'first_name.required' => __("{$this->langPath}.first_name.required"),
            'first_name.string' => __("{$this->langPath}.first_name.string"),
            'first_name.max' => __("{$this->langPath}.first_name.max"),

            'last_name.required' => __("{$this->langPath}.last_name.required"),
            'last_name.string' => __("{$this->langPath}.last_name.string"),
            'last_name.max' => __("{$this->langPath}.last_name.max"),

            'gender.required' => __("{$this->langPath}.gender.required"),
            'gender.in' => __("{$this->langPath}.gender.in"),

            'status.required' => __("{$this->langPath}.status.required"),
            'status.in' => __("{$this->langPath}.status.in"),

            'country_id.required' => __("{$this->langPath}.country_id.required"),
            'country_id.exists' => __("{$this->langPath}.country_id.exists"),

            'password.min' => __("{$this->langPath}.password.min"),
            'password.string' => __("{$this->langPath}.password.string"),

            'roles.required' => __("{$this->langPath}.roles.required"),
            'roles.array' => __("{$this->langPath}.roles.array"),
            'roles.*.exists' => __("{$this->langPath}.roles.exists"),

            'cloud_platform_uuid.string' => __("{$this->langPath}.cloud_platform_uuid.string"),
            'cloud_platform_uuid.exists' => __("{$this->langPath}.cloud_platform_uuid.exists"),

            'image.image' => __("{$this->langPath}.image.image"),
            'image.mimes' => __("{$this->langPath}.image.mimes"),
            'image.max' => __("{$this->langPath}.image.max"),
        ];
    }
}
