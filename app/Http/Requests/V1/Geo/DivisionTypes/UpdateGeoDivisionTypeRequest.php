<?php

namespace App\Http\Requests\V1\Geo\DivisionTypes;

use App\Enums\Geos\DivisionTypes\GeoDivisionTypeStatus;
use App\Models\GeoDivisionType;
use App\Models\GeoDivisionTypeTranslation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

use function __;

class UpdateGeoDivisionTypeRequest extends FormRequest
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/geos/division-types/messages/division-types-messages-validation';

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
            // ⚠️ `code` est IMMUABLE : absent des règles (jamais mis à jour).

            // Niveau par défaut suggéré
            'level' => ['required', 'integer', 'min:1'],

            // Ordre d'affichage — entier positif ou nul (pas d'unicité)
            'sort_order' => ['required', 'integer', 'min:0'],

            // Statut
            'status' => ['required', Rule::in(GeoDivisionTypeStatus::values())],

            // Traductions
            'translations' => ['required', 'array', 'min:1'],

            'translations.*.lang_id' => ['required', 'integer', 'exists:langs,id'],
            'translations.*.name' => ['required', 'string', 'max:100'],
            'translations.*.description' => ['required', 'string'],
        ];
    }

    /**
     * Vérifications métier transverses (doublons de langue, unicité du nom par langue).
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {

            /** @var GeoDivisionType|null $divisionType */
            $divisionType = $this->route('geoDivisionType');
            $divisionTypeId = $divisionType?->id;

            $translations = (array) $this->input('translations', []);

            // 1. Doublons de lang_id dans le payload
            $langIds = array_filter(array_map(fn ($t) => $t['lang_id'] ?? null, $translations));

            if (count($langIds) !== count(array_unique($langIds))) {
                $validator->errors()->add('translations', __("{$this->backendLangPath}.translations.duplicate_lang"));
            }

            // 2. Unicité du nom par langue (corbeille incluse), hors type courant
            foreach ($translations as $index => $translation) {
                $langId = $translation['lang_id'] ?? null;
                $name = trim((string) ($translation['name'] ?? ''));

                if ($langId === null || $name === '') {
                    continue;
                }

                $exists = GeoDivisionTypeTranslation::withTrashed()
                    ->where('lang_id', $langId)
                    ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
                    ->where('division_type_id', '!=', $divisionTypeId)
                    ->exists();

                if ($exists) {
                    $validator->errors()->add(
                        "translations.{$index}.name",
                        __("{$this->backendLangPath}.translations.name.unique", ['name' => $name])
                    );
                }
            }
        });
    }

    /**
     * Get the custom validation messages.
     */
    public function messages(): array
    {
        return [
            'level.required' => __($this->backendLangPath.'.level.required'),
            'level.integer' => __($this->backendLangPath.'.level.integer'),
            'level.min' => __($this->backendLangPath.'.level.min'),

            'sort_order.required' => __($this->backendLangPath.'.sort_order.required'),
            'sort_order.integer' => __($this->backendLangPath.'.sort_order.integer'),
            'sort_order.min' => __($this->backendLangPath.'.sort_order.min'),

            'status.required' => __($this->backendLangPath.'.status.required'),
            'status.in' => __($this->backendLangPath.'.status.in'),

            'translations.required' => __($this->backendLangPath.'.translations.required'),
            'translations.array' => __($this->backendLangPath.'.translations.array'),
            'translations.min' => __($this->backendLangPath.'.translations.min'),

            'translations.*.lang_id.required' => __($this->backendLangPath.'.translations.lang_id.required'),
            'translations.*.lang_id.integer' => __($this->backendLangPath.'.translations.lang_id.integer'),
            'translations.*.lang_id.exists' => __($this->backendLangPath.'.translations.lang_id.exists'),

            'translations.*.name.required' => __($this->backendLangPath.'.translations.name.required'),
            'translations.*.name.string' => __($this->backendLangPath.'.translations.name.string'),
            'translations.*.name.max' => __($this->backendLangPath.'.translations.name.max'),

            'translations.*.description.required' => __($this->backendLangPath.'.translations.description.required'),
            'translations.*.description.string' => __($this->backendLangPath.'.translations.description.string'),
        ];
    }
}
