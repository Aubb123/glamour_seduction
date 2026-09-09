<?php

namespace App\Http\Requests\V1\Currencies;
 
use App\Enums\Currencies\CurrencyStatus;
use App\Models\Currency;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

class UpdateCurrencyRequest extends FormRequest
{
    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/currencies/messages/currencies-messages-validation';

    /**
     * ID résolu de la devise à partir de l'UUID de la route.
     * Utilisé pour exclure la devise courante des règles d'unicité.
     */
    protected ?int $currencyId = null;

    /**
     * Résolution de l'ID de la devise avant la validation.
     * La devise est déjà résolue par le binding implicite de la route {currency}
     * (clé = uuid via Currency::getRouteKeyName()).
     */
    protected function prepareForValidation(): void
    {
        /** @var Currency|null $currency */
        $currency = $this->route('currency');

        // Sécurité : si le paramètre n'a pas été injecté comme modèle, on renvoie une 404.
        if (! $currency instanceof Currency) {
            abort(404);
        }

        $this->currencyId = $currency->id;
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
            // Code ISO 4217 alpha-3 (ex: EUR) — obligatoire, 3 majuscules, unique
            'code' => ['required', 'string', 'size:3', 'regex:/^[A-Z]{3}$/', Rule::unique('currencies', 'code')->ignore($this->currencyId)],

            // Symbole de la devise (ex: €, $, ₫) — obligatoire (peut être multi-octets)
            'symbol' => ['required', 'string', 'max:10'],

            // Nombre de décimales (0 pour JPY, 2 pour EUR, 3 pour KWD) — obligatoire
            'decimal_places' => ['required', 'integer', 'between:0,4'],

            // Code ISO 4217 numérique (ex: 978) — optionnel, 3 chiffres, unique
            'numeric_code' => ['nullable', 'string', 'size:3', 'regex:/^[0-9]{3}$/', Rule::unique('currencies', 'numeric_code')->ignore($this->currencyId)],

            // Ordre d'affichage — obligatoire, entier positif ou nul, unique
            'sort_order' => ['required', 'integer', 'min:0', Rule::unique('currencies', 'sort_order')->ignore($this->currencyId)],

            // Devise par défaut de la plateforme — optionnelle (case à cocher)
            'is_default' => ['nullable', 'boolean'],

            // Statut de la devise — obligatoire, valeur de l'enum CurrencyStatus
            'status' => ['required', Rule::in(CurrencyStatus::values())],

            // Traductions : au moins une traduction est obligatoire
            'translations' => ['required', 'array', 'min:1'],

            // Identifiant de la langue — obligatoire, doit exister dans la table langs
            'translations.*.lang_id' => ['required', 'integer', 'exists:langs,id'],

            // Nom de la devise dans la langue concernée — obligatoire
            'translations.*.name' => ['required', 'string', 'max:100'],

            // Slug (régénéré automatiquement par HasSlug si absent)
            'translations.*.slug' => ['nullable', 'string', 'max:255'],

            // Description de la devise dans la langue concernée — optionnelle
            'translations.*.description' => ['nullable', 'string'],
        ];
    }

    /**
     * Get the custom validation messages.
     */
    public function messages(): array 
    {
        return [
            // Code
            'code.required' => __("{$this->langPath}.code.required"),
            'code.string' => __("{$this->langPath}.code.string"),
            'code.size' => __("{$this->langPath}.code.size"),
            'code.regex' => __("{$this->langPath}.code.regex"),
            'code.unique' => __("{$this->langPath}.code.unique"),

            // Symbole
            'symbol.required' => __("{$this->langPath}.symbol.required"),
            'symbol.string' => __("{$this->langPath}.symbol.string"),
            'symbol.max' => __("{$this->langPath}.symbol.max"),

            // Décimales
            'decimal_places.required' => __("{$this->langPath}.decimal_places.required"),
            'decimal_places.integer' => __("{$this->langPath}.decimal_places.integer"),
            'decimal_places.between' => __("{$this->langPath}.decimal_places.between"),

            // Code numérique
            'numeric_code.string' => __("{$this->langPath}.numeric_code.string"),
            'numeric_code.size' => __("{$this->langPath}.numeric_code.size"),
            'numeric_code.regex' => __("{$this->langPath}.numeric_code.regex"),
            'numeric_code.unique' => __("{$this->langPath}.numeric_code.unique"),

            // Ordre d'affichage
            'sort_order.required' => __("{$this->langPath}.sort_order.required"),
            'sort_order.integer' => __("{$this->langPath}.sort_order.integer"),
            'sort_order.min' => __("{$this->langPath}.sort_order.min"),
            'sort_order.unique' => __("{$this->langPath}.sort_order.unique"),

            // Devise par défaut
            'is_default.boolean' => __("{$this->langPath}.is_default.boolean"),

            // Statut
            'status.required' => __("{$this->langPath}.status.required"), 
            'status.in' => __("{$this->langPath}.status.in"),

            // Traductions (tableau)
            'translations.required' => __("{$this->langPath}.translations.required"),
            'translations.array' => __("{$this->langPath}.translations.array"),
            'translations.min' => __("{$this->langPath}.translations.min"),

            // Langue de la traduction
            'translations.*.lang_id.required' => __("{$this->langPath}.translations.lang_id.required"),
            'translations.*.lang_id.integer' => __("{$this->langPath}.translations.lang_id.integer"),
            'translations.*.lang_id.exists' => __("{$this->langPath}.translations.lang_id.exists"),

            // Nom de la traduction
            'translations.*.name.required' => __("{$this->langPath}.translations.name.required"),
            'translations.*.name.string' => __("{$this->langPath}.translations.name.string"),
            'translations.*.name.max' => __("{$this->langPath}.translations.name.max"),

            // Slug de la traduction
            'translations.*.slug.string' => __("{$this->langPath}.translations.slug.string"),
            'translations.*.slug.max' => __("{$this->langPath}.translations.slug.max"),

            // Description de la traduction
            'translations.*.description.string' => __("{$this->langPath}.translations.description.string"),
        ];
    }
}
