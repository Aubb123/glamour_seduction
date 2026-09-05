<?php

namespace App\Services\V1\Controllers;

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
    public function destroy(RoleTranslation $roleTranslation): void
    {
        DB::transaction(function () use ($roleTranslation) {

            $role = $roleTranslation->role()->withTrashed()->first();

            if ($role) {
                $translationsCount = $role->translations()->count();

                if ($translationsCount <= 1) {
                    throw new \InvalidArgumentException(__("{$this->langPath}.destroy.last_translation_forbidden"));
                }
            }

            if ($roleTranslation->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.already_deleted"));
            }
            destroy_function($roleTranslation);
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function restore(RoleTranslation $roleTranslation): void
    {
        DB::transaction(function () use ($roleTranslation) {

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
    public function forceDestroy(RoleTranslation $roleTranslation): void
    {
        DB::transaction(function () use ($roleTranslation) {

            $role = $roleTranslation->role()->withTrashed()->first();

            if ($role) {
                $translationsCount = $role->translations()->withTrashed()->count();

                if ($translationsCount <= 1) {
                    throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.last_translation_forbidden"));
                }
            }

            if ($roleTranslation->trashed()) {
                force_destroy_function($roleTranslation);
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.not_trashed_forbidden"));
            }
        });
    }
}
