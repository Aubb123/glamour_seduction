<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ProductWarehouseLocation;
use App\Models\ProductWarehouseLocationTranslation;
use App\Services\V1\Controllers\ProductWarehouseLocationTranslationService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

use function __;

class ProductWarehouseLocationTranslationController extends Controller
{
    use LogActivity;

    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/products/warehouse-locations/messages/warehouse-locations-messages-controller.warehouseLocations';

    public function __construct(
        protected ProductWarehouseLocationTranslationService $productWarehouseLocationTranslationService
    ) {}

    // ─────────────────────────────────────────────
    // DESTROY (soft delete d'une traduction)
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_location_translations_destroy(ProductWarehouseLocationTranslation $productWarehouseLocationTranslation): RedirectResponse
    {
        try {

            // Traduction résolue par le binding implicite sur uuid (route {productWarehouseLocationTranslation})
            $translation = $productWarehouseLocationTranslation;

            $this->productWarehouseLocationTranslationService->backend_product_warehouse_location_translations_destroy($translation);

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-location-translations.destroy', context: ['translation_uuid' => $translation->uuid], message: null, activity: __("{$this->backendLangPath}.translations.destroy.success", ['uuid' => $translation->uuid]), is_success: true, loggable: $translation);

            return back()->with('success', __("{$this->backendLangPath}.translations.destroy.flash_success"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-location-translations.destroy', context: ['translation_uuid' => $translation->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.translations.destroy.warning_meta", ['uuid' => $translation->uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-location-translations.destroy', context: ['translation_uuid' => $translation->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.translations.destroy.error", ['uuid' => $translation->uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.translations.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED (Corbeille des traductions d'un emplacement)
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_location_translations_trashed(ProductWarehouseLocation $productWarehouseLocation): View
    {
        try {

            // Emplacement d'entrepôt résolu par le binding implicite sur uuid (route {productWarehouseLocation} avec ->withTrashed())
            $location = $productWarehouseLocation;

            $locationName = $location->active_translation?->name ?? $location->code;

            $productWarehouseLocationTranslations = ProductWarehouseLocationTranslation::onlyTrashed()->where('location_id', $location->id)->with('lang')->get();

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-location-translations.trashed', context: ['location_uuid' => $location->uuid], message: null, activity: __("{$this->backendLangPath}.translations.trashed.accessed", ['name' => $locationName]), is_success: true, loggable: $location);

            return view('v1.backend.pages.products.warehouse-locations.translations.trashed', compact('location', 'productWarehouseLocationTranslations'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-location-translations.trashed', context: ['location_uuid' => $location->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.translations.trashed.error", ['uuid' => $location->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_location_translations_restore(string $uuid): RedirectResponse
    {
        try {

            $translation = ProductWarehouseLocationTranslation::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            $this->productWarehouseLocationTranslationService->backend_product_warehouse_location_translations_restore($translation);

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-location-translations.restore', context: ['translation_uuid' => $uuid], message: null, activity: __("{$this->backendLangPath}.translations.restore.success", ['uuid' => $uuid]), is_success: true, loggable: $translation);

            return back()->with('success', __("{$this->backendLangPath}.translations.restore.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-location-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.translations.restore.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.translations.restore.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-location-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.translations.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-location-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.translations.restore.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.translations.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_location_translations_force_destroy(string $uuid): RedirectResponse
    {
        try {

            $translation = ProductWarehouseLocationTranslation::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            $this->productWarehouseLocationTranslationService->backend_product_warehouse_location_translations_force_destroy($translation);

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-location-translations.force_destroy', context: ['translation_uuid' => $uuid], message: null, activity: __("{$this->backendLangPath}.translations.force_destroy.success", ['uuid' => $uuid]), is_success: true, loggable: $translation);

            return back()->with('success', __("{$this->backendLangPath}.translations.force_destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-location-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.translations.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.translations.force_destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-location-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.translations.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-location-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.translations.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.translations.force_destroy.flash_error"));
        }
    }
}
