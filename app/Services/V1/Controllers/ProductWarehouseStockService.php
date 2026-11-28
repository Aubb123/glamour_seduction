<?php

namespace App\Services\V1\Controllers;

use App\Enums\Products\Warehouses\ProductWarehouseStockTrackingMode;
use App\Models\ProductVariant;
use App\Models\ProductWarehouse;
use App\Models\ProductWarehouseStock;
use Illuminate\Support\Facades\DB;

use function __;

class ProductWarehouseStockService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/products/warehouse-stocks/messages/warehouse-stocks-messages-service.warehouseStocks';

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_stocks_store(array $data): ProductWarehouseStock
    {
        return DB::transaction(function () use ($data) {

            $warehouse = ProductWarehouse::findOrFail($data['warehouse_id']);

            // Un stock global n'est possible que pour un entrepôt en mode « warehouse ».
            $this->assertWarehouseTracked($warehouse);

            $variant = ProductVariant::findOrFail($data['product_variant_id']);

            // Un solde unique par couple (entrepôt, variante) : on refuse un doublon.
            $exists = ProductWarehouseStock::where('warehouse_id', $warehouse->id)
                ->where('product_variant_id', $variant->id)
                ->exists();

            if ($exists) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.store.already_exists"));
            }

            // Quantité refusée si négative (règle métier : pas de stock négatif à cette étape).
            $quantity = (int) ($data['quantity'] ?? 0);

            if ($quantity < 0) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.store.negative_quantity"));
            }

            return ProductWarehouseStock::create([
                'warehouse_id' => $warehouse->id,
                'product_variant_id' => $variant->id,
                'quantity' => $quantity,
            ]);
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_stocks_update(ProductWarehouseStock $stock, array $data): ProductWarehouseStock
    {
        return DB::transaction(function () use ($stock, $data) {

            $warehouse = $stock->warehouse;

            // Un stock global ne peut être modifié que pour un entrepôt en mode « warehouse ».
            $this->assertWarehouseTracked($warehouse);

            // Quantité refusée si négative.
            $quantity = (int) ($data['quantity'] ?? $stock->quantity);

            if ($quantity < 0) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.update.negative_quantity"));
            }

            $stock->update([
                'quantity' => $quantity,
            ]);

            return $stock;
        });
    }

    // ─────────────────────────────────────────────
    // DESTROY (soft delete)
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_stocks_destroy(ProductWarehouseStock $stock): void
    {
        DB::transaction(function () use ($stock) {

            // Un stock global ne peut être supprimé que pour un entrepôt en mode « warehouse ».
            $this->assertWarehouseTracked($stock->warehouse);

            // Suppression via le helper du projet (soft delete + gestion des relations dépendantes).
            destroy_function($stock);
        });
    }

    // ─────────────────────────────────────────────
    // ASSERT WAREHOUSE TRACKED (privé)
    // ─────────────────────────────────────────────
    private function assertWarehouseTracked(ProductWarehouse $warehouse): void
    {
        // Vérifie que l'entrepôt est bien configuré pour un suivi global du stock.
        if ($warehouse->stock_tracking_mode !== ProductWarehouseStockTrackingMode::Warehouse) {
            throw new \InvalidArgumentException(__("{$this->backendLangPath}.warehouse_not_tracked"));
        }
    }
}
