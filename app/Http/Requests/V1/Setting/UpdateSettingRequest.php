<?php

namespace App\Http\Requests\V1\Setting;

use Illuminate\Foundation\Http\FormRequest;

use function __;

class UpdateSettingRequest extends FormRequest
{
    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/settings/messages/settings-messages-validation.settings';

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
            'settings.required' => __("{$this->langPath}.settings.required"),
            'settings.array' => __("{$this->langPath}.settings.array"),
            'settings.min' => __("{$this->langPath}.settings.min"),

            'settings.*.uuid.required' => __("{$this->langPath}.uuid.required"),
            'settings.*.uuid.exists' => __("{$this->langPath}.uuid.exists"),

            'settings.*.value.string' => __("{$this->langPath}.value.string"),

            'settings.*.translations.*.lang_id.required_with' => __("{$this->langPath}.translations.lang_id.required"),
            'settings.*.translations.*.lang_id.integer' => __("{$this->langPath}.translations.lang_id.integer"),
            'settings.*.translations.*.lang_id.exists' => __("{$this->langPath}.translations.lang_id.exists"),

            'settings.*.translations.*.value.string' => __("{$this->langPath}.translations.value.string"),
        ];
    }
}
