<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Products\Stocks\StoreProductWarehouseStockRequest;
use App\Http\Requests\V1\Products\Stocks\UpdateProductWarehouseStockRequest;
use App\Models\ProductVariant;
use App\Models\ProductWarehouse;
use App\Models\ProductWarehouseStock;
use App\Services\V1\Controllers\ProductWarehouseStockService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

// Alias pour les chemins de traduction
use function __;

class ProductWarehouseStockController extends Controller
{
    use LogActivity; // Trait pour le logging d'activité

    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/products/stocks/messages/stocks-messages-controller.productWarehouseStocks';

    // Injection de dépendance du service de gestion des stocks
    public function __construct(protected ProductWarehouseStockService $productWarehouseStockService) {}

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_stocks_index(): View
    {
        try {

            // ✅ Récupère tous les stocks, triés par date de création décroissante
            $stocks = ProductWarehouseStock::with(['warehouse', 'variant'])->withCount(['audits', 'activityLogs'])->orderBy('created_at', 'desc')->get();

            // Log de l'accès à la liste des stocks
            $this->logActivity(type: 'info', action: 'backend.product-warehouse-stocks.index', context: ['count' => $stocks->count()], message: null, activity: __("{$this->backendLangPath}.index.success"), is_success: true);

            // Affiche la vue avec les stocks récupérés
            return view('v1.backend.pages.products.stocks.index', compact('stocks'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur avec le message d'exception
            $this->logActivity(type: 'error', action: 'backend.product-warehouse-stocks.index', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.index.error"), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération des stocks
        }
    }

    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_stocks_create(): View
    {
        try {

            // Log de l'accès à la page de création d'un stock
            $this->logActivity(type: 'info', action: 'backend.product-warehouse-stocks.create', context: [], message: null, activity: __("{$this->backendLangPath}.create.success"), is_success: true);

            // Listes pour les selects (entrepôts et variantes)
            $warehouses = ProductWarehouse::orderBy('name')->get();
            $variants = ProductVariant::with('product')->orderBy('sku')->get();

            // Affiche la vue de création d'un stock
            return view('v1.backend.pages.products.stocks.create', compact('warehouses', 'variants'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de l'accès à la page de création d'un stock
            $this->logActivity(type: 'error', action: 'backend.product-warehouse-stocks.create', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.create.error"), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de l'accès à la page de création
        }
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_stocks_store(StoreProductWarehouseStockRequest $request): RedirectResponse
    {
        try {

            // Création du stock via le service, en passant les données validées du formulaire
            $stock = $this->productWarehouseStockService->backend_product_warehouse_stocks_store($request->validated());

            // Log de la création du stock avec l'UUID du stock créé
            $this->logActivity(type: 'info', action: 'backend.product-warehouse-stocks.store', context: ['stock_uuid' => $stock->uuid], message: null, activity: __("{$this->backendLangPath}.store.success"), is_success: true, loggable: $stock);

            // Redirection vers le show du stock avec un message de succès
            return redirect()->route('backend.product-warehouse-stocks.show', ['productWarehouseStock' => $stock->uuid])->with('success', __("{$this->backendLangPath}.store.flash_success"));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la création du stock
            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-stocks.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            // Redirection avec le message métier explicite
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la création du stock
            $this->logActivity(type: 'error', action: 'backend.product-warehouse-stocks.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            // Redirection avec un message d'erreur en cas de problème, en conservant les données du formulaire
            return redirect()->back()->with('error', __("{$this->backendLangPath}.store.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_stocks_show(ProductWarehouseStock $productWarehouseStock): View
    {
        try {

            // Charge les relations + compteurs (withCount)
            $productWarehouseStock->load(['warehouse', 'variant']);
            $productWarehouseStock->loadCount(['audits', 'activityLogs']);

            // Log de la consultation du détail d'un stock
            $this->logActivity(type: 'info', action: 'backend.product-warehouse-stocks.show', context: ['stock_uuid' => $productWarehouseStock->uuid], message: null, activity: __("{$this->backendLangPath}.show.success"), is_success: true, loggable: $productWarehouseStock);

            $stock = $productWarehouseStock;

            // Retourne la vue du stock avec les données nécessaires
            return view('v1.backend.pages.products.stocks.show', compact('stock'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la consultation du détail d'un stock
            $this->logActivity(type: 'error', action: 'backend.product-warehouse-stocks.show', context: ['stock_uuid' => $productWarehouseStock->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.show.error"), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération du stock
        }
    }

    // ─────────────────────────────────────────────
    // AUDITS
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_stocks_audits(ProductWarehouseStock $productWarehouseStock): View
    {
        try {

            // Récupère les audits du stock
            $audits = $productWarehouseStock->audits()->orderBy('created_at', 'desc')->get();

            // Log de la consultation des audits du stock
            $this->logActivity(type: 'info', action: 'backend.product-warehouse-stocks.audits', context: ['stock_uuid' => $productWarehouseStock->uuid], message: null, activity: __("{$this->backendLangPath}.audits.success"), is_success: true, loggable: $productWarehouseStock);

            $stock = $productWarehouseStock;

            return view('v1.backend.pages.products.stocks.others.audits', compact('stock', 'audits'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-stocks.audits', context: ['stock_uuid' => $productWarehouseStock->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.audits.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVITY LOGS
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_stocks_activity_logs(ProductWarehouseStock $productWarehouseStock): View
    {
        try {

            // Récupère les journaux d'activité du stock
            $logs = $productWarehouseStock->activityLogs()->orderBy('created_at', 'desc')->get();

            // Log de la consultation des journaux d'activité du stock
            $this->logActivity(type: 'info', action: 'backend.product-warehouse-stocks.activity-logs', context: ['stock_uuid' => $productWarehouseStock->uuid], message: null, activity: __("{$this->backendLangPath}.activity-logs.success"), is_success: true, loggable: $productWarehouseStock);

            $stock = $productWarehouseStock;

            return view('v1.backend.pages.products.stocks.others.activity-logs', compact('stock', 'logs'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-stocks.activity-logs', context: ['stock_uuid' => $productWarehouseStock->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activity-logs.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_stocks_edit(ProductWarehouseStock $productWarehouseStock): View
    {
        try {

            // Log de l'accès à la page de modification d'un stock
            $this->logActivity(type: 'info', action: 'backend.product-warehouse-stocks.edit', context: ['stock_uuid' => $productWarehouseStock->uuid], message: null, activity: __("{$this->backendLangPath}.edit.success"), is_success: true, loggable: $productWarehouseStock);

            $stock = $productWarehouseStock;

            // Listes pour les selects (entrepôts et variantes)
            $warehouses = ProductWarehouse::orderBy('name')->get();
            $variants = ProductVariant::with('product')->orderBy('sku')->get();

            return view('v1.backend.pages.products.stocks.edit', compact('stock', 'warehouses', 'variants'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-stocks.edit', context: ['stock_uuid' => $productWarehouseStock->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.edit.error"), is_success: false);

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

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-stocks.update', context: ['stock_uuid' => $productWarehouseStock->uuid], message: null, activity: __("{$this->backendLangPath}.update.success"), is_success: true, loggable: $productWarehouseStock);

            return redirect()->route('backend.product-warehouse-stocks.show', ['productWarehouseStock' => $productWarehouseStock->uuid])->with('success', __("{$this->backendLangPath}.update.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-stocks.update', context: ['stock_uuid' => $productWarehouseStock->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.warning"), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-stocks.update', context: ['stock_uuid' => $productWarehouseStock->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.warning_meta"), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-stocks.update', context: ['stock_uuid' => $productWarehouseStock->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.error"), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.update.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_stocks_destroy(ProductWarehouseStock $productWarehouseStock): RedirectResponse
    {
        try {

            $this->productWarehouseStockService->backend_product_warehouse_stocks_destroy($productWarehouseStock);

            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-stocks.destroy', context: ['stock_uuid' => $productWarehouseStock->uuid], message: null, activity: __("{$this->backendLangPath}.destroy.success"), is_success: true, loggable: $productWarehouseStock);

            return redirect()->route('backend.product-warehouse-stocks.index')->with('success', __("{$this->backendLangPath}.destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-stocks.destroy', context: ['stock_uuid' => $productWarehouseStock->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning"), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-stocks.destroy', context: ['stock_uuid' => $productWarehouseStock->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning_meta"), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-stocks.destroy', context: ['stock_uuid' => $productWarehouseStock->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.error"), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_stocks_trashed(): View
    {
        try {

            $stocks = ProductWarehouseStock::onlyTrashed()->with(['warehouse', 'variant'])->withCount(['audits', 'activityLogs'])->orderBy('deleted_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-stocks.trashed', context: ['count' => $stocks->count()], message: null, activity: __("{$this->backendLangPath}.trashed.success"), is_success: true);

            return view('v1.backend.pages.products.stocks.trashed', compact('stocks'));

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

            // Récupère le stock supprimé par son UUID ou échoue
            $stock = ProductWarehouseStock::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            $this->productWarehouseStockService->backend_product_warehouse_stocks_restore($stock);

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-stocks.restore', context: ['stock_uuid' => $stock->uuid], message: null, activity: __("{$this->backendLangPath}.restore.success"), is_success: true, loggable: $stock);

            return back()->with('success', __("{$this->backendLangPath}.restore.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-stocks.restore', context: ['stock_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning"), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-stocks.restore', context: ['stock_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning_meta"), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-stocks.restore', context: ['stock_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.error"), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_stocks_force_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupère le stock supprimé par son UUID ou échoue
            $stock = ProductWarehouseStock::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            $this->productWarehouseStockService->backend_product_warehouse_stocks_force_destroy($stock);

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-stocks.force_destroy', context: ['stock_uuid' => $stock->uuid], message: null, activity: __("{$this->backendLangPath}.force_destroy.success"), is_success: true, loggable: $stock);

            return back()->with('success', __("{$this->backendLangPath}.force_destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-stocks.force_destroy', context: ['stock_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning"), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-stocks.force_destroy', context: ['stock_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning_meta"), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-stocks.force_destroy', context: ['stock_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.error"), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_error"));
        }
    }
}
