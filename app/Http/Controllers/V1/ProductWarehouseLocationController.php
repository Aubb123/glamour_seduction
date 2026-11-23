<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Products\WarehouseLocations\StoreProductWarehouseLocationRequest;
use App\Http\Requests\V1\Products\WarehouseLocations\UpdateProductWarehouseLocationRequest;
use App\Models\ProductWarehouse;
use App\Models\ProductWarehouseLocation;
use App\Models\ProductWarehouseLocationTranslation;
use App\Services\V1\Controllers\ProductWarehouseLocationService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

use function __;

class ProductWarehouseLocationController extends Controller
{
    use LogActivity;

    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/products/warehouse-locations/messages/warehouse-locations-messages-controller.warehouseLocations';

    // Injection de dépendance du service de gestion des emplacements
    public function __construct(protected ProductWarehouseLocationService $productWarehouseLocationService) {}

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_locations_index(): View
    {
        try {

            // Récupère tous les emplacements avec leur traduction active, leur entrepôt,
            // leur parent et le nombre d'audits/logs
            $locations = ProductWarehouseLocation::withActiveTranslation()
                ->with(['warehouse', 'parent'])
                ->withCount(['audits', 'activityLogs'])
                ->orderBy('warehouse_id')
                ->orderBy('sort_order')
                ->get();

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-locations.index', context: ['count' => $locations->count()], message: null, activity: __("{$this->backendLangPath}.index.success"), is_success: true);

            return view('v1.backend.pages.products.warehouse-locations.index', compact('locations'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-locations.index', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.index.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_locations_create(): View
    {
        try {

            // Langues actives, entrepôts et emplacements candidats comme parent
            $langs = getActiveLangs()['langs'];
            $warehouses = ProductWarehouse::withActiveTranslation()->orderBy('sort_order')->get();
            $parentLocations = ProductWarehouseLocation::withActiveTranslation()->orderBy('sort_order')->get();

            // Dernier ordre d'affichage utilisé (indice affiché sous le champ sort_order)
            $lastSortOrder = getLastSortOrder(ProductWarehouseLocation::class);

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-locations.create', context: [], message: null, activity: __("{$this->backendLangPath}.create.success"), is_success: true);

            return view('v1.backend.pages.products.warehouse-locations.create', compact('langs', 'warehouses', 'parentLocations', 'lastSortOrder'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-locations.create', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.create.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_locations_store(StoreProductWarehouseLocationRequest $request): RedirectResponse
    {
        try {

            $location = $this->productWarehouseLocationService->backend_product_warehouse_locations_store($request->validated());

            $locationName = $location->active_translation?->name ?? $location->code;

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-locations.store', context: ['location_uuid' => $location->uuid, 'location_code' => $location->code], message: null, activity: __("{$this->backendLangPath}.store.success", ['name' => $locationName]), is_success: true, loggable: $location);

            return redirect()->route('backend.product-warehouse-locations.show', ['productWarehouseLocation' => $location->uuid])->with('success', __("{$this->backendLangPath}.store.flash_success", ['name' => $locationName]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-locations.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-locations.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.store.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_locations_show(ProductWarehouseLocation $productWarehouseLocation): View
    {
        $warehouse = $productWarehouseLocation->warehouse;
        $location = $productWarehouseLocation;

        try {

            $location->load([
                'translations' => fn ($query) => $query->whereNull('deleted_at')->with('lang'),
                'warehouse',
                'parent',
            ])->loadCount(['audits', 'activityLogs']);

            $langs = getActiveLangs()['langs'];

            $productWarehouseLocationTranslations = ProductWarehouseLocationTranslation::onlyTrashed()
                ->where('location_id', $location->id)
                ->with('lang')
                ->get();

            $locationName = $location->active_translation?->name ?? __("{$this->backendLangPath}.show.no_name");

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-locations.show', context: ['location_uuid' => $location->uuid, 'location_code' => $location->code], message: null, activity: __("{$this->backendLangPath}.show.success", ['name' => $locationName]), is_success: true, loggable: $location);

            return view('v1.backend.pages.products.warehouse-locations.show', compact('location', 'warehouse', 'langs', 'productWarehouseLocationTranslations'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-locations.show', context: ['location_uuid' => $location->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.show.error", ['uuid' => $location->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // AUDITS
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_locations_audits(ProductWarehouseLocation $productWarehouseLocation): View
    {
        $location = $productWarehouseLocation;

        try {

            $location->load(['translations' => fn ($query) => $query->whereNull('deleted_at')->with('lang'), 'warehouse', 'parent'])->loadCount(['audits', 'activityLogs']);

            $locationName = $location->active_translation?->name ?? '';

            $audits = $location->audits()->with(['user', 'auditable'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-locations.audits', context: ['location_uuid' => $location->uuid], message: null, activity: __("{$this->backendLangPath}.audits.success", ['name' => $locationName]), is_success: true, loggable: $location);

            return view('v1.backend.pages.products.warehouse-locations.others.audits', compact('location', 'audits'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-locations.audits', context: ['location_uuid' => $location->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.audits.error", ['uuid' => $location->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVITY LOGS
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_locations_activity_logs(ProductWarehouseLocation $productWarehouseLocation): View
    {
        $location = $productWarehouseLocation;

        try {

            $location->load(['translations' => fn ($query) => $query->whereNull('deleted_at')->with('lang'), 'warehouse', 'parent'])->loadCount(['audits', 'activityLogs']);

            $locationName = $location->active_translation?->name ?? '';

            $logs = $location->activityLogs()->with(['user'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-locations.activity-logs', context: ['location_uuid' => $location->uuid], message: null, activity: __("{$this->backendLangPath}.activity-logs.success", ['name' => $locationName]), is_success: true, loggable: $location);

            return view('v1.backend.pages.products.warehouse-locations.others.activity-logs', compact('location', 'logs'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-locations.activity-logs', context: ['location_uuid' => $location->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activity-logs.error", ['uuid' => $location->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_locations_edit(ProductWarehouseLocation $productWarehouseLocation): View
    {
        $warehouse = $productWarehouseLocation->warehouse;
        $location = $productWarehouseLocation;

        try {

            $langs = getActiveLangs()['langs'];

            // Parents candidats : même entrepôt, hors emplacement courant et hors descendants
            $excludedIds = [$location->id];
            $queue = [$location->id];

            while ($queue !== []) {
                $parentId = array_shift($queue);
                $children = ProductWarehouseLocation::where('parent_id', $parentId)->pluck('id')->all();

                foreach ($children as $childId) {
                    if (! in_array($childId, $excludedIds, true)) {
                        $excludedIds[] = $childId;
                        $queue[] = $childId;
                    }
                }
            }

            $parentLocations = ProductWarehouseLocation::withActiveTranslation()
                ->where('warehouse_id', $location->warehouse_id)
                ->whereNotIn('id', $excludedIds)
                ->orderBy('sort_order')
                ->get();

            $location->load(['translations.lang']);

            // Dernier ordre d'affichage utilisé (indice affiché sous le champ sort_order)
            $lastSortOrder = getLastSortOrder(ProductWarehouseLocation::class);

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-locations.edit', context: ['location_uuid' => $location->uuid], message: null, activity: __("{$this->backendLangPath}.edit.success", ['name' => $location->active_translation?->name ?? $location->code]), is_success: true, loggable: $location);

            return view('v1.backend.pages.products.warehouse-locations.edit', compact('warehouse', 'location', 'langs', 'parentLocations', 'lastSortOrder'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-locations.edit', context: ['location_uuid' => $location->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.edit.error", ['uuid' => $location->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_locations_update(UpdateProductWarehouseLocationRequest $request, ProductWarehouseLocation $productWarehouseLocation): RedirectResponse
    {
        $location = $productWarehouseLocation;

        try {

            $this->productWarehouseLocationService->backend_product_warehouse_locations_update($location, $request->validated());

            $changes = $location->getChanges();

            $location->refresh()->load(['translations.lang']);

            $locationName = $location->active_translation?->name ?? $location->code;

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-locations.update', context: ['location_uuid' => $location->uuid, 'changes' => $changes], message: null, activity: __("{$this->backendLangPath}.update.success", ['name' => $locationName]), is_success: true, loggable: $location);

            return redirect()->route('backend.product-warehouse-locations.show', ['productWarehouseLocation' => $location->uuid])->with('success', __("{$this->backendLangPath}.update.flash_success", ['name' => $locationName]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-locations.update', context: ['location_uuid' => $location->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.warning_meta", ['uuid' => $location->uuid]), is_success: false);

            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-locations.update', context: ['location_uuid' => $location->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.error", ['uuid' => $location->uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.update.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_locations_activate(Request $request, ProductWarehouseLocation $productWarehouseLocation): RedirectResponse
    {
        $location = $productWarehouseLocation;

        try {

            // Activation de l'emplacement via le service
            $this->productWarehouseLocationService->backend_product_warehouse_locations_activate($location);

            $locationName = $location->active_translation?->name ?? $location->code;

            // Log de l'activation de l'emplacement
            $this->logActivity(type: 'info', action: 'backend.product-warehouse-locations.activate', context: ['location_uuid' => $location->uuid, 'location_code' => $location->code], message: null, activity: __("{$this->backendLangPath}.activate.success", ['name' => $locationName]), is_success: true, loggable: $location);

            // Redirection avec un message de succès
            return back()->with('success', __("{$this->backendLangPath}.activate.flash_success", ['name' => $locationName]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-locations.activate', context: ['location_uuid' => $location->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activate.warning_meta", ['uuid' => $location->uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-locations.activate', context: ['location_uuid' => $location->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activate.error", ['uuid' => $location->uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.activate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_locations_deactivate(Request $request, ProductWarehouseLocation $productWarehouseLocation): RedirectResponse
    {
        $location = $productWarehouseLocation;

        try {

            // Désactivation de l'emplacement via le service
            $this->productWarehouseLocationService->backend_product_warehouse_locations_deactivate($location);

            $locationName = $location->active_translation?->name ?? $location->code;

            // Log de la désactivation de l'emplacement
            $this->logActivity(type: 'info', action: 'backend.product-warehouse-locations.deactivate', context: ['location_uuid' => $location->uuid, 'location_code' => $location->code], message: null, activity: __("{$this->backendLangPath}.deactivate.success", ['name' => $locationName]), is_success: true, loggable: $location);

            // Redirection avec un message de succès
            return back()->with('success', __("{$this->backendLangPath}.deactivate.flash_success", ['name' => $locationName]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-locations.deactivate', context: ['location_uuid' => $location->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.deactivate.warning_meta", ['uuid' => $location->uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-locations.deactivate', context: ['location_uuid' => $location->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.deactivate.error", ['uuid' => $location->uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.deactivate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY (soft delete)
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_locations_destroy(ProductWarehouseLocation $productWarehouseLocation): RedirectResponse
    {
        $location = $productWarehouseLocation;

        try {

            $locationName = $location->active_translation?->name ?? __("{$this->backendLangPath}.destroy.unknown");

            $this->productWarehouseLocationService->backend_product_warehouse_locations_destroy($location);

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-locations.destroy', context: ['location_uuid' => $location->uuid, 'location_code' => $location->code], message: null, activity: __("{$this->backendLangPath}.destroy.success", ['name' => $locationName]), is_success: true, loggable: $location);

            return redirect()->route('backend.product-warehouse-locations.index')->with('success', __("{$this->backendLangPath}.destroy.flash_success", ['name' => $locationName]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-locations.destroy', context: ['location_uuid' => $location->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning_meta", ['uuid' => $location->uuid]), is_success: false);

            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-locations.destroy', context: ['location_uuid' => $location->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.error", ['uuid' => $location->uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_locations_trashed(): View
    {
        try {

            $locations = ProductWarehouseLocation::onlyTrashed()->withTrashedActiveTranslation()->with(['warehouse', 'parent'])->withCount(['audits', 'activityLogs'])->orderBy('deleted_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-locations.trashed', context: ['count' => $locations->count()], message: null, activity: __("{$this->backendLangPath}.trashed.success"), is_success: true);

            return view('v1.backend.pages.products.warehouse-locations.trashed', compact('locations'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-locations.trashed', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.trashed.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_locations_restore(string $uuid): RedirectResponse
    {
        try {

            $location = ProductWarehouseLocation::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            $this->productWarehouseLocationService->backend_product_warehouse_locations_restore($location);

            $locationName = $location->active_translation?->name ?? $location->code;

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-locations.restore', context: ['location_uuid' => $location->uuid], message: null, activity: __("{$this->backendLangPath}.restore.success", ['name' => $locationName]), is_success: true, loggable: $location);

            return redirect()->back()->with('success', __("{$this->backendLangPath}.restore.flash_success", ['name' => $locationName]));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-locations.restore', context: ['location_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.restore.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-locations.restore', context: ['location_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            return redirect()->back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-locations.restore', context: ['location_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_locations_force_destroy(string $uuid): RedirectResponse
    {
        try {

            $location = ProductWarehouseLocation::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            $locationName = $location->active_translation?->name ?? __("{$this->backendLangPath}.force_destroy.unknown");

            $this->productWarehouseLocationService->backend_product_warehouse_locations_force_destroy($location);

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-locations.force_destroy', context: ['location_uuid' => $location->uuid], message: null, activity: __("{$this->backendLangPath}.force_destroy.success", ['name' => $locationName]), is_success: true, loggable: $location);

            return redirect()->back()->with('success', __("{$this->backendLangPath}.force_destroy.flash_success", ['name' => $locationName]));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-locations.force_destroy', context: ['location_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-locations.force_destroy', context: ['location_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return redirect()->back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-locations.force_destroy', context: ['location_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_error"));
        }
    }
}
