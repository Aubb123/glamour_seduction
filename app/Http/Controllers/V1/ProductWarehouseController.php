<?php

namespace App\Http\Controllers\V1;

use App\Enums\Shops\Positions\PositionContext;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Products\Warehouses\StoreProductWarehouseRequest;
use App\Http\Requests\V1\Products\Warehouses\UpdateProductWarehouseRequest;
use App\Models\Position;
use App\Models\ProductWarehouse;
use App\Models\User;
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
    protected string $backendLangPath = 'v1/backend/views/pages/products/warehouses/messages/warehouses-messages-controller.productWarehouses';

    // Injection de dépendance du service de gestion des entrepôts
    public function __construct(protected ProductWarehouseService $productWarehouseService) {}

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_index(): View
    {
        try {

            // ✅ Récupère tous les entrepôts avec leurs traductions actives, triés par date de création décroissante
            $warehouses = ProductWarehouse::withActiveTranslation()
                ->withCount(['audits', 'activityLogs'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Log de l'accès à la liste des entrepôts
            $this->logActivity(type: 'info', action: 'backend.product-warehouses.index', context: ['count' => $warehouses->count()], message: null, activity: __("{$this->backendLangPath}.index.success"), is_success: true);

            // Affiche la vue avec les entrepôts récupérés
            return view('v1.backend.pages.products.warehouses.index', compact('warehouses'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur avec le message d'exception
            $this->logActivity(type: 'error', action: 'backend.product-warehouses.index', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.index.error"), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération des entrepôts
        }
    }

    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_create(): View
    {
        try {

            // Récupère les langues actives pour les passer à la vue
            $langs = getActiveLangs()['langs'];

            // Log de l'accès à la page de création d'un entrepôt
            $this->logActivity(type: 'info', action: 'backend.product-warehouses.create', context: [], message: null, activity: __("{$this->backendLangPath}.create.success"), is_success: true);

            // Affiche la vue de création d'un entrepôt
            return view('v1.backend.pages.products.warehouses.create', compact('langs'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de l'accès à la page de création d'un entrepôt
            $this->logActivity(type: 'error', action: 'backend.product-warehouses.create', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.create.error"), is_success: false);

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

            // Identifiant d'affichage : nom traduit actif, repli sur le code
            $warehouseLabel = $warehouse->active_translation?->name ?? $warehouse->code;

            // Log de la création de l'entrepôt avec le code de l'entrepôt créé
            $this->logActivity(type: 'info', action: 'backend.product-warehouses.store', context: ['warehouse_uuid' => $warehouse->uuid, 'warehouse_code' => $warehouse->code], message: null, activity: __("{$this->backendLangPath}.store.success", ['name' => $warehouseLabel]), is_success: true, loggable: $warehouse);

            // Redirection vers le show de l'entrepôt avec un message de succès
            return redirect()->route('backend.product-warehouses.show', ['productWarehouse' => $warehouse->uuid])->with('success', __("{$this->backendLangPath}.store.flash_success", ['name' => $warehouseLabel]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la création de l'entrepôt
            $this->logActivity(type: 'warning', action: 'backend.product-warehouses.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            // Redirection avec le message métier explicite
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la création de l'entrepôt
            $this->logActivity(type: 'error', action: 'backend.product-warehouses.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            // Redirection avec un message d'erreur en cas de problème, en conservant les données du formulaire
            return redirect()->back()->with('error', __("{$this->backendLangPath}.store.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_show(ProductWarehouse $productWarehouse): View
    {
        try {

            // Charge les traductions actives et les compteurs
            $warehouse = $productWarehouse;
            $warehouse->load(['warehouseUsers.user', 'warehouseUsers.position.translations'])
                ->loadCount(['audits', 'activityLogs', 'warehouseUsers']);
            $warehouse->load('translations.lang');

            // Log de la consultation du détail d'un entrepôt
            $this->logActivity(type: 'info', action: 'backend.product-warehouses.show', context: ['warehouse_uuid' => $warehouse->uuid, 'warehouse_code' => $warehouse->code], message: null, activity: __("{$this->backendLangPath}.show.success", ['name' => $warehouse->active_translation?->name ?? $warehouse->code]), is_success: true, loggable: $warehouse);

            // Données pour le panneau « Personnel » de la fiche entrepôt
            $availableUsers = User::orderBy('first_name')->get();

            $availablePositions = Position::withActiveTranslation()
                ->active()
                ->forContext(PositionContext::Warehouse)
                ->orderBy('sort_order')->get();

            // Retourne la vue de l'entrepôt avec les données nécessaires
            return view('v1.backend.pages.products.warehouses.show', compact('warehouse', 'availableUsers', 'availablePositions'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la consultation du détail d'un entrepôt
            $this->logActivity(type: 'error', action: 'backend.product-warehouses.show', context: ['warehouse_uuid' => $productWarehouse->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.show.error", ['uuid' => $productWarehouse->uuid]), is_success: false);

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
            $this->logActivity(type: 'info', action: 'backend.product-warehouses.audits', context: ['warehouse_uuid' => $productWarehouse->uuid, 'warehouse_code' => $productWarehouse->code], message: null, activity: __("{$this->backendLangPath}.audits.success", ['name' => $productWarehouse->active_translation?->name ?? $productWarehouse->code]), is_success: true, loggable: $productWarehouse);

            $warehouse = $productWarehouse;

            return view('v1.backend.pages.products.warehouses.others.audits', compact('warehouse', 'audits'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouses.audits', context: ['warehouse_uuid' => $productWarehouse->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.audits.error", ['uuid' => $productWarehouse->uuid]), is_success: false);

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
            $this->logActivity(type: 'info', action: 'backend.product-warehouses.activity-logs', context: ['warehouse_uuid' => $productWarehouse->uuid, 'warehouse_code' => $productWarehouse->code], message: null, activity: __("{$this->backendLangPath}.activity-logs.success", ['name' => $productWarehouse->active_translation?->name ?? $productWarehouse->code]), is_success: true, loggable: $productWarehouse);

            $warehouse = $productWarehouse;

            return view('v1.backend.pages.products.warehouses.others.activity-logs', compact('warehouse', 'logs'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouses.activity-logs', context: ['warehouse_uuid' => $productWarehouse->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activity-logs.error", ['uuid' => $productWarehouse->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_edit(ProductWarehouse $productWarehouse): View
    {
        try {

            // Récupère les langues actives pour les passer à la vue
            $langs = getActiveLangs()['langs'];

            // Log de l'accès à la page de modification d'un entrepôt
            $this->logActivity(type: 'info', action: 'backend.product-warehouses.edit', context: ['warehouse_uuid' => $productWarehouse->uuid, 'warehouse_code' => $productWarehouse->code], message: null, activity: __("{$this->backendLangPath}.edit.success", ['name' => $productWarehouse->active_translation?->name ?? $productWarehouse->code]), is_success: true, loggable: $productWarehouse);

            $warehouse = $productWarehouse;
            $warehouse->load('translations.lang');

            return view('v1.backend.pages.products.warehouses.edit', compact('warehouse', 'langs'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouses.edit', context: ['warehouse_uuid' => $productWarehouse->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.edit.error", ['uuid' => $productWarehouse->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_update(UpdateProductWarehouseRequest $request, ProductWarehouse $productWarehouse): RedirectResponse
    {
        try {

            $this->productWarehouseService->backend_product_warehouses_update($productWarehouse, $request->validated());

            // Identifiant d'affichage : nom traduit actif, repli sur le code
            $warehouseLabel = $productWarehouse->fresh()->active_translation?->name ?? $productWarehouse->code;

            $this->logActivity(type: 'info', action: 'backend.product-warehouses.update', context: ['warehouse_uuid' => $productWarehouse->uuid, 'warehouse_code' => $productWarehouse->code], message: null, activity: __("{$this->backendLangPath}.update.success", ['name' => $warehouseLabel]), is_success: true, loggable: $productWarehouse);

            return redirect()->route('backend.product-warehouses.show', ['productWarehouse' => $productWarehouse->uuid])->with('success', __("{$this->backendLangPath}.update.flash_success", ['name' => $warehouseLabel]));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouses.update', context: ['warehouse_uuid' => $productWarehouse->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.warning", ['uuid' => $productWarehouse->uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouses.update', context: ['warehouse_uuid' => $productWarehouse->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.warning_meta", ['uuid' => $productWarehouse->uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouses.update', context: ['warehouse_uuid' => $productWarehouse->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.error", ['uuid' => $productWarehouse->uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.update.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_activate(Request $request, ProductWarehouse $productWarehouse): RedirectResponse
    {
        try {

            $warehouseLabel = $productWarehouse->active_translation?->name ?? $productWarehouse->code;

            $this->productWarehouseService->backend_product_warehouses_activate($productWarehouse);

            $this->logActivity(type: 'info', action: 'backend.product-warehouses.activate', context: ['warehouse_uuid' => $productWarehouse->uuid, 'warehouse_code' => $productWarehouse->code], message: null, activity: __("{$this->backendLangPath}.activate.success", ['name' => $warehouseLabel]), is_success: true, loggable: $productWarehouse);

            return back()->with('success', __("{$this->backendLangPath}.activate.flash_success", ['name' => $warehouseLabel]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouses.activate', context: ['warehouse_uuid' => $productWarehouse->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activate.warning_meta", ['uuid' => $productWarehouse->uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouses.activate', context: ['warehouse_uuid' => $productWarehouse->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activate.error", ['uuid' => $productWarehouse->uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.activate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_deactivate(Request $request, ProductWarehouse $productWarehouse): RedirectResponse
    {
        try {

            $warehouseLabel = $productWarehouse->active_translation?->name ?? $productWarehouse->code;

            $this->productWarehouseService->backend_product_warehouses_deactivate($productWarehouse);

            $this->logActivity(type: 'info', action: 'backend.product-warehouses.deactivate', context: ['warehouse_uuid' => $productWarehouse->uuid, 'warehouse_code' => $productWarehouse->code], message: null, activity: __("{$this->backendLangPath}.deactivate.success", ['name' => $warehouseLabel]), is_success: true, loggable: $productWarehouse);

            return back()->with('success', __("{$this->backendLangPath}.deactivate.flash_success", ['name' => $warehouseLabel]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouses.deactivate', context: ['warehouse_uuid' => $productWarehouse->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.deactivate.warning_meta", ['uuid' => $productWarehouse->uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouses.deactivate', context: ['warehouse_uuid' => $productWarehouse->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.deactivate.error", ['uuid' => $productWarehouse->uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.deactivate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_destroy(ProductWarehouse $productWarehouse): RedirectResponse
    {
        try {

            $warehouseLabel = $productWarehouse->active_translation?->name ?? $productWarehouse->code;

            $this->productWarehouseService->backend_product_warehouses_destroy($productWarehouse);

            $this->logActivity(type: 'warning', action: 'backend.product-warehouses.destroy', context: ['warehouse_uuid' => $productWarehouse->uuid, 'warehouse_code' => $productWarehouse->code], message: null, activity: __("{$this->backendLangPath}.destroy.success", ['name' => $warehouseLabel]), is_success: true, loggable: $productWarehouse);

            return redirect()->route('backend.product-warehouses.index')->with('success', __("{$this->backendLangPath}.destroy.flash_success", ['name' => $warehouseLabel]));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouses.destroy', context: ['warehouse_uuid' => $productWarehouse->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning", ['uuid' => $productWarehouse->uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouses.destroy', context: ['warehouse_uuid' => $productWarehouse->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning_meta", ['uuid' => $productWarehouse->uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouses.destroy', context: ['warehouse_uuid' => $productWarehouse->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.error", ['uuid' => $productWarehouse->uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_trashed(): View
    {
        try {

            $warehouses = ProductWarehouse::onlyTrashed()
                ->withActiveTranslation()
                ->withCount(['audits', 'activityLogs'])
                ->orderBy('deleted_at', 'desc')
                ->get();

            $this->logActivity(type: 'info', action: 'backend.product-warehouses.trashed', context: ['count' => $warehouses->count()], message: null, activity: __("{$this->backendLangPath}.trashed.success"), is_success: true);

            return view('v1.backend.pages.products.warehouses.trashed', compact('warehouses'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouses.trashed', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.trashed.error"), is_success: false);

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

            $warehouseLabel = $warehouse->active_translation?->name ?? $warehouse->code;

            $this->productWarehouseService->backend_product_warehouses_restore($warehouse);

            $this->logActivity(type: 'info', action: 'backend.product-warehouses.restore', context: ['warehouse_uuid' => $warehouse->uuid, 'warehouse_code' => $warehouse->code], message: null, activity: __("{$this->backendLangPath}.restore.success", ['name' => $warehouseLabel]), is_success: true, loggable: $warehouse);

            return back()->with('success', __("{$this->backendLangPath}.restore.flash_success", ['name' => $warehouseLabel]));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouses.restore', context: ['warehouse_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouses.restore', context: ['warehouse_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouses.restore', context: ['warehouse_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.restore.flash_error"));
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

            $warehouseLabel = $warehouse->active_translation?->name ?? $warehouse->code;

            $this->productWarehouseService->backend_product_warehouses_force_destroy($warehouse);

            $this->logActivity(type: 'info', action: 'backend.product-warehouses.force_destroy', context: ['warehouse_uuid' => $warehouse->uuid, 'warehouse_code' => $warehouse->code], message: null, activity: __("{$this->backendLangPath}.force_destroy.success", ['name' => $warehouseLabel]), is_success: true, loggable: $warehouse);

            return back()->with('success', __("{$this->backendLangPath}.force_destroy.flash_success", ['name' => $warehouseLabel]));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouses.force_destroy', context: ['warehouse_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouses.force_destroy', context: ['warehouse_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouses.force_destroy', context: ['warehouse_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_error"));
        }
    }
}
