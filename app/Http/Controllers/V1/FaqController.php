<?php

namespace App\Http\Controllers\V1;

use App\Enums\Faqs\FaqStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Faq\StoreFaqRequest;
use App\Http\Requests\V1\Faq\UpdateFaqRequest;
use App\Models\Faq;
use App\Models\FaqTranslation;
use App\Services\V1\Controllers\FaqService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

use function __;

class FaqController extends Controller
{
    use LogActivity;

    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/faqs/messages/faqs-messages-controller.faqs';

    // Injection de dépendance du service de gestion des FAQs
    public function __construct(protected FaqService $faqService) {}

    // ═══════════════════════════════════════════════════════════════
    // BACKEND
    // ═══════════════════════════════════════════════════════════════

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_faqs_index(): View
    {
        try {

            // Récupère toutes les FAQs avec leur traduction active, triées par ordre
            $faqs = Faq::withCount(['audits', 'activityLogs'])->with('user')->withActiveTranslation()->orderBy('sort_order', 'desc')->get();

            // Log de l'activité
            $this->logActivity(type: 'info', action: 'backend.faqs.index', context: ['count' => $faqs->count()], message: null, activity: __("{$this->langPath}.index.success"), is_success: true);

            // Retourne la vue avec les FAQs
            return view('v1.backend.pages.faqs.index', compact('faqs'));

        } catch (\Exception $e) {

            // Log de l'erreur
            $this->logActivity(type: 'error', action: 'backend.faqs.index', context: [], message: $e->getMessage(), activity: __("{$this->langPath}.index.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────
    public function backend_faqs_create(): View
    {
        try {

            // Récupère les langues actives pour le formulaire de création
            $langs = getActiveLangs()['langs'];

            // Log de l'accès à la page de création
            $this->logActivity(type: 'info', action: 'backend.faqs.create', context: [], message: null, activity: __("{$this->langPath}.create.success"), is_success: true);

            // Retourne la vue de création avec les langues
            return view('v1.backend.pages.faqs.create', compact('langs'));

        } catch (\Exception $e) {

            // Log de l'erreur lors de l'accès à la page de création
            $this->logActivity(type: 'error', action: 'backend.faqs.create', context: [], message: $e->getMessage(), activity: __("{$this->langPath}.create.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_faqs_store(StoreFaqRequest $request): RedirectResponse
    {
        try {

            // Utilise le service pour créer une nouvelle FAQ avec les données validées
            $faq = $this->faqService->backend_faqs_store($request->validated());

            // Récupère la question de la FAQ pour les logs et les messages flash
            $faqName = $faq->active_translation?->question ?? '';

            // Log de l'activité de création réussie
            $this->logActivity(type: 'info', action: 'backend.faqs.store', context: ['faq_uuid' => $faq->uuid, 'faq_question' => $faqName], message: null, activity: __("{$this->langPath}.store.success", ['name' => $faqName]), is_success: true, loggable: $faq);

            // Redirige vers la page de détails de la FAQ avec un message flash de succès
            return redirect()->route('backend.faqs.show', ['faq' => $faq->uuid, 'slug' => $faq->active_translation?->slug ?? 'null'])->with('success', __("{$this->langPath}.store.flash_success", ['name' => $faqName]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur de création avec un message d'avertissement
            $this->logActivity(type: 'warning', action: 'backend.faqs.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->langPath}.store.error"), is_success: false);

            // Redirige en arrière avec un message d'erreur et les données du formulaire
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur de création avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.faqs.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->langPath}.store.error"), is_success: false);

            // Redirige en arrière avec un message d'erreur et les données du formulaire
            return redirect()->back()->with('error', __("{$this->langPath}.store.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // Résolution + vérification slug (helper privé)
    // ─────────────────────────────────────────────
    private function resolveFaqWithSlugCheck(Faq $faq, string $slug, string $action, string $langKey): Faq
    {
        $expectedSlug = $faq->slug ?? $faq->active_translation?->slug ?? 'null';

        if ($expectedSlug !== $slug) {
            // Log de l'erreur de correspondance du slug avec un message d'avertissement
            $this->logActivity(type: 'warning', action: $action, context: ['faq_uuid' => $faq->uuid, 'faq_slug' => $slug, 'expected_slug' => $expectedSlug], message: null, activity: __("{$this->langPath}.{$langKey}.slug_mismatch"), is_success: false, loggable: $faq);

            abort(404);
        }

        return $faq;
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function backend_faqs_show(Faq $faq, string $slug): View
    {
        try {

            // Récupère les langues actives pour l'affichage
            $langs = getActiveLangs()['langs'];

            // Vérifie que le slug correspond
            $faq = $this->resolveFaqWithSlugCheck($faq, $slug, 'backend.faqs.show', 'show');

            // Charge les compteurs
            $faq->loadCount(['audits', 'activityLogs']);

            // Récupère les traductions supprimées
            $faqTranslations = FaqTranslation::onlyTrashed()->where('faq_id', $faq->id)->get();

            // Récupère la question de la FAQ pour les logs et les messages flash
            $faqName = $faq->active_translation?->question ?? __("{$this->langPath}.show.no_name");

            // Log de l'activité de visualisation réussie
            $this->logActivity(type: 'info', action: 'backend.faqs.show', context: ['faq_uuid' => $faq->uuid, 'faq_question' => $faqName], message: null, activity: __("{$this->langPath}.show.success", ['name' => $faqName]), is_success: true, loggable: $faq);

            // Retourne la vue de détails avec la FAQ, les langues et les traductions supprimées
            return view('v1.backend.pages.faqs.show', compact('faq', 'langs', 'faqTranslations'));

        } catch (\Exception $e) {

            // Log de l'erreur de visualisation avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.faqs.show', context: ['faq_uuid' => $faq->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.show.error", ['uuid' => $faq->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // AUDITS
    // ─────────────────────────────────────────────
    public function backend_faqs_audits(Faq $faq, string $slug): View
    {
        try {

            // Vérifie que le slug correspond
            $faq = $this->resolveFaqWithSlugCheck($faq, $slug, 'backend.faqs.audits', 'audits');

            // Charge les compteurs
            $faq->loadCount(['audits', 'activityLogs']);

            $faqName = $faq->active_translation?->question ?? '';

            $audits = $faq->audits()->with(['user', 'auditable'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.faqs.audits', context: ['faq_uuid' => $faq->uuid, 'faq_question' => $faqName], message: null, activity: __("{$this->langPath}.audits.success", ['name' => $faqName]), is_success: true, loggable: $faq);

            return view('v1.backend.pages.faqs.others.audits', compact('faq', 'audits'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.faqs.audits', context: ['faq_uuid' => $faq->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.audits.error", ['uuid' => $faq->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVITY LOGS
    // ─────────────────────────────────────────────
    public function backend_faqs_activity_logs(Faq $faq, string $slug): View
    {
        try {

            // Vérifie que le slug correspond
            $faq = $this->resolveFaqWithSlugCheck($faq, $slug, 'backend.faqs.activity-logs', 'activity-logs');

            // Charge les compteurs
            $faq->loadCount(['audits', 'activityLogs']);

            $faqName = $faq->active_translation?->question ?? '';

            $logs = $faq->activityLogs()->with(['user'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.faqs.activity-logs', context: ['faq_uuid' => $faq->uuid, 'faq_question' => $faqName], message: null, activity: __("{$this->langPath}.activity-logs.success", ['name' => $faqName]), is_success: true, loggable: $faq);

            return view('v1.backend.pages.faqs.others.activity-logs', compact('faq', 'logs'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.faqs.activity-logs', context: ['faq_uuid' => $faq->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.activity-logs.error", ['uuid' => $faq->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────
    public function backend_faqs_edit(Faq $faq): View
    {
        try {

            // Récupère les langues actives pour le formulaire d'édition
            $langs = getActiveLangs()['langs'];

            // Log de l'accès à la page d'édition réussie
            $this->logActivity(type: 'info', action: 'backend.faqs.edit', context: ['faq_uuid' => $faq->uuid, 'faq_question' => $faq->active_translation?->question], message: null, activity: __("{$this->langPath}.edit.success", ['name' => $faq->active_translation?->question ?? '']), is_success: true, loggable: $faq);

            // Retourne la vue d'édition avec la FAQ et les langues
            return view('v1.backend.pages.faqs.edit', compact('faq', 'langs'));

        } catch (\Exception $e) {

            // Log de l'erreur d'accès à la page d'édition avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.faqs.edit', context: ['faq_uuid' => $faq->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.edit.error", ['uuid' => $faq->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_faqs_update(UpdateFaqRequest $request, Faq $faq): RedirectResponse
    {
        try {

            // Utilise le service pour mettre à jour la FAQ avec les données validées
            $this->faqService->backend_faqs_update($faq, $request->validated());

            $faq = $faq->refresh(); // Rafraîchit l'instance du modèle pour obtenir les dernières données après la mise à jour

            $faqName = $faq->active_translation?->question ?? ''; // Récupère la question de la FAQ pour les logs et les messages flash

            // Log de l'activité de mise à jour réussie
            $this->logActivity(type: 'info', action: 'backend.faqs.update', context: ['faq_uuid' => $faq->uuid, 'faq_question' => $faqName, 'changes' => $faq->getChanges()], message: null, activity: __("{$this->langPath}.update.success", ['name' => $faqName]), is_success: true, loggable: $faq);

            // Redirige vers la page de détails de la FAQ avec un message flash de succès
            return redirect()->route('backend.faqs.show', ['faq' => $faq->uuid, 'slug' => $faq->active_translation?->slug ?? 'null'])->with('success', __("{$this->langPath}.update.flash_success", ['name' => $faqName]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur de mise à jour avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.faqs.update', context: ['faq_uuid' => $faq->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.update.warning_meta", ['uuid' => $faq->uuid]), is_success: false);

            // Redirige en arrière avec un message d'erreur et les données du formulaire
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur de mise à jour avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.faqs.update', context: ['faq_uuid' => $faq->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.update.error", ['uuid' => $faq->uuid]), is_success: false);

            // Redirige en arrière avec un message d'erreur et les données du formulaire
            return redirect()->back()->with('error', __("{$this->langPath}.update.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // TOGGLE ACTIVE (Activer/Désactiver)
    // ─────────────────────────────────────────────
    public function backend_faqs_toggle_active(Request $request, Faq $faq): RedirectResponse
    {
        try {

            // Stocke l'état actif actuel avant de le changer
            $wasActive = $faq->status === FaqStatus::Active;

            // Utilise le service pour basculer le statut actif de la FAQ
            $this->faqService->backend_faqs_toggle_active($faq);

            $faqName = $faq->active_translation?->question ?? ''; // Récupère la question de la FAQ pour les logs et les messages flash

            // Log de l'activité de basculement de statut réussie
            if ($wasActive) {
                // Si la FAQ était active avant, elle est maintenant désactivée
                $this->logActivity(type: 'info', action: 'backend.faqs.toggle_active', context: ['faq_uuid' => $faq->uuid, 'faq_question' => $faqName], message: null, activity: __("{$this->langPath}.toggle_active.success_deactivated", ['name' => $faqName]), is_success: true, loggable: $faq);

                // Redirige vers la page précédente avec un message flash de succès pour la désactivation
                return redirect()->back()->with('success', __("{$this->langPath}.toggle_active.flash_success_deactivated", ['name' => $faqName]));

            } else {
                // Si la FAQ était inactive avant, elle est maintenant activée
                $this->logActivity(type: 'info', action: 'backend.faqs.toggle_active', context: ['faq_uuid' => $faq->uuid, 'faq_question' => $faqName], message: null, activity: __("{$this->langPath}.toggle_active.success_activated", ['name' => $faqName]), is_success: true, loggable: $faq);

                // Redirige vers la page précédente avec un message flash de succès pour l'activation
                return redirect()->back()->with('success', __("{$this->langPath}.toggle_active.flash_success_activated", ['name' => $faqName]));
            }

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur de basculement de statut avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.faqs.toggle_active', context: ['faq_uuid' => $faq->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.toggle_active.warning_meta", ['uuid' => $faq->uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'erreur métier
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur de basculement de statut avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.faqs.toggle_active', context: ['faq_uuid' => $faq->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.toggle_active.error", ['uuid' => $faq->uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'échec du basculement
            return redirect()->back()->with('error', __("{$this->langPath}.toggle_active.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_faqs_destroy(Faq $faq): RedirectResponse
    {
        try {

            // Récupère la question de la FAQ pour les logs et les messages flash
            $faqName = $faq->active_translation?->question ?? __("{$this->langPath}.destroy.unknown");

            // Utilise le service pour supprimer la FAQ
            $this->faqService->backend_faqs_destroy($faq);

            // Log de l'activité de suppression réussie
            $this->logActivity(type: 'info', action: 'backend.faqs.destroy', context: ['faq_uuid' => $faq->uuid, 'faq_question' => $faqName], message: null, activity: __("{$this->langPath}.destroy.success", ['name' => $faqName]), is_success: true, loggable: $faq);

            // Redirige vers la liste des FAQs avec un message flash de succès
            return redirect()->route('backend.faqs.index')->with('success', __("{$this->langPath}.destroy.flash_success", ['name' => $faqName]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur de suppression avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.faqs.destroy', context: ['faq_uuid' => $faq->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.warning_meta", ['uuid' => $faq->uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'erreur métier
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur de suppression avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.faqs.destroy', context: ['faq_uuid' => $faq->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.error", ['uuid' => $faq->uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'échec de la suppression
            return redirect()->back()->with('error', __("{$this->langPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_faqs_trashed(): View
    {
        try {

            // Récupère toutes les FAQs supprimées avec leur traduction active, triées par date de suppression décroissante
            $faqs = Faq::onlyTrashed()->withTrashedActiveTranslation()->withCount(['audits', 'activityLogs'])->orderBy('deleted_at', 'desc')->get();

            // Log de l'activité d'affichage de la liste des FAQs supprimées réussie
            $this->logActivity(type: 'info', action: 'backend.faqs.trashed', context: ['count' => $faqs->count()], message: null, activity: __("{$this->langPath}.trashed.success"), is_success: true);

            // Retourne la vue avec les FAQs supprimées
            return view('v1.backend.pages.faqs.trashed', compact('faqs'));

        } catch (\Exception $e) {

            // Log de l'erreur lors de l'affichage de la liste des FAQs supprimées
            $this->logActivity(type: 'error', action: 'backend.faqs.trashed', context: [], message: $e->getMessage(), activity: __("{$this->langPath}.trashed.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_faqs_restore(string $uuid): RedirectResponse
    {
        try {

            // Récupère la FAQ supprimée par UUID ou échoue si non trouvée
            $faq = Faq::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Utilise le service pour restaurer la FAQ
            $this->faqService->backend_faqs_restore($faq);

            $faqName = $faq->active_translation?->question ?? ''; // Récupère la question de la FAQ pour les logs et les messages flash

            // Log de l'activité de restauration réussie
            $this->logActivity(type: 'info', action: 'backend.faqs.restore', context: ['faq_uuid' => $faq->uuid, 'faq_question' => $faqName], message: null, activity: __("{$this->langPath}.restore.success", ['name' => $faqName]), is_success: true, loggable: $faq);

            // Redirige vers la page précédente avec un message flash de succès pour la restauration
            return redirect()->back()->with('success', __("{$this->langPath}.restore.flash_success", ['name' => $faqName]));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur lors de la tentative de restauration d'une FAQ non trouvée
            $this->logActivity(type: 'warning', action: 'backend.faqs.restore', context: ['faq_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur lors de la tentative de restauration avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.faqs.restore', context: ['faq_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'erreur métier
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur lors de la tentative de restauration avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.faqs.restore', context: ['faq_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->langPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_faqs_force_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupère la FAQ supprimée par UUID ou échoue si non trouvée
            $faq = Faq::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Utilise le service pour supprimer définitivement la FAQ
            $this->faqService->backend_faqs_force_destroy($faq);

            // Log de l'activité de suppression définitive réussie
            $this->logActivity(type: 'info', action: 'backend.faqs.force_destroy', context: ['faq_uuid' => $faq->uuid], message: null, activity: __("{$this->langPath}.force_destroy.success"), is_success: true, loggable: $faq);

            // Redirige vers la page précédente avec un message flash de succès pour la suppression définitive
            return redirect()->back()->with('success', __("{$this->langPath}.force_destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur lors de la tentative de suppression définitive d'une FAQ non trouvée
            $this->logActivity(type: 'warning', action: 'backend.faqs.force_destroy', context: ['faq_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur lors de la tentative de suppression définitive avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.faqs.force_destroy', context: ['faq_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'erreur métier
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur lors de la tentative de suppression définitive avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.faqs.force_destroy', context: ['faq_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'échec de la suppression définitive
            return redirect()->back()->with('error', __("{$this->langPath}.force_destroy.flash_error"));
        }
    }
}
