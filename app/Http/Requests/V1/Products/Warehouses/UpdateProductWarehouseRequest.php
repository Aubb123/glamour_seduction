<?php

namespace App\Http\Requests\V1\Products\Warehouses;

use App\Enums\Products\Warehouses\ProductWarehouseStatus;
use App\Enums\Products\Warehouses\ProductWarehouseType;
use App\Models\ProductWarehouseTranslation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

/**
 * Requête de validation pour la mise à jour d'un entrepôt de produits.
 *
 * NOTE : `code` est volontairement absent des règles — il ne peut plus être modifié
 * après la création (règle métier de la table product_warehouses).
 */
class UpdateProductWarehouseRequest extends FormRequest
{
    /**
     * Chemin de base pour les traductions des messages de validation.
     */
    protected string $backendLangPath = 'v1/backend/views/pages/products/warehouses/messages/warehouses-messages-validation.warehouses';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Entrepôt courant résolu par le model binding (route {productWarehouse}).
     */
    protected function getWarehouseId(): ?int
    {
        return $this->route('productWarehouse')?->id;
    }

    public function rules(): array
    {
        $warehouseId = $this->getWarehouseId();

        $rules = [
            'sort_order' => ['required', 'integer', 'min:0', Rule::unique('product_warehouses', 'sort_order')->ignore($warehouseId)],

            'type' => ['required', Rule::in(ProductWarehouseType::values())],
            'status' => ['required', Rule::in(ProductWarehouseStatus::values())],
            'is_default' => ['nullable', 'boolean'],

            'country_id' => ['nullable', 'integer', 'exists:geo_countries,id'],
            'city' => ['nullable', 'string', 'max:150'],
            'address' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90', 'required_with:longitude'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180', 'required_with:latitude'],

            'phone' => ['nullable', 'string', 'max:30', 'regex:/^\+?[0-9\s().-]{6,30}$/'],
            'email' => ['nullable', 'email', 'max:150'],
            'notes' => ['nullable', 'string', 'max:'.(int) getRequiredSettingValue('max_product_warehouse_notes_length', app()->getLocale())],

            'translations' => ['required', 'array'],
            'translations.*.lang_id' => ['required', 'exists:langs,id'],
            'translations.*.name' => ['required', 'string', 'max:150'],
            'translations.*.description' => ['nullable', 'string'],
        ];

        // Le nom doit être unique par langue (corbeille incluse), hors entrepôt courant
        foreach ($this->input('translations', []) as $index => $translation) {
            $langId = is_array($translation) ? ($translation['lang_id'] ?? null) : null;

            if (! $langId) {
                continue;
            }

            $rules["translations.{$index}.name"][] = Rule::unique('product_warehouse_translations', 'name')
                ->where(fn ($query) => $query
                    ->where('lang_id', $langId)
                    ->where('warehouse_id', '!=', $warehouseId));
        }

        return $rules;
    }

    /**
     * Configure le validateur avec des vérifications complémentaires.
     */
    public function withValidator($validator): void
    {
        $warehouseId = $this->getWarehouseId();

        $validator->after(function ($validator) use ($warehouseId): void {
            $translations = $this->input('translations', []);

            if (! is_array($translations)) {
                return;
            }

            // Vérifie les doublons de lang_id dans le payload
            $langIds = collect($translations)->pluck('lang_id')->filter();

            if ($langIds->count() !== $langIds->unique()->count()) {
                $validator->errors()->add(
                    'translations',
                    __("{$this->backendLangPath}.translations.lang_id.duplicate"),
                );
            }

            // Un entrepôt par défaut doit être un entrepôt de stockage actif
            if ((bool) $this->input('is_default', false)) {
                if ($this->input('type') !== ProductWarehouseType::Storage->value) {
                    $validator->errors()->add('is_default', __("{$this->backendLangPath}.is_default.storage_required"));
                }

                if ($this->input('status') !== ProductWarehouseStatus::Active->value) {
                    $validator->errors()->add('is_default', __("{$this->backendLangPath}.is_default.active_required"));
                }
            }

            // Doublons de nom dans le payload courant, par langue
            $seen = [];

            foreach ($translations as $key => $translation) {
                if (! is_array($translation)) {
                    continue;
                }

                $langId = $translation['lang_id'] ?? null;
                $name = $translation['name'] ?? null;

                if (! $langId || $name === null || $name === '') {
                    continue;
                }

                $fingerprint = (string) $langId.'|'.(string) $name;

                if (isset($seen[$fingerprint])) {
                    $validator->errors()->add(
                        "translations.{$key}.name",
                        __("{$this->backendLangPath}.translations.name.unique"),
                    );

                    continue;
                }

                $seen[$fingerprint] = true;

                // Doublon en base de données (corbeille incluse, hors entrepôt courant)
                $exists = ProductWarehouseTranslation::withTrashed()
                    ->where('lang_id', $langId)
                    ->where('warehouse_id', '!=', $warehouseId)
                    ->where('name', $name)
                    ->exists();

                if ($exists) {
                    $validator->errors()->add(
                        "translations.{$key}.name",
                        __("{$this->backendLangPath}.translations.name.unique"),
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'sort_order.required' => __("{$this->backendLangPath}.sort_order.required"),
            'sort_order.integer' => __("{$this->backendLangPath}.sort_order.integer"),
            'sort_order.min' => __("{$this->backendLangPath}.sort_order.min"),
            'sort_order.unique' => __("{$this->backendLangPath}.sort_order.unique"),

            'type.required' => __("{$this->backendLangPath}.type.required"),
            'type.in' => __("{$this->backendLangPath}.type.in"),
            'status.required' => __("{$this->backendLangPath}.status.required"),
            'status.in' => __("{$this->backendLangPath}.status.in"),
            'is_default.boolean' => __("{$this->backendLangPath}.is_default.boolean"),
            'country_id.integer' => __("{$this->backendLangPath}.country_id.integer"),
            'country_id.exists' => __("{$this->backendLangPath}.country_id.exists"),
            'city.string' => __("{$this->backendLangPath}.city.string"),
            'city.max' => __("{$this->backendLangPath}.city.max"),
            'address.string' => __("{$this->backendLangPath}.address.string"),
            'address.max' => __("{$this->backendLangPath}.address.max"),
            'latitude.numeric' => __("{$this->backendLangPath}.latitude.numeric"),
            'latitude.between' => __("{$this->backendLangPath}.latitude.between"),
            'latitude.required_with' => __("{$this->backendLangPath}.latitude.required_with"),
            'longitude.numeric' => __("{$this->backendLangPath}.longitude.numeric"),
            'longitude.between' => __("{$this->backendLangPath}.longitude.between"),
            'longitude.required_with' => __("{$this->backendLangPath}.longitude.required_with"),
            'phone.string' => __("{$this->backendLangPath}.phone.string"),
            'phone.max' => __("{$this->backendLangPath}.phone.max"),
            'phone.regex' => __("{$this->backendLangPath}.phone.regex"),
            'email.email' => __("{$this->backendLangPath}.email.email"),
            'email.max' => __("{$this->backendLangPath}.email.max"),
            'notes.string' => __("{$this->backendLangPath}.notes.string"),
            'notes.max' => __("{$this->backendLangPath}.notes.max", ['max' => (int) getRequiredSettingValue('max_product_warehouse_notes_length', app()->getLocale())]),
            'translations.required' => __("{$this->backendLangPath}.translations.required"),
            'translations.array' => __("{$this->backendLangPath}.translations.array"),
            'translations.*.lang_id.required' => __("{$this->backendLangPath}.translations.lang_id.required"),
            'translations.*.lang_id.exists' => __("{$this->backendLangPath}.translations.lang_id.exists"),
            'translations.*.name.required' => __("{$this->backendLangPath}.translations.name.required"),
            'translations.*.name.string' => __("{$this->backendLangPath}.translations.name.string"),
            'translations.*.name.max' => __("{$this->backendLangPath}.translations.name.max"),
            'translations.*.name.unique' => __("{$this->backendLangPath}.translations.name.unique"),
            'translations.*.description.string' => __("{$this->backendLangPath}.translations.description.string"),
            'is_default.storage_required' => __("{$this->backendLangPath}.is_default.storage_required"),
            'is_default.active_required' => __("{$this->backendLangPath}.is_default.active_required"),
        ];
    }
}
