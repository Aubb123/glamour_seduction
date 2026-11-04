<?php

namespace App\Services\V1\Controllers;

use App\Enums\Clouds\Platforms\CloudPlatformName;
use App\Enums\Clouds\Platforms\CloudPlatformStatus;
use App\Models\CloudPlatform;
use App\Services\Cloud\CloudStorageService;
use App\Traits\LogActivity;
use Illuminate\Support\Facades\DB;

use function __;

class CloudPlatformService
{
    use LogActivity;

    protected string $langPath = 'v1/backend/views/pages/clouds/platforms/messages/cloud-platforms-messages-service.cloud-platforms';

    public function __construct(
        protected CloudStorageService $cloudStorage,
        protected CloudFolderService $cloudFolderService,
    ) {}

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_cloud_platforms_store(array $data): CloudPlatform
    {
        return DB::transaction(function () use ($data) {

            if (isset($data['name'])) {
                $validNames = CloudPlatformName::values();
                if (! in_array($data['name'], $validNames)) {
                    throw new \InvalidArgumentException(__("{$this->langPath}.store.invalid_name"));
                }
            }

            $platform = CloudPlatform::create([
                'name' => $data['name'],
                'label' => $data['label'],
                'disk_name' => $data['disk_name'],
                'api_endpoint' => $data['api_endpoint'],
                'api_key' => $data['api_key'] ?? null,
                'api_secret' => $data['api_secret'] ?? null,
                'description' => $data['description'] ?? null,
                'metadata' => $data['metadata'] ?? null,
                'status' => ($data['status'] ?? null) === CloudPlatformStatus::Active->value
                    ? CloudPlatformStatus::Active
                    : CloudPlatformStatus::Inactive,
            ]);

            return $platform;
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_cloud_platforms_update(CloudPlatform $platform, array $data): CloudPlatform
    {
        return DB::transaction(function () use ($platform, $data) {

            if (isset($data['name'])) {
                $validNames = CloudPlatformName::values();
                if (! in_array($data['name'], $validNames)) {
                    throw new \InvalidArgumentException(__("{$this->langPath}.update.invalid_name"));
                }
            }

            $updateData = [];

            if (isset($data['name'])) {
                $updateData['name'] = $data['name'];
            }

            if (isset($data['label'])) {
                $updateData['label'] = $data['label'];
            }

            if (isset($data['api_endpoint'])) {
                $updateData['api_endpoint'] = $data['api_endpoint'];
            }

            if (isset($data['api_key'])) {
                $updateData['api_key'] = $data['api_key'];
            }

            if (isset($data['api_secret'])) {
                $updateData['api_secret'] = $data['api_secret'];
            }

            if (isset($data['description'])) {
                $updateData['description'] = $data['description'];
            }

            if (isset($data['metadata'])) {
                $updateData['metadata'] = $data['metadata'];
            }

            $updateData['status'] = ($data['status'] ?? null) === CloudPlatformStatus::Active->value
                ? CloudPlatformStatus::Active
                : CloudPlatformStatus::Inactive;

            if (! empty($updateData)) {
                $platform->update($updateData);
            }

            return $platform;
        });
    }

    // ─────────────────────────────────────────────
    // TOGGLE ACTIVE
    // ─────────────────────────────────────────────
    public function backend_cloud_platforms_toggle_active(CloudPlatform $platform): void
    {
        DB::transaction(function () use ($platform) {
            $platform->update([
                // Inverse le statut actuel (active ↔ inactive)
                'status' => $platform->status === CloudPlatformStatus::Active
                    ? CloudPlatformStatus::Inactive
                    : CloudPlatformStatus::Active,
            ]);
        });
    }

    // ─────────────────────────────────────────────
    // DESTROY (soft delete)
    // ─────────────────────────────────────────────
    public function backend_cloud_platforms_destroy(CloudPlatform $platform): void
    {
        DB::transaction(function () use ($platform) {

            // Redondance suppression : le verrou générique BackendHelper::assertNoDependentChildren
            // (via $dependentRelations = ['folders']) gère le blocage si des dossiers existent.
            if ($platform->status === CloudPlatformStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.status_active_forbidden"));
            }

            if ($platform->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.already_deleted"));
            }
            destroy_function($platform);
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_cloud_platforms_restore(CloudPlatform $platform): void
    {
        DB::transaction(function () use ($platform) {
            if ($platform->trashed()) {
                restore_function($platform);
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.restore.not_trashed_forbidden"));
            }
        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_cloud_platforms_force_destroy(CloudPlatform $platform): void
    {
        DB::transaction(function () use ($platform) {

            // Redondance suppression : le verrou générique BackendHelper::assertNoDependentChildren
            // (via $dependentRelations = ['folders']) gère le blocage si des dossiers existent.
            if ($platform->status === CloudPlatformStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.status_active_forbidden"));
            }

            if ($platform->trashed()) {
                force_destroy_function($platform);
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.not_trashed_forbidden"));
            }
        });
    }

    // ─────────────────────────────────────────────
    // ARCHITECTURE STORE
    // ─────────────────────────────────────────────
    public function backend_cloud_platforms_architecture_store(CloudPlatform $cloudPlatform): void
    {
        DB::transaction(function () use ($cloudPlatform) {

            if ($cloudPlatform->status !== CloudPlatformStatus::Active) {
                $this->logActivity(type: 'error', action: 'backend_cloud_platforms_architecture_store', context: ['platform' => $cloudPlatform->name], message: __("{$this->langPath}.architecture_store.inactive_platform", ['name' => $cloudPlatform->name]), activity: __("{$this->langPath}.architecture_store.error"), is_success: false, loggable: $cloudPlatform);
                throw new \InvalidArgumentException(__("{$this->langPath}.architecture_store.inactive_platform", ['name' => $cloudPlatform->name]));
            }

            $getArchitectureCloud = getArchitectureCloud();
            $architecture = $getArchitectureCloud['architecture'] ?? [];
            $foldersToUpdate = $getArchitectureCloud['foldersToUpdate'] ?? [];

            // 1. Création des dossiers dans cloud_folders à partir de l'architecture
            $createdCount = $this->cloudFolderService->createFoldersFromArchitecture($cloudPlatform, $architecture);

            // 2. Marquage des dossiers spécifiques (content_type, entity_table)
            $updatedCount = $this->cloudFolderService->markFoldersWithContentType($cloudPlatform, $foldersToUpdate);

            $this->logActivity(type: 'info', action: 'backend_cloud_platforms_architecture_store', context: ['platform' => $cloudPlatform->name, 'created' => $createdCount, 'updated' => $updatedCount], message: __("{$this->langPath}.architecture_store.success", ['name' => $cloudPlatform->name]), activity: __("{$this->langPath}.architecture_store.success", ['name' => $cloudPlatform->name]), is_success: true, loggable: $cloudPlatform);
        });
    }
}
