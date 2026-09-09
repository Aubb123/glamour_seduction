<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\CurrencyTranslation;
use App\Services\V1\Controllers\CurrencyTranslationService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CurrencyTranslationController extends Controller
{
    use LogActivity; // Trait pour le logging d'activité

    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/currencies/messages/currencies-messages-controller.currencies.translations';

    public function __construct(
        protected CurrencyTranslationService $currencyTranslationService
    ) {}

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_currencies_translations_destroy(CurrencyTranslation $currencyTranslation): RedirectResponse
    {
        try {

            // Suppression via le service (soft delete)
            $this->currencyTranslationService->destroy($currencyTranslation);

            // Log de la suppression
            $this->logActivity(type: 'warning', action: 'backend.currencies.translation.destroy', context: ['currency_translation_uuid' => $currencyTranslation->uuid], message: null, activity: __("{$this->langPath}.destroy.success", ['uuid' => $currencyTranslation->uuid]), is_success: true, loggable: $currencyTranslation);

            return back()->with('success', __("{$this->langPath}.destroy.flash_success"));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur de validation
            $this->logActivity(type: 'warning', action: 'backend.currencies.translation.destroy', context: ['currency_translation_uuid' => $currencyTranslation->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.warning_meta", ['uuid' => $currencyTranslation->uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.currencies.translation.destroy', context: ['currency_translation_uuid' => $currencyTranslation->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.error", ['uuid' => $currencyTranslation->uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_currencies_translations_trashed(Currency $currency): View|RedirectResponse
    {
        try {

            $currency = Currency::query()->withActiveTranslation()->findOrFail($currency->id);

            $currencyTranslations = CurrencyTranslation::onlyTrashed()->where('currency_id', $currency->id)->get();

            $this->logActivity(type: 'info', action: 'backend.currencies.translation.trashed', context: ['currency_uuid' => $currency->uuid], message: null, activity: __("{$this->langPath}.trashed.success", ['name' => $currency->active_translation?->name ?? $currency->code]), is_success: true, loggable: $currency);

            return view('v1.backend.pages.currencies.translations.trashed', compact('currency', 'currencyTranslations'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.currencies.translation.trashed', context: ['currency_uuid' => $currency->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.trashed.error", ['uuid' => $currency->uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.trashed.error"));
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_currencies_translations_restore(string $uuid): RedirectResponse
    {
        try {

            $currencyTranslation = CurrencyTranslation::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            $this->currencyTranslationService->restore($currencyTranslation);

            $this->logActivity(type: 'info', action: 'backend.currencies.translation.restore', context: ['currency_translation_uuid' => $currencyTranslation->uuid], message: null, activity: __("{$this->langPath}.restore.success", ['uuid' => $currencyTranslation->uuid]), is_success: true, loggable: $currencyTranslation);

            return back()->with('success', __("{$this->langPath}.restore.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.currencies.translation.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.restore.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.currencies.translation.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.currencies.translation.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_currencies_translations_force_destroy(string $uuid): RedirectResponse
    {
        try {

            $currencyTranslation = CurrencyTranslation::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            $this->currencyTranslationService->forceDestroy($currencyTranslation);

            $this->logActivity(type: 'info', action: 'backend.currencies.translation.force_destroy', context: ['currency_translation_uuid' => $currencyTranslation->uuid], message: null, activity: __("{$this->langPath}.force_destroy.success", ['uuid' => $currencyTranslation->uuid]), is_success: true, loggable: $currencyTranslation);

            return back()->with('success', __("{$this->langPath}.force_destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.currencies.translation.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.force_destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.currencies.translation.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.currencies.translation.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.force_destroy.flash_error"));
        }
    }
}
