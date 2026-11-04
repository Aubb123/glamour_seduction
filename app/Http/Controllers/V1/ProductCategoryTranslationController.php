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
    protected string $backendLangPath = 'v1/backend/views/pages/products/categories/messages/categories-messages-controller.productCategories.translations';

    public function __construct(
        protected ProductCategoryTranslationService $productCategoryTranslationService
    ) {}

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_category_translations_destroy(string $uuid): RedirectResponse
    {
        try {

            // $translation récupère la traduction de catégorie de produit par son UUID, ou échoue si elle n'existe pas
            $translation = ProductCategoryTranslation::where('uuid', $uuid)->firstOrFail();

            // Suppression via le service (soft delete)
            $this->productCategoryTranslationService->backend_product_category_translations_destroy($translation);

            // Log de l'activité de suppression
            $this->logActivity(type: 'warning', action: 'backend.product-category-translations.destroy', context: ['translation_uuid' => $translation->uuid], message: null, activity: __("{$this->backendLangPath}.destroy.success", ['uuid' => $translation->uuid]), is_success: true, loggable: $translation);

            // Redirection vers la page précédente avec un message de succès
            return back()->with('success', __("{$this->backendLangPath}.destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur de non-trouvabilité de la traduction à supprimer
            $this->logActivity(type: 'warning', action: 'backend.product-category-translations.destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning", ['uuid' => $uuid]), is_success: false);

            // Redirection vers la page précédente avec un message d'erreur de non-trouvabilité
            return back()->with('error', __("{$this->backendLangPath}.destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la suppression de la traduction
            $this->logActivity(type: 'warning', action: 'backend.product-category-translations.destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            // Redirection vers la page précédente avec un message d'erreur métier explicite
            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la suppression de la traduction
            $this->logActivity(type: 'error', action: 'backend.product-category-translations.destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.error", ['uuid' => $uuid]), is_success: false);

            // Redirection vers la page précédente avec un message d'erreur générique
            return back()->with('error', __("{$this->backendLangPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_product_category_translations_trashed(string $category_uuid): View
    {
        try {

            // $category récupère la catégorie de produit par son UUID, même si elle est supprimée (soft deleted)
            $category = ProductCategory::withTrashed()->where('uuid', $category_uuid)->firstOrFail();

            // Récupération des traductions supprimées (soft deleted) pour cette catégorie
            $trashedTranslations = ProductCategoryTranslation::onlyTrashed()->where('category_id', $category->id)->get();

            // Log de l'accès à la liste des traductions supprimées
            $this->logActivity(type: 'info', action: 'backend.product-category-translations.trashed', context: ['count' => $trashedTranslations->count(), 'category_uuid' => $category_uuid], message: null, activity: __("{$this->backendLangPath}.trashed.accessed", ['name' => $category->active_translation?->name ?? $category_uuid]), is_success: true, loggable: $category);

            // Retourne la vue avec les traductions supprimées et la catégorie
            return view('v1.backend.pages.products.categories.translations.trashed', compact('trashedTranslations', 'category'));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur de non-trouvabilité de la catégorie pour laquelle on veut voir les traductions supprimées
            $this->logActivity(type: 'warning', action: 'backend.product-category-translations.trashed', context: ['category_uuid' => $category_uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.trashed.not_found", ['uuid' => $category_uuid]), is_success: false);

            // Redirection vers la page précédente avec un message d'erreur de non-trouvabilité
            return back()->with('error', __("{$this->backendLangPath}.trashed.not_found", ['uuid' => $category_uuid]));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de l'accès à la liste des traductions supprimées pour la catégorie
            $this->logActivity(type: 'error', action: 'backend.product-category-translations.trashed', context: ['category_uuid' => $category_uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.trashed.error", ['uuid' => $category_uuid]), is_success: false);

            // Redirection vers la page précédente avec un message d'erreur générique
            return back()->with('error', __("{$this->backendLangPath}.trashed.error", ['uuid' => $category_uuid]));
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_product_category_translations_restore(string $uuid): RedirectResponse
    {
        try {

            // Récupération de la traduction supprimée (soft deleted) par son UUID
            $translation = ProductCategoryTranslation::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Restauration via le service
            $this->productCategoryTranslationService->backend_product_category_translations_restore($translation);

            // Log de l'activité de restauration
            $this->logActivity(type: 'info', action: 'backend.product-category-translations.restore', context: ['translation_uuid' => $translation->uuid], message: null, activity: __("{$this->backendLangPath}.restore.success", ['uuid' => $translation->uuid]), is_success: true, loggable: $translation);

            // Redirection vers la page précédente avec un message de succès
            return back()->with('success', __("{$this->backendLangPath}.restore.flash_success"));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur de non-trouvabilité de la traduction à restaurer
            $this->logActivity(type: 'warning', action: 'backend.product-category-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            // Redirection vers la page précédente avec un message d'erreur de non-trouvabilité
            return back()->with('error', __("{$this->backendLangPath}.restore.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la restauration de la traduction
            $this->logActivity(type: 'warning', action: 'backend.product-category-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            // Redirection vers la page précédente avec un message d'erreur métier explicite
            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la restauration de la traduction
            $this->logActivity(type: 'error', action: 'backend.product-category-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            // Redirection vers la page précédente avec un message d'erreur générique
            return back()->with('error', __("{$this->backendLangPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_category_translations_force_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupération de la traduction supprimée (soft deleted) par son UUID
            $translation = ProductCategoryTranslation::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Suppression définitive via le service
            $this->productCategoryTranslationService->backend_product_category_translations_force_destroy($translation);

            // Log de l'activité
            $this->logActivity(type: 'info', action: 'backend.product-category-translations.force_destroy', context: ['translation_uuid' => $uuid], message: null, activity: __("{$this->backendLangPath}.force_destroy.success", ['uuid' => $uuid]), is_success: true, loggable: $translation);

            // Redirection vers la page précédente avec un message de succès
            return back()->with('success', __("{$this->backendLangPath}.force_destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur de non-trouvabilité de la traduction à supprimer définitivement
            $this->logActivity(type: 'warning', action: 'backend.product-category-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            // Redirection vers la page précédente avec un message d'erreur de non-trouvabilité
            return back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la suppression définitive de la traduction
            $this->logActivity(type: 'warning', action: 'backend.product-category-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            // Redirection vers la page précédente avec un message d'erreur métier explicite
            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la suppression définitive de la traduction
            $this->logActivity(type: 'error', action: 'backend.product-category-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            // Redirection vers la page précédente avec un message d'erreur générique
            return back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_error"));
        }
    }
}
