<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Subscribe;
use App\Services\V1\Controllers\SubscribeService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

use function __;

class SubscribeController extends Controller
{
    use LogActivity;

    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/subscribes/messages/subscribes-messages-controller.subscribes';

    // Injection de dépendance du service de gestion des abonnés
    public function __construct(protected SubscribeService $subscribeService) {}

    // ═══════════════════════════════════════════════════════════════
    // BACKEND
    // ═══════════════════════════════════════════════════════════════

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_subscribes_index(): View
    {
        try {

            // Récupère tous les abonnés avec leur utilisateur, triés par date de création décroissante
            $subscribes = Subscribe::withCount(['audits', 'activityLogs'])->with(['user'])->orderBy('created_at', 'desc')->get();

            // Log de l'activité
            $this->logActivity(type: 'info', action: 'backend.subscribes.index', context: ['count' => $subscribes->count()], message: null, activity: __("{$this->backendLangPath}.index.success"), is_success: true);

            // Retourne la vue avec les abonnés
            return view('v1.backend.pages.subscribes.index', compact('subscribes'));

        } catch (\Exception $e) {

            // Log de l'erreur
            $this->logActivity(type: 'error', action: 'backend.subscribes.index', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.index.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function backend_subscribes_show(Subscribe $subscribe): View
    {
        try {

            // Instance bindée + relations et compteurs
            $subscribe->load(['user', 'histories'])->loadCount(['audits', 'activityLogs']);

            // Récupère l'email pour les logs
            $subscribeEmail = $subscribe->email;

            // Log de l'activité de visualisation réussie
            $this->logActivity(type: 'info', action: 'backend.subscribes.show', context: ['subscribe_uuid' => $subscribe->uuid, 'subscribe_email' => $subscribeEmail], message: null, activity: __("{$this->backendLangPath}.show.success", ['name' => $subscribeEmail]), is_success: true, loggable: $subscribe);

            // Retourne la vue de détails avec l'abonné
            return view('v1.backend.pages.subscribes.show', compact('subscribe'));

        } catch (\Exception $e) {

            // Log de l'erreur de visualisation avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.subscribes.show', context: ['subscribe_uuid' => $subscribe->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.show.error", ['uuid' => $subscribe->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // AUDITS
    // ─────────────────────────────────────────────
    public function backend_subscribes_audits(Subscribe $subscribe): View
    {
        try {

            // Instance bindée + compteurs
            $subscribe->loadCount(['audits', 'activityLogs']);
            $audits = $subscribe->audits()->with(['user', 'auditable'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.subscribes.audits', context: ['subscribe_uuid' => $subscribe->uuid, 'subscribe_email' => $subscribe->email], message: null, activity: __("{$this->backendLangPath}.audits.success", ['name' => $subscribe->email]), is_success: true, loggable: $subscribe);

            return view('v1.backend.pages.subscribes.others.audits', compact('subscribe', 'audits'));

        } catch (\Exception $e) {
            $this->logActivity(type: 'error', action: 'backend.subscribes.audits', context: ['subscribe_uuid' => $subscribe->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.audits.error", ['uuid' => $subscribe->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVITY LOGS
    // ─────────────────────────────────────────────
    public function backend_subscribes_activity_logs(Subscribe $subscribe): View
    {
        try {

            // Instance bindée + compteurs
            $subscribe->loadCount(['audits', 'activityLogs']);

            $logs = $subscribe->activityLogs()->with(['user'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.subscribes.activity-logs', context: ['subscribe_uuid' => $subscribe->uuid, 'subscribe_email' => $subscribe->email], message: null, activity: __("{$this->backendLangPath}.activity-logs.success", ['name' => $subscribe->email]), is_success: true, loggable: $subscribe);

            return view('v1.backend.pages.subscribes.others.activity-logs', compact('subscribe', 'logs'));

        } catch (\Exception $e) {
            $this->logActivity(type: 'error', action: 'backend.subscribes.activity-logs', context: ['subscribe_uuid' => $subscribe->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activity-logs.error", ['uuid' => $subscribe->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_subscribes_activate(Subscribe $subscribe): RedirectResponse
    {
        try {

            // Utilise le service pour activer l'abonné
            $this->subscribeService->backend_subscribes_activate($subscribe);

            $subscribeEmail = $subscribe->email; // Récupère l'email pour les logs

            // Log de l'activité d'activation réussie
            $this->logActivity(type: 'info', action: 'backend.subscribes.activate', context: ['subscribe_uuid' => $subscribe->uuid, 'subscribe_email' => $subscribeEmail], message: null, activity: __("{$this->backendLangPath}.activate.success", ['name' => $subscribeEmail]), is_success: true, loggable: $subscribe);

            // Redirige vers la page précédente avec un message flash de succès
            return redirect()->back()->with('success', __("{$this->backendLangPath}.activate.flash_success"));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur d'activation avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.subscribes.activate', context: ['subscribe_uuid' => $subscribe->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activate.warning_meta", ['uuid' => $subscribe->uuid]), is_success: false);

            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur d'activation avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.subscribes.activate', context: ['subscribe_uuid' => $subscribe->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activate.error", ['uuid' => $subscribe->uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.activate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_subscribes_deactivate(Subscribe $subscribe): RedirectResponse
    {
        try {

            // Utilise le service pour désactiver l'abonné
            $this->subscribeService->backend_subscribes_deactivate($subscribe);

            $subscribeEmail = $subscribe->email; // Récupère l'email pour les logs

            // Log de l'activité de désactivation réussie
            $this->logActivity(type: 'info', action: 'backend.subscribes.deactivate', context: ['subscribe_uuid' => $subscribe->uuid, 'subscribe_email' => $subscribeEmail], message: null, activity: __("{$this->backendLangPath}.deactivate.success", ['name' => $subscribeEmail]), is_success: true, loggable: $subscribe);

            // Redirige vers la page précédente avec un message flash de succès
            return redirect()->back()->with('success', __("{$this->backendLangPath}.deactivate.flash_success"));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur de désactivation avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.subscribes.deactivate', context: ['subscribe_uuid' => $subscribe->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.deactivate.warning_meta", ['uuid' => $subscribe->uuid]), is_success: false);

            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur de désactivation avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.subscribes.deactivate', context: ['subscribe_uuid' => $subscribe->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.deactivate.error", ['uuid' => $subscribe->uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.deactivate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_subscribes_destroy(Subscribe $subscribe): RedirectResponse
    {
        try {

            // Récupère l'email pour les logs et les messages flash
            $subscribeEmail = $subscribe->email;

            // Utilise le service pour supprimer l'abonné
            $this->subscribeService->backend_subscribes_destroy($subscribe);

            // Log de l'activité de suppression réussie
            $this->logActivity(type: 'info', action: 'backend.subscribes.destroy', context: ['subscribe_uuid' => $subscribe->uuid, 'subscribe_email' => $subscribeEmail], message: null, activity: __("{$this->backendLangPath}.destroy.success", ['name' => $subscribeEmail]), is_success: true, loggable: $subscribe);

            // Redirige vers la liste des abonnés avec un message flash de succès
            return redirect()->route('backend.subscribes.index')->with('success', __("{$this->backendLangPath}.destroy.flash_success", ['name' => $subscribeEmail]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur de suppression avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.subscribes.destroy', context: ['subscribe_uuid' => $subscribe->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning_meta", ['uuid' => $subscribe->uuid]), is_success: false);

            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur de suppression avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.subscribes.destroy', context: ['subscribe_uuid' => $subscribe->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.error", ['uuid' => $subscribe->uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_subscribes_trashed(): View
    {
        try {

            // Récupère tous les abonnés supprimés, triés par date de suppression décroissante
            $subscribes = Subscribe::onlyTrashed()->with(['user'])->withCount(['audits', 'activityLogs'])->orderBy('deleted_at', 'desc')->get();

            // Log de l'activité d'affichage de la liste des abonnés supprimés réussie
            $this->logActivity(type: 'info', action: 'backend.subscribes.trashed', context: ['count' => $subscribes->count()], message: null, activity: __("{$this->backendLangPath}.trashed.success"), is_success: true);

            // Retourne la vue avec les abonnés supprimés
            return view('v1.backend.pages.subscribes.trashed', compact('subscribes'));

        } catch (\Exception $e) {

            // Log de l'erreur lors de l'affichage de la liste des abonnés supprimés
            $this->logActivity(type: 'error', action: 'backend.subscribes.trashed', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.trashed.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_subscribes_restore(string $uuid): RedirectResponse
    {
        try {

            // Récupère l'abonné supprimé par UUID ou échoue si non trouvé
            $subscribe = Subscribe::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Utilise le service pour restaurer l'abonné
            $this->subscribeService->backend_subscribes_restore($subscribe);

            $subscribeEmail = $subscribe->email; // Récupère l'email pour les logs

            // Log de l'activité de restauration réussie
            $this->logActivity(type: 'info', action: 'backend.subscribes.restore', context: ['subscribe_uuid' => $subscribe->uuid, 'subscribe_email' => $subscribeEmail], message: null, activity: __("{$this->backendLangPath}.restore.success", ['name' => $subscribeEmail]), is_success: true, loggable: $subscribe);

            // Redirige vers la page précédente avec un message flash de succès pour la restauration
            return redirect()->back()->with('success', __("{$this->backendLangPath}.restore.flash_success", ['name' => $subscribeEmail]));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur lors de la tentative de restauration d'un abonné non trouvé
            $this->logActivity(type: 'warning', action: 'backend.subscribes.restore', context: ['subscribe_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur lors de la tentative de restauration avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.subscribes.restore', context: ['subscribe_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur lors de la tentative de restauration avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.subscribes.restore', context: ['subscribe_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_subscribes_force_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupère l'abonné supprimé par UUID ou échoue si non trouvé
            $subscribe = Subscribe::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Utilise le service pour supprimer définitivement l'abonné
            $this->subscribeService->backend_subscribes_force_destroy($subscribe);

            // Log de l'activité de suppression définitive réussie
            $this->logActivity(type: 'info', action: 'backend.subscribes.force_destroy', context: ['subscribe_uuid' => $subscribe->uuid], message: null, activity: __("{$this->backendLangPath}.force_destroy.success"), is_success: true, loggable: $subscribe);

            // Redirige vers la page précédente avec un message flash de succès pour la suppression définitive
            return redirect()->back()->with('success', __("{$this->backendLangPath}.force_destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur lors de la tentative de suppression définitive d'un abonné non trouvé
            $this->logActivity(type: 'warning', action: 'backend.subscribes.force_destroy', context: ['subscribe_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur lors de la tentative de suppression définitive avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.subscribes.force_destroy', context: ['subscribe_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur lors de la tentative de suppression définitive avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.subscribes.force_destroy', context: ['subscribe_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_error"));
        }
    }
}
