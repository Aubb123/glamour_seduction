<?php

namespace App\Http\Requests\V1\Faq;

use App\Enums\Faqs\FaqStatus;
use App\Models\FaqTranslation;
use App\Traits\LogActivity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

class UpdateFaqRequest extends FormRequest
{
    use LogActivity;

    protected string $langPath = 'v1/backend/views/pages/faqs/messages/faqs-messages-validation.faqs';

    // ID de la FAQ résolu une seule fois par requête (binding implicite {faq} → Faq)
    protected ?int $faqId = null;

    public function authorize(): bool
    {
        return true;
    }

    // Résout et met en cache l'ID de la FAQ pour éviter une double requête
    protected function getFaqId(): ?int
    {
        if ($this->faqId === null) {
            $this->faqId = $this->route('faq')?->id;
        }

        return $this->faqId;
    }

    public function rules(): array
    {
        $faqId = $this->getFaqId();

        if (! $faqId) {
            $this->logActivity(type: 'error', action: 'backend.faqs.update', context: ['faq_uuid' => $this->route('faq')?->uuid], message: 'FAQ not found for update.', activity: __("{$this->langPath}.not_found"), is_success: false);
            abort(404, __("{$this->langPath}.not_found"));
        }

        return [
            'sort_order' => ['required', 'integer', 'min:0', 'unique:faqs,sort_order,'.$faqId],
            'status' => ['required', Rule::in(FaqStatus::values())],

            'translations' => ['required', 'array', 'min:1'],
            'translations.*.lang_id' => ['required', 'exists:langs,id'],
            'translations.*.question' => ['required', 'string', 'max:255'],
            'translations.*.answer' => ['required', 'string'],
        ];
    }

    public function withValidator($validator): void
    {
        $faqId = $this->getFaqId();

        if (! $faqId) {
            return;
        }

        // Ajoute une validation personnalisée pour vérifier l'unicité de la question dans les traductions
        $validator->after(function ($validator) use ($faqId) {

            $translations = $this->input('translations', []);

            foreach ($translations as $key => $translation) {

                $langId = $translation['lang_id'] ?? null;
                $question = $translation['question'] ?? null;

                if (! $langId || ! $question) {
                    continue;
                }

                $exists = FaqTranslation::where('question', $question)
                    ->where('lang_id', $langId)
                    ->where('faq_id', '!=', $faqId)
                    ->exists();

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
