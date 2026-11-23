<?php

namespace App\Services\V1\Controllers;

use App\Models\ProductWarehouse;
use App\Models\ProductWarehouseTranslation;
use Illuminate\Support\Facades\DB;

use function __;

class ProductWarehouseTranslationService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/products/warehouses/messages/warehouses-messages-service.warehouses.translations';

    // Chemin des messages génériques du backend helper (factorisés pour toutes les entités traduisibles)
    protected string $backendHelperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_translations_destroy(ProductWarehouseTranslation $productWarehouseTranslation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($productWarehouseTranslation) {

            // 1. Verrou sur l'entrepôt (même soft-deleted)
            $warehouse = ProductWarehouse::withTrashed()
                ->lockForUpdate()
                ->find($productWarehouseTranslation->warehouse_id);

            if (! $warehouse) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.warehouse_not_found"));
            }

            // 2. Relecture verrouillée de la traduction
            $productWarehouseTranslation = ProductWarehouseTranslation::withTrashed()
                ->lockForUpdate()
                ->find($productWarehouseTranslation->id);

            if (! $productWarehouseTranslation) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.translation_not_found"));
            }

            // 3. Déjà en corbeille
            if ($productWarehouseTranslation->trashed()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.already_deleted"));
            }

            // 4. Dernière traduction active
            if ($warehouse->translations()->count() <= 1) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.last_translation_forbidden"));
            }

            // 5. Suppression
            destroy_function($productWarehouseTranslation); // ← suppression de la traduction et de ses dépendances associées
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_translations_restore(ProductWarehouseTranslation $productWarehouseTranslation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($productWarehouseTranslation) {

            // Restauration simple (restore)
            if ($productWarehouseTranslation->trashed()) {

                // Validation générique factorisée (BackendHelper) : refuse de restaurer
                // une traduction dont le nom/slug entre en conflit avec une autre traduction
                // (active ou en corbeille) dans la même langue.
                assertTranslationRestore(
                    $productWarehouseTranslation,
                    __('v1/backend/others/helpers/backend-helper-messages.entityLabels.warehouse'),
                    $this->backendHelperLangPath,
                    'name',
                    'slug'
                );

                restore_function($productWarehouseTranslation); // ← restauration de la traduction de l'entrepôt sélectionné

            } else {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.restore.not_trashed_forbidden"));
            }

        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_translations_force_destroy(ProductWarehouseTranslation $productWarehouseTranslation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($productWarehouseTranslation) {

            // 1. Verrou sur l'entrepôt (même soft-deleted), toujours en premier
            //    pour garder le même ordre de verrouillage que destroy() et syncTranslations()
            $warehouse = ProductWarehouse::withTrashed()
                ->lockForUpdate()
                ->find($productWarehouseTranslation->warehouse_id);

            if (! $warehouse) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.warehouse_not_found"));
            }

            // 2. Relecture verrouillée de la traduction (le binding peut être périmé)
            $productWarehouseTranslation = ProductWarehouseTranslation::withTrashed()
                ->lockForUpdate()
                ->find($productWarehouseTranslation->id);

            if (! $productWarehouseTranslation) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.translation_not_found"));
            }

            // 3. Seule une traduction en corbeille peut être supprimée définitivement
            if (! $productWarehouseTranslation->trashed()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.not_trashed_forbidden"));
            }

            // 4. Interdit de supprimer la dernière traduction de l'entrepôt (corbeille incluse)
            if ($warehouse->translations()->withTrashed()->count() <= 1) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.last_translation_forbidden"));
            }

            // 5. Suppression définitive de la traduction et de ses dépendances
            force_destroy_function($productWarehouseTranslation); // ← suppression définitive de la traduction de l'entrepôt sélectionné
        });
    }
}
