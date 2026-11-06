<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Shops\ShopUsers\StoreShopUserRequest;
use App\Http\Requests\V1\Shops\ShopUsers\UpdateShopUserRequest;
use App\Models\Shop;
use App\Models\ShopUser;
use App\Services\V1\Controllers\ShopUserService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;

use function __;

class ShopUserController extends Controller
{
    use LogActivity;

    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/shops/shops/messages/shops-messages-controller.shopUsers';

    public function __construct(protected ShopUserService $shopUserService) {}

    // ─────────────────────────────────────────────
    // STORE (affecter un membre du personnel)
    // ─────────────────────────────────────────────
    public function backend_shop_users_store(StoreShopUserRequest $request, Shop $shop): RedirectResponse
    {
        try {

            $shopUser = $this->shopUserService->backend_shop_users_store($shop, $request->validated());

            $userName = trim(($shopUser->user?->first_name ?? '').' '.($shopUser->user?->last_name ?? ''));

            $this->logActivity(type: 'info', action: 'backend.shop-users.store', context: ['shop_uuid' => $shop->uuid, 'shop_user_uuid' => $shopUser->uuid], message: null, activity: __("{$this->backendLangPath}.store.success", ['name' => $userName]), is_success: true, loggable: $shop);

            return back()->with('success', __("{$this->backendLangPath}.store.flash_success", ['name' => $userName]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.shop-users.store', context: ['shop_uuid' => $shop->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.shop-users.store', context: ['shop_uuid' => $shop->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.not_found"), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.store.not_found"));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.shop-users.store', context: ['shop_uuid' => $shop->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.store.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // UPDATE (modifier une affectation)
    // ─────────────────────────────────────────────
    public function backend_shop_users_update(UpdateShopUserRequest $request, ShopUser $shopUser): RedirectResponse
    {
        try {

            $this->shopUserService->backend_shop_users_update($shopUser, $request->validated());

            $userName = trim(($shopUser->user?->first_name ?? '').' '.($shopUser->user?->last_name ?? ''));

            $this->logActivity(type: 'info', action: 'backend.shop-users.update', context: ['shop_user_uuid' => $shopUser->uuid], message: null, activity: __("{$this->backendLangPath}.update.success", ['name' => $userName]), is_success: true);

            return back()->with('success', __("{$this->backendLangPath}.update.flash_success", ['name' => $userName]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.shop-users.update', context: ['shop_user_uuid' => $shopUser->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.warning_meta", ['uuid' => $shopUser->uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.shop-users.update', context: ['shop_user_uuid' => $shopUser->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.error", ['uuid' => $shopUser->uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.update.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY (retirer un membre du personnel)
    // ─────────────────────────────────────────────
    public function backend_shop_users_destroy(ShopUser $shopUser): RedirectResponse
    {
        try {

            $userName = trim(($shopUser->user?->first_name ?? '').' '.($shopUser->user?->last_name ?? ''));

            $this->shopUserService->backend_shop_users_destroy($shopUser);

            $this->logActivity(type: 'info', action: 'backend.shop-users.destroy', context: ['shop_user_uuid' => $shopUser->uuid], message: null, activity: __("{$this->backendLangPath}.destroy.success", ['name' => $userName]), is_success: true);

            return back()->with('success', __("{$this->backendLangPath}.destroy.flash_success", ['name' => $userName]));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.shop-users.destroy', context: ['shop_user_uuid' => $shopUser->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.error", ['uuid' => $shopUser->uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.destroy.flash_error"));
        }
    }
}
