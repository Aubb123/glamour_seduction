<?php

namespace App\Http\Requests\V1\Role;

use App\Enums\Roles\RoleStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

class UpdateRoleRequest extends FormRequest
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/roles/messages/role-messages-validation';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Statut du rôle — nullable + valeurs de l'enum
            'status' => ['required', Rule::in(RoleStatus::values())],

            // Traductions : au moins une traduction est obligatoire
            'translations' => ['required', 'array', 'min:1'],

            // Identifiant de la langue
            'translations.*.lang_id' => ['required', 'integer', 'exists:langs,id'],

            // Libellé du rôle
            'translations.*.label' => ['required', 'string', 'max:255'],

            // Description
            'translations.*.description' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => __("{$this->backendLangPath}.status.required"),
            'status.in' => __("{$this->backendLangPath}.status.in"),

            // Traductions (tableau)
            'translations.required' => __("{$this->backendLangPath}.translations.required"),
            'translations.array' => __("{$this->backendLangPath}.translations.array"),
            'translations.min' => __("{$this->backendLangPath}.translations.min"),

            // Langue de la traduction
            'translations.*.lang_id.required' => __("{$this->backendLangPath}.translations.lang_id.required"),
            'translations.*.lang_id.integer' => __("{$this->backendLangPath}.translations.lang_id.integer"),
            'translations.*.lang_id.exists' => __("{$this->backendLangPath}.translations.lang_id.exists"),

            // Libellé
            'translations.*.label.required' => __("{$this->backendLangPath}.translations.label.required"),
            'translations.*.label.string' => __("{$this->backendLangPath}.translations.label.string"),
            'translations.*.label.max' => __("{$this->backendLangPath}.translations.label.max"),

            // Description
            'translations.*.description.required' => __("{$this->backendLangPath}.translations.description.required"),
            'translations.*.description.string' => __("{$this->backendLangPath}.translations.description.string"),
        ];
    }
}
