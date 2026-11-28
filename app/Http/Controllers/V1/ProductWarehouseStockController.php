<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Products\WarehouseStocks\StoreProductWarehouseStockRequest;
use App\Http\Requests\V1\Products\WarehouseStocks\UpdateProductWarehouseStockRequest;
use App\Models\ProductVariant;
use App\Models\ProductWarehouse;
use App\Models\ProductWarehouseStock;
use App\Services\V1\Controllers\ProductWarehouseStockService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

use function __;

class ProductWarehouseStockController extends Controller
{
    use LogActivity;

    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/products/warehouse-stocks/messages/warehouse-stocks-messages-controller.warehouseStocks';

    // Injection de dépendance du service de gestion des stocks
    public function __construct(protected ProductWarehouseStockService $productWarehouseStockService) {}

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_stocks_index(): View
    {
        try {

            // Récupère tous les soldes avec leur entrepôt et leur variante, et le nombre d'audits/logs
            $stocks = ProductWarehouseStock::with(['warehouse', 'variant.product'])->withAuditsCountTotal()->withCount(['audits', 'activityLogs'])->orderByDesc('created_at')->get();

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-stocks.index', context: ['count' => $stocks->count()], message: null, activity: __("{$this->backendLangPath}.index.success"), is_success: true);

            return view('v1.backend.pages.products.warehouse-stocks.index', compact('stocks'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-stocks.index', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.index.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_stocks_create(): View
    {
        try {

            // Entrepôts en mode « warehouse » (seuls à pouvoir gérer un stock global)
            $warehouses = ProductWarehouse::withActiveTranslation()
                ->where('stock_tracking_mode', 'warehouse')
                ->orderBy('sort_order')
                ->get();

            // Variantes disponibles (avec leur produit pour l'affichage)
            $variants = ProductVariant::with(['product'])
                ->orderBy('sku')
                ->get();

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-stocks.create', context: [], message: null, activity: __("{$this->backendLangPath}.create.success"), is_success: true);

            return view('v1.backend.pages.products.warehouse-stocks.create', compact('warehouses', 'variants'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-stocks.create', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.create.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_stocks_store(StoreProductWarehouseStockRequest $request): RedirectResponse
    {
        try {

            $productWarehouseStock = $this->productWarehouseStockService->backend_product_warehouse_stocks_store($request->validated());

            $productWarehouseStockLabel = $this->stockLabel($productWarehouseStock);

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-stocks.store', context: ['stock_uuid' => $productWarehouseStock->uuid], message: null, activity: __("{$this->backendLangPath}.store.success", ['label' => $productWarehouseStockLabel]), is_success: true, loggable: $productWarehouseStock);

            return redirect()->route('backend.product-warehouse-stocks.index')->with('success', __("{$this->backendLangPath}.store.flash_success", ['label' => $productWarehouseStockLabel]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-stocks.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-stocks.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.store.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_stocks_show(ProductWarehouseStock $productWarehouseStock): View
    {

        try {

            $productWarehouseStock->load(['warehouse', 'variant.product'])->loadCount(['audits', 'activityLogs']);

            $productWarehouseStockLabel = $this->stockLabel($productWarehouseStock);

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-stocks.show', context: ['stock_uuid' => $productWarehouseStock->uuid], message: null, activity: __("{$this->backendLangPath}.show.success", ['label' => $productWarehouseStockLabel]), is_success: true, loggable: $productWarehouseStock);

            return view('v1.backend.pages.products.warehouse-stocks.show', compact('productWarehouseStock'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-stocks.show', context: ['stock_uuid' => $productWarehouseStock->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.show.error", ['uuid' => $productWarehouseStock->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // AUDITS
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_stocks_audits(ProductWarehouseStock $productWarehouseStock): View
    {

        try {

            $productWarehouseStock->load(['warehouse', 'variant.product'])->loadCount(['audits', 'activityLogs']);

            $productWarehouseStockLabel = $this->stockLabel($productWarehouseStock);

            $audits = $productWarehouseStock->auditsWithTranslations()->with(['user', 'auditable'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-stocks.audits', context: ['stock_uuid' => $productWarehouseStock->uuid], message: null, activity: __("{$this->backendLangPath}.audits.success", ['label' => $productWarehouseStockLabel]), is_success: true, loggable: $productWarehouseStock);

            return view('v1.backend.pages.products.warehouse-stocks.others.audits', compact('productWarehouseStock', 'audits'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-stocks.audits', context: ['stock_uuid' => $productWarehouseStock->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.audits.error", ['uuid' => $productWarehouseStock->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVITY LOGS
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_stocks_activity_logs(ProductWarehouseStock $productWarehouseStock): View
    {

        try {

            $productWarehouseStock->load(['warehouse', 'variant.product'])->loadCount(['audits', 'activityLogs']);

            $productWarehouseStockLabel = $this->stockLabel($productWarehouseStock);

            $logs = $productWarehouseStock->activityLogs()->with(['user'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-stocks.activity-logs', context: ['stock_uuid' => $productWarehouseStock->uuid], message: null, activity: __("{$this->backendLangPath}.activity-logs.success", ['label' => $productWarehouseStockLabel]), is_success: true, loggable: $productWarehouseStock);

            return view('v1.backend.pages.products.warehouse-stocks.others.activity-logs', compact('productWarehouseStock', 'logs'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-stocks.activity-logs', context: ['stock_uuid' => $productWarehouseStock->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activity-logs.error", ['uuid' => $productWarehouseStock->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_stocks_edit(ProductWarehouseStock $productWarehouseStock): View
    {

        try {

            $productWarehouseStock->load(['warehouse', 'variant.product']);

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-stocks.edit', context: ['stock_uuid' => $productWarehouseStock->uuid], message: null, activity: __("{$this->backendLangPath}.edit.success", ['label' => $this->stockLabel($productWarehouseStock)]), is_success: true, loggable: $productWarehouseStock);

            return view('v1.backend.pages.products.warehouse-stocks.edit', compact('productWarehouseStock'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-stocks.edit', context: ['stock_uuid' => $productWarehouseStock->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.edit.error", ['uuid' => $productWarehouseStock->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_stocks_update(UpdateProductWarehouseStockRequest $request, ProductWarehouseStock $productWarehouseStock): RedirectResponse
    {

        try {

            $this->productWarehouseStockService->backend_product_warehouse_stocks_update($productWarehouseStock, $request->validated());

            $changes = $productWarehouseStock->getChanges();

            $productWarehouseStock->refresh()->load(['warehouse', 'variant.product']);

            $productWarehouseStockLabel = $this->stockLabel($productWarehouseStock);

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-stocks.update', context: ['stock_uuid' => $productWarehouseStock->uuid, 'changes' => $changes], message: null, activity: __("{$this->backendLangPath}.update.success", ['label' => $productWarehouseStockLabel]), is_success: true, loggable: $productWarehouseStock);

            return redirect()->route('backend.product-warehouse-stocks.show', ['productWarehouseStock' => $productWarehouseStock->uuid])->with('success', __("{$this->backendLangPath}.update.flash_success", ['label' => $productWarehouseStockLabel]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-stocks.update', context: ['stock_uuid' => $productWarehouseStock->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.warning_meta", ['uuid' => $productWarehouseStock->uuid]), is_success: false);

            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-stocks.update', context: ['stock_uuid' => $productWarehouseStock->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.error", ['uuid' => $productWarehouseStock->uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.update.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY (soft delete)
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_stocks_destroy(ProductWarehouseStock $productWarehouseStock): RedirectResponse
    {

        try {

            $productWarehouseStockLabel = $this->stockLabel($productWarehouseStock);

            $this->productWarehouseStockService->backend_product_warehouse_stocks_destroy($productWarehouseStock);

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-stocks.destroy', context: ['stock_uuid' => $productWarehouseStock->uuid], message: null, activity: __("{$this->backendLangPath}.destroy.success", ['label' => $productWarehouseStockLabel]), is_success: true, loggable: $productWarehouseStock);

            return redirect()->route('backend.product-warehouse-stocks.index')->with('success', __("{$this->backendLangPath}.destroy.flash_success", ['label' => $productWarehouseStockLabel]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-stocks.destroy', context: ['stock_uuid' => $productWarehouseStock->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning_meta", ['uuid' => $productWarehouseStock->uuid]), is_success: false);

            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-stocks.destroy', context: ['stock_uuid' => $productWarehouseStock->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.error", ['uuid' => $productWarehouseStock->uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_stocks_trashed(): View
    {
        try {

            $stocks = ProductWarehouseStock::onlyTrashed()->with(['warehouse', 'variant.product'])->withAuditsCountTotal()->withCount(['audits', 'activityLogs'])->orderBy('deleted_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-stocks.trashed', context: ['count' => $stocks->count()], message: null, activity: __("{$this->backendLangPath}.trashed.success"), is_success: true);

            return view('v1.backend.pages.products.warehouse-stocks.trashed', compact('stocks'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-stocks.trashed', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.trashed.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_stocks_restore(string $uuid): RedirectResponse
    {
        try {

            $productWarehouseStock = ProductWarehouseStock::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            restore_function($productWarehouseStock);

            $productWarehouseStockLabel = $this->stockLabel($productWarehouseStock);

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-stocks.restore', context: ['stock_uuid' => $productWarehouseStock->uuid], message: null, activity: __("{$this->backendLangPath}.restore.success", ['label' => $productWarehouseStockLabel]), is_success: true, loggable: $productWarehouseStock);

            return redirect()->back()->with('success', __("{$this->backendLangPath}.restore.flash_success", ['label' => $productWarehouseStockLabel]));

        } catch (ModelNotFoundException $e) {

            return redirect()->back()->with('error', __("{$this->backendLangPath}.restore.flash_warning"));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-stocks.restore', context: ['stock_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_stocks_force_destroy(string $uuid): RedirectResponse
    {
        try {

            $productWarehouseStock = ProductWarehouseStock::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            $productWarehouseStockLabel = $this->stockLabel($productWarehouseStock);

            force_destroy_function($productWarehouseStock);

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-stocks.force_destroy', context: ['stock_uuid' => $productWarehouseStock->uuid], message: null, activity: __("{$this->backendLangPath}.force_destroy.success", ['label' => $productWarehouseStockLabel]), is_success: true, loggable: $productWarehouseStock);

            return redirect()->back()->with('success', __("{$this->backendLangPath}.force_destroy.flash_success", ['label' => $productWarehouseStockLabel]));

        } catch (ModelNotFoundException $e) {

            return redirect()->back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_warning"));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-stocks.force_destroy', context: ['stock_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // STOCK LABEL (privé)
    // ─────────────────────────────────────────────
    private function stockLabel(ProductWarehouseStock $productWarehouseStock): string
    {
        $variantName = $productWarehouseStock->variant?->sku ?? '—';
        $warehouseName = $productWarehouseStock->warehouse?->active_translation?->name ?? $productWarehouseStock->warehouse?->code ?? '—';

        return "{$variantName} @ {$warehouseName}";
    }
}
