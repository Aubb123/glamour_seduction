<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeTranslation;
use App\Services\V1\Controllers\ProductAttributeTranslationService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

use function __;

class ProductAttributeTranslationController extends Controller
{
    use LogActivity;

    // Chemin de base centralisé pour les traductions
    protected string $backendLangPath = 'v1/backend/views/pages/products/attributes/messages/attributes-messages-controller.productAttributes.translations';

    // Injection du service de gestion des traductions d'attributs de produit
    public function __construct(
        protected ProductAttributeTranslationService $productAttributeTranslationService
    ) {}

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_attribute_translations_destroy(string $uuid): RedirectResponse
    {
        try {

            $translation = ProductAttributeTranslation::where('uuid', $uuid)->firstOrFail();

            // Supprime la traduction de façon réversible via le service
            $this->productAttributeTranslationService->backend_product_attribute_translations_destroy($translation);

            $this->logActivity(type: 'warning', action: 'backend.product-attribute-translations.destroy', context: ['translation_uuid' => $translation->uuid], message: null, activity: __("{$this->backendLangPath}.destroy.success", ['uuid' => $translation->uuid]), is_success: true, loggable: $translation);

            return back()->with('success', __("{$this->backendLangPath}.destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-attribute-translations.destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-attribute-translations.destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-attribute-translations.destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_product_attribute_translations_trashed(string $attribute_uuid): View
    {
        try {

            // Récupère l'attribut de produit correspondant à l'UUID de la route
            $attribute = ProductAttribute::withTrashed()->where('uuid', $attribute_uuid)->firstOrFail();

            // Récupère les traductions supprimées de cet attribut de produit
            $trashedTranslations = ProductAttributeTranslation::onlyTrashed()->where('attribute_id', $attribute->id)->get();

            // Journalise l'accès à la corbeille des traductions de cet attribut de produit
            $this->logActivity(type: 'info', action: 'backend.product-attribute-translations.trashed', context: ['count' => $trashedTranslations->count(), 'attribute_uuid' => $attribute_uuid], message: null, activity: __("{$this->backendLangPath}.trashed.accessed", ['name' => $attribute->active_translation?->name ?? $attribute_uuid]), is_success: true, loggable: $attribute);

            return view('v1.backend.pages.products.attributes.translations.trashed', compact('trashedTranslations', 'attribute'));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-attribute-translations.trashed', context: ['attribute_uuid' => $attribute_uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.trashed.not_found", ['uuid' => $attribute_uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.trashed.not_found", ['uuid' => $attribute_uuid]));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-attribute-translations.trashed', context: ['attribute_uuid' => $attribute_uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.trashed.error", ['uuid' => $attribute_uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.trashed.error", ['uuid' => $attribute_uuid]));
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_product_attribute_translations_restore(string $uuid): RedirectResponse
    {
        try {

            // Récupère la traduction supprimée
            $translation = ProductAttributeTranslation::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Restaure la traduction via le service
            $this->productAttributeTranslationService->backend_product_attribute_translations_restore($translation);

            $this->logActivity(type: 'info', action: 'backend.product-attribute-translations.restore', context: ['translation_uuid' => $translation->uuid], message: null, activity: __("{$this->backendLangPath}.restore.success", ['uuid' => $translation->uuid]), is_success: true, loggable: $translation);

            return back()->with('success', __("{$this->backendLangPath}.restore.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-attribute-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.restore.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-attribute-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-attribute-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_attribute_translations_force_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupère la traduction supprimée
            $translation = ProductAttributeTranslation::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Supprime définitivement la traduction via le service
            $this->productAttributeTranslationService->backend_product_attribute_translations_force_destroy($translation);

            // Journalise la suppression définitive de la traduction
            $this->logActivity(type: 'info', action: 'backend.product-attribute-translations.force_destroy', context: ['translation_uuid' => $uuid], message: null, activity: __("{$this->backendLangPath}.force_destroy.success", ['uuid' => $uuid]), is_success: true, loggable: $translation);

            return back()->with('success', __("{$this->backendLangPath}.force_destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-attribute-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-attribute-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-attribute-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_error"));
        }
    }
}