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
    use LogActivity; // Trait pour la journalisation des activités

    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/products/brands/messages/brands-messages-controller.productBrands.translations';

    // Injection de dépendance du service de traduction de marque de produit
    public function __construct(
        protected ProductBrandTranslationService $productBrandTranslationService
    ) {}

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_brand_translations_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupération de la traduction par son UUID ou échoue
            $translation = ProductBrandTranslation::where('uuid', $uuid)->firstOrFail();

            // Suppression via le service (soft delete)
            $this->productBrandTranslationService->backend_product_brand_translations_destroy($translation);

            // Log de l'activité de suppression
            $this->logActivity(type: 'warning', action: 'backend.product-brand-translations.destroy', context: ['translation_uuid' => $translation->uuid], message: null, activity: __("{$this->langPath}.destroy.success", ['uuid' => $translation->uuid]), is_success: true, loggable: $translation);

            // Redirection vers la page précédente avec un message de succès
            return back()->with('success', __("{$this->langPath}.destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur de non-trouvabilité de la traduction à supprimer
            $this->logActivity(type: 'warning', action: 'backend.product-brand-translations.destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.warning", ['uuid' => $uuid]), is_success: false);

            // Redirection vers la page précédente avec un message d'erreur de non-trouvabilité
            return back()->with('error', __("{$this->langPath}.destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la suppression de la traduction
            $this->logActivity(type: 'warning', action: 'backend.product-brand-translations.destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            // Redirection vers la page précédente avec un message d'erreur métier explicite
            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la suppression de la traduction
            $this->logActivity(type: 'error', action: 'backend.product-brand-translations.destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.error", ['uuid' => $uuid]), is_success: false);

            // Redirection vers la page précédente avec un message d'erreur générique
            return back()->with('error', __("{$this->langPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_product_brand_translations_trashed(string $brand_uuid): View
    {
        try {

            // $brand récupère la marque de produit, y compris celles qui sont supprimées (soft deleted)
            $brand = ProductBrand::withTrashed()->where('uuid', $brand_uuid)->firstOrFail();

            // $trashedTranslations récupère toutes les traductions supprimées (soft deleted) associées à la marque
            $trashedTranslations = ProductBrandTranslation::onlyTrashed()->where('brand_id', $brand->id)->get();

            // Log de l'accès à la liste des traductions supprimées pour la marque spécifiée
            $this->logActivity(type: 'info', action: 'backend.product-brand-translations.trashed', context: ['count' => $trashedTranslations->count(), 'brand_uuid' => $brand_uuid], message: null, activity: __("{$this->langPath}.trashed.accessed", ['name' => $brand->active_translation?->name ?? $brand_uuid]), is_success: true, loggable: $brand);

            // Retourne la vue avec les traductions supprimées et la marque associée
            return view('v1.backend.pages.products.brands.translations.trashed', compact('trashedTranslations', 'brand'));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur de non-trouvabilité de la marque pour laquelle on tente d'accéder aux traductions supprimées
            $this->logActivity(type: 'warning', action: 'backend.product-brand-translations.trashed', context: ['brand_uuid' => $brand_uuid], message: $e->getMessage(), activity: __("{$this->langPath}.trashed.not_found", ['uuid' => $brand_uuid]), is_success: false);

            // Redirection vers la page précédente avec un message d'erreur de non-trouvabilité
            return back()->with('error', __("{$this->langPath}.trashed.not_found", ['uuid' => $brand_uuid]));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de l'accès aux traductions supprimées pour la marque spécifiée
            $this->logActivity(type: 'error', action: 'backend.product-brand-translations.trashed', context: ['brand_uuid' => $brand_uuid], message: $e->getMessage(), activity: __("{$this->langPath}.trashed.error", ['uuid' => $brand_uuid]), is_success: false);

            // Redirection vers la page précédente avec un message d'erreur générique
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
            $this->productBrandTranslationService->backend_product_brand_translations_restore($translation);

            // Log de l'activité de restauration
            $this->logActivity(type: 'info', action: 'backend.product-brand-translations.restore', context: ['translation_uuid' => $translation->uuid], message: null, activity: __("{$this->langPath}.restore.success", ['uuid' => $translation->uuid]), is_success: true, loggable: $translation);

            // Redirection vers la page précédente avec un message de succès
            return back()->with('success', __("{$this->langPath}.restore.flash_success"));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur de non-trouvabilité de la traduction à restaurer
            $this->logActivity(type: 'warning', action: 'backend.product-brand-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            // Redirection vers la page précédente avec un message d'erreur de non-trouvabilité
            return back()->with('error', __("{$this->langPath}.restore.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la restauration de la traduction
            $this->logActivity(type: 'warning', action: 'backend.product-brand-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            // Redirection vers la page précédente avec un message d'erreur métier explicite
            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la restauration de la traduction
            $this->logActivity(type: 'error', action: 'backend.product-brand-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            // Redirection vers la page précédente avec un message d'erreur générique
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
            $this->productBrandTranslationService->backend_product_brand_translations_force_destroy($translation);

            // Log de l'activité
            $this->logActivity(type: 'info', action: 'backend.product-brand-translations.force_destroy', context: ['translation_uuid' => $uuid], message: null, activity: __("{$this->langPath}.force_destroy.success", ['uuid' => $uuid]), is_success: true, loggable: $translation);

            // Redirection vers la page précédente avec un message de succès
            return back()->with('success', __("{$this->langPath}.force_destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur de non-trouvabilité de la traduction à supprimer définitivement
            $this->logActivity(type: 'warning', action: 'backend.product-brand-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            // Redirection vers la page précédente avec un message d'erreur de non-trouvabilité
            return back()->with('error', __("{$this->langPath}.force_destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la suppression définitive de la traduction
            $this->logActivity(type: 'warning', action: 'backend.product-brand-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            //  Redirection vers la page précédente avec un message d'erreur métier explicite
            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la suppression définitive de la traduction
            $this->logActivity(type: 'error', action: 'backend.product-brand-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            // Redirection vers la page précédente avec un message d'erreur générique
            return back()->with('error', __("{$this->langPath}.force_destroy.flash_error"));
        }
    }
}
