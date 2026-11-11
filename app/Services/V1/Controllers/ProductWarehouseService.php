<?php

namespace App\Services\V1\Controllers;

use App\Enums\Products\Warehouses\ProductWarehouseStatus;
use App\Models\ProductWarehouse;
use App\Models\ProductWarehouseTranslation;
use Illuminate\Support\Facades\DB;

use function __;

class ProductWarehouseService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/products/warehouses/messages/warehouses-messages-service.productWarehouses';

    // Chemin des messages génériques du backend helper (factorisés pour toutes les entités traduisibles)
    protected string $backendHelperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_store(array $data): ProductWarehouse
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        return DB::transaction(function () use ($data) {

            // Création de l'entrepôt (données non traduites uniquement)
            $warehouse = ProductWarehouse::create([
                'code' => $data['code'],
                'status' => ($data['status'] ?? null) === ProductWarehouseStatus::Active->value ? ProductWarehouseStatus::Active : ProductWarehouseStatus::Inactive,
            ]);

            // Synchronisation des traductions (création)
            $this->syncTranslations($warehouse, $data['translations']);

            return $warehouse;
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_update(ProductWarehouse $warehouse, array $data): ProductWarehouse
    {
        return DB::transaction(function () use ($warehouse, $data) {

            $updateData = [];

            if (isset($data['code'])) {
                $updateData['code'] = $data['code'];
            }

            if (isset($data['status'])) {
                $updateData['status'] = ($data['status'] ?? null) === ProductWarehouseStatus::Active->value ? ProductWarehouseStatus::Active : ProductWarehouseStatus::Inactive;
            }

            $warehouse->update($updateData);

            // Synchronisation des traductions (création ou mise à jour)
            if (isset($data['translations'])) {
                $this->syncTranslations($warehouse, $data['translations']);
            }

            return $warehouse;
        });
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_activate(ProductWarehouse $warehouse): void
    {
        DB::transaction(function () use ($warehouse) {

            if ($warehouse->status === ProductWarehouseStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.activate.already_active"));
            }

            $warehouse->update(['status' => ProductWarehouseStatus::Active]);
        });
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_deactivate(ProductWarehouse $warehouse): void
    {
        DB::transaction(function () use ($warehouse) {

            if ($warehouse->status === ProductWarehouseStatus::Inactive) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.deactivate.already_inactive"));
            }

            // NB : « entrepôt par défaut » étant une notion PAR BOUTIQUE (pivot
            // `shop_warehouse.is_primary`), la désactivation ne rompt aucune
            // exclusivité globale : il n'y a donc plus de garde ici.
            $warehouse->update(['status' => ProductWarehouseStatus::Inactive]);
        });
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_destroy(ProductWarehouse $warehouse): void
    {
        DB::transaction(function () use ($warehouse) {

            // Empêche la suppression d'un entrepôt actif (règle métier : le désactiver d'abord)
            if ($warehouse->status === ProductWarehouseStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.active_forbidden"));
            }

            // NB : destroy refusé si des stocks/mouvements/personnel existent
            // (géré par BackendHelper::destroy_function via $dependentRelations = ['stocks', 'movements', 'warehouseUsers']).

            // Empêche la suppression double (soft delete)
            if ($warehouse->trashed()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.already_deleted"));
            }

            destroy_function($warehouse);
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_restore(ProductWarehouse $warehouse): void
    {
        DB::transaction(function () use ($warehouse) {

            if ($warehouse->trashed()) {
                restore_function($warehouse);
            } else {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.restore.not_trashed_forbidden"));
            }
        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_force_destroy(ProductWarehouse $warehouse): void
    {
        DB::transaction(function () use ($warehouse) {

            if ($warehouse->trashed()) {
                // Une entité active ne peut pas être supprimée définitivement
                if ($warehouse->status === ProductWarehouseStatus::Active) {
                    throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.active_forbidden"));
                }
                force_destroy_function($warehouse);
            } else {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.not_trashed_forbidden"));
            }
        });
    }

    // ─────────────────────────────────────────────
    // SYNC TRANSLATIONS (privé)
    // ─────────────────────────────────────────────
    private function syncTranslations(ProductWarehouse $warehouse, array $translations): void
    {
        // Récupère les IDs des langues soumises
        $submittedLangIds = collect($translations)->pluck('lang_id')->toArray();

        // Supprime temporairement les traductions qui ne sont pas présentes dans les données soumises
        ProductWarehouseTranslation::where('warehouse_id', $warehouse->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->delete();

        // Suppression définitive des traductions supprimées (soft delete) déjà en corbeille
        ProductWarehouseTranslation::onlyTrashed()->where('warehouse_id', $warehouse->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->forceDelete();

        // Validation générique factorisée (BackendHelper) :
        //  - conflit nom/slug avec une autre entité (active ou en corbeille) dans la même langue ;
        //  - conflit avec la propre traduction de l'entité, placée en corbeille.
        // Lève une InvalidArgumentException avec un message explicite si besoin.
        assertTranslationUniqueness(
            $warehouse,
            $translations,
            __('v1/backend/others/helpers/backend-helper-messages.entityLabels.warehouse'),
            $this->backendHelperLangPath,
            'name',
            'slug'
        );

        foreach ($translations as $translation) {

            // Vérifie si une traduction existe déjà pour l'entrepôt et la langue
            $existingTranslation = ProductWarehouseTranslation::withTrashed()
                ->where('warehouse_id', $warehouse->id)
                ->where('lang_id', $translation['lang_id'])
                ->first();

            if ($existingTranslation) {

                // Une traduction en corbeille ne doit pas être modifiée silencieusement
                if ($existingTranslation->trashed()) {
                    throw new \InvalidArgumentException(
                        __("{$this->backendLangPath}.trashedTranslationConflict", [
                            'lang_id' => $translation['lang_id'],
                        ])
                    );
                }

                // Met à jour la traduction existante
                $existingTranslation->update([
                    'name' => $translation['name'],
                    'address' => $translation['address'] ?? null,
                    'city' => $translation['city'] ?? null,
                    'phone' => $translation['phone'] ?? null,
                ]);

            } else {

                // Sinon, on crée une nouvelle traduction
                ProductWarehouseTranslation::create([
                    'warehouse_id' => $warehouse->id,
                    'lang_id' => $translation['lang_id'],
                    'name' => $translation['name'],
                    'address' => $translation['address'] ?? null,
                    'city' => $translation['city'] ?? null,
                    'phone' => $translation['phone'] ?? null,
                ]);
            }
        }
    }
}
