<?php

namespace App\Services\V1\Controllers;

use App\Models\GeoDivisionType;
use App\Models\GeoDivisionTypeTranslation;
use Illuminate\Support\Facades\DB;

use function __;

class GeoDivisionTypeTranslationService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/geos/division-types/messages/division-types-messages-service.divisionTypes.translations';

    // Chemin des messages génériques du backend helper (factorisés pour toutes les entités traduisibles)
    protected string $backendHelperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_geodivisiontypes_translations_destroy(GeoDivisionTypeTranslation $geoDivisionTypeTranslation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($geoDivisionTypeTranslation) {

            // 1. Verrou sur le type de division (même soft-deleted)
            $geoDivisionType = GeoDivisionType::withTrashed()
                ->lockForUpdate()
                ->find($geoDivisionTypeTranslation->division_type_id);

            if (! $geoDivisionType) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.division_type_not_found"));
            }

            // 2. Relecture verrouillée de la traduction
            $geoDivisionTypeTranslation = GeoDivisionTypeTranslation::withTrashed()
                ->lockForUpdate()
                ->find($geoDivisionTypeTranslation->id);

            if (! $geoDivisionTypeTranslation) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.translation_not_found"));
            }

            // 3. Déjà en corbeille
            if ($geoDivisionTypeTranslation->trashed()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.already_deleted"));
            }

            // 4. Dernière traduction active
            if ($geoDivisionType->translations()->count() <= 1) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.last_translation_forbidden"));
            }

            // 5. Suppression
            destroy_function($geoDivisionTypeTranslation); // ← suppression de la traduction et de ses dépendances associées + gestion de suppression des fichiers associés si besoin
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_geodivisiontypes_translations_restore(GeoDivisionTypeTranslation $geoDivisionTypeTranslation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($geoDivisionTypeTranslation) {

            // Restauration simple (restore)
            if ($geoDivisionTypeTranslation->trashed()) {

                // Validation générique factorisée (BackendHelper) : refuse de restaurer
                // une traduction dont le nom entre en conflit avec une autre traduction
                // (active ou en corbeille) dans la même langue.
                assertTranslationRestore(
                    $geoDivisionTypeTranslation,
                    __('v1/backend/others/helpers/backend-helper-messages.entityLabels.divisionType'),
                    $this->backendHelperLangPath
                );

                restore_function($geoDivisionTypeTranslation); // <- restauration de la traduction du type de division sélectionné

            } else {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.restore.not_trashed_forbidden"));
            }

        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_geodivisiontypes_translations_force_destroy(GeoDivisionTypeTranslation $geoDivisionTypeTranslation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($geoDivisionTypeTranslation) {

            // 1. Verrou sur le type de division (même soft-deleted), toujours en premier
            //    pour garder le même ordre de verrouillage que destroy() et syncTranslations()
            $geoDivisionType = GeoDivisionType::withTrashed()
                ->lockForUpdate()
                ->find($geoDivisionTypeTranslation->division_type_id);

            if (! $geoDivisionType) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.division_type_not_found"));
            }

            // 2. Relecture verrouillée de la traduction (le binding peut être périmé)
            $geoDivisionTypeTranslation = GeoDivisionTypeTranslation::withTrashed()
                ->lockForUpdate()
                ->find($geoDivisionTypeTranslation->id);

            if (! $geoDivisionTypeTranslation) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.translation_not_found"));
            }

            // 3. Seule une traduction en corbeille peut être supprimée définitivement
            if (! $geoDivisionTypeTranslation->trashed()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.not_trashed_forbidden"));
            }

            // 4. Interdit de supprimer la dernière traduction du type (corbeille incluse)
            if ($geoDivisionType->translations()->withTrashed()->count() <= 1) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.last_translation_forbidden"));
            }

            // 5. Suppression définitive de la traduction et de ses dépendances
            force_destroy_function($geoDivisionTypeTranslation); // <- suppression définitive de la traduction du type de division sélectionné + gestion de suppression des fichiers associés si besoin
        });
    }
}
