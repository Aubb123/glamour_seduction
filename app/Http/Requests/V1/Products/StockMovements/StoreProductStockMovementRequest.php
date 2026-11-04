<?php

namespace App\Http\Requests\V1\Products\StockMovements;

use App\Enums\Products\StockMovements\ProductStockMovementType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

class StoreProductStockMovementRequest extends FormRequest
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/products/stock-movements/messages/stock-movements-messages-validation.productStockMovements';

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
        return [
            // Entrepôt et variante obligatoires et existants
            'warehouse_id' => ['required', 'integer', 'exists:product_warehouses,id'],
            'product_variant_id' => ['required', 'integer', 'exists:product_variants,id'],

            // Type de mouvement dans l'enum (le sens est donné par le type)
            'type' => ['required', 'string', Rule::in(ProductStockMovementType::values())],

            // Quantité toujours > 0 (le sens est donné par le type)
            'quantity' => ['required', 'integer', 'min:1'],

            // Référence polymorphique optionnelle (commande, réassort, transfert…)
            'reference_type' => ['nullable', 'string', 'max:255'],
            'reference_id' => ['nullable', 'integer', 'min:1'],

            // Note libre optionnelle
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Validation conditionnelle : une référence polymorphique doit être complète
     * (type ET id fournis ensemble).
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $hasType = filled($this->input('reference_type'));
            $hasId = filled($this->input('reference_id'));

            if ($hasType !== $hasId) {
                $validator->errors()->add(
                    'reference_type',
                    __("{$this->backendLangPath}.reference.incomplete")
                );
            }
        });
    }

    /**
     * Messages de validation personnalisés.
     */
    public function messages(): array
    {
        return [
            'warehouse_id.required' => __("{$this->backendLangPath}.warehouse_id.required"),
            'warehouse_id.integer' => __("{$this->backendLangPath}.warehouse_id.integer"),
            'warehouse_id.exists' => __("{$this->backendLangPath}.warehouse_id.exists"),

            'product_variant_id.required' => __("{$this->backendLangPath}.product_variant_id.required"),
            'product_variant_id.integer' => __("{$this->backendLangPath}.product_variant_id.integer"),
            'product_variant_id.exists' => __("{$this->backendLangPath}.product_variant_id.exists"),

            'type.required' => __("{$this->backendLangPath}.type.required"),
            'type.string' => __("{$this->backendLangPath}.type.string"),
            'type.in' => __("{$this->backendLangPath}.type.in"),

            'quantity.required' => __("{$this->backendLangPath}.quantity.required"),
            'quantity.integer' => __("{$this->backendLangPath}.quantity.integer"),
            'quantity.min' => __("{$this->backendLangPath}.quantity.min"),

            'reference_type.string' => __("{$this->backendLangPath}.reference_type.string"),
            'reference_type.max' => __("{$this->backendLangPath}.reference_type.max"),
            'reference_id.integer' => __("{$this->backendLangPath}.reference_id.integer"),
            'reference_id.min' => __("{$this->backendLangPath}.reference_id.min"),

            'note.string' => __("{$this->backendLangPath}.note.string"),
            'note.max' => __("{$this->backendLangPath}.note.max"),
        ];
    }
}
