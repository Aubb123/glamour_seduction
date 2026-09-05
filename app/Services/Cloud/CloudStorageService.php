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

use function __;

class CloudStorageService
{
    use LogActivity;

    protected string $langPath = 'v1/backend/others/services/cloud-storage-service.files';

    protected ?CloudStorageInterface $driver = null;

    protected ?CloudPlatform $platform = null;

    public function __construct() {}

    /**
     * Configurer le service pour une plateforme cloud spécifique
     */
    public function forPlatform(CloudPlatform $platform): static
    {
        $this->platform = $platform;

        $diskName = $platform->disk_name;

        if (! $diskName) {
            throw new \InvalidArgumentException(
                __("{$this->langPath}.no_disk_name", ['platform' => $platform->name])
            );
        }

        // Seul le driver Backblaze B2 est réellement implémenté. Toute autre
        // plateforme (mega, etc.) lève une exception plutôt que de mapper
        // silencieusement sur un driver inadapté.
        $this->driver = match (CloudPlatformName::tryFrom($platform->name)) {
            CloudPlatformName::BackblazeB2 => new BackblazeDriver($diskName),
            null => throw new \InvalidArgumentException(
                __("{$this->langPath}.unsupported_platform", ['platform' => $platform->name])
            ),
        };

        return $this;
    }

    /**
     * Upload un fichier en local (rapide) et crée l'enregistrement en base.
     * La migration vers le cloud (B2) est ensuite effectuée de manière asynchrone
     * (commande cloud:sync / page de synchronisation).
     */
    public function uploadEntityFile(UploadedFile $file, CloudPlatform $cloudPlatform, $entity, CloudFolder $cloudFolder): array
    {
        $this->forPlatform($cloudPlatform);

        return DB::transaction(function () use ($file, $cloudPlatform, $entity, $cloudFolder) {

            $extension = strtolower($file->getClientOriginalExtension()); // Ex: jpg, png, mp4, zip, etc.

            // On s'assure que le type de contenu attendu par le dossier accepte cette extension
            // (la source de vérité reste le content_type du dossier, défini par l'architecture)
            $contentTypes = $cloudFolder->content_type ?? [];
            if (! empty($contentTypes) && ! in_array($extension, $contentTypes, true)) {
                throw new \InvalidArgumentException(
                    __("{$this->langPath}.extension_not_allowed", ['extension' => $extension, 'folder' => $cloudFolder->folder_name])
                );
            }

            // Type de fichier dérivé de manière centralisée (enum) — plus de mapping en dur
            $fileType = CloudFileType::fromExtension($extension);

            // Garde-fou MIME : le type MIME détecté doit être cohérent avec le type de fichier
            $mime = $file->getMimeType();
            if (! CloudFileType::matchesMime($fileType, $mime)) {
                throw new \InvalidArgumentException(
                    __("{$this->langPath}.mime_mismatch", ['extension' => $extension, 'type' => $fileType->value])
                );
            }

            $newName = $entity->uuid.'.'.$extension; // Ex: "blog_category_uuid.jpg" ou "user_uuid.mp4"
            $localDir = trim($cloudFolder->path, '/').'/'.$cloudFolder->folder_name; // Ex: "others/all/galleries/images"
            $path = $localDir.'/'.$newName; // Ex: "others/all/galleries/images/blog_category_uuid.jpg"

            // Déduplication : si un fichier avec la même empreinte existe déjà dans ce dossier,
            // on refuse le doublon. NB : le champ `hash` est volontairement laissé null
            // (aucune valeur n'est stockée en base), donc cette garde ne se déclenche
            // actuellement jamais ; elle est prête pour le jour où le hash sera renseigné.
            $hash = md5_file($file->getRealPath());
            $duplicate = CloudFile::where('cloud_folder_id', $cloudFolder->id)
                ->where('hash', $hash)
                ->exists();

            if ($duplicate) {
                throw new \InvalidArgumentException(
                    __("{$this->langPath}.duplicate_file", ['name' => $file->getClientOriginalName()])
                );
            }

            // 1. Upload LOCAL : quasi instantané
            $localPath = Storage::disk('local_uploads')->putFileAs($localDir, $file, $newName);

            // 2. Enregistrement en base avec statut "pending" (en attente de migration vers B2).
            //    `local_path` = chemin sur le serveur local, `remote_path` = chemin cible sur le cloud
            //    (identiques tant que le fichier n'est pas migré). `cloud_file_id` reste volontairement null.
            $data = [
                'cloud_folder_id' => $cloudFolder->id,
                'fileable_type' => get_class($entity),
                'fileable_id' => $entity->id,
                'cloud_file_id' => null, // Réservé : toujours null (voir remote_path)
                'remote_path' => $path, // Chemin du fichier sur le stockage cloud (B2)
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
                    'path' => $path, // chemin B2 cible pour la migration
                    'disk' => $cloudPlatform->disk_name,
                    'platform' => $cloudPlatform->name,
                ], // Tableau brut : le cast "array" du modèle sérialise en JSON à l'enregistrement
            ];

            return $data;
        });
    }

    /**
     * Migre un fichier local vers le cloud (B2).
     */
    public function migrateToCloud(CloudFile $cloudFile): bool
    {
        try {
            if (! $cloudFile->local_path || ! Storage::disk('local_uploads')->exists($cloudFile->local_path)) {
                // Fichier local introuvable : passer en "error" pour diagnostic
                $cloudFile->update([
                    'sync_status' => CloudFileStatus::Error,
                    'last_sync_attempt_at' => getDateTime(),
                ]);

                return false;
            }

            // Résoudre le driver à partir du CloudFile (via sa plateforme)
            $this->resolveDriverFromCloudFile($cloudFile);

            // Le metadata est normalement un array (via le cast du modèle).
            // Garde défensive pour d'anciennes lignes stockées en string JSON.
            $metadata = is_array($cloudFile->metadata) ? $cloudFile->metadata : (json_decode($cloudFile->metadata, true) ?? []);
            $targetPath = $metadata['path'] ?? $cloudFile->file_name; // Chemin cible sur B2 (ex: "blog_categories/uuid.jpg")

            // Ouvrir le fichier local en mode lecture (streaming — évite de charger tout le fichier en mémoire)
            $stream = Storage::disk('local_uploads')->readStream($cloudFile->local_path);

            if ($stream === false) {
                throw new \RuntimeException("Impossible d'ouvrir le fichier local : {$cloudFile->local_path}");
            }

            // Upload vers le cloud (B2) en streaming
            $uploaded = Storage::disk($this->platform->disk_name)->put($targetPath, $stream, [
                'ContentType' => $cloudFile->mime_type, // Définir le type MIME pour B2 Ex: image/jpeg, video/mp4, application/zip, etc.
            ]);

            // Fermer le stream après l'upload
            if (is_resource($stream)) {
                fclose($stream);
            }

            // Vérifier si l'upload a réussi
            if (! $uploaded) {

                // Échec de l'upload vers B2, mettre à jour le statut en "error"
                $cloudFile->update([
                    'sync_status' => CloudFileStatus::Error,
                    'last_sync_attempt_at' => getDateTime(),
                ]);

                // Log de l'erreur
                $this->logActivity(type: 'error', action: 'backend.cloud-files.migrate', context: ['uuid' => $cloudFile->uuid], message: __("{$this->langPath}.migrate_error", ['name' => $cloudFile->file_name]), is_success: false, loggable: $cloudFile);

                return false;
            }

            // Mise à jour de la ligne en base : remote_path devient le chemin effectif sur B2,
            // cloud_file_id reste null (champ réservé).
            $cloudFile->update([
                'remote_path' => $targetPath,
                'sync_status' => CloudFileStatus::Synced,
                'last_sync_attempt_at' => getDateTime(),
                'metadata' => array_merge($metadata, ['synced_at' => getDateTime()]),
            ]);

            // Suppression du fichier local après migration réussie
            Storage::disk('local_uploads')->delete($cloudFile->local_path);

            // Mettre à jour le champ local_path à null après suppression
            $cloudFile->update([
                'local_path' => null,
            ]);

            return true;

        } catch (\Exception $e) {
            report($e);
            $cloudFile->update([
                'sync_status' => CloudFileStatus::Error,
                'last_sync_attempt_at' => getDateTime(),
            ]);

            // Log de l'erreur
            $this->logActivity(type: 'error', action: 'backend.cloud-files.migrate', context: ['uuid' => $cloudFile->uuid], message: __("{$this->langPath}.migrate_error", ['name' => $cloudFile->file_name]), is_success: false, loggable: $cloudFile);

            return false;
        }
    }

    /**
     * Récupère l'URL publique d'un fichier cloud
     */
    public function url(CloudFile $cloudFile): string
    {
        // Résout le driver à partir du CloudFile (via sa plateforme)
        $this->resolveDriverFromCloudFile($cloudFile);

        // Retourne l'URL publique du fichier sur le cloud (B2)
        return $this->getDriver()->url($cloudFile->remote_path ?: $cloudFile->file_name);
    }

    /**
     * Récupère une URL temporairement signée
     */
    public function temporaryUrl(CloudFile $cloudFile, ?int $expiresInMinutes = null): ?string
    {
        try {
            // Durée de validité par défaut pilotée par le setting "cloudfiles.temporary_url_expires_in_minutes".
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

    /**
     * Supprime un fichier cloud (stockage + base)
     */
    public function delete(CloudFile $cloudFile): bool
    {
        try {
            // Si le fichier n'est pas encore migré, supprimer le fichier local uniquement
            if ($cloudFile->sync_status !== CloudFileStatus::Synced) {
                if ($cloudFile->local_path && Storage::disk('local_uploads')->exists($cloudFile->local_path)) {
                    Storage::disk('local_uploads')->delete($cloudFile->local_path);
                }

                return true;
            }

            $this->resolveDriverFromCloudFile($cloudFile);

            $deleted = $this->getDriver()->delete($cloudFile->remote_path);

            return $deleted;

        } catch (\Exception $e) {
            report($e);

            return false;
        }
    }

    /**
     * Vérifie si un fichier existe
     */
    public function exists(CloudFile $cloudFile): bool
    {
        $this->resolveDriverFromCloudFile($cloudFile);

        return $this->getDriver()->exists($cloudFile->remote_path);
    }

    /**
     * Télécharge un fichier
     */
    public function download(CloudFile $cloudFile)
    {
        $this->resolveDriverFromCloudFile($cloudFile);

        return $this->getDriver()->download($cloudFile->remote_path);
    }

    /**
     * Résout le driver à partir du CloudFile (via sa plateforme)
     */
    protected function resolveDriverFromCloudFile(CloudFile $cloudFile): void
    {
        if ($this->driver) {
            return;
        }

        // Pas de fallback silencieux : si la plateforme du fichier est introuvable,
        // on lève une exception plutôt que de servir/supprimer le fichier sur une
        // autre plateforme active (risque de corruption / fuite de données).
        $platform = $cloudFile->cloudFolder?->platform;

        if (! $platform) {
            throw new \RuntimeException(
                __("{$this->langPath}.platform_not_found", ['uuid' => $cloudFile->uuid])
            );
        }

        $this->forPlatform($platform);
    }

    /**
     * Retourne le driver ou lève une exception s'il n'est pas configuré
     */
    protected function getDriver(): CloudStorageInterface
    {
        if (! $this->driver) {
            throw new \RuntimeException(
                'Aucun driver de stockage configuré. Appelez forPlatform() ou setDriver() d\'abord.'
            );
        }

        return $this->driver;
    }

    /**
     * Délègue les appels de méthode au driver
     */
    public function __call(string $method, array $arguments)
    {
        return $this->getDriver()->{$method}(...$arguments);
    }
}
