<?php

namespace App\Http\Requests\V1\Geo\DivisionTypes;

use App\Enums\Geos\DivisionTypes\GeoDivisionTypeStatus;
use App\Models\GeoDivisionTypeTranslation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

use function __;

class StoreGeoDivisionTypeRequest extends FormRequest
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
            // Clé technique du type — minuscules, chiffres et underscores (ex : department, commune)
            'code' => ['required', 'string', 'max:50', 'regex:/^[a-z][a-z0-9_]*$/', 'unique:geo_division_types,code'],

            // Niveau par défaut suggéré (1 = plus haut niveau du pays)
            'level' => ['required', 'integer', 'min:1'],

            // Ordre d'affichage dans le référentiel — entier positif ou nul (pas d'unicité)
            'sort_order' => ['required', 'integer', 'min:0'],

            // Statut — source unique de vérité : l'enum GeoDivisionTypeStatus
            'status' => ['required', Rule::in(GeoDivisionTypeStatus::values())],

            // Traductions : au moins une est obligatoire
            'translations' => ['required', 'array', 'min:1'],

            // Langue de la traduction — obligatoire, doit exister dans la table langs
            'translations.*.lang_id' => ['required', 'integer', 'exists:langs,id'],

            // Nom du type dans la langue concernée — obligatoire
            'translations.*.name' => ['required', 'string', 'max:100'],

            // Description du type dans la langue concernée — obligatoire
            'translations.*.description' => ['required', 'string'],
        ];
    }

    /**
     * Vérifications métier transverses (doublons de langue, unicité du nom par langue).
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {

            $translations = (array) $this->input('translations', []);

            // 1. Doublons de lang_id dans le payload
            $langIds = array_filter(array_map(fn ($t) => $t['lang_id'] ?? null, $translations));

            if (count($langIds) !== count(array_unique($langIds))) {
                $validator->errors()->add('translations', __("{$this->backendLangPath}.translations.duplicate_lang"));
            }

            // 2. Unicité du nom par langue (corbeille incluse) — création : aucune entité à exclure
            foreach ($translations as $index => $translation) {
                $langId = $translation['lang_id'] ?? null;
                $name = trim((string) ($translation['name'] ?? ''));

                if ($langId === null || $name === '') {
                    continue;
                }

                $exists = GeoDivisionTypeTranslation::withTrashed()
                    ->where('lang_id', $langId)
                    ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
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
            'code.required' => __($this->backendLangPath.'.code.required'),
            'code.string' => __($this->backendLangPath.'.code.string'),
            'code.max' => __($this->backendLangPath.'.code.max'),
            'code.regex' => __($this->backendLangPath.'.code.regex'),
            'code.unique' => __($this->backendLangPath.'.code.unique'),

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
