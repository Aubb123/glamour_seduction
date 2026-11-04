<?php

namespace App\Services\V1\Controllers;

use App\Models\GeoCountry;
use App\Models\GeoCountryTranslation;
use Illuminate\Support\Facades\DB;

use function __;

class GeoCountryTranslationService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/geos/countries/messages/countries-messages-service.countries.translations';

    // Chemin des messages génériques du backend helper (factorisés pour toutes les entités traduisibles)
    protected string $backendHelperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_geocountries_translations_destroy(GeoCountryTranslation $geoCountryTranslation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($geoCountryTranslation) {

            // 1. Verrou sur le pays (même soft-deleted)
            $geoCountry = GeoCountry::withTrashed()
                ->lockForUpdate()
                ->find($geoCountryTranslation->country_id);

            if (! $geoCountry) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.country_not_found"));
            }

            // 2. Relecture verrouillée de la traduction
            $geoCountryTranslation = GeoCountryTranslation::withTrashed()
                ->lockForUpdate()
                ->find($geoCountryTranslation->id);

            if (! $geoCountryTranslation) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.translation_not_found"));
            }

            // 3. Déjà en corbeille
            if ($geoCountryTranslation->trashed()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.already_deleted"));
            }

            // 4. Dernière traduction active
            if ($geoCountry->translations()->count() <= 1) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.last_translation_forbidden"));
            }

            // 5. Suppression
            destroy_function($geoCountryTranslation); // ← suppression de la traduction et de ses dépendances associées + gestion de suppression des fichiers associés si besoin
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_geocountries_translations_restore(GeoCountryTranslation $geoCountryTranslation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($geoCountryTranslation) {

            // Restauration simple (restore)
            if ($geoCountryTranslation->trashed()) {

                // Validation générique factorisée (BackendHelper) : refuse de restaurer
                // une traduction dont le nom/slug entre en conflit avec une autre traduction
                // (active ou en corbeille) dans la même langue. Lève une InvalidArgumentException
                // avec un message explicite si besoin.
                assertTranslationRestore(
                    $geoCountryTranslation,
                    __('v1/backend/others/helpers/backend-helper-messages.entityLabels.country'),
                    $this->backendHelperLangPath
                );

                restore_function($geoCountryTranslation); // <- restauration de la traduction du pays sélectionné

            } else {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.restore.not_trashed_forbidden"));
            }

        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_geocountries_translations_force_destroy(GeoCountryTranslation $geoCountryTranslation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($geoCountryTranslation) {

            // 1. Verrou sur le pays (même soft-deleted), toujours en premier
            //    pour garder le même ordre de verrouillage que destroy() et syncTranslations()
            $geoCountry = GeoCountry::withTrashed()
                ->lockForUpdate()
                ->find($geoCountryTranslation->country_id);

            if (! $geoCountry) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.country_not_found"));
            }

            // 2. Relecture verrouillée de la traduction (le binding peut être périmé)
            $geoCountryTranslation = GeoCountryTranslation::withTrashed()
                ->lockForUpdate()
                ->find($geoCountryTranslation->id);

            if (! $geoCountryTranslation) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.translation_not_found"));
            }

            // 3. Seule une traduction en corbeille peut être supprimée définitivement
            if (! $geoCountryTranslation->trashed()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.not_trashed_forbidden"));
            }

            // 4. Interdit de supprimer la dernière traduction du pays (corbeille incluse)
            if ($geoCountry->translations()->withTrashed()->count() <= 1) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.last_translation_forbidden"));
            }

            // 5. Suppression définitive de la traduction et de ses dépendances
            force_destroy_function($geoCountryTranslation); // <- suppression définitive de la traduction du pays sélectionné + gestion de suppression des fichiers associés si besoin
        });
    }
}
