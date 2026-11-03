<?php

namespace App\Services\V1\Controllers;

use App\Models\Product;
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

            // 1. Verrou sur le produit (même soft-deleted)
            $product = Product::withTrashed()
                ->lockForUpdate()
                ->find($translation->product_id);

            if (! $product) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.product_not_found"));
            }

            // 2. Relecture verrouillée de la traduction
            $translation = ProductTranslation::withTrashed()
                ->lockForUpdate()
                ->find($translation->id);

            if (! $translation) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.translation_not_found"));
            }

            // 3. Déjà en corbeille
            if ($translation->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.already_deleted"));
            }

            // 4. Dernière traduction active
            if ($product->translations()->count() <= 1) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.last_translation_forbidden"));
            }

            // 5. Suppression
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

            // 1. Verrou sur le produit (même soft-deleted), toujours en premier
            //    pour garder le même ordre de verrouillage que destroy() et syncTranslations()
            $product = Product::withTrashed()
                ->lockForUpdate()
                ->find($translation->product_id);

            if (! $product) {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.product_not_found"));
            }

            // 2. Relecture verrouillée de la traduction (le binding peut être périmé)
            $translation = ProductTranslation::withTrashed()
                ->lockForUpdate()
                ->find($translation->id);

            if (! $translation) {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.translation_not_found"));
            }

            // 3. Seule une traduction en corbeille peut être supprimée définitivement
            if (! $translation->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.not_trashed_forbidden"));
            }

            // 4. Interdit de supprimer la dernière traduction du produit (corbeille incluse)
            if ($product->translations()->withTrashed()->count() <= 1) {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.last_translation_forbidden"));
            }

            // 5. Suppression définitive de la traduction et de ses dépendances
            force_destroy_function($translation); // ← suppression définitive de la traduction du produit sélectionné
        });
    }
}
