<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Geo\DivisionTypes\StoreGeoDivisionTypeRequest;
use App\Http\Requests\V1\Geo\DivisionTypes\UpdateGeoDivisionTypeRequest;
use App\Models\GeoDivisionType;
use App\Models\GeoDivisionTypeTranslation;
use App\Services\V1\Controllers\GeoDivisionTypeService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GeoDivisionTypeController extends Controller
{
    use LogActivity; // Trait pour le logging d'activité

    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/geos/division-types/messages/division-types-messages-controller.divisionTypes';

    // Injection de dépendance du service de gestion des types de division
    public function __construct(protected GeoDivisionTypeService $geoDivisionTypeService) {}

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_geodivisiontypes_index(): View
    {
        try {

            // ✅ Récupère tous les types avec leur traduction active, triés par ordre d'affichage
            $geodivisiontypes = GeoDivisionType::withAuditsCountTotal()->withCount(['audits', 'activityLogs'])->withActiveTranslation()->orderBy('sort_order', 'asc')->get();

            // Log de l'accès à la liste des types de division
            $this->logActivity(type: 'info', action: 'backend.geodivisiontypes.index', context: ['count' => $geodivisiontypes->count()], message: null, activity: __("{$this->backendLangPath}.index.success"), is_success: true);

            // Affiche la vue avec les types récupérés
            return view('v1.backend.pages.geos.division-types.index', compact('geodivisiontypes'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur avec le message d'exception
            $this->logActivity(type: 'error', action: 'backend.geodivisiontypes.index', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.index.error"), is_success: false);

            abort(500); // Erreur serveur générique
        }
    }

    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────
    public function backend_geodivisiontypes_create(): View
    {
        try {

            $langs = getActiveLangs()['langs']; // Récupère les langues actives pour afficher les onglets de traduction
            $lastSortOrder = getLastSortOrder(GeoDivisionType::class); // Dernier ordre d'affichage

            $this->logActivity(type: 'info', action: 'backend.geodivisiontypes.create', context: [], message: null, activity: __("{$this->backendLangPath}.create.success"), is_success: true);

            return view('v1.backend.pages.geos.division-types.create', compact('langs', 'lastSortOrder'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.geodivisiontypes.create', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.create.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_geodivisiontypes_store(StoreGeoDivisionTypeRequest $request): RedirectResponse
    {
        try {

            // Création du type + de ses traductions via le service
            $geodivisiontype = $this->geoDivisionTypeService->backend_geodivisiontypes_store($request->validated());

            $this->logActivity(type: 'info', action: 'backend.geodivisiontypes.store', context: ['division_type_uuid' => $geodivisiontype->uuid, 'division_type_code' => $geodivisiontype->code], message: null, activity: __("{$this->backendLangPath}.store.success", ['name' => $geodivisiontype->active_translation?->name]), is_success: true, loggable: $geodivisiontype);

            // Redirection vers la fiche du type avec un message de succès
            return redirect()->route('backend.geodivisiontypes.show', ['geoDivisionType' => $geodivisiontype->uuid, 'slug' => $geodivisiontype->active_translation?->slug ?? $geodivisiontype->code])
                ->with('success', __("{$this->backendLangPath}.store.flash_success", ['name' => $geodivisiontype->active_translation?->name ?? $geodivisiontype->code]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.geodivisiontypes.store', context: ['division_type_code' => $request->input('code')], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.warning"), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.geodivisiontypes.store', context: ['division_type_code' => $request->input('code')], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.store.flash_error"))->withInput();

        }
    }

    // ─────────────────────────────────────────────
    // Résolution + vérification slug (helper privé)
    // ─────────────────────────────────────────────
    private function resolveDivisionTypeWithSlugCheck(GeoDivisionType $divisionType, string $slug, string $action, string $langKey): GeoDivisionType
    {
        // Vérifie que le slug correspond au type de division, sinon renvoie une 404
        $expectedSlug = $divisionType->active_translation?->slug ?? $divisionType->code;

        // Si le slug fourni ne correspond pas au slug attendu, on log l'erreur et on renvoie une 404
        if ($expectedSlug !== $slug) {
            // Log de l'erreur de correspondance du slug avec un message d'avertissement
            $this->logActivity(type: 'warning', action: $action, context: ['division_type_uuid' => $divisionType->uuid, 'division_type_slug' => $slug, 'expected_slug' => $expectedSlug], message: null, activity: __("{$this->backendLangPath}.{$langKey}.slug_mismatch"), is_success: false, loggable: $divisionType);

            abort(404);
        }

        return $divisionType;
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function backend_geodivisiontypes_show(GeoDivisionType $geoDivisionType, string $slug): View
    {
        // Vérifie que le slug correspond
        $geoDivisionType = $this->resolveDivisionTypeWithSlugCheck($geoDivisionType, $slug, 'backend.geodivisiontypes.show', 'show');

        try {

            $langs = getActiveLangs()['langs']; // Langues actives pour les onglets de traduction

            // Type résolu par le binding implicite sur uuid (404 si introuvable)
            $divisionType = GeoDivisionType::query()->withAuditsCountTotal()->withCount(['audits', 'activityLogs'])->with(['translations' => fn ($query) => $query->whereNull('deleted_at')->with('lang')])->findOrFail($geoDivisionType->id);

            $geoDivisionTypeTranslations = GeoDivisionTypeTranslation::onlyTrashed()->where('division_type_id', $divisionType->id)->get(); // Traductions en corbeille

            $divisionTypeName = $divisionType->active_translation?->name ?? __("{$this->backendLangPath}.show.no_name");

            // Log de la consultation du détail d'un type
            $this->logActivity(type: 'info', action: 'backend.geodivisiontypes.show', context: ['division_type_uuid' => $divisionType->uuid, 'division_type_code' => $divisionType->code], message: null, activity: __("{$this->backendLangPath}.show.success", ['name' => $divisionTypeName]), is_success: true, loggable: $divisionType);

            return view('v1.backend.pages.geos.division-types.show', compact('divisionType', 'langs', 'geoDivisionTypeTranslations'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.geodivisiontypes.show', context: ['division_type_uuid' => $geoDivisionType->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.show.error", ['uuid' => $geoDivisionType->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────
    public function backend_geodivisiontypes_edit(GeoDivisionType $geoDivisionType): View
    {
        try {

            $langs = getActiveLangs()['langs']; // Langues actives pour les onglets de traduction

            // Type résolu par le binding implicite sur uuid (404 si introuvable)
            $divisionType = GeoDivisionType::query()->with(['translations' => fn ($query) => $query->whereNull('deleted_at')->with('lang')])->findOrFail($geoDivisionType->id);

            $this->logActivity(type: 'info', action: 'backend.geodivisiontypes.edit', context: ['division_type_uuid' => $divisionType->uuid], message: null, activity: __("{$this->backendLangPath}.edit.success", ['name' => $divisionType->active_translation?->name]), is_success: true, loggable: $divisionType);

            return view('v1.backend.pages.geos.division-types.edit', compact('divisionType', 'langs'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.geodivisiontypes.edit', context: ['division_type_uuid' => $geoDivisionType->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.edit.error", ['uuid' => $geoDivisionType->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_geodivisiontypes_update(UpdateGeoDivisionTypeRequest $request, GeoDivisionType $geoDivisionType): RedirectResponse
    {
        try {

            // Mise à jour du type + synchronisation des traductions via le service
            $geodivisiontype = $this->geoDivisionTypeService->backend_geodivisiontypes_update($geoDivisionType, $request->validated());

            $this->logActivity(type: 'info', action: 'backend.geodivisiontypes.update', context: ['division_type_uuid' => $geodivisiontype->uuid], message: null, activity: __("{$this->backendLangPath}.update.success", ['name' => $geodivisiontype->active_translation?->name]), is_success: true, loggable: $geodivisiontype);

            // Redirection vers la fiche du type avec un message de succès
            return redirect()->route('backend.geodivisiontypes.show', ['geoDivisionType' => $geodivisiontype->uuid, 'slug' => $geodivisiontype->active_translation?->slug ?? $geodivisiontype->code])
                ->with('success', __("{$this->backendLangPath}.update.flash_success", ['name' => $geodivisiontype->active_translation?->name ?? $geodivisiontype->code]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.geodivisiontypes.update', context: ['division_type_uuid' => $geoDivisionType->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.warning"), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.geodivisiontypes.update', context: ['division_type_uuid' => $geoDivisionType->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.error"), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.update.flash_error"))->withInput();

        }
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_geodivisiontypes_activate(GeoDivisionType $geoDivisionType): RedirectResponse
    {
        try {

            $this->geoDivisionTypeService->backend_geodivisiontypes_activate($geoDivisionType);

            $this->logActivity(type: 'info', action: 'backend.geodivisiontypes.activate', context: ['division_type_uuid' => $geoDivisionType->uuid], message: null, activity: __("{$this->backendLangPath}.activate.success", ['name' => $geoDivisionType->active_translation?->name]), is_success: true, loggable: $geoDivisionType);

            return back()->with('success', __("{$this->backendLangPath}.activate.flash_success", ['name' => $geoDivisionType->active_translation?->name ?? $geoDivisionType->code]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.geodivisiontypes.activate', context: ['division_type_uuid' => $geoDivisionType->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activate.warning"), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.geodivisiontypes.activate', context: ['division_type_uuid' => $geoDivisionType->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activate.error"), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.activate.flash_error"));

        }
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_geodivisiontypes_deactivate(GeoDivisionType $geoDivisionType): RedirectResponse
    {
        try {

            $this->geoDivisionTypeService->backend_geodivisiontypes_deactivate($geoDivisionType);

            $this->logActivity(type: 'info', action: 'backend.geodivisiontypes.deactivate', context: ['division_type_uuid' => $geoDivisionType->uuid], message: null, activity: __("{$this->backendLangPath}.deactivate.success", ['name' => $geoDivisionType->active_translation?->name]), is_success: true, loggable: $geoDivisionType);

            return back()->with('success', __("{$this->backendLangPath}.deactivate.flash_success", ['name' => $geoDivisionType->active_translation?->name ?? $geoDivisionType->code]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.geodivisiontypes.deactivate', context: ['division_type_uuid' => $geoDivisionType->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.deactivate.warning"), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.geodivisiontypes.deactivate', context: ['division_type_uuid' => $geoDivisionType->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.deactivate.error"), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.deactivate.flash_error"));

        }
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_geodivisiontypes_destroy(GeoDivisionType $geoDivisionType): RedirectResponse
    {
        try {

            $this->geoDivisionTypeService->backend_geodivisiontypes_destroy($geoDivisionType);

            $this->logActivity(type: 'warning', action: 'backend.geodivisiontypes.destroy', context: ['division_type_uuid' => $geoDivisionType->uuid], message: null, activity: __("{$this->backendLangPath}.destroy.success", ['name' => $geoDivisionType->active_translation?->name]), is_success: true, loggable: $geoDivisionType);

            return redirect()->route('backend.geodivisiontypes.index')->with('success', __("{$this->backendLangPath}.destroy.flash_success"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.geodivisiontypes.destroy', context: ['division_type_uuid' => $geoDivisionType->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning"), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.geodivisiontypes.destroy', context: ['division_type_uuid' => $geoDivisionType->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.error"), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.destroy.flash_error"));

        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_geodivisiontypes_trashed(): View
    {
        try {

            $geodivisiontypes = GeoDivisionType::onlyTrashed()->withAuditsCountTotal()->withCount(['audits', 'activityLogs'])->withActiveTranslation()->orderBy('deleted_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.geodivisiontypes.trashed', context: ['count' => $geodivisiontypes->count()], message: null, activity: __("{$this->backendLangPath}.trashed.success"), is_success: true);

            return view('v1.backend.pages.geos.division-types.trashed', compact('geodivisiontypes'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.geodivisiontypes.trashed', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.trashed.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_geodivisiontypes_restore(string $uuid): RedirectResponse
    {
        try {

            // Récupère le type même s'il est soft-deleted
            $geoDivisionType = GeoDivisionType::withTrashed()->where('uuid', $uuid)->firstOrFail();

            $this->geoDivisionTypeService->backend_geodivisiontypes_restore($geoDivisionType);

            $this->logActivity(type: 'info', action: 'backend.geodivisiontypes.restore', context: ['division_type_uuid' => $geoDivisionType->uuid], message: null, activity: __("{$this->backendLangPath}.restore.success", ['name' => $geoDivisionType->active_translation?->name]), is_success: true, loggable: $geoDivisionType);

            return back()->with('success', __("{$this->backendLangPath}.restore.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.geodivisiontypes.restore', context: ['division_type_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.restore.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.geodivisiontypes.restore', context: ['division_type_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.geodivisiontypes.restore', context: ['division_type_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.restore.flash_error"));

        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_geodivisiontypes_force_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupère le type uniquement s'il est en corbeille
            $geoDivisionType = GeoDivisionType::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            $this->geoDivisionTypeService->backend_geodivisiontypes_force_destroy($geoDivisionType);

            $this->logActivity(type: 'warning', action: 'backend.geodivisiontypes.force_destroy', context: ['division_type_uuid' => $geoDivisionType->uuid], message: null, activity: __("{$this->backendLangPath}.force_destroy.success", ['name' => $geoDivisionType->active_translation?->name]), is_success: true, loggable: $geoDivisionType);

            return back()->with('success', __("{$this->backendLangPath}.force_destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.geodivisiontypes.force_destroy', context: ['division_type_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.geodivisiontypes.force_destroy', context: ['division_type_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.geodivisiontypes.force_destroy', context: ['division_type_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_error"));

        }
    }

    // ─────────────────────────────────────────────
    // AUDITS
    // ─────────────────────────────────────────────
    public function backend_geodivisiontypes_audits(GeoDivisionType $geoDivisionType): View
    {
        try {

            // Type résolu par le binding implicite sur uuid (404 automatique si introuvable)
            $divisionType = $geoDivisionType;

            // Historique des modifications du type (entité + ses traductions)
            $audits = $divisionType->auditsWithTranslations()->with(['user', 'auditable'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.geodivisiontypes.audits', context: ['division_type_uuid' => $divisionType->uuid], message: null, activity: __("{$this->backendLangPath}.audits.success", ['name' => $divisionType->active_translation?->name]), is_success: true, loggable: $divisionType);

            return view('v1.backend.pages.geos.division-types.others.audits', compact('divisionType', 'audits'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.geodivisiontypes.audits', context: ['division_type_uuid' => $geoDivisionType->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.audits.error", ['uuid' => $geoDivisionType->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVITY LOGS
    // ─────────────────────────────────────────────
    public function backend_geodivisiontypes_activity_logs(GeoDivisionType $geoDivisionType): View
    {
        try {

            // Type résolu par le binding implicite sur uuid (404 automatique si introuvable)
            $divisionType = $geoDivisionType;

            $activityLogs = $divisionType->activityLogs()->latest()->paginate(20); // Journal d'activité du type

            $this->logActivity(type: 'info', action: 'backend.geodivisiontypes.activity_logs', context: ['division_type_uuid' => $divisionType->uuid], message: null, activity: __("{$this->backendLangPath}.activity_logs.success", ['name' => $divisionType->active_translation?->name]), is_success: true, loggable: $divisionType);

            return view('v1.backend.pages.geos.division-types.others.activity-logs', compact('divisionType', 'activityLogs'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.geodivisiontypes.activity_logs', context: ['division_type_uuid' => $geoDivisionType->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activity_logs.error", ['uuid' => $geoDivisionType->uuid]), is_success: false);

            abort(500);
        }
    }
}
