<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Geo\Continents\UpdateGeoContinentRequest;
use App\Models\GeoContinent;
use App\Models\GeoContinentTranslation;
use App\Models\GeoCountry;
use App\Services\V1\Controllers\GeoContinentService;
use App\Traits\LogActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GeoContinentController extends Controller
{
    use LogActivity; // Trait pour le logging d'activité

    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/geos/continents/messages/continent-messages-controller.geocontinents';

    public function __construct(
        protected GeoContinentService $geoContinentService
    ) {}

    // Fonction backend ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // ///////////////////  Fonction backend ///////////////////////////////////////////////////////////////////////////////////////////
    // //////////////////////////////////////// Fonction backend ///////////////////////////////////////////////////////////////////////
    // //////////////////////////////////////////////////////////// Fonction backend ///////////////////////////////////////////////////
    // //////////////////////////////////////////////////////////////////////////////// Fonction backend ///////////////////////////////

    // ═══════════════════════════════════════════════════════════════
    // BACKEND
    // ═══════════════════════════════════════════════════════════════

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_geocontinents_index(): View
    {
        try {

            // ✅ Récupère tous les continents avec leurs traductions et le nombre de pays associés.
            // Tri par ordre de tri croissant : plus le chiffre est petit, plus le continent apparaît en premier.
            $geocontinents = GeoContinent::withCount(['audits', 'activityLogs', 'countries'])->withActiveTranslation()->orderBy('sort_order', 'desc')->get();

            // Log de l'accès à la liste des continents
            $this->logActivity(type: 'info', action: 'backend.geocontinents.index', context: ['count' => $geocontinents->count()], message: null, activity: __("{$this->backendLangPath}.index.success"), is_success: true);

            // Affiche la vue avec les continents récupérés
            return view('v1.backend.pages.geos.continents.index', compact('geocontinents'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur avec le message d'exception
            $this->logActivity(type: 'error', action: 'backend.geocontinents.index', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.index.error"), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération des continents
        }
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function backend_geocontinents_show(GeoContinent $geoContinent, string $slug): View|RedirectResponse
    {
        try {

            $langs = getActiveLangs()['langs']; // Récupère les langues actives pour les passer à la vue

            // Continent résolu par le binding implicite sur uuid (404 automatique si introuvable)
            // + contrôle du slug SEO présent dans l'URL (redirection vers l'index en cas d'incohérence)
            $continent = $this->resolveContinent($geoContinent, $slug, 'show');
            if ($continent instanceof RedirectResponse) {
                return $continent;
            }

            // Récupère toutes les traductions supprimées (soft deleted) de ce continent
            $trashedTranslations = GeoContinentTranslation::onlyTrashed()->where('continent_id', $continent->id)->get();

            // Récupère les pays associés à ce continent avec leurs traductions actives
            $geocountries = GeoCountry::withActiveTranslation()->with(['continent'])->where('continent_id', $continent->id)->orderBy('sort_order', 'desc')->get();

            $continentName = isset($continent->active_translation) ? $continent->active_translation->name : __("{$this->backendLangPath}.show.no_name");

            // Log de la consultation du détail d'un continent
            $this->logActivity(type: 'info', action: 'backend.geocontinents.show', context: ['continent_uuid' => $continent->uuid, 'continent_code' => $continent->code], message: null, activity: __("{$this->backendLangPath}.show.success", ['name' => $continentName]), is_success: true, loggable: $continent);

            // Retourne la vue du continent avec les données nécessaires
            return view('v1.backend.pages.geos.continents.show', compact('continent', 'langs', 'trashedTranslations', 'geocountries'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la consultation du détail d'un continent
            $this->logActivity(type: 'error', action: 'backend.geocontinents.show', context: ['continent_uuid' => $geoContinent->uuid, 'continent_slug' => $slug], message: $e->getMessage(), activity: __("{$this->backendLangPath}.show.error", ['uuid' => $geoContinent->uuid]), is_success: false);

            abort(500); // Erreur serveur générique
        }
    }

    // ─────────────────────────────────────────────
    // COUNTRIES
    // ─────────────────────────────────────────────
    public function backend_geocontinents_countries(GeoContinent $geoContinent, string $slug): View|RedirectResponse
    {
        try {

            // Continent résolu par le binding implicite sur uuid (404 automatique si introuvable)
            // + contrôle du slug SEO présent dans l'URL (redirection vers l'index en cas d'incohérence)
            $continent = $this->resolveContinent($geoContinent, $slug, 'countries');
            if ($continent instanceof RedirectResponse) {
                return $continent;
            }

            // Récupère les pays associés à ce continent avec leurs traductions actives
            $geocountries = GeoCountry::withActiveTranslation()->with(['continent'])->where('continent_id', $continent->id)->orderBy('sort_order', 'desc')->get();

            $continentName = $continent->active_translation?->name ?? $continent->code;

            // Log de la consultation des pays d'un continent
            $this->logActivity(type: 'info', action: 'backend.geocontinents.countries', context: ['continent_uuid' => $continent->uuid, 'continent_code' => $continent->code], message: null, activity: __("{$this->backendLangPath}.countries.success", ['name' => $continentName]), is_success: true, loggable: $continent);

            // Retourne la vue des pays du continent
            return view('v1.backend.pages.geos.continents.others.countries', compact('continent', 'geocountries'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la consultation des pays d'un continent
            $this->logActivity(type: 'error', action: 'backend.geocontinents.countries', context: ['continent_uuid' => $geoContinent->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.countries.error", ['uuid' => $geoContinent->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // AUDITS
    // ─────────────────────────────────────────────
    public function backend_geocontinents_audits(GeoContinent $geoContinent, string $slug): View|RedirectResponse
    {
        try {

            // Continent résolu par le binding implicite sur uuid (404 automatique si introuvable)
            // + contrôle du slug SEO présent dans l'URL (redirection vers l'index en cas d'incohérence)
            $continent = $this->resolveContinent($geoContinent, $slug, 'audits');
            if ($continent instanceof RedirectResponse) {
                return $continent;
            }

            $continentName = $continent->active_translation?->name ?? $continent->code;

            $audits = $continent->audits()->with(['user', 'auditable'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.geocontinents.audits', context: ['continent_uuid' => $continent->uuid, 'continent_code' => $continent->code], message: null, activity: __("{$this->backendLangPath}.audits.success", ['name' => $continentName]), is_success: true, loggable: $continent);

            return view('v1.backend.pages.geos.continents.others.audits', compact('continent', 'audits'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.geocontinents.audits', context: ['continent_uuid' => $geoContinent->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.audits.error", ['uuid' => $geoContinent->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVITY LOGS
    // ─────────────────────────────────────────────
    public function backend_geocontinents_activity_logs(GeoContinent $geoContinent, string $slug): View|RedirectResponse
    {
        try {

            // Continent résolu par le binding implicite sur uuid (404 automatique si introuvable)
            // + contrôle du slug SEO présent dans l'URL (redirection vers l'index en cas d'incohérence)
            $continent = $this->resolveContinent($geoContinent, $slug, 'activity-logs');
            if ($continent instanceof RedirectResponse) {
                return $continent;
            }

            $continentName = $continent->active_translation?->name ?? $continent->code;

            $logs = $continent->activityLogs()->with(['user'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.geocontinents.activity-logs', context: ['continent_uuid' => $continent->uuid, 'continent_code' => $continent->code], message: null, activity: __("{$this->backendLangPath}.activity-logs.success", ['name' => $continentName]), is_success: true, loggable: $continent);

            return view('v1.backend.pages.geos.continents.others.activity-logs', compact('continent', 'logs'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.geocontinents.activity-logs', context: ['continent_uuid' => $geoContinent->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activity-logs.error", ['uuid' => $geoContinent->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────
    public function backend_geocontinents_edit(GeoContinent $geoContinent): View
    {
        try {

            $langs = getActiveLangs()['langs']; // Récupère les langues actives pour les passer à la vue

            // Continent résolu par le binding implicite sur uuid (404 automatique si introuvable)
            $continent = $geoContinent;

            // Log de l'accès à la page de modification d'un continent
            $this->logActivity(type: 'info', action: 'backend.geocontinents.edit', context: ['continent_uuid' => $continent->uuid, 'continent_code' => $continent->code], message: null, activity: __("{$this->backendLangPath}.edit.success", ['name' => $continent->active_translation?->name ?? $continent->code]), is_success: true, loggable: $continent);

            // Retourne la vue d'édition du continent avec les données nécessaires
            return view('v1.backend.pages.geos.continents.edit', compact('continent', 'langs'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de l'accès à la page de modification d'un continent
            $this->logActivity(type: 'error', action: 'backend.geocontinents.edit', context: ['continent_uuid' => $geoContinent->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.edit.error", ['uuid' => $geoContinent->uuid]), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération du continent
        }
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_geocontinents_update(UpdateGeoContinentRequest $request, GeoContinent $geoContinent): RedirectResponse
    {
        try {

            // Continent résolu par le binding implicite sur uuid (404 automatique si introuvable)
            $continent = $geoContinent;

            // Mise à jour du continent via le service, en passant les données validées du formulaire
            $this->geoContinentService->backend_geocontinents_update($continent, $request->validated());

            // Log de la mise à jour du continent
            $this->logActivity(type: 'info', action: 'backend.geocontinents.update', context: ['continent_uuid' => $continent->uuid, 'continent_code' => $continent->code, 'changes' => $continent->getChanges()], message: null, activity: __("{$this->backendLangPath}.update.success", ['name' => $continent->active_translation?->name ?? $continent->code]), is_success: true, loggable: $continent);

            // Redirection vers la page show du continent avec un message de succès
            return redirect()->route('backend.geocontinents.show', ['geoContinent' => $continent->uuid, 'slug' => $continent->active_translation?->slug ?? 'null'])->with('success', __("{$this->backendLangPath}.update.flash_success", ['name' => $continent->active_translation?->name ?? $continent->code]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la mise à jour du continent
            $this->logActivity(type: 'warning', action: 'backend.geocontinents.update', context: ['continent_uuid' => $geoContinent->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.warning_meta", ['uuid' => $geoContinent->uuid]), is_success: false);

            // Redirection avec le message métier explicite
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la mise à jour du continent
            $this->logActivity(type: 'error', action: 'backend.geocontinents.update', context: ['continent_uuid' => $geoContinent->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.error", ['uuid' => $geoContinent->uuid]), is_success: false);

            // Redirection avec un message d'erreur en cas de problème lors de la mise à jour
            return back()->with('error', __("{$this->backendLangPath}.update.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_geocontinents_activate(Request $request, GeoContinent $geoContinent): RedirectResponse
    {
        try {

            // Continent résolu par le binding implicite sur uuid (404 automatique si introuvable)
            $continent = $geoContinent;

            // Activation du continent via le service
            $this->geoContinentService->backend_geocontinents_activate($continent);

            // Log de l'activation du continent avec le slug du continent activé
            $this->logActivity(type: 'info', action: 'backend.geocontinents.activate', context: ['continent_uuid' => $continent->uuid, 'continent_slug' => $continent->active_translation?->slug], message: null, activity: __("{$this->backendLangPath}.activate.success", ['name' => $continent->active_translation?->name]), is_success: true, loggable: $continent);

            // Redirection avec un message de succès
            return back()->with('success', __("{$this->backendLangPath}.activate.flash_success", ['name' => $continent->active_translation?->name]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur lors de l'activation du continent (ex: si le continent est déjà actif)
            $this->logActivity(type: 'warning', action: 'backend.geocontinents.activate', context: ['continent_uuid' => $geoContinent->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activate.warning", ['uuid' => $geoContinent->uuid]), is_success: false);

            // Redirection avec un message d'erreur spécifique
            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de l'activation du continent
            $this->logActivity(type: 'error', action: 'backend.geocontinents.activate', context: ['continent_uuid' => $geoContinent->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activate.error", ['uuid' => $geoContinent->uuid]), is_success: false);

            // Redirection avec un message d'erreur en cas de problème lors de l'activation du continent
            return back()->with('error', __("{$this->backendLangPath}.activate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_geocontinents_deactivate(Request $request, GeoContinent $geoContinent): RedirectResponse
    {
        try {

            // Continent résolu par le binding implicite sur uuid (404 automatique si introuvable)
            $continent = $geoContinent;

            // Désactivation du continent via le service
            $this->geoContinentService->backend_geocontinents_deactivate($continent);

            // Log de la désactivation du continent avec le slug du continent désactivé
            $this->logActivity(type: 'info', action: 'backend.geocontinents.deactivate', context: ['continent_uuid' => $continent->uuid, 'continent_slug' => $continent->active_translation?->slug], message: null, activity: __("{$this->backendLangPath}.deactivate.success", ['name' => $continent->active_translation?->name]), is_success: true, loggable: $continent);

            // Redirection avec un message de succès
            return back()->with('success', __("{$this->backendLangPath}.deactivate.flash_success", ['name' => $continent->active_translation?->name]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur lors de la désactivation du continent (ex: si le continent est déjà inactif)
            $this->logActivity(type: 'warning', action: 'backend.geocontinents.deactivate', context: ['continent_uuid' => $geoContinent->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.deactivate.warning", ['uuid' => $geoContinent->uuid]), is_success: false);

            // Redirection avec un message d'erreur spécifique
            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la désactivation du continent
            $this->logActivity(type: 'error', action: 'backend.geocontinents.deactivate', context: ['continent_uuid' => $geoContinent->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.deactivate.error", ['uuid' => $geoContinent->uuid]), is_success: false);

            // Redirection avec un message d'erreur en cas de problème lors de la désactivation du continent
            return back()->with('error', __("{$this->backendLangPath}.deactivate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_geocontinents_trashed(): View
    {
        try {

            // Récupère tous les continents supprimés (soft deleted) avec leurs traductions, triés par date de suppression décroissante
            $geocontinents = GeoContinent::onlyTrashed()->withTrashedActiveTranslation()->withCount(['countries', 'audits', 'activityLogs'])->orderBy('deleted_at', 'desc')->get();

            // Log de la consultation de la corbeille des continents
            $this->logActivity(type: 'info', action: 'backend.geocontinents.trashed', context: ['count' => $geocontinents->count()], message: null, activity: __("{$this->backendLangPath}.trashed.success"), is_success: true);

            return view('v1.backend.pages.geos.continents.trashed', compact('geocontinents'));

        } catch (\Exception $e) {

            // Log de l'erreur lors de la consultation de la corbeille des continents
            $this->logActivity(type: 'error', action: 'backend.geocontinents.trashed', context: ['exception' => get_class($e)], message: $e->getMessage(), activity: __("{$this->backendLangPath}.trashed.error"), is_success: false);

            abort(500);
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // HELPERS PRIVÉS
    // ═══════════════════════════════════════════════════════════════

    /**
     * Résout le continent (model binding implicite sur uuid, 404 automatique si introuvable)
     * et vérifie la cohérence du slug SEO présent dans l'URL avec celui de la traduction active.
     *
     * Centralise la logique commune aux pages show / countries / audits / activity-logs :
     * - charge les compteurs (audits, activityLogs, countries) ;
     * - en cas d'incohérence du slug, logge un avertissement et redirige vers l'index.
     *
     * NB : le slug vit sur la traduction (pas de colonne slug sur geo_continents),
     * on lit donc directement `active_translation?->slug`.
     */
    private function resolveContinent(GeoContinent $geoContinent, string $slug, string $segment): GeoContinent|RedirectResponse
    {
        $continent = $geoContinent->loadCount(['audits', 'activityLogs', 'countries']);

        $expectedSlug = $continent->active_translation?->slug ?? 'null';

        if ($expectedSlug !== $slug) {
            $this->logActivity(type: 'warning', action: "backend.geocontinents.{$segment}", context: ['continent_uuid' => $continent->uuid, 'continent_slug' => $slug, 'expected_slug' => $expectedSlug], message: null, activity: __("{$this->backendLangPath}.{$segment}.slug_mismatch"), is_success: false, loggable: $continent);

            return redirect()->route('backend.geocontinents.index')->with('error', __("{$this->backendLangPath}.{$segment}.slug_mismatch"));
        }

        return $continent;
    }
}
