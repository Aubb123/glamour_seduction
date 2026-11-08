<?php

namespace App\Http\Requests\V1\Products\AttributeValues;

use App\Enums\Products\Attributes\ProductAttributeType;
use App\Enums\Products\Attributes\ProductAttributeValueStatus;
use App\Models\ProductAttribute;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

/**
 * Requête de validation pour la création d'une valeur d'attribut de produit.
 *
 * Valide les données entrantes pour la création d'une valeur (product_attribute_values)
 * avec ses traductions associées (product_attribute_value_translations).
 */
class StoreProductAttributeValueRequest extends FormRequest
{
    /**
     * Chemin de base pour les traductions des messages de validation.
     */
    protected string $backendLangPath = 'v1/backend/views/pages/products/attribute-values/messages/attribute-values-messages-validation.productAttributeValues';

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
        // Code couleur HEX : requis UNIQUEMENT si l'attribut parent est
        // de type "color". Sinon le champ est neutralisé (anti-rejeu).
        // ────────────────────────────────────────────────────────────
        $attributeId = $this->input('attribute_id');

        $isColor = $attributeId && ProductAttribute::query()
            ->whereKey($attributeId)
            ->where('type', ProductAttributeType::Color)
            ->exists();

        if (! $isColor) {
            // L'attribut n'est pas une couleur : on écrase toute valeur parasite
            // pour qu'aucun HEX fantôme ne soit persisté.
            $this->merge(['hex_color' => null]);
        }

        $rules['hex_color'] = $isColor
            ? ['required', 'string', 'max:9', 'hex_color']
            : ['nullable'];

        // ────────────────────────────────────────────────────────────
        // Génération dynamique des règles "unique par langue",
        // basées sur les données réellement envoyées dans la requête
        // (accessible via $this->input()).
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
                    ->where(fn ($query) => $query->where('lang_id', $langId)),
            ];

        }

        return $rules;
    }

    /**
     * Messages de validation personnalisés.
     */
    public function messages(): array
    {
        return [
            'attribute_id.required' => __("{$this->backendLangPath}.attribute_id.required"),
            'attribute_id.integer' => __("{$this->backendLangPath}.attribute_id.integer"),
            'attribute_id.exists' => __("{$this->backendLangPath}.attribute_id.exists"),

            'value.required' => __("{$this->backendLangPath}.value.required"),
            'value.string' => __("{$this->backendLangPath}.value.string"),
            'value.max' => __("{$this->backendLangPath}.value.max"),
            'value.regex' => __("{$this->backendLangPath}.value.regex"),

            'sort_order.required' => __("{$this->backendLangPath}.sort_order.required"),
            'sort_order.integer' => __("{$this->backendLangPath}.sort_order.integer"),
            'sort_order.min' => __("{$this->backendLangPath}.sort_order.min"),

            'status.required' => __("{$this->backendLangPath}.status.required"),
            'status.in' => __("{$this->backendLangPath}.status.in"),

            'hex_color.required' => __("{$this->backendLangPath}.hex_color.required"),
            'hex_color.string' => __("{$this->backendLangPath}.hex_color.string"),
            'hex_color.max' => __("{$this->backendLangPath}.hex_color.max"),
            'hex_color.hex_color' => __("{$this->backendLangPath}.hex_color.hex_color"),

            'translations.required' => __("{$this->backendLangPath}.translations.required"),
            'translations.array' => __("{$this->backendLangPath}.translations.array"),

            'translations.*.lang_id.required' => __("{$this->backendLangPath}.translations.lang_id.required"),
            'translations.*.lang_id.exists' => __("{$this->backendLangPath}.translations.lang_id.exists"),

            'translations.*.label.required' => __("{$this->backendLangPath}.translations.label.required"),
            'translations.*.label.string' => __("{$this->backendLangPath}.translations.label.string"),
            'translations.*.label.max' => __("{$this->backendLangPath}.translations.label.max"),
            'translations.*.label.unique' => __("{$this->backendLangPath}.translations.label.unique"),

        ];
    }
}
