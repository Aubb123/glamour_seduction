<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ProductWarehouse;
use App\Models\ProductWarehouseTranslation;
use App\Services\V1\Controllers\ProductWarehouseTranslationService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

use function __;

class ProductWarehouseTranslationController extends Controller
{
    use LogActivity;

    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/products/warehouses/messages/warehouses-messages-controller.warehouses';

    public function __construct(
        protected ProductWarehouseTranslationService $productWarehouseTranslationService
    ) {}

    // ─────────────────────────────────────────────
    // DESTROY (soft delete d'une traduction)
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_translations_destroy(string $uuid): RedirectResponse
    {
        try {

            $translation = ProductWarehouseTranslation::where('uuid', $uuid)->firstOrFail();

            $this->productWarehouseTranslationService->backend_product_warehouse_translations_destroy($translation);

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-translations.destroy', context: ['translation_uuid' => $translation->uuid], message: null, activity: __("{$this->backendLangPath}.translations.destroy.success", ['uuid' => $translation->uuid]), is_success: true, loggable: $translation);

            return back()->with('success', __("{$this->backendLangPath}.translations.destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur : traduction d'entrepôt introuvable
            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-translations.destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.translations.destroy.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.translations.destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-translations.destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.translations.destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-translations.destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.translations.destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.translations.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED (Corbeille des traductions d'un entrepôt)
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_translations_trashed(string $warehouse_uuid): View|RedirectResponse
    {
        try {

            $warehouse = ProductWarehouse::where('uuid', $warehouse_uuid)->firstOrFail();

            $warehouseTitle = $warehouse->active_translation?->name ?? $warehouse->code;

            $productWarehouseTranslations = ProductWarehouseTranslation::onlyTrashed()->where('warehouse_id', $warehouse->id)->with('lang')->get();

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-translations.trashed', context: ['warehouse_uuid' => $warehouse->uuid], message: null, activity: __("{$this->backendLangPath}.translations.trashed.accessed", ['name' => $warehouseTitle]), is_success: true, loggable: $warehouse);

            return view('v1.backend.pages.products.warehouses.translations.trashed', compact('warehouse', 'productWarehouseTranslations'));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-translations.trashed', context: ['warehouse_uuid' => $warehouse_uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.translations.trashed.warning", ['uuid' => $warehouse_uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.translations.trashed.flash_warning"));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-translations.trashed', context: ['warehouse_uuid' => $warehouse_uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.translations.trashed.error", ['uuid' => $warehouse_uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.translations.trashed.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_translations_restore(string $uuid): RedirectResponse
    {
        try {

            $translation = ProductWarehouseTranslation::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            $this->productWarehouseTranslationService->backend_product_warehouse_translations_restore($translation);

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-translations.restore', context: ['translation_uuid' => $uuid], message: null, activity: __("{$this->backendLangPath}.translations.restore.success", ['uuid' => $uuid]), is_success: true, loggable: $translation);

            return back()->with('success', __("{$this->backendLangPath}.translations.restore.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.translations.restore.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.translations.restore.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.translations.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.translations.restore.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.translations.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_translations_force_destroy(string $uuid): RedirectResponse
    {
        try {

            $translation = ProductWarehouseTranslation::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            $this->productWarehouseTranslationService->backend_product_warehouse_translations_force_destroy($translation);

            $this->logActivity(type: 'info', action: 'backend.product-warehouse-translations.force_destroy', context: ['translation_uuid' => $uuid], message: null, activity: __("{$this->backendLangPath}.translations.force_destroy.success", ['uuid' => $uuid]), is_success: true, loggable: $translation);

            return back()->with('success', __("{$this->backendLangPath}.translations.force_destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.translations.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.translations.force_destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.translations.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.translations.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.translations.force_destroy.flash_error"));
        }
    }
}
