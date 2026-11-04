<?php

namespace App\Services\V1\Controllers;

use App\Enums\Roles\RoleStatus;
use App\Models\Role;
use App\Models\RoleTranslation;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

use function __;

class RoleService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/roles/messages/role-messages-service.roles';

    // Chemin des messages génériques du backend helper (factorisés pour toutes les entités traduisibles)
    protected string $backendHelperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_roles_activate(Role $role): void
    {
        DB::transaction(function () use ($role) {

            if ($role->status === RoleStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.activate.already_active"));
            }

            $role->update([
                'status' => RoleStatus::Active,
            ]);

            $this->flushPermissionCache();
        });
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_roles_deactivate(Role $role): void
    {
        DB::transaction(function () use ($role) {

            if ($role->status !== RoleStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.deactivate.already_inactive"));
            }

            $role->update([
                'status' => RoleStatus::Inactive,
            ]);

            $this->flushPermissionCache();
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_roles_update(Role $role, array $data): Role
    {
        return DB::transaction(function () use ($role, $data) {

            $role->update([
                // 'name'       => $data['name'], // Pas besoin de mettre à jour le nom interne (name) car il est principalement utilisé pour la gestion interne et les autorisations. Le nom interne est généralement défini lors de la création du rôle et ne change pas fréquemment.
                // 'guard_name' => $data['guard_name'] ?? $role->guard_name, // Pas besoin de mettre à jour le guard_name car il est généralement défini lors de la création du rôle et ne change pas fréquemment.
                'status' => ($data['status'] ?? null) === RoleStatus::Active->value
                ? RoleStatus::Active
                : RoleStatus::Inactive, // Défaut cohérent avec la migration (active)
            ]);

            // Synchronisation des traductions
            if (isset($data['translations'])) {
                $this->syncTranslations($role, $data['translations']);
            }

            // Purge du cache Spatie si le statut d'activation a changé
            if ($role->wasChanged('status')) {
                $this->flushPermissionCache();
            }

            return $role;
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

    // ─────────────────────────────────────────────
    // SYNC TRANSLATIONS (privé)
    // ─────────────────────────────────────────────
    private function syncTranslations(Role $role, array $translations): void
    {
        // Récupère les IDs des langues soumises
        $submittedLangIds = collect($translations)->pluck('lang_id')->toArray();

        // Supprime les traductions qui ne sont pas présentes dans les données soumises
        RoleTranslation::where('role_id', $role->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->delete();
        // Suppression définitive des traductions supprimées (soft delete) qui sont en corbeille, supprimer précédemment
        RoleTranslation::onlyTrashed()->where('role_id', $role->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->forceDelete();

        // Validation générique factorisée (BackendHelper) :
        //  - conflit label/slug avec un autre rôle (actif ou en corbeille) dans la même langue ;
        //  - conflit avec la propre traduction du rôle, placée en corbeille.
        // NB : la validation porte sur role_translations.label uniquement — la colonne
        // Spatie `name` (rôle technique) n'est jamais touchée.
        // Lève une InvalidArgumentException avec un message explicite si besoin.
        assertTranslationUniqueness(
            $role,
            $translations,
            __('v1/backend/others/helpers/backend-helper-messages.entityLabels.role'),
            $this->backendHelperLangPath,
            'label',
            'slug'
        );

        foreach ($translations as $translation) {

            // Vérifie si une traduction existe déjà pour le rôle et la langue
            $existingTranslation = RoleTranslation::withTrashed()
                ->where('role_id', $role->id)
                ->where('lang_id', $translation['lang_id'])
                ->first();

            // Si une traduction existante est trouvée
            if ($existingTranslation) {

                // Une traduction en corbeille ne doit pas être modifiée silencieusement :
                // elle doit être restaurée manuellement depuis la corbeille avant de pouvoir
                // être mise à jour (comportement cohérent avec les autres services).
                if ($existingTranslation->trashed()) {
                    throw new \InvalidArgumentException(
                        __("{$this->backendLangPath}.trashedTranslationConflict", [
                            'lang_id' => $translation['lang_id'],
                        ])
                    );
                }

                // Met à jour la traduction existante
                $existingTranslation->update([
                    'label' => $translation['label'],
                    'description' => $translation['description'] ?? null,
                ]);
            } else {
                // Sinon, crée une nouvelle traduction
                RoleTranslation::create([
                    'role_id' => $role->id,
                    'lang_id' => $translation['lang_id'],
                    'label' => $translation['label'],
                    'description' => $translation['description'] ?? null,
                ]);
            }
        }
    }
}
