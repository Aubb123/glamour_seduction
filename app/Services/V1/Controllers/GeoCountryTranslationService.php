<?php

namespace App\Services\V1\Controllers;

use App\Models\GeoCountryTranslation;
use Illuminate\Support\Facades\DB;

use function __;

class GeoCountryTranslationService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/geos/countries/messages/countries-messages-service.countries.translations';

    // Chemin des messages génériques du backend helper (factorisés pour toutes les entités traduisibles)
    protected string $helperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function destroy(GeoCountryTranslation $geoCountryTranslation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($geoCountryTranslation) {

            // Récupération du pays associé à la traduction (même s'il est soft-deleted)
            $geoCountry = $geoCountryTranslation->country()->withTrashed()->first();

            // Empêche la suppression de la traduction si c'est la dernière traduction restante du pays (même si le pays est soft-deleted)
            if ($geoCountry) {

                // Compte le nombre de traductions associées au pays, en excluant les traductions soft-deleted
                $translationsCount = $geoCountry->translations()->count();

                if ($translationsCount <= 1) {
                    throw new \InvalidArgumentException(__("{$this->langPath}.destroy.last_translation_forbidden"));
                }
            }

            // Suppression simple (deleted_at)
            if ($geoCountryTranslation->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.already_deleted"));
            }
            destroy_function($geoCountryTranslation); // ← suppression de la traduction et de ses dépendances associées + gestion de suppression des fichiers associés si besoin

        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function restore(GeoCountryTranslation $geoCountryTranslation): void
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
                    $this->helperLangPath
                );

                restore_function($geoCountryTranslation); // <- restauration de la traduction du pays sélectionné

            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.restore.not_trashed_forbidden"));
            }

        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function forceDestroy(GeoCountryTranslation $geoCountryTranslation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($geoCountryTranslation) {

            // Empêche la suppression définitive de la traduction si c'est la dernière traduction restante (même si le pays est soft-deleted)
            $geoCountry = $geoCountryTranslation->country()->withTrashed()->first();

            if ($geoCountry) {

                // Compte le nombre de traductions associées au pays, y compris les traductions soft-deleted
                $translationsCount = $geoCountry->translations()->withTrashed()->count();

                if ($translationsCount <= 1) {
                    throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.last_translation_forbidden"));
                }
            }

            // Suppression définitive (forceDelete)
            if ($geoCountryTranslation->trashed()) {
                force_destroy_function($geoCountryTranslation); // <- suppression définitive de la traduction du pays sélectionné + gestion de suppression des fichiers associés si besoin
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.not_trashed_forbidden"));
            }

        });
    }
}
