<?php

namespace App\Services\V1\Controllers;

use App\Models\ProductWarehouseLocation;
use App\Models\ProductWarehouseLocationTranslation;
use Illuminate\Support\Facades\DB;

use function __;

class ProductWarehouseLocationTranslationService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/products/warehouse-locations/messages/warehouse-locations-messages-service.warehouseLocations.translations';

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_location_translations_destroy(ProductWarehouseLocationTranslation $translation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($translation) {

            // 1. Verrou sur l'emplacement (même soft-deleted)
            $location = ProductWarehouseLocation::withTrashed()
                ->lockForUpdate()
                ->find($translation->location_id);

            if (! $location) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.location_not_found"));
            }

            // 2. Relecture verrouillée de la traduction
            $translation = ProductWarehouseLocationTranslation::withTrashed()
                ->lockForUpdate()
                ->find($translation->id);

            if (! $translation) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.translation_not_found"));
            }

            // 3. Déjà en corbeille
            if ($translation->trashed()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.already_deleted"));
            }

            // 4. Dernière traduction active
            if ($location->translations()->count() <= 1) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.last_translation_forbidden"));
            }

            // 5. Suppression
            destroy_function($translation); // ← suppression de la traduction et de ses dépendances associées
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_location_translations_restore(ProductWarehouseLocationTranslation $translation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($translation) {

            // Restauration simple (restore)
            if ($translation->trashed()) {

                // Unicité du nom par entrepôt et par langue : contrôle porté par une requête dédiée
                // car assertTranslationRestore() (BackendHelper) ne filtre que par langue, et la table
                // de traduction ne porte pas warehouse_id.
                $location = ProductWarehouseLocation::withTrashed()->find($translation->location_id);

                if (! $location) {
                    throw new \InvalidArgumentException(__("{$this->backendLangPath}.restore.location_not_found"));
                }

                $conflict = ProductWarehouseLocationTranslation::withTrashed()
                    ->where('lang_id', $translation->lang_id)
                    ->where('id', '!=', $translation->id)
                    ->whereRaw('LOWER(name) = ?', [mb_strtolower(trim((string) $translation->name))])
                    ->whereHas('location', fn ($query) => $query->withTrashed()->where('warehouse_id', $location->warehouse_id))
                    ->exists();

                if ($conflict) {
                    throw new \InvalidArgumentException(__("{$this->backendLangPath}.restore.name_conflict"));
                }

                restore_function($translation); // ← restauration de la traduction de l'emplacement sélectionné

            } else {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.restore.not_trashed_forbidden"));
            }

        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_location_translations_force_destroy(ProductWarehouseLocationTranslation $translation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($translation) {

            // 1. Verrou sur l'emplacement (même soft-deleted), toujours en premier
            //    pour garder le même ordre de verrouillage que destroy()
            $location = ProductWarehouseLocation::withTrashed()
                ->lockForUpdate()
                ->find($translation->location_id);

            if (! $location) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.location_not_found"));
            }

            // 2. Relecture verrouillée de la traduction (le binding peut être périmé)
            $translation = ProductWarehouseLocationTranslation::withTrashed()
                ->lockForUpdate()
                ->find($translation->id);

            if (! $translation) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.translation_not_found"));
            }

            // 3. Seule une traduction en corbeille peut être supprimée définitivement
            if (! $translation->trashed()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.not_trashed_forbidden"));
            }

            // 4. Interdit de supprimer la dernière traduction de l'emplacement (corbeille incluse)
            if ($location->translations()->withTrashed()->count() <= 1) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.last_translation_forbidden"));
            }

            // 5. Suppression définitive de la traduction et de ses dépendances
            force_destroy_function($translation); // ← suppression définitive de la traduction de l'emplacement sélectionné
        });
    }
}
