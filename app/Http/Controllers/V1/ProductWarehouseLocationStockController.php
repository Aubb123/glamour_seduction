<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Products\WarehouseLocationStocks\StoreProductWarehouseLocationStockRequest;
use App\Http\Requests\V1\Products\WarehouseLocationStocks\UpdateProductWarehouseLocationStockRequest;
use App\Models\ProductVariant;
use App\Models\ProductWarehouseLocation;
use App\Models\ProductWarehouseLocationStock;
use App\Services\V1\Controllers\ProductWarehouseLocationStockService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

use function __;

class ProductWarehouseLocationStockController extends Controller
{
    use LogActivity;

    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/products/warehouse-location-stocks/messages/warehouse-location-stocks-messages-controller.warehouseLocationStocks';

    // Injection de dépendance du service de gestion des stocks par emplacement
    public function __construct(protected ProductWarehouseLocationStockService $productWarehouseLocationStockService) {}

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_location_stocks_index(): View
    {
        try {

            // Récupère tous les soldes avec leur emplacement, entrepôt et variante
            $stocks = ProductWarehouseLocationStock::with(['location.warehouse', 'variant.product'])->withAuditsCountTotal()->withCount(['audits', 'activityLogs'])->orderByDesc('created_at')->get();

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-location-stocks.index', context: ['count' => $stocks->count()], message: null, activity: __("{$this->backendLangPath}.index.success"), is_success: true);

            return view('v1.backend.pages.products.warehouse-location-stocks.index', compact('stocks'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-location-stocks.index', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.index.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_location_stocks_create(): View
    {
        try {

            // Emplacements dont l'entrepôt parent est en mode « location »
            $locations = ProductWarehouseLocation::with(['warehouse'])->whereHas('warehouse', fn ($query) => $query->where('stock_tracking_mode', 'location'))->orderBy('sort_order')->get();

            // Variantes disponibles (avec leur produit pour l'affichage)
            $variants = ProductVariant::with(['product'])->orderBy('sku')->get();

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-location-stocks.create', context: [], message: null, activity: __("{$this->backendLangPath}.create.success"), is_success: true);

            return view('v1.backend.pages.products.warehouse-location-stocks.create', compact('locations', 'variants'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-location-stocks.create', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.create.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_location_stocks_store(StoreProductWarehouseLocationStockRequest $request): RedirectResponse
    {
        try {

            $productWarehouseLocationStock = $this->productWarehouseLocationStockService->backend_product_warehouse_location_stocks_store($request->validated());

            $productWarehouseLocationStockLabel = $this->stockLabel($productWarehouseLocationStock);

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-location-stocks.store', context: ['stock_uuid' => $productWarehouseLocationStock->uuid], message: null, activity: __("{$this->backendLangPath}.store.success", ['label' => $productWarehouseLocationStockLabel]), is_success: true, loggable: $productWarehouseLocationStock);

            return redirect()->route('backend.product-warehouse-location-stocks.index')->with('success', __("{$this->backendLangPath}.store.flash_success", ['label' => $productWarehouseLocationStockLabel]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-location-stocks.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-location-stocks.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.store.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_location_stocks_show(ProductWarehouseLocationStock $productWarehouseLocationStock): View
    {

        try {

            $productWarehouseLocationStock->load(['location.warehouse', 'variant.product'])->loadCount(['audits', 'activityLogs']);

            $productWarehouseLocationStockLabel = $this->stockLabel($productWarehouseLocationStock);

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-location-stocks.show', context: ['stock_uuid' => $productWarehouseLocationStock->uuid], message: null, activity: __("{$this->backendLangPath}.show.success", ['label' => $productWarehouseLocationStockLabel]), is_success: true, loggable: $productWarehouseLocationStock);

            return view('v1.backend.pages.products.warehouse-location-stocks.show', compact('productWarehouseLocationStock'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-location-stocks.show', context: ['stock_uuid' => $productWarehouseLocationStock->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.show.error", ['uuid' => $productWarehouseLocationStock->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // AUDITS
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_location_stocks_audits(ProductWarehouseLocationStock $productWarehouseLocationStock): View
    {

        try {

            $productWarehouseLocationStock->load(['location.warehouse', 'variant.product'])->loadCount(['audits', 'activityLogs']);

            $productWarehouseLocationStockLabel = $this->stockLabel($productWarehouseLocationStock);

            $audits = $productWarehouseLocationStock->auditsWithTranslations()->with(['user', 'auditable'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-location-stocks.audits', context: ['stock_uuid' => $productWarehouseLocationStock->uuid], message: null, activity: __("{$this->backendLangPath}.audits.success", ['label' => $productWarehouseLocationStockLabel]), is_success: true, loggable: $productWarehouseLocationStock);

            return view('v1.backend.pages.products.warehouse-location-stocks.others.audits', compact('productWarehouseLocationStock', 'audits'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-location-stocks.audits', context: ['stock_uuid' => $productWarehouseLocationStock->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.audits.error", ['uuid' => $productWarehouseLocationStock->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVITY LOGS
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_location_stocks_activity_logs(ProductWarehouseLocationStock $productWarehouseLocationStock): View
    {

        try {

            $productWarehouseLocationStock->load(['location.warehouse', 'variant.product'])->loadCount(['audits', 'activityLogs']);

            $productWarehouseLocationStockLabel = $this->stockLabel($productWarehouseLocationStock);

            $logs = $productWarehouseLocationStock->activityLogs()->with(['user'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-location-stocks.activity-logs', context: ['stock_uuid' => $productWarehouseLocationStock->uuid], message: null, activity: __("{$this->backendLangPath}.activity-logs.success", ['label' => $productWarehouseLocationStockLabel]), is_success: true, loggable: $productWarehouseLocationStock);

            return view('v1.backend.pages.products.warehouse-location-stocks.others.activity-logs', compact('productWarehouseLocationStock', 'logs'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-location-stocks.activity-logs', context: ['stock_uuid' => $productWarehouseLocationStock->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activity-logs.error", ['uuid' => $productWarehouseLocationStock->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_location_stocks_edit(ProductWarehouseLocationStock $productWarehouseLocationStock): View
    {

        try {

            $productWarehouseLocationStock->load(['location.warehouse', 'variant.product']);

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-location-stocks.edit', context: ['stock_uuid' => $productWarehouseLocationStock->uuid], message: null, activity: __("{$this->backendLangPath}.edit.success", ['label' => $this->stockLabel($productWarehouseLocationStock)]), is_success: true, loggable: $productWarehouseLocationStock);

            return view('v1.backend.pages.products.warehouse-location-stocks.edit', compact('productWarehouseLocationStock'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-location-stocks.edit', context: ['stock_uuid' => $productWarehouseLocationStock->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.edit.error", ['uuid' => $productWarehouseLocationStock->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_location_stocks_update(UpdateProductWarehouseLocationStockRequest $request, ProductWarehouseLocationStock $productWarehouseLocationStock): RedirectResponse
    {

        try {

            $this->productWarehouseLocationStockService->backend_product_warehouse_location_stocks_update($productWarehouseLocationStock, $request->validated());

            $changes = $productWarehouseLocationStock->getChanges();

            $productWarehouseLocationStock->refresh()->load(['location.warehouse', 'variant.product']);

            $productWarehouseLocationStockLabel = $this->stockLabel($productWarehouseLocationStock);

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-location-stocks.update', context: ['stock_uuid' => $productWarehouseLocationStock->uuid, 'changes' => $changes], message: null, activity: __("{$this->backendLangPath}.update.success", ['label' => $productWarehouseLocationStockLabel]), is_success: true, loggable: $productWarehouseLocationStock);

            return redirect()->route('backend.product-warehouse-location-stocks.show', ['productWarehouseLocationStock' => $productWarehouseLocationStock->uuid])->with('success', __("{$this->backendLangPath}.update.flash_success", ['label' => $productWarehouseLocationStockLabel]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-location-stocks.update', context: ['stock_uuid' => $productWarehouseLocationStock->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.warning_meta", ['uuid' => $productWarehouseLocationStock->uuid]), is_success: false);

            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-location-stocks.update', context: ['stock_uuid' => $productWarehouseLocationStock->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.error", ['uuid' => $productWarehouseLocationStock->uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.update.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY (soft delete)
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_location_stocks_destroy(ProductWarehouseLocationStock $productWarehouseLocationStock): RedirectResponse
    {

        try {

            $productWarehouseLocationStockLabel = $this->stockLabel($productWarehouseLocationStock);

            $this->productWarehouseLocationStockService->backend_product_warehouse_location_stocks_destroy($productWarehouseLocationStock);

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-location-stocks.destroy', context: ['stock_uuid' => $productWarehouseLocationStock->uuid], message: null, activity: __("{$this->backendLangPath}.destroy.success", ['label' => $productWarehouseLocationStockLabel]), is_success: true, loggable: $productWarehouseLocationStock);

            return redirect()->route('backend.product-warehouse-location-stocks.index')->with('success', __("{$this->backendLangPath}.destroy.flash_success", ['label' => $productWarehouseLocationStockLabel]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-location-stocks.destroy', context: ['stock_uuid' => $productWarehouseLocationStock->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning_meta", ['uuid' => $productWarehouseLocationStock->uuid]), is_success: false);

            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-location-stocks.destroy', context: ['stock_uuid' => $productWarehouseLocationStock->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.error", ['uuid' => $productWarehouseLocationStock->uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_location_stocks_trashed(): View
    {
        try {

            $stocks = ProductWarehouseLocationStock::onlyTrashed()->with(['location.warehouse', 'variant.product'])->withAuditsCountTotal()->withCount(['audits', 'activityLogs'])->orderBy('deleted_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-location-stocks.trashed', context: ['count' => $stocks->count()], message: null, activity: __("{$this->backendLangPath}.trashed.success"), is_success: true);

            return view('v1.backend.pages.products.warehouse-location-stocks.trashed', compact('stocks'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-location-stocks.trashed', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.trashed.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_location_stocks_restore(string $uuid): RedirectResponse
    {
        try {

            $productWarehouseLocationStock = ProductWarehouseLocationStock::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            restore_function($productWarehouseLocationStock);

            $productWarehouseLocationStockLabel = $this->stockLabel($productWarehouseLocationStock);

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-location-stocks.restore', context: ['stock_uuid' => $productWarehouseLocationStock->uuid], message: null, activity: __("{$this->backendLangPath}.restore.success", ['label' => $productWarehouseLocationStockLabel]), is_success: true, loggable: $productWarehouseLocationStock);

            return redirect()->back()->with('success', __("{$this->backendLangPath}.restore.flash_success", ['label' => $productWarehouseLocationStockLabel]));

        } catch (ModelNotFoundException $e) {

            return redirect()->back()->with('error', __("{$this->backendLangPath}.restore.flash_warning"));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-location-stocks.restore', context: ['stock_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_location_stocks_force_destroy(string $uuid): RedirectResponse
    {
        try {

            $productWarehouseLocationStock = ProductWarehouseLocationStock::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            $productWarehouseLocationStockLabel = $this->stockLabel($productWarehouseLocationStock);

            force_destroy_function($productWarehouseLocationStock);

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-location-stocks.force_destroy', context: ['stock_uuid' => $productWarehouseLocationStock->uuid], message: null, activity: __("{$this->backendLangPath}.force_destroy.success", ['label' => $productWarehouseLocationStockLabel]), is_success: true, loggable: $productWarehouseLocationStock);

            return redirect()->back()->with('success', __("{$this->backendLangPath}.force_destroy.flash_success", ['label' => $productWarehouseLocationStockLabel]));

        } catch (ModelNotFoundException $e) {

            return redirect()->back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_warning"));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-location-stocks.force_destroy', context: ['stock_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // STOCK LABEL (privé)
    // ─────────────────────────────────────────────
    private function stockLabel(ProductWarehouseLocationStock $productWarehouseLocationStock): string
    {
        $variantName = $productWarehouseLocationStock->variant?->sku ?? '—';
        $locationName = $productWarehouseLocationStock->location?->code ?? '—';

        return "{$variantName} @ {$locationName}";
    }
}
