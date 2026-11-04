<?php

namespace App\Services\V1\Controllers;

use App\Models\Role;
use App\Models\RoleTranslation;
use Illuminate\Support\Facades\DB;

use function __;

class RoleTranslationService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/roles/messages/role-messages-service.roles.translations';

    // Chemin des messages génériques du backend helper (factorisés pour toutes les entités traduisibles)
    protected string $helperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_roles_translations_destroy(RoleTranslation $roleTranslation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($roleTranslation) {

            // 1. Verrou sur le rôle (même soft-deleted)
            $role = Role::withTrashed()
                ->lockForUpdate()
                ->find($roleTranslation->role_id);

            if (! $role) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.role_not_found"));
            }

            // 2. Relecture verrouillée de la traduction
            $roleTranslation = RoleTranslation::withTrashed()
                ->lockForUpdate()
                ->find($roleTranslation->id);

            if (! $roleTranslation) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.translation_not_found"));
            }

            // 3. Déjà en corbeille
            if ($roleTranslation->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.already_deleted"));
            }

            // 4. Dernière traduction active
            if ($role->translations()->count() <= 1) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.last_translation_forbidden"));
            }

            // 5. Suppression
            destroy_function($roleTranslation);
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_roles_translations_restore(RoleTranslation $roleTranslation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($roleTranslation) {

            // Restauration simple (restore)
            if ($roleTranslation->trashed()) {

                // Validation générique factorisée (BackendHelper) : refuse de restaurer
                // une traduction dont le label/slug entre en conflit avec une autre traduction
                // (active ou en corbeille) dans la même langue.
                assertTranslationRestore(
                    $roleTranslation,
                    __('v1/backend/others/helpers/backend-helper-messages.entityLabels.role'),
                    $this->helperLangPath,
                    'label',
                    'slug'
                );

                restore_function($roleTranslation);
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.restore.not_trashed_forbidden"));
            }

        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_roles_translations_force_destroy(RoleTranslation $roleTranslation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($roleTranslation) {

            // 1. Verrou sur le rôle (même soft-deleted), toujours en premier
            //    pour garder le même ordre de verrouillage que destroy() et syncTranslations()
            $role = Role::withTrashed()
                ->lockForUpdate()
                ->find($roleTranslation->role_id);

            if (! $role) {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.role_not_found"));
            }

            // 2. Relecture verrouillée de la traduction (le binding peut être périmé)
            $roleTranslation = RoleTranslation::withTrashed()
                ->lockForUpdate()
                ->find($roleTranslation->id);

            if (! $roleTranslation) {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.translation_not_found"));
            }

            // 3. Seule une traduction en corbeille peut être supprimée définitivement
            if (! $roleTranslation->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.not_trashed_forbidden"));
            }

            // 4. Interdit de supprimer la dernière traduction du rôle (corbeille incluse)
            if ($role->translations()->withTrashed()->count() <= 1) {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.last_translation_forbidden"));
            }

            // 5. Suppression définitive de la traduction et de ses dépendances
            force_destroy_function($roleTranslation);
        });
    }
}
