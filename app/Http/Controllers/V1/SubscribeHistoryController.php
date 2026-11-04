<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\SubscribeHistory;
use App\Services\V1\Controllers\SubscribeHistoryService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

use function __;

class SubscribeHistoryController extends Controller
{
    use LogActivity;

    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/subscribes/messages/subscribes-messages-controller.subscribes.histories';

    // Injection de dépendance du service de gestion des historiques d'abonnés
    public function __construct(
        protected SubscribeHistoryService $subscribeHistoryService
    ) {}

    // ═══════════════════════════════════════════════════════════════
    // BACKEND
    // ═══════════════════════════════════════════════════════════════

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_subscribes_histories_index(): View
    {
        try {

            // // Récupère tous les historiques d'abonnés, triés par date d'action décroissante
            // $histories = SubscribeHistory::orderBy('acted_at', 'desc')->get();

            // Récupère tous les historiques d'abonnés, dont les abonnés ne sont pas supprimés, triés par date d'action décroissante
            $histories = SubscribeHistory::whereHas('subscribe', function ($query) {
                $query->whereNull('deleted_at');
            })->orderBy('acted_at', 'desc')->get();

            // Log de l'activité
            $this->logActivity(type: 'info', action: 'backend.subscribe-histories.index', context: ['count' => $histories->count()], message: null, activity: __("{$this->backendLangPath}.index.success"), is_success: true);

            // Retourne la vue avec les historiques
            return view('v1.backend.pages.subscribes.histories.index', compact('histories'));

        } catch (\Exception $e) {

            // Log de l'erreur
            $this->logActivity(type: 'error', action: 'backend.subscribe-histories.index', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.index.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function backend_subscribes_histories_show(SubscribeHistory $subscribeHistory): View
    {
        try {

            // Instance résolue par binding implicite
            $history = $subscribeHistory;

            // Log de l'activité de visualisation réussie
            $this->logActivity(type: 'info', action: 'backend.subscribe-histories.show', context: ['history_uuid' => $history->uuid], message: null, activity: __("{$this->backendLangPath}.show.success", ['uuid' => $history->uuid]), is_success: true, loggable: $history);

            // Retourne la vue de détails avec l'historique
            return view('v1.backend.pages.subscribes.histories.show', compact('history'));

        } catch (\Exception $e) {

            // Log de l'erreur de visualisation avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.subscribe-histories.show', context: ['history_uuid' => $subscribeHistory->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.show.error", ['uuid' => $subscribeHistory->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_subscribes_histories_trashed(): View
    {
        try {

            // Récupère tous les historiques supprimés, triés par date de suppression décroissante
            $histories = SubscribeHistory::onlyTrashed()->orderBy('deleted_at', 'desc')->get();

            // Log de l'activité d'affichage de la liste des historiques supprimés réussie
            $this->logActivity(type: 'info', action: 'backend.subscribe-histories.trashed', context: ['count' => $histories->count()], message: null, activity: __("{$this->backendLangPath}.trashed.success"), is_success: true);

            // Retourne la vue avec les historiques supprimés
            return view('v1.backend.pages.subscribes.histories.trashed', compact('histories'));

        } catch (\Exception $e) {

            // Log de l'erreur lors de l'affichage de la liste des historiques supprimés
            $this->logActivity(type: 'error', action: 'backend.subscribe-histories.trashed', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.trashed.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_subscribes_histories_destroy(SubscribeHistory $subscribeHistory): RedirectResponse
    {
        try {

            // Instance résolue par binding implicite
            $history = $subscribeHistory;

            // Utilise le service pour supprimer l'historique
            $this->subscribeHistoryService->backend_subscribes_histories_destroy($history);

            // Log de l'activité de suppression réussie
            $this->logActivity(type: 'info', action: 'backend.subscribe-histories.destroy', context: ['history_uuid' => $history->uuid], message: null, activity: __("{$this->backendLangPath}.destroy.success", ['uuid' => $history->uuid]), is_success: true, loggable: $history);

            // Redirige vers la page précédente avec un message flash de succès
            return back()->with('success', __("{$this->backendLangPath}.destroy.flash_success"));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur de suppression avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.subscribe-histories.destroy', context: ['history_uuid' => $subscribeHistory->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning_meta", ['uuid' => $subscribeHistory->uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            // Log de l'erreur de suppression avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.subscribe-histories.destroy', context: ['history_uuid' => $subscribeHistory->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.error", ['uuid' => $subscribeHistory->uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_subscribes_histories_restore(string $uuid): RedirectResponse
    {
        try {

            // Récupère l'historique supprimé par UUID ou échoue si non trouvé
            $history = SubscribeHistory::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Utilise le service pour restaurer l'historique
            $this->subscribeHistoryService->backend_subscribes_histories_restore($history);

            // Log de l'activité de restauration réussie
            $this->logActivity(type: 'info', action: 'backend.subscribe-histories.restore', context: ['history_uuid' => $uuid], message: null, activity: __("{$this->backendLangPath}.restore.success", ['uuid' => $uuid]), is_success: true, loggable: $history);

            // Redirige vers la page précédente avec un message flash de succès pour la restauration
            return back()->with('success', __("{$this->backendLangPath}.restore.flash_success"));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur lors de la tentative de restauration d'un historique non trouvé
            $this->logActivity(type: 'warning', action: 'backend.subscribe-histories.restore', context: ['history_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.restore.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur lors de la tentative de restauration avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.subscribe-histories.restore', context: ['history_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            // Log de l'erreur lors de la tentative de restauration avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.subscribe-histories.restore', context: ['history_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_subscribes_histories_force_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupère l'historique supprimé par UUID ou échoue si non trouvé
            $history = SubscribeHistory::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Utilise le service pour supprimer définitivement l'historique
            $this->subscribeHistoryService->backend_subscribes_histories_force_destroy($history);

            // Log de l'activité de suppression définitive réussie
            $this->logActivity(type: 'info', action: 'backend.subscribe-histories.force_destroy', context: ['history_uuid' => $uuid], message: null, activity: __("{$this->backendLangPath}.force_destroy.success", ['uuid' => $uuid]), is_success: true, loggable: $history);

            // Redirige vers la page précédente avec un message flash de succès pour la suppression définitive
            return back()->with('success', __("{$this->backendLangPath}.force_destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur lors de la tentative de suppression définitive d'un historique non trouvé
            $this->logActivity(type: 'warning', action: 'backend.subscribe-histories.force_destroy', context: ['history_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur lors de la tentative de suppression définitive avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.subscribe-histories.force_destroy', context: ['history_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            // Log de l'erreur lors de la tentative de suppression définitive avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.subscribe-histories.force_destroy', context: ['history_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_error"));
        }
    }
}
