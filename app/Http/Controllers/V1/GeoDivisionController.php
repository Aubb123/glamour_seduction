<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Geo\Divisions\StoreGeoDivisionRequest;
use App\Http\Requests\V1\Geo\Divisions\UpdateGeoDivisionRequest;
use App\Models\GeoCountry;
use App\Models\GeoDivision;
use App\Models\GeoDivisionTranslation;
use App\Models\GeoDivisionType;
use App\Services\V1\Controllers\GeoDivisionService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

use function __;

class GeoDivisionController extends Controller
{
    use LogActivity; // Trait pour le logging d'activité

    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/geos/divisions/messages/divisions-messages-controller.divisions';

    // Injection de dépendance du service de gestion des divisions
    public function __construct(protected GeoDivisionService $geoDivisionService) {}

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_geodivisions_index(): View
    {
        try {

            // ✅ Récupère toutes les divisions avec leur traduction active, pays, type et parent
            $geodivisions = GeoDivision::withAuditsCountTotal()
                ->withCount(['audits', 'activityLogs'])
                ->withActiveTranslation()
                ->with(['country', 'type', 'parent'])
                ->orderBy('level', 'asc')
                ->orderBy('created_at', 'asc')
                ->get();

            // Log de l'accès à la liste des divisions
            $this->logActivity(type: 'info', action: 'backend.geodivisions.index', context: ['count' => $geodivisions->count()], message: null, activity: __("{$this->backendLangPath}.index.success"), is_success: true);

            // Affiche la vue avec les divisions récupérées
            return view('v1.backend.pages.geos.divisions.index', compact('geodivisions'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur avec le message d'exception
            $this->logActivity(type: 'error', action: 'backend.geodivisions.index', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.index.error"), is_success: false);

            abort(500); // Erreur serveur générique
        }
    }

    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────
    public function backend_geodivisions_create(): View
    {
        try {

            $langs = getActiveLangs()['langs']; // Langues actives pour les onglets de traduction

            // Pays actifs et types de division actifs pour les selects
            $countries = GeoCountry::active()->withActiveTranslation()->orderBy('sort_order', 'asc')->get();
            $divisionTypes = GeoDivisionType::active()->withActiveTranslation()->orderBy('level', 'asc')->get();

            $this->logActivity(type: 'info', action: 'backend.geodivisions.create', context: [], message: null, activity: __("{$this->backendLangPath}.create.success"), is_success: true);

            return view('v1.backend.pages.geos.divisions.create', compact('langs', 'countries', 'divisionTypes'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.geodivisions.create', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.create.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_geodivisions_store(StoreGeoDivisionRequest $request): RedirectResponse
    {
        try {

            // Création de la division + de ses traductions via le service
            $geodivision = $this->geoDivisionService->backend_geodivisions_store($request->validated());

            $this->logActivity(type: 'info', action: 'backend.geodivisions.store', context: ['division_uuid' => $geodivision->uuid], message: null, activity: __("{$this->backendLangPath}.store.success", ['name' => $geodivision->active_translation?->name]), is_success: true, loggable: $geodivision);

            // Redirection vers la fiche de la division avec un message de succès
            return redirect()->route('backend.geodivisions.show', ['geoDivision' => $geodivision->uuid, 'slug' => $geodivision->active_translation?->slug ?? 'null'])->with('success', __("{$this->backendLangPath}.store.flash_success", ['name' => $geodivision->active_translation?->name ?? '']));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.geodivisions.store', context: ['country_id' => $request->input('country_id')], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.warning"), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.geodivisions.store', context: ['country_id' => $request->input('country_id')], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.store.flash_error"))->withInput();

        }
    }

    // ─────────────────────────────────────────────
    // Résolution + vérification slug (helper privé)
    // ─────────────────────────────────────────────
    private function resolveDivisionWithSlugCheck(GeoDivision $division, string $slug, string $action, string $langKey): GeoDivision
    {
        // Résout AVANT le try pour conserver un vrai 404 (pas de catch d'exception HTTP)
        $expectedSlug = $division->active_translation?->slug ?? 'null';

        if ($expectedSlug !== $slug) {
            // Log de l'erreur de correspondance du slug avec un message d'avertissement
            $this->logActivity(type: 'warning', action: $action, context: ['division_uuid' => $division->uuid, 'division_slug' => $slug, 'expected_slug' => $expectedSlug], message: null, activity: __("{$this->backendLangPath}.{$langKey}.slug_mismatch"), is_success: false, loggable: $division);

            abort(404);
        }

        return $division;
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function backend_geodivisions_show(GeoDivision $geoDivision, string $slug): View
    {
        // Vérifie que le slug correspond (404 sinon)
        $geoDivision = $this->resolveDivisionWithSlugCheck($geoDivision, $slug, 'backend.geodivisions.show', 'show');

        try {

            $langs = getActiveLangs()['langs']; // Langues actives pour les onglets de traduction

            $division = GeoDivision::query()->withAuditsCountTotal()->withCount(['audits', 'activityLogs', 'children'])->with(['country', 'type', 'parent'])->with(['translations' => fn ($query) => $query->whereNull('deleted_at')->with('lang')])->findOrFail($geoDivision->id);

            $geoDivisionTranslations = GeoDivisionTranslation::onlyTrashed()->where('division_id', $division->id)->get();

            $divisionName = $division->active_translation?->name ?? __("{$this->backendLangPath}.show.no_name");

            $this->logActivity(type: 'info', action: 'backend.geodivisions.show', context: ['division_uuid' => $division->uuid], message: null, activity: __("{$this->backendLangPath}.show.success", ['name' => $divisionName]), is_success: true, loggable: $division);

            return view('v1.backend.pages.geos.divisions.show', compact('division', 'langs', 'geoDivisionTranslations'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.geodivisions.show', context: ['division_uuid' => $geoDivision->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.show.error", ['uuid' => $geoDivision->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────
    public function backend_geodivisions_edit(GeoDivision $geoDivision): View
    {
        try {

            $langs = getActiveLangs()['langs'];

            // Pays (immuable, affiché en lecture seule) + tous les pays pour le filtre JS du parent
            $countries = GeoCountry::active()->withActiveTranslation()->orderBy('sort_order', 'asc')->get();
            $divisionTypes = GeoDivisionType::active()->withActiveTranslation()->orderBy('level', 'asc')->get();

            $division = GeoDivision::query()
                ->with(['country', 'type', 'parent'])
                ->with(['translations' => fn ($query) => $query->whereNull('deleted_at')->with('lang')])
                ->findOrFail($geoDivision->id);

            // Toutes les divisions du même pays (pour le select parent, filtrées en JS)
            $divisions = GeoDivision::where('country_id', $division->country_id)
                ->withActiveTranslation()
                ->orderBy('level', 'asc')
                ->get();

            $this->logActivity(type: 'info', action: 'backend.geodivisions.edit', context: ['division_uuid' => $division->uuid], message: null, activity: __("{$this->backendLangPath}.edit.success", ['name' => $division->active_translation?->name]), is_success: true, loggable: $division);

            return view('v1.backend.pages.geos.divisions.edit', compact('division', 'langs', 'countries', 'divisionTypes', 'divisions'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.geodivisions.edit', context: ['division_uuid' => $geoDivision->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.edit.error", ['uuid' => $geoDivision->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_geodivisions_update(UpdateGeoDivisionRequest $request, GeoDivision $geoDivision): RedirectResponse
    {
        try {

            // Mise à jour de la division + synchronisation des traductions via le service
            $geodivision = $this->geoDivisionService->backend_geodivisions_update($geoDivision, $request->validated());

            $this->logActivity(type: 'info', action: 'backend.geodivisions.update', context: ['division_uuid' => $geodivision->uuid], message: null, activity: __("{$this->backendLangPath}.update.success", ['name' => $geodivision->active_translation?->name]), is_success: true, loggable: $geodivision);

            // Redirection vers la fiche de la division avec un message de succès
            return redirect()->route('backend.geodivisions.show', ['geoDivision' => $geodivision->uuid, 'slug' => $geodivision->active_translation?->slug ?? 'null'])->with('success', __("{$this->backendLangPath}.update.flash_success", ['name' => $geodivision->active_translation?->name ?? '']));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.geodivisions.update', context: ['division_uuid' => $geoDivision->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.warning"), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.geodivisions.update', context: ['division_uuid' => $geoDivision->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.error"), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.update.flash_error"))->withInput();

        }
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_geodivisions_activate(GeoDivision $geoDivision): RedirectResponse
    {
        try {

            $this->geoDivisionService->backend_geodivisions_activate($geoDivision);

            $this->logActivity(type: 'info', action: 'backend.geodivisions.activate', context: ['division_uuid' => $geoDivision->uuid], message: null, activity: __("{$this->backendLangPath}.activate.success", ['name' => $geoDivision->active_translation?->name]), is_success: true, loggable: $geoDivision);

            return back()->with('success', __("{$this->backendLangPath}.activate.flash_success", ['name' => $geoDivision->active_translation?->name ?? '']));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.geodivisions.activate', context: ['division_uuid' => $geoDivision->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activate.warning"), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.geodivisions.activate', context: ['division_uuid' => $geoDivision->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activate.error"), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.activate.flash_error"));

        }
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_geodivisions_deactivate(GeoDivision $geoDivision): RedirectResponse
    {
        try {

            $this->geoDivisionService->backend_geodivisions_deactivate($geoDivision);

            $this->logActivity(type: 'info', action: 'backend.geodivisions.deactivate', context: ['division_uuid' => $geoDivision->uuid], message: null, activity: __("{$this->backendLangPath}.deactivate.success", ['name' => $geoDivision->active_translation?->name]), is_success: true, loggable: $geoDivision);

            return back()->with('success', __("{$this->backendLangPath}.deactivate.flash_success", ['name' => $geoDivision->active_translation?->name ?? '']));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.geodivisions.deactivate', context: ['division_uuid' => $geoDivision->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.deactivate.warning"), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.geodivisions.deactivate', context: ['division_uuid' => $geoDivision->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.deactivate.error"), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.deactivate.flash_error"));

        }
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_geodivisions_destroy(GeoDivision $geoDivision): RedirectResponse
    {
        try {

            $this->geoDivisionService->backend_geodivisions_destroy($geoDivision);

            $this->logActivity(type: 'warning', action: 'backend.geodivisions.destroy', context: ['division_uuid' => $geoDivision->uuid], message: null, activity: __("{$this->backendLangPath}.destroy.success", ['name' => $geoDivision->active_translation?->name]), is_success: true, loggable: $geoDivision);

            return redirect()->route('backend.geodivisions.index')->with('success', __("{$this->backendLangPath}.destroy.flash_success"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.geodivisions.destroy', context: ['division_uuid' => $geoDivision->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning"), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.geodivisions.destroy', context: ['division_uuid' => $geoDivision->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.error"), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.destroy.flash_error"));

        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_geodivisions_trashed(): View
    {
        try {

            $geodivisions = GeoDivision::onlyTrashed()->withAuditsCountTotal()->withCount(['audits', 'activityLogs'])->withTrashedActiveTranslation()->with(['country', 'type', 'parent'])->orderBy('deleted_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.geodivisions.trashed', context: ['count' => $geodivisions->count()], message: null, activity: __("{$this->backendLangPath}.trashed.success"), is_success: true);

            return view('v1.backend.pages.geos.divisions.trashed', compact('geodivisions'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.geodivisions.trashed', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.trashed.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_geodivisions_restore(string $uuid): RedirectResponse
    {
        try {

            // Récupère la division même si elle est soft-deleted
            $geoDivision = GeoDivision::withTrashed()->where('uuid', $uuid)->firstOrFail();

            $this->geoDivisionService->backend_geodivisions_restore($geoDivision);

            $this->logActivity(type: 'info', action: 'backend.geodivisions.restore', context: ['division_uuid' => $geoDivision->uuid], message: null, activity: __("{$this->backendLangPath}.restore.success", ['name' => $geoDivision->active_translation?->name]), is_success: true, loggable: $geoDivision);

            return back()->with('success', __("{$this->backendLangPath}.restore.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.geodivisions.restore', context: ['division_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.restore.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.geodivisions.restore', context: ['division_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.geodivisions.restore', context: ['division_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.restore.flash_error"));

        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_geodivisions_force_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupère la division uniquement si elle est en corbeille
            $geoDivision = GeoDivision::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            $this->geoDivisionService->backend_geodivisions_force_destroy($geoDivision);

            $this->logActivity(type: 'warning', action: 'backend.geodivisions.force_destroy', context: ['division_uuid' => $geoDivision->uuid], message: null, activity: __("{$this->backendLangPath}.force_destroy.success", ['name' => $geoDivision->active_translation?->name]), is_success: true, loggable: $geoDivision);

            return back()->with('success', __("{$this->backendLangPath}.force_destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.geodivisions.force_destroy', context: ['division_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.geodivisions.force_destroy', context: ['division_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.geodivisions.force_destroy', context: ['division_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_error"));

        }
    }

    // ─────────────────────────────────────────────
    // AUDITS
    // ─────────────────────────────────────────────
    public function backend_geodivisions_audits(GeoDivision $geoDivision, string $slug): View
    {
        // Vérifie que le slug correspond (404 sinon)
        $geoDivision = $this->resolveDivisionWithSlugCheck($geoDivision, $slug, 'backend.geodivisions.audits', 'audits');

        try {

            $division = $geoDivision;

            $division->loadCount(['audits', 'activityLogs', 'children']);

            $audits = $division->auditsWithTranslations()->with(['user', 'auditable'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.geodivisions.audits', context: ['division_uuid' => $division->uuid], message: null, activity: __("{$this->backendLangPath}.audits.success", ['name' => $division->active_translation?->name]), is_success: true, loggable: $division);

            return view('v1.backend.pages.geos.divisions.others.audits', compact('division', 'audits'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.geodivisions.audits', context: ['division_uuid' => $geoDivision->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.audits.error", ['uuid' => $geoDivision->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVITY LOGS
    // ─────────────────────────────────────────────
    public function backend_geodivisions_activity_logs(GeoDivision $geoDivision, string $slug): View
    {
        // Vérifie que le slug correspond (404 sinon)
        $geoDivision = $this->resolveDivisionWithSlugCheck($geoDivision, $slug, 'backend.geodivisions.activity_logs', 'activity_logs');

        try {

            $division = $geoDivision;

            $division->loadCount(['audits', 'activityLogs', 'children']);

            $activityLogs = $division->activityLogs()->with(['user'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.geodivisions.activity_logs', context: ['division_uuid' => $division->uuid], message: null, activity: __("{$this->backendLangPath}.activity_logs.success", ['name' => $division->active_translation?->name]), is_success: true, loggable: $division);

            return view('v1.backend.pages.geos.divisions.others.activity-logs', compact('division', 'activityLogs'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.geodivisions.activity_logs', context: ['division_uuid' => $geoDivision->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activity_logs.error", ['uuid' => $geoDivision->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // CHILDREN (sous-divisions)
    // ─────────────────────────────────────────────
    public function backend_geodivisions_children(GeoDivision $geoDivision, string $slug): View
    {
        // Vérifie que le slug correspond (404 sinon)
        $geoDivision = $this->resolveDivisionWithSlugCheck($geoDivision, $slug, 'backend.geodivisions.children', 'children');

        try {

            $division = $geoDivision;

            $division->loadCount(['audits', 'activityLogs', 'children']);

            $children = $division->children()->withActiveTranslation()->with(['country', 'type', 'parent'])->withAuditsCountTotal()->withCount(['audits', 'activityLogs'])->orderBy('level', 'asc')->orderBy('created_at', 'asc')->get();

            $this->logActivity(type: 'info', action: 'backend.geodivisions.children', context: ['division_uuid' => $division->uuid], message: null, activity: __("{$this->backendLangPath}.children.success", ['name' => $division->active_translation?->name]), is_success: true, loggable: $division);

            return view('v1.backend.pages.geos.divisions.others.children', compact('division', 'children'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.geodivisions.children', context: ['division_uuid' => $geoDivision->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.children.error", ['uuid' => $geoDivision->uuid]), is_success: false);

            abort(500);
        }
    }
}
