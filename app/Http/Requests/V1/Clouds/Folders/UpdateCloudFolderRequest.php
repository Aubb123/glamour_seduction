<?php

namespace App\Http\Requests\V1\Clouds\Folders;

use App\Enums\Clouds\Folders\CloudFolderStatus;
use App\Traits\LogActivity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function __;

class UpdateCloudFolderRequest extends FormRequest
{
    use LogActivity;

    protected string $langPath = 'v1/backend/views/pages/clouds/folders/messages/cloud-folders-messages-validation.cloud-folders';

    // ID du dossier résolu une seule fois par requête
    protected ?int $cloudFolderId = null;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalise l'entrée avant validation :
     * - `content_type` est saisi sous forme de liste d'extensions séparées par des virgules
     *   dans le formulaire ; on le convertit ici en tableau d'extensions.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('content_type')) {
            $raw = $this->input('content_type');

            if (is_array($raw)) {
                $values = $raw;
            } else {
                $values = array_map('trim', explode(',', (string) $raw));
            }

            $this->merge([
                'content_type' => array_values(array_filter($values, fn ($value) => $value !== '')),
            ]);
        }
    }

    // Résout et met en cache l'ID du dossier (binding implicite {cloudFolder}) pour éviter une double requête
    protected function getCloudFolderId(): ?int
    {
        if ($this->cloudFolderId === null) {
            $this->cloudFolderId = $this->route('cloudFolder')?->id;
        }

        return $this->cloudFolderId;
    }

    public function rules(): array
    {
        if (! $this->getCloudFolderId()) {

            $this->logActivity(type: 'error', action: 'backend.cloud-folders.update', context: ['cloud_folder_uuid' => $this->route('cloudFolder')?->uuid], message: 'CloudFolder not found for update.', activity: __("{$this->langPath}.not_found"), is_success: false);

            abort(404, __("{$this->langPath}.not_found"));
        }

        return [
            'folder_name' => ['nullable', 'string', 'max:191'],
            'entity_table' => ['nullable', 'string', 'max:191'],
            'content_type' => ['nullable', 'array'],
            'content_type.*' => ['string', 'max:32'],
            'metadata' => ['nullable', 'array'],
            'status' => ['required', Rule::in(CloudFolderStatus::values())],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => __("{$this->langPath}.status.required"),
            'status.in' => __("{$this->langPath}.status.in"),
            'folder_name.string' => __("{$this->langPath}.folder_name.string"),
            'entity_table.string' => __("{$this->langPath}.entity_table.string"),
            'content_type.array' => __("{$this->langPath}.content_type.array"),
        ];
    }
}
