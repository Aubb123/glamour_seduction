<?php

namespace App\Services\V1\Controllers;

use App\Enums\Products\Warehouses\ProductWarehouseStockTrackingMode;
use App\Models\ProductVariant;
use App\Models\ProductWarehouseLocation;
use App\Models\ProductWarehouseLocationStock;
use Illuminate\Support\Facades\DB;

use function __;

class ProductWarehouseLocationStockService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/products/warehouse-location-stocks/messages/warehouse-location-stocks-messages-service.warehouseLocationStocks';

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_location_stocks_store(array $data): ProductWarehouseLocationStock
    {
        return DB::transaction(function () use ($data) {

            $location = ProductWarehouseLocation::with('warehouse')->findOrFail($data['location_id']);

            // Le stock par emplacement n'est permis que pour un entrepôt en mode « location ».
            $this->assertLocationTracked($location);

            // Si un entrepôt est fourni, on vérifie qu'il correspond bien à l'emplacement.
            if (isset($data['warehouse_id'])) {
                $this->assertWarehouseMatchesLocation($location, (int) $data['warehouse_id']);
            }

            // Un solde unique par couple (emplacement, variante) : on refuse un doublon.
            $variant = ProductVariant::findOrFail($data['product_variant_id']);

            // Un solde unique par couple (emplacement, variante) : on refuse un doublon.
            $exists = ProductWarehouseLocationStock::where('location_id', $location->id)
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

            return ProductWarehouseLocationStock::create([
                'location_id' => $location->id,
                'product_variant_id' => $variant->id,
                'quantity' => $quantity,
            ]);
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_location_stocks_update(ProductWarehouseLocationStock $productWarehouseLocationStock, array $data): ProductWarehouseLocationStock
    {
        return DB::transaction(function () use ($productWarehouseLocationStock, $data) {

            $location = $productWarehouseLocationStock->location;

            // Le stock par emplacement ne peut être modifié que pour un entrepôt en mode « location ».
            $this->assertLocationTracked($location);

            // Quantité refusée si négative.
            $quantity = (int) ($data['quantity'] ?? $productWarehouseLocationStock->quantity);

            if ($quantity < 0) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.update.negative_quantity"));
            }

            $productWarehouseLocationStock->update([
                'quantity' => $quantity,
            ]);

            return $productWarehouseLocationStock;
        });
    }

    // ─────────────────────────────────────────────
    // DESTROY (soft delete)
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_location_stocks_destroy(ProductWarehouseLocationStock $productWarehouseLocationStock): void
    {
        DB::transaction(function () use ($productWarehouseLocationStock) {

            // Le stock par emplacement ne peut être supprimé que pour un entrepôt en mode « location ».
            $this->assertLocationTracked($productWarehouseLocationStock->location);

            // Suppression via le helper du projet (soft delete + gestion des relations dépendantes).
            destroy_function($productWarehouseLocationStock);
        });
    }

    // ─────────────────────────────────────────────
    // ASSERT LOCATION TRACKED (privé)
    // ─────────────────────────────────────────────
    private function assertLocationTracked(ProductWarehouseLocation $location): void
    {
        // Vérifie que l'entrepôt parent est bien configuré pour un suivi par emplacement.
        if ($location->warehouse?->stock_tracking_mode !== ProductWarehouseStockTrackingMode::Location) {
            throw new \InvalidArgumentException(__("{$this->backendLangPath}.warehouse_not_tracked"));
        }
    }

    // ─────────────────────────────────────────────
    // ASSERT WAREHOUSE MATCHES LOCATION (privé)
    // ─────────────────────────────────────────────
    private function assertWarehouseMatchesLocation(ProductWarehouseLocation $location, int $warehouseId): void
    {
        // L'entrepôt doit correspondre à celui de l'emplacement (pas de confiance au navigateur).
        if ((int) $location->warehouse_id !== $warehouseId) {
            throw new \InvalidArgumentException(__("{$this->backendLangPath}.warehouse_mismatch"));
        }
    }
}
