<?php

namespace App\Http\Controllers\V1;

use App\Enums\Products\StockMovements\ProductStockMovementType;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Products\StockMovements\StoreProductStockMovementRequest;
use App\Models\ProductStockMovement;
use App\Models\ProductVariant;
use App\Models\ProductWarehouse;
use App\Services\V1\Controllers\ProductStockMovementService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

// Alias pour les chemins de traduction
use function __;

class ProductStockMovementController extends Controller
{
    use LogActivity; // Trait pour le logging d'activité

    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/products/stock-movements/messages/stock-movements-messages-controller.productStockMovements';

    // Injection de dépendance du service de gestion des mouvements de stock
    public function __construct(protected ProductStockMovementService $productStockMovementService) {}

    // ─────────────────────────────────────────────
    // INDEX (avec filtres type / entrepôt / variante)
    // ─────────────────────────────────────────────
    public function backend_product_stock_movements_index(): View
    {
        try {

            // Requête de base : tous les mouvements avec leurs relations
            $query = ProductStockMovement::with(['warehouse', 'variant.product', 'createdBy'])->orderBy('created_at', 'desc');

            // Filtre par type de mouvement (in/out/adjustment/…)
            if (filled($type = request('type')) && in_array($type, ProductStockMovementType::values(), true)) {
                $query->where('type', $type);
            }

            // Filtre par entrepôt
            if (filled($warehouseId = request('warehouse_id'))) {
                $query->where('warehouse_id', (int) $warehouseId);
            }

            // Filtre par variante
            if (filled($variantId = request('product_variant_id'))) {
                $query->where('product_variant_id', (int) $variantId);
            }

            $movements = $query->get();

            // Listes pour les selects de filtres
            $warehouses = ProductWarehouse::orderBy('name')->get();
            $variants = ProductVariant::with('product')->orderBy('sku')->get();

            // Log de l'accès à la liste des mouvements
            $this->logActivity(type: 'info', action: 'backend.product-stock-movements.index', context: ['count' => $movements->count()], message: null, activity: __("{$this->langPath}.index.success"), is_success: true);

            // Affiche la vue avec les mouvements récupérés
            return view('v1.backend.pages.products.stock-movements.index', compact('movements', 'warehouses', 'variants'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur avec le message d'exception
            $this->logActivity(type: 'error', action: 'backend.product-stock-movements.index', context: [], message: $e->getMessage(), activity: __("{$this->langPath}.index.error"), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération des mouvements
        }
    }

    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────
    public function backend_product_stock_movements_create(): View
    {
        try {

            // Listes pour les selects (entrepôts et variantes)
            $warehouses = ProductWarehouse::orderBy('name')->get();
            $variants = ProductVariant::with('product')->orderBy('sku')->get();

            // Log de l'accès à la page de création d'un mouvement
            $this->logActivity(type: 'info', action: 'backend.product-stock-movements.create', context: [], message: null, activity: __("{$this->langPath}.create.success"), is_success: true);

            // Affiche la vue de création d'un mouvement
            return view('v1.backend.pages.products.stock-movements.create', compact('warehouses', 'variants'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de l'accès à la page de création d'un mouvement
            $this->logActivity(type: 'error', action: 'backend.product-stock-movements.create', context: [], message: $e->getMessage(), activity: __("{$this->langPath}.create.error"), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de l'accès à la page de création
        }
    }

    // ─────────────────────────────────────────────
    // STORE — enregistre un mouvement via le cœur métier record()
    // ─────────────────────────────────────────────
    public function backend_product_stock_movements_store(StoreProductStockMovementRequest $request): RedirectResponse
    {
        try {

            // Enregistrement du mouvement via le service (met à jour le stock + crée l'audit)
            $movement = $this->productStockMovementService->backend_product_stock_movements_store($request->validated());

            // Log de la création du mouvement
            $this->logActivity(type: 'info', action: 'backend.product-stock-movements.store', context: ['movement_uuid' => $movement->uuid, 'type' => $movement->type->value, 'quantity' => $movement->quantity], message: null, activity: __("{$this->langPath}.store.success"), is_success: true, loggable: $movement);

            // Redirection vers le show du mouvement avec un message de succès
            return redirect()->route('backend.product-stock-movements.show', ['productStockMovement' => $movement->uuid])->with('success', __("{$this->langPath}.store.flash_success"));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la création du mouvement
            $this->logActivity(type: 'warning', action: 'backend.product-stock-movements.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->langPath}.store.error"), is_success: false);

            // Redirection avec le message métier explicite
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la création du mouvement
            $this->logActivity(type: 'error', action: 'backend.product-stock-movements.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->langPath}.store.error"), is_success: false);

            // Redirection avec un message d'erreur en cas de problème, en conservant les données du formulaire
            return redirect()->back()->with('error', __("{$this->langPath}.store.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function backend_product_stock_movements_show(ProductStockMovement $productStockMovement): View
    {
        try {

            // Charge les relations pour l'affichage
            $productStockMovement->load(['warehouse', 'variant.product', 'createdBy']);

            // Log de la consultation du détail d'un mouvement
            $this->logActivity(type: 'info', action: 'backend.product-stock-movements.show', context: ['movement_uuid' => $productStockMovement->uuid], message: null, activity: __("{$this->langPath}.show.success"), is_success: true, loggable: $productStockMovement);

            $movement = $productStockMovement;

            // Retourne la vue du mouvement avec les données nécessaires
            return view('v1.backend.pages.products.stock-movements.show', compact('movement'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la consultation du détail d'un mouvement
            $this->logActivity(type: 'error', action: 'backend.product-stock-movements.show', context: ['movement_uuid' => $productStockMovement->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.show.error"), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération du mouvement
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY (soft delete) — les mouvements ne se modifient jamais
    // ─────────────────────────────────────────────
    public function backend_product_stock_movements_destroy(ProductStockMovement $productStockMovement): RedirectResponse
    {
        try {

            $this->productStockMovementService->backend_product_stock_movements_destroy($productStockMovement);

            $this->logActivity(type: 'warning', action: 'backend.product-stock-movements.destroy', context: ['movement_uuid' => $productStockMovement->uuid], message: null, activity: __("{$this->langPath}.destroy.success"), is_success: true, loggable: $productStockMovement);

            return redirect()->route('backend.product-stock-movements.index')->with('success', __("{$this->langPath}.destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-stock-movements.destroy', context: ['movement_uuid' => $productStockMovement->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.warning"), is_success: false);

            abort(404);

        } catch (InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-stock-movements.destroy', context: ['movement_uuid' => $productStockMovement->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.warning_meta"), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-stock-movements.destroy', context: ['movement_uuid' => $productStockMovement->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.error"), is_success: false);

            return back()->with('error', __("{$this->langPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_product_stock_movements_trashed(): View
    {
        try {

            $movements = ProductStockMovement::onlyTrashed()->with(['warehouse', 'variant.product', 'createdBy'])->orderBy('deleted_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.product-stock-movements.trashed', context: ['count' => $movements->count()], message: null, activity: __("{$this->langPath}.trashed.success"), is_success: true);

            return view('v1.backend.pages.products.stock-movements.trashed', compact('movements'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-stock-movements.trashed', context: [], message: $e->getMessage(), activity: __("{$this->langPath}.trashed.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_product_stock_movements_restore(string $uuid): RedirectResponse
    {
        try {

            // Récupère le mouvement supprimé par son UUID ou échoue
            $movement = ProductStockMovement::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            $this->productStockMovementService->backend_product_stock_movements_restore($movement);

            $this->logActivity(type: 'info', action: 'backend.product-stock-movements.restore', context: ['movement_uuid' => $movement->uuid], message: null, activity: __("{$this->langPath}.restore.success"), is_success: true, loggable: $movement);

            return back()->with('success', __("{$this->langPath}.restore.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-stock-movements.restore', context: ['movement_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.warning"), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-stock-movements.restore', context: ['movement_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.warning_meta"), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-stock-movements.restore', context: ['movement_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.error"), is_success: false);

            return back()->with('error', __("{$this->langPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_stock_movements_force_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupère le mouvement supprimé par son UUID ou échoue
            $movement = ProductStockMovement::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            $this->productStockMovementService->backend_product_stock_movements_force_destroy($movement);

            $this->logActivity(type: 'info', action: 'backend.product-stock-movements.force_destroy', context: ['movement_uuid' => $movement->uuid], message: null, activity: __("{$this->langPath}.force_destroy.success"), is_success: true, loggable: $movement);

            return back()->with('success', __("{$this->langPath}.force_destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-stock-movements.force_destroy', context: ['movement_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.warning"), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-stock-movements.force_destroy', context: ['movement_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.warning_meta"), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-stock-movements.force_destroy', context: ['movement_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.error"), is_success: false);

            return back()->with('error', __("{$this->langPath}.force_destroy.flash_error"));
        }
    }
}
