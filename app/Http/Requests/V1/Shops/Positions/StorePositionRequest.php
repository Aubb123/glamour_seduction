<?php

namespace App\Http\Requests\V1\Shops\Positions;

use App\Enums\Shops\Positions\PositionContext;
use App\Enums\Shops\Positions\PositionStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

class StorePositionRequest extends FormRequest
{
    // Chemin de base pour les traductions des messages de validation
    protected string $backendLangPath = 'v1/backend/views/pages/shops/positions/messages/positions-messages-validation.positions';

    public function authorize(): bool
    {
        return true; // ← géré par middleware
    }

    public function rules(): array
    {
        $rules = [
            'code' => ['required', 'string', 'max:30', 'unique:positions,code'],
            'context' => ['required', Rule::in(PositionContext::values())],
            'status' => ['required', Rule::in(PositionStatus::values())],
            'sort_order' => ['required', 'integer', 'min:0'],

            'translations' => ['required', 'array'],
            'translations.*.lang_id' => ['required', 'exists:langs,id'],
            'translations.*.description' => ['nullable', 'string', 'max:1000'],
        ];

        // Unicité du nom par langue (règles dynamiques)
        foreach ($this->input('translations', []) as $index => $translation) {

            $langId = $translation['lang_id'] ?? null;

            $rules["translations.{$index}.name"] = [
                'required',
                'string',
                'max:255',
                Rule::unique('position_translations', 'name')
                    ->where(fn ($query) => $query->where('lang_id', $langId))
                    ->whereNull('deleted_at'),
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'code.required' => __("{$this->backendLangPath}.code.required"),
            'code.string' => __("{$this->backendLangPath}.code.string"),
            'code.max' => __("{$this->backendLangPath}.code.max"),
            'code.unique' => __("{$this->backendLangPath}.code.unique"),

            'context.required' => __("{$this->backendLangPath}.context.required"),
            'context.in' => __("{$this->backendLangPath}.context.in"),

            'status.required' => __("{$this->backendLangPath}.status.required"),
            'status.in' => __("{$this->backendLangPath}.status.in"),

            'sort_order.required' => __("{$this->backendLangPath}.sort_order.required"),
            'sort_order.integer' => __("{$this->backendLangPath}.sort_order.integer"),
            'sort_order.min' => __("{$this->backendLangPath}.sort_order.min"),

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
