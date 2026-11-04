<?php

namespace App\Services\V1\Controllers;

use App\Enums\Products\StockMovements\ProductStockMovementType;
use App\Models\ProductStockMovement;
use App\Models\ProductWarehouseStock;
use Illuminate\Support\Facades\DB;

use function __;

class ProductStockMovementService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/products/stock-movements/messages/stock-movements-messages-service.productStockMovements';

    // ─────────────────────────────────────────────
    // RECORD — cœur métier transactionnel
    // (lit/verrouille la ligne de stock, applique le sens du type,
    //  puis crée le mouvement d'audit avec created_by = auth()->id())
    // ─────────────────────────────────────────────
    public function backend_product_stock_movements_store(array $data): ProductStockMovement
    {
        return DB::transaction(function () use ($data) {

            $type = ProductStockMovementType::tryFrom($data['type']);

            if (! $type) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.record.invalid_type"));
            }

            $quantity = (int) $data['quantity'];

            // Garde : la quantité est toujours strictement positive (le sens est donné par le type)
            if ($quantity <= 0) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.record.quantity_must_be_positive"));
            }

            // 1. Lit et verrouille la ligne de stock (couple entrepôt × variante),
            //    ou la crée à la volée (stock initial = 0) pour un premier mouvement d'entrée.
            $stock = ProductWarehouseStock::withTrashed()
                ->where('warehouse_id', $data['warehouse_id'])
                ->where('product_variant_id', $data['product_variant_id'])
                ->lockForUpdate()
                ->first();

            if (! $stock) {
                $stock = ProductWarehouseStock::create([
                    'warehouse_id' => $data['warehouse_id'],
                    'product_variant_id' => $data['product_variant_id'],
                    'quantity' => 0,
                    'reserved_quantity' => 0,
                    'low_stock_threshold' => 5,
                ]);
            } elseif ($stock->trashed()) {
                $stock->restore();
            }

            // 2. Applique le sens du type : +quantity (in/return/transfer_in) ou -quantity (out/adjustment/transfer_out)
            //    avec garde : jamais de stock négatif.
            $newQuantity = $stock->quantity + ($type->decreasesStock() ? -$quantity : $quantity);

            if ($newQuantity < 0) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.record.negative_stock_forbidden", ['stock' => $stock->quantity]));
            }

            $stock->update(['quantity' => $newQuantity]);

            // 3. Crée le mouvement d'audit (immuable : jamais modifiable, seulement soft deletable)
            return ProductStockMovement::create([
                'warehouse_id' => $data['warehouse_id'],
                'product_variant_id' => $data['product_variant_id'],
                'type' => $type,
                'quantity' => $quantity,
                'reference_type' => $data['reference_type'] ?? null,
                'reference_id' => $data['reference_id'] ?? null,
                'note' => $data['note'] ?? null,
                'created_by' => $data['created_by'] ?? auth()->id(),
            ]);
        });
    }

    // ─────────────────────────────────────────────
    // DESTROY (soft delete) — seul mode de retrait autorisé (audit fiable)
    // ─────────────────────────────────────────────
    public function backend_product_stock_movements_destroy(ProductStockMovement $movement): void
    {
        DB::transaction(function () use ($movement) {

            // Empêche la suppression double (soft delete)
            if ($movement->trashed()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.already_deleted"));
            }

            destroy_function($movement);
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_product_stock_movements_restore(ProductStockMovement $movement): void
    {
        DB::transaction(function () use ($movement) {

            if ($movement->trashed()) {
                restore_function($movement);
            } else {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.restore.not_trashed_forbidden"));
            }
        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_stock_movements_force_destroy(ProductStockMovement $movement): void
    {
        DB::transaction(function () use ($movement) {

            if ($movement->trashed()) {
                force_destroy_function($movement);
            } else {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.not_trashed_forbidden"));
            }
        });
    }
}
