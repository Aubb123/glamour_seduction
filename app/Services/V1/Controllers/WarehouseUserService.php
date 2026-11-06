<?php

namespace App\Services\V1\Controllers;

use App\Models\ProductWarehouse;
use App\Models\User;
use App\Models\WarehouseUser;
use Illuminate\Support\Facades\DB;

class WarehouseUserService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/products/warehouses/messages/warehouses-messages-service.warehouseUsers';

    // ─────────────────────────────────────────────
    // STORE (affectation d'un membre du personnel)
    // ─────────────────────────────────────────────
    public function backend_warehouse_users_store(ProductWarehouse $warehouse, array $data): WarehouseUser
    {
        return DB::transaction(function () use ($warehouse, $data) {

            // Résout l'utilisateur par UUID
            $user = User::where('uuid', $data['user_uuid'])->firstOrFail();

            // Résout le poste (optionnel)
            $positionId = null;

            if (! empty($data['position_uuid'])) {
                $position = \App\Models\Position::where('uuid', $data['position_uuid'])->firstOrFail();
                $positionId = $position->id;
            }

            $isManager = ($data['is_manager'] ?? null) === '1' || ($data['is_manager'] ?? false) === true;

            $warehouseUser = WarehouseUser::create([
                'warehouse_id' => $warehouse->id,
                'user_id' => $user->id,
                'position_id' => $positionId,
                'is_manager' => $isManager,
            ]);

            if ($isManager) {
                $this->unsetOtherManagers($warehouse, $warehouseUser);
            }

            return $warehouseUser;
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_warehouse_users_update(WarehouseUser $warehouseUser, array $data): WarehouseUser
    {
        return DB::transaction(function () use ($warehouseUser, $data) {

            $updateData = [];

            if (array_key_exists('position_uuid', $data)) {

                if (empty($data['position_uuid'])) {
                    $updateData['position_id'] = null;
                } else {
                    $position = \App\Models\Position::where('uuid', $data['position_uuid'])->firstOrFail();
                    $updateData['position_id'] = $position->id;
                }
            }

            if (array_key_exists('is_manager', $data)) {
                $updateData['is_manager'] = ($data['is_manager'] ?? null) === '1' || ($data['is_manager'] ?? false) === true;
            }

            $warehouseUser->update($updateData);

            if ($warehouseUser->is_manager) {
                $this->unsetOtherManagers($warehouseUser->warehouse, $warehouseUser);
            }

            return $warehouseUser;
        });
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_warehouse_users_destroy(WarehouseUser $warehouseUser): void
    {
        DB::transaction(function () use ($warehouseUser) {
            $warehouseUser->delete();
        });
    }

    // ─────────────────────────────────────────────
    // UNSET OTHER MANAGERS (privé) — un seul responsable par entrepôt
    // ─────────────────────────────────────────────
    private function unsetOtherManagers(ProductWarehouse $warehouse, WarehouseUser $current): void
    {
        WarehouseUser::where('warehouse_id', $warehouse->id)
            ->where('id', '!=', $current->id)
            ->where('is_manager', true)
            ->update(['is_manager' => false]);
    }
}
