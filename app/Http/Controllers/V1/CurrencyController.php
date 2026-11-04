<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Currencies\UpdateCurrencyRequest;
use App\Models\Currency;
use App\Models\CurrencyTranslation;
use App\Models\GeoCountry;
use App\Services\V1\Controllers\CurrencyService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CurrencyController extends Controller
{
    use LogActivity; // Trait pour le logging d'activité

    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/currencies/messages/currencies-messages-controller.currencies';

    // Injection de dépendance du service de gestion des devises
    public function __construct(protected CurrencyService $currencyService) {}

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_currencies_index(): View
    {
        try {

            // Récupère toutes les devises avec leurs traductions actives et leurs compteurs
            $currencies = Currency::withCount(['countries', 'audits', 'activityLogs'])->withActiveTranslation()->orderBy('sort_order', 'desc')->get();

            // Log de l'accès à la liste des devises
            $this->logActivity(type: 'info', action: 'backend.currencies.index', context: ['count' => $currencies->count()], message: null, activity: __("{$this->backendLangPath}.index.success"), is_success: true);

            // Affiche la vue avec les devises récupérées
            return view('v1.backend.pages.currencies.index', compact('currencies'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur avec le message d'exception
            $this->logActivity(type: 'error', action: 'backend.currencies.index', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.index.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function backend_currencies_show(Currency $currency, string $slug): View|RedirectResponse
    {
        try {

            $langs = getActiveLangs()['langs']; // Récupère les langues actives pour afficher les onglets de traduction

            // Devise résolue par le binding implicite sur uuid (404 si introuvable)
            $currency = Currency::query()
                ->withCount(['countries', 'audits', 'activityLogs'])
                ->with(['translations' => fn ($query) => $query->whereNull('deleted_at')->with('lang')])
                ->findOrFail($currency->id);

            $expectedSlug = $currency->active_translation?->slug ?? $currency->code;

            if ($expectedSlug !== $slug) {
                $this->logActivity(type: 'warning', action: 'backend.currencies.show', context: ['currency_uuid' => $currency->uuid, 'currency_slug' => $slug, 'expected_slug' => $expectedSlug], message: null, activity: __("{$this->backendLangPath}.show.slug_mismatch"), is_success: false, loggable: $currency);

                return redirect()->route('backend.currencies.index')->with('error', __("{$this->backendLangPath}.show.slug_mismatch"));
            }

            $currencyTranslations = CurrencyTranslation::onlyTrashed()->where('currency_id', $currency->id)->get(); // Récupère toutes les traductions supprimées (soft deleted) de cette devise

            $currencyName = $currency->active_translation?->name ?? __("{$this->backendLangPath}.show.no_name");

            // Log de la consultation du détail d'une devise
            $this->logActivity(type: 'info', action: 'backend.currencies.show', context: ['currency_uuid' => $currency->uuid, 'currency_code' => $currency->code], message: null, activity: __("{$this->backendLangPath}.show.success", ['name' => $currencyName]), is_success: true, loggable: $currency);

            // Retourne la vue de détail de la devise
            return view('v1.backend.pages.currencies.show', compact('currency', 'langs', 'currencyTranslations'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.currencies.show', context: ['currency_uuid' => $currency->uuid, 'currency_slug' => $slug], message: $e->getMessage(), activity: __("{$this->backendLangPath}.show.error", ['uuid' => $currency->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // COUNTRIES
    // ─────────────────────────────────────────────
    public function backend_currencies_countries(Currency $currency, string $slug): View|RedirectResponse
    {
        try {

            $currency = Currency::query()
                ->withCount(['countries', 'audits', 'activityLogs'])
                ->withActiveTranslation()
                ->findOrFail($currency->id);

            $expectedSlug = $currency->active_translation?->slug ?? $currency->code;

            if ($expectedSlug !== $slug) {
                $this->logActivity(type: 'warning', action: 'backend.currencies.countries', context: ['currency_uuid' => $currency->uuid, 'currency_slug' => $slug, 'expected_slug' => $expectedSlug], message: null, activity: __("{$this->backendLangPath}.countries.slug_mismatch"), is_success: false, loggable: $currency);

                return redirect()->route('backend.currencies.index')->with('error', __("{$this->backendLangPath}.countries.slug_mismatch"));
            }

            $geocountries = GeoCountry::withActiveTranslation()->with(['continent', 'currency'])->where('currency_id', $currency->id)->orderBy('sort_order', 'asc')->get();

            $currencyName = $currency->active_translation?->name ?? $currency->code;

            $this->logActivity(type: 'info', action: 'backend.currencies.countries', context: ['currency_uuid' => $currency->uuid, 'currency_code' => $currency->code], message: null, activity: __("{$this->backendLangPath}.countries.success", ['name' => $currencyName]), is_success: true, loggable: $currency);

            return view('v1.backend.pages.currencies.others.countries', compact('currency', 'geocountries'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.currencies.countries', context: ['currency_uuid' => $currency->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.countries.error", ['uuid' => $currency->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // AUDITS
    // ─────────────────────────────────────────────
    public function backend_currencies_audits(Currency $currency, string $slug): View|RedirectResponse
    {
        try {

            $currency = Currency::query()
                ->withCount(['countries', 'audits', 'activityLogs'])
                ->withActiveTranslation()
                ->findOrFail($currency->id);

            $expectedSlug = $currency->active_translation?->slug ?? $currency->code;

            if ($expectedSlug !== $slug) {
                $this->logActivity(type: 'warning', action: 'backend.currencies.audits', context: ['currency_uuid' => $currency->uuid, 'currency_slug' => $slug, 'expected_slug' => $expectedSlug], message: null, activity: __("{$this->backendLangPath}.audits.slug_mismatch"), is_success: false, loggable: $currency);

                return redirect()->route('backend.currencies.index')->with('error', __("{$this->backendLangPath}.audits.slug_mismatch"));
            }

            $currencyName = $currency->active_translation?->name ?? $currency->code;

            $audits = $currency->audits()->with(['user', 'auditable'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.currencies.audits', context: ['currency_uuid' => $currency->uuid, 'currency_code' => $currency->code], message: null, activity: __("{$this->backendLangPath}.audits.success", ['name' => $currencyName]), is_success: true, loggable: $currency);

            return view('v1.backend.pages.currencies.others.audits', compact('currency', 'audits'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.currencies.audits', context: ['currency_uuid' => $currency->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.audits.error", ['uuid' => $currency->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVITY LOGS
    // ─────────────────────────────────────────────
    public function backend_currencies_activity_logs(Currency $currency, string $slug): View|RedirectResponse
    {
        try {

            $currency = Currency::query()
                ->withCount(['countries', 'audits', 'activityLogs'])
                ->withActiveTranslation()
                ->findOrFail($currency->id);

            $expectedSlug = $currency->active_translation?->slug ?? $currency->code;

            if ($expectedSlug !== $slug) {
                $this->logActivity(type: 'warning', action: 'backend.currencies.activity-logs', context: ['currency_uuid' => $currency->uuid, 'currency_slug' => $slug, 'expected_slug' => $expectedSlug], message: null, activity: __("{$this->backendLangPath}.activity-logs.slug_mismatch"), is_success: false, loggable: $currency);

                return redirect()->route('backend.currencies.index')->with('error', __("{$this->backendLangPath}.activity-logs.slug_mismatch"));
            }

            $currencyName = $currency->active_translation?->name ?? $currency->code;

            $logs = $currency->activityLogs()->with(['user', 'loggable'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.currencies.activity-logs', context: ['currency_uuid' => $currency->uuid, 'currency_code' => $currency->code], message: null, activity: __("{$this->backendLangPath}.activity-logs.success", ['name' => $currencyName]), is_success: true, loggable: $currency);

            return view('v1.backend.pages.currencies.others.activity-logs', compact('currency', 'logs'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.currencies.activity-logs', context: ['currency_uuid' => $currency->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activity-logs.error", ['uuid' => $currency->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────
    public function backend_currencies_edit(Currency $currency): View
    {
        try {

            $langs = getActiveLangs()['langs']; // Récupère les langues actives pour les passer à la vue

            // Log de l'accès à la page de modification d'une devise
            $this->logActivity(type: 'info', action: 'backend.currencies.edit', context: ['currency_uuid' => $currency->uuid, 'currency_code' => $currency->code], message: null, activity: __("{$this->backendLangPath}.edit.success", ['name' => $currency->active_translation?->name ?? $currency->code]), is_success: true, loggable: $currency);

            // Retourne la vue d'édition de la devise avec les données nécessaires
            return view('v1.backend.pages.currencies.edit', compact('currency', 'langs'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.currencies.edit', context: ['currency_uuid' => $currency->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.edit.error", ['uuid' => $currency->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_currencies_update(UpdateCurrencyRequest $request, Currency $currency): RedirectResponse
    {
        try {

            // Mise à jour de la devise via le service, en passant les données validées du formulaire
            $this->currencyService->backend_currencies_update($currency, $request->validated());

            // Log dédié (Module 10) lorsque le taux de change a été modifié (traçabilité multi-devise)
            if ($currency->wasChanged('exchange_rate')) {
                $this->logActivity(type: 'info', action: 'backend.currencies.update', context: ['currency_uuid' => $currency->uuid, 'exchange_rate' => $currency->exchange_rate], message: null, activity: __("{$this->backendLangPath}.update.exchange_rate_changed", ['code' => $currency->code, 'rate' => $currency->exchange_rate]), is_success: true, loggable: $currency);
            }

            // Log de la mise à jour de la devise
            $this->logActivity(type: 'info', action: 'backend.currencies.update', context: ['currency_uuid' => $currency->uuid, 'currency_code' => $currency->code, 'changes' => $currency->getChanges()], message: null, activity: __("{$this->backendLangPath}.update.success", ['name' => $currency->active_translation?->name ?? $currency->code]), is_success: true, loggable: $currency);

            return redirect()->route('backend.currencies.show', ['currency' => $currency->uuid, 'slug' => ($currency->active_translation?->slug) ?? $currency->code])->with('success', __("{$this->backendLangPath}.update.flash_success", ['name' => $currency->active_translation?->name ?? $currency->code]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.currencies.update', context: ['currency_uuid' => $currency->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.warning_meta", ['uuid' => $currency->uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.currencies.update', context: ['currency_uuid' => $currency->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.error", ['uuid' => $currency->uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.update.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_currencies_activate(Currency $currency): RedirectResponse
    {
        try {

            // Activation de la devise via le service
            $this->currencyService->backend_currencies_activate($currency);

            // Log de l'activation de la devise
            $this->logActivity(type: 'info', action: 'backend.currencies.activate', context: ['currency_uuid' => $currency->uuid, 'currency_code' => $currency->code], message: null, activity: __("{$this->backendLangPath}.activate.success", ['name' => $currency->active_translation?->name ?? $currency->code]), is_success: true, loggable: $currency);

            return back()->with('success', __("{$this->backendLangPath}.activate.flash_success", ['name' => $currency->active_translation?->name ?? $currency->code]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.currencies.activate', context: ['currency_uuid' => $currency->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activate.warning_meta", ['uuid' => $currency->uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.currencies.activate', context: ['currency_uuid' => $currency->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activate.error", ['uuid' => $currency->uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.activate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_currencies_deactivate(Currency $currency): RedirectResponse
    {
        try {

            // Désactivation de la devise via le service
            $this->currencyService->backend_currencies_deactivate($currency);

            // Log de la désactivation de la devise
            $this->logActivity(type: 'info', action: 'backend.currencies.deactivate', context: ['currency_uuid' => $currency->uuid, 'currency_code' => $currency->code], message: null, activity: __("{$this->backendLangPath}.deactivate.success", ['name' => $currency->active_translation?->name ?? $currency->code]), is_success: true, loggable: $currency);

            return back()->with('success', __("{$this->backendLangPath}.deactivate.flash_success", ['name' => $currency->active_translation?->name ?? $currency->code]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.currencies.deactivate', context: ['currency_uuid' => $currency->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.deactivate.warning_meta", ['uuid' => $currency->uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.currencies.deactivate', context: ['currency_uuid' => $currency->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.deactivate.error", ['uuid' => $currency->uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.deactivate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY (soft delete)
    // ─────────────────────────────────────────────
    public function backend_currencies_destroy(Currency $currency): RedirectResponse
    {
        try {

            // Suppression (soft delete) via le service
            $this->currencyService->backend_currencies_destroy($currency);

            // Log de la suppression
            $this->logActivity(type: 'warning', action: 'backend.currencies.destroy', context: ['currency_uuid' => $currency->uuid], message: null, activity: __("{$this->backendLangPath}.destroy.success", ['name' => $currency->active_translation?->name ?? $currency->code]), is_success: true, loggable: $currency);

            return redirect()->route('backend.currencies.index')->with('success', __("{$this->backendLangPath}.destroy.flash_success"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.currencies.destroy', context: ['currency_uuid' => $currency->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning_meta", ['uuid' => $currency->uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.currencies.destroy', context: ['currency_uuid' => $currency->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.error", ['uuid' => $currency->uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_currencies_trashed(): View
    {
        try {

            // Récupère toutes les devises supprimées (soft deleted) avec leurs traductions, triées par date de suppression décroissante
            $currencies = Currency::onlyTrashed()->withTrashedActiveTranslation()->withCount(['countries', 'audits', 'activityLogs'])->orderBy('deleted_at', 'desc')->get();

            // Log de la consultation de la corbeille des devises
            $this->logActivity(type: 'info', action: 'backend.currencies.trashed', context: ['count' => $currencies->count()], message: null, activity: __("{$this->backendLangPath}.trashed.success"), is_success: true);

            return view('v1.backend.pages.currencies.trashed', compact('currencies'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.currencies.trashed', context: ['exception' => get_class($e)], message: $e->getMessage(), activity: __("{$this->backendLangPath}.trashed.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_currencies_restore(string $uuid): RedirectResponse
    {
        try {

            // Récupère la devise supprimée par son UUID ou échoue
            $currency = Currency::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Restauration via le service
            $this->currencyService->backend_currencies_restore($currency);

            // Log de la restauration
            $this->logActivity(type: 'info', action: 'backend.currencies.restore', context: ['currency_uuid' => $currency->uuid], message: null, activity: __("{$this->backendLangPath}.restore.success", ['name' => $currency->active_translation?->name ?? $currency->code]), is_success: true, loggable: $currency);

            return back()->with('success', __("{$this->backendLangPath}.restore.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.currencies.restore', context: ['currency_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.restore.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.currencies.restore', context: ['currency_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.currencies.restore', context: ['currency_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_currencies_force_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupère la devise supprimée par son UUID ou échoue
            $currency = Currency::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Suppression définitive via le service
            $this->currencyService->backend_currencies_force_destroy($currency);

            // Log de la suppression définitive
            $this->logActivity(type: 'info', action: 'backend.currencies.force_destroy', context: ['currency_uuid' => $currency->uuid], message: null, activity: __("{$this->backendLangPath}.force_destroy.success", ['name' => $currency->active_translation?->name ?? $currency->code]), is_success: true, loggable: $currency);

            return back()->with('success', __("{$this->backendLangPath}.force_destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.currencies.force_destroy', context: ['currency_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.currencies.force_destroy', context: ['currency_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.currencies.force_destroy', context: ['currency_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_error"));
        }
    }
}
