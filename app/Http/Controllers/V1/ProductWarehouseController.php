<?php

namespace App\Http\Controllers\V1;

use App\Enums\Products\Warehouses\ProductWarehouseStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Products\Warehouses\StoreProductWarehouseRequest;
use App\Http\Requests\V1\Products\Warehouses\UpdateProductWarehouseRequest;
use App\Models\GeoCountry;
use App\Models\ProductWarehouse;
use App\Models\ProductWarehouseTranslation;
use App\Services\V1\Controllers\ProductWarehouseService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

use function __;

class ProductWarehouseController extends Controller
{
    use LogActivity;

    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/products/warehouses/messages/warehouses-messages-controller.warehouses';

    // Injection de dépendance du service de gestion des entrepôts
    public function __construct(protected ProductWarehouseService $productWarehouseService) {}

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_index(): View
    {
        try {

            // Récupère tous les entrepôts avec leur traduction active, leur pays et le nombre d'audits/logs
            $warehouses = ProductWarehouse::withActiveTranslation()
                ->with(['country'])
                ->withAuditsCountTotal()->withCount(['audits', 'activityLogs', 'locations'])
                ->orderBy('sort_order', 'desc')
                ->get();

            $this->logActivity(type: 'info', action: 'backend.product-warehouses.index', context: ['count' => $warehouses->count()], message: null, activity: __("{$this->backendLangPath}.index.success"), is_success: true);

            return view('v1.backend.pages.products.warehouses.index', compact('warehouses'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouses.index', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.index.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_create(): View
    {
        try {

            // Langues actives et pays pour le formulaire
            $langs = getActiveLangs()['langs'];
            $countries = GeoCountry::withActiveTranslation()->orderBy('code')->get();

            $this->logActivity(type: 'info', action: 'backend.product-warehouses.create', context: [], message: null, activity: __("{$this->backendLangPath}.create.success"), is_success: true);

            $lastSortOrder = getLastSortOrder(ProductWarehouse::class);

            return view('v1.backend.pages.products.warehouses.create', compact('langs', 'countries', 'lastSortOrder'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouses.create', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.create.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_store(StoreProductWarehouseRequest $request): RedirectResponse
    {
        try {

            $warehouse = $this->productWarehouseService->backend_product_warehouses_store($request->validated());

            $warehouseName = $warehouse->active_translation?->name ?? $warehouse->code;

            $this->logActivity(type: 'info', action: 'backend.product-warehouses.store', context: ['warehouse_uuid' => $warehouse->uuid, 'warehouse_code' => $warehouse->code], message: null, activity: __("{$this->backendLangPath}.store.success", ['name' => $warehouseName]), is_success: true, loggable: $warehouse);

            return redirect()
                ->route('backend.product-warehouses.show', ['productWarehouse' => $warehouse->uuid, 'slug' => $warehouse->active_translation?->slug ?? 'null'])
                ->with('success', __("{$this->backendLangPath}.store.flash_success", ['name' => $warehouseName]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouses.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouses.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.store.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // Résolution + vérification slug (helper privé)
    // ─────────────────────────────────────────────
    private function resolveWarehouseWithSlugCheck(ProductWarehouse $warehouse, string $slug, string $action, string $langKey): ProductWarehouse
    {
        $expectedSlug = $warehouse->active_translation?->slug ?? 'null';

        if ($expectedSlug !== $slug) {

            $this->logActivity(type: 'warning', action: $action, context: ['warehouse_uuid' => $warehouse->uuid, 'warehouse_slug' => $slug, 'expected_slug' => $expectedSlug], message: null, activity: __("{$this->backendLangPath}.{$langKey}.slug_mismatch"), is_success: false, loggable: $warehouse);

            abort(404);
        }

        return $warehouse;
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_show(ProductWarehouse $productWarehouse, string $slug): View
    {
        $warehouse = $this->resolveWarehouseWithSlugCheck($productWarehouse, $slug, 'backend.product-warehouses.show', 'show');

        try {

            $warehouse->load([
                'translations' => fn ($query) => $query->whereNull('deleted_at')->with('lang'),
                'country',
            ])->loadCount(['audits', 'activityLogs', 'locations']);

            $langs = getActiveLangs()['langs'];

            $productWarehouseTranslations = ProductWarehouseTranslation::onlyTrashed()
                ->where('warehouse_id', $warehouse->id)
                ->with('lang')
                ->get();

            $warehouseName = $warehouse->active_translation?->name ?? __("{$this->backendLangPath}.show.no_name");

            $this->logActivity(type: 'info', action: 'backend.product-warehouses.show', context: ['warehouse_uuid' => $warehouse->uuid, 'warehouse_code' => $warehouse->code], message: null, activity: __("{$this->backendLangPath}.show.success", ['name' => $warehouseName]), is_success: true, loggable: $warehouse);

            return view('v1.backend.pages.products.warehouses.show', compact('warehouse', 'langs', 'productWarehouseTranslations'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouses.show', context: ['warehouse_uuid' => $productWarehouse->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.show.error", ['uuid' => $productWarehouse->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // AUDITS
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_audits(ProductWarehouse $productWarehouse, string $slug): View
    {
        $warehouse = $this->resolveWarehouseWithSlugCheck($productWarehouse, $slug, 'backend.product-warehouses.audits', 'audits');

        try {

            $warehouse->load(['translations' => fn ($query) => $query->whereNull('deleted_at')->with('lang')])->loadCount(['audits', 'activityLogs', 'locations']);

            $warehouseName = $warehouse->active_translation?->name ?? '';

            $audits = $warehouse->auditsWithTranslations()->with(['user', 'auditable'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.product-warehouses.audits', context: ['warehouse_uuid' => $warehouse->uuid], message: null, activity: __("{$this->backendLangPath}.audits.success", ['name' => $warehouseName]), is_success: true, loggable: $warehouse);

            return view('v1.backend.pages.products.warehouses.others.audits', compact('warehouse', 'audits'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouses.audits', context: ['warehouse_uuid' => $productWarehouse->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.audits.error", ['uuid' => $productWarehouse->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVITY LOGS
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_activity_logs(ProductWarehouse $productWarehouse, string $slug): View
    {
        $warehouse = $this->resolveWarehouseWithSlugCheck($productWarehouse, $slug, 'backend.product-warehouses.activity-logs', 'activity-logs');

        try {

            $warehouse->load(['translations' => fn ($query) => $query->whereNull('deleted_at')->with('lang')])
                ->loadCount(['audits', 'activityLogs', 'locations']);

            $warehouseName = $warehouse->active_translation?->name ?? '';

            $logs = $warehouse->activityLogs()->with(['user'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.product-warehouses.activity-logs', context: ['warehouse_uuid' => $warehouse->uuid], message: null, activity: __("{$this->backendLangPath}.activity-logs.success", ['name' => $warehouseName]), is_success: true, loggable: $warehouse);

            return view('v1.backend.pages.products.warehouses.others.activity-logs', compact('warehouse', 'logs'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouses.activity-logs', context: ['warehouse_uuid' => $productWarehouse->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activity-logs.error", ['uuid' => $productWarehouse->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // LOCATIONS (emplacements rattachés à l'entrepôt)
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_locations(ProductWarehouse $productWarehouse, string $slug): View
    {
        $warehouse = $this->resolveWarehouseWithSlugCheck($productWarehouse, $slug, 'backend.product-warehouses.locations', 'locations');

        try {

            // Compteurs affichés par la navigation (_show-navigation / _show-start)
            $warehouse->loadCount(['locations', 'audits', 'activityLogs']);

            // Emplacements de l'entrepôt, prêts pour le tableau partagé du module emplacements
            $locations = $warehouse->locations()
                ->withActiveTranslation()
                ->with(['warehouse', 'parent'])
                ->withAuditsCountTotal()->withCount(['audits', 'activityLogs'])
                ->orderBy('sort_order')
                ->get();

            $warehouseName = $warehouse->active_translation?->name ?? __("{$this->backendLangPath}.show.no_name");

            $this->logActivity(type: 'info', action: 'backend.product-warehouses.locations', context: ['warehouse_uuid' => $warehouse->uuid, 'count' => $locations->count()], message: null, activity: __("{$this->backendLangPath}.locations.success", ['name' => $warehouseName]), is_success: true, loggable: $warehouse);

            return view('v1.backend.pages.products.warehouses.others.locations', compact('warehouse', 'locations'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouses.locations', context: ['warehouse_uuid' => $warehouse->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.locations.error", ['uuid' => $warehouse->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_edit(ProductWarehouse $productWarehouse): View
    {
        try {

            $langs = getActiveLangs()['langs'];
            $countries = GeoCountry::withActiveTranslation()->orderBy('code')->get();

            $warehouse = $productWarehouse->load(['translations.lang', 'country']);

            $this->logActivity(type: 'info', action: 'backend.product-warehouses.edit', context: ['warehouse_uuid' => $warehouse->uuid], message: null, activity: __("{$this->backendLangPath}.edit.success", ['name' => $warehouse->active_translation?->name ?? $warehouse->code]), is_success: true, loggable: $warehouse);

            $lastSortOrder = getLastSortOrder(ProductWarehouse::class);

            return view('v1.backend.pages.products.warehouses.edit', compact('warehouse', 'langs', 'countries', 'lastSortOrder'));

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

            $changes = $productWarehouse->getChanges();

            $productWarehouse->refresh()->load(['translations.lang']);

            $warehouseName = $productWarehouse->active_translation?->name ?? $productWarehouse->code;

            $this->logActivity(type: 'info', action: 'backend.product-warehouses.update', context: ['warehouse_uuid' => $productWarehouse->uuid, 'changes' => $changes], message: null, activity: __("{$this->backendLangPath}.update.success", ['name' => $warehouseName]), is_success: true, loggable: $productWarehouse);

            return redirect()
                ->route('backend.product-warehouses.show', ['productWarehouse' => $productWarehouse->uuid, 'slug' => $productWarehouse->active_translation?->slug ?? 'null'])
                ->with('success', __("{$this->backendLangPath}.update.flash_success", ['name' => $warehouseName]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouses.update', context: ['warehouse_uuid' => $productWarehouse->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.warning_meta", ['uuid' => $productWarehouse->uuid]), is_success: false);

            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouses.update', context: ['warehouse_uuid' => $productWarehouse->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.error", ['uuid' => $productWarehouse->uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.update.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // CHANGE STATUS (activer / désactiver)
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_change_status(Request $request, ProductWarehouse $productWarehouse): RedirectResponse
    {
        try {

            $newStatus = ProductWarehouseStatus::tryFrom((string) $request->input('status', ''));

            if ($newStatus === null) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.change_status.invalid_status"));
            }

            $oldStatus = $productWarehouse->status;

            $this->productWarehouseService->backend_product_warehouses_change_status($productWarehouse, $newStatus);

            $productWarehouse->refresh()->load(['translations.lang']);

            $warehouseName = $productWarehouse->active_translation?->name ?? $productWarehouse->code;

            $this->logActivity(type: 'info', action: 'backend.product-warehouses.change_status', context: ['warehouse_uuid' => $productWarehouse->uuid, 'old_status' => $oldStatus?->value, 'new_status' => $newStatus->value], message: null, activity: __("{$this->backendLangPath}.change_status.success", ['name' => $warehouseName, 'from' => $oldStatus?->label() ?? '', 'to' => $newStatus->label()]), is_success: true, loggable: $productWarehouse);

            return redirect()->back()->with('success', __("{$this->backendLangPath}.change_status.flash_success", ['name' => $warehouseName, 'to' => $newStatus->label()]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouses.change_status', context: ['warehouse_uuid' => $productWarehouse->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.change_status.warning_meta", ['uuid' => $productWarehouse->uuid]), is_success: false);

            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouses.change_status', context: ['warehouse_uuid' => $productWarehouse->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.change_status.error", ['uuid' => $productWarehouse->uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.change_status.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // SET DEFAULT
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_set_default(ProductWarehouse $productWarehouse): RedirectResponse
    {
        try {

            $this->productWarehouseService->backend_product_warehouses_set_default($productWarehouse);

            $productWarehouse->refresh()->load(['translations.lang']);

            $warehouseName = $productWarehouse->active_translation?->name ?? $productWarehouse->code;

            $this->logActivity(type: 'info', action: 'backend.product-warehouses.set_default', context: ['warehouse_uuid' => $productWarehouse->uuid], message: null, activity: __("{$this->backendLangPath}.set_default.success", ['name' => $warehouseName]), is_success: true, loggable: $productWarehouse);

            return redirect()->back()->with('success', __("{$this->backendLangPath}.set_default.flash_success", ['name' => $warehouseName]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouses.set_default', context: ['warehouse_uuid' => $productWarehouse->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.set_default.warning_meta", ['uuid' => $productWarehouse->uuid]), is_success: false);

            return redirect()->back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouses.set_default', context: ['warehouse_uuid' => $productWarehouse->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.set_default.error", ['uuid' => $productWarehouse->uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.set_default.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY (soft delete)
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_destroy(ProductWarehouse $productWarehouse): RedirectResponse
    {
        try {

            $warehouseName = $productWarehouse->active_translation?->name ?? __("{$this->backendLangPath}.destroy.unknown");

            $this->productWarehouseService->backend_product_warehouses_destroy($productWarehouse);

            $this->logActivity(type: 'info', action: 'backend.product-warehouses.destroy', context: ['warehouse_uuid' => $productWarehouse->uuid, 'warehouse_code' => $productWarehouse->code], message: null, activity: __("{$this->backendLangPath}.destroy.success", ['name' => $warehouseName]), is_success: true, loggable: $productWarehouse);

            return redirect()->route('backend.product-warehouses.index')->with('success', __("{$this->backendLangPath}.destroy.flash_success", ['name' => $warehouseName]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouses.destroy', context: ['warehouse_uuid' => $productWarehouse->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning_meta", ['uuid' => $productWarehouse->uuid]), is_success: false);

            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouses.destroy', context: ['warehouse_uuid' => $productWarehouse->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.error", ['uuid' => $productWarehouse->uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_trashed(): View
    {
        try {

            $warehouses = ProductWarehouse::onlyTrashed()->withTrashedActiveTranslation()->with(['country'])->withAuditsCountTotal()->withCount(['audits', 'activityLogs', 'locations'])->orderBy('deleted_at', 'desc')->get();

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

            $productWarehouse = ProductWarehouse::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            $this->productWarehouseService->backend_product_warehouses_restore($productWarehouse);

            $warehouseName = $productWarehouse->active_translation?->name ?? $productWarehouse->code;

            $this->logActivity(type: 'info', action: 'backend.product-warehouses.restore', context: ['warehouse_uuid' => $productWarehouse->uuid], message: null, activity: __("{$this->backendLangPath}.restore.success", ['name' => $warehouseName]), is_success: true, loggable: $productWarehouse);

            return redirect()->back()->with('success', __("{$this->backendLangPath}.restore.flash_success", ['name' => $warehouseName]));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouses.restore', context: ['warehouse_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.restore.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouses.restore', context: ['warehouse_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            return redirect()->back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouses.restore', context: ['warehouse_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_force_destroy(string $uuid): RedirectResponse
    {
        try {

            $productWarehouse = ProductWarehouse::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            $warehouseName = $productWarehouse->active_translation?->name ?? __("{$this->backendLangPath}.force_destroy.unknown");

            $this->productWarehouseService->backend_product_warehouses_force_destroy($productWarehouse);

            $this->logActivity(type: 'info', action: 'backend.product-warehouses.force_destroy', context: ['warehouse_uuid' => $productWarehouse->uuid], message: null, activity: __("{$this->backendLangPath}.force_destroy.success", ['name' => $warehouseName]), is_success: true, loggable: $productWarehouse);

            return redirect()->back()->with('success', __("{$this->backendLangPath}.force_destroy.flash_success", ['name' => $warehouseName]));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouses.force_destroy', context: ['warehouse_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouses.force_destroy', context: ['warehouse_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return redirect()->back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouses.force_destroy', context: ['warehouse_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_error"));
        }
    }
}
