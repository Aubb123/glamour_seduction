<?php

namespace App\Http\Requests\V1\Clouds\Platforms;

use App\Enums\Clouds\Platforms\CloudPlatformDiskName;
use App\Enums\Clouds\Platforms\CloudPlatformName;
use App\Enums\Clouds\Platforms\CloudPlatformStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

class StoreCloudPlatformRequest extends FormRequest
{
    protected string $backendLangPath = 'v1/backend/views/pages/clouds/platforms/messages/cloud-platforms-messages-validation.cloud-platforms';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::in(CloudPlatformName::values())],
            'label' => ['required', 'string', 'max:255', 'unique:cloud_platforms,label'],
            'disk_name' => ['required', 'string', 'max:255', Rule::in(CloudPlatformDiskName::values())],
            'api_endpoint' => ['required', 'string', 'max:255', 'url:http,https'],
            'api_key' => ['nullable', 'string', 'max:255', 'unique:cloud_platforms,api_key'],
            'api_secret' => ['nullable', 'string', 'max:255', 'unique:cloud_platforms,api_secret'],
            'description' => ['required', 'string'],
            'metadata' => ['nullable', 'json'],
            'status' => ['required', Rule::in(CloudPlatformStatus::values())],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __("{$this->backendLangPath}.name.required"),
            'name.string' => __("{$this->backendLangPath}.name.string"),
            'name.max' => __("{$this->backendLangPath}.name.max"),

            'label.required' => __("{$this->backendLangPath}.label.required"),
            'label.string' => __("{$this->backendLangPath}.label.string"),
            'label.max' => __("{$this->backendLangPath}.label.max"),

            'api_endpoint.required' => __("{$this->backendLangPath}.api_endpoint.required"),
            'api_endpoint.url' => __("{$this->backendLangPath}.api_endpoint.url"),
            'api_endpoint.string' => __("{$this->backendLangPath}.api_endpoint.string"),
            'api_endpoint.max' => __("{$this->backendLangPath}.api_endpoint.max"),

            'api_key.string' => __("{$this->backendLangPath}.api_key.string"),
            'api_key.max' => __("{$this->backendLangPath}.api_key.max"),
            'api_key.unique' => __("{$this->backendLangPath}.api_key.unique"),

            'api_secret.string' => __("{$this->backendLangPath}.api_secret.string"),
            'api_secret.max' => __("{$this->backendLangPath}.api_secret.max"),

            'description.string' => __("{$this->backendLangPath}.description.string"),

            'disk_name.required' => __("{$this->backendLangPath}.disk_name.required"),
            'disk_name.string' => __("{$this->backendLangPath}.disk_name.string"),
            'disk_name.max' => __("{$this->backendLangPath}.disk_name.max"),
            'disk_name.in' => __("{$this->backendLangPath}.disk_name.in"),

            'metadata.json' => __("{$this->backendLangPath}.metadata.json"),

            'status.required' => __("{$this->backendLangPath}.status.required"),
            'status.in' => __("{$this->backendLangPath}.status.in"),
        ];
    }
}
