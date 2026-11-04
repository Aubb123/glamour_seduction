<?php

namespace App\Http\Controllers\V1;

use App\Enums\Clouds\Platforms\CloudPlatformStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Clouds\Platforms\StoreCloudPlatformRequest;
use App\Http\Requests\V1\Clouds\Platforms\UpdateCloudPlatformRequest;
use App\Models\CloudPlatform;
use App\Services\V1\Controllers\CloudPlatformService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

use function __;

class CloudPlatformController extends Controller
{
    use LogActivity;

    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/clouds/platforms/messages/cloud-platforms-messages-controller.cloud-platforms';

    // Injection de dépendance du service de gestion des plateformes cloud
    public function __construct(protected CloudPlatformService $cloudPlatformService) {}

    // ═══════════════════════════════════════════════════════════════
    // BACKEND
    // ═══════════════════════════════════════════════════════════════

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_cloud_platforms_index(): View
    {
        try {

            // Récupère toutes les plateformes cloud, triées par date de création décroissante
            $platforms = CloudPlatform::withCount(['audits', 'activityLogs', 'folders', 'files'])->orderBy('created_at', 'desc')->get();

            // Log de l'activité
            $this->logActivity(type: 'info', action: 'backend.cloud-platforms.index', context: ['count' => $platforms->count()], message: null, activity: __("{$this->langPath}.index.success"), is_success: true);

            // Retourne la vue avec les plateformes cloud
            return view('v1.backend.pages.clouds.platforms.index', compact('platforms'));

        } catch (\Exception $e) {

            // Log de l'erreur
            $this->logActivity(type: 'error', action: 'backend.cloud-platforms.index', context: [], message: $e->getMessage(), activity: __("{$this->langPath}.index.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────
    public function backend_cloud_platforms_create(): View
    {
        try {

            // Log de l'accès à la page de création
            $this->logActivity(type: 'info', action: 'backend.cloud-platforms.create', context: [], message: null, activity: __("{$this->langPath}.create.success"), is_success: true);

            // Retourne la vue de création
            return view('v1.backend.pages.clouds.platforms.create');

        } catch (\Exception $e) {

            // Log de l'erreur lors de l'accès à la page de création
            $this->logActivity(type: 'error', action: 'backend.cloud-platforms.create', context: [], message: $e->getMessage(), activity: __("{$this->langPath}.create.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_cloud_platforms_store(StoreCloudPlatformRequest $request): RedirectResponse
    {
        try {

            // Utilise le service pour créer une nouvelle plateforme cloud avec les données validées
            $platform = $this->cloudPlatformService->backend_cloud_platforms_store($request->validated());

            // Récupère le label de la plateforme pour les logs et les messages flash
            $platformName = $platform->label;

            // Log de l'activité de création réussie
            $this->logActivity(type: 'info', action: 'backend.cloud-platforms.store', context: ['cloud_platform_uuid' => $platform->uuid, 'cloud_platform_name' => $platformName], message: null, activity: __("{$this->langPath}.store.success", ['name' => $platformName]), is_success: true, loggable: $platform);

            // Redirige vers la page de détails de la plateforme avec un message flash de succès
            return redirect()->route('backend.cloud-platforms.show', ['cloudPlatform' => $platform->uuid, 'slug' => $platform->slug])->with('success', __("{$this->langPath}.store.flash_success", ['name' => $platformName]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur de création avec un message d'avertissement
            $this->logActivity(type: 'warning', action: 'backend.cloud-platforms.store', context: ['data' => $this->sanitizeForLog($request->validated())], message: $e->getMessage(), activity: __("{$this->langPath}.store.error"), is_success: false);

            // Redirige en arrière avec un message d'erreur et les données du formulaire
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur de création avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.cloud-platforms.store', context: ['data' => $this->sanitizeForLog($request->validated())], message: $e->getMessage(), activity: __("{$this->langPath}.store.error"), is_success: false);

            // Redirige en arrière avec un message d'erreur et les données du formulaire
            return redirect()->back()->with('error', __("{$this->langPath}.store.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // Résolution + vérification slug (helper privé)
    // ─────────────────────────────────────────────
    private function platformSlugMatches(CloudPlatform $cloudPlatform, ?string $slug, string $action, string $langKey): bool
    {
        $expectedSlug = $cloudPlatform->slug ?? 'null';

        if ($expectedSlug === $slug) {
            return true;
        }

        // Log de l'incohérence de slug avec un message d'avertissement
        $this->logActivity(type: 'warning', action: $action, context: ['cloud_platform_uuid' => $cloudPlatform->uuid, 'cloud_platform_slug' => $slug, 'expected_slug' => $expectedSlug], message: null, activity: __("{$this->langPath}.{$langKey}.slug_mismatch"), is_success: false, loggable: $cloudPlatform);

        return false;
    }

    /**
     * Filtrer les champs sensibles avant toute journalisation afin d'éviter
     * toute fuite d'api_key / api_secret dans les ActivityLogs ou les logs applicatifs.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function sanitizeForLog(array $data): array
    {
        $sensitive = ['api_key', 'api_secret'];

        foreach ($sensitive as $key) {
            if (isset($data[$key])) {
                $data[$key] = '[FILTRÉ]';
            }
        }

        return $data;
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function backend_cloud_platforms_show(CloudPlatform $cloudPlatform, ?string $slug = null): View|RedirectResponse
    {
        try {

            // Plateforme cloud résolue par binding implicite ({cloudPlatform}), chargée avec ses compteurs et ses dossiers
            $platform = $cloudPlatform->loadCount(['audits', 'activityLogs', 'folders', 'files'])->load('folders');

            // Vérifie que le slug correspond
            if (! $this->platformSlugMatches($cloudPlatform, $slug, 'backend.cloud-platforms.show', 'show')) {
                return redirect()->route('backend.cloud-platforms.index')->with('error', __("{$this->langPath}.show.slug_mismatch"));
            }

            // Récupère le label de la plateforme pour les logs et les messages flash
            $platformName = $platform->label;

            // Log de l'activité de visualisation réussie
            $this->logActivity(type: 'info', action: 'backend.cloud-platforms.show', context: ['cloud_platform_uuid' => $platform->uuid, 'cloud_platform_name' => $platformName, 'cloud_platform_slug' => $platform->slug], message: null, activity: __("{$this->langPath}.show.success", ['name' => $platformName]), is_success: true, loggable: $platform);

            // Retourne la vue de détails avec la plateforme et ses dossiers
            return view('v1.backend.pages.clouds.platforms.show', compact('platform'));

        } catch (\Exception $e) {

            // Log de l'erreur de visualisation avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.cloud-platforms.show', context: ['cloud_platform_uuid' => $cloudPlatform->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.show.error", ['uuid' => $cloudPlatform->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // FOLDERS
    // ─────────────────────────────────────────────
    public function backend_cloud_platforms_folders(CloudPlatform $cloudPlatform, ?string $slug = null): View|RedirectResponse
    {
        try {

            // Plateforme cloud résolue par binding implicite ({cloudPlatform}) avec ses compteurs
            $platform = $cloudPlatform->loadCount(['audits', 'activityLogs', 'folders', 'files']);

            // Vérifie que le slug correspond
            if (! $this->platformSlugMatches($cloudPlatform, $slug, 'backend.cloud-platforms.folders', 'folders')) {
                return redirect()->route('backend.cloud-platforms.index')->with('error', __("{$this->langPath}.folders.slug_mismatch"));
            }

            $platformName = $platform->label;

            // Récupère les dossiers de la plateforme avec leurs compteurs
            $folders = $platform->folders()->withCount(['files', 'children'])->orderBy('created_at', 'desc')->get();

            // Log de l'activité de visualisation réussie
            $this->logActivity(type: 'info', action: 'backend.cloud-platforms.folders', context: ['cloud_platform_uuid' => $platform->uuid, 'cloud_platform_name' => $platformName, 'count' => $folders->count()], message: null, activity: __("{$this->langPath}.folders.success", ['name' => $platformName]), is_success: true, loggable: $platform);

            // Retourne la vue des dossiers de la plateforme
            return view('v1.backend.pages.clouds.platforms.others.folders', compact('platform', 'folders'));

        } catch (\Exception $e) {

            // Log de l'erreur de visualisation avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.cloud-platforms.folders', context: ['cloud_platform_uuid' => $cloudPlatform->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.folders.error", ['uuid' => $cloudPlatform->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // FILES
    // ─────────────────────────────────────────────
    public function backend_cloud_platforms_files(CloudPlatform $cloudPlatform, ?string $slug = null): View|RedirectResponse
    {
        try {

            // Plateforme cloud résolue par binding implicite ({cloudPlatform}) avec ses compteurs
            $platform = $cloudPlatform->loadCount(['audits', 'activityLogs', 'folders', 'files']);

            // Vérifie que le slug correspond
            if (! $this->platformSlugMatches($cloudPlatform, $slug, 'backend.cloud-platforms.files', 'files')) {
                return redirect()->route('backend.cloud-platforms.index')->with('error', __("{$this->langPath}.files.slug_mismatch"));
            }

            $platformName = $platform->label;

            // Récupère les fichiers de la plateforme via les dossiers avec leurs entités associées
            $files = $platform->files()->with(['cloudFolder', 'fileable'])->orderBy('created_at', 'desc')->get();

            // Log de l'activité de visualisation réussie
            $this->logActivity(type: 'info', action: 'backend.cloud-platforms.files', context: ['cloud_platform_uuid' => $platform->uuid, 'cloud_platform_name' => $platformName, 'count' => $files->count()], message: null, activity: __("{$this->langPath}.files.success", ['name' => $platformName]), is_success: true, loggable: $platform);

            // Retourne la vue des fichiers de la plateforme
            return view('v1.backend.pages.clouds.platforms.others.files', compact('platform', 'files'));

        } catch (\Exception $e) {

            // Log de l'erreur de visualisation avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.cloud-platforms.files', context: ['cloud_platform_uuid' => $cloudPlatform->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.files.error", ['uuid' => $cloudPlatform->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // AUDITS
    // ─────────────────────────────────────────────
    public function backend_cloud_platforms_audits(CloudPlatform $cloudPlatform, ?string $slug = null): View|RedirectResponse
    {
        try {

            // Plateforme cloud résolue par binding implicite ({cloudPlatform}) avec ses compteurs
            $platform = $cloudPlatform->loadCount(['audits', 'activityLogs', 'folders', 'files']);

            // Vérifie que le slug correspond
            if (! $this->platformSlugMatches($cloudPlatform, $slug, 'backend.cloud-platforms.audits', 'audits')) {
                return redirect()->route('backend.cloud-platforms.index')->with('error', __("{$this->langPath}.audits.slug_mismatch"));
            }

            $audits = $platform->audits()->with(['user', 'auditable'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.cloud-platforms.audits', context: ['cloud_platform_uuid' => $platform->uuid, 'cloud_platform_name' => $platform->label], message: null, activity: __("{$this->langPath}.audits.success", ['name' => $platform->label]), is_success: true, loggable: $platform);

            return view('v1.backend.pages.clouds.platforms.others.audits', compact('platform', 'audits'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.cloud-platforms.audits', context: ['cloud_platform_uuid' => $cloudPlatform->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.audits.error", ['uuid' => $cloudPlatform->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVITY LOGS
    // ─────────────────────────────────────────────
    public function backend_cloud_platforms_activity_logs(CloudPlatform $cloudPlatform, ?string $slug = null): View|RedirectResponse
    {
        try {

            // Plateforme cloud résolue par binding implicite ({cloudPlatform}) avec ses compteurs
            $platform = $cloudPlatform->loadCount(['audits', 'activityLogs', 'folders', 'files']);

            // Vérifie que le slug correspond
            if (! $this->platformSlugMatches($cloudPlatform, $slug, 'backend.cloud-platforms.activity-logs', 'activity-logs')) {
                return redirect()->route('backend.cloud-platforms.index')->with('error', __("{$this->langPath}.activity-logs.slug_mismatch"));
            }

            $logs = $platform->activityLogs()->with(['user'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.cloud-platforms.activity-logs', context: ['cloud_platform_uuid' => $platform->uuid, 'cloud_platform_name' => $platform->label], message: null, activity: __("{$this->langPath}.activity-logs.success", ['name' => $platform->label]), is_success: true, loggable: $platform);

            return view('v1.backend.pages.clouds.platforms.others.activity-logs', compact('platform', 'logs'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.cloud-platforms.activity-logs', context: ['cloud_platform_uuid' => $cloudPlatform->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.activity-logs.error", ['uuid' => $cloudPlatform->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────
    public function backend_cloud_platforms_edit(CloudPlatform $cloudPlatform): View
    {
        try {

            // Plateforme cloud résolue par binding implicite ({cloudPlatform})
            $platform = $cloudPlatform;

            // Log de l'accès à la page d'édition réussie
            $this->logActivity(type: 'info', action: 'backend.cloud-platforms.edit', context: ['cloud_platform_uuid' => $platform->uuid, 'cloud_platform_name' => $platform->label], message: null, activity: __("{$this->langPath}.edit.success", ['name' => $platform->label]), is_success: true, loggable: $platform);

            // Retourne la vue d'édition avec la plateforme
            return view('v1.backend.pages.clouds.platforms.edit', compact('platform'));

        } catch (\Exception $e) {

            // Log de l'erreur d'accès à la page d'édition avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.cloud-platforms.edit', context: ['cloud_platform_uuid' => $cloudPlatform->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.edit.error", ['uuid' => $cloudPlatform->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_cloud_platforms_update(UpdateCloudPlatformRequest $request, CloudPlatform $cloudPlatform): RedirectResponse
    {
        try {

            // Plateforme cloud résolue par binding implicite ({cloudPlatform})
            $platform = $cloudPlatform;

            // Utilise le service pour mettre à jour la plateforme cloud avec les données validées
            $this->cloudPlatformService->backend_cloud_platforms_update($platform, $request->validated());

            $platform->refresh(); // Rafraîchit l'instance du modèle pour obtenir les dernières données après la mise à jour

            $platformName = $platform->label; // Récupère le label de la plateforme pour les logs et les messages flash

            // Log de l'activité de mise à jour réussie
            $this->logActivity(type: 'info', action: 'backend.cloud-platforms.update', context: ['cloud_platform_uuid' => $platform->uuid, 'cloud_platform_name' => $platformName, 'changes' => $this->sanitizeForLog($platform->getChanges())], message: null, activity: __("{$this->langPath}.update.success", ['name' => $platformName]), is_success: true, loggable: $platform);

            // Redirige vers la page de détails de la plateforme avec un message flash de succès
            return redirect()->route('backend.cloud-platforms.show', ['cloudPlatform' => $platform->uuid, 'slug' => $platform->slug])->with('success', __("{$this->langPath}.update.flash_success", ['name' => $platformName]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur de mise à jour avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.cloud-platforms.update', context: ['cloud_platform_uuid' => $cloudPlatform->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.update.warning_meta", ['uuid' => $cloudPlatform->uuid]), is_success: false);

            // Redirige en arrière avec un message d'erreur et les données du formulaire
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur de mise à jour avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.cloud-platforms.update', context: ['cloud_platform_uuid' => $cloudPlatform->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.update.error", ['uuid' => $cloudPlatform->uuid]), is_success: false);

            // Redirige en arrière avec un message d'erreur et les données du formulaire
            return redirect()->back()->with('error', __("{$this->langPath}.update.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // TOGGLE ACTIVE (Activer/Désactiver)
    // ─────────────────────────────────────────────
    public function backend_cloud_platforms_toggle_active(Request $request, CloudPlatform $cloudPlatform): RedirectResponse
    {
        try {

            // Plateforme cloud résolue par binding implicite ({cloudPlatform})
            $platform = $cloudPlatform;

            // Stocke l'état actif actuel avant de le changer
            $wasActive = $platform->status === CloudPlatformStatus::Active;

            // Utilise le service pour basculer le statut actif de la plateforme
            $this->cloudPlatformService->backend_cloud_platforms_toggle_active($platform);

            $platformName = $platform->label; // Récupère le label de la plateforme pour les logs et les messages flash

            // Log de l'activité de basculement de statut réussie
            if ($wasActive) {
                // Si la plateforme était active avant, elle est maintenant désactivée
                $this->logActivity(type: 'info', action: 'backend.cloud-platforms.toggle_active', context: ['cloud_platform_uuid' => $platform->uuid, 'cloud_platform_name' => $platformName], message: null, activity: __("{$this->langPath}.toggle_active.success_deactivated", ['name' => $platformName]), is_success: true, loggable: $platform);

                // Redirige vers la page précédente avec un message flash de succès pour la désactivation
                return redirect()->back()->with('success', __("{$this->langPath}.toggle_active.flash_success_deactivated", ['name' => $platformName]));

            } else {
                // Si la plateforme était inactive avant, elle est maintenant activée
                $this->logActivity(type: 'info', action: 'backend.cloud-platforms.toggle_active', context: ['cloud_platform_uuid' => $platform->uuid, 'cloud_platform_name' => $platformName], message: null, activity: __("{$this->langPath}.toggle_active.success_activated", ['name' => $platformName]), is_success: true, loggable: $platform);

                // Redirige vers la page précédente avec un message flash de succès pour l'activation
                return redirect()->back()->with('success', __("{$this->langPath}.toggle_active.flash_success_activated", ['name' => $platformName]));
            }

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur de basculement de statut avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.cloud-platforms.toggle_active', context: ['cloud_platform_uuid' => $cloudPlatform->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.toggle_active.warning_meta", ['uuid' => $cloudPlatform->uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'erreur métier
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur de basculement de statut avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.cloud-platforms.toggle_active', context: ['cloud_platform_uuid' => $cloudPlatform->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.toggle_active.error", ['uuid' => $cloudPlatform->uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'échec du basculement
            return redirect()->back()->with('error', __("{$this->langPath}.toggle_active.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_cloud_platforms_destroy(CloudPlatform $cloudPlatform): RedirectResponse
    {
        try {

            // Plateforme cloud résolue par binding implicite ({cloudPlatform})
            $platform = $cloudPlatform;

            // Récupère le label de la plateforme pour les logs et les messages flash
            $platformName = $platform->label;

            // Utilise le service pour supprimer la plateforme
            $this->cloudPlatformService->backend_cloud_platforms_destroy($platform);

            // Log de l'activité de suppression réussie
            $this->logActivity(type: 'info', action: 'backend.cloud-platforms.destroy', context: ['cloud_platform_uuid' => $platform->uuid, 'cloud_platform_name' => $platformName], message: null, activity: __("{$this->langPath}.destroy.success", ['name' => $platformName]), is_success: true, loggable: $platform);

            // Redirige vers la liste des plateformes avec un message flash de succès
            return redirect()->route('backend.cloud-platforms.index')->with('success', __("{$this->langPath}.destroy.flash_success", ['name' => $platformName]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur de suppression avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.cloud-platforms.destroy', context: ['cloud_platform_uuid' => $cloudPlatform->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.warning_meta", ['uuid' => $cloudPlatform->uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'erreur métier
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur de suppression avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.cloud-platforms.destroy', context: ['cloud_platform_uuid' => $cloudPlatform->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.error", ['uuid' => $cloudPlatform->uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'échec de la suppression
            return redirect()->back()->with('error', __("{$this->langPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_cloud_platforms_trashed(): View
    {
        try {

            // Récupère toutes les plateformes cloud supprimées, triées par date de suppression décroissante
            $platforms = CloudPlatform::onlyTrashed()->withCount(['audits', 'activityLogs', 'folders', 'files'])->orderBy('deleted_at', 'desc')->get();

            // Log de l'activité d'affichage de la liste des plateformes supprimées réussie
            $this->logActivity(type: 'info', action: 'backend.cloud-platforms.trashed', context: ['count' => $platforms->count()], message: null, activity: __("{$this->langPath}.trashed.success"), is_success: true);

            // Retourne la vue avec les plateformes supprimées
            return view('v1.backend.pages.clouds.platforms.trashed', compact('platforms'));

        } catch (\Exception $e) {

            // Log de l'erreur lors de l'affichage de la liste des plateformes supprimées
            $this->logActivity(type: 'error', action: 'backend.cloud-platforms.trashed', context: [], message: $e->getMessage(), activity: __("{$this->langPath}.trashed.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_cloud_platforms_restore(string $uuid): RedirectResponse
    {
        try {

            // Récupère la plateforme cloud supprimée par UUID ou échoue si non trouvée
            $platform = CloudPlatform::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Utilise le service pour restaurer la plateforme
            $this->cloudPlatformService->backend_cloud_platforms_restore($platform);

            $platformName = $platform->label; // Récupère le label de la plateforme pour les logs et les messages flash

            // Log de l'activité de restauration réussie
            $this->logActivity(type: 'info', action: 'backend.cloud-platforms.restore', context: ['cloud_platform_uuid' => $platform->uuid, 'cloud_platform_name' => $platformName], message: null, activity: __("{$this->langPath}.restore.success", ['name' => $platformName]), is_success: true, loggable: $platform);

            // Redirige vers la page précédente avec un message flash de succès pour la restauration
            return redirect()->back()->with('success', __("{$this->langPath}.restore.flash_success", ['name' => $platformName]));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur lors de la tentative de restauration d'une plateforme non trouvée
            $this->logActivity(type: 'warning', action: 'backend.cloud-platforms.restore', context: ['cloud_platform_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur lors de la tentative de restauration avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.cloud-platforms.restore', context: ['cloud_platform_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'erreur métier
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur lors de la tentative de restauration avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.cloud-platforms.restore', context: ['cloud_platform_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->langPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_cloud_platforms_force_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupère la plateforme cloud supprimée par UUID ou échoue si non trouvée
            $platform = CloudPlatform::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Utilise le service pour supprimer définitivement la plateforme
            $this->cloudPlatformService->backend_cloud_platforms_force_destroy($platform);

            // Log de l'activité de suppression définitive réussie
            $this->logActivity(type: 'info', action: 'backend.cloud-platforms.force_destroy', context: ['cloud_platform_uuid' => $platform->uuid], message: null, activity: __("{$this->langPath}.force_destroy.success"), is_success: true, loggable: $platform);

            // Redirige vers la page précédente avec un message flash de succès pour la suppression définitive
            return redirect()->back()->with('success', __("{$this->langPath}.force_destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur lors de la tentative de suppression définitive d'une plateforme non trouvée
            $this->logActivity(type: 'warning', action: 'backend.cloud-platforms.force_destroy', context: ['cloud_platform_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur lors de la tentative de suppression définitive avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.cloud-platforms.force_destroy', context: ['cloud_platform_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'erreur métier
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur lors de la tentative de suppression définitive avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.cloud-platforms.force_destroy', context: ['cloud_platform_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'échec de la suppression définitive
            return redirect()->back()->with('error', __("{$this->langPath}.force_destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // ARCHITECTURE STORE
    // ─────────────────────────────────────────────
    public function backend_cloud_platforms_architecture_store(CloudPlatform $cloudPlatform): RedirectResponse
    {
        try {

            // Plateforme cloud résolue par binding implicite ({cloudPlatform})
            $platform = $cloudPlatform;

            $this->cloudPlatformService->backend_cloud_platforms_architecture_store($platform);

            $this->logActivity(type: 'info', action: 'backend.cloud-platforms.architecture_store', context: ['cloud_platform_uuid' => $platform->uuid, 'cloud_platform_name' => $platform->label], message: null, activity: __("{$this->langPath}.architecture_store.success", ['name' => $platform->label]), is_success: true, loggable: $platform);

            return redirect()->route('backend.cloud-platforms.show', ['cloudPlatform' => $platform->uuid, 'slug' => $platform->slug])->with('success', __("{$this->langPath}.architecture_store.flash_success", ['name' => $platform->label]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.cloud-platforms.architecture_store', context: ['cloud_platform_uuid' => $cloudPlatform->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.architecture_store.warning_meta", ['uuid' => $cloudPlatform->uuid]), is_success: false);

            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.cloud-platforms.architecture_store', context: ['cloud_platform_uuid' => $cloudPlatform->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.architecture_store.error", ['uuid' => $cloudPlatform->uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->langPath}.architecture_store.flash_error"));
        }
    }
}
