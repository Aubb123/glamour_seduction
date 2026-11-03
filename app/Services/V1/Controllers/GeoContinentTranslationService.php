<?php

namespace App\Services\V1\Controllers;

use App\Models\GeoContinent;
use App\Models\GeoContinentTranslation;
use Illuminate\Support\Facades\DB;

use function __;

class GeoContinentTranslationService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/geos/continents/messages/continent-messages-service.translations';

    // Chemin des messages génériques du backend helper (factorisés pour toutes les entités traduisibles)
    protected string $helperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function destroy(GeoContinentTranslation $geoContinentTranslation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($geoContinentTranslation) {

            // 1. Verrou sur le continent (même soft-deleted)
            $continent = GeoContinent::withTrashed()
                ->lockForUpdate()
                ->find($geoContinentTranslation->continent_id);

            if (! $continent) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.continent_not_found"));
            }

            // 2. Relecture verrouillée de la traduction
            $geoContinentTranslation = GeoContinentTranslation::withTrashed()
                ->lockForUpdate()
                ->find($geoContinentTranslation->id);

            if (! $geoContinentTranslation) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.translation_not_found"));
            }

            // 3. Déjà en corbeille
            if ($geoContinentTranslation->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.already_deleted"));
            }

            // 4. Dernière traduction active
            if ($continent->translations()->count() <= 1) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.last_translation_forbidden"));
            }

            // 5. Suppression
            destroy_function($geoContinentTranslation); // ← suppression de la traduction et de ses dépendances associées + gestion de suppression des fichiers associés si besoin
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function restore(GeoContinentTranslation $translation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($translation) {

            // Restauration simple (restore)
            if ($translation->trashed()) {

                // Validation générique factorisée (BackendHelper) : refuse de restaurer
                // une traduction dont le nom/slug entre en conflit avec une autre traduction
                // (active ou en corbeille) dans la même langue. Lève une InvalidArgumentException
                // avec un message explicite si besoin.
                assertTranslationRestore(
                    $translation,
                    __('v1/backend/others/helpers/backend-helper-messages.entityLabels.continent'),
                    $this->helperLangPath
                );

                restore_function($translation);
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.restore.not_trashed_forbidden"));
            }

        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function forceDestroy(GeoContinentTranslation $translation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($translation) {

            // 1. Verrou sur le continent (même soft-deleted), toujours en premier
            //    pour garder le même ordre de verrouillage que destroy() et syncTranslations()
            $continent = GeoContinent::withTrashed()
                ->lockForUpdate()
                ->find($translation->continent_id);

            if (! $continent) {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.continent_not_found"));
            }

            // 2. Relecture verrouillée de la traduction (le binding peut être périmé)
            $translation = GeoContinentTranslation::withTrashed()
                ->lockForUpdate()
                ->find($translation->id);

            if (! $translation) {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.translation_not_found"));
            }

            // 3. Seule une traduction en corbeille peut être supprimée définitivement
            if (! $translation->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.not_trashed_forbidden"));
            }

            // 4. Interdit de supprimer la dernière traduction du continent (corbeille incluse)
            if ($continent->translations()->withTrashed()->count() <= 1) {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.last_translation_forbidden"));
            }

            // 5. Suppression définitive de la traduction et de ses dépendances
            force_destroy_function($translation);
        });
    }
}
