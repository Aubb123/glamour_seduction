<?php

namespace App\Services\V1\Controllers;

use App\Models\ProductWarehouseStock;
use Illuminate\Support\Facades\DB;

use function __;

class ProductWarehouseStockService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/products/stocks/messages/stocks-messages-service.productWarehouseStocks';

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_stocks_store(array $data): ProductWarehouseStock
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        return DB::transaction(function () use ($data) {

            // Garde : une seule ligne par couple (entrepôt, variante) — contrainte unique DB
            $exists = ProductWarehouseStock::withTrashed()
                ->where('warehouse_id', $data['warehouse_id'])
                ->where('product_variant_id', $data['product_variant_id'])
                ->exists();

            if ($exists) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.store.pair_already_exists"));
            }

            // Création du stock
            return ProductWarehouseStock::create([
                'warehouse_id' => $data['warehouse_id'],
                'product_variant_id' => $data['product_variant_id'],
                'quantity' => $data['quantity'],
                'reserved_quantity' => $data['reserved_quantity'] ?? 0,
                'low_stock_threshold' => $data['low_stock_threshold'],
            ]);
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_stocks_update(ProductWarehouseStock $stock, array $data): ProductWarehouseStock
    {
        return DB::transaction(function () use ($stock, $data) {

            $updateData = [];

            if (isset($data['warehouse_id'])) {
                $updateData['warehouse_id'] = $data['warehouse_id'];
            }

            if (isset($data['product_variant_id'])) {
                $updateData['product_variant_id'] = $data['product_variant_id'];
            }

            if (isset($data['quantity'])) {
                $updateData['quantity'] = $data['quantity'];
            }

            if (array_key_exists('reserved_quantity', $data)) {
                $updateData['reserved_quantity'] = $data['reserved_quantity'] ?? 0;
            }

            if (isset($data['low_stock_threshold'])) {
                $updateData['low_stock_threshold'] = $data['low_stock_threshold'];
            }

            // Garde : si le couple (entrepôt, variante) change, il ne doit pas exister ailleurs
            if (isset($updateData['warehouse_id'], $updateData['product_variant_id'])) {
                $exists = ProductWarehouseStock::withTrashed()
                    ->where('id', '!=', $stock->id)
                    ->where('warehouse_id', $updateData['warehouse_id'])
                    ->where('product_variant_id', $updateData['product_variant_id'])
                    ->exists();

                if ($exists) {
                    throw new \InvalidArgumentException(__("{$this->backendLangPath}.update.pair_already_exists"));
                }
            }

            $stock->update($updateData);

            return $stock;
        });
    }

    // ─────────────────────────────────────────────
    // DESTROY (soft delete)
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_stocks_destroy(ProductWarehouseStock $stock): void
    {
        DB::transaction(function () use ($stock) {

            // Empêche la suppression double (soft delete)
            if ($stock->trashed()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.already_deleted"));
            }

            destroy_function($stock);
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_stocks_restore(ProductWarehouseStock $stock): void
    {
        DB::transaction(function () use ($stock) {

            if ($stock->trashed()) {
                restore_function($stock);
            } else {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.restore.not_trashed_forbidden"));
            }
        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_stocks_force_destroy(ProductWarehouseStock $stock): void
    {
        DB::transaction(function () use ($stock) {

            if ($stock->trashed()) {
                force_destroy_function($stock);
            } else {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.not_trashed_forbidden"));
            }
        });
    }
}
