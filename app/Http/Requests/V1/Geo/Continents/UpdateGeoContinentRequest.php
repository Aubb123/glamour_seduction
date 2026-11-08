<?php

namespace App\Http\Requests\V1\Geo\Continents;

use App\Enums\Geos\Continents\GeoContinentStatus;
use App\Models\GeoContinent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

class UpdateGeoContinentRequest extends FormRequest
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/geos/continents/messages/continent-messages-validation';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // ← géré par middleware + Policy
    }

    public function rules(): array
    {
        // Le continent est résolu automatiquement par le binding implicite sur la route
        // {geoContinent} (clé de route = uuid via GeoContinent::getRouteKeyName()).
        $geoContinent = $this->route('geoContinent');

        // Sécurité : si le paramètre n'a pas été injecté comme modèle, on renvoie une 404.
        if (! $geoContinent instanceof GeoContinent) {
            abort(404);
        }

        return [
            // Le code est UNIQUE sur toutes les lignes (y compris les continents soft-deleted) :
            // un code de continent est définitif et ne peut pas être réutilisé.
            'code' => ['required', 'string', 'max:2', 'regex:/^[A-Z]+$/', Rule::unique('geo_continents', 'code')->ignore($geoContinent->id)],
            'population' => ['required', 'integer', 'min:0'],
            'area_km2' => ['required', 'numeric', 'min:0'],

            // L'ordre d'affichage n'est pas unique : des valeurs identiques sont tolérées,
            // et la valeur d'un continent supprimé reste réutilisable.
            'sort_order' => ['required', 'integer', 'min:0', 'unique:geo_continents,sort_order,'.$geoContinent->id],

            'status' => ['required', Rule::in(GeoContinentStatus::values())],

            // Translations
            'translations' => ['required', 'array', 'min:1'],
            'translations.*.lang_id' => ['required', 'integer', 'exists:langs,id'],
            'translations.*.name' => ['required', 'string', 'max:255'],
            'translations.*.description' => ['required', 'string'],
        ];

        // Règle d'unicité dynamique par langue sur le nom
        $translations = $this->input('translations', []);

        foreach ($translations as $index => $translation) {
            $langId = $translation['lang_id'] ?? null;

            $rules["translations.{$index}.name"] = [
                'required',
                'string',
                'max:255',
                Rule::unique('geo_continent_translations', 'name')
                    ->where(fn ($query) => $query->where('lang_id', $langId))
                    ->ignore($geoContinent->id, 'continent_id'),
            ];
        }

        return $rules;
    }

    /**
     * Validate qu'il n'y a pas de doublon de langue dans les traductions.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $langIds = collect($this->input('translations', []))->pluck('lang_id');

            if ($langIds->count() !== $langIds->unique()->count()) {
                $validator->errors()->add('translations', __("{$this->backendLangPath}.translations.lang_id.duplicate"));
            }
        });
    }

    public function messages(): array
    {
        return [
            // Code
            'code.required' => __("{$this->backendLangPath}.code.required"),
            'code.string' => __("{$this->backendLangPath}.code.string"),
            'code.max' => __("{$this->backendLangPath}.code.max"),
            'code.regex' => __("{$this->backendLangPath}.code.regex"),
            'code.unique' => __("{$this->backendLangPath}.code.unique"),

            // Population
            'population.required' => __("{$this->backendLangPath}.population.required"),
            'population.integer' => __("{$this->backendLangPath}.population.integer"),
            'population.min' => __("{$this->backendLangPath}.population.min"),

            // area_km2
            'area_km2.required' => __("{$this->backendLangPath}.area_km2.required"),
            'area_km2.numeric' => __("{$this->backendLangPath}.area_km2.numeric"),
            'area_km2.min' => __("{$this->backendLangPath}.area_km2.min"),

            // Sort order
            'sort_order.required' => __("{$this->backendLangPath}.sort_order.required"),
            'sort_order.integer' => __("{$this->backendLangPath}.sort_order.integer"),
            'sort_order.min' => __("{$this->backendLangPath}.sort_order.min"),
            'sort_order.unique' => __("{$this->backendLangPath}.sort_order.unique"),

            'status.required' => __("{$this->backendLangPath}.status.required"),
            'status.in' => __("{$this->backendLangPath}.status.in"),

            // Translations
            'translations.required' => __("{$this->backendLangPath}.translations.required"),
            'translations.array' => __("{$this->backendLangPath}.translations.array"),
            'translations.min' => __("{$this->backendLangPath}.translations.min"),

            'translations.*.lang_id.required' => __("{$this->backendLangPath}.translations.lang_id.required"),
            'translations.*.lang_id.integer' => __("{$this->backendLangPath}.translations.lang_id.integer"),
            'translations.*.lang_id.exists' => __("{$this->backendLangPath}.translations.lang_id.exists"),

            'translations.*.name.required' => __("{$this->backendLangPath}.translations.name.required"),
            'translations.*.name.string' => __("{$this->backendLangPath}.translations.name.string"),
            'translations.*.name.max' => __("{$this->backendLangPath}.translations.name.max"),

            'translations.*.description.required' => __("{$this->backendLangPath}.translations.description.required"),
            'translations.*.description.string' => __("{$this->backendLangPath}.translations.description.string"),
        ];
    }
}
