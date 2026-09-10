<?php

namespace App\Http\Requests\V1\Products\Attributes;

use App\Enums\Products\Attributes\ProductAttributeValueStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

class StoreProductAttributeValueRequest extends FormRequest
{
    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/products/attribute-values/messages/attribute-values-messages-validation.productAttributeValues';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // ← géré par middleware
    }

    /**
     * Règles de validation.
     *
     * NOTE IMPORTANTE :
     * Les règles "unique" sur translations.*.name et translations.*.slug
     * ne peuvent PAS être écrites en syntaxe statique de type :
     *   'unique:product_attribute_value_translations,name,NULL,id,lang_id,{translations.*.lang_id}'
     * car Laravel n'interprète PAS "{translations.*.lang_id}" comme une référence
     * dynamique — il le traite comme une chaîne littérale. Résultat : le filtre
     * where("lang_id", "{translations.*.lang_id}") ne correspond à aucune ligne réelle,
     * et la contrainte d'unicité "par langue" ne fonctionne pas du tout.
     *
     * La solution est de construire ces règles dynamiquement en PHP, en itérant
     * sur les traductions réellement envoyées dans la requête, pour injecter
     * la vraie valeur de lang_id de chaque ligne dans un vrai closure.
     */
    public function rules(): array
    {
        $rules = [
            // L'attribut parent est obligatoire et doit exister
            'attribute_id' => ['required', 'integer', 'exists:product_attributes,id'],

            // La clé machine est obligatoire et unique pour l'attribut concerné (contrainte DB attribute_id + value)
            'value' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],

            // sort_order n'est PAS unique ici (contrairement aux attributs) : l'ordre est géré par attribut
            'sort_order' => ['required', 'integer', 'min:0'],

            'status' => ['required', Rule::in(ProductAttributeValueStatus::values())],

            // Le tableau de traductions lui-même
            'translations' => ['required', 'array'],

            // Chaque traduction doit référencer une langue existante
            'translations.*.lang_id' => ['required', 'exists:langs,id'],
        ];

        // ────────────────────────────────────────────────────────────
        // Génération dynamique des règles "unique par langue"
        // pour "label" et "slug", basées sur les données réellement
        // envoyées dans la requête (accessible via $this->input()).
        // ────────────────────────────────────────────────────────────
        $translations = $this->input('translations', []);

        foreach ($translations as $index => $translation) {
            // Récupère le lang_id RÉEL de cette ligne précise de traduction.
            // Si lang_id est absent/invalide, la règle 'exists' définie plus haut
            // se chargera de le signaler séparément — ici on se contente de
            // sécuriser l'accès pour éviter une erreur PHP.
            $langId = $translation['lang_id'] ?? null;

            // Règle unique dynamique pour le "label" :
            // → le libellé doit être unique, mais seulement PARMI les traductions
            //   de la MÊME langue (lang_id identique), et uniquement parmi les
            //   lignes actives (non soft-deleted).
            $rules["translations.{$index}.label"] = [
                'required',
                'string',
                'max:255',
                Rule::unique('product_attribute_value_translations', 'label')
                    ->where(fn ($query) => $query->where('lang_id', $langId))
                    ->whereNull('deleted_at'),
            ];

            // Même logique pour "slug", si ce champ est envoyé.
            if (array_key_exists('slug', $translation)) {
                $rules["translations.{$index}.slug"] = [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('product_attribute_value_translations', 'slug')
                        ->where(fn ($query) => $query->where('lang_id', $langId))
                        ->whereNull('deleted_at'),
                ];
            }
        }

        return $rules;
    }

    /**
     * Messages de validation personnalisés.
     */
    public function messages(): array
    {
        return [
            'attribute_id.required' => __("{$this->langPath}.attribute_id.required"),
            'attribute_id.integer' => __("{$this->langPath}.attribute_id.integer"),
            'attribute_id.exists' => __("{$this->langPath}.attribute_id.exists"),

            'value.required' => __("{$this->langPath}.value.required"),
            'value.string' => __("{$this->langPath}.value.string"),
            'value.max' => __("{$this->langPath}.value.max"),
            'value.regex' => __("{$this->langPath}.value.regex"),

            'sort_order.required' => __("{$this->langPath}.sort_order.required"),
            'sort_order.integer' => __("{$this->langPath}.sort_order.integer"),
            'sort_order.min' => __("{$this->langPath}.sort_order.min"),

            'status.required' => __("{$this->langPath}.status.required"),
            'status.in' => __("{$this->langPath}.status.in"),

            'translations.required' => __("{$this->langPath}.translations.required"),
            'translations.array' => __("{$this->langPath}.translations.array"),

            'translations.*.lang_id.required' => __("{$this->langPath}.translations.lang_id.required"),
            'translations.*.lang_id.exists' => __("{$this->langPath}.translations.lang_id.exists"),

            'translations.*.label.required' => __("{$this->langPath}.translations.label.required"),
            'translations.*.label.string' => __("{$this->langPath}.translations.label.string"),
            'translations.*.label.max' => __("{$this->langPath}.translations.label.max"),
            'translations.*.label.unique' => __("{$this->langPath}.translations.label.unique"),

            'translations.*.slug.required' => __("{$this->langPath}.translations.slug.required"),
            'translations.*.slug.string' => __("{$this->langPath}.translations.slug.string"),
            'translations.*.slug.max' => __("{$this->langPath}.translations.slug.max"),
            'translations.*.slug.unique' => __("{$this->langPath}.translations.slug.unique"),
        ];
    }
}
