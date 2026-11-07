<?php

namespace App\Http\Requests\V1\Products\Warehouses;

use App\Enums\Products\Warehouses\ProductWarehouseStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

/**
 * Requête de validation pour la mise à jour d'un entrepôt.
 *
 * Valide les données entrantes pour la mise à jour d'un entrepôt (product_warehouses)
 * avec ses traductions associées (product_warehouse_translations).
 */
class UpdateProductWarehouseRequest extends FormRequest
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/products/warehouses/messages/warehouses-messages-validation.productWarehouses';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // ← géré par middleware
    }

    /**
     * Règles de validation.
     */
    public function rules(): array
    {
        $rules = [
            // Code obligatoire et unique — ignore la propre ligne (update) — non traduit
            'code' => ['sometimes', 'required', 'string', 'max:20', Rule::unique('product_warehouses', 'code')->ignore($this->route('productWarehouse'))],

            'status' => ['sometimes', 'required', Rule::in(ProductWarehouseStatus::values())],

            'is_default' => ['sometimes', 'boolean'],

            // Le tableau de traductions lui-même
            'translations' => ['sometimes', 'required', 'array'],

            'translations.*.lang_id' => ['required', 'exists:langs,id'],

            'translations.*.address' => ['nullable', 'string', 'max:255'],
            'translations.*.city' => ['nullable', 'string', 'max:100'],
            'translations.*.phone' => ['nullable', 'string', 'max:30'],
        ];

        // ────────────────────────────────────────────────────────────
        // Génération dynamique des règles "unique par langue" pour le nom,
        // basées sur les données réellement envoyées dans la requête.
        // ────────────────────────────────────────────────────────────
        $translations = $this->input('translations', []);

        foreach ($translations as $index => $translation) {

            $langId = $translation['lang_id'] ?? null;

            // Nom : unique parmi les traductions de la MÊME langue (hors corbeille)
            $rules["translations.{$index}.name"] = [
                'required',
                'string',
                'max:255',
                Rule::unique('product_warehouse_translations', 'name')
                    ->where(fn ($query) => $query->where('lang_id', $langId))
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
            'code.required' => __("{$this->backendLangPath}.code.required"),
            'code.string' => __("{$this->backendLangPath}.code.string"),
            'code.max' => __("{$this->backendLangPath}.code.max"),
            'code.unique' => __("{$this->backendLangPath}.code.unique"),

            'status.required' => __("{$this->backendLangPath}.status.required"),
            'status.in' => __("{$this->backendLangPath}.status.in"),

            'is_default.boolean' => __("{$this->backendLangPath}.is_default.boolean"),

            'translations.required' => __("{$this->backendLangPath}.translations.required"),
            'translations.array' => __("{$this->backendLangPath}.translations.array"),

            'translations.*.lang_id.required' => __("{$this->backendLangPath}.translations.lang_id.required"),
            'translations.*.lang_id.exists' => __("{$this->backendLangPath}.translations.lang_id.exists"),

            'translations.*.name.required' => __("{$this->backendLangPath}.translations.name.required"),
            'translations.*.name.string' => __("{$this->backendLangPath}.translations.name.string"),
            'translations.*.name.max' => __("{$this->backendLangPath}.translations.name.max"),
            'translations.*.name.unique' => __("{$this->backendLangPath}.translations.name.unique"),

            'translations.*.address.string' => __("{$this->backendLangPath}.translations.address.string"),
            'translations.*.address.max' => __("{$this->backendLangPath}.translations.address.max"),

            'translations.*.city.string' => __("{$this->backendLangPath}.translations.city.string"),
            'translations.*.city.max' => __("{$this->backendLangPath}.translations.city.max"),

            'translations.*.phone.string' => __("{$this->backendLangPath}.translations.phone.string"),
            'translations.*.phone.max' => __("{$this->backendLangPath}.translations.phone.max"),
        ];
    }
}
