<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Products\Warehouses\StoreProductWarehouseRequest;
use App\Http\Requests\V1\Products\Warehouses\UpdateProductWarehouseRequest;
use App\Models\ProductWarehouse;
use App\Services\V1\Controllers\ProductWarehouseService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Alias pour les chemins de traduction
use function __;

class ProductWarehouseController extends Controller
{
    use LogActivity; // Trait pour le logging d'activité

    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/products/warehouses/messages/warehouses-messages-controller.productWarehouses';

    // Injection de dépendance du service de gestion des entrepôts
    public function __construct(protected ProductWarehouseService $productWarehouseService) {}

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_index(): View
    {
        try {

            // ✅ Récupère tous les entrepôts, triés par date de création décroissante
            $warehouses = ProductWarehouse::withCount(['audits', 'activityLogs'])->orderBy('created_at', 'desc')->get();

            // Log de l'accès à la liste des entrepôts
            $this->logActivity(type: 'info', action: 'backend.product-warehouses.index', context: ['count' => $warehouses->count()], message: null, activity: __("{$this->langPath}.index.success"), is_success: true);

            // Affiche la vue avec les entrepôts récupérés
            return view('v1.backend.pages.products.warehouses.index', compact('warehouses'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur avec le message d'exception
            $this->logActivity(type: 'error', action: 'backend.product-warehouses.index', context: [], message: $e->getMessage(), activity: __("{$this->langPath}.index.error"), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération des entrepôts
        }
    }

    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_create(): View
    {
        try {

            // Log de l'accès à la page de création d'un entrepôt
            $this->logActivity(type: 'info', action: 'backend.product-warehouses.create', context: [], message: null, activity: __("{$this->langPath}.create.success"), is_success: true);

            // Affiche la vue de création d'un entrepôt
            return view('v1.backend.pages.products.warehouses.create');

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de l'accès à la page de création d'un entrepôt
            $this->logActivity(type: 'error', action: 'backend.product-warehouses.create', context: [], message: $e->getMessage(), activity: __("{$this->langPath}.create.error"), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de l'accès à la page de création
        }
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_store(StoreProductWarehouseRequest $request): RedirectResponse
    {
        try {

            // Création de l'entrepôt via le service, en passant les données validées du formulaire
            $warehouse = $this->productWarehouseService->backend_product_warehouses_store($request->validated());

            // Log de la création de l'entrepôt avec le code de l'entrepôt créé
            $this->logActivity(type: 'info', action: 'backend.product-warehouses.store', context: ['warehouse_uuid' => $warehouse->uuid, 'warehouse_code' => $warehouse->code], message: null, activity: __("{$this->langPath}.store.success", ['code' => $warehouse->code]), is_success: true, loggable: $warehouse);

            // Redirection vers le show de l'entrepôt avec un message de succès
            return redirect()->route('backend.product-warehouses.show', ['productWarehouse' => $warehouse->uuid])->with('success', __("{$this->langPath}.store.flash_success", ['code' => $warehouse->code]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la création de l'entrepôt
            $this->logActivity(type: 'warning', action: 'backend.product-warehouses.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->langPath}.store.error"), is_success: false);

            // Redirection avec le message métier explicite
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la création de l'entrepôt
            $this->logActivity(type: 'error', action: 'backend.product-warehouses.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->langPath}.store.error"), is_success: false);

            // Redirection avec un message d'erreur en cas de problème, en conservant les données du formulaire
            return redirect()->back()->with('error', __("{$this->langPath}.store.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_show(ProductWarehouse $productWarehouse): View
    {
        try {

            // Charge les compteurs (withCount)
            $productWarehouse->loadCount(['audits', 'activityLogs']);

            // Log de la consultation du détail d'un entrepôt
            $this->logActivity(type: 'info', action: 'backend.product-warehouses.show', context: ['warehouse_uuid' => $productWarehouse->uuid, 'warehouse_code' => $productWarehouse->code], message: null, activity: __("{$this->langPath}.show.success", ['code' => $productWarehouse->code]), is_success: true, loggable: $productWarehouse);

            $warehouse = $productWarehouse;

            // Retourne la vue de l'entrepôt avec les données nécessaires
            return view('v1.backend.pages.products.warehouses.show', compact('warehouse'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la consultation du détail d'un entrepôt
            $this->logActivity(type: 'error', action: 'backend.product-warehouses.show', context: ['warehouse_uuid' => $productWarehouse->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.show.error", ['uuid' => $productWarehouse->uuid]), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération de l'entrepôt
        }
    }

    // ─────────────────────────────────────────────
    // AUDITS
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_audits(ProductWarehouse $productWarehouse): View
    {
        try {

            // Récupère les audits de l'entrepôt
            $audits = $productWarehouse->audits()->orderBy('created_at', 'desc')->get();

            // Log de la consultation des audits de l'entrepôt
            $this->logActivity(type: 'info', action: 'backend.product-warehouses.audits', context: ['warehouse_uuid' => $productWarehouse->uuid, 'warehouse_code' => $productWarehouse->code], message: null, activity: __("{$this->langPath}.audits.success", ['code' => $productWarehouse->code]), is_success: true, loggable: $productWarehouse);

            $warehouse = $productWarehouse;

            return view('v1.backend.pages.products.warehouses.others.audits', compact('warehouse', 'audits'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouses.audits', context: ['warehouse_uuid' => $productWarehouse->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.audits.error", ['uuid' => $productWarehouse->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVITY LOGS
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_activity_logs(ProductWarehouse $productWarehouse): View
    {
        try {

            // Récupère les journaux d'activité de l'entrepôt
            $logs = $productWarehouse->activityLogs()->orderBy('created_at', 'desc')->get();

            // Log de la consultation des journaux d'activité de l'entrepôt
            $this->logActivity(type: 'info', action: 'backend.product-warehouses.activity-logs', context: ['warehouse_uuid' => $productWarehouse->uuid, 'warehouse_code' => $productWarehouse->code], message: null, activity: __("{$this->langPath}.activity-logs.success", ['code' => $productWarehouse->code]), is_success: true, loggable: $productWarehouse);

            $warehouse = $productWarehouse;

            return view('v1.backend.pages.products.warehouses.others.activity-logs', compact('warehouse', 'logs'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouses.activity-logs', context: ['warehouse_uuid' => $productWarehouse->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.activity-logs.error", ['uuid' => $productWarehouse->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_edit(ProductWarehouse $productWarehouse): View
    {
        try {

            // Log de l'accès à la page de modification d'un entrepôt
            $this->logActivity(type: 'info', action: 'backend.product-warehouses.edit', context: ['warehouse_uuid' => $productWarehouse->uuid, 'warehouse_code' => $productWarehouse->code], message: null, activity: __("{$this->langPath}.edit.success", ['code' => $productWarehouse->code]), is_success: true, loggable: $productWarehouse);

            $warehouse = $productWarehouse;

            return view('v1.backend.pages.products.warehouses.edit', compact('warehouse'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouses.edit', context: ['warehouse_uuid' => $productWarehouse->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.edit.error", ['uuid' => $productWarehouse->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_update(UpdateProductWarehouseRequest $request, ProductWarehouse $productWarehouse): RedirectResponse
    {
        try {

            $warehouseCode = $productWarehouse->code;

            $this->productWarehouseService->backend_product_warehouses_update($productWarehouse, $request->validated());

            $this->logActivity(type: 'info', action: 'backend.product-warehouses.update', context: ['warehouse_uuid' => $productWarehouse->uuid, 'warehouse_code' => $warehouseCode], message: null, activity: __("{$this->langPath}.update.success", ['code' => $warehouseCode]), is_success: true, loggable: $productWarehouse);

            return redirect()->route('backend.product-warehouses.show', ['productWarehouse' => $productWarehouse->uuid])->with('success', __("{$this->langPath}.update.flash_success", ['code' => $warehouseCode]));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouses.update', context: ['warehouse_uuid' => $productWarehouse->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.update.warning", ['uuid' => $productWarehouse->uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouses.update', context: ['warehouse_uuid' => $productWarehouse->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.update.warning_meta", ['uuid' => $productWarehouse->uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouses.update', context: ['warehouse_uuid' => $productWarehouse->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.update.error", ['uuid' => $productWarehouse->uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.update.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_activate(Request $request, ProductWarehouse $productWarehouse): RedirectResponse
    {
        try {

            $warehouseCode = $productWarehouse->code;

            $this->productWarehouseService->backend_product_warehouses_activate($productWarehouse);

            $this->logActivity(type: 'info', action: 'backend.product-warehouses.activate', context: ['warehouse_uuid' => $productWarehouse->uuid, 'warehouse_code' => $warehouseCode], message: null, activity: __("{$this->langPath}.activate.success", ['code' => $warehouseCode]), is_success: true, loggable: $productWarehouse);

            return back()->with('success', __("{$this->langPath}.activate.flash_success", ['code' => $warehouseCode]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouses.activate', context: ['warehouse_uuid' => $productWarehouse->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.activate.warning_meta", ['uuid' => $productWarehouse->uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouses.activate', context: ['warehouse_uuid' => $productWarehouse->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.activate.error", ['uuid' => $productWarehouse->uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.activate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_deactivate(Request $request, ProductWarehouse $productWarehouse): RedirectResponse
    {
        try {

            $warehouseCode = $productWarehouse->code;

            $this->productWarehouseService->backend_product_warehouses_deactivate($productWarehouse);

            $this->logActivity(type: 'info', action: 'backend.product-warehouses.deactivate', context: ['warehouse_uuid' => $productWarehouse->uuid, 'warehouse_code' => $warehouseCode], message: null, activity: __("{$this->langPath}.deactivate.success", ['code' => $warehouseCode]), is_success: true, loggable: $productWarehouse);

            return back()->with('success', __("{$this->langPath}.deactivate.flash_success", ['code' => $warehouseCode]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouses.deactivate', context: ['warehouse_uuid' => $productWarehouse->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.deactivate.warning_meta", ['uuid' => $productWarehouse->uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouses.deactivate', context: ['warehouse_uuid' => $productWarehouse->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.deactivate.error", ['uuid' => $productWarehouse->uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.deactivate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_destroy(ProductWarehouse $productWarehouse): RedirectResponse
    {
        try {

            $warehouseCode = $productWarehouse->code;

            $this->productWarehouseService->backend_product_warehouses_destroy($productWarehouse);

            $this->logActivity(type: 'warning', action: 'backend.product-warehouses.destroy', context: ['warehouse_uuid' => $productWarehouse->uuid, 'warehouse_code' => $warehouseCode], message: null, activity: __("{$this->langPath}.destroy.success", ['code' => $warehouseCode]), is_success: true, loggable: $productWarehouse);

            return redirect()->route('backend.product-warehouses.index')->with('success', __("{$this->langPath}.destroy.flash_success", ['code' => $warehouseCode]));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouses.destroy', context: ['warehouse_uuid' => $productWarehouse->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.warning", ['uuid' => $productWarehouse->uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouses.destroy', context: ['warehouse_uuid' => $productWarehouse->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.warning_meta", ['uuid' => $productWarehouse->uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouses.destroy', context: ['warehouse_uuid' => $productWarehouse->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.error", ['uuid' => $productWarehouse->uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_trashed(): View
    {
        try {

            $warehouses = ProductWarehouse::onlyTrashed()->withCount(['audits', 'activityLogs'])->orderBy('deleted_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.product-warehouses.trashed', context: ['count' => $warehouses->count()], message: null, activity: __("{$this->langPath}.trashed.success"), is_success: true);

            return view('v1.backend.pages.products.warehouses.trashed', compact('warehouses'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouses.trashed', context: [], message: $e->getMessage(), activity: __("{$this->langPath}.trashed.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_restore(string $uuid): RedirectResponse
    {
        try {

            // Récupère l'entrepôt supprimé par son UUID ou échoue
            $warehouse = ProductWarehouse::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            $warehouseCode = $warehouse->code;

            $this->productWarehouseService->backend_product_warehouses_restore($warehouse);

            $this->logActivity(type: 'info', action: 'backend.product-warehouses.restore', context: ['warehouse_uuid' => $warehouse->uuid, 'warehouse_code' => $warehouseCode], message: null, activity: __("{$this->langPath}.restore.success", ['code' => $warehouseCode]), is_success: true, loggable: $warehouse);

            return back()->with('success', __("{$this->langPath}.restore.flash_success", ['code' => $warehouseCode]));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouses.restore', context: ['warehouse_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouses.restore', context: ['warehouse_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouses.restore', context: ['warehouse_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_force_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupère l'entrepôt supprimé par son UUID ou échoue
            $warehouse = ProductWarehouse::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            $warehouseCode = $warehouse->code;

            $this->productWarehouseService->backend_product_warehouses_force_destroy($warehouse);

            $this->logActivity(type: 'info', action: 'backend.product-warehouses.force_destroy', context: ['warehouse_uuid' => $warehouse->uuid, 'warehouse_code' => $warehouseCode], message: null, activity: __("{$this->langPath}.force_destroy.success", ['code' => $warehouseCode]), is_success: true, loggable: $warehouse);

            return back()->with('success', __("{$this->langPath}.force_destroy.flash_success", ['code' => $warehouseCode]));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouses.force_destroy', context: ['warehouse_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouses.force_destroy', context: ['warehouse_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouses.force_destroy', context: ['warehouse_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.force_destroy.flash_error"));
        }
    }
}
