<?php

namespace App\Services\V1\Controllers;

use App\Enums\Clouds\Folders\CloudFolderStatus;
use App\Models\CloudFolder;
use App\Models\CloudPlatform;
use Illuminate\Support\Facades\DB;

class CloudFolderService
{
    protected string $backendLangPath = 'v1/backend/views/pages/clouds/folders/messages/cloud-folders-messages-service.cloud-folders';

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_cloud_folders_store($data): CloudFolder
    {
        return DB::transaction(function () use ($data) {

            $cloudFolder = CloudFolder::create([
                'cloud_platform_id' => $data['cloud_platform_id'],
                'cloud_folder_id' => $data['cloud_folder_id'] ?? null,
                'folder_name' => $data['folder_name'],
                'folder_id' => $data['folder_id'] ?? null,
                'path' => $data['path'] ?? null,
                'entity_table' => $data['entity_table'] ?? null,
                'content_type' => $data['content_type'] ?? [],
                'metadata' => $data['metadata'] ?? null,
                'status' => ($data['status'] ?? null) === CloudFolderStatus::Active->value
                    ? CloudFolderStatus::Active
                    : CloudFolderStatus::Inactive,
            ]);

            return $cloudFolder;
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_cloud_folders_update(CloudFolder $folder, array $data): CloudFolder
    {
        return DB::transaction(function () use ($folder, $data) {

            $updateData = [];

            // Les champs édités via l'UI (folder_name, entity_table, content_type, metadata)
            foreach (['folder_name', 'entity_table', 'content_type', 'metadata'] as $field) {
                if (array_key_exists($field, $data)) {
                    $updateData[$field] = $data[$field];
                }
            }

            $updateData['status'] = ($data['status'] ?? null) === CloudFolderStatus::Active->value
                ? CloudFolderStatus::Active
                : CloudFolderStatus::Inactive;

            if (! empty($updateData)) {
                $folder->update($updateData);
            }

            return $folder;
        });
    }

    // ─────────────────────────────────────────────
    // TOGGLE ACTIVE
    // ─────────────────────────────────────────────
    public function backend_cloud_folders_toggle_active(CloudFolder $folder): void
    {
        DB::transaction(function () use ($folder) {
            $folder->update([
                // Inverse le statut actuel (active ↔ inactive)
                'status' => $folder->status === CloudFolderStatus::Active
                    ? CloudFolderStatus::Inactive
                    : CloudFolderStatus::Active,
            ]);
        });
    }

    // ─────────────────────────────────────────────
    // CRÉER LES DOSSIERS À PARTIR DE L'ARCHITECTURE
    // ─────────────────────────────────────────────
    /**
     * Crée les dossiers dans cloud_folders de manière hiérarchique
     * à partir de l'architecture définie dans getArchitectureCloud()['architecture'].
     *
     * @return int Nombre de dossiers créés
     */
    public function createFoldersFromArchitecture(CloudPlatform $cloudPlatform, array $architecture): int
    {
        return DB::transaction(function () use ($cloudPlatform, $architecture) {

            $createdCount = 0;

            foreach ($architecture as $group => $paths) {
                foreach ($paths as $subfolder) {
                    $segments = explode('/', $subfolder);
                    $currentPath = '';
                    $parentId = null;

                    foreach ($segments as $folderName) {
                        // ✅ On garde le path AVANT d'ajouter le segment courant
                        $parentPath = $currentPath;

                        // Puis on met à jour le path complet pour la prochaine itération
                        $currentPath = $currentPath === '' ? $folderName : $currentPath.'/'.$folderName;

                        $folder = CloudFolder::firstOrCreate(
                            [
                                'cloud_platform_id' => $cloudPlatform->id,
                                'folder_name' => $folderName,
                                'cloud_folder_id' => $parentId,
                            ],
                            [
                                'path' => $parentPath, // ✅ chemin du PARENT, pas du dossier lui-même
                                'status' => CloudFolderStatus::Active->value,
                            ]
                        );

                        if ($folder->wasRecentlyCreated) {
                            $createdCount++;
                        }

                        $parentId = $folder->id;
                    }
                }
            }

            return $createdCount;
        });
    }

    // ─────────────────────────────────────────────
    // MARQUER LES DOSSIERS AVEC CONTENT_TYPE ET EMPLACEMENT
    // ─────────────────────────────────────────────
    /**
     * Met à jour ou crée les dossiers spécifiques avec leurs content_type et entity_table.
     *
     * @return int Nombre de dossiers mis à jour
     */
    public function markFoldersWithContentType(CloudPlatform $cloudPlatform, array $foldersToUpdate): int
    {
        return DB::transaction(function () use ($cloudPlatform, $foldersToUpdate) {

            $updatedCount = 0;

            foreach ($foldersToUpdate as $folderData) {
                $folder = CloudFolder::updateOrCreate(
                    [
                        'cloud_platform_id' => $cloudPlatform->id,
                        'folder_name' => $folderData['folder_name'],
                        'path' => $folderData['path'] ?? null,
                    ],
                    [
                        'entity_table' => $folderData['entity_table'] ?? null,
                        'content_type' => $folderData['content_type'] ?? [],
                        'metadata' => [
                            'disk' => $cloudPlatform->disk_name,
                            'platform' => $cloudPlatform->name,
                        ],
                        'status' => CloudFolderStatus::Active->value,
                    ]
                );

                if (! $folder->wasRecentlyCreated) {
                    $updatedCount++;
                }
            }

            return $updatedCount;
        });
    }
}
