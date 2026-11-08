<?php

namespace App\Http\Requests\V1\Geo\Countries;

use App\Enums\Countries\CountryType;
use App\Enums\Geos\Countries\GeoCountryStatus;
use App\Models\GeoCountry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

class UpdateGeoCountryRequest extends FormRequest
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/geos/countries/messages/countries-messages-validation';

    /** 
     * ID résolu du pays à partir de l'UUID de la route.
     * Utilisé pour exclure le pays courant des règles d'unicité.
     */
    protected ?int $countryId = null;

    /**
     * Résolution de l'ID du pays avant la validation.
     * Le pays est déjà résolu par le binding implicite de la route {geoCountry}
     * (clé = uuid via GeoCountry::getRouteKeyName()). On en extrait uniquement l'ID
     * pour l'exclure des contraintes d'unicité.
     */
    protected function prepareForValidation(): void
    {
        /** @var GeoCountry|null $geoCountry */
        $geoCountry = $this->route('geoCountry');

        // Sécurité : si le paramètre n'a pas été injecté comme modèle, on renvoie une 404.
        if (! $geoCountry instanceof GeoCountry) {
            abort(404);
        }

        $this->countryId = $geoCountry->id;
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return false;
        return true; // ← géré par middleware
    }

    public function rules(): array
    {
        return [
            // Continent auquel appartient le pays (obligatoire, doit exister dans la table geo_continents)
            'continent_id' => ['required', 'integer', 'exists:geo_continents,id'],

            // Type d'entité géographique — source unique de vérité : l'enum CountryType
            'type' => ['required', 'string', Rule::in(CountryType::values())],

            // Pays souverain de rattachement :
            // - obligatoire dès que le type n'est pas "country"
            // - interdit lorsque le type est "country" (un pays souverain n'a pas de pays de rattachement)
            // - interdit pour le pays courant lui-même (pas d'auto-rattachement)
            // - doit exister, être actif (non supprimé) et être un vrai pays souverain (type = country)
            'sovereign_id' => ['bail', 'nullable', 'required_unless:type,'.CountryType::Country->value, 'prohibited_if:type,'.CountryType::Country->value, 'integer', Rule::exists('geo_countries', 'id')->where(fn ($query) => $query->whereNull('deleted_at')->where('type', CountryType::Country->value)), Rule::notIn([$this->countryId])],

            // Code ISO 3166-1 alpha-3 (ex: FRA) — optionnel (subdivisions GB-ENG...), 3 majuscules, unique sauf pour le pays courant
            'code' => ['nullable', 'string', 'size:3', 'regex:/^[A-Z]{3}$/', 'unique:geo_countries,code,'.$this->countryId],

            // Code ISO 3166-1 alpha-2 (ex: FR) ou code étendu de subdivision (ex: GB-ENG) — obligatoire, unique sauf pour le pays courant.
            // max:6 : le format le plus long accepté par le regex est "XX-XXX" (ex: GB-ENG).
            'code_2' => ['required', 'string', 'max:6', 'regex:/^[A-Z]{2}(-[A-Z]{3})?$/', 'unique:geo_countries,code_2,'.$this->countryId],

            // Indicatif téléphonique (ex: +229) — optionnel (certains territoires n'en ont pas),
            // format +XXX ou +X-XXXX pour les indicatifs "composés" (ex: +1-441, +49-30), max 10 (taille de la colonne DB)
            'phone_code' => ['nullable', 'string', 'max:10', 'regex:/^\+[0-9]{1,4}(-[0-9]{1,4})?$/'],

            // Chemin de l'image/drapeau — optionnel, fichier image valide
            'file_path' => ['nullable', 'image', 'mimes:png,jpg,webp', 'max:2048'],

            // Ordre d'affichage dans les listes — optionnel, entier positif ou nul
            'sort_order' => ['required', 'integer', 'min:0', 'unique:geo_countries,sort_order,'.$this->countryId],

            // Devise principale du pays (normalisée via la table currencies) — optionnelle
            'currency_id' => ['nullable', 'integer', 'exists:currencies,id'],

            // Statut du pays — optionnel, doit être une valeur de l'enum GeoCountryStatus
            'status' => ['required', Rule::in(GeoCountryStatus::values())],

            // Traductions : au moins une traduction est obligatoire
            'translations' => ['required', 'array', 'min:1'],

            // Identifiant de la langue — obligatoire, doit exister dans la table langs
            'translations.*.lang_id' => ['required', 'integer', 'exists:langs,id'],

            // Nom du pays dans la langue concernée — obligatoire
            'translations.*.name' => ['required', 'string', 'max:255'],

            // Description du pays dans la langue concernée — optionnelle
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
                Rule::unique('geo_country_translations', 'name')
                    ->where(fn ($query) => $query->where('lang_id', $langId))
                    ->ignore($this->countryId, 'country_id'),
            ];
        }

        return $rules;
    }

    /**
     * Get the custom validation messages.
     */
    public function messages(): array
    {
        return [
            // Continent
            'continent_id.required' => __($this->backendLangPath.'.continent_id.required'),
            'continent_id.integer' => __($this->backendLangPath.'.continent_id.integer'),
            'continent_id.exists' => __($this->backendLangPath.'.continent_id.exists'),

            // Type
            'type.required' => __($this->backendLangPath.'.type.required'),
            'type.in' => __($this->backendLangPath.'.type.in'),
            // Statut
            'status.required' => __($this->backendLangPath.'.status.required'),
            'status.in' => __($this->backendLangPath.'.status.in'),

            // Pays souverain
            'sovereign_id.exists' => __($this->backendLangPath.'.sovereign_id.exists'),
            'sovereign_id.required_unless' => __($this->backendLangPath.'.sovereign_id.required_unless'),
            'sovereign_id.prohibited_if' => __($this->backendLangPath.'.sovereign_id.prohibited_if'),
            'sovereign_id.not_in' => __($this->backendLangPath.'.sovereign_id.not_in'),

            // Code alpha-3
            'code.string' => __($this->backendLangPath.'.code.string'),
            'code.size' => __($this->backendLangPath.'.code.size'),
            'code.regex' => __($this->backendLangPath.'.code.regex'),
            'code.unique' => __($this->backendLangPath.'.code.unique'),

            // Code alpha-2
            'code_2.required' => __($this->backendLangPath.'.code_2.required'),
            'code_2.string' => __($this->backendLangPath.'.code_2.string'),
            'code_2.max' => __($this->backendLangPath.'.code_2.max'),
            'code_2.regex' => __($this->backendLangPath.'.code_2.regex'),
            'code_2.unique' => __($this->backendLangPath.'.code_2.unique'),

            // Indicatif téléphonique
            'phone_code.string' => __($this->backendLangPath.'.phone_code.string'),
            'phone_code.max' => __($this->backendLangPath.'.phone_code.max'),
            'phone_code.regex' => __($this->backendLangPath.'.phone_code.regex'),

            // Image
            'file_path.image' => __($this->backendLangPath.'.file_path.image'),
            'file_path.mimes' => __($this->backendLangPath.'.file_path.mimes'),
            'file_path.max' => __($this->backendLangPath.'.file_path.max'),

            // Ordre d'affichage
            'sort_order.integer' => __($this->backendLangPath.'.sort_order.integer'),
            'sort_order.min' => __($this->backendLangPath.'.sort_order.min'),
            'sort_order.unique' => __($this->backendLangPath.'.sort_order.unique'),

            // Devise
            'currency_id.integer' => __($this->backendLangPath.'.currency_id.integer'),
            'currency_id.exists' => __($this->backendLangPath.'.currency_id.exists'),

            // Traductions (tableau)
            'translations.required' => __($this->backendLangPath.'.translations.required'),
            'translations.array' => __($this->backendLangPath.'.translations.array'),
            'translations.min' => __($this->backendLangPath.'.translations.min'),

            // Langue de la traduction
            'translations.*.lang_id.required' => __($this->backendLangPath.'.translations.lang_id.required'),
            'translations.*.lang_id.integer' => __($this->backendLangPath.'.translations.lang_id.integer'),
            'translations.*.lang_id.exists' => __($this->backendLangPath.'.translations.lang_id.exists'),

            // Nom de la traduction
            'translations.*.name.required' => __($this->backendLangPath.'.translations.name.required'),
            'translations.*.name.string' => __($this->backendLangPath.'.translations.name.string'),
            'translations.*.name.max' => __($this->backendLangPath.'.translations.name.max'),

            // Description de la traduction
            'translations.*.description.required' => __($this->backendLangPath.'.translations.description.required'),
            'translations.*.description.string' => __($this->backendLangPath.'.translations.description.string'),
        ];
    }
}
