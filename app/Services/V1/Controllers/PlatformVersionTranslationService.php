<?php

namespace App\Services\V1\Controllers;

use App\Helpers\Web\Backend\BackendHelper;
use App\Models\PlatformVersionTranslation;
use Illuminate\Support\Facades\DB;

use function __;

class PlatformVersionTranslationService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/platform-versions/messages/platform-messages-service.versions.translations';

    // Chemin des messages génériques du backend helper (factorisés pour toutes les entités traduisibles)
    protected string $helperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_platform_version_translations_destroy(PlatformVersionTranslation $platformVersionTranslation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($platformVersionTranslation) {

            // Récupération de la version associée à la traduction (même si elle est soft-deleted)
            $version = $platformVersionTranslation->platformVersion()->withTrashed()->first();

            // Empêche la suppression de la traduction si c'est la dernière traduction restante de la version (même si la version est soft-deleted)
            if ($version) {

                // Compte le nombre de traductions associées à la version, y compris les traductions soft-deleted
                // $translationsCount = $version->translations()->withTrashed()->count();

                // Compte le nombre de traductions associées à la version, en excluant les traductions soft-deleted
                $translationsCount = $version->translations()->count();

                if ($translationsCount <= 1) {
                    // Empêche la suppression de la traduction si c'est la dernière traduction restante de la version
                    // (même si la version est soft-deleted)
                    throw new \InvalidArgumentException(__("{$this->langPath}.destroy.last_translation_forbidden"));
                }
            } else {
                // Empêche la suppression de la traduction si la version associée n'existe pas (même si elle est soft-deleted)
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.version_not_found"));
            }

            // Suppression simple (deleted_at)
            if ($platformVersionTranslation->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.already_deleted"));
            }

            destroy_function($platformVersionTranslation); // ← suppression de la traduction et de ses dépendances associées + gestion de suppression des fichiers associés si besoin

        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_platform_version_translations_restore(PlatformVersionTranslation $platformVersionTranslation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($platformVersionTranslation) {

            // Restauration simple (restore)
            if ($platformVersionTranslation->trashed()) {

                // Validation générique factorisée (BackendHelper) : refuse de restaurer
                // une traduction dont le nom/slug entre en conflit avec une autre traduction
                // (active ou en corbeille) dans la même langue.
                BackendHelper::assertTranslationRestore(
                    $platformVersionTranslation,
                    __('v1/backend/others/helpers/backend-helper-messages.entityLabels.version'),
                    $this->helperLangPath,
                    'name',
                    'slug'
                );

                // Check métier : aucune autre traduction active ne doit exister pour ce
                // couple (version, langue), sinon la restauration violerait la contrainte unique.
                $langConflict = PlatformVersionTranslation::where('platform_version_id', $platformVersionTranslation->platform_version_id)
                    ->where('lang_id', $platformVersionTranslation->lang_id)
                    ->whereNull('deleted_at')
                    ->where('id', '!=', $platformVersionTranslation->id)
                    ->exists();

                if ($langConflict) {
                    throw new \InvalidArgumentException(__("{$this->langPath}.restore.lang_conflict"));
                }

                restore_function($platformVersionTranslation);
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.restore.not_trashed_forbidden"));
            }

        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_platform_version_translations_force_destroy(PlatformVersionTranslation $platformVersionTranslation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($platformVersionTranslation) {

            // Empêche la suppression définitive de la traduction si la traduction est la dernière traduction restante de la version (même si la version est soft-deleted)
            $version = $platformVersionTranslation->platformVersion()->withTrashed()->first();

            if ($version) {

                // Compte le nombre de traductions associées à la version, y compris les traductions soft-deleted
                $translationsCount = $version->translations()->withTrashed()->count();

                if ($translationsCount <= 1) {
                    throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.last_translation_forbidden"));
                }
            } else {
                // Empêche la suppression définitive de la traduction si la version associée n'existe pas (même si elle est soft-deleted)
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.version_not_found"));
            }

            // Suppression définitive (forceDelete)
            if ($platformVersionTranslation->trashed()) {
                force_destroy_function($platformVersionTranslation); // <- suppression définitive de la traduction de la version sélectionnée
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.not_trashed_forbidden"));
            }

        });
    }
}
