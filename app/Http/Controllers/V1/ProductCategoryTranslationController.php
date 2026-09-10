<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Models\ProductCategoryTranslation;
use App\Services\V1\Controllers\ProductCategoryTranslationService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

use function __;

class ProductCategoryTranslationController extends Controller
{
    use LogActivity;

    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/products/categories/messages/categories-messages-controller.productCategories.translations';

    public function __construct(
        protected ProductCategoryTranslationService $productCategoryTranslationService
    ) {}

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_category_translations_destroy(string $uuid): RedirectResponse
    {
        try {

            $translation = ProductCategoryTranslation::where('uuid', $uuid)->firstOrFail();

            // Suppression via le service (soft delete)
            $this->productCategoryTranslationService->destroy($translation);

            $this->logActivity(type: 'warning', action: 'backend.product-category-translations.destroy', context: ['translation_uuid' => $translation->uuid], message: null, activity: __("{$this->langPath}.destroy.success", ['uuid' => $translation->uuid]), is_success: true, loggable: $translation);

            return back()->with('success', __("{$this->langPath}.destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-category-translations.destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-category-translations.destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-category-translations.destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_product_category_translations_trashed(string $category_uuid): View
    {
        try {

            $category = ProductCategory::withTrashed()->where('uuid', $category_uuid)->firstOrFail();

            $trashedTranslations = ProductCategoryTranslation::onlyTrashed()->where('category_id', $category->id)->get();

            $this->logActivity(type: 'info', action: 'backend.product-category-translations.trashed', context: ['count' => $trashedTranslations->count(), 'category_uuid' => $category_uuid], message: null, activity: __("{$this->langPath}.trashed.accessed", ['name' => $category->active_translation?->name ?? $category_uuid]), is_success: true, loggable: $category);

            return view('v1.backend.pages.products.categories.translations.trashed', compact('trashedTranslations', 'category'));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-category-translations.trashed', context: ['category_uuid' => $category_uuid], message: $e->getMessage(), activity: __("{$this->langPath}.trashed.not_found", ['uuid' => $category_uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.trashed.not_found", ['uuid' => $category_uuid]));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-category-translations.trashed', context: ['category_uuid' => $category_uuid], message: $e->getMessage(), activity: __("{$this->langPath}.trashed.error", ['uuid' => $category_uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.trashed.error", ['uuid' => $category_uuid]));
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_product_category_translations_restore(string $uuid): RedirectResponse
    {
        try {

            // Récupération de la traduction supprimée
            $translation = ProductCategoryTranslation::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Restauration via le service
            $this->productCategoryTranslationService->restore($translation);

            $this->logActivity(type: 'info', action: 'backend.product-category-translations.restore', context: ['translation_uuid' => $translation->uuid], message: null, activity: __("{$this->langPath}.restore.success", ['uuid' => $translation->uuid]), is_success: true, loggable: $translation);

            return back()->with('success', __("{$this->langPath}.restore.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-category-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.restore.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-category-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-category-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_category_translations_force_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupération de la traduction supprimée
            $translation = ProductCategoryTranslation::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Suppression définitive via le service
            $this->productCategoryTranslationService->forceDestroy($translation);

            // Log de l'activité
            $this->logActivity(type: 'info', action: 'backend.product-category-translations.force_destroy', context: ['translation_uuid' => $uuid], message: null, activity: __("{$this->langPath}.force_destroy.success", ['uuid' => $uuid]), is_success: true, loggable: $translation);

            return back()->with('success', __("{$this->langPath}.force_destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-category-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.force_destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-category-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-category-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.force_destroy.flash_error"));
        }
    }
}
