<?php

namespace App\Http\Controllers\V1;

use App\Enums\Clouds\Folders\CloudFolderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Clouds\Folders\UpdateCloudFolderRequest;
use App\Models\CloudFolder;
use App\Services\V1\Controllers\CloudFolderService;
use App\Traits\LogActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

use function __;

class CloudFolderController extends Controller
{
    use LogActivity;

    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/clouds/folders/messages/cloud-folders-messages-controller.cloud-folders';

    // Injection de dépendance du service de gestion des dossiers cloud
    public function __construct(protected CloudFolderService $cloudFolderService) {}

    // ═══════════════════════════════════════════════════════════════
    // BACKEND
    // ═══════════════════════════════════════════════════════════════

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_cloud_folders_index(): View
    {
        try {

            // Récupère tous les dossiers cloud avec leur plateforme et dossier parent, triés par date de création décroissante
            $folders = CloudFolder::withCount(['audits', 'activityLogs', 'files', 'children'])->with('platform', 'parent')->orderBy('created_at', 'desc')->get();

            // Log de l'activité
            $this->logActivity(type: 'info', action: 'backend.cloud-folders.index', context: ['count' => $folders->count()], message: null, activity: __("{$this->langPath}.index.success"), is_success: true);

            // Retourne la vue avec les dossiers
            return view('v1.backend.pages.clouds.folders.index', compact('folders'));

        } catch (\Exception $e) {

            // Log de l'erreur
            $this->logActivity(type: 'error', action: 'backend.cloud-folders.index', context: [], message: $e->getMessage(), activity: __("{$this->langPath}.index.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function backend_cloud_folders_show(CloudFolder $cloudFolder): View
    {
        try {

            // Dossier cloud résolu par binding implicite ({cloudFolder}) avec ses compteurs
            $folder = $cloudFolder->loadCount(['audits', 'activityLogs', 'files', 'children']);

            // Récupérer les sous-dossiers et les fichiers associés au dossier
            $subFolders = $folder->children()->withCount(['files', 'children'])->get();
            $files = $folder->files()->with(['fileable'])->get();

            // Récupère le nom du dossier pour les logs et les messages flash
            $folderName = $folder->folder_name;

            // Log de l'activité de visualisation réussie
            $this->logActivity(type: 'info', action: 'backend.cloud-folders.show', context: ['cloud_folder_uuid' => $folder->uuid, 'cloud_folder_name' => $folderName], message: null, activity: __("{$this->langPath}.show.success", ['name' => $folderName]), is_success: true, loggable: $folder);

            // Retourne la vue de détails avec le dossier
            return view('v1.backend.pages.clouds.folders.show', compact('folder', 'subFolders', 'files'));

        } catch (\Exception $e) {

            // Log de l'erreur de visualisation avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.cloud-folders.show', context: ['cloud_folder_uuid' => $cloudFolder->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.show.error", ['uuid' => $cloudFolder->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // FILES
    // ─────────────────────────────────────────────
    public function backend_cloud_folders_files(CloudFolder $cloudFolder): View
    {
        try {

            // Dossier cloud résolu par binding implicite ({cloudFolder}) avec ses compteurs
            $folder = $cloudFolder->loadCount(['audits', 'activityLogs', 'files', 'children']);

            $folderName = $folder->folder_name;

            // Récupère les fichiers du dossier avec leurs entités associées
            $files = $folder->files()->with(['fileable'])->orderBy('created_at', 'desc')->get();

            // Log de l'activité de visualisation réussie
            $this->logActivity(type: 'info', action: 'backend.cloud-folders.files', context: ['cloud_folder_uuid' => $folder->uuid, 'cloud_folder_name' => $folderName, 'count' => $files->count()], message: null, activity: __("{$this->langPath}.files.success", ['name' => $folderName]), is_success: true, loggable: $folder);

            // Retourne la vue des fichiers du dossier
            return view('v1.backend.pages.clouds.folders.others.files', compact('folder', 'files'));

        } catch (\Exception $e) {

            // Log de l'erreur de visualisation avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.cloud-folders.files', context: ['cloud_folder_uuid' => $cloudFolder->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.files.error", ['uuid' => $cloudFolder->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // SUBFOLDERS
    // ─────────────────────────────────────────────
    public function backend_cloud_folders_subfolders(CloudFolder $cloudFolder): View
    {
        try {

            // Dossier cloud résolu par binding implicite ({cloudFolder}) avec ses compteurs
            $folder = $cloudFolder->loadCount(['audits', 'activityLogs', 'files', 'children']);

            $folderName = $folder->folder_name;

            // Récupère les sous-dossiers
            $subFolders = $folder->children()->with(['platform', 'parent'])->orderBy('created_at', 'desc')->get();

            // Log de l'activité de visualisation réussie
            $this->logActivity(type: 'info', action: 'backend.cloud-folders.subfolders', context: ['cloud_folder_uuid' => $folder->uuid, 'cloud_folder_name' => $folderName, 'count' => $subFolders->count()], message: null, activity: __("{$this->langPath}.subfolders.success", ['name' => $folderName]), is_success: true, loggable: $folder);

            // Retourne la vue des sous-dossiers
            return view('v1.backend.pages.clouds.folders.others.subfolders', compact('folder', 'subFolders'));

        } catch (\Exception $e) {

            // Log de l'erreur de visualisation avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.cloud-folders.subfolders', context: ['cloud_folder_uuid' => $cloudFolder->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.subfolders.error", ['uuid' => $cloudFolder->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // AUDITS
    // ─────────────────────────────────────────────
    public function backend_cloud_folders_audits(CloudFolder $cloudFolder): View
    {
        try {

            // Dossier cloud résolu par binding implicite ({cloudFolder}) avec ses compteurs
            $folder = $cloudFolder->loadCount(['audits', 'activityLogs', 'files', 'children']);

            $audits = $folder->audits()->with(['user', 'auditable'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.cloud-folders.audits', context: ['cloud_folder_uuid' => $folder->uuid, 'cloud_folder_name' => $folder->folder_name], message: null, activity: __("{$this->langPath}.audits.success", ['name' => $folder->folder_name]), is_success: true, loggable: $folder);

            return view('v1.backend.pages.clouds.folders.others.audits', compact('folder', 'audits'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.cloud-folders.audits', context: ['cloud_folder_uuid' => $cloudFolder->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.audits.error", ['uuid' => $cloudFolder->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVITY LOGS
    // ─────────────────────────────────────────────
    public function backend_cloud_folders_activity_logs(CloudFolder $cloudFolder): View
    {
        try {

            // Dossier cloud résolu par binding implicite ({cloudFolder}) avec ses compteurs
            $folder = $cloudFolder->loadCount(['audits', 'activityLogs', 'files', 'children']);

            $logs = $folder->activityLogs()->with(['user'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.cloud-folders.activity-logs', context: ['cloud_folder_uuid' => $folder->uuid, 'cloud_folder_name' => $folder->folder_name], message: null, activity: __("{$this->langPath}.activity-logs.success", ['name' => $folder->folder_name]), is_success: true, loggable: $folder);

            return view('v1.backend.pages.clouds.folders.others.activity-logs', compact('folder', 'logs'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.cloud-folders.activity-logs', context: ['cloud_folder_uuid' => $cloudFolder->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.activity-logs.error", ['uuid' => $cloudFolder->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────
    public function backend_cloud_folders_edit(CloudFolder $cloudFolder): View
    {
        try {

            // Dossier cloud résolu par binding implicite ({cloudFolder})
            $folder = $cloudFolder;

            // Log de l'accès à la page d'édition réussie
            $this->logActivity(type: 'info', action: 'backend.cloud-folders.edit', context: ['cloud_folder_uuid' => $folder->uuid, 'cloud_folder_name' => $folder->folder_name], message: null, activity: __("{$this->langPath}.edit.success", ['name' => $folder->folder_name]), is_success: true, loggable: $folder);

            // Retourne la vue d'édition avec le dossier
            return view('v1.backend.pages.clouds.folders.edit', compact('folder'));

        } catch (\Exception $e) {

            // Log de l'erreur d'accès à la page d'édition avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.cloud-folders.edit', context: ['cloud_folder_uuid' => $cloudFolder->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.edit.error", ['uuid' => $cloudFolder->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_cloud_folders_update(UpdateCloudFolderRequest $request, CloudFolder $cloudFolder): RedirectResponse
    {
        try {

            // Dossier cloud résolu par binding implicite ({cloudFolder})
            $folder = $cloudFolder;

            // Utilise le service pour mettre à jour le dossier cloud avec les données validées
            $this->cloudFolderService->backend_cloud_folders_update($folder, $request->validated());

            $folder->refresh(); // Rafraîchit l'instance du modèle pour obtenir les dernières données après la mise à jour

            $folderName = $folder->folder_name; // Récupère le nom du dossier pour les logs et les messages flash

            // Log de l'activité de mise à jour réussie
            $this->logActivity(type: 'info', action: 'backend.cloud-folders.update', context: ['cloud_folder_uuid' => $folder->uuid, 'cloud_folder_name' => $folderName, 'changes' => $folder->getChanges()], message: null, activity: __("{$this->langPath}.update.success", ['name' => $folderName]), is_success: true, loggable: $folder);

            // Redirige vers la page de détails du dossier avec un message flash de succès
            return redirect()->route('backend.cloud-folders.show', ['cloudFolder' => $folder->uuid])->with('success', __("{$this->langPath}.update.flash_success", ['name' => $folderName]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur de mise à jour avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.cloud-folders.update', context: ['cloud_folder_uuid' => $cloudFolder->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.update.warning_meta", ['uuid' => $cloudFolder->uuid]), is_success: false);

            // Redirige en arrière avec un message d'erreur et les données du formulaire
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur de mise à jour avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.cloud-folders.update', context: ['cloud_folder_uuid' => $cloudFolder->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.update.error", ['uuid' => $cloudFolder->uuid]), is_success: false);

            // Redirige en arrière avec un message d'erreur et les données du formulaire
            return redirect()->back()->with('error', __("{$this->langPath}.update.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // TOGGLE ACTIVE (Activer/Désactiver)
    // ─────────────────────────────────────────────
    public function backend_cloud_folders_toggle_active(Request $request, CloudFolder $cloudFolder): RedirectResponse
    {
        try {

            // Dossier cloud résolu par binding implicite ({cloudFolder})
            $folder = $cloudFolder;

            // Stocke l'état actif actuel avant de le changer
            $wasActive = $folder->status === CloudFolderStatus::Active;

            // Utilise le service pour basculer le statut actif du dossier
            $this->cloudFolderService->backend_cloud_folders_toggle_active($folder);

            $folderName = $folder->folder_name; // Récupère le nom du dossier pour les logs et les messages flash

            // Log de l'activité de basculement de statut réussie
            if ($wasActive) {
                // Si le dossier était actif avant, il est maintenant désactivé
                $this->logActivity(type: 'info', action: 'backend.cloud-folders.toggle_active', context: ['cloud_folder_uuid' => $folder->uuid, 'cloud_folder_name' => $folderName], message: null, activity: __("{$this->langPath}.toggle_active.success_deactivated", ['name' => $folderName]), is_success: true, loggable: $folder);

                // Redirige vers la page précédente avec un message flash de succès pour la désactivation
                return redirect()->back()->with('success', __("{$this->langPath}.toggle_active.flash_success_deactivated", ['name' => $folderName]));

            } else {
                // Si le dossier était inactif avant, il est maintenant activé
                $this->logActivity(type: 'info', action: 'backend.cloud-folders.toggle_active', context: ['cloud_folder_uuid' => $folder->uuid, 'cloud_folder_name' => $folderName], message: null, activity: __("{$this->langPath}.toggle_active.success_activated", ['name' => $folderName]), is_success: true, loggable: $folder);

                // Redirige vers la page précédente avec un message flash de succès pour l'activation
                return redirect()->back()->with('success', __("{$this->langPath}.toggle_active.flash_success_activated", ['name' => $folderName]));
            }

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur de basculement de statut avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.cloud-folders.toggle_active', context: ['cloud_folder_uuid' => $cloudFolder->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.toggle_active.warning_meta", ['uuid' => $cloudFolder->uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'erreur métier
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur de basculement de statut avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.cloud-folders.toggle_active', context: ['cloud_folder_uuid' => $cloudFolder->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.toggle_active.error", ['uuid' => $cloudFolder->uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'échec du basculement
            return redirect()->back()->with('error', __("{$this->langPath}.toggle_active.flash_error"));
        }
    }
}
