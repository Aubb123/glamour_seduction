<?php

namespace App\Http\Requests\V1\Products\Tags;

use App\Enums\Products\Tags\ProductTagStatus;
use App\Models\ProductTag;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

/**
 * Requête de validation pour la mise à jour d'un tag de produit.
 *
 * Valide les données entrantes pour la modification d'un tag (product_tags)
 * avec ses traductions associées (product_tag_translations).
 * Gère l'unicité de sort_order en excluant le tag en cours de modification.
 */
class UpdateProductTagRequest extends FormRequest
{
    /**
     * Chemin de base pour les traductions des messages de validation.
     */
    protected string $backendLangPath = 'v1/backend/views/pages/products/tags/messages/tags-messages-validation.productTags';

    /**
     * ID résolu du tag à partir de l'UUID de la route.
     * Utilisé pour exclure le tag courant des règles d'unicité.
     */
    protected ?int $productTagId = null;

    /**
     * Résolution de l'ID du tag avant la validation.
     * Le tag est déjà résolu par le binding implicite de la route {productTag}
     * (clé = uuid via ProductTag::getRouteKeyName()).
     */
    protected function prepareForValidation(): void
    {
        /** @var ProductTag|null $productTag */
        $productTag = $this->route('productTag');

        // Sécurité : si le paramètre n'a pas été injecté comme modèle, on renvoie une 404.
        if (! $productTag instanceof ProductTag) {
            abort(404);
        }

        $this->productTagId = $productTag->id;
    }

    public function authorize(): bool
    {
        return true; // ← géré par middleware
    }

    /**
     * Règles de validation.
     *
     * NOTE IMPORTANTE (même approche que StoreProductTagRequest) :
     * La syntaxe statique 'unique:...,lang_id,{translations.*.lang_id}' ne
     * fonctionne PAS — Laravel traite "{translations.*.lang_id}" comme une
     * chaîne littérale, pas comme une référence dynamique au champ frère.
     * On reconstruit ces règles dynamiquement en PHP, avec un vrai closure
     * qui reçoit la vraie valeur de lang_id de chaque ligne de traduction.
     */
    public function rules(): array
    {
        $productTagId = $this->productTagId;

        $rules = [
            // sort_order doit être unique, sauf pour LE tag qu'on modifie
            // lui-même (sinon impossible de sauvegarder sans changer sa valeur).
            'sort_order' => ['required', 'integer', 'min:0', 'unique:product_tags,sort_order,'.$productTagId],

            'status' => ['required', Rule::in(ProductTagStatus::values())],

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
            //   tag en cours de modification.
            $rules["translations.{$index}.name"] = [
                'required',
                'string',
                'max:255',
                Rule::unique('product_tag_translations', 'name')
                    ->where(fn ($query) => $query->where('lang_id', $langId))
                    ->ignore($productTagId, 'tag_id')
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
            'sort_order.required' => __("{$this->backendLangPath}.sort_order.required"),
            'sort_order.integer' => __("{$this->backendLangPath}.sort_order.integer"),
            'sort_order.min' => __("{$this->backendLangPath}.sort_order.min"),
            'sort_order.unique' => __("{$this->backendLangPath}.sort_order.unique"),

            'status.required' => __("{$this->backendLangPath}.status.required"),
            'status.in' => __("{$this->backendLangPath}.status.in"),

            'translations.required' => __("{$this->backendLangPath}.translations.required"),
            'translations.array' => __("{$this->backendLangPath}.translations.array"),

            'translations.*.lang_id.required' => __("{$this->backendLangPath}.translations.lang_id.required"),
            'translations.*.lang_id.exists' => __("{$this->backendLangPath}.translations.lang_id.exists"),

            'translations.*.name.required' => __("{$this->backendLangPath}.translations.name.required"),
            'translations.*.name.string' => __("{$this->backendLangPath}.translations.name.string"),
            'translations.*.name.max' => __("{$this->backendLangPath}.translations.name.max"),
            'translations.*.name.unique' => __("{$this->backendLangPath}.translations.name.unique"),

            'translations.*.description.required' => __("{$this->backendLangPath}.translations.description.required"),
            'translations.*.description.string' => __("{$this->backendLangPath}.translations.description.string"),
            'translations.*.description.max' => __("{$this->backendLangPath}.translations.description.max"),
        ];
    }
}
