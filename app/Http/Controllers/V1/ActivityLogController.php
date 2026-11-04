<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use App\Services\V1\Controllers\ActivityLogService;
use App\Traits\LogActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

use function __;

class ActivityLogController extends Controller
{
    use LogActivity;

    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/activity-logs/messages/activity-logs-messages-controller.activity_logs';

    // Injection de dépendance du service de gestion des logs d'activité
    public function __construct(protected ActivityLogService $activityLogService) {}

    // ═══════════════════════════════════════════════════════════════
    // BACKEND
    // ═══════════════════════════════════════════════════════════════

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_activity_logs_index(Request $request): View
    {
        try {
            // Résout l'UUID utilisateur en ID numérique si fourni
            $userUuid = $request->input('user_uuid');
            $userId = $userUuid ? User::where('uuid', $userUuid)->value('id') : null;

            // Récupère les filtres de la requête
            $filters = [
                'type' => $request->input('type'),
                'action' => $request->input('action'),
                'is_success' => $request->input('is_success'),
                'user_id' => $userId,
                'user_uuid' => $userUuid,
                'date_from' => $request->input('date_from'), // Permet de filtrer les logs à partir d'une date spécifique
                'date_to' => $request->input('date_to'), // Permet de filtrer les logs jusqu'à une date spécifique
            ];

            // Récupère tous les logs avec leurs relations, filtrés et triés par date de création décroissante
            $logs = ActivityLog::query()
                ->with(['user', 'loggable'])
                ->when($filters['type'], fn ($q, $v) => $q->where('type', $v))
                ->when($filters['action'], fn ($q, $v) => $q->where('action', $v))
                ->when($filters['is_success'] !== null && $filters['is_success'] !== '', fn ($q) => $q->where('is_success', $filters['is_success']))
                ->when($filters['user_id'], fn ($q, $v) => $q->where('user_id', $v))
                ->when($filters['date_from'], fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
                ->when($filters['date_to'], fn ($q, $v) => $q->whereDate('created_at', '<=', $v))
                ->orderBy('created_at', 'desc')
                ->get();

            // Récupère les listes distinctes pour les filtres
            $types = ActivityLog::select('type')->distinct()->pluck('type');
            $actions = ActivityLog::select('action')->distinct()->pluck('action');

            // Log de l'activité
            $this->logActivity(type: 'info', action: 'backend.activity-logs.index', context: ['count' => $logs->count()], message: null, activity: "{$this->langPath}.index.success", is_success: true);

            // Retourne la vue avec les logs
            return view('v1.backend.pages.activity-logs.index', compact('logs', 'types', 'actions', 'filters'));

        } catch (\Exception $e) {

            // Log de l'erreur
            $this->logActivity(type: 'error', action: 'backend.activity-logs.index', context: [], message: $e->getMessage(), activity: "{$this->langPath}.index.error", is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function backend_activity_logs_show(ActivityLog $activityLog): View
    {
        try {

            // Journal d'activité résolu par binding implicite ({activityLog}), chargé avec son utilisateur et son entité associée
            $log = $activityLog->load(['user', 'loggable']);

            // Log de l'activité de visualisation réussie
            $this->logActivity(type: 'info', action: 'backend.activity-logs.show', context: ['log_uuid' => $log->uuid], message: null, activity: "{$this->langPath}.show.success", is_success: true, loggable: $log);

            // Retourne la vue de détails avec le log
            return view('v1.backend.pages.activity-logs.show', compact('log'));

        } catch (\Exception $e) {

            // Log de l'erreur de visualisation avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.activity-logs.show', context: ['log_uuid' => $activityLog->uuid, 'i18n_params' => ['uuid' => $activityLog->uuid]], message: $e->getMessage(), activity: "{$this->langPath}.show.error", is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_activity_logs_destroy(ActivityLog $activityLog): RedirectResponse
    {
        try {

            // Journal résolu par binding implicite ({activityLog}) — Log de l'activité de suppression en cours
            $this->logActivity(type: 'info', action: 'backend.activity-logs.destroy', context: ['log_uuid' => $activityLog->uuid], message: null, activity: "{$this->langPath}.destroy.flash_info", is_success: true);

            // Faire un redirecte back avec un message flash impossible de supprimer le log
            return redirect()->route('backend.activity-logs.index')->with('info', __("{$this->langPath}.destroy.flash_info", ['uuid' => $activityLog->uuid]));

            // // Récupère le log résolu par binding implicite
            // $log = $activityLog;

            // // Utilise le service pour supprimer le log
            // $this->activityLogService->destroy($log);

            // // Log de l'activité de suppression réussie
            // $this->logActivity(type: 'info', action: 'backend.activity-logs.destroy', context: ['log_uuid' => $log->uuid], message: null, activity: __("{$this->langPath}.destroy.success"), is_success: true, loggable: $log);

            // // Redirige vers la liste des logs avec un message flash de succès
            // return redirect()->route('backend.activity-logs.index')->with('success', __("{$this->langPath}.destroy.flash_success"));

        } catch (\Exception $e) {

            // Log de l'erreur de suppression avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.activity-logs.destroy', context: ['log_uuid' => $activityLog->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.error", ['uuid' => $activityLog->uuid]), is_success: false);

            // Redirige vers la liste des logs avec un message flash d'erreur
            return redirect()->back()->with('error', __("{$this->langPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // CLEAR (Vider les logs de plus de 3 jours)
    // ─────────────────────────────────────────────
    public function backend_activity_logs_clear(): RedirectResponse
    {
        try {

            // Log de l'activité de nettoyage en cours
            $this->logActivity(type: 'info', action: 'backend.activity-logs.clear', context: [], message: null, activity: "{$this->langPath}.clear.flash_info", is_success: true);

            // Faire un redirecte back avec un message flash impossible de nettoyer les logs
            return redirect()->route('backend.activity-logs.index')->with('info', __("{$this->langPath}.clear.flash_info", ['count' => $deletedCount ?? 0]));

            // // Utilise le service pour supprimer les logs de plus de 3 jours
            // $deletedCount = $this->activityLogService->backend_activity_logs_clear_old_logs(3);

            // // Log de l'activité de nettoyage réussie
            // $this->logActivity(type: 'info', action: 'backend.activity-logs.clear', context: ['deleted_count' => $deletedCount], message: null, activity: __("{$this->langPath}.clear.success", ['count' => $deletedCount]), is_success: true);

            // // Redirige vers la liste des logs avec un message flash de succès
            // return redirect()->route('backend.activity-logs.index')->with('success', __("{$this->langPath}.clear.flash_success", ['count' => $deletedCount]));

        } catch (\Exception $e) {

            // Log de l'erreur de nettoyage avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.activity-logs.clear', context: [], message: $e->getMessage(), activity: "{$this->langPath}.clear.error", is_success: false);

            // Redirige vers la liste des logs avec un message flash d'erreur
            return redirect()->back()->with('error', __("{$this->langPath}.clear.flash_error"));
        }
    }
}
