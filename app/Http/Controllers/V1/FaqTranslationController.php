<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\FaqTranslation;
use App\Services\V1\Controllers\FaqTranslationService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

use function __;

class FaqTranslationController extends Controller
{
    use LogActivity;

    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/faqs/messages/faqs-messages-controller.faqs';

    // Injection de dépendance du service de gestion des traductions de FAQs
    public function __construct(
        protected FaqTranslationService $faqTranslationService
    ) {}

    // ─────────────────────────────────────────────
    // DESTROY (soft delete d'une traduction)
    // ─────────────────────────────────────────────
    public function backend_faq_translations_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupère la traduction de la FAQ par UUID ou échoue si non trouvée
            $translation = FaqTranslation::where('uuid', $uuid)->firstOrFail();

            // Suppression via le service (soft delete) — le service vérifie si c'est la dernière traduction
            $this->faqTranslationService->destroy($translation);

            // Log de la suppression réussie
            $this->logActivity(type: 'info', action: 'backend.faq-translations.destroy', context: ['translation_uuid' => $uuid], message: null, activity: __("{$this->langPath}.translations.destroy.success", ['uuid' => $uuid]), is_success: true, loggable: $translation);

            // Redirige vers la page précédente avec un message flash de succès pour la suppression
            return back()->with('success', __("{$this->langPath}.translations.destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur lors de la tentative de suppression d'une traduction non trouvée
            $this->logActivity(type: 'warning', action: 'backend.faq-translations.destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.translations.destroy.warning", ['uuid' => $uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour la traduction introuvable
            return back()->with('error', __("{$this->langPath}.translations.destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur lors de la tentative de suppression avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.faq-translations.destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.translations.destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'erreur métier
            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            // Log de l'erreur lors de la tentative de suppression avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.faq-translations.destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.translations.destroy.error", ['uuid' => $uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'échec de la suppression
            return back()->with('error', __("{$this->langPath}.translations.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED (Corbeille des traductions d'une FAQ)
    // ─────────────────────────────────────────────
    public function backend_faq_translations_trashed(string $faq_uuid): View
    {
        try {

            // Récupère la FAQ par UUID ou échoue si non trouvée, y compris les FAQs supprimées (soft deleted)
            $faq = Faq::withTrashed()->where('uuid', $faq_uuid)->firstOrFail();

            $faqName = $faq->active_translation?->question ?? ''; // Récupère la question de la FAQ pour les logs et les messages flash

            // Récupère toutes les traductions supprimées (soft deleted) de cette FAQ
            $faqTranslations = FaqTranslation::onlyTrashed()->where('faq_id', $faq->id)->get();

            // Log de l'accès à la corbeille des traductions pour cette FAQ
            $this->logActivity(type: 'info', action: 'backend.faq-translations.trashed', context: ['faq_uuid' => $faq->uuid, 'faq_question' => $faqName], message: null, activity: __("{$this->langPath}.translations.trashed.accessed", ['name' => $faqName]), is_success: true, loggable: $faq);

            // Retourne la vue avec les traductions supprimées et la FAQ correspondante
            return view('v1.backend.pages.faqs.translations.trashed', compact('faq', 'faqTranslations'));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur lors de la tentative d'accès à la corbeille des traductions pour une FAQ non trouvée
            $this->logActivity(type: 'warning', action: 'backend.faq-translations.trashed', context: ['faq_uuid' => $faq_uuid], message: $e->getMessage(), activity: __("{$this->langPath}.translations.trashed.not_found", ['uuid' => $faq_uuid]), is_success: false);

            abort(404);

        } catch (\Exception $e) {

            // Log de l'erreur lors de la tentative d'accès à la corbeille des traductions pour une FAQ avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.faq-translations.trashed', context: ['faq_uuid' => $faq_uuid], message: $e->getMessage(), activity: __("{$this->langPath}.translations.trashed.error", ['uuid' => $faq_uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_faq_translations_restore(string $uuid): RedirectResponse
    {
        try {

            // Récupère la traduction supprimée (soft deleted) par UUID ou échoue si non trouvée
            $translation = FaqTranslation::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Restaure la traduction via le service
            $this->faqTranslationService->restore($translation);

            // Log de la restauration réussie
            $this->logActivity(type: 'info', action: 'backend.faq-translations.restore', context: ['translation_uuid' => $uuid], message: null, activity: __("{$this->langPath}.translations.restore.success", ['uuid' => $uuid]), is_success: true, loggable: $translation);

            // Redirige vers la page précédente avec un message flash de succès pour la restauration
            return back()->with('success', __("{$this->langPath}.translations.restore.flash_success"));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur lors de la tentative de restauration d'une traduction non trouvée
            $this->logActivity(type: 'warning', action: 'backend.faq-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.translations.restore.warning", ['uuid' => $uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour la traduction introuvable
            return back()->with('error', __("{$this->langPath}.translations.restore.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur lors de la tentative de restauration avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.faq-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.translations.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'erreur métier
            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            // Log de l'erreur lors de la tentative de restauration avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.faq-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.translations.restore.error", ['uuid' => $uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'échec de la restauration
            return back()->with('error', __("{$this->langPath}.translations.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_faq_translations_force_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupère la traduction supprimée (soft deleted) par UUID ou échoue si non trouvée
            $translation = FaqTranslation::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Suppression définitive via le service — le service vérifie si c'est la dernière traduction
            $this->faqTranslationService->forceDestroy($translation);

            // Log de la suppression définitive réussie
            $this->logActivity(type: 'info', action: 'backend.faq-translations.force_destroy', context: ['translation_uuid' => $uuid], message: null, activity: __("{$this->langPath}.translations.force_destroy.success", ['uuid' => $uuid]), is_success: true, loggable: $translation);

            // Redirige vers la page précédente avec un message flash de succès pour la suppression définitive
            return back()->with('success', __("{$this->langPath}.translations.force_destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur lors de la tentative de suppression définitive d'une traduction non trouvée
            $this->logActivity(type: 'warning', action: 'backend.faq-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.translations.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour la traduction introuvable
            return back()->with('error', __("{$this->langPath}.translations.force_destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur lors de la tentative de suppression définitive avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.faq-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.translations.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'erreur métier
            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            // Log de l'erreur lors de la tentative de suppression définitive avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.faq-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.translations.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'échec de la suppression définitive
            return back()->with('error', __("{$this->langPath}.translations.force_destroy.flash_error"));
        }
    }
}
