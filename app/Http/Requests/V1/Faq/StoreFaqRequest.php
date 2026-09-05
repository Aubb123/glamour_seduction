<?php

namespace App\Http\Requests\V1\Faq;

use App\Enums\Faqs\FaqStatus;
use App\Models\FaqTranslation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

class StoreFaqRequest extends FormRequest
{
    protected string $langPath = 'v1/backend/views/pages/faqs/messages/faqs-messages-validation.faqs';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sort_order' => ['required', 'integer', 'min:0', 'unique:faqs,sort_order'],
            'status' => ['required', Rule::in(FaqStatus::values())],

            'translations' => ['required', 'array', 'min:1'],
            'translations.*.lang_id' => ['required', 'exists:langs,id'],
            'translations.*.question' => ['required', 'string', 'max:255'],
            'translations.*.answer' => ['required', 'string'],
        ];
    }

    public function withValidator($validator): void
    {
        // Ajoute une validation personnalisée pour vérifier l'unicité de la question dans les traductions
        $validator->after(function ($validator) {

            $translations = $this->input('translations', []);

            foreach ($translations as $key => $translation) {

                $langId = $translation['lang_id'] ?? null;
                $question = $translation['question'] ?? null;

                if (! $langId || ! $question) {
                    continue;
                }

                // Vérifie si une traduction avec la même question et le même lang_id existe déjà
                $exists = FaqTranslation::where('question', $question)
                    ->where('lang_id', $langId)
                    ->exists();

                // Si une telle traduction existe, ajoute une erreur de validation
                if ($exists) {
                    $validator->errors()->add(
                        "translations.{$key}.question",
                        __("{$this->langPath}.translations.unique_question")
                    );
                }
            }
        });
    }

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
            'translations.min' => __("{$this->langPath}.translations.min"),

            'translations.*.lang_id.required' => __("{$this->langPath}.translations.lang_id.required"),
            'translations.*.lang_id.exists' => __("{$this->langPath}.translations.lang_id.exists"),

            'translations.*.question.required' => __("{$this->langPath}.translations.question.required"),
            'translations.*.question.string' => __("{$this->langPath}.translations.question.string"),
            'translations.*.question.max' => __("{$this->langPath}.translations.question.max"),

            'translations.*.answer.required' => __("{$this->langPath}.translations.answer.required"),
            'translations.*.answer.string' => __("{$this->langPath}.translations.answer.string"),
        ];
    }
}
