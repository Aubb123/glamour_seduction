<?php

namespace App\Services\V1\Controllers;

use App\Models\ProductWarehouse;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WarehouseUserService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/products/warehouses/messages/warehouses-messages-service.warehouseUsers';

    // ─────────────────────────────────────────────
    // STORE (affectation d'un membre du personnel)
    // ─────────────────────────────────────────────
    public function backend_warehouse_users_store(ProductWarehouse $warehouse, array $data): User
    {
        return DB::transaction(function () use ($warehouse, $data) {

            // Résout l'utilisateur par UUID
            $user = User::where('uuid', $data['user_uuid'])->firstOrFail();

            $isManager = ($data['is_manager'] ?? null) === '1' || ($data['is_manager'] ?? false) === true;

            // Affectation unique par couple (warehouse_id, user_id)
            if ($warehouse->warehouseUsers()->whereKey($user->getKey())->exists()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.store.duplicate"));
            }

            $warehouse->warehouseUsers()->attach($user->getKey(), [
                'uuid' => (string) Str::uuid(),
                'is_manager' => $isManager,
            ]);

            if ($isManager) {
                $this->unsetOtherManagers($warehouse, $user->getKey());
            }

            return $user;
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_warehouse_users_update(string $warehouseUserUuid, array $data): User
    {
        return DB::transaction(function () use ($warehouseUserUuid, $data) {

            $pivot = DB::table('warehouse_users')->where('uuid', $warehouseUserUuid)->firstOrFail();

            $warehouse = ProductWarehouse::findOrFail($pivot->warehouse_id);
            $user = User::findOrFail($pivot->user_id);

            $updateData = [];

            if (array_key_exists('is_manager', $data)) {
                $updateData['is_manager'] = ($data['is_manager'] ?? null) === '1' || ($data['is_manager'] ?? false) === true;
            }

            if (! empty($updateData)) {
                $warehouse->warehouseUsers()->updateExistingPivot($user->getKey(), $updateData);
            }

            if (($updateData['is_manager'] ?? false) === true) {
                $this->unsetOtherManagers($warehouse, $user->getKey());
            }

            return $user;
        });
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_warehouse_users_destroy(string $warehouseUserUuid): User
    {
        return DB::transaction(function () use ($warehouseUserUuid) {

            $pivot = DB::table('warehouse_users')->where('uuid', $warehouseUserUuid)->firstOrFail();

            $warehouse = ProductWarehouse::findOrFail($pivot->warehouse_id);
            $user = User::findOrFail($pivot->user_id);

            $warehouse->warehouseUsers()->detach($user->getKey());

            return $user;
        });
    }

    // ─────────────────────────────────────────────
    // UNSET OTHER MANAGERS (privé) — un seul responsable par entrepôt
    // ─────────────────────────────────────────────
    private function unsetOtherManagers(ProductWarehouse $warehouse, int $userId): void
    {
        DB::table('warehouse_users')
            ->where('warehouse_id', $warehouse->id)
            ->where('user_id', '!=', $userId)
            ->where('is_manager', true)
            ->update(['is_manager' => false]);
    }
}
