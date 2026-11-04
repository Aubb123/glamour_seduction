<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\PlatformVersion\StorePlatformVersionRequest;
use App\Http\Requests\V1\PlatformVersion\UpdatePlatformVersionRequest;
use App\Models\PlatformVersion;
use App\Models\PlatformVersionTranslation;
use App\Services\V1\Controllers\PlatformVersionService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PlatformVersionController extends Controller
{
    use LogActivity; // Trait pour le logging d'activité

    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/platform-versions/messages/platform-messages-controller.versions';

    // Injection de dépendance du service de gestion des versions du site
    public function __construct(protected PlatformVersionService $platformVersionService) {}

    // ═══════════════════════════════════════════════════════════════
    // BACKEND
    // ═══════════════════════════════════════════════════════════════

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_versions_index(): View
    {
        try {

            // ✅ Récupère toutes les versions avec leurs traductions, triées par date de création décroissante
            $versions = PlatformVersion::withCount(['audits', 'activityLogs'])->withActiveTranslation()->orderBy('created_at', 'desc')->get();

            // Log de l'accès à la liste des versions
            $this->logActivity(type: 'info', action: 'backend.versions.index', context: ['count' => $versions->count()], message: null, activity: __("{$this->langPath}.index.success"), is_success: true);

            // Affiche la vue avec les versions récupérées
            return view('v1.backend.pages.platform-versions.index', compact('versions'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur avec le message d'exception
            $this->logActivity(type: 'error', action: 'backend.versions.index', context: [], message: $e->getMessage(), activity: __("{$this->langPath}.index.error"), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération des versions
        }
    }

    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────
    public function backend_versions_create(): View
    {
        try {

            // Récupère les langues actives pour le formulaire de création
            $langs = getActiveLangs()['langs'];

            // Log de l'accès à la page de création d'une version
            $this->logActivity(type: 'info', action: 'backend.versions.create', context: [], message: null, activity: __("{$this->langPath}.create.success"), is_success: true);

            // Affiche la vue de création d'une version
            return view('v1.backend.pages.platform-versions.create', compact('langs'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de l'accès à la page de création d'une version
            $this->logActivity(type: 'error', action: 'backend.versions.create', context: [], message: $e->getMessage(), activity: __("{$this->langPath}.create.error"), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de l'accès à la page de création
        }
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_versions_store(StorePlatformVersionRequest $request): RedirectResponse
    {
        try {

            // Création de la version via le service, en passant les données validées du formulaire
            $version = $this->platformVersionService->backend_versions_store($request->validated());

            // Log de la création de la version avec le code de la version créée
            $this->logActivity(type: 'info', action: 'backend.versions.store', context: ['version_uuid' => $version->uuid, 'version_code' => $version->code], message: null, activity: __("{$this->langPath}.store.success", ['uuid' => $version->uuid]), is_success: true, loggable: $version);

            // Redirection vers la liste des versions avec un message de succès
            return redirect()->route('backend.versions.show', ['platformVersion' => $version->uuid, 'slug' => $version->active_translation?->slug ?? $version->code])->with('success', __("{$this->langPath}.store.flash_success", ['name' => $version->active_translation->name]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la création de la version
            $this->logActivity(type: 'warning', action: 'backend.versions.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->langPath}.store.error"), is_success: false);

            // Redirection avec le message métier explicite
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la création de la version
            $this->logActivity(type: 'error', action: 'backend.versions.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->langPath}.store.error"), is_success: false);

            // Redirection avec un message d'erreur en cas de problème, en conservant les données du formulaire
            return redirect()->back()->with('error', __("{$this->langPath}.store.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function backend_versions_show(PlatformVersion $platformVersion, string $slug): View|RedirectResponse
    {
        try {

            $langs = getActiveLangs()['langs']; // Récupère les langues actives pour afficher les onglets de traduction

            // Récupère la version avec ses traductions (résolue par le binding implicite sur uuid)
            $version = $platformVersion->loadCount(['audits', 'activityLogs']);

            $expectedSlug = $version->active_translation?->slug ?? $version->code;

            if ($expectedSlug !== $slug) {
                $this->logActivity(type: 'warning', action: 'backend.versions.show', context: ['version_uuid' => $version->uuid, 'version_code' => $slug, 'expected_slug' => $version->active_translation?->slug ?? $version->code], message: null, activity: __("{$this->langPath}.show.slug_mismatch"), is_success: false, loggable: $version);

                return redirect()->route('backend.versions.index')->with('error', __("{$this->langPath}.show.slug_mismatch")); // Redirection vers la liste des versions avec un message d'erreur
            }

            // Récupère toutes les traductions supprimées (soft deleted) de cette version
            $platformVersionTranslations = PlatformVersionTranslation::onlyTrashed()->where('platform_version_id', $version->id)->get();

            $versionName = isset($version->active_translation) ? $version->active_translation->name : __("{$this->langPath}.show.no_name");

            // Log de la consultation du détail d'une version
            $this->logActivity(type: 'info', action: 'backend.versions.show', context: ['version_uuid' => $version->uuid, 'version_code' => $version->code], message: null, activity: __("{$this->langPath}.show.success", ['name' => $versionName]), is_success: true, loggable: $version);

            // Retourne la vue de détail de la version avec les données nécessaires
            return view('v1.backend.pages.platform-versions.show', compact('version', 'langs', 'platformVersionTranslations'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la consultation du détail d'une version
            $this->logActivity(type: 'error', action: 'backend.versions.show', context: ['version_uuid' => $platformVersion->uuid, 'version_code' => $slug], message: $e->getMessage(), activity: __("{$this->langPath}.show.error", ['uuid' => $platformVersion->uuid]), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération de la version
        }
    }

    // ─────────────────────────────────────────────
    // AUDITS
    // ─────────────────────────────────────────────
    public function backend_versions_audits(PlatformVersion $platformVersion, string $slug): View|RedirectResponse
    {
        try {

            // Version résolue par le binding implicite sur uuid (404 automatique si introuvable)
            $version = $platformVersion->loadCount(['audits', 'activityLogs']);

            $expectedSlug = $version->active_translation?->slug ?? $version->code;

            if ($expectedSlug !== $slug) {
                $this->logActivity(type: 'warning', action: 'backend.versions.audits', context: ['version_uuid' => $version->uuid, 'version_code' => $slug, 'expected_slug' => $expectedSlug], message: null, activity: __("{$this->langPath}.audits.slug_mismatch"), is_success: false, loggable: $version);

                return redirect()->route('backend.versions.index')->with('error', __("{$this->langPath}.audits.slug_mismatch"));
            }

            $versionName = $version->active_translation?->name ?? $version->code;

            $audits = $version->audits()->with(['user', 'auditable'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.versions.audits', context: ['version_uuid' => $version->uuid, 'version_code' => $version->code], message: null, activity: __("{$this->langPath}.audits.success", ['name' => $versionName]), is_success: true, loggable: $version);

            return view('v1.backend.pages.platform-versions.others.audits', compact('version', 'audits'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.versions.audits', context: ['version_uuid' => $platformVersion->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.audits.error", ['uuid' => $platformVersion->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVITY LOGS
    // ─────────────────────────────────────────────
    public function backend_versions_activity_logs(PlatformVersion $platformVersion, string $slug): View|RedirectResponse
    {
        try {

            // Version résolue par le binding implicite sur uuid (404 automatique si introuvable)
            $version = $platformVersion->loadCount(['audits', 'activityLogs']);

            $expectedSlug = $version->active_translation?->slug ?? $version->code;

            if ($expectedSlug !== $slug) {
                $this->logActivity(type: 'warning', action: 'backend.versions.activity-logs', context: ['version_uuid' => $version->uuid, 'version_code' => $slug, 'expected_slug' => $expectedSlug], message: null, activity: __("{$this->langPath}.activity-logs.slug_mismatch"), is_success: false, loggable: $version);

                return redirect()->route('backend.versions.index')->with('error', __("{$this->langPath}.activity-logs.slug_mismatch"));
            }

            $versionName = $version->active_translation?->name ?? $version->code;

            $logs = $version->activityLogs()->with(['user'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.versions.activity-logs', context: ['version_uuid' => $version->uuid, 'version_code' => $version->code], message: null, activity: __("{$this->langPath}.activity-logs.success", ['name' => $versionName]), is_success: true, loggable: $version);

            return view('v1.backend.pages.platform-versions.others.activity-logs', compact('version', 'logs'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.versions.activity-logs', context: ['version_uuid' => $platformVersion->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.activity-logs.error", ['uuid' => $platformVersion->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────
    public function backend_versions_edit(PlatformVersion $platformVersion): View
    {
        try {

            $langs = getActiveLangs()['langs']; // Récupère les langues actives pour les passer à la vue

            // Version résolue par le binding implicite sur uuid (404 automatique si introuvable)
            $version = $platformVersion;

            // Log de l'accès à la page de modification d'une version
            $this->logActivity(type: 'info', action: 'backend.versions.edit', context: ['version_uuid' => $version->uuid, 'version_code' => $version->code], message: null, activity: __("{$this->langPath}.edit.success", ['name' => $version->active_translation->name]), is_success: true, loggable: $version);

            // Retourne la vue d'édition de la version avec les données nécessaires
            return view('v1.backend.pages.platform-versions.edit', compact('version', 'langs'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de l'accès à la page de modification d'une version
            $this->logActivity(type: 'error', action: 'backend.versions.edit', context: ['version' => $platformVersion->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.edit.error", ['uuid' => $platformVersion->uuid]), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération de la version
        }
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_versions_update(UpdatePlatformVersionRequest $request, PlatformVersion $platformVersion): RedirectResponse
    {
        try {

            // Version résolue par le binding implicite sur uuid (404 automatique si introuvable)
            $version = $platformVersion;

            // Mise à jour de la version via le service, en passant les données validées du formulaire
            $this->platformVersionService->backend_versions_update($version, $request->validated());

            // Récupère une instance fraîche pour capturer les vrais changements
            $version->refresh();

            // Log de la mise à jour de la version avec le uuid et les changements effectués
            $this->logActivity(type: 'info', action: 'backend.versions.update', context: ['version_uuid' => $version->uuid, 'version_code' => $version->code, 'changes' => $version->getChanges()], message: null, activity: __("{$this->langPath}.update.success", ['name' => $version->active_translation->name]), is_success: true, loggable: $version);

            // Redirection vers la liste des versions avec un message de succès
            return redirect()->route('backend.versions.show', ['platformVersion' => $version->uuid, 'slug' => $version->active_translation?->slug ?? $version->code])->with('success', __("{$this->langPath}.update.flash_success", ['name' => $version->active_translation->name]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la dépublication de la version
            $this->logActivity(type: 'warning', action: 'backend.versions.update', context: ['version_uuid' => $platformVersion->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.update.warning_unpub", ['uuid' => $platformVersion->uuid]), is_success: false);

            // Redirection avec le message métier explicite
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la mise à jour de la version
            $this->logActivity(type: 'error', action: 'backend.versions.update', context: ['version_uuid' => $platformVersion->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.update.error", ['uuid' => $platformVersion->uuid]), is_success: false);

            // Redirection avec un message d'erreur en cas de problème lors de la mise à jour, en conservant les données du formulaire
            return back()->with('error', __("{$this->langPath}.update.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_versions_activate(PlatformVersion $platformVersion): RedirectResponse
    {
        try {

            // Version résolue par le binding implicite sur uuid (404 automatique si introuvable)
            $version = $platformVersion;

            // Activation de la version via le service
            $this->platformVersionService->backend_versions_activate($version);

            // Log de l'activation de la version avec le code de la version activée
            $this->logActivity(type: 'info', action: 'backend.versions.activate', context: ['version_uuid' => $version->uuid, 'version_code' => $version->code], message: null, activity: __("{$this->langPath}.activate.success", ['name' => $version->active_translation?->name ?? 'Version inconnue']), is_success: true, loggable: $version);

            // Redirection avec un message de succès
            return back()->with('success', __("{$this->langPath}.activate.flash_success", ['name' => $version->active_translation?->name ?? 'Version inconnue']));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de l'activation de la version
            $this->logActivity(type: 'warning', action: 'backend.versions.activate', context: ['version_uuid' => $platformVersion->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.activate.warning_meta", ['uuid' => $platformVersion->uuid]), is_success: false);

            // Redirection avec le message métier explicite
            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de l'activation de la version
            $this->logActivity(type: 'error', action: 'backend.versions.activate', context: ['version_uuid' => $platformVersion->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.activate.error", ['uuid' => $platformVersion->uuid]), is_success: false);

            // Redirection avec un message d'erreur en cas de problème lors de l'activation de la version
            return back()->with('error', __("{$this->langPath}.activate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_versions_deactivate(PlatformVersion $platformVersion): RedirectResponse
    {
        try {

            // Version résolue par le binding implicite sur uuid (404 automatique si introuvable)
            $version = $platformVersion;

            // Désactivation de la version via le service
            $this->platformVersionService->backend_versions_deactivate($version);

            // Log de la désactivation de la version avec le code de la version désactivée
            $this->logActivity(type: 'info', action: 'backend.versions.deactivate', context: ['version_uuid' => $version->uuid, 'version_code' => $version->code], message: null, activity: __("{$this->langPath}.deactivate.success", ['name' => $version->active_translation?->name ?? 'Version inconnue']), is_success: true, loggable: $version);

            // Redirection avec un message de succès
            return back()->with('success', __("{$this->langPath}.deactivate.flash_success", ['name' => $version->active_translation?->name ?? 'Version inconnue']));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la désactivation de la version
            $this->logActivity(type: 'warning', action: 'backend.versions.deactivate', context: ['version_uuid' => $platformVersion->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.deactivate.warning_meta", ['uuid' => $platformVersion->uuid]), is_success: false);

            // Redirection avec le message métier explicite
            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la désactivation de la version
            $this->logActivity(type: 'error', action: 'backend.versions.deactivate', context: ['version_uuid' => $platformVersion->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.deactivate.error", ['uuid' => $platformVersion->uuid]), is_success: false);

            // Redirection avec un message d'erreur en cas de problème lors de la désactivation de la version
            return back()->with('error', __("{$this->langPath}.deactivate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // PUBLISH
    // ─────────────────────────────────────────────
    public function backend_versions_publish(PlatformVersion $platformVersion): RedirectResponse
    {
        try {

            // Version résolue par le binding implicite sur uuid (404 automatique si introuvable)
            $version = $platformVersion;

            // Publication de la version via le service
            $this->platformVersionService->backend_versions_publish($version);

            // Log de la publication de la version avec le code de la version publiée
            $this->logActivity(type: 'info', action: 'backend.versions.publish', context: ['version_uuid' => $version->uuid, 'version_code' => $version->code], message: null, activity: __("{$this->langPath}.publish.success", ['name' => $version->active_translation->name]), is_success: true, loggable: $version);

            // Redirection avec un message de succès
            return back()->with('success', __("{$this->langPath}.publish.flash_success", ['name' => $version->active_translation->name]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la publication de la version
            $this->logActivity(type: 'warning', action: 'backend.versions.publish', context: ['version_uuid' => $platformVersion->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.publish.warning_meta", ['uuid' => $platformVersion->uuid]), is_success: false);

            // Redirection avec le message métier explicite
            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la publication de la version
            $this->logActivity(type: 'error', action: 'backend.versions.publish', context: ['version_uuid' => $platformVersion->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.publish.error", ['uuid' => $platformVersion->uuid]), is_success: false);

            // Redirection avec un message d'erreur en cas de problème lors de la publication de la version
            return back()->with('error', __("{$this->langPath}.publish.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // UNPUBLISH
    // ─────────────────────────────────────────────
    public function backend_versions_unpublish(PlatformVersion $platformVersion): RedirectResponse
    {
        try {

            // Version résolue par le binding implicite sur uuid (404 automatique si introuvable)
            $version = $platformVersion;

            // Dépublication de la version via le service
            $this->platformVersionService->backend_versions_unpublish($version);

            // Log de la dépublication de la version avec le code de la version dépubliée
            $this->logActivity(type: 'info', action: 'backend.versions.unpublish', context: ['version_uuid' => $version->uuid, 'version_code' => $version->code], message: null, activity: __("{$this->langPath}.unpublish.success", ['code' => $version->code]), is_success: true, loggable: $version);

            // Redirection avec un message de succès
            return back()->with('success', __("{$this->langPath}.unpublish.flash_success", ['name' => $version->active_translation->name]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la dépublication de la version
            $this->logActivity(type: 'warning', action: 'backend.versions.unpublish', context: ['version_uuid' => $platformVersion->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.unpublish.warning", ['uuid' => $platformVersion->uuid]), is_success: false);

            // Redirection avec le message métier explicite
            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la dépublication de la version
            $this->logActivity(type: 'error', action: 'backend.versions.unpublish', context: ['version_uuid' => $platformVersion->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.unpublish.error", ['uuid' => $platformVersion->uuid]), is_success: false);

            // Redirection avec un message d'erreur en cas de problème lors de la dépublication de la version
            return back()->with('error', __("{$this->langPath}.unpublish.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_versions_destroy(PlatformVersion $platformVersion): RedirectResponse
    {
        try {

            // Version résolue par le binding implicite sur uuid (404 automatique si introuvable)
            $version = $platformVersion;

            // Récupère le nom de la version avant suppression (pour les logs et messages flash)
            $name = $version->active_translation?->name ?? $version->code;

            // Suppression de la version via le service (soft delete)
            $this->platformVersionService->backend_versions_destroy($version);

            // Log de la suppression de la version avec le uuid de la version supprimée
            $this->logActivity(type: 'info', action: 'backend.versions.destroy', context: ['version_uuid' => $version->uuid, 'version_code' => $version->code], message: null, activity: __("{$this->langPath}.destroy.success", ['name' => $name]), is_success: true, loggable: $version);

            // Redirection avec un message de succès
            return back()->with('success', __("{$this->langPath}.destroy.flash_success", ['name' => $name]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la suppression de la version
            $this->logActivity(type: 'warning', action: 'backend.versions.destroy', context: ['version_uuid' => $platformVersion->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.warning_meta", ['uuid' => $platformVersion->uuid]), is_success: false);

            // Redirection avec le message métier explicite
            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la suppression de la version
            $this->logActivity(type: 'error', action: 'backend.versions.destroy', context: ['version_uuid' => $platformVersion->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.error", ['uuid' => $platformVersion->uuid]), is_success: false);

            // Redirection avec un message d'erreur en cas de problème lors de la suppression de la version
            return back()->with('error', __("{$this->langPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_versions_trashed(): View
    {
        try {

            // Récupère toutes les versions supprimées (soft deleted) avec leurs traductions, triés par date de suppression décroissante
            $versions = PlatformVersion::onlyTrashed()->withTrashedActiveTranslation()->orderBy('deleted_at', 'desc')->get();

            // Log de la consultation de la corbeille des versions
            $this->logActivity(type: 'info', action: 'backend.versions.trashed', context: ['count' => $versions->count()], message: null, activity: __("{$this->langPath}.trashed.success"), is_success: true);

            return view('v1.backend.pages.platform-versions.trashed', compact('versions'));

        } catch (\Exception $e) {

            // Log de l'erreur lors de la consultation de la corbeille des versions
            $this->logActivity(type: 'error', action: 'backend.versions.trashed', context: ['exception' => get_class($e)], message: $e->getMessage(), activity: __("{$this->langPath}.trashed.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_versions_restore(string $uuid): RedirectResponse
    {
        try {

            // Récupère la version supprimée en fonction du uuid, ou échoue avec une 404 si elle n'existe pas
            $version = PlatformVersion::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Restauration de la version via le service
            $this->platformVersionService->backend_versions_restore($version);

            // Log de la restauration de la version avec le uuid de la version restaurée
            $this->logActivity(type: 'info', action: 'backend.versions.restore', context: ['version_uuid' => $version->uuid, 'version_code' => $version->code], message: null, activity: __("{$this->langPath}.restore.success", ['name' => $version->active_translation->name]), is_success: true, loggable: $version);

            return back()->with('success', __("{$this->langPath}.restore.flash_success", ['name' => $version->active_translation->name]));

        } catch (ModelNotFoundException $e) {

            // Log de la tentative de restauration d'une version introuvable
            $this->logActivity(type: 'warning', action: 'backend.versions.restore', context: ['version_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.restore.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la restauration de la version
            $this->logActivity(type: 'warning', action: 'backend.versions.restore', context: ['version_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la restauration de la version
            $this->logActivity(type: 'error', action: 'backend.versions.restore', context: ['version_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_versions_force_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupère la version supprimée en fonction du uuid, ou échoue avec une 404 si elle n'existe pas
            $version = PlatformVersion::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Suppression définitive de la version via le service
            $name = $version->active_translation->name ?? __("{$this->langPath}.force_destroy.unknown");

            $this->platformVersionService->backend_versions_force_destroy($version);

            // Log de la suppression définitive de la version avec le uuid de la version supprimée
            $this->logActivity(type: 'info', action: 'backend.versions.force_destroy', context: ['version_uuid' => $version->uuid, 'version_code' => $version->code], message: null, activity: __("{$this->langPath}.force_destroy.success", ['name' => $name]), is_success: true, loggable: $version);

            return back()->with('success', __("{$this->langPath}.force_destroy.flash_success", ['name' => $name]));

        } catch (ModelNotFoundException $e) {

            // Log de la tentative de suppression définitive d'une version introuvable
            $this->logActivity(type: 'warning', action: 'backend.versions.force_destroy', context: ['version_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.force_destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la suppression définitive de la version
            $this->logActivity(type: 'warning', action: 'backend.versions.force_destroy', context: ['version_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la suppression définitive de la version
            $this->logActivity(type: 'error', action: 'backend.versions.force_destroy', context: ['version_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.force_destroy.flash_error"));
        }
    }
}
