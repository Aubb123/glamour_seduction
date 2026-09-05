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
    protected string $langPath = 'v1/backend/views/pages/geos/continents/messages/continent-messages-validation';

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
            'code.regex' => __("{$this->langPath}.code.regex"),
            'code.unique' => __("{$this->langPath}.code.unique"),

            // Population
            'population.required' => __("{$this->langPath}.population.required"),
            'population.integer' => __("{$this->langPath}.population.integer"),
            'population.min' => __("{$this->langPath}.population.min"),

            // area_km2
            'area_km2.required' => __("{$this->langPath}.area_km2.required"),
            'area_km2.numeric' => __("{$this->langPath}.area_km2.numeric"),
            'area_km2.min' => __("{$this->langPath}.area_km2.min"),

            // Sort order
            'sort_order.required' => __("{$this->langPath}.sort_order.required"),
            'sort_order.integer' => __("{$this->langPath}.sort_order.integer"),
            'sort_order.min' => __("{$this->langPath}.sort_order.min"),
            'sort_order.unique' => __("{$this->langPath}.sort_order.unique"),

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
