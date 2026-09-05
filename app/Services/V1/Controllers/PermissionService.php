<?php

namespace App\Services\V1\Controllers;

use App\Enums\Permissions\PermissionStatus;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class PermissionService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/permissions/messages/permission-messages-service.permissions';

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function store(array $data): Permission
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
    public function update(Permission $permission, array $data): Permission
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        return DB::transaction(function () use ($permission, $data) {

            // Empêche le renommage si la permission est déjà attribuée à des rôles ou utilisateurs
            if (isset($data['name']) && $data['name'] !== $permission->name) {
                if ($permission->roles()->count() > 0 || $permission->users()->count() > 0) {
                    throw new \InvalidArgumentException(__("{$this->langPath}.update.cannot_rename_used"));
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
    public function activate(Permission $permission): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($permission) {

            // Vérification que la permission n'est pas déjà active
            if ($permission->status === PermissionStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->langPath}.activate.already_active"));
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
    public function deactivate(Permission $permission): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($permission) {

            // Vérification que la permission n'est pas déjà inactive
            if ($permission->status !== PermissionStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->langPath}.deactivate.already_inactive"));
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
    public function destroy(Permission $permission): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($permission) {

            // Une permission active ne peut pas être supprimée
            if ($permission->status === PermissionStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.active_forbidden"));
            }

            // NB : les rattachements aux rôles/utilisateurs passent par des tables pivots
            // (role_has_permissions / model_has_permissions) : par règle métier, les liens
            // N-N ne bloquent plus la suppression (nettoyés en cascade par la base).
            // Le verrou générique BackendHelper::assertNoDependentChildren s'applique ici.

            // Suppression simple (soft delete)
            if ($permission->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.already_deleted"));
            }
            destroy_function($permission); // ← suppression de la permission et nettoyage des fichiers associés si besoin
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function restore(string $uuid): Permission
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        return DB::transaction(function () use ($uuid) {

            // Récupération de la permission (y compris les supprimées) via son uuid
            $permission = Permission::withTrashed()->where('uuid', $uuid)->firstOrFail();

            // Vérification que la permission est bien supprimée
            if (! $permission->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.restore.not_deleted"));
            }

            // Restauration simple (restore)
            if ($permission->trashed()) {
                restore_function($permission); // ← restauration de la permission
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.restore.not_trashed_forbidden"));
            }

            return $permission; // ← retourne la permission restaurée
        });
    }

    // ─────────────────────────────────────────────
    // SYNC ROLES
    // ─────────────────────────────────────────────
    public function syncRoles(Permission $permission, array $roleIds): Permission
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
    public function forceDestroy(string $uuid): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($uuid) {

            // Récupération de la permission (y compris les supprimées) via son uuid
            $permission = Permission::withTrashed()->where('uuid', $uuid)->firstOrFail();

            // NB : les rattachements aux rôles/utilisateurs passent par des tables pivots
            // (role_has_permissions / model_has_permissions) : par règle métier, les liens
            // N-N ne bloquent plus la suppression (nettoyés en cascade par la base).
            // Le verrou générique BackendHelper::assertNoDependentChildren s'applique ici.

            // Suppression définitive (forceDelete)
            if ($permission->trashed()) {
                force_destroy_function($permission); // ← suppression définitive de la permission et nettoyage des fichiers associés si besoin
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.not_trashed_forbidden"));
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
