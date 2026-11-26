<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ProductAttributeValue;
use App\Models\ProductAttributeValueTranslation;
use App\Services\V1\Controllers\ProductAttributeValueTranslationService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

use function __;

class ProductAttributeValueTranslationController extends Controller
{
    use LogActivity;

    // Chemin de base centralisé pour les traductions
    protected string $backendLangPath = 'v1/backend/views/pages/products/attribute-values/messages/attribute-values-messages-controller.productAttributeValues.translations';

    // Injection du service de gestion des traductions de valeurs d'attributs de produit
    public function __construct(
        protected ProductAttributeValueTranslationService $productAttributeValueTranslationService
    ) {}

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_attribute_value_translations_destroy(ProductAttributeValueTranslation $productAttributeValueTranslation): RedirectResponse
    {
        try {

            // Traduction résolue par le binding implicite sur uuid (route {productAttributeValueTranslation})
            $translation = $productAttributeValueTranslation;

            // Supprime la traduction de façon réversible via le service
            $this->productAttributeValueTranslationService->backend_product_attribute_value_translations_destroy($translation);

            $this->logActivity(type: 'warning', action: 'backend.product-attribute-value-translations.destroy', context: ['translation_uuid' => $translation->uuid], message: null, activity: __("{$this->backendLangPath}.destroy.success", ['uuid' => $translation->uuid]), is_success: true, loggable: $translation);

            return back()->with('success', __("{$this->backendLangPath}.destroy.flash_success"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-attribute-value-translations.destroy', context: ['translation_uuid' => $translation->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning_meta", ['uuid' => $translation->uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-attribute-value-translations.destroy', context: ['translation_uuid' => $translation->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.error", ['uuid' => $translation->uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_product_attribute_value_translations_trashed(ProductAttributeValue $productAttributeValue): View|RedirectResponse
    {
        try {

            // Valeur d'attribut résolue par le binding implicite sur uuid (route {productAttributeValue} avec ->withTrashed())
            $value = $productAttributeValue;

            // Récupère les traductions supprimées de cette valeur d'attribut
            $trashedTranslations = ProductAttributeValueTranslation::onlyTrashed()->where('attribute_value_id', $value->id)->get();

            // Journalise l'accès à la corbeille des traductions de cette valeur d'attribut
            $this->logActivity(type: 'info', action: 'backend.product-attribute-value-translations.trashed', context: ['count' => $trashedTranslations->count(), 'attribute_value_uuid' => $value->uuid], message: null, activity: __("{$this->backendLangPath}.trashed.accessed", ['name' => $value->active_translation?->label ?? $value->uuid]), is_success: true, loggable: $value);

            return view('v1.backend.pages.products.attribute-values.translations.trashed', compact('trashedTranslations', 'value'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-attribute-value-translations.trashed', context: ['attribute_value_uuid' => $value->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.trashed.error", ['uuid' => $value->uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.trashed.error", ['uuid' => $attribute_value_uuid]));
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_product_attribute_value_translations_restore(string $uuid): RedirectResponse
    {
        try {

            // Récupère la traduction supprimée
            $translation = ProductAttributeValueTranslation::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Restaure la traduction via le service
            $this->productAttributeValueTranslationService->backend_product_attribute_value_translations_restore($translation);

            $this->logActivity(type: 'info', action: 'backend.product-attribute-value-translations.restore', context: ['translation_uuid' => $translation->uuid], message: null, activity: __("{$this->backendLangPath}.restore.success", ['uuid' => $translation->uuid]), is_success: true, loggable: $translation);

            return back()->with('success', __("{$this->backendLangPath}.restore.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-attribute-value-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.restore.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-attribute-value-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-attribute-value-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_attribute_value_translations_force_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupère la traduction supprimée
            $translation = ProductAttributeValueTranslation::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Supprime définitivement la traduction via le service
            $this->productAttributeValueTranslationService->backend_product_attribute_value_translations_force_destroy($translation);

            // Journalise la suppression définitive de la traduction
            $this->logActivity(type: 'info', action: 'backend.product-attribute-value-translations.force_destroy', context: ['translation_uuid' => $uuid], message: null, activity: __("{$this->backendLangPath}.force_destroy.success", ['uuid' => $uuid]), is_success: true, loggable: $translation);

            return back()->with('success', __("{$this->backendLangPath}.force_destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-attribute-value-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-attribute-value-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-attribute-value-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_error"));
        }
    }
}
