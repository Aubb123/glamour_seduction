<?php

namespace App\Services\V1\Controllers;

use App\Models\ProductTranslation;
use Illuminate\Support\Facades\DB;

use function __;

class ProductTranslationService
{
    // Chemin de base pour les messages
    protected string $langPath = 'v1/backend/views/pages/products/products/messages/products-messages-service.productsTranslations';

    // Chemin des messages génériques du backend helper (factorisés pour toutes les entités traduisibles)
    protected string $helperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function destroy(ProductTranslation $translation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($translation) {

            // Récupération du produit associé à la traduction (même s'il est soft-deleted)
            $product = $translation->product()->withTrashed()->first();

            // Empêche la suppression de la traduction si c'est la dernière traduction restante du produit (même si le produit est soft-deleted)
            if ($product) {

                // Compte le nombre de traductions associées au produit, en excluant les traductions soft-deleted
                $translationsCount = $product->translations()->count();

                if ($translationsCount <= 1) {
                    throw new \InvalidArgumentException(__("{$this->langPath}.destroy.last_translation_forbidden"));
                }
            }

            // Suppression simple (deleted_at)
            if ($translation->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.already_deleted"));
            }
            destroy_function($translation); // ← suppression de la traduction et de ses dépendances associées

        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function restore(ProductTranslation $translation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($translation) {

            // Restauration simple (restore)
            if ($translation->trashed()) {

                // Validation générique factorisée (BackendHelper) : refuse de restaurer
                // une traduction dont le nom/slug entre en conflit avec une autre traduction
                // (active ou en corbeille) dans la même langue.
                assertTranslationRestore(
                    $translation,
                    __('v1/backend/others/helpers/backend-helper-messages.entityLabels.product'),
                    $this->helperLangPath,
                    'name',
                    'slug'
                );

                restore_function($translation); // ← restauration de la traduction du produit sélectionné
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.restore.not_trashed_forbidden"));
            }

        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function forceDestroy(ProductTranslation $translation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($translation) {

            // Récupération du produit associé à la traduction (même s'il est soft-deleted)
            $product = $translation->product()->withTrashed()->first();

            // Si le produit existe, on vérifie si c'est la dernière traduction restante avant de permettre la suppression définitive
            if ($product) {

                // Compte le nombre de traductions associées au produit, y compris les traductions soft-deleted
                $translationsCount = $product->translations()->withTrashed()->count();

                // Empêche la suppression définitive de la traduction si c'est la dernière traduction restante du produit (même si le produit est soft-deleted)
                if ($translationsCount <= 1) {
                    throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.last_translation_forbidden"));
                }
            }

            // Suppression définitive (forceDelete)
            if ($translation->trashed()) {
                force_destroy_function($translation); // ← suppression définitive de la traduction du produit sélectionné
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.not_trashed_forbidden"));
            }

        });
    }
}