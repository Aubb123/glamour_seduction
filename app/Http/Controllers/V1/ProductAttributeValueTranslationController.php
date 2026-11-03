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

    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/products/attribute-values/messages/attribute-values-messages-controller.productAttributeValues.translations';

    public function __construct(
        protected ProductAttributeValueTranslationService $productAttributeValueTranslationService
    ) {}

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_attribute_value_translations_destroy(string $uuid): RedirectResponse
    {
        try {

            $translation = ProductAttributeValueTranslation::where('uuid', $uuid)->firstOrFail();

            // Suppression via le service (soft delete)
            $this->productAttributeValueTranslationService->backend_product_attribute_value_translations_destroy($translation);

            $this->logActivity(type: 'warning', action: 'backend.product-attribute-value-translations.destroy', context: ['translation_uuid' => $translation->uuid], message: null, activity: __("{$this->langPath}.destroy.success", ['uuid' => $translation->uuid]), is_success: true, loggable: $translation);

            return back()->with('success', __("{$this->langPath}.destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-attribute-value-translations.destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-attribute-value-translations.destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-attribute-value-translations.destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_product_attribute_value_translations_trashed(string $tag_uuid): View
    {
        try {

            $value = ProductAttributeValue::withTrashed()->where('uuid', $tag_uuid)->firstOrFail();

            $trashedTranslations = ProductAttributeValueTranslation::onlyTrashed()->where('attribute_value_id', $value->id)->get();

            $this->logActivity(type: 'info', action: 'backend.product-attribute-value-translations.trashed', context: ['count' => $trashedTranslations->count(), 'tag_uuid' => $tag_uuid], message: null, activity: __("{$this->langPath}.trashed.accessed", ['name' => $value->active_translation?->label ?? $tag_uuid]), is_success: true, loggable: $value);

            return view('v1.backend.pages.products.attribute-values.translations.trashed', compact('trashedTranslations', 'value'));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-attribute-value-translations.trashed', context: ['tag_uuid' => $tag_uuid], message: $e->getMessage(), activity: __("{$this->langPath}.trashed.not_found", ['uuid' => $tag_uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.trashed.not_found", ['uuid' => $tag_uuid]));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-attribute-value-translations.trashed', context: ['tag_uuid' => $tag_uuid], message: $e->getMessage(), activity: __("{$this->langPath}.trashed.error", ['uuid' => $tag_uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.trashed.error", ['uuid' => $tag_uuid]));
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_product_attribute_value_translations_restore(string $uuid): RedirectResponse
    {
        try {

            // Récupération de la traduction supprimée
            $translation = ProductAttributeValueTranslation::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Restauration via le service
            $this->productAttributeValueTranslationService->backend_product_attribute_value_translations_restore($translation);

            $this->logActivity(type: 'info', action: 'backend.product-attribute-value-translations.restore', context: ['translation_uuid' => $translation->uuid], message: null, activity: __("{$this->langPath}.restore.success", ['uuid' => $translation->uuid]), is_success: true, loggable: $translation);

            return back()->with('success', __("{$this->langPath}.restore.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-attribute-value-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.restore.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-attribute-value-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-attribute-value-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_attribute_value_translations_force_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupération de la traduction supprimée
            $translation = ProductAttributeValueTranslation::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Suppression définitive via le service
            $this->productAttributeValueTranslationService->backend_product_attribute_value_translations_force_destroy($translation);

            // Log de l'activité
            $this->logActivity(type: 'info', action: 'backend.product-attribute-value-translations.force_destroy', context: ['translation_uuid' => $uuid], message: null, activity: __("{$this->langPath}.force_destroy.success", ['uuid' => $uuid]), is_success: true, loggable: $translation);

            return back()->with('success', __("{$this->langPath}.force_destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-attribute-value-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.force_destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-attribute-value-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-attribute-value-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.force_destroy.flash_error"));
        }
    }
}
