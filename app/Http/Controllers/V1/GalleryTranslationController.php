<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\GalleryTranslation;
use App\Services\V1\Controllers\GalleryTranslationService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

use function __;

class GalleryTranslationController extends Controller
{
    use LogActivity;

    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/galleries/messages/galleries-messages-controller.galleries';

    // Injection de dépendance du service de gestion des traductions de galeries
    public function __construct(
        protected GalleryTranslationService $galleryTranslationService
    ) {}

    // ─────────────────────────────────────────────
    // DESTROY (soft delete d'une traduction)
    // ─────────────────────────────────────────────
    public function backend_gallery_translations_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupère la traduction de la galerie par UUID ou échoue si non trouvée
            $translation = GalleryTranslation::where('uuid', $uuid)->firstOrFail();

            // Suppression via le service (soft delete) — le service vérifie si c'est la dernière traduction
            $this->galleryTranslationService->backend_galleries_translations_destroy($translation);

            // Log de la suppression réussie
            $this->logActivity(type: 'info', action: 'backend.gallery-translations.destroy', context: ['translation_uuid' => $uuid], message: null, activity: __("{$this->backendLangPath}.translations.destroy.success", ['uuid' => $uuid]), is_success: true, loggable: $translation);

            // Redirige vers la page précédente avec un message flash de succès pour la suppression
            return back()->with('success', __("{$this->backendLangPath}.translations.destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur lors de la tentative de suppression d'une traduction non trouvée
            $this->logActivity(type: 'warning', action: 'backend.gallery-translations.destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.translations.destroy.warning", ['uuid' => $uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour la traduction introuvable
            return back()->with('error', __("{$this->backendLangPath}.translations.destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur lors de la tentative de suppression avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.gallery-translations.destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.translations.destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'erreur métier
            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            // Log de l'erreur lors de la tentative de suppression avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.gallery-translations.destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.translations.destroy.error", ['uuid' => $uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'échec de la suppression
            return back()->with('error', __("{$this->backendLangPath}.translations.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED (Corbeille des traductions d'une galerie)
    // ─────────────────────────────────────────────
    public function backend_gallery_translations_trashed(string $gallery_uuid): View
    {
        try {

            // Récupère la galerie par UUID ou échoue si non trouvée, y compris les galeries supprimées (soft deleted)
            $gallery = Gallery::withTrashed()->where('uuid', $gallery_uuid)->firstOrFail();

            $galleryName = $gallery->active_translation?->title ?? ''; // Récupère le titre de la galerie pour les logs et les messages flash

            // Récupère toutes les traductions supprimées (soft deleted) de cette galerie
            $galleryTranslations = GalleryTranslation::onlyTrashed()->where('gallery_id', $gallery->id)->get();

            // Log de l'accès à la corbeille des traductions pour cette galerie
            $this->logActivity(type: 'info', action: 'backend.gallery-translations.trashed', context: ['gallery_uuid' => $gallery->uuid, 'gallery_title' => $galleryName], message: null, activity: __("{$this->backendLangPath}.translations.trashed.accessed", ['name' => $galleryName]), is_success: true, loggable: $gallery);

            // Retourne la vue avec les traductions supprimées et la galerie correspondante
            return view('v1.backend.pages.galleries.translations.trashed', compact('gallery', 'galleryTranslations'));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur lors de la tentative d'accès à la corbeille des traductions pour une galerie non trouvée
            $this->logActivity(type: 'warning', action: 'backend.gallery-translations.trashed', context: ['gallery_uuid' => $gallery_uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.translations.trashed.not_found", ['uuid' => $gallery_uuid]), is_success: false);

            abort(404);

        } catch (\Exception $e) {

            // Log de l'erreur lors de la tentative d'accès à la corbeille des traductions pour une galerie avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.gallery-translations.trashed', context: ['gallery_uuid' => $gallery_uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.translations.trashed.error", ['uuid' => $gallery_uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_gallery_translations_restore(string $uuid): RedirectResponse
    {
        try {

            // Récupère la traduction supprimée (soft deleted) par UUID ou échoue si non trouvée
            $translation = GalleryTranslation::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Restaure la traduction via le service
            $this->galleryTranslationService->backend_galleries_translations_restore($translation);

            // Log de la restauration réussie
            $this->logActivity(type: 'info', action: 'backend.gallery-translations.restore', context: ['translation_uuid' => $uuid], message: null, activity: __("{$this->backendLangPath}.translations.restore.success", ['uuid' => $uuid]), is_success: true, loggable: $translation);

            // Redirige vers la page précédente avec un message flash de succès pour la restauration
            return back()->with('success', __("{$this->backendLangPath}.translations.restore.flash_success"));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur lors de la tentative de restauration d'une traduction non trouvée
            $this->logActivity(type: 'warning', action: 'backend.gallery-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.translations.restore.warning", ['uuid' => $uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour la traduction introuvable
            return back()->with('error', __("{$this->backendLangPath}.translations.restore.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur lors de la tentative de restauration avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.gallery-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.translations.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'erreur métier
            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            // Log de l'erreur lors de la tentative de restauration avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.gallery-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.translations.restore.error", ['uuid' => $uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'échec de la restauration
            return back()->with('error', __("{$this->backendLangPath}.translations.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_gallery_translations_force_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupère la traduction supprimée (soft deleted) par UUID ou échoue si non trouvée
            $translation = GalleryTranslation::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Suppression définitive via le service — le service vérifie si c'est la dernière traduction
            $this->galleryTranslationService->backend_galleries_translations_force_destroy($translation);

            // Log de la suppression définitive réussie
            $this->logActivity(type: 'info', action: 'backend.gallery-translations.force_destroy', context: ['translation_uuid' => $uuid], message: null, activity: __("{$this->backendLangPath}.translations.force_destroy.success", ['uuid' => $uuid]), is_success: true, loggable: $translation);

            // Redirige vers la page précédente avec un message flash de succès pour la suppression définitive
            return back()->with('success', __("{$this->backendLangPath}.translations.force_destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur lors de la tentative de suppression définitive d'une traduction non trouvée
            $this->logActivity(type: 'warning', action: 'backend.gallery-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.translations.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour la traduction introuvable
            return back()->with('error', __("{$this->backendLangPath}.translations.force_destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur lors de la tentative de suppression définitive avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.gallery-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.translations.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'erreur métier
            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            // Log de l'erreur lors de la tentative de suppression définitive avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.gallery-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.translations.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'échec de la suppression définitive
            return back()->with('error', __("{$this->backendLangPath}.translations.force_destroy.flash_error"));
        }
    }
}
