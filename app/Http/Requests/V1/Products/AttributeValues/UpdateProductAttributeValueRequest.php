<?php

namespace App\Http\Requests\V1\Products\AttributeValues;

use App\Enums\Products\Attributes\ProductAttributeType;
use App\Enums\Products\Attributes\ProductAttributeValueStatus;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

/**
 * Requête de validation pour la mise à jour d'une valeur d'attribut de produit.
 *
 * Valide les données entrantes pour la modification d'une valeur (product_attribute_values)
 * avec ses traductions associées (product_attribute_value_translations).
 * Gère l'unicité de sort_order en excluant la valeur en cours de modification.
 */
class UpdateProductAttributeValueRequest extends FormRequest
{
    /**
     * Chemin de base pour les traductions des messages de validation.
     */
    protected string $backendLangPath = 'v1/backend/views/pages/products/attribute-values/messages/attribute-values-messages-validation.productAttributeValues';

    /**
     * ID résolu de la valeur à partir de l'UUID de la route.
     * Utilisé pour exclure la valeur courante des règles d'unicité.
     */
    protected ?int $productAttributeValueId = null;

    /**
     * Résolution de l'ID de la valeur avant la validation.
     * La valeur est déjà résolue par le binding implicite de la route {productAttributeValue}
     * (clé = uuid via ProductAttributeValue::getRouteKeyName()).
     */
    protected function prepareForValidation(): void
    {
        /** @var ProductAttributeValue|null $productAttributeValue */
        $productAttributeValue = $this->route('productAttributeValue');

        // Sécurité : si le paramètre n'a pas été injecté comme modèle, on renvoie une 404.
        if (! $productAttributeValue instanceof ProductAttributeValue) {
            abort(404);
        }

        $this->productAttributeValueId = $productAttributeValue->id;
    }

    public function authorize(): bool
    {
        return true; // ← géré par middleware
    }

    /**
     * Règles de validation.
     *
     * NOTE IMPORTANTE (même approche que StoreProductAttributeValueRequest) :
     * La syntaxe statique 'unique:...,lang_id,{translations.*.lang_id}' ne
     * fonctionne PAS — Laravel traite "{translations.*.lang_id}" comme une
     * chaîne littérale, pas comme une référence dynamique au champ frère.
     * On reconstruit ces règles dynamiquement en PHP, avec un vrai closure
     * qui reçoit la vraie valeur de lang_id de chaque ligne de traduction.
     */
    public function rules(): array
    {
        $productAttributeValueId = $this->productAttributeValueId;

        $rules = [
            // L'attribut parent est obligatoire et doit exister
            'attribute_id' => ['required', 'integer', 'exists:product_attributes,id'],

            // La clé machine est obligatoire (l'unicité attribute_id + value est assurée par la contrainte DB,
            // le serveur lève une QueryException traduite si la paire existe déjà sur une autre valeur)
            'value' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],

            // sort_order n'est PAS unique ici : l'ordre est géré par attribut
            'sort_order' => ['required', 'integer', 'min:0'],

            'status' => ['required', Rule::in(ProductAttributeValueStatus::values())],

            // Le tableau de traductions lui-même
            'translations' => ['required', 'array'],

            // Chaque traduction doit référencer une langue existante
            'translations.*.lang_id' => ['required', 'exists:langs,id'],
        ];

        // ────────────────────────────────────────────────────────────
        // Code couleur HEX : requis UNIQUEMENT si l'attribut parent (celui
        // soumis, potentiellement changé) est de type "color".
        // ────────────────────────────────────────────────────────────
        $attributeId = $this->input('attribute_id'); // $attributeId est l'ID de l'attribut parent soumis dans la requête (potentiellement modifié par rapport à la valeur existante).

        // $isColor permet de savoir si l'attribut parent soumis est de type "color"
        $isColor = $attributeId && ProductAttribute::query()
            ->whereKey($attributeId) // Filtre sur l'ID de l'attribut parent soumis
            ->where('type', ProductAttributeType::Color)
            ->exists();

        // Si  $isColor est faux, on neutralise toute valeur parasite pour éviter de persister un HEX fantôme.
        if (! $isColor) {
            // L'attribut n'est pas une couleur : on écrase toute valeur parasite
            // pour qu'aucun HEX fantôme ne soit persisté.
            $this->merge(['hex_color' => null]);
        }

        // Regle de validation pour le champ hex_color : requis si l'attribut parent est de type "color", sinon nullable.
        $rules['hex_color'] = $isColor
            ? ['required', 'string', 'max:9', 'hex_color']
            : ['nullable'];

        // ────────────────────────────────────────────────────────────
        // Génération dynamique des règles "unique par langue" pour
        // "label", en excluant l'enregistrement en cours de modification.
        // ────────────────────────────────────────────────────────────
        $translations = $this->input('translations', []);

        foreach ($translations as $index => $translation) {
            // lang_id RÉEL de cette ligne précise (pas un placeholder texte).
            $langId = $translation['lang_id'] ?? null;

            // Règle unique dynamique pour "label" :
            // → unique parmi les traductions ayant le MÊME lang_id que cette
            //   ligne, MAIS en excluant celles qui appartiennent déjà à la
            //   valeur en cours de modification.
            $rules["translations.{$index}.label"] = [
                'required',
                'string',
                'max:255',
                Rule::unique('product_attribute_value_translations', 'label')
                    ->where(fn ($query) => $query->where('lang_id', $langId))
                    ->ignore($productAttributeValueId, 'attribute_value_id'),
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
