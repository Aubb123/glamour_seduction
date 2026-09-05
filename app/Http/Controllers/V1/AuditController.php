<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Audit;
use App\Models\User;
use App\Services\V1\Controllers\AuditService;
use App\Traits\LogActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

use function __;

class AuditController extends Controller
{
    use LogActivity;

    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/audits/messages/audits-messages-controller.audits';

    // Injection de dépendance du service de gestion des audits
    public function __construct(protected AuditService $auditService) {}

    // ═══════════════════════════════════════════════════════════════
    // BACKEND
    // ═══════════════════════════════════════════════════════════════

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_audits_index(Request $request): View
    {
        try {
            // Résout l'UUID utilisateur en ID numérique si fourni
            $userUuid = $request->input('user_uuid');
            $userId = $userUuid ? User::where('uuid', $userUuid)->value('id') : null;

            // Récupère les filtres de la requête
            $filters = [
                'action' => $request->input('action'),
                'auditable_type' => $request->input('auditable_type'),
                'user_id' => $userId,
                'user_uuid' => $userUuid,
                'date_from' => $request->input('date_from'),
                'date_to' => $request->input('date_to'),
            ];

            // Récupère tous les audits avec leurs relations, filtrés et triés par ID décroissant
            $audits = Audit::query()
                ->with(['user', 'auditable'])
                ->when($filters['action'], fn ($q, $v) => $q->where('action', $v))
                ->when($filters['auditable_type'], fn ($q, $v) => $q->where('auditable_type', $v))
                ->when($filters['user_id'], fn ($q, $v) => $q->where('user_id', $v))
                ->when($filters['date_from'], fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
                ->when($filters['date_to'], fn ($q, $v) => $q->whereDate('created_at', '<=', $v))
                ->orderBy('created_at', 'desc')
                ->get();

            // Récupère la liste des types auditable distincts pour le filtre
            $auditableTypes = Audit::select('auditable_type')->distinct()->pluck('auditable_type');

            // Récupère la liste des actions distinctes pour le filtre
            $actions = Audit::select('action')->distinct()->pluck('action');

            // Log de l'activité
            $this->logActivity(type: 'info', action: 'backend.audits.index', context: ['count' => $audits->count()], message: null, activity: __("{$this->langPath}.index.success"), is_success: true);

            // Retourne la vue avec les audits
            return view('v1.backend.pages.audits.index', compact('audits', 'auditableTypes', 'actions', 'filters'));

        } catch (\Exception $e) {

            // Log de l'erreur
            $this->logActivity(type: 'error', action: 'backend.audits.index', context: [], message: $e->getMessage(), activity: __("{$this->langPath}.index.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function backend_audits_show(Audit $audit): View
    {
        try {

            // Audit résolu par binding implicite ({audit}), chargé avec son utilisateur et son entité associée
            $audit->load(['user', 'auditable']);

            // Log de l'activité de visualisation réussie
            $this->logActivity(type: 'info', action: 'backend.audits.show', context: ['audit_uuid' => $audit->uuid], message: null, activity: __("{$this->langPath}.show.success"), is_success: true, loggable: $audit);

            // Retourne la vue de détails avec l'audit
            return view('v1.backend.pages.audits.show', compact('audit'));

        } catch (\Exception $e) {

            // Log de l'erreur de visualisation avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.audits.show', context: ['audit_uuid' => $audit->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.show.error", ['uuid' => $audit->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_audits_destroy(Audit $audit): RedirectResponse
    {
        try {

            // Audit résolu par binding implicite ({audit})

            $this->logActivity(type: 'info', action: 'backend.audits.destroy', context: ['audit_uuid' => $audit->uuid], message: null, activity: __("{$this->langPath}.destroy.flash_info"), is_success: false, loggable: $audit);

            // Faire un redirecte back avec un message flash impossible de supprimer l'audit
            return redirect()->back()->with('info', __("{$this->langPath}.destroy.flash_info"));

            // // Utilise le service pour supprimer l'audit
            // $this->auditService->destroy($audit);

            // // Log de l'activité de suppression réussie
            // $this->logActivity(type: 'info', action: 'backend.audits.destroy', context: ['audit_uuid' => $audit->uuid], message: null, activity: __("{$this->langPath}.destroy.success"), is_success: true, loggable: $audit);

            // // Redirige vers la liste des audits avec un message flash de succès
            // return redirect()->route('backend.audits.index')->with('success', __("{$this->langPath}.destroy.flash_success"));

        } catch (\Exception $e) {

            // Log de l'erreur de suppression avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.audits.destroy', context: ['audit_uuid' => $audit->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.error", ['uuid' => $audit->uuid]), is_success: false);

            // Redirige vers la liste des audits avec un message flash d'erreur
            return redirect()->back()->with('error', __("{$this->langPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // CLEAR (Vider les audits de plus de 3 jours)
    // ─────────────────────────────────────────────
    public function backend_audits_clear(): RedirectResponse
    {
        try {

            // Log de l'activité de nettoyage en cours
            $this->logActivity(type: 'info', action: 'backend.audits.clear', context: [], message: null, activity: __("{$this->langPath}.clear.flash_info"), is_success: true);

            // Faire un redirecte back avec un message flash impossible de nettoyer les audits
            return redirect()->route('backend.audits.index')->with('info', __("{$this->langPath}.clear.flash_info", ['count' => 0]));

            // // Utilise le service pour supprimer les audits de plus de 3 jours
            // $deletedCount = $this->auditService->clearOldAudits(3);

            // // Log de l'activité de nettoyage réussie
            // $this->logActivity(type: 'info', action: 'backend.audits.clear', context: ['deleted_count' => $deletedCount], message: null, activity: __("{$this->langPath}.clear.success", ['count' => $deletedCount]), is_success: true);

            // // Redirige vers la liste des audits avec un message flash de succès
            // return redirect()->route('backend.audits.index')->with('success', __("{$this->langPath}.clear.flash_success", ['count' => $deletedCount]));

        } catch (\Exception $e) {

            // Log de l'erreur de nettoyage avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.audits.clear', context: [], message: $e->getMessage(), activity: __("{$this->langPath}.clear.error"), is_success: false);

            // Redirige vers la liste des audits avec un message flash d'erreur
            return redirect()->back()->with('error', __("{$this->langPath}.clear.flash_error"));
        }
    }
}
