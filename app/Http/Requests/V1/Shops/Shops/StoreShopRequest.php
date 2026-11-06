<?php

namespace App\Http\Requests\V1\Shops\Shops;

use App\Enums\Shops\Shops\ShopStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

/**
 * Requête de validation pour la création d'une boutique.
 *
 * Valide les données entrantes pour la création d'une boutique (shops)
 * avec ses traductions associées (shop_translations) et ses entrepôts
 * rattachés (pivot shop_warehouse).
 */
class StoreShopRequest extends FormRequest
{
    /**
     * Chemin de base pour les traductions des messages de validation.
     */
    protected string $backendLangPath = 'v1/backend/views/pages/shops/shops/messages/shops-messages-validation.shops';

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
     * NOTE : les règles d'unicité « par langue » sur les traductions sont construites
     * dynamiquement (cf. StoreProductCategoryRequest) car Laravel n'interprète pas
     * "{translations.*.lang_id}" comme une référence dynamique en syntaxe statique.
     */
    public function rules(): array
    {
        $rules = [
            // Code unique de la boutique
            'code' => ['required', 'string', 'max:20', 'unique:shops,code'],

            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],

            // Le statut doit être l'une des valeurs de l'énumération ShopStatus
            'status' => ['required', Rule::in(ShopStatus::values())],

            'is_default' => ['sometimes', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],

            // Règle métier : une boutique est TOUJOURS liée à au moins un entrepôt
            'warehouses' => ['required', 'array', 'min:1'],
            'warehouses.*' => ['exists:product_warehouses,uuid'],

            // Entrepôt principal d'approvisionnement (optionnel)
            'primary_warehouse_uuid' => ['nullable', 'exists:product_warehouses,uuid'],

            // Le tableau de traductions lui-même
            'translations' => ['required', 'array'],
            'translations.*.lang_id' => ['required', 'exists:langs,id'],
            'translations.*.description' => ['nullable', 'string', 'max:1000'],
        ];

        // Génération dynamique des règles « nom unique par langue »
        $translations = $this->input('translations', []);

        foreach ($translations as $index => $translation) {

            $langId = $translation['lang_id'] ?? null;

            $rules["translations.{$index}.name"] = [
                'required',
                'string',
                'max:255',
                Rule::unique('shop_translations', 'name')
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

            'email.email' => __("{$this->backendLangPath}.email.email"),
            'email.max' => __("{$this->backendLangPath}.email.max"),
            'phone.string' => __("{$this->backendLangPath}.phone.string"),
            'phone.max' => __("{$this->backendLangPath}.phone.max"),
            'address.string' => __("{$this->backendLangPath}.address.string"),
            'address.max' => __("{$this->backendLangPath}.address.max"),
            'city.string' => __("{$this->backendLangPath}.city.string"),
            'city.max' => __("{$this->backendLangPath}.city.max"),

            'status.required' => __("{$this->backendLangPath}.status.required"),
            'status.in' => __("{$this->backendLangPath}.status.in"),

            'is_default.boolean' => __("{$this->backendLangPath}.is_default.boolean"),

            'sort_order.required' => __("{$this->backendLangPath}.sort_order.required"),
            'sort_order.integer' => __("{$this->backendLangPath}.sort_order.integer"),
            'sort_order.min' => __("{$this->backendLangPath}.sort_order.min"),

            'warehouses.required' => __("{$this->backendLangPath}.warehouses.required"),
            'warehouses.array' => __("{$this->backendLangPath}.warehouses.array"),
            'warehouses.min' => __("{$this->backendLangPath}.warehouses.min"),
            'warehouses.*.exists' => __("{$this->backendLangPath}.warehouses.exists"),

            'primary_warehouse_uuid.exists' => __("{$this->backendLangPath}.primary_warehouse_uuid.exists"),

            'translations.required' => __("{$this->backendLangPath}.translations.required"),
            'translations.array' => __("{$this->backendLangPath}.translations.array"),

            'translations.*.lang_id.required' => __("{$this->backendLangPath}.translations.lang_id.required"),
            'translations.*.lang_id.exists' => __("{$this->backendLangPath}.translations.lang_id.exists"),

            'translations.*.name.required' => __("{$this->backendLangPath}.translations.name.required"),
            'translations.*.name.string' => __("{$this->backendLangPath}.translations.name.string"),
            'translations.*.name.max' => __("{$this->backendLangPath}.translations.name.max"),
            'translations.*.name.unique' => __("{$this->backendLangPath}.translations.name.unique"),

            'translations.*.description.string' => __("{$this->backendLangPath}.translations.description.string"),
            'translations.*.description.max' => __("{$this->backendLangPath}.translations.description.max"),
        ];
    }
}
