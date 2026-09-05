<?php

namespace App\Http\Requests\V1\PlatformVersion;

use App\Enums\PlatformVersions\PlatformVersionStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

use function __;

class StorePlatformVersionRequest extends FormRequest
{
    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/platform-versions/messages/platform-messages-validation';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return false;
        return true; // ← géré par middleware
    }

    /**
     * Normalise les données reçues avant validation :
     * le champ "released_at" provient d'un input type="datetime-local" (ex: "2026-07-25T14:30").
     * On le convertit au format classique "Y-m-d H:i:s" pour que les règles
     * de validation "date" et "after_or_equal:today" soient évaluées correctement.
     */
    protected function prepareForValidation(): void
    {
        $releasedAt = $this->input('released_at');

        if (is_string($releasedAt) && $releasedAt !== '' && str_contains($releasedAt, 'T')) {
            try {
                $this->merge([
                    'released_at' => Carbon::parse($releasedAt)->format('Y-m-d H:i:s'),
                ]);
            } catch (\Throwable $e) {
                // Date invalide : on conserve la valeur brute afin que la règle
                // de validation "date" la rejette proprement et évite tout crash 500.
            }
        }
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:10', 'regex:/^[a-z0-9\-]+$/', 'unique:platform_versions,code'], // regex pour autoriser uniquement les lettres minuscules, chiffres et tirets
            'status' => ['required', Rule::in(PlatformVersionStatus::values())],
            'is_published' => ['boolean'],
            'released_at' => ['nullable', 'date', 'after_or_equal:today'], // La date de lancement est optionnelle et doit être une date supérieure ou égale à aujourd'hui si elle est fournie

            // Translations
            'translations' => ['required', 'array', 'min:1'], // Au moins une traduction est obligatoire
            'translations.*.lang_id' => ['required', 'integer', 'exists:langs,id'],
            'translations.*.name' => ['required', 'string', 'max:255'],
            'translations.*.description' => ['required', 'string'],
        ];
    }

    /**
     * Validate qu'il n'y a pas de doublon de langue dans les traductions.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $langIds = collect($this->input('translations', []))->pluck('lang_id');

            if ($langIds->count() !== $langIds->unique()->count()) {
                $validator->errors()->add('translations', __("{$this->langPath}.translations.lang_id.duplicate"));
            }
        });
    }

    public function messages(): array
    {
        return [
            // Code
            'code.required' => __("{$this->langPath}.code.required"),
            'code.string' => __("{$this->langPath}.code.string"),
            'code.max' => __("{$this->langPath}.code.max"),
            'code.unique' => __("{$this->langPath}.code.unique"),
            'code.regex' => __("{$this->langPath}.code.regex"),

            // Released at
            'released_at.date' => __("{$this->langPath}.released_at.date"),
            'released_at.after_or_equal' => __("{$this->langPath}.released_at.after_or_equal"),

            'status.required' => __("{$this->langPath}.status.required"),
            'status.in' => __("{$this->langPath}.status.in"),

            // Translations
            'translations.required' => __("{$this->langPath}.translations.required"),
            'translations.array' => __("{$this->langPath}.translations.array"),
            'translations.min' => __("{$this->langPath}.translations.min"),

            'translations.*.lang_id.required' => __("{$this->langPath}.translations.lang_id.required"),
            'translations.*.lang_id.integer' => __("{$this->langPath}.translations.lang_id.integer"),
            'translations.*.lang_id.exists' => __("{$this->langPath}.translations.lang_id.exists"),

            'translations.*.name.required' => __("{$this->langPath}.translations.name.required"),
            'translations.*.name.string' => __("{$this->langPath}.translations.name.string"),
            'translations.*.name.max' => __("{$this->langPath}.translations.name.max"),

            'translations.*.description.required' => __("{$this->langPath}.translations.description.required"),
            'translations.*.description.string' => __("{$this->langPath}.translations.description.string"),
        ];
    }
}
