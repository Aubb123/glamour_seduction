<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Services\V1\Controllers\ContactService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

use function __;

class ContactController extends Controller
{
    use LogActivity;

    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/contacts/messages/contacts-messages-controller.contacts';

    // Injection de dépendance du service de gestion des contacts
    public function __construct(protected ContactService $contactService) {}

    // ═══════════════════════════════════════════════════════════════
    // BACKEND
    // ═══════════════════════════════════════════════════════════════

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_contacts_index(): View
    {
        try {

            // Récupère tous les contacts avec leur utilisateur, triés par date de création décroissante
            $contacts = Contact::withCount(['audits', 'activityLogs'])->orderBy('created_at', 'desc')->get();

            // Log de l'activité
            $this->logActivity(type: 'info', action: 'backend.contacts.index', context: ['count' => $contacts->count()], message: null, activity: __("{$this->backendLangPath}.index.success"), is_success: true);

            // Retourne la vue avec les contacts
            return view('v1.backend.pages.contacts.index', compact('contacts'));

        } catch (\Exception $e) {

            // Log de l'erreur
            $this->logActivity(type: 'error', action: 'backend.contacts.index', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.index.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function backend_contacts_show(Contact $contact): View
    {
        try {

            // Contact résolu par binding implicite ({contact}) avec ses compteurs
            $contact = $contact->loadCount(['audits', 'activityLogs']);

            // Récupère le nom complet pour les logs
            $contactName = $contact->fullname;

            // Log de l'activité de visualisation réussie
            $this->logActivity(type: 'info', action: 'backend.contacts.show', context: ['contact_uuid' => $contact->uuid, 'contact_fullname' => $contactName], message: null, activity: __("{$this->backendLangPath}.show.success", ['name' => $contactName]), is_success: true, loggable: $contact);

            // Retourne la vue de détails avec le contact
            return view('v1.backend.pages.contacts.show', compact('contact'));

        } catch (\Exception $e) {

            // Log de l'erreur de visualisation avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.contacts.show', context: ['contact_uuid' => $contact->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.show.error", ['uuid' => $contact->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // AUDITS
    // ─────────────────────────────────────────────
    public function backend_contacts_audits(Contact $contact): View
    {
        try {

            // Contact résolu par binding implicite ({contact}) avec ses compteurs
            $contact = $contact->loadCount(['audits', 'activityLogs']);

            $audits = $contact->audits()->with(['user', 'auditable'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.contacts.audits', context: ['contact_uuid' => $contact->uuid, 'contact_fullname' => $contact->fullname], message: null, activity: __("{$this->backendLangPath}.audits.success", ['name' => $contact->fullname]), is_success: true, loggable: $contact);

            return view('v1.backend.pages.contacts.others.audits', compact('contact', 'audits'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.contacts.audits', context: ['contact_uuid' => $contact->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.audits.error", ['uuid' => $contact->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVITY LOGS
    // ─────────────────────────────────────────────
    public function backend_contacts_activity_logs(Contact $contact): View
    {
        try {

            // Contact résolu par binding implicite ({contact}) avec ses compteurs
            $contact = $contact->loadCount(['audits', 'activityLogs']);

            $logs = $contact->activityLogs()->with(['user'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.contacts.activity-logs', context: ['contact_uuid' => $contact->uuid, 'contact_fullname' => $contact->fullname], message: null, activity: __("{$this->backendLangPath}.activity-logs.success", ['name' => $contact->fullname]), is_success: true, loggable: $contact);

            return view('v1.backend.pages.contacts.others.activity-logs', compact('contact', 'logs'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.contacts.activity-logs', context: ['contact_uuid' => $contact->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activity-logs.error", ['uuid' => $contact->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // MARK AS READ
    // ─────────────────────────────────────────────
    public function backend_contacts_mark_as_read(Contact $contact): RedirectResponse
    {
        try {

            // Contact résolu par binding implicite ({contact})
            // Utilise le service pour marquer le message comme lu
            $this->contactService->backend_contacts_mark_as_read($contact);

            $contactName = $contact->fullname; // Récupère le nom complet pour les logs

            // Log de l'activité de marquage comme lu réussie
            $this->logActivity(type: 'info', action: 'backend.contacts.mark_as_read', context: ['contact_uuid' => $contact->uuid, 'contact_fullname' => $contactName], message: null, activity: __("{$this->backendLangPath}.mark_as_read.success", ['name' => $contactName]), is_success: true, loggable: $contact);

            // Redirige vers la page précédente avec un message flash de succès
            return redirect()->back()->with('success', __("{$this->backendLangPath}.mark_as_read.flash_success"));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur de marquage comme lu avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.contacts.mark_as_read', context: ['contact_uuid' => $contact->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.mark_as_read.warning_meta", ['uuid' => $contact->uuid]), is_success: false);

            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur de marquage comme lu avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.contacts.mark_as_read', context: ['contact_uuid' => $contact->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.mark_as_read.error", ['uuid' => $contact->uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.mark_as_read.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // MARK AS UNREAD
    // ─────────────────────────────────────────────
    public function backend_contacts_mark_as_unread(Contact $contact): RedirectResponse
    {
        try {

            // Contact résolu par binding implicite ({contact})
            // Utilise le service pour marquer le message comme non lu
            $this->contactService->backend_contacts_mark_as_unread($contact);

            $contactName = $contact->fullname; // Récupère le nom complet pour les logs

            // Log de l'activité de marquage comme non lu réussie
            $this->logActivity(type: 'info', action: 'backend.contacts.mark_as_unread', context: ['contact_uuid' => $contact->uuid, 'contact_fullname' => $contactName], message: null, activity: __("{$this->backendLangPath}.mark_as_unread.success", ['name' => $contactName]), is_success: true, loggable: $contact);

            // Redirige vers la page précédente avec un message flash de succès
            return redirect()->back()->with('success', __("{$this->backendLangPath}.mark_as_unread.flash_success"));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur de marquage comme non lu avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.contacts.mark_as_unread', context: ['contact_uuid' => $contact->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.mark_as_unread.warning_meta", ['uuid' => $contact->uuid]), is_success: false);

            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur de marquage comme non lu avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.contacts.mark_as_unread', context: ['contact_uuid' => $contact->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.mark_as_unread.error", ['uuid' => $contact->uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.mark_as_unread.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_contacts_destroy(Contact $contact): RedirectResponse
    {
        try {

            // Contact résolu par binding implicite ({contact})
            // Récupère le nom complet pour les logs et les messages flash
            $contactName = $contact->fullname;

            // Utilise le service pour supprimer le contact
            $this->contactService->backend_contacts_destroy($contact);

            // Log de l'activité de suppression réussie
            $this->logActivity(type: 'info', action: 'backend.contacts.destroy', context: ['contact_uuid' => $contact->uuid, 'contact_fullname' => $contactName], message: null, activity: __("{$this->backendLangPath}.destroy.success", ['name' => $contactName]), is_success: true, loggable: $contact);

            // Redirige vers la liste des contacts avec un message flash de succès
            return redirect()->route('backend.contacts.index')->with('success', __("{$this->backendLangPath}.destroy.flash_success", ['name' => $contactName]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur de suppression avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.contacts.destroy', context: ['contact_uuid' => $contact->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning_meta", ['uuid' => $contact->uuid]), is_success: false);

            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur de suppression avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.contacts.destroy', context: ['contact_uuid' => $contact->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.error", ['uuid' => $contact->uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_contacts_trashed(): View
    {
        try {

            // Récupère tous les contacts supprimés, triés par date de suppression décroissante
            $contacts = Contact::onlyTrashed()->withCount(['audits', 'activityLogs'])->orderBy('deleted_at', 'desc')->get();

            // Log de l'activité d'affichage de la liste des contacts supprimés réussie
            $this->logActivity(type: 'info', action: 'backend.contacts.trashed', context: ['count' => $contacts->count()], message: null, activity: __("{$this->backendLangPath}.trashed.success"), is_success: true);

            // Retourne la vue avec les contacts supprimés
            return view('v1.backend.pages.contacts.trashed', compact('contacts'));

        } catch (\Exception $e) {

            // Log de l'erreur lors de l'affichage de la liste des contacts supprimés
            $this->logActivity(type: 'error', action: 'backend.contacts.trashed', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.trashed.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_contacts_restore(string $uuid): RedirectResponse
    {
        try {

            // Récupère le contact supprimé par UUID ou échoue si non trouvé
            $contact = Contact::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Utilise le service pour restaurer le contact
            $this->contactService->backend_contacts_restore($contact);

            $contactName = $contact->fullname; // Récupère le nom complet pour les logs

            // Log de l'activité de restauration réussie
            $this->logActivity(type: 'info', action: 'backend.contacts.restore', context: ['contact_uuid' => $contact->uuid, 'contact_fullname' => $contactName], message: null, activity: __("{$this->backendLangPath}.restore.success", ['name' => $contactName]), is_success: true, loggable: $contact);

            // Redirige vers la page précédente avec un message flash de succès pour la restauration
            return redirect()->back()->with('success', __("{$this->backendLangPath}.restore.flash_success", ['name' => $contactName]));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur lors de la tentative de restauration d'un contact non trouvé
            $this->logActivity(type: 'warning', action: 'backend.contacts.restore', context: ['contact_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur lors de la tentative de restauration avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.contacts.restore', context: ['contact_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur lors de la tentative de restauration avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.contacts.restore', context: ['contact_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_contacts_force_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupère le contact supprimé par UUID ou échoue si non trouvé
            $contact = Contact::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Utilise le service pour supprimer définitivement le contact
            $this->contactService->backend_contacts_force_destroy($contact);

            // Log de l'activité de suppression définitive réussie
            $this->logActivity(type: 'info', action: 'backend.contacts.force_destroy', context: ['contact_uuid' => $contact->uuid], message: null, activity: __("{$this->backendLangPath}.force_destroy.success"), is_success: true, loggable: $contact);

            // Redirige vers la page précédente avec un message flash de succès pour la suppression définitive
            return redirect()->back()->with('success', __("{$this->backendLangPath}.force_destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur lors de la tentative de suppression définitive d'un contact non trouvé
            $this->logActivity(type: 'warning', action: 'backend.contacts.force_destroy', context: ['contact_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur lors de la tentative de suppression définitive avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.contacts.force_destroy', context: ['contact_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur lors de la tentative de suppression définitive avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.contacts.force_destroy', context: ['contact_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_error"));
        }
    }
}
