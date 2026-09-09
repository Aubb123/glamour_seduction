<?php

namespace App\Services\V1\Controllers;

use App\Models\CurrencyTranslation;
use Illuminate\Support\Facades\DB;

use function __;

class CurrencyTranslationService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/currencies/messages/currencies-messages-service.currencies.translations';

    // Chemin des messages génériques du backend helper (factorisés pour toutes les entités traduisibles)
    protected string $helperLangPath = 'v1/backend/others/helpers/backend-helper-messages'; 

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function destroy(CurrencyTranslation $currencyTranslation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($currencyTranslation) {

            // Récupération de la devise associée à la traduction (même si elle est soft-deleted)
            $currency = $currencyTranslation->currency()->withTrashed()->first();

            // Empêche la suppression de la traduction si c'est la dernière traduction restante de la devise
            if ($currency) {

                // Compte le nombre de traductions associées à la devise, en excluant les traductions soft-deleted
                $translationsCount = $currency->translations()->count();
 
                if ($translationsCount <= 1) {
                    throw new \InvalidArgumentException(__("{$this->langPath}.destroy.last_translation_forbidden"));
                }
            }

            // Suppression simple (deleted_at)
            if ($currencyTranslation->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.already_deleted"));
            }
            destroy_function($currencyTranslation); // ← suppression de la traduction
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function restore(CurrencyTranslation $currencyTranslation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($currencyTranslation) {

            // Restauration simple (restore)
            if ($currencyTranslation->trashed()) {

                // Validation générique factorisée (BackendHelper) : refuse de restaurer
                // une traduction dont le nom/slug entre en conflit avec une autre traduction
                // (active ou en corbeille) dans la même langue.
                assertTranslationRestore(
                    $currencyTranslation,
                    __('v1/backend/others/helpers/backend-helper-messages.entityLabels.currency'),
                    $this->helperLangPath
                );

                restore_function($currencyTranslation); // ← restauration de la traduction

            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.restore.not_trashed_forbidden"));
            }
        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function forceDestroy(CurrencyTranslation $currencyTranslation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($currencyTranslation) {

            // Empêche la suppression définitive de la traduction si c'est la dernière traduction restante
            $currency = $currencyTranslation->currency()->withTrashed()->first();

            if ($currency) {

                // Compte le nombre de traductions associées à la devise, y compris les traductions soft-deleted
                $translationsCount = $currency->translations()->withTrashed()->count();

                if ($translationsCount <= 1) {
                    throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.last_translation_forbidden"));
                }
            }

            // Suppression définitive (forceDelete)
            if ($currencyTranslation->trashed()) {
                force_destroy_function($currencyTranslation);
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.not_trashed_forbidden"));
            }
        });
    }
}