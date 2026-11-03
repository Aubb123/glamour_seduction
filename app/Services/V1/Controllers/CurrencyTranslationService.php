<?php

namespace App\Services\V1\Controllers;

use App\Models\Currency;
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

            // 1. Verrou sur la devise (même soft-deleted)
            $currency = Currency::withTrashed()
                ->lockForUpdate()
                ->find($currencyTranslation->currency_id);

            if (! $currency) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.currency_not_found"));
            }

            // 2. Relecture verrouillée de la traduction
            $currencyTranslation = CurrencyTranslation::withTrashed()
                ->lockForUpdate()
                ->find($currencyTranslation->id);

            if (! $currencyTranslation) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.translation_not_found"));
            }

            // 3. Déjà en corbeille
            if ($currencyTranslation->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.already_deleted"));
            }

            // 4. Dernière traduction active
            if ($currency->translations()->count() <= 1) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.last_translation_forbidden"));
            }

            // 5. Suppression
            destroy_function($currencyTranslation); // ← suppression de la traduction et de ses dépendances associées
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

            // 1. Verrou sur la devise (même soft-deleted), toujours en premier
            //    pour garder le même ordre de verrouillage que destroy() et syncTranslations()
            $currency = Currency::withTrashed()
                ->lockForUpdate()
                ->find($currencyTranslation->currency_id);

            if (! $currency) {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.currency_not_found"));
            }

            // 2. Relecture verrouillée de la traduction (le binding peut être périmé)
            $currencyTranslation = CurrencyTranslation::withTrashed()
                ->lockForUpdate()
                ->find($currencyTranslation->id);

            if (! $currencyTranslation) {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.translation_not_found"));
            }

            // 3. Seule une traduction en corbeille peut être supprimée définitivement
            if (! $currencyTranslation->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.not_trashed_forbidden"));
            }

            // 4. Interdit de supprimer la dernière traduction de la devise (corbeille incluse)
            if ($currency->translations()->withTrashed()->count() <= 1) {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.last_translation_forbidden"));
            }

            // 5. Suppression définitive de la traduction et de ses dépendances
            force_destroy_function($currencyTranslation);
        });
    }
}
