<?php

namespace App\Services\V1\Controllers;

use App\Enums\Products\Warehouses\ProductWarehouseStatus;
use App\Models\ProductWarehouse;
use Illuminate\Support\Facades\DB;

use function __;

class ProductWarehouseService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/products/warehouses/messages/warehouses-messages-service.productWarehouses';

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function store(array $data): ProductWarehouse
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        return DB::transaction(function () use ($data) {

            // Le premier entrepôt créé devient automatiquement l'entrepôt par défaut
            $hasWarehouses = ProductWarehouse::count() > 0;
            $isDefault = ! $hasWarehouses || ($data['is_default'] ?? null) === '1' || ($data['is_default'] ?? false) === true;

            // Création de l'entrepôt
            $warehouse = ProductWarehouse::create([
                'code' => $data['code'],
                'name' => $data['name'],
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'phone' => $data['phone'] ?? null,
                'status' => ($data['status'] ?? null) === ProductWarehouseStatus::Active->value ? ProductWarehouseStatus::Active : ProductWarehouseStatus::Inactive,
                'is_default' => $isDefault,
            ]);

            // Gère l'unicité de l'entrepôt par défaut (un seul is_default)
            if ($isDefault) {
                $this->unsetOtherDefaults($warehouse);
            }

            return $warehouse;
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function update(ProductWarehouse $warehouse, array $data): ProductWarehouse
    {
        return DB::transaction(function () use ($warehouse, $data) {

            $updateData = [];

            if (isset($data['code'])) {
                $updateData['code'] = $data['code'];
            }

            if (isset($data['name'])) {
                $updateData['name'] = $data['name'];
            }

            if (array_key_exists('address', $data)) {
                $updateData['address'] = $data['address'] ?: null;
            }

            if (array_key_exists('city', $data)) {
                $updateData['city'] = $data['city'] ?: null;
            }

            if (array_key_exists('phone', $data)) {
                $updateData['phone'] = $data['phone'] ?: null;
            }

            if (isset($data['status'])) {
                $updateData['status'] = ($data['status'] ?? null) === ProductWarehouseStatus::Active->value ? ProductWarehouseStatus::Active : ProductWarehouseStatus::Inactive;
            }

            // Si is_default est explicitement false → on dé-sélectionne.
            // NB : on ne peut pas dé-sélectionner un entrepôt par défaut s'il est le dernier actif.
            if (array_key_exists('is_default', $data)) {
                $updateData['is_default'] = ($data['is_default'] ?? null) === '1' || ($data['is_default'] ?? false) === true;
            }

            $warehouse->update($updateData);

            // Gère l'unicité de l'entrepôt par défaut (un seul is_default)
            if ($warehouse->is_default) {
                $this->unsetOtherDefaults($warehouse);
            }

            return $warehouse;
        });
    }

    // ─────────────────────────────────────────────
    // UNSET OTHER DEFAULTS (privé) — un seul entrepôt par défaut
    // ─────────────────────────────────────────────
    private function unsetOtherDefaults(ProductWarehouse $warehouse): void
    {
        ProductWarehouse::where('id', '!=', $warehouse->id)
            ->where('is_default', true)
            ->update(['is_default' => false]);
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function activate(ProductWarehouse $warehouse): void
    {
        DB::transaction(function () use ($warehouse) {

            if ($warehouse->status === ProductWarehouseStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->langPath}.activate.already_active"));
            }

            $warehouse->update(['status' => ProductWarehouseStatus::Active]);
        });
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function deactivate(ProductWarehouse $warehouse): void
    {
        DB::transaction(function () use ($warehouse) {

            if ($warehouse->status === ProductWarehouseStatus::Inactive) {
                throw new \InvalidArgumentException(__("{$this->langPath}.deactivate.already_inactive"));
            }

            $warehouse->update(['status' => ProductWarehouseStatus::Inactive]);
        });
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function destroy(ProductWarehouse $warehouse): void
    {
        DB::transaction(function () use ($warehouse) {

            // Empêche la suppression d'un entrepôt actif (règle métier : le désactiver d'abord)
            if ($warehouse->status === ProductWarehouseStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.active_forbidden"));
            }

            // NB : destroy refusé si stocks existent (relation ajoutée au module 8)

            // Empêche la suppression double (soft delete)
            if ($warehouse->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.already_deleted"));
            }

            destroy_function($warehouse);
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function restore(ProductWarehouse $warehouse): void
    {
        DB::transaction(function () use ($warehouse) {

            if ($warehouse->trashed()) {
                restore_function($warehouse);
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.restore.not_trashed_forbidden"));
            }
        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function forceDestroy(ProductWarehouse $warehouse): void
    {
        DB::transaction(function () use ($warehouse) {

            if ($warehouse->trashed()) {
                force_destroy_function($warehouse);
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.not_trashed_forbidden"));
            }
        });
    }
}