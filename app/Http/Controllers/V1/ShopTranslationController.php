<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\ShopTranslation;
use App\Services\V1\Controllers\ShopTranslationService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

use function __;

class ShopTranslationController extends Controller
{
    use LogActivity;

    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/shops/shops/messages/shops-messages-controller.shops.translations';

    public function __construct(
        protected ShopTranslationService $shopTranslationService
    ) {}

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_shop_translations_destroy(string $uuid): RedirectResponse
    {
        try {

            // Résolution manuelle de la traduction par UUID
            $translation = ShopTranslation::where('uuid', $uuid)->firstOrFail();

            // Suppression via le service (soft delete)
            $this->shopTranslationService->backend_shop_translations_destroy($translation);

            $this->logActivity(type: 'info', action: 'backend.shop-translations.destroy', context: ['translation_uuid' => $translation->uuid], message: null, activity: __("{$this->backendLangPath}.destroy.success", ['uuid' => $translation->uuid]), is_success: true, loggable: $translation);

            return back()->with('success', __("{$this->backendLangPath}.destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.shop-translations.destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.shop-translations.destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.shop-translations.destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_shop_translations_trashed(string $shop_uuid): View
    {
        try {

            // Boutique résolue manuellement par UUID (même soft deleted)
            $shop = Shop::withTrashed()->where('uuid', $shop_uuid)->firstOrFail();

            // Traductions supprimées (soft deleted) de cette boutique
            $trashedTranslations = ShopTranslation::onlyTrashed()->where('shop_id', $shop->id)->get();

            $this->logActivity(type: 'info', action: 'backend.shop-translations.trashed', context: ['count' => $trashedTranslations->count(), 'shop_uuid' => $shop_uuid], message: null, activity: __("{$this->backendLangPath}.trashed.accessed", ['name' => $shop->active_translation?->name ?? $shop_uuid]), is_success: true, loggable: $shop);

            return view('v1.backend.pages.shops.shops.translations.trashed', compact('trashedTranslations', 'shop'));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.shop-translations.trashed', context: ['shop_uuid' => $shop_uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.trashed.not_found", ['uuid' => $shop_uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.trashed.not_found", ['uuid' => $shop_uuid]));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.shop-translations.trashed', context: ['shop_uuid' => $shop_uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.trashed.error", ['uuid' => $shop_uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.trashed.error", ['uuid' => $shop_uuid]));
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_shop_translations_restore(string $uuid): RedirectResponse
    {
        try {

            // Traduction supprimée (soft deleted) par UUID
            $translation = ShopTranslation::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            $this->shopTranslationService->backend_shop_translations_restore($translation);

            $this->logActivity(type: 'info', action: 'backend.shop-translations.restore', context: ['translation_uuid' => $translation->uuid], message: null, activity: __("{$this->backendLangPath}.restore.success", ['uuid' => $translation->uuid]), is_success: true, loggable: $translation);

            return back()->with('success', __("{$this->backendLangPath}.restore.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.shop-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.restore.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.shop-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.shop-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_shop_translations_force_destroy(string $uuid): RedirectResponse
    {
        try {

            // Traduction supprimée (soft deleted) par UUID
            $translation = ShopTranslation::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            $this->shopTranslationService->backend_shop_translations_force_destroy($translation);

            $this->logActivity(type: 'info', action: 'backend.shop-translations.force_destroy', context: ['translation_uuid' => $uuid], message: null, activity: __("{$this->backendLangPath}.force_destroy.success", ['uuid' => $uuid]), is_success: true, loggable: $translation);

            return back()->with('success', __("{$this->backendLangPath}.force_destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.shop-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.shop-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.shop-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_error"));
        }
    }
}
