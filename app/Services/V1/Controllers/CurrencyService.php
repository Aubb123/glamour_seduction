<?php

namespace App\Services\V1\Controllers;

use App\Enums\Currencies\CurrencyStatus;
use App\Models\Currency;
use App\Models\CurrencyTranslation;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

use function __;

class CurrencyService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/currencies/messages/currencies-messages-service.currencies';

    // Chemin des messages génériques du backend helper (factorisés pour toutes les entités traduisibles)
    protected string $backendHelperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_currencies_update(Currency $currency, array $data): Currency
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        return DB::transaction(function () use ($currency, $data) {

            // Si la devise est marquée comme par défaut, on retire le flag
            // à toutes les autres devises (une seule devise par défaut à la fois).
            $isDefault = ! empty($data['is_default']);

            if ($isDefault) {
                Currency::where('id', '!=', $currency->id)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }

            // Garde métier (Module 10) : la devise par défaut est la base de saisie des prix
            // → son taux de change reste toujours fixé à 1.000000.
            $exchangeRate = (float) ($data['exchange_rate'] ?? $currency->exchange_rate ?? 1);

            if ($isDefault && abs($exchangeRate - 1.0) > 0.0000005) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.update.exchange_rate_default_forbidden"));
            }

            $attributes = [
                // Champs obligatoires : toujours présents dans les données validées
                'code' => $data['code'],
                'symbol' => $data['symbol'],
                'decimal_places' => $data['decimal_places'],
                'sort_order' => $data['sort_order'],
                // Taux de change (Module 10 : prix multi-devise)
                'exchange_rate' => $exchangeRate,
                // Statut : la case à cocher est absente du payload = devise masquée (convention formulaire)
                'status' => ($data['status'] ?? null) === CurrencyStatus::Active->value
                    ? CurrencyStatus::Active
                    : CurrencyStatus::Inactive,
                'is_default' => $isDefault,
            ];

            // Champs nullables : on distingue « champ absent du payload » (on conserve
            // la valeur existante) et « champ présent avec null » (on VIDE le champ).
            foreach (['numeric_code'] as $nullableField) {
                $attributes[$nullableField] = Arr::exists($data, $nullableField)
                    ? $data[$nullableField] // ← null explicite = on vide le champ
                    : $currency->{$nullableField}; // ← absent = on conserve l'existant
            }

            // Mise à jour des champs de la devise (sauf les traductions)
            $currency->update($attributes);

            // Ensuite, synchronisation des traductions (création ou mise à jour)
            $this->syncTranslations($currency, $data['translations']);

            return $currency; // ← retourne la devise mise à jour avec ses traductions
        });
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_currencies_activate(Currency $currency): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($currency) {

            // Si la devise est déjà active, on lève une exception métier pour éviter une opération inutile
            if ($currency->status === CurrencyStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.activate.already_active"));
            }

            // Activation de la devise sélectionnée
            $currency->update([
                'status' => CurrencyStatus::Active,
            ]);
        });
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_currencies_deactivate(Currency $currency): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($currency) {

            // Si la devise est déjà inactive, on lève une exception métier
            if ($currency->status === CurrencyStatus::Inactive) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.deactivate.already_inactive"));
            }

            // Désactivation de la devise sélectionnée
            $currency->update([
                'status' => CurrencyStatus::Inactive,
            ]);
        });
    }

    // ─────────────────────────────────────────────
    // DESTROY (soft delete)
    // ─────────────────────────────────────────────
    public function backend_currencies_destroy(Currency $currency): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($currency) {

            // Une devise par défaut ne peut pas être supprimée (il faut d'abord en désigner une autre)
            if ($currency->is_default) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.default_currency_forbidden"));
            }

            // Une devise active ne peut pas être supprimée
            if ($currency->status === CurrencyStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.active_forbidden"));
            }

            // Empêche la suppression si la devise est déjà en corbeille
            if ($currency->trashed()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.already_deleted"));
            }

            // Suppression (soft delete) de la devise et de ses dépendances associées
            destroy_function($currency);
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_currencies_restore(Currency $currency): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($currency) {

            // Restauration simple (restore)
            if ($currency->trashed()) {
                restore_function($currency);
            } else {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.restore.not_trashed_forbidden"));
            }
        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_currencies_force_destroy(Currency $currency): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($currency) {

            // Une devise par défaut ne peut pas être supprimée définitivement
            if ($currency->is_default) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.default_currency_forbidden"));
            }

            // Suppression définitive (forceDelete)
            if ($currency->trashed()) {
                // Une entité active ne peut pas être supprimée définitivement
                if ($currency->status === CurrencyStatus::Active) {
                    throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.active_forbidden"));
                }
                force_destroy_function($currency);
            } else {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.not_trashed_forbidden"));
            }
        });
    }

    // ─────────────────────────────────────────────
    // SYNC TRANSLATIONS (privé)
    // ─────────────────────────────────────────────
    private function syncTranslations(Currency $currency, array $translations): void
    {
        // Récupère les IDs des langues soumises
        $submittedLangIds = collect($translations)->pluck('lang_id')->toArray();

        // Supprime les traductions qui ne sont plus présentes dans le formulaire
        CurrencyTranslation::where('currency_id', $currency->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->delete();
        // Suppression définitive des traductions supprimées (soft delete) qui sont en corbeille, supprimer précédemment
        CurrencyTranslation::onlyTrashed()->where('currency_id', $currency->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->forceDelete();

        // Validation générique factorisée (BackendHelper) :
        //  - conflit nom/slug avec une autre entité (active ou en corbeille) dans la même langue ;
        //  - conflit avec la propre traduction de l'entité, placée en corbeille.
        // Lève une InvalidArgumentException avec un message explicite si besoin.
        assertTranslationUniqueness(
            $currency,
            $translations,
            __('v1/backend/others/helpers/backend-helper-messages.entityLabels.currency'),
            $this->backendHelperLangPath
        );

        // Pour chaque traduction reçue, on utilise updateOrCreate pour créer ou mettre à jour la traduction correspondante
        foreach ($translations as $translation) {

            $langId = $translation['lang_id'];

            // Vérifie si une traduction existe déjà pour la devise et la langue
            $existingTranslation = CurrencyTranslation::withTrashed()
                ->where('currency_id', $currency->id)
                ->where('lang_id', $langId)
                ->first();

            // Si une traduction existante est trouvée
            if ($existingTranslation) {

                // Une traduction en corbeille ne doit pas être modifiée silencieusement :
                // elle doit être restaurée manuellement depuis la corbeille avant de pouvoir
                // être mise à jour (remplace l'ancienne restauration automatique).
                if ($existingTranslation->trashed()) {
                    throw new \InvalidArgumentException(
                        __("{$this->backendLangPath}.trashedTranslationConflict", [
                            'lang_id' => $langId,
                        ])
                    );
                }

                // NB : le slug n'est PAS défini manuellement ici — le trait HasSlug le régénère
                // automatiquement (avec garantie d'unicité par langue) si le nom change.
                $existingTranslation->update([
                    'name' => $translation['name'],
                    // La description est optionnelle
                    'description' => $translation['description'] ?? null,
                ]);

            } else {
                // Si la traduction n'existe pas, on la crée
                CurrencyTranslation::create([
                    'currency_id' => $currency->id,
                    'lang_id' => $langId,
                    'name' => $translation['name'],
                    'description' => $translation['description'] ?? null,
                ]);
            }
        }
    }
}
