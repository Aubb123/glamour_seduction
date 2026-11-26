<?php

namespace App\Http\Requests\V1\Geo\Divisions;

use App\Enums\Geos\Divisions\GeoDivisionStatus;
use App\Models\GeoDivision;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

use function __;

/**
 * Requête de validation pour la mise à jour d'une division géographique.
 *
 * Valide les données entrantes pour la modification d'une division (geo_divisions)
 * avec ses traductions associées (geo_division_translations).
 * Le pays (country_id) est immuable ; le niveau (level) est recalculé par le service.
 */
class UpdateGeoDivisionRequest extends FormRequest
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/geos/divisions/messages/divisions-messages-validation';

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
            // ⚠️ `country_id` est IMMUABLE : absent des règles (jamais mis à jour).

            // Type de division — obligatoire, doit exister et être actif
            'division_type_id' => ['required', 'integer', 'exists:geo_division_types,id'],

            // Division parente — optionnelle (racine si absente)
            'parent_id' => ['nullable', 'integer', 'exists:geo_divisions,id'],

            // Statut
            'status' => ['required', Rule::in(GeoDivisionStatus::values())],

            // Notes internes — optionnelles
            'notes' => ['nullable', 'string', 'max:2000'],

            // Traductions
            'translations' => ['required', 'array', 'min:1'],

            'translations.*.lang_id' => ['required', 'integer', 'exists:langs,id'],
            'translations.*.name' => ['required', 'string', 'max:150'],
            'translations.*.description' => ['required', 'string'],
        ];
    }

    /**
     * Vérifications métier transverses (doublons de langue, parent même pays / self / cycle).
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {

            /** @var GeoDivision|null $division */
            $division = $this->route('geoDivision');
            $divisionId = $division?->id;

            $translations = (array) $this->input('translations', []);

            // 1. Doublons de lang_id dans le payload
            $langIds = array_filter(array_map(fn ($t) => $t['lang_id'] ?? null, $translations));

            if (count($langIds) !== count(array_unique($langIds))) {
                $validator->errors()->add('translations', __("{$this->backendLangPath}.translations.duplicate_lang"));
            }

            // 2. Vérifications liées au parent
            $parentId = $this->input('parent_id');

            if (! $division) {
                return;
            }

            if ($parentId) {

                // Pas de self-parent
                if ((int) $parentId === (int) $divisionId) {
                    $validator->errors()->add('parent_id', __("{$this->backendLangPath}.parent_id.self"));

                    return;
                }

                $parent = GeoDivision::find($parentId);

                if (! $parent) {
                    return;
                }

                // Même pays (le pays de la division est immuable)
                if ((int) $parent->country_id !== (int) $division->country_id) {
                    $validator->errors()->add('parent_id', __("{$this->backendLangPath}.parent_id.other_country"));

                    return;
                }

                // Pas de cycle : le parent ne doit pas être un descendant de la division courante
                $ancestorId = $parent->parent_id;
                $guard = 0;

                while ($ancestorId && $guard < 1000) {

                    if ((int) $ancestorId === (int) $divisionId) {
                        $validator->errors()->add('parent_id', __("{$this->backendLangPath}.parent_id.cycle"));

                        return;
                    }

                    $ancestor = GeoDivision::find($ancestorId);
                    $ancestorId = $ancestor?->parent_id;
                    $guard++;
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
            'division_type_id.required' => __($this->backendLangPath.'.division_type_id.required'),
            'division_type_id.integer' => __($this->backendLangPath.'.division_type_id.integer'),
            'division_type_id.exists' => __($this->backendLangPath.'.division_type_id.exists'),

            'parent_id.integer' => __($this->backendLangPath.'.parent_id.integer'),
            'parent_id.exists' => __($this->backendLangPath.'.parent_id.exists'),

            'status.required' => __($this->backendLangPath.'.status.required'),
            'status.in' => __($this->backendLangPath.'.status.in'),

            'notes.string' => __($this->backendLangPath.'.notes.string'),
            'notes.max' => __($this->backendLangPath.'.notes.max'),

            'translations.required' => __($this->backendLangPath.'.translations.required'),
            'translations.array' => __($this->backendLangPath.'.translations.array'),
            'translations.min' => __($this->backendLangPath.'.translations.min'),

            'translations.*.lang_id.required' => __($this->backendLangPath.'.translations.lang_id.required'),
            'translations.*.lang_id.integer' => __($this->backendLangPath.'.translations.lang_id.integer'),
            'translations.*.lang_id.exists' => __($this->backendLangPath.'.translations.lang_id.exists'),

            'translations.*.name.required' => __($this->backendLangPath.'.translations.name.required'),
            'translations.*.name.string' => __($this->backendLangPath.'.translations.name.string'),
            'translations.*.name.max' => __($this->backendLangPath.'.translations.name.max'),

            'translations.*.description.string' => __($this->backendLangPath.'.translations.description.string'),
        ];
    }
}
