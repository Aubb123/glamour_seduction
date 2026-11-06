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
    protected string $backendLangPath = 'v1/backend/views/pages/products/warehouses/messages/warehouses-messages-controller.productWarehouses.translations';

    public function __construct(
        protected ProductWarehouseTranslationService $productWarehouseTranslationService
    ) {}

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_translations_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupère la traduction de l'entrepôt par son UUID, ou échoue si elle n'existe pas
            $translation = ProductWarehouseTranslation::where('uuid', $uuid)->firstOrFail();

            // Suppression via le service (soft delete)
            $this->productWarehouseTranslationService->backend_product_warehouse_translations_destroy($translation);

            // Log de l'activité de suppression
            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-translations.destroy', context: ['translation_uuid' => $translation->uuid], message: null, activity: __("{$this->backendLangPath}.destroy.success", ['uuid' => $translation->uuid]), is_success: true, loggable: $translation);

            // Redirection vers la page précédente avec un message de succès
            return back()->with('success', __("{$this->backendLangPath}.destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur de non-trouvabilité de la traduction à supprimer
            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-translations.destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la suppression de la traduction
            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-translations.destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la suppression de la traduction
            $this->logActivity(type: 'error', action: 'backend.product-warehouse-translations.destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_translations_trashed(string $warehouse_uuid): View
    {
        try {

            // Récupère l'entrepôt par son UUID même s'il est supprimé (soft deleted)
            $warehouse = ProductWarehouse::withTrashed()->where('uuid', $warehouse_uuid)->firstOrFail();

            // Récupération des traductions supprimées (soft deleted) pour cet entrepôt
            $trashedTranslations = ProductWarehouseTranslation::onlyTrashed()->where('warehouse_id', $warehouse->id)->get();

            // Log de l'accès à la liste des traductions supprimées
            $this->logActivity(type: 'info', action: 'backend.product-warehouse-translations.trashed', context: ['count' => $trashedTranslations->count(), 'warehouse_uuid' => $warehouse_uuid], message: null, activity: __("{$this->backendLangPath}.trashed.accessed", ['name' => $warehouse->active_translation?->name ?? $warehouse_uuid]), is_success: true, loggable: $warehouse);

            // Retourne la vue avec les traductions supprimées et l'entrepôt
            return view('v1.backend.pages.products.warehouses.translations.trashed', compact('trashedTranslations', 'warehouse'));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur de non-trouvabilité de l'entrepôt
            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-translations.trashed', context: ['warehouse_uuid' => $warehouse_uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.trashed.not_found", ['uuid' => $warehouse_uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.trashed.not_found", ['uuid' => $warehouse_uuid]));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de l'accès à la liste des traductions supprimées
            $this->logActivity(type: 'error', action: 'backend.product-warehouse-translations.trashed', context: ['warehouse_uuid' => $warehouse_uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.trashed.error", ['uuid' => $warehouse_uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.trashed.error", ['uuid' => $warehouse_uuid]));
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_translations_restore(string $uuid): RedirectResponse
    {
        try {

            // Récupération de la traduction supprimée (soft deleted) par son UUID
            $translation = ProductWarehouseTranslation::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Restauration via le service
            $this->productWarehouseTranslationService->backend_product_warehouse_translations_restore($translation);

            // Log de l'activité de restauration
            $this->logActivity(type: 'info', action: 'backend.product-warehouse-translations.restore', context: ['translation_uuid' => $translation->uuid], message: null, activity: __("{$this->backendLangPath}.restore.success", ['uuid' => $translation->uuid]), is_success: true, loggable: $translation);

            return back()->with('success', __("{$this->backendLangPath}.restore.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.restore.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_translations_force_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupération de la traduction supprimée (soft deleted) par son UUID
            $translation = ProductWarehouseTranslation::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Suppression définitive via le service
            $this->productWarehouseTranslationService->backend_product_warehouse_translations_force_destroy($translation);

            // Log de l'activité
            $this->logActivity(type: 'info', action: 'backend.product-warehouse-translations.force_destroy', context: ['translation_uuid' => $uuid], message: null, activity: __("{$this->backendLangPath}.force_destroy.success", ['uuid' => $uuid]), is_success: true, loggable: $translation);

            return back()->with('success', __("{$this->backendLangPath}.force_destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-warehouse-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-warehouse-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_error"));
        }
    }
}
