<?php

namespace App\Services\V1\Controllers;

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

            // Récupération du continent associé à la traduction (même si elle est soft-deleted)
            $continent = $geoContinentTranslation->continent()->withTrashed()->first();

            // Empêche la suppression de la traduction si c'est la dernière traduction restante du continent (même si le continent est soft-deleted)
            if ($continent) {

                // Compte le nombre de traductions associées au continent, y compris les traductions soft-deleted
                // $translationsCount = $continent->translations()->withTrashed()->count();

                // Compte le nombre de traductions associées au continent, en excluant les traductions soft-deleted
                $translationsCount = $continent->translations()->count();

                if ($translationsCount <= 1) {
                    throw new \InvalidArgumentException(__("{$this->langPath}.destroy.last_translation_forbidden"));
                }
            }

            // Suppression simple (deleted_at)
            if ($geoContinentTranslation->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.already_deleted"));
            }
            destroy_function($geoContinentTranslation); // ← suppression de la traduction et de ses dépendances associées + gestion de suppression des fichiers associés si besoin

        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function restore(GeoContinentTranslation $translation): void
    {
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
        DB::transaction(function () use ($translation) {

            // Récupération du continent associé à la traduction (même si soft-deleted)
            $continent = $translation->continent()->withTrashed()->first();

            // Empêche la suppression définitive de la traduction si c'est la dernière traduction restante du continent (même si le continent est soft-deleted)
            if ($continent) {

                // Compte le nombre de traductions associées au continent, y compris les traductions soft-deleted
                $translationsCount = $continent->translations()->withTrashed()->count();

                if ($translationsCount <= 1) {
                    throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.last_translation_forbidden"));
                }
            }

            // Suppression définitive (forceDelete)
            if ($translation->trashed()) {
                force_destroy_function($translation);
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.not_trashed_forbidden"));
            }

        });
    }
}
