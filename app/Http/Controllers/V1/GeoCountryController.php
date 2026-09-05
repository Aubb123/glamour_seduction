<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Geo\Countries\UpdateGeoCountryRequest;
use App\Models\GeoContinent;
use App\Models\GeoCountry;
use App\Models\GeoCountryTranslation;
use App\Models\User;
use App\Services\V1\Controllers\GeoCountryService;
use App\Traits\LogActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GeoCountryController extends Controller
{
    use LogActivity; // Trait pour le logging d'activité

    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/geos/countries/messages/countries-messages-controller.countries';

    // Injection de dépendance du service de gestion des pays
    public function __construct(protected GeoCountryService $geoCountryService) {}

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_geocountries_index(): View
    {
        try {

            // ✅ Récupère tous les pays avec leurs traductions actives et leur continent, triés par ordre d'affichage décroissant
            $geocountries = GeoCountry::withCount(['users', 'audits', 'activityLogs'])->withActiveTranslation()->with(['continent'])->orderBy('sort_order', 'desc')->get();

            // Log de l'accès à la liste des pays
            $this->logActivity(type: 'info', action: 'backend.geocountries.index', context: ['count' => $geocountries->count()], message: null, activity: __("{$this->langPath}.index.success"), is_success: true);

            // Affiche la vue avec les pays récupérés
            return view('v1.backend.pages.geos.countries.index', compact('geocountries'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur avec le message d'exception
            $this->logActivity(type: 'error', action: 'backend.geocountries.index', context: [], message: $e->getMessage(), activity: __("{$this->langPath}.index.error"), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération des pays
        }
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function backend_geocountries_show(GeoCountry $geoCountry, string $slug): View|RedirectResponse
    {
        try {

            $langs = getActiveLangs()['langs']; // Récupère les langues actives pour afficher les onglets de traduction

            // Pays résolu par le binding implicite sur uuid (404 si introuvable).
            // Charge tout en une seule requête : traductions actives (toutes langues actives,
            // car l'onglet "Traductions" de la vue affiche chaque langue), continent et compteurs.
            $country = GeoCountry::query()
                ->withCount(['users', 'audits', 'activityLogs'])
                ->with(['continent'])
                ->with(['translations' => fn ($query) => $query->whereNull('deleted_at')->with('lang')])
                ->findOrFail($geoCountry->id);

            $expectedSlug = $country->active_translation?->slug ?? $country->code;

            if ($expectedSlug !== $slug) {
                $this->logActivity(type: 'warning', action: 'backend.geocountries.show', context: ['country_uuid' => $country->uuid, 'country_slug' => $slug, 'expected_slug' => $expectedSlug], message: null, activity: __("{$this->langPath}.show.slug_mismatch"), is_success: false, loggable: $country);

                return redirect()->route('backend.geocountries.index')->with('error', __("{$this->langPath}.show.slug_mismatch")); // Redirection vers la liste des pays avec un message d'erreur
            }

            $geoCountryTranslations = GeoCountryTranslation::onlyTrashed()->where('country_id', $country->id)->get(); // Récupère toutes les traductions supprimées (soft deleted) de ce pays

            $countryName = $country->active_translation?->name ?? __("{$this->langPath}.show.no_name"); // Détermine le nom affiché dans le log

            // Log de la consultation du détail d'un pays
            $this->logActivity(type: 'info', action: 'backend.geocountries.show', context: ['country_uuid' => $country->uuid, 'country_code' => $country->code], message: null, activity: __("{$this->langPath}.show.success", ['name' => $countryName]), is_success: true, loggable: $country);

            // Retourne la vue de détail du pays avec toutes les données nécessaires
            return view('v1.backend.pages.geos.countries.show', compact('country', 'langs', 'geoCountryTranslations'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la consultation du détail d'un pays
            $this->logActivity(type: 'error', action: 'backend.geocountries.show', context: ['country_uuid' => $geoCountry->uuid, 'country_slug' => $slug], message: $e->getMessage(), activity: __("{$this->langPath}.show.error", ['uuid' => $geoCountry->uuid]), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération du pays
        }
    }

    // ─────────────────────────────────────────────
    // USERS
    // ─────────────────────────────────────────────
    public function backend_geocountries_users(GeoCountry $geoCountry, string $slug): View|RedirectResponse
    {
        try {

            // Pays résolu par le binding implicite sur uuid (404 automatique si introuvable)
            $country = GeoCountry::query()
                ->withActiveTranslation()
                ->withCount(['users', 'audits', 'activityLogs'])
                ->findOrFail($geoCountry->id);

            $expectedSlug = $country->active_translation?->slug ?? $country->code;

            if ($expectedSlug !== $slug) {
                $this->logActivity(type: 'warning', action: 'backend.geocountries.users', context: ['country_uuid' => $country->uuid, 'country_slug' => $slug, 'expected_slug' => $expectedSlug], message: null, activity: __("{$this->langPath}.users.slug_mismatch"), is_success: false, loggable: $country);

                return redirect()->route('backend.geocountries.index')->with('error', __("{$this->langPath}.users.slug_mismatch"));
            }

            // Récupère les utilisateurs de ce pays
            $users = User::with(['cloudFiles'])->withCount(['activityLogs', 'galleries', 'subscribes', 'audits', 'blogPosts', 'blogPostComments', 'blogPostCommentReplies'])->where('country_id', $country->id)->orderBy('created_at', 'desc')->get();

            $countryName = $country->activeTranslation?->name ?? $country->code;

            // Log de la consultation des utilisateurs d'un pays
            $this->logActivity(type: 'info', action: 'backend.geocountries.users', context: ['country_uuid' => $country->uuid, 'country_code' => $country->code], message: null, activity: __("{$this->langPath}.users.success", ['name' => $countryName]), is_success: true, loggable: $country);

            // Retourne la vue des utilisateurs du pays
            return view('v1.backend.pages.geos.countries.others.users', compact('country', 'users'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la consultation des utilisateurs d'un pays
            $this->logActivity(type: 'error', action: 'backend.geocountries.users', context: ['country_uuid' => $geoCountry->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.users.error", ['uuid' => $geoCountry->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // AUDITS
    // ─────────────────────────────────────────────
    public function backend_geocountries_audits(GeoCountry $geoCountry, string $slug): View|RedirectResponse
    {
        try {

            // Pays résolu par le binding implicite sur uuid (404 automatique si introuvable)
            $country = GeoCountry::query()
                ->withActiveTranslation()
                ->withCount(['users', 'audits', 'activityLogs'])
                ->findOrFail($geoCountry->id);

            $expectedSlug = $country->active_translation?->slug ?? $country->code;

            if ($expectedSlug !== $slug) {
                $this->logActivity(type: 'warning', action: 'backend.geocountries.audits', context: ['country_uuid' => $country->uuid, 'country_slug' => $slug, 'expected_slug' => $expectedSlug], message: null, activity: __("{$this->langPath}.audits.slug_mismatch"), is_success: false, loggable: $country);

                return redirect()->route('backend.geocountries.index')->with('error', __("{$this->langPath}.audits.slug_mismatch"));
            }

            $countryName = $country->active_translation?->name ?? $country->code;

            $audits = $country->audits()->with(['user', 'auditable'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.geocountries.audits', context: ['country_uuid' => $country->uuid, 'country_code' => $country->code], message: null, activity: __("{$this->langPath}.audits.success", ['name' => $countryName]), is_success: true, loggable: $country);

            return view('v1.backend.pages.geos.countries.others.audits', compact('country', 'audits'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.geocountries.audits', context: ['country_uuid' => $geoCountry->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.audits.error", ['uuid' => $geoCountry->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVITY LOGS
    // ─────────────────────────────────────────────
    public function backend_geocountries_activity_logs(GeoCountry $geoCountry, string $slug): View|RedirectResponse
    {
        try {

            // Pays résolu par le binding implicite sur uuid (404 automatique si introuvable)
            $country = GeoCountry::query()
                ->withActiveTranslation()
                ->withCount(['users', 'audits', 'activityLogs'])
                ->findOrFail($geoCountry->id);

            $expectedSlug = $country->active_translation?->slug ?? $country->code;

            if ($expectedSlug !== $slug) {
                $this->logActivity(type: 'warning', action: 'backend.geocountries.activity-logs', context: ['country_uuid' => $country->uuid, 'country_slug' => $slug, 'expected_slug' => $expectedSlug], message: null, activity: __("{$this->langPath}.activity-logs.slug_mismatch"), is_success: false, loggable: $country);

                return redirect()->route('backend.geocountries.index')->with('error', __("{$this->langPath}.activity-logs.slug_mismatch"));
            }

            $countryName = $country->active_translation?->name ?? $country->code;

            $logs = $country->activityLogs()->with(['user'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.geocountries.activity-logs', context: ['country_uuid' => $country->uuid, 'country_code' => $country->code], message: null, activity: __("{$this->langPath}.activity-logs.success", ['name' => $countryName]), is_success: true, loggable: $country);

            return view('v1.backend.pages.geos.countries.others.activity-logs', compact('country', 'logs'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.geocountries.activity-logs', context: ['country_uuid' => $geoCountry->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.activity-logs.error", ['uuid' => $geoCountry->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────
    public function backend_geocountries_edit(GeoCountry $geoCountry): View
    {
        try {

            $langs = getActiveLangs()['langs']; // Récupère les langues actives pour les passer à la vue
            $continents = GeoContinent::withActiveTranslation()->orderBy('sort_order', 'asc')->get(); // Récupère tous les continents actifs pour le select du formulaire

            // Récupère tous les pays (pour le select du pays souverain)
            $geocountries = GeoCountry::withActiveTranslation()->orderBy('sort_order', 'asc')->get();

            // Pays résolu par le binding implicite sur uuid (404 automatique si introuvable)
            $country = $geoCountry;

            // Log de l'accès à la page de modification d'un pays
            $this->logActivity(type: 'info', action: 'backend.geocountries.edit', context: ['country_uuid' => $country->uuid, 'country_code' => $country->code], message: null, activity: __("{$this->langPath}.edit.success", ['name' => $country->active_translation?->name ?? $country->code]), is_success: true, loggable: $country);

            // Retourne la vue d'édition du pays avec les données nécessaires
            return view('v1.backend.pages.geos.countries.edit', compact('country', 'langs', 'continents', 'geocountries'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de l'accès à la page de modification d'un pays
            $this->logActivity(type: 'error', action: 'backend.geocountries.edit', context: ['country_uuid' => $geoCountry->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.edit.error", ['uuid' => $geoCountry->uuid]), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération du pays
        }
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_geocountries_update(UpdateGeoCountryRequest $request, GeoCountry $geoCountry): RedirectResponse
    {
        try {

            // Pays résolu par le binding implicite sur uuid (404 automatique si introuvable)
            $country = $geoCountry;

            // Mise à jour du pays via le service, en passant les données validées du formulaire
            $this->geoCountryService->update($country, $request->validated());

            // Log de la mise à jour du pays avec le uuid et les changements effectués
            $this->logActivity(type: 'info', action: 'backend.geocountries.update', context: ['country_uuid' => $country->uuid, 'country_code' => $country->code, 'changes' => $country->getChanges()], message: null, activity: __("{$this->langPath}.update.success", ['name' => $country->active_translation?->name ?? $country->code]), is_success: true, loggable: $country);

            // Redirection vers la page de détail du pays avec un message de succès.
            // NB : après syncTranslations, la relation "translations" n'étant pas chargée sur
            // $country, l'accès à active_translation déclenche une requête fraîche → le slug
            // est toujours à jour même s'il vient d'être régénéré par HasSlug.
            return redirect()->route('backend.geocountries.show', ['geoCountry' => $country->uuid, 'slug' => ($country->active_translation?->slug) ?? $country->code])->with('success', __("{$this->langPath}.update.flash_success", ['name' => $country->active_translation?->name ?? $country->code]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la mise à jour du pays
            $this->logActivity(type: 'warning', action: 'backend.geocountries.update', context: ['country_uuid' => $geoCountry->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.update.warning_meta", ['uuid' => $geoCountry->uuid]), is_success: false);

            // Redirection avec le message métier explicite
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la mise à jour du pays
            $this->logActivity(type: 'error', action: 'backend.geocountries.update', context: ['country_uuid' => $geoCountry->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.update.error", ['uuid' => $geoCountry->uuid]), is_success: false);

            // Redirection avec un message d'erreur en cas de problème lors de la mise à jour, en conservant les données du formulaire
            return back()->with('error', __("{$this->langPath}.update.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_geocountries_activate(GeoCountry $geoCountry): RedirectResponse
    {
        try {

            // Pays résolu par le binding implicite sur uuid (404 automatique si introuvable)
            $country = $geoCountry;

            // Activation du pays via le service
            $this->geoCountryService->activate($country);

            // Log de l'activation du pays
            $this->logActivity(type: 'info', action: 'backend.geocountries.activate', context: ['country_uuid' => $country->uuid, 'country_code' => $country->code], message: null, activity: __("{$this->langPath}.activate.success", ['name' => $country->active_translation?->name ?? $country->code]), is_success: true, loggable: $country);

            // Redirection avec un message de succès
            return back()->with('success', __("{$this->langPath}.activate.flash_success", ['name' => $country->active_translation?->name ?? $country->code]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de l'activation du pays
            $this->logActivity(type: 'warning', action: 'backend.geocountries.activate', context: ['country_uuid' => $geoCountry->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.activate.warning_meta", ['uuid' => $geoCountry->uuid]), is_success: false);

            // Redirection avec le message métier explicite
            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de l'activation du pays
            $this->logActivity(type: 'error', action: 'backend.geocountries.activate', context: ['country_uuid' => $geoCountry->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.activate.error", ['uuid' => $geoCountry->uuid]), is_success: false);

            // Redirection avec un message d'erreur en cas de problème lors de l'activation du pays
            return back()->with('error', __("{$this->langPath}.activate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_geocountries_deactivate(GeoCountry $geoCountry): RedirectResponse
    {
        try {

            // Pays résolu par le binding implicite sur uuid (404 automatique si introuvable)
            $country = $geoCountry;

            // Désactivation du pays via le service
            $this->geoCountryService->deactivate($country);

            // Log de la désactivation du pays
            $this->logActivity(type: 'info', action: 'backend.geocountries.deactivate', context: ['country_uuid' => $country->uuid, 'country_code' => $country->code], message: null, activity: __("{$this->langPath}.deactivate.success", ['name' => $country->active_translation?->name ?? $country->code]), is_success: true, loggable: $country);

            // Redirection avec un message de succès
            return back()->with('success', __("{$this->langPath}.deactivate.flash_success", ['name' => $country->active_translation?->name ?? $country->code]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la désactivation du pays
            $this->logActivity(type: 'warning', action: 'backend.geocountries.deactivate', context: ['country_uuid' => $geoCountry->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.deactivate.warning_meta", ['uuid' => $geoCountry->uuid]), is_success: false);

            // Redirection avec le message métier explicite
            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la désactivation du pays
            $this->logActivity(type: 'error', action: 'backend.geocountries.deactivate', context: ['country_uuid' => $geoCountry->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.deactivate.error", ['uuid' => $geoCountry->uuid]), is_success: false);

            // Redirection avec un message d'erreur en cas de problème lors de la désactivation du pays
            return back()->with('error', __("{$this->langPath}.deactivate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_geocountries_trashed(): View
    {
        try {

            // Récupère tous les pays supprimés (soft deleted) avec leurs traductions, triés par date de suppression décroissante
            $geocountries = GeoCountry::onlyTrashed()->withTrashedActiveTranslation()->with(['continent'])->withCount(['users', 'audits', 'activityLogs'])->orderBy('deleted_at', 'desc')->get();

            // Log de la consultation de la corbeille des pays
            $this->logActivity(type: 'info', action: 'backend.geocountries.trashed', context: ['count' => $geocountries->count()], message: null, activity: __("{$this->langPath}.trashed.success"), is_success: true);

            return view('v1.backend.pages.geos.countries.trashed', compact('geocountries'));

        } catch (\Exception $e) {

            // Log de l'erreur lors de la consultation de la corbeille des pays
            $this->logActivity(type: 'error', action: 'backend.geocountries.trashed', context: ['exception' => get_class($e)], message: $e->getMessage(), activity: __("{$this->langPath}.trashed.error"), is_success: false);

            abort(500);
        }
    }
}
