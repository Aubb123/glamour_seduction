<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Products\WarehouseUsers\StoreWarehouseUserRequest;
use App\Http\Requests\V1\Products\WarehouseUsers\UpdateWarehouseUserRequest;
use App\Models\ProductWarehouse;
use App\Services\V1\Controllers\WarehouseUserService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;

use function __;

class WarehouseUserController extends Controller
{
    use LogActivity;

    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/products/warehouses/messages/warehouses-messages-controller.warehouseUsers';

    public function __construct(protected WarehouseUserService $warehouseUserService) {}

    // ─────────────────────────────────────────────
    // STORE (affecter un membre du personnel)
    // ─────────────────────────────────────────────
    public function backend_warehouse_users_store(StoreWarehouseUserRequest $request, ProductWarehouse $productWarehouse): RedirectResponse
    {
        try {

            $user = $this->warehouseUserService->backend_warehouse_users_store($productWarehouse, $request->validated());

            $userName = trim(($user->first_name ?? '').' '.($user->last_name ?? ''));

            $this->logActivity(type: 'info', action: 'backend.warehouse-users.store', context: ['warehouse_uuid' => $productWarehouse->uuid, 'user_uuid' => $user->uuid], message: null, activity: __("{$this->backendLangPath}.store.success", ['name' => $userName]), is_success: true, loggable: $productWarehouse);

            return back()->with('success', __("{$this->backendLangPath}.store.flash_success", ['name' => $userName]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.warehouse-users.store', context: ['warehouse_uuid' => $productWarehouse->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.warehouse-users.store', context: ['warehouse_uuid' => $productWarehouse->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.not_found"), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.store.not_found"));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.warehouse-users.store', context: ['warehouse_uuid' => $productWarehouse->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.store.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // UPDATE (modifier une affectation)
    // ─────────────────────────────────────────────
    public function backend_warehouse_users_update(UpdateWarehouseUserRequest $request, string $warehouseUser): RedirectResponse
    {
        try {

            // NB : le pivot `warehouse_users` n'a plus de modèle : la route transmet
            // l'uuid de la ligne pivot (paramètre {warehouseUser}).
            $user = $this->warehouseUserService->backend_warehouse_users_update($warehouseUser, $request->validated());

            $userName = trim(($user->first_name ?? '').' '.($user->last_name ?? ''));

            $this->logActivity(type: 'info', action: 'backend.warehouse-users.update', context: ['warehouse_user_uuid' => $warehouseUser], message: null, activity: __("{$this->backendLangPath}.update.success", ['name' => $userName]), is_success: true);

            return back()->with('success', __("{$this->backendLangPath}.update.flash_success", ['name' => $userName]));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.warehouse-users.update', context: ['warehouse_user_uuid' => $warehouseUser], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.not_found"), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.update.not_found"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.warehouse-users.update', context: ['warehouse_user_uuid' => $warehouseUser], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.warning_meta", ['uuid' => $warehouseUser]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.warehouse-users.update', context: ['warehouse_user_uuid' => $warehouseUser], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.error", ['uuid' => $warehouseUser]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.update.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY (retirer un membre du personnel)
    // ─────────────────────────────────────────────
    public function backend_warehouse_users_destroy(string $warehouseUser): RedirectResponse
    {
        try {

            $user = $this->warehouseUserService->backend_warehouse_users_destroy($warehouseUser);

            $userName = trim(($user->first_name ?? '').' '.($user->last_name ?? ''));

            $this->logActivity(type: 'info', action: 'backend.warehouse-users.destroy', context: ['warehouse_user_uuid' => $warehouseUser], message: null, activity: __("{$this->backendLangPath}.destroy.success", ['name' => $userName]), is_success: true);

            return back()->with('success', __("{$this->backendLangPath}.destroy.flash_success", ['name' => $userName]));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.warehouse-users.destroy', context: ['warehouse_user_uuid' => $warehouseUser], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.not_found"), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.destroy.not_found"));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.warehouse-users.destroy', context: ['warehouse_user_uuid' => $warehouseUser], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.error", ['uuid' => $warehouseUser]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.destroy.flash_error"));
        }
    }
}
