<?php

namespace App\Http\Requests\V1\Products\Attributes;

use App\Enums\Products\Attributes\ProductAttributeStatus;
use App\Models\ProductAttribute;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

/**
 * Requête de validation pour la mise à jour d'un attribut de produit.
 *
 * Valide les données entrantes pour la modification d'un attribut (product_attributes)
 * avec ses traductions associées (product_attribute_translations).
 * Gère l'unicité de sort_order en excluant l'attribut en cours de modification.
 */
class UpdateProductAttributeRequest extends FormRequest
{
    /**
     * Chemin de base pour les traductions des messages de validation.
     */
    protected string $langPath = 'v1/backend/views/pages/products/attributes/messages/attributes-messages-validation.productAttributes';

    /**
     * ID résolu du attribut à partir de l'UUID de la route.
     * Utilisé pour exclure le attribut courant des règles d'unicité.
     */
    protected ?int $productAttributeId = null;

    /**
     * Résolution de l'ID du attribut avant la validation.
     * L'attribut est déjà résolu par le binding implicite de la route {productAttribute}
     * (clé = uuid via ProductAttribute::getRouteKeyName()).
     */
    protected function prepareForValidation(): void
    {
        /** @var ProductAttribute|null $productAttribute */
        $productAttribute = $this->route('productAttribute');

        // Sécurité : si le paramètre n'a pas été injecté comme modèle, on renvoie une 404.
        if (! $productAttribute instanceof ProductAttribute) {
            abort(404);
        }

        $this->productAttributeId = $productAttribute->id;
    }

    public function authorize(): bool
    {
        return true; // ← géré par middleware
    }

    /**
     * Règles de validation.
     *
     * NOTE IMPORTANTE (même approche que StoreProductAttributeRequest) :
     * La syntaxe statique 'unique:...,lang_id,{translations.*.lang_id}' ne
     * fonctionne PAS — Laravel traite "{translations.*.lang_id}" comme une
     * chaîne littérale, pas comme une référence dynamique au champ frère.
     * On reconstruit ces règles dynamiquement en PHP, avec un vrai closure
     * qui reçoit la vraie valeur de lang_id de chaque ligne de traduction.
     */
    public function rules(): array
    {
        $productAttributeId = $this->productAttributeId;

        $rules = [
            // sort_order doit être unique, sauf pour LE attribut qu'on modifie
            // lui-même (sinon impossible de sauvegarder sans changer sa valeur).
            'sort_order' => ['required', 'integer', 'min:0', 'unique:product_attributes,sort_order,'.$productAttributeId],

            'status' => ['required', Rule::in(ProductAttributeStatus::values())],

            // Le tableau de traductions lui-même
            'translations' => ['required', 'array'],

            // Chaque traduction doit référencer une langue existante
            'translations.*.lang_id' => ['required', 'exists:langs,id'],

            // La description reste une règle simple et statique (pas d'unicité à gérer)
            'translations.*.description' => ['required', 'string', 'max:1000'],
        ];

        // ────────────────────────────────────────────────────────────
        // Génération dynamique des règles "unique par langue" pour
        // "name", en excluant l'enregistrement en cours de modification.
        // ────────────────────────────────────────────────────────────
        $translations = $this->input('translations', []);

        foreach ($translations as $index => $translation) {
            // lang_id RÉEL de cette ligne précise (pas un placeholder texte).
            $langId = $translation['lang_id'] ?? null;

            // Règle unique dynamique pour "name" :
            // → unique parmi les traductions ayant le MÊME lang_id que cette
            //   ligne, MAIS en excluant celles qui appartiennent déjà au
            //   attribut en cours de modification.
            $rules["translations.{$index}.name"] = [
                'required',
                'string',
                'max:255',
                Rule::unique('product_attribute_translations', 'name')
                    ->where(fn ($query) => $query->where('lang_id', $langId))
                    ->ignore($productAttributeId, 'attribute_id')
                    // Ignore aussi les lignes soft-deleted (cohérent avec le
                    // SoftDeletes de la table) : un nom "libéré" par une
                    // suppression logique ne doit pas bloquer la sauvegarde.
                    ->whereNull('deleted_at'),
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
            'sort_order.required' => __("{$this->langPath}.sort_order.required"),
            'sort_order.integer' => __("{$this->langPath}.sort_order.integer"),
            'sort_order.min' => __("{$this->langPath}.sort_order.min"),
            'sort_order.unique' => __("{$this->langPath}.sort_order.unique"),

            'status.required' => __("{$this->langPath}.status.required"),
            'status.in' => __("{$this->langPath}.status.in"),

            'translations.required' => __("{$this->langPath}.translations.required"),
            'translations.array' => __("{$this->langPath}.translations.array"),

            'translations.*.lang_id.required' => __("{$this->langPath}.translations.lang_id.required"),
            'translations.*.lang_id.exists' => __("{$this->langPath}.translations.lang_id.exists"),

            'translations.*.name.required' => __("{$this->langPath}.translations.name.required"),
            'translations.*.name.string' => __("{$this->langPath}.translations.name.string"),
            'translations.*.name.max' => __("{$this->langPath}.translations.name.max"),
            'translations.*.name.unique' => __("{$this->langPath}.translations.name.unique"),

            'translations.*.description.required' => __("{$this->langPath}.translations.description.required"),
            'translations.*.description.string' => __("{$this->langPath}.translations.description.string"),
            'translations.*.description.max' => __("{$this->langPath}.translations.description.max"),
        ];
    }
}
