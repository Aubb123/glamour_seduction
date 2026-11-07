<?php

namespace App\Services\V1\Controllers;

use App\Enums\Shops\Positions\PositionContext;
use App\Models\Position;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ShopUserService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/shops/shops/messages/shops-messages-service.shopUsers';

    // ─────────────────────────────────────────────
    // STORE (affectation d'un membre du personnel)
    // ─────────────────────────────────────────────
    public function backend_shop_users_store(Shop $shop, array $data): User
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

            // Affectation unique par couple (shop_id, user_id)
            if ($shop->shopUsers()->whereKey($user->getKey())->exists()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.store.duplicate"));
            }

            // Attache le personnel au pivot enrichi `shop_users`
            $shop->shopUsers()->attach($user->getKey(), [
                'uuid' => (string) Str::uuid(),
                'position_id' => $positionId,
                'is_manager' => $isManager,
            ]);

            // Un seul responsable par boutique
            if ($isManager) {
                $this->unsetOtherManagers($shop, $user->getKey());
            }

            return $user;
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE (modification d'une affectation)
    // ─────────────────────────────────────────────
    public function backend_shop_users_update(string $shopUserUuid, array $data): User
    {
        return DB::transaction(function () use ($shopUserUuid, $data) {

            // Le pivot `shop_users` n'a plus de modèle : on résout la ligne par son uuid
            $pivot = DB::table('shop_users')->where('uuid', $shopUserUuid)->firstOrFail();

            $shop = Shop::findOrFail($pivot->shop_id);
            $user = User::findOrFail($pivot->user_id);

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

            if (! empty($updateData)) {
                $shop->shopUsers()->updateExistingPivot($user->getKey(), $updateData);
            }

            if (($updateData['is_manager'] ?? false) === true) {
                $this->unsetOtherManagers($shop, $user->getKey());
            }

            return $user;
        });
    }

    // ─────────────────────────────────────────────
    // DESTROY (retrait d'un membre du personnel)
    // ─────────────────────────────────────────────
    public function backend_shop_users_destroy(string $shopUserUuid): User
    {
        return DB::transaction(function () use ($shopUserUuid) {

            $pivot = DB::table('shop_users')->where('uuid', $shopUserUuid)->firstOrFail();

            $shop = Shop::findOrFail($pivot->shop_id);
            $user = User::findOrFail($pivot->user_id);

            // Une affectation est un simple lien : détachement physique du pivot
            $shop->shopUsers()->detach($user->getKey());

            return $user;
        });
    }

    // ─────────────────────────────────────────────
    // UNSET OTHER MANAGERS (privé) — un seul responsable par boutique
    // ─────────────────────────────────────────────
    private function unsetOtherManagers(Shop $shop, int $userId): void
    {
        DB::table('shop_users')
            ->where('shop_id', $shop->id)
            ->where('user_id', '!=', $userId)
            ->where('is_manager', true)
            ->update(['is_manager' => false]);
    }
}
