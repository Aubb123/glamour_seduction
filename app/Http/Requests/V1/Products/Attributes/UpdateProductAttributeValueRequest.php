<?php

namespace App\Http\Requests\V1\Products\Attributes;

use App\Enums\Products\Attributes\ProductAttributeValueStatus;
use App\Models\ProductAttributeValue;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

class UpdateProductAttributeValueRequest extends FormRequest
{
    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/products/attribute-values/messages/attribute-values-messages-validation.productAttributeValues';

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
        // Génération dynamique des règles "unique par langue" pour
        // "label" et "slug", en excluant la valeur en cours de modification.
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
                    ->ignore($productAttributeValueId, 'attribute_value_id')
                    // Ignore aussi les lignes soft-deleted (cohérent avec le
                    // SoftDeletes de la table) : un nom "libéré" par une
                    // suppression logique ne doit pas bloquer la sauvegarde.
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
                        ->ignore($productAttributeValueId, 'attribute_value_id')
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
