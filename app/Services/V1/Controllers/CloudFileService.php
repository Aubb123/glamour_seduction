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

use function __;

class CloudFileService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/clouds/files/messages/cloud-files-messages-service.cloud-files';

    public function __construct(
        protected CloudStorageService $cloudStorage,
    ) {}

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_cloud_files_store($data): CloudFile
    {
        return DB::transaction(function () use ($data) {

            $cloudFile = CloudFile::create([
                'cloud_folder_id' => $data['cloud_folder_id'],
                'fileable_type' => $data['fileable_type'],
                'fileable_id' => $data['fileable_id'],
                'cloud_file_id' => null, // Réservé : toujours null (le chemin cloud est stocké dans remote_path)
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

            return $cloudFile;

        });
    }

    // ─────────────────────────────────────────────
    // FILES SYNC SELECTED
    // ─────────────────────────────────────────────
    public function backend_cloud_files_sync_selected(array $fileUuids): array
    {
        // Désactive la limite d'exécution pour permettre le traitement de plusieurs fichiers volumineux
        set_time_limit(0);

        // Si le tableau des UUIDs est vide, on retourne un tableau vide
        if (empty($fileUuids)) {
            return [
                'success_count' => 0,
                'failed_count' => 0,
            ];
        }

        // Vérifie si le nombre de fichiers sélectionnés dépasse la limite définie dans les settings
        $setting_max_files_sync_batch = getSettingValue('max_files_sync_batch', app()->getLocale());
        if (count($fileUuids) > $setting_max_files_sync_batch) {
            throw new \InvalidArgumentException(__("{$this->langPath}.sync.max_selection_exceeded", ['max' => $setting_max_files_sync_batch]));
        }

        // Récupère les fichiers sélectionnés éligibles à une migration (retries bornés) :
        // seuls les fichiers en "pending"/"error" dont la dernière tentative est absente
        // ou plus vieille que 10 minutes sont traités.
        $cloudFiles = CloudFile::whereIn('uuid', $fileUuids)
            ->retryable()
            ->get();

        $successCount = 0;
        $failedCount = 0;

        foreach ($cloudFiles as $cloudFile) {
            // Marquer comme "processing" avant migration
            $cloudFile->update([
                'sync_status' => CloudFileStatus::Processing,
                'last_sync_attempt_at' => getDateTime(),
            ]);

            if ($this->cloudStorage->migrateToCloud($cloudFile)) {
                $successCount++;
            } else {
                $failedCount++;
            }
        }

        return [
            'success_count' => $successCount,
            'failed_count' => $failedCount,
        ];
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_cloud_files_destroy(CloudFile $cloudFile): void
    {
        DB::transaction(function () use ($cloudFile) {

            // Vérifie si le fichier cloud est en cours de traitement
            if ($cloudFile->sync_status === CloudFileStatus::Processing) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.processing_forbidden"));
            }

            // Si le fichier cloud est lié à un modele (fileable), empêcher la suppression
            if ($cloudFile->fileable) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.no_delete_forbidden"));
            }

            // Vérifie si le fichier cloud est déjà supprimé
            if ($cloudFile->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.already_deleted"));
            }

            destroy_function($cloudFile);
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_cloud_files_restore(CloudFile $cloudFile): void
    {
        DB::transaction(function () use ($cloudFile) {

            // Récupère le model/entité auquel le fichier cloud est lié (fileable), meme si le model/entité est supprimé (soft deleted)
            $fileable = $cloudFile->fileable()->withTrashed()->first();

            // Si le modèle/entité auquel le fichier cloud est lié (fileable) est introuvable
            // (y compris dans la corbeille) OU est lui-même encore supprimé (soft deleted),
            // on ne peut pas restaurer le fichier cloud seul : il n'aurait plus d'entité de référence.
            if (! $fileable || $fileable->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.restore.fileable_not_found"));
            }

            // Vérifie si le fichier cloud est déjà supprimé (soft deleted)
            if ($cloudFile->trashed()) {
                restore_function($cloudFile);
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.restore.not_trashed_forbidden"));
            }
        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_cloud_files_force_destroy(CloudFile $cloudFile): void
    {
        DB::transaction(function () use ($cloudFile) {

            // Récupère le model/entité auquel le fichier cloud est lié (fileable), meme si le model/entité est supprimé (soft deleted)
            $fileable = $cloudFile->fileable()->withTrashed()->first();

            // Si le model/entité auquel le fichier cloud est lié (fileable) n'existe plus, c'est-à-dire qu'il a été supprimé temporairement,
            // on ne peut pas restaurer le fichier cloud, car il n'y a plus de référence pour le lier à un model/entité.
            // On peut donc lever une exception pour indiquer que seule la suppression définitive depuis le model/entité
            // est possible, mais pas depuis le fichier cloud lui-même.
            if ($fileable && $fileable->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.fileable_not_found"));
            }

            // Vérifie si le fichier cloud est en cours de traitement
            if ($cloudFile->sync_status === CloudFileStatus::Processing) {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.processing_forbidden"));
            }

            // Si le fichier cloud est lié à un modele (fileable), empêcher la suppression définitive
            if ($cloudFile->fileable) {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.no_delete_forbidden"));
            }

            // Vérifie si le fichier cloud est déjà supprimé (soft deleted)
            if ($cloudFile->trashed()) {
                force_destroy_function($cloudFile);
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.not_trashed_forbidden"));
            }
        });
    }

    // ─────────────────────────────────────────────
    // UPLOAD ENTITY IMAGE (mutualisé store + update)
    // ─────────────────────────────────────────────
    /**
     * Upload une image pour une entité (BlogCategory, etc.) et l'associe en base.
     *
     * @param  UploadedFile  $file  Le fichier image uploadé
     * @param  string  $cloudPlatformUuid  UUID de la plateforme cloud
     * @param  Model  $entity  L'entité à laquelle associer l'image (BlogCategory, etc.)
     * @param  string  $entityTable  Nom de la table de l'entité (ex: blog_categories)
     * @param  string  $langPath  Chemin de langue pour les messages d'erreur (ex: $this->langPath)
     *
     * @throws \InvalidArgumentException
     */
    public function uploadEntityImage(UploadedFile $file, string $cloudPlatformUuid, Model $entity, string $entityTable, string $langPath): CloudFile
    {

        // Supprimer tous les anciens fichiers cloud de type 'image' associés à l'entité
        deleteOldCloudFiles($entity, 'image');

        $cloudPlatform = CloudPlatform::where('uuid', $cloudPlatformUuid)->firstOrFail();

        if ($cloudPlatform->status !== CloudPlatformStatus::Active) {
            throw new \InvalidArgumentException(__("{$langPath}.store.cloud_platform_inactive"));
        }

        if ($cloudPlatform->name !== 'backblaze_b2') {
            throw new \InvalidArgumentException(__("{$langPath}.store.cloud_platform_not_supported"));
        }

        $extension = strtolower($file->getClientOriginalExtension());
        $table = $entityTable;

        // Recherche du dossier correspondant à l'entité, au type de contenu (image)
        // et rattaché à la même plateforme (scope obligatoire pour gérer le multi-plateforme)
        // On ne prend que les dossiers actifs pour éviter de stocker des fichiers dans des dossiers désactivés
        $cloudFolder = CloudFolder::query()
            ->where('cloud_platform_id', $cloudPlatform->id)
            ->where('entity_table', $table)
            ->where('status', CloudFolderStatus::Active->value) // On ne prend que les dossiers actifs pour éviter de stocker des fichiers dans des dossiers désactivés
            ->whereJsonContains('content_type', $extension)
            ->first();

        if (! $cloudFolder) {
            throw new \InvalidArgumentException(__("{$langPath}.store.cloud_platform_folder_not_found"));
        }

        // Upload du fichier en local et création de l'enregistrement en base (statut "pending")
        // — la migration vers B2 est asynchrone
        $data = $this->cloudStorage->uploadEntityFile($file, $cloudPlatform, $entity, $cloudFolder);

        // Création de l'enregistrement CloudFile en base
        $cloudFile = $this->backend_cloud_files_store($data);

        // Upload local + enregistrement en base (statut "pending") — la migration vers B2 est asynchrone
        return $cloudFile;
    }
}
