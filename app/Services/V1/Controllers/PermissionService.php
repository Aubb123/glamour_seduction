<?php

namespace App\Services\V1\Controllers;

use App\Enums\Permissions\PermissionStatus;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class PermissionService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/permissions/messages/permission-messages-service.permissions';

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_permissions_store(array $data): Permission
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        return DB::transaction(function () use ($data) {

            // Création de la permission avec les données fournies
            $permission = Permission::create([
                'name' => $data['name'],
                'guard_name' => $data['guard_name'] ?? 'web',
                'status' => ($data['status'] ?? null) === PermissionStatus::Active->value
                    ? PermissionStatus::Active
                    : PermissionStatus::Inactive,
            ]);

            // Synchronisation des rôles associés si présents
            if (isset($data['roles']) && ! empty($data['roles'])) {
                $permission->roles()->sync($data['roles']);
            }

            // Purge du cache Spatie (le sync Eloquent direct ne déclenche pas la purge automatique)
            $this->flushPermissionCache();

            return $permission; // ← retourne la permission créée
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_permissions_update(Permission $permission, array $data): Permission
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        return DB::transaction(function () use ($permission, $data) {

            // Empêche le renommage si la permission est déjà attribuée à des rôles ou utilisateurs
            if (isset($data['name']) && $data['name'] !== $permission->name) {
                if ($permission->roles()->count() > 0 || $permission->users()->count() > 0) {
                    throw new \InvalidArgumentException(__("{$this->backendLangPath}.update.cannot_rename_used"));
                }
            }

            // Préparation des données de mise à jour
            $updateData = [];
            $updateData['status'] = ($data['status'] ?? null) === PermissionStatus::Active->value
                ? PermissionStatus::Active
                : PermissionStatus::Inactive;

            if (isset($data['name'])) {
                $updateData['name'] = $data['name'];
            }

            if (isset($data['guard_name'])) {
                $updateData['guard_name'] = $data['guard_name'];
            }

            // Application de la mise à jour si des données sont présentes
            if (! empty($updateData)) {
                $permission->update($updateData);
            }

            // Synchronisation des rôles associés
            $permission->roles()->sync($data['roles'] ?? []);

            // Purge du cache Spatie (pivot et status modifiés ; le sync Eloquent
            // direct ne déclenche pas la purge automatique du registrar)
            $this->flushPermissionCache();

            return $permission; // ← retourne la permission mise à jour
        });
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_permissions_activate(Permission $permission): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($permission) {

            // Vérification que la permission n'est pas déjà active
            if ($permission->status === PermissionStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.activate.already_active"));
            }

            // Activation de la permission
            $permission->update(['status' => PermissionStatus::Active]);

            // Purge du cache Spatie
            $this->flushPermissionCache();
        });
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_permissions_deactivate(Permission $permission): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($permission) {

            // Vérification que la permission n'est pas déjà inactive
            if ($permission->status !== PermissionStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.deactivate.already_inactive"));
            }

            // Désactivation de la permission
            $permission->update(['status' => PermissionStatus::Inactive]);

            // Purge du cache Spatie
            $this->flushPermissionCache();
        });
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_permissions_destroy(Permission $permission): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($permission) {

            // Une permission active ne peut pas être supprimée
            if ($permission->status === PermissionStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.active_forbidden"));
            }

            // NB : les rattachements aux rôles/utilisateurs passent par des tables pivots
            // (role_has_permissions / model_has_permissions) : par règle métier, les liens
            // N-N ne bloquent plus la suppression (nettoyés en cascade par la base).
            // Le verrou générique BackendHelper::assertNoDependentChildren s'applique ici.

            // Suppression simple (soft delete)
            if ($permission->trashed()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.already_deleted"));
            }
            destroy_function($permission); // ← suppression de la permission et nettoyage des fichiers associés si besoin
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_permissions_restore(Permission $permission): Permission
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        return DB::transaction(function () use ($permission) {

            // Vérification que la permission est bien supprimée
            if (! $permission->trashed()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.restore.not_deleted"));
            }

            // Restauration simple (restore)
            restore_function($permission); // ← restauration de la permission

            return $permission; // ← retourne la permission restaurée
        });
    }

    // ─────────────────────────────────────────────
    // SYNC ROLES
    // ─────────────────────────────────────────────
    public function backend_permissions_sync_roles(Permission $permission, array $roleIds): Permission
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        return DB::transaction(function () use ($permission, $roleIds) {

            // Synchronisation des rôles associés à la permission
            $permission->roles()->sync($roleIds);

            // Purge du cache Spatie (le sync Eloquent direct ne déclenche pas la purge automatique)
            $this->flushPermissionCache();

            return $permission; // ← retourne la permission avec ses rôles synchronisés
        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_permissions_force_destroy(Permission $permission): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($permission) {

            // NB : les rattachements aux rôles/utilisateurs passent par des tables pivots
            // (role_has_permissions / model_has_permissions) : par règle métier, les liens
            // N-N ne bloquent plus la suppression (nettoyés en cascade par la base).
            // Le verrou générique BackendHelper::assertNoDependentChildren s'applique ici.

            // Suppression définitive (forceDelete)
            if ($permission->trashed()) {
                // Une entité active ne peut pas être supprimée définitivement
                if ($permission->status === PermissionStatus::Active) {
                    throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.active_forbidden"));
                }
                force_destroy_function($permission); // ← suppression définitive de la permission et nettoyage des fichiers associés si besoin
            } else {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.not_trashed_forbidden"));
            }
        });
    }

    // ─────────────────────────────────────────────
    // PURGE DU CACHE SPATIE (privé)
    // ─────────────────────────────────────────────
    // Le cache Spatie stocke les permissions/rôles résolus par nom : sans purge,
    // un changement d'status (ou de pivot) ne prendrait effet qu'au prochain
    // cycle d'expiration du cache.
    private function flushPermissionCache(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
