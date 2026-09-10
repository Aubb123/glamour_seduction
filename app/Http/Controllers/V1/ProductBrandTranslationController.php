<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ProductBrand;
use App\Models\ProductBrandTranslation;
use App\Services\V1\Controllers\ProductBrandTranslationService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

use function __;

class ProductBrandTranslationController extends Controller
{
    use LogActivity;

    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/products/brands/messages/brands-messages-controller.productBrands.translations';

    public function __construct(
        protected ProductBrandTranslationService $productBrandTranslationService
    ) {}

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_brand_translations_destroy(string $uuid): RedirectResponse
    {
        try {

            $translation = ProductBrandTranslation::where('uuid', $uuid)->firstOrFail();

            // Suppression via le service (soft delete)
            $this->productBrandTranslationService->destroy($translation);

            $this->logActivity(type: 'warning', action: 'backend.product-brand-translations.destroy', context: ['translation_uuid' => $translation->uuid], message: null, activity: __("{$this->langPath}.destroy.success", ['uuid' => $translation->uuid]), is_success: true, loggable: $translation);

            return back()->with('success', __("{$this->langPath}.destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-brand-translations.destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-brand-translations.destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-brand-translations.destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_product_brand_translations_trashed(string $brand_uuid): View
    {
        try {

            $brand = ProductBrand::withTrashed()->where('uuid', $brand_uuid)->firstOrFail();

            $trashedTranslations = ProductBrandTranslation::onlyTrashed()->where('brand_id', $brand->id)->get();

            $this->logActivity(type: 'info', action: 'backend.product-brand-translations.trashed', context: ['count' => $trashedTranslations->count(), 'brand_uuid' => $brand_uuid], message: null, activity: __("{$this->langPath}.trashed.accessed", ['name' => $brand->active_translation?->name ?? $brand_uuid]), is_success: true, loggable: $brand);

            return view('v1.backend.pages.products.brands.translations.trashed', compact('trashedTranslations', 'brand'));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-brand-translations.trashed', context: ['brand_uuid' => $brand_uuid], message: $e->getMessage(), activity: __("{$this->langPath}.trashed.not_found", ['uuid' => $brand_uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.trashed.not_found", ['uuid' => $brand_uuid]));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-brand-translations.trashed', context: ['brand_uuid' => $brand_uuid], message: $e->getMessage(), activity: __("{$this->langPath}.trashed.error", ['uuid' => $brand_uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.trashed.error", ['uuid' => $brand_uuid]));
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_product_brand_translations_restore(string $uuid): RedirectResponse
    {
        try {

            // Récupération de la traduction supprimée
            $translation = ProductBrandTranslation::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Restauration via le service
            $this->productBrandTranslationService->restore($translation);

            $this->logActivity(type: 'info', action: 'backend.product-brand-translations.restore', context: ['translation_uuid' => $translation->uuid], message: null, activity: __("{$this->langPath}.restore.success", ['uuid' => $translation->uuid]), is_success: true, loggable: $translation);

            return back()->with('success', __("{$this->langPath}.restore.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-brand-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.restore.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-brand-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-brand-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_brand_translations_force_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupération de la traduction supprimée
            $translation = ProductBrandTranslation::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Suppression définitive via le service
            $this->productBrandTranslationService->forceDestroy($translation);

            // Log de l'activité
            $this->logActivity(type: 'info', action: 'backend.product-brand-translations.force_destroy', context: ['translation_uuid' => $uuid], message: null, activity: __("{$this->langPath}.force_destroy.success", ['uuid' => $uuid]), is_success: true, loggable: $translation);

            return back()->with('success', __("{$this->langPath}.force_destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-brand-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.force_destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-brand-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-brand-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.force_destroy.flash_error"));
        }
    }
}
