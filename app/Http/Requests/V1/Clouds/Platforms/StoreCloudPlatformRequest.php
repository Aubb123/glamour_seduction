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
    protected string $langPath = 'v1/backend/views/pages/clouds/platforms/messages/cloud-platforms-messages-validation.cloud-platforms';

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
            'name.required' => __("{$this->langPath}.name.required"),
            'name.string' => __("{$this->langPath}.name.string"),
            'name.max' => __("{$this->langPath}.name.max"),

            'label.required' => __("{$this->langPath}.label.required"),
            'label.string' => __("{$this->langPath}.label.string"),
            'label.max' => __("{$this->langPath}.label.max"),

            'api_endpoint.required' => __("{$this->langPath}.api_endpoint.required"),
            'api_endpoint.url' => __("{$this->langPath}.api_endpoint.url"),
            'api_endpoint.string' => __("{$this->langPath}.api_endpoint.string"),
            'api_endpoint.max' => __("{$this->langPath}.api_endpoint.max"),

            'api_key.string' => __("{$this->langPath}.api_key.string"),
            'api_key.max' => __("{$this->langPath}.api_key.max"),
            'api_key.unique' => __("{$this->langPath}.api_key.unique"),

            'api_secret.string' => __("{$this->langPath}.api_secret.string"),
            'api_secret.max' => __("{$this->langPath}.api_secret.max"),

            'description.string' => __("{$this->langPath}.description.string"),

            'disk_name.required' => __("{$this->langPath}.disk_name.required"),
            'disk_name.string' => __("{$this->langPath}.disk_name.string"),
            'disk_name.max' => __("{$this->langPath}.disk_name.max"),
            'disk_name.in' => __("{$this->langPath}.disk_name.in"),

            'metadata.json' => __("{$this->langPath}.metadata.json"),

            'status.required' => __("{$this->langPath}.status.required"),
            'status.in' => __("{$this->langPath}.status.in"),
        ];
    }
}
