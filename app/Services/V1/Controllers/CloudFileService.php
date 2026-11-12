<?php

namespace App\Services\V1\Controllers;

use App\Enums\Clouds\Files\CloudFileStatus;
use App\Enums\Clouds\Folders\CloudFolderStatus;
use App\Enums\Clouds\Platforms\CloudPlatformStatus;
use App\Models\CloudFile;
use App\Models\CloudFolder;
use App\Models\CloudPlatform;
use App\Services\Cloud\CloudStorageService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use function __;

class CloudFileService
{
    protected string $backendLangPath = 'v1/backend/views/pages/clouds/files/messages/cloud-files-messages-service.cloud-files';

    public function __construct(protected CloudStorageService $cloudStorage) {}

    public function backend_cloud_files_store($data): CloudFile
    {
        return DB::transaction(function () use ($data) {
            return CloudFile::create([
                'cloud_folder_id' => $data['cloud_folder_id'],
                'fileable_type' => $data['fileable_type'],
                'fileable_id' => $data['fileable_id'],
                'cloud_file_id' => null,
                'remote_path' => $data['remote_path'] ?? null,
                'sync_status' => $data['sync_status'],
                'local_path' => $data['local_path'],
                'last_sync_attempt_at' => $data['last_sync_attempt_at'],
                'file_name' => $data['file_name'],
                'file_type' => $data['file_type'],
                'file_size' => $data['file_size'],
                'mime_type' => $data['mime_type'],
                'shareable_link' => $data['shareable_link'],
                'hash' => $data['hash'],
                'metadata' => $data['metadata'],
            ]);
        });
    }

    public function backend_cloud_files_sync_selected(array $fileUuids): array
    {
        set_time_limit(0);
        if (empty($fileUuids)) return ['success_count' => 0, 'failed_count' => 0];

        $maxFiles = getSettingValue('max_files_sync_batch', app()->getLocale());
        if (count($fileUuids) > $maxFiles) {
            throw new \InvalidArgumentException(__("{$this->backendLangPath}.sync.max_selection_exceeded", ['max' => $maxFiles]));
        }

        $cloudFiles = CloudFile::whereIn('uuid', $fileUuids)->retryable()->get();
        $successCount = 0;
        $failedCount = 0;
        foreach ($cloudFiles as $cloudFile) {
            $cloudFile->update(['sync_status' => CloudFileStatus::Processing, 'last_sync_attempt_at' => getDateTime()]);
            $this->cloudStorage->migrateToCloud($cloudFile) ? $successCount++ : $failedCount++;
        }
        return ['success_count' => $successCount, 'failed_count' => $failedCount];
    }

    public function backend_cloud_files_destroy(CloudFile $cloudFile): void
    {
        DB::transaction(function () use ($cloudFile) {
            if ($cloudFile->sync_status === CloudFileStatus::Processing) throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.processing_forbidden"));
            if ($cloudFile->fileable) throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.no_delete_forbidden"));
            if ($cloudFile->trashed()) throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.already_deleted"));
            destroy_function($cloudFile);
        });
    }

    public function backend_cloud_files_restore(CloudFile $cloudFile): void
    {
        DB::transaction(function () use ($cloudFile) {
            $fileable = $cloudFile->fileable()->withTrashed()->first();
            if (! $fileable || $fileable->trashed()) throw new \InvalidArgumentException(__("{$this->backendLangPath}.restore.fileable_not_found"));
            if ($cloudFile->trashed()) restore_function($cloudFile);
            else throw new \InvalidArgumentException(__("{$this->backendLangPath}.restore.not_trashed_forbidden"));
        });
    }

    public function backend_cloud_files_force_destroy(CloudFile $cloudFile): void
    {
        DB::transaction(function () use ($cloudFile) {
            $fileable = $cloudFile->fileable()->withTrashed()->first();
            if ($fileable && $fileable->trashed()) throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.fileable_not_found"));
            if ($cloudFile->sync_status === CloudFileStatus::Processing) throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.processing_forbidden"));
            if ($cloudFile->fileable) throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.no_delete_forbidden"));
            if ($cloudFile->trashed()) force_destroy_function($cloudFile);
            else throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.not_trashed_forbidden"));
        });
    }

    public function uploadEntityImage(UploadedFile $file, string $cloudPlatformUuid, Model $entity, string $entityTable, string $backendLangPath): CloudFile
    {
        deleteOldCloudFiles($entity, 'image');
        $cloudPlatform = CloudPlatform::where('uuid', $cloudPlatformUuid)->firstOrFail();
        if ($cloudPlatform->status !== CloudPlatformStatus::Active) throw new \InvalidArgumentException(__("{$backendLangPath}.store.cloud_platform_inactive"));
        if ($cloudPlatform->name !== 'backblaze_b2') throw new \InvalidArgumentException(__("{$backendLangPath}.store.cloud_platform_not_supported"));

        $extension = strtolower($file->getClientOriginalExtension());
        $cloudFolder = CloudFolder::query()
            ->where('cloud_platform_id', $cloudPlatform->id)
            ->where('entity_table', $entityTable)
            ->where('status', CloudFolderStatus::Active->value)
            ->whereJsonContains('content_type', $extension)
            ->first();
        if (! $cloudFolder) throw new \InvalidArgumentException(__("{$backendLangPath}.store.cloud_platform_folder_not_found"));

        return $this->backend_cloud_files_store($this->cloudStorage->uploadEntityFile($file, $cloudPlatform, $entity, $cloudFolder));
    }

    /** @param array<int, UploadedFile> $files */
    public function uploadEntityFiles(array $files, string $cloudPlatformUuid, Model $entity, string $entityTable, string $backendLangPath): array
    {
        $cloudPlatform = CloudPlatform::where('uuid', $cloudPlatformUuid)->firstOrFail();
        if ($cloudPlatform->status !== CloudPlatformStatus::Active) throw new \InvalidArgumentException(__("{$backendLangPath}.store.cloud_platform_inactive"));
        if ($cloudPlatform->name !== 'backblaze_b2') throw new \InvalidArgumentException(__("{$backendLangPath}.store.cloud_platform_not_supported"));

        $firstPage = ((int) $entity->cloudFiles()->get()->max(fn (CloudFile $file) => (int) ($file->metadata['page'] ?? 0))) + 1;
        $cloudFiles = [];

        foreach (array_values($files) as $offset => $file) {
            $page = $firstPage + $offset;
            $extension = strtolower($file->getClientOriginalExtension());
            $cloudFolder = CloudFolder::query()
                ->where('cloud_platform_id', $cloudPlatform->id)
                ->where('entity_table', $entityTable)
                ->where('status', CloudFolderStatus::Active->value)
                ->whereJsonContains('content_type', $extension)
                ->first();
            if (! $cloudFolder) throw new \InvalidArgumentException(__("{$backendLangPath}.store.cloud_platform_folder_not_found"));

            $data = $this->cloudStorage->uploadEntityFile($file, $cloudPlatform, $entity, $cloudFolder, Str::random(12));
            $data['metadata']['page'] = $page;
            $cloudFiles[] = $this->backend_cloud_files_store($data);
        }
        return $cloudFiles;
    }
}
