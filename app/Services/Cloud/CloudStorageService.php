<?php

namespace App\Services\Cloud;

use App\Enums\Clouds\Files\CloudFileStatus;
use App\Enums\Clouds\Files\CloudFileType;
use App\Enums\Clouds\Platforms\CloudPlatformName;
use App\Models\CloudFile;
use App\Models\CloudFolder;
use App\Models\CloudPlatform;
use App\Services\Cloud\Contracts\CloudStorageInterface;
use App\Services\Cloud\Drivers\BackblazeDriver;
use App\Traits\LogActivity;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use function __;

class CloudStorageService
{
    use LogActivity;

    protected string $backendLangPath = 'v1/backend/others/services/cloud-storage-service.files';

    protected ?CloudStorageInterface $driver = null;

    protected ?CloudPlatform $platform = null;

    public function __construct() {}

    public function forPlatform(CloudPlatform $platform): static
    {
        $this->platform = $platform;
        $diskName = $platform->disk_name;

        if (! $diskName) {
            throw new \InvalidArgumentException(
                __("{$this->backendLangPath}.no_disk_name", ['platform' => $platform->name])
            );
        }

        $this->driver = match (CloudPlatformName::tryFrom($platform->name)) {
            CloudPlatformName::BackblazeB2 => new BackblazeDriver($diskName),
            null => throw new \InvalidArgumentException(
                __("{$this->backendLangPath}.unsupported_platform", ['platform' => $platform->name])
            ),
        };

        return $this;
    }

    /**
     * Upload a file locally and create its metadata record. Cloud migration is asynchronous.
     * The optional suffix lets a logical entity store multiple files with the same extension.
     */
    public function uploadEntityFile(
        UploadedFile $file,
        CloudPlatform $cloudPlatform,
        $entity,
        CloudFolder $cloudFolder,
        ?string $fileNameSuffix = null,
    ): array {
        $this->forPlatform($cloudPlatform);

        return DB::transaction(function () use ($file, $cloudPlatform, $entity, $cloudFolder, $fileNameSuffix) {
            $extension = strtolower($file->getClientOriginalExtension());
            $contentTypes = $cloudFolder->content_type ?? [];

            if (! empty($contentTypes) && ! in_array($extension, $contentTypes, true)) {
                throw new \InvalidArgumentException(
                    __("{$this->backendLangPath}.extension_not_allowed", ['extension' => $extension, 'folder' => $cloudFolder->folder_name])
                );
            }

            $fileType = CloudFileType::fromExtension($extension);
            $mime = $file->getMimeType();

            if (! CloudFileType::matchesMime($fileType, $mime)) {
                throw new \InvalidArgumentException(
                    __("{$this->backendLangPath}.mime_mismatch", ['extension' => $extension, 'type' => $fileType->value])
                );
            }

            // Unique paths prevent same-extension uploads for one entity from replacing each other.
            $suffix = $fileNameSuffix ? '-'.$fileNameSuffix : '';
            $newName = $entity->uuid.$suffix.'.'.$extension;
            $localDir = trim($cloudFolder->path, '/').'/'.$cloudFolder->folder_name;
            $path = $localDir.'/'.$newName;

            $hash = md5_file($file->getRealPath());
            $duplicate = CloudFile::where('cloud_folder_id', $cloudFolder->id)
                ->where('hash', $hash)
                ->exists();

            if ($duplicate) {
                throw new \InvalidArgumentException(
                    __("{$this->backendLangPath}.duplicate_file", ['name' => $file->getClientOriginalName()])
                );
            }

            $localPath = Storage::disk('local_uploads')->putFileAs($localDir, $file, $newName);

            return [
                'cloud_folder_id' => $cloudFolder->id,
                'fileable_type' => get_class($entity),
                'fileable_id' => $entity->id,
                'cloud_file_id' => null,
                'remote_path' => $path,
                'sync_status' => CloudFileStatus::Pending,
                'local_path' => $localPath,
                'last_sync_attempt_at' => null,
                'file_name' => basename($path),
                'file_type' => $fileType->value,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'shareable_link' => null,
                'hash' => null,
                'metadata' => [
                    'path' => $path,
                    'disk' => $cloudPlatform->disk_name,
                    'platform' => $cloudPlatform->name,
                ],
            ];
        });
    }

    /** Migrate a local file to cloud storage. */
    public function migrateToCloud(CloudFile $cloudFile): bool
    {
        try {
            if (! $cloudFile->local_path || ! Storage::disk('local_uploads')->exists($cloudFile->local_path)) {
                $cloudFile->update([
                    'sync_status' => CloudFileStatus::Error,
                    'last_sync_attempt_at' => getDateTime(),
                ]);

                return false;
            }

            $this->resolveDriverFromCloudFile($cloudFile);
            $metadata = is_array($cloudFile->metadata) ? $cloudFile->metadata : (json_decode($cloudFile->metadata, true) ?? []);
            $targetPath = $metadata['path'] ?? $cloudFile->file_name;
            $stream = Storage::disk('local_uploads')->readStream($cloudFile->local_path);

            if ($stream === false) {
                throw new \RuntimeException("Impossible d'ouvrir le fichier local : {$cloudFile->local_path}");
            }

            $uploaded = Storage::disk($this->platform->disk_name)->put($targetPath, $stream, [
                'ContentType' => $cloudFile->mime_type,
            ]);

            if (is_resource($stream)) {
                fclose($stream);
            }

            if (! $uploaded) {
                $cloudFile->update([
                    'sync_status' => CloudFileStatus::Error,
                    'last_sync_attempt_at' => getDateTime(),
                ]);
                $this->logActivity(type: 'error', action: 'backend.cloud-files.migrate', context: ['uuid' => $cloudFile->uuid], message: __("{$this->backendLangPath}.migrate_error", ['name' => $cloudFile->file_name]), is_success: false, loggable: $cloudFile);

                return false;
            }

            $cloudFile->update([
                'remote_path' => $targetPath,
                'sync_status' => CloudFileStatus::Synced,
                'last_sync_attempt_at' => getDateTime(),
                'metadata' => array_merge($metadata, ['synced_at' => getDateTime()]),
            ]);

            Storage::disk('local_uploads')->delete($cloudFile->local_path);
            $cloudFile->update(['local_path' => null]);

            return true;
        } catch (\Exception $e) {
            report($e);
            $cloudFile->update([
                'sync_status' => CloudFileStatus::Error,
                'last_sync_attempt_at' => getDateTime(),
            ]);
            $this->logActivity(type: 'error', action: 'backend.cloud-files.migrate', context: ['uuid' => $cloudFile->uuid], message: __("{$this->backendLangPath}.migrate_error", ['name' => $cloudFile->file_name]), is_success: false, loggable: $cloudFile);

            return false;
        }
    }

    public function url(CloudFile $cloudFile): string
    {
        $this->resolveDriverFromCloudFile($cloudFile);
        return $this->getDriver()->url($cloudFile->remote_path ?: $cloudFile->file_name);
    }

    public function temporaryUrl(CloudFile $cloudFile, ?int $expiresInMinutes = null): ?string
    {
        try {
            $expiresInMinutes ??= (int) (getSettingValue('temporary_url_expires_in_minutes', app()->getLocale()));
            $this->resolveDriverFromCloudFile($cloudFile);
            return $this->getDriver()->temporaryUrl(
                $cloudFile->remote_path ?: $cloudFile->file_name,
                getDateTime()->addMinutes($expiresInMinutes)
            );
        } catch (\Exception $e) {
            report($e);
            return null;
        }
    }

    public function delete(CloudFile $cloudFile): bool
    {
        try {
            if ($cloudFile->sync_status !== CloudFileStatus::Synced) {
                if ($cloudFile->local_path && Storage::disk('local_uploads')->exists($cloudFile->local_path)) {
                    Storage::disk('local_uploads')->delete($cloudFile->local_path);
                }
                return true;
            }
            $this->resolveDriverFromCloudFile($cloudFile);
            return $this->getDriver()->delete($cloudFile->remote_path);
        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }

    public function exists(CloudFile $cloudFile): bool
    {
        $this->resolveDriverFromCloudFile($cloudFile);
        return $this->getDriver()->exists($cloudFile->remote_path);
    }

    public function download(CloudFile $cloudFile)
    {
        $this->resolveDriverFromCloudFile($cloudFile);
        return $this->getDriver()->download($cloudFile->remote_path);
    }

    protected function resolveDriverFromCloudFile(CloudFile $cloudFile): void
    {
        if ($this->driver) {
            return;
        }

        $platform = $cloudFile->cloudFolder?->platform;
        if (! $platform) {
            throw new \RuntimeException(
                __("{$this->backendLangPath}.platform_not_found", ['uuid' => $cloudFile->uuid])
            );
        }
        $this->forPlatform($platform);
    }

    protected function getDriver(): CloudStorageInterface
    {
        if (! $this->driver) {
            throw new \RuntimeException('Aucun driver de stockage configuré. Appelez forPlatform() ou setDriver() d\'abord.');
        }
        return $this->driver;
    }

    public function __call(string $method, array $arguments)
    {
        return $this->getDriver()->{$method}(...$arguments);
    }
}
