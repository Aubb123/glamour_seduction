<?php

namespace App\Http\Requests\V1\PlatformVersion;

use App\Enums\PlatformVersions\PlatformVersionStatus;
use App\Http\Requests\Concerns\ValidatesTranslatedFields;
use App\Models\PlatformVersion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

use function __;

class UpdatePlatformVersionRequest extends FormRequest
{
    use ValidatesTranslatedFields;

    protected string $backendLangPath = 'v1/backend/views/pages/platform-versions/messages/platform-messages-validation';

    public function authorize(): bool
    {
        return true; // ← géré par middleware + Policy
    }

    protected function prepareForValidation(): void
    {
        $releasedAt = $this->input('released_at');

        if (is_string($releasedAt) && $releasedAt !== '' && str_contains($releasedAt, 'T')) {
            try {
                $this->merge([
                    'released_at' => Carbon::parse($releasedAt)->format('Y-m-d H:i:s'),
                ]);
            } catch (\Throwable $e) {
                // Keep the original value so the date rule can reject it cleanly.
            }
        }
    }

    public function rules(): array
    {
        $platformVersion = $this->route('platformVersion');

        if (! $platformVersion instanceof PlatformVersion) {
            abort(404);
        }

        return [
            'code' => ['required', 'string', 'max:10', 'regex:/^[a-z0-9\-]+$/', 'unique:platform_versions,code,'.$platformVersion->id],
            'status' => ['required', Rule::in(PlatformVersionStatus::values())],
            'is_published' => ['boolean'],
            'released_at' => ['nullable', 'date'],
            'translations' => ['required', 'array', 'min:1'],
            'translations.*.lang_id' => ['required', 'integer', 'exists:langs,id'],
            'translations.*.name' => ['required', 'string', 'max:255'],
            'translations.*.description' => ['required', 'string'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $platformVersion = $this->route('platformVersion');

            if (! $platformVersion instanceof PlatformVersion) {
                return;
            }

            $this->validateTranslatedFields(
                $validator,
                'platform_version_translations',
                'platform_version_id',
                (int) $platformVersion->id,
                ['name', 'description'],
            );

            $langIds = collect($this->input('translations', []))->pluck('lang_id');

            if ($langIds->count() !== $langIds->unique()->count()) {
                $validator->errors()->add(
                    'translations',
                    __("{$this->backendLangPath}.translations.lang_id.duplicate"),
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'code.required' => __("{$this->backendLangPath}.code.required"),
            'code.string' => __("{$this->backendLangPath}.code.string"),
            'code.max' => __("{$this->backendLangPath}.code.max"),
            'code.unique' => __("{$this->backendLangPath}.code.unique"),
            'code.regex' => __("{$this->backendLangPath}.code.regex"),
            'released_at.date' => __("{$this->backendLangPath}.released_at.date"),
            'status.required' => __("{$this->backendLangPath}.status.required"),
            'status.in' => __("{$this->backendLangPath}.status.in"),
            'translations.required' => __("{$this->backendLangPath}.translations.required"),
            'translations.array' => __("{$this->backendLangPath}.translations.array"),
            'translations.min' => __("{$this->backendLangPath}.translations.min"),
            'translations.lang_id.duplicate' => __("{$this->backendLangPath}.translations.lang_id.duplicate"),
            'translations.*.lang_id.required' => __("{$this->backendLangPath}.translations.lang_id.required"),
            'translations.*.lang_id.integer' => __("{$this->backendLangPath}.translations.lang_id.integer"),
            'translations.*.lang_id.exists' => __("{$this->backendLangPath}.translations.lang_id.exists"),
            'translations.*.name.required' => __("{$this->backendLangPath}.translations.name.required"),
            'translations.*.name.string' => __("{$this->backendLangPath}.translations.name.string"),
            'translations.*.name.max' => __("{$this->backendLangPath}.translations.name.max"),
            'translations.*.name.unique' => __("{$this->backendLangPath}.translations.name.unique"),
            'translations.*.description.required' => __("{$this->backendLangPath}.translations.description.required"),
            'translations.*.description.string' => __("{$this->backendLangPath}.translations.description.string"),
        ];
    }
}
