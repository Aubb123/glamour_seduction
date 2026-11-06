<?php

namespace App\Services\V1\Controllers;

use App\Enums\Shops\Positions\PositionContext;
use App\Models\Position;
use App\Models\Shop;
use App\Models\ShopUser;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ShopUserService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/shops/shops/messages/shops-messages-service.shopUsers';

    // ─────────────────────────────────────────────
    // STORE (affectation d'un membre du personnel)
    // ─────────────────────────────────────────────
    public function backend_shop_users_store(Shop $shop, array $data): ShopUser
    {
        return DB::transaction(function () use ($shop, $data) {

            // Résout l'utilisateur par UUID (jamais par ID direct)
            $user = User::where('uuid', $data['user_uuid'])->firstOrFail();

            // Vérifie le poste fourni (optionnel) : il doit exister et être compatible
            $positionId = null;

            if (! empty($data['position_uuid'])) {
                $position = Position::where('uuid', $data['position_uuid'])->firstOrFail();
                $positionId = $position->id;
            }

            $isManager = ($data['is_manager'] ?? null) === '1' || ($data['is_manager'] ?? false) === true;

            // Affectation : unique par couple (shop_id, user_id)
            $shopUser = ShopUser::create([
                'shop_id' => $shop->id,
                'user_id' => $user->id,
                'position_id' => $positionId,
                'is_manager' => $isManager,
            ]);

            // Un seul responsable par boutique
            if ($isManager) {
                $this->unsetOtherManagers($shop, $shopUser);
            }

            return $shopUser;
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE (modification d'une affectation)
    // ─────────────────────────────────────────────
    public function backend_shop_users_update(ShopUser $shopUser, array $data): ShopUser
    {
        return DB::transaction(function () use ($shopUser, $data) {

            $updateData = [];

            if (array_key_exists('position_uuid', $data)) {

                if (empty($data['position_uuid'])) {
                    $updateData['position_id'] = null;
                } else {
                    $position = Position::where('uuid', $data['position_uuid'])->firstOrFail();
                    $updateData['position_id'] = $position->id;
                }
            }

            if (array_key_exists('is_manager', $data)) {
                $updateData['is_manager'] = ($data['is_manager'] ?? null) === '1' || ($data['is_manager'] ?? false) === true;
            }

            $shopUser->update($updateData);

            if ($shopUser->is_manager) {
                $this->unsetOtherManagers($shopUser->shop, $shopUser);
            }

            return $shopUser;
        });
    }

    // ─────────────────────────────────────────────
    // DESTROY (retrait d'un membre du personnel)
    // ─────────────────────────────────────────────
    public function backend_shop_users_destroy(ShopUser $shopUser): void
    {
        DB::transaction(function () use ($shopUser) {
            // Une affectation est un simple lien : suppression physique directe
            // (pas de soft delete sur les pivots de personnel).
            $shopUser->delete();
        });
    }

    // ─────────────────────────────────────────────
    // UNSET OTHER MANAGERS (privé) — un seul responsable par boutique
    // ─────────────────────────────────────────────
    private function unsetOtherManagers(Shop $shop, ShopUser $current): void
    {
        ShopUser::where('shop_id', $shop->id)
            ->where('id', '!=', $current->id)
            ->where('is_manager', true)
            ->update(['is_manager' => false]);
    }
}
