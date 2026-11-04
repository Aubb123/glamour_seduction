<?php

namespace App\Http\Requests\V1\Setting;

use Illuminate\Foundation\Http\FormRequest;

use function __;

class UpdateSettingRequest extends FormRequest
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/settings/messages/settings-messages-validation.settings';

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
            // Chaque setting est indexé par son UUID
            'settings' => ['required', 'array', 'min:1'],

            // UUID du setting (doit exister dans la table settings)
            'settings.*.uuid' => ['required', 'string', 'exists:settings,uuid'],

            // Valeur du setting — optionnelle (nullable en BDD)
            'settings.*.value' => ['nullable', 'string'],

            // Visibilité publique — champ caché '0' + checkbox '1' si cochée
            'settings.*.is_public' => ['nullable', 'in:0,1'],

            // Traductions — optionnelles (tableau indexé par lang_id)
            'settings.*.translations' => ['nullable', 'array'],

            // Langue de la traduction — obligatoire si une traduction est fournie
            'settings.*.translations.*.lang_id' => ['required_with:settings.*.translations.*', 'integer', 'exists:langs,id'],

            // Valeur traduite — optionnelle (nullable en BDD)
            'settings.*.translations.*.value' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'settings.required' => __("{$this->backendLangPath}.settings.required"),
            'settings.array' => __("{$this->backendLangPath}.settings.array"),
            'settings.min' => __("{$this->backendLangPath}.settings.min"),

            'settings.*.uuid.required' => __("{$this->backendLangPath}.uuid.required"),
            'settings.*.uuid.exists' => __("{$this->backendLangPath}.uuid.exists"),

            'settings.*.value.string' => __("{$this->backendLangPath}.value.string"),

            'settings.*.translations.*.lang_id.required_with' => __("{$this->backendLangPath}.translations.lang_id.required"),
            'settings.*.translations.*.lang_id.integer' => __("{$this->backendLangPath}.translations.lang_id.integer"),
            'settings.*.translations.*.lang_id.exists' => __("{$this->backendLangPath}.translations.lang_id.exists"),

            'settings.*.translations.*.value.string' => __("{$this->backendLangPath}.translations.value.string"),
        ];
    }
}
