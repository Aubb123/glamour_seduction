<?php

namespace App\Http\Controllers\V1;

use App\Enums\Clouds\Files\CloudFileStatus;
use App\Http\Controllers\Controller;
use App\Models\CloudFile;
use App\Services\Cloud\CloudStorageService;
use App\Services\V1\Controllers\CloudFileService;
use App\Traits\LogActivity;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

use function __;

class CloudFileController extends Controller
{
    use LogActivity;

    protected string $backendLangPath = 'v1/backend/views/pages/clouds/files/messages/cloud-files-messages-controller.cloud-files';

    public function __construct(
        protected CloudStorageService $cloudStorage,
        protected CloudFileService $cloudFileService,
    ) {}

    // ═══════════════════════════════════════════════════════════════
    // BACKEND
    // ═══════════════════════════════════════════════════════════════

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_cloud_files_index(): View
    {
        try {

            // Récupère tous les fichiers cloud avec leur dossier, plateforme et entité associée, triés par date de création décroissante
            // (eager-loading pour éviter les requêtes N+1 sur la liste)
            $files = CloudFile::with(['cloudFolder.platform', 'fileable'])
                ->withCount(['audits', 'activityLogs'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Log de l'accès à la liste des fichiers cloud
            $this->logActivity(type: 'info', action: 'backend.cloud-files.index', context: ['count' => $files->count()], message: null, activity: __("{$this->backendLangPath}.index.success"), is_success: true);

            // Affiche la vue avec les fichiers cloud récupérés
            return view('v1.backend.pages.clouds.files.index', compact('files'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur avec le message d'exception
            $this->logActivity(type: 'error', action: 'backend.cloud-files.index', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.index.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function backend_cloud_files_show(CloudFile $cloudFile): View
    {
        try {

            // Fichier cloud résolu par binding implicite ({cloudFile}) avec ses compteurs
            $file = $cloudFile->loadCount(['audits', 'activityLogs']);

            // Log de la consultation du détail d'un fichier cloud
            $this->logActivity(type: 'info', action: 'backend.cloud-files.show', context: ['cloud_file_uuid' => $file->uuid, 'cloud_file_name' => $file->file_name], message: null, activity: __("{$this->backendLangPath}.show.success", ['name' => $file->file_name]), is_success: true, loggable: $file);

            // Retourne la vue de détail du fichier cloud
            return view('v1.backend.pages.clouds.files.show', compact('file'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la consultation du détail d'un fichier cloud
            $this->logActivity(type: 'error', action: 'backend.cloud-files.show', context: ['cloud_file_uuid' => $cloudFile->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.show.error", ['uuid' => $cloudFile->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // AUDITS
    // ─────────────────────────────────────────────
    public function backend_cloud_files_audits(CloudFile $cloudFile): View
    {
        try {

            // Fichier cloud résolu par binding implicite ({cloudFile}) avec ses compteurs
            $file = $cloudFile->loadCount(['audits', 'activityLogs']);

            $audits = $file->audits()->with(['user', 'auditable'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.cloud-files.audits', context: ['cloud_file_uuid' => $file->uuid, 'cloud_file_name' => $file->file_name], message: null, activity: __("{$this->backendLangPath}.audits.success", ['name' => $file->file_name]), is_success: true, loggable: $file);

            return view('v1.backend.pages.clouds.files.others.audits', compact('file', 'audits'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.cloud-files.audits', context: ['cloud_file_uuid' => $cloudFile->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.audits.error", ['uuid' => $cloudFile->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVITY LOGS
    // ─────────────────────────────────────────────
    public function backend_cloud_files_activity_logs(CloudFile $cloudFile): View
    {
        try {

            // Fichier cloud résolu par binding implicite ({cloudFile}) avec ses compteurs
            $file = $cloudFile->loadCount(['audits', 'activityLogs']);

            $logs = $file->activityLogs()->with(['user'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.cloud-files.activity-logs', context: ['cloud_file_uuid' => $file->uuid, 'cloud_file_name' => $file->file_name], message: null, activity: __("{$this->backendLangPath}.activity-logs.success", ['name' => $file->file_name]), is_success: true, loggable: $file);

            return view('v1.backend.pages.clouds.files.others.activity-logs', compact('file', 'logs'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.cloud-files.activity-logs', context: ['cloud_file_uuid' => $cloudFile->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activity-logs.error", ['uuid' => $cloudFile->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // SYNC INDEX (fichiers non synchronisés)
    // ─────────────────────────────────────────────
    public function backend_cloud_files_sync_index(): View
    {
        try {

            // Récupère les fichiers non synchronisés (statut != synced) avec leur dossier, plateforme et entité associée
            // (eager-loading pour éviter les requêtes N+1 sur la liste)
            $files = CloudFile::with(['cloudFolder.platform', 'fileable'])
                ->where('sync_status', '!=', CloudFileStatus::Synced)
                ->orderBy('created_at', 'desc')
                ->get();

            // Log de l'accès à la page de synchronisation
            $this->logActivity(type: 'info', action: 'backend.cloud-files.sync', context: ['count' => $files->count()], message: null, activity: __("{$this->backendLangPath}.sync.success"), is_success: true);

            // Affiche la vue de synchronisation avec les fichiers non synchronisés
            return view('v1.backend.pages.clouds.files.sync', compact('files'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur
            $this->logActivity(type: 'error', action: 'backend.cloud-files.sync', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.sync.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // SYNC SELECTED (synchroniser la sélection cochée)
    // ─────────────────────────────────────────────
    public function backend_cloud_files_sync_selected(Request $request): RedirectResponse
    {
        try {

            // Récupère les UUID cochés par l'utilisateur
            $fileUuids = $request->input('file_uuids', []);

            if (empty($fileUuids)) {
                return redirect()->route('backend.cloud-files.sync')->with('warning', __("{$this->backendLangPath}.sync.no_selection"));
            }

            // Appelle le service pour synchroniser les fichiers sélectionnés
            $result = $this->cloudFileService->backend_cloud_files_sync_selected($fileUuids);

            // Log de la synchronisation
            $this->logActivity(type: 'info', action: 'backend.cloud-files.sync-selected', context: ['selected' => count($fileUuids), 'success' => $result['success_count'], 'failed' => $result['failed_count']], message: null, activity: __("{$this->backendLangPath}.sync.flash_success", ['success' => $result['success_count'], 'failed' => $result['failed_count']]), is_success: $result['failed_count'] === 0);

            return redirect()->route('backend.cloud-files.sync')->with('success', __("{$this->backendLangPath}.sync.flash_success", ['success' => $result['success_count'], 'failed' => $result['failed_count']]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'error', action: 'backend.cloud-files.sync-selected', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.sync.flash_invalid_argument"), is_success: false);

            return redirect()->route('backend.cloud-files.sync')->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.cloud-files.sync-selected', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.sync.flash_error"), is_success: false);

            return redirect()->route('backend.cloud-files.sync')->with('error', __("{$this->backendLangPath}.sync.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_cloud_files_destroy(CloudFile $cloudFile): RedirectResponse
    {
        try {

            // Fichier cloud résolu par binding implicite ({cloudFile})
            $file = $cloudFile;

            $fileName = $file->file_name;

            $this->cloudFileService->backend_cloud_files_destroy($file);

            // Log de la suppression du fichier cloud
            $this->logActivity(type: 'info', action: 'backend.cloud-files.destroy', context: ['cloud_file_uuid' => $file->uuid, 'cloud_file_name' => $fileName], message: null, activity: __("{$this->backendLangPath}.destroy.success", ['name' => $fileName]), is_success: true, loggable: $file);

            // Redirection vers la liste des fichiers cloud avec un message de succès
            return redirect()->route('backend.cloud-files.index')->with('success', __("{$this->backendLangPath}.destroy.flash_success", ['name' => $fileName]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur spécifique (par exemple, cloud_platform_uuid manquant)
            $this->logActivity(type: 'error', action: 'backend.cloud-files.destroy', context: ['cloud_file_uuid' => $cloudFile->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.invalid_argument", ['uuid' => $cloudFile->uuid]), is_success: false);

            // Redirection vers la liste des fichiers cloud avec un message d'erreur
            return redirect()->route('backend.cloud-files.index')->with('error', $e->getMessage());

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la suppression du fichier cloud
            $this->logActivity(type: 'error', action: 'backend.cloud-files.destroy', context: ['cloud_file_uuid' => $cloudFile->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.error", ['uuid' => $cloudFile->uuid]), is_success: false);

            // Redirection vers la liste des fichiers cloud avec un message d'erreur
            return redirect()->route('backend.cloud-files.index')->with('error', __("{$this->backendLangPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_cloud_files_trashed(): View
    {
        try {

            // Récupère tous les fichiers cloud supprimés (soft deleted) avec leur dossier, plateforme et entité associée
            $files = CloudFile::onlyTrashed()->withCount(['audits', 'activityLogs'])->orderBy('deleted_at', 'desc')->get();

            // Log de l'activité d'affichage de la liste des fichiers supprimés réussie
            $this->logActivity(type: 'info', action: 'backend.cloud-files.trashed', context: ['count' => $files->count()], message: null, activity: __("{$this->backendLangPath}.trashed.success"), is_success: true);

            // Retourne la vue avec les fichiers supprimés
            return view('v1.backend.pages.clouds.files.trashed', compact('files'));

        } catch (\Exception $e) {

            // Log de l'erreur lors de l'affichage de la liste des fichiers supprimés
            $this->logActivity(type: 'error', action: 'backend.cloud-files.trashed', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.trashed.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_cloud_files_restore(string $uuid): RedirectResponse
    {
        try {

            // Récupère le fichier cloud supprimé par UUID ou échoue si non trouvé
            $file = CloudFile::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Utilise le service pour restaurer le fichier cloud
            $this->cloudFileService->backend_cloud_files_restore($file);

            $fileName = $file->file_name;  // Récupère le titre du fichier pour les logs et les messages flash

            // Log de l'activité de restauration réussie
            $this->logActivity(type: 'info', action: 'backend.cloud-files.restore', context: ['cloud_file_uuid' => $file->uuid, 'file_name' => $fileName], message: null, activity: __("{$this->backendLangPath}.restore.success", ['name' => $fileName]), is_success: true, loggable: $file);

            // Redirige vers la page précédente avec un message flash de succès pour la restauration
            return redirect()->back()->with('success', __("{$this->backendLangPath}.restore.flash_success", ['name' => $fileName]));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur lors de la tentative de restauration d'un fichier non trouvé
            $this->logActivity(type: 'warning', action: 'backend.cloud-files.restore', context: ['cloud_file_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur lors de la tentative de restauration avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.cloud-files.restore', context: ['cloud_file_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'erreur métier
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            //  Log de l'erreur lors de la tentative de restauration avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.cloud-files.restore', context: ['cloud_file_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_cloud_files_force_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupère le fichier supprimé par UUID ou échoue si non trouvé
            $file = CloudFile::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Utilise le service pour supprimer définitivement le fichier
            $this->cloudFileService->backend_cloud_files_force_destroy($file);

            // Récupère le nom du fichier pour les logs et les messages flash
            $this->logActivity(type: 'info', action: 'backend.cloud-files.force_destroy', context: ['cloud_file_uuid' => $file->uuid], message: null, activity: __("{$this->backendLangPath}.force_destroy.success"), is_success: true, loggable: $file);

            // Redirige vers la page précédente avec un message flash de succès pour la suppression définitive
            return redirect()->back()->with('success', __("{$this->backendLangPath}.force_destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur lors de la tentative de suppression définitive d'un article non trouvé
            $this->logActivity(type: 'warning', action: 'backend.cloud-files.force_destroy', context: ['cloud_file_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur lors de la tentative de suppression définitive avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.cloud-files.force_destroy', context: ['cloud_file_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'erreur métier
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur lors de la tentative de suppression définitive avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.cloud-files.force_destroy', context: ['cloud_file_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'échec de la suppression définitive
            return redirect()->back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_error"));
        }
    }

    /**
     * Sert un fichier cloud sans révéler l'URL réelle.
     *
     * Le contenu est transmis en streaming (readStream + StreamedResponse) pour ne
     * jamais charger le fichier entier en mémoire (critique pour les vidéos volumineuses).
     */
    public function show(string $uuid): StreamedResponse
    {
        try {

            // Récupère le fichier cloud par UUID (y compris les fichiers soft-deleted), sinon 404
            $cloudFile = CloudFile::withTrashed()->where('uuid', $uuid)->firstOrFail();

            $headers = [
                'Content-Type' => $cloudFile->mime_type ?: 'application/octet-stream',
                'Content-Disposition' => 'inline',
                'Cache-Control' => 'private, max-age=3600',
            ];

            // Si le fichier n'est pas encore migré vers B2, le servir depuis le stockage local
            if ($cloudFile->sync_status !== CloudFileStatus::Synced && $cloudFile->local_path) {
                $disk = Storage::disk('local_uploads');

                // Vérifie si le fichier existe dans le stockage local
                if (! $disk->exists($cloudFile->local_path)) {
                    abort(404);
                }

                $this->logActivity(type: 'info', action: 'backend.cloud-files.show', context: ['uuid' => $uuid], message: null, activity: __("{$this->backendLangPath}.show.success", ['name' => $cloudFile->file_name]), is_success: true, loggable: $cloudFile);

                return $this->streamFile($disk, $cloudFile->local_path, $headers);
            }

            // Fichier migré : on le sert depuis le disque cloud via remote_path (pas d'URL signée)
            $disk = Storage::disk($cloudFile->cloudFolder->platform->disk_name);
            $remotePath = $cloudFile->remote_path ?: $cloudFile->file_name;

            // Vérifie si le fichier existe sur le disque cloud
            if (! $disk->exists($remotePath)) {
                abort(404);
            }

            $this->logActivity(type: 'info', action: 'backend.cloud-files.show', context: ['uuid' => $uuid], message: null, activity: __("{$this->backendLangPath}.show.success", ['name' => $cloudFile->file_name]), is_success: true, loggable: $cloudFile);

            return $this->streamFile($disk, $remotePath, $headers);

        } catch (ModelNotFoundException $e) {

            // Log de l'activité
            $this->logActivity(type: 'error', action: 'backend.cloud-files.show', context: ['uuid' => $uuid], message: null, activity: __("{$this->backendLangPath}.show.not_found", ['uuid' => $uuid]), is_success: false);

            abort(404);

        } catch (FileNotFoundException $e) {

            // Fichier introuvable sur le stockage (disque configuré avec 'throw' => true) → 404
            $this->logActivity(type: 'error', action: 'backend.cloud-files.show', context: ['uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.show.not_found", ['uuid' => $uuid]), is_success: false);

            abort(404);

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.cloud-files.show', context: ['uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.show.proxy_error", ['message' => $e->getMessage()]), is_success: false);

            abort(500);
        }
    }

    /**
     * Transmet un fichier en streaming depuis le disque fourni (lecture par flux, sans chargement en mémoire).
     */
    private function streamFile(Filesystem $disk, string $path, array $headers): StreamedResponse
    {
        $stream = $disk->readStream($path);

        if ($stream === false) {
            abort(404);
        }

        // Retourne une réponse en streaming avec les en-têtes appropriés
        return new StreamedResponse(function () use ($stream) {
            fpassthru($stream);

            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, $headers);
    }
}
