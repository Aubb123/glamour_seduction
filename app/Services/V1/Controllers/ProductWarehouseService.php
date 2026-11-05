<?php

namespace App\Services\V1\Controllers;

use App\Enums\Products\Warehouses\ProductWarehouseStatus;
use App\Models\ProductWarehouse;
use Illuminate\Support\Facades\DB;

use function __;

class ProductWarehouseService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/products/warehouses/messages/warehouses-messages-service.productWarehouses';

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_store(array $data): ProductWarehouse
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
    public function backend_product_warehouses_update(ProductWarehouse $warehouse, array $data): ProductWarehouse
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

            // Gestion du statut par défaut.
            // NB : on ne peut pas retirer le statut « par défaut » au dernier entrepôt
            // par défaut restant (sinon la boutique n'a plus aucun entrepôt par défaut).
            if (array_key_exists('is_default', $data)) {

                $wantsDefault = ($data['is_default'] ?? null) === '1' || ($data['is_default'] ?? false) === true;

                if (! $wantsDefault && $warehouse->is_default && $this->isLastDefault($warehouse)) {
                    throw new \InvalidArgumentException(__("{$this->backendLangPath}.update.default_forbidden"));
                }

                $updateData['is_default'] = $wantsDefault;
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
    // IS LAST DEFAULT (privé) — vrai si l'entrepôt est le seul par défaut restant
    // ─────────────────────────────────────────────
    private function isLastDefault(ProductWarehouse $warehouse): bool
    {
        return ProductWarehouse::where('id', '!=', $warehouse->id)
            ->where('is_default', true)
            ->doesntExist();
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

            // Un entrepôt par défaut ne peut pas être désactivé s'il est le dernier par défaut :
            // la boutique perdrait son entrepôt de réservation par défaut.
            if ($warehouse->is_default && $this->isLastDefault($warehouse)) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.deactivate.default_forbidden"));
            }

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

            // NB : destroy refusé si des stocks ou des mouvements existent
            // (géré par BackendHelper::destroy_function via $dependentRelations = ['stocks', 'movements']).

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
                force_destroy_function($warehouse);
            } else {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.not_trashed_forbidden"));
            }
        });
    }
}
