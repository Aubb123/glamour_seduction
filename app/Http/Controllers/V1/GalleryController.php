<?php

namespace App\Http\Controllers\V1;

use App\Enums\Galleries\GalleryStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Gallery\StoreGalleryRequest;
use App\Http\Requests\V1\Gallery\UpdateGalleryRequest;
use App\Models\Gallery;
use App\Models\GalleryTranslation;
use App\Services\V1\Controllers\GalleryService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

use function __;

class GalleryController extends Controller
{
    use LogActivity;

    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/galleries/messages/galleries-messages-controller.galleries';

    // Injection de dépendance du service de gestion des galeries
    public function __construct(protected GalleryService $galleryService) {}

    // ═══════════════════════════════════════════════════════════════
    // BACKEND
    // ═══════════════════════════════════════════════════════════════

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_galleries_index(): View
    {
        try {

            // Récupère toutes les galeries avec leur traduction active, triées par ordre
            $galleries = Gallery::withCount(['audits', 'activityLogs'])->withActiveTranslation()->with(['user', 'cloudFiles'])->orderBy('sort_order', 'desc')->get();

            // Log de l'activité
            $this->logActivity(type: 'info', action: 'backend.galleries.index', context: ['count' => $galleries->count()], message: null, activity: __("{$this->backendLangPath}.index.success"), is_success: true);

            // Retourne la vue avec les galeries
            return view('v1.backend.pages.galleries.index', compact('galleries'));

        } catch (\Exception $e) {

            // Log de l'erreur
            $this->logActivity(type: 'error', action: 'backend.galleries.index', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.index.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────
    public function backend_galleries_create(): View
    {
        try {

            // Récupère les langues actives pour le formulaire de création
            $langs = getActiveLangs()['langs'];

            // Log de l'accès à la page de création
            $this->logActivity(type: 'info', action: 'backend.galleries.create', context: [], message: null, activity: __("{$this->backendLangPath}.create.success"), is_success: true);

            // Retourne la vue de création avec les langues
            return view('v1.backend.pages.galleries.create', compact('langs'));

        } catch (\Exception $e) {

            // Log de l'erreur lors de l'accès à la page de création
            $this->logActivity(type: 'error', action: 'backend.galleries.create', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.create.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_galleries_store(StoreGalleryRequest $request): RedirectResponse
    {
        try {

            // Utilise le service pour créer une nouvelle galerie avec les données validées
            $gallery = $this->galleryService->backend_galleries_store($request->validated());

            // Récupère le titre de la galerie pour les logs et les messages flash
            $galleryName = $gallery->active_translation?->title ?? '';

            // Log de l'activité de création réussie
            $this->logActivity(type: 'info', action: 'backend.galleries.store', context: ['gallery_uuid' => $gallery->uuid, 'gallery_title' => $galleryName], message: null, activity: __("{$this->backendLangPath}.store.success", ['name' => $galleryName]), is_success: true, loggable: $gallery);

            // Redirige vers la page de détails de la galerie avec un message flash de succès
            return redirect()->route('backend.galleries.show', ['gallery' => $gallery->uuid, 'slug' => $gallery->active_translation?->slug ?? 'null'])->with('success', __("{$this->backendLangPath}.store.flash_success", ['name' => $galleryName]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur de création avec un message d'avertissement
            $this->logActivity(type: 'warning', action: 'backend.galleries.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            // Redirige en arrière avec un message d'erreur et les données du formulaire
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur de création avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.galleries.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            // Redirige en arrière avec un message d'erreur et les données du formulaire
            return redirect()->back()->with('error', __("{$this->backendLangPath}.store.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // Résolution + vérification slug (helper privé)
    // ─────────────────────────────────────────────
    private function resolveGalleryWithSlugCheck(Gallery $gallery, string $slug, string $action, string $langKey): Gallery
    {
        $expectedSlug = $gallery->active_translation?->slug ?? 'null';

        if ($expectedSlug !== $slug) {
            // Log de l'erreur de correspondance du slug avec un message d'avertissement
            $this->logActivity(type: 'warning', action: $action, context: ['gallery_uuid' => $gallery->uuid, 'gallery_slug' => $slug, 'expected_slug' => $expectedSlug], message: null, activity: __("{$this->backendLangPath}.{$langKey}.slug_mismatch"), is_success: false, loggable: $gallery);

            abort(404);
        }

        return $gallery;
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function backend_galleries_show(Gallery $gallery, string $slug): View
    {
        try {

            // Récupère les langues actives pour l'affichage
            $langs = getActiveLangs()['langs'];

            // Vérifie que le slug correspond
            $gallery = $this->resolveGalleryWithSlugCheck($gallery, $slug, 'backend.galleries.show', 'show');

            // Charge les compteurs et la relation utilisateur
            $gallery->load(['user'])->loadCount(['audits', 'activityLogs']);

            // Récupère les traductions supprimées
            $galleryTranslations = GalleryTranslation::onlyTrashed()->where('gallery_id', $gallery->id)->get();

            // Récupère le titre de la galerie pour les logs et les messages flash
            $galleryName = $gallery->active_translation?->title ?? __("{$this->backendLangPath}.show.no_name");

            // Log de l'activité de visualisation réussie
            $this->logActivity(type: 'info', action: 'backend.galleries.show', context: ['gallery_uuid' => $gallery->uuid, 'gallery_title' => $galleryName], message: null, activity: __("{$this->backendLangPath}.show.success", ['name' => $galleryName]), is_success: true, loggable: $gallery);

            // Retourne la vue de détails avec la galerie, les langues et les traductions supprimées
            return view('v1.backend.pages.galleries.show', compact('gallery', 'langs', 'galleryTranslations'));

        } catch (\Exception $e) {

            // Log de l'erreur de visualisation avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.galleries.show', context: ['gallery_uuid' => $gallery->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.show.error", ['uuid' => $gallery->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // AUDITS
    // ─────────────────────────────────────────────
    public function backend_galleries_audits(Gallery $gallery, string $slug): View
    {
        try {

            // Vérifie que le slug correspond
            $gallery = $this->resolveGalleryWithSlugCheck($gallery, $slug, 'backend.galleries.audits', 'audits');

            // Charge les compteurs
            $gallery->loadCount(['audits', 'activityLogs']);

            $galleryName = $gallery->active_translation?->title ?? '';

            $audits = $gallery->audits()->with(['user', 'auditable'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.galleries.audits', context: ['gallery_uuid' => $gallery->uuid, 'gallery_title' => $galleryName], message: null, activity: __("{$this->backendLangPath}.audits.success", ['name' => $galleryName]), is_success: true, loggable: $gallery);

            return view('v1.backend.pages.galleries.others.audits', compact('gallery', 'audits'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.galleries.audits', context: ['gallery_uuid' => $gallery->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.audits.error", ['uuid' => $gallery->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVITY LOGS
    // ─────────────────────────────────────────────
    public function backend_galleries_activity_logs(Gallery $gallery, string $slug): View
    {
        try {

            // Vérifie que le slug correspond
            $gallery = $this->resolveGalleryWithSlugCheck($gallery, $slug, 'backend.galleries.activity-logs', 'activity-logs');

            // Charge les compteurs
            $gallery->loadCount(['audits', 'activityLogs']);

            $galleryName = $gallery->active_translation?->title ?? '';

            $logs = $gallery->activityLogs()->with(['user'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.galleries.activity-logs', context: ['gallery_uuid' => $gallery->uuid, 'gallery_title' => $galleryName], message: null, activity: __("{$this->backendLangPath}.activity-logs.success", ['name' => $galleryName]), is_success: true, loggable: $gallery);

            return view('v1.backend.pages.galleries.others.activity-logs', compact('gallery', 'logs'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.galleries.activity-logs', context: ['gallery_uuid' => $gallery->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activity-logs.error", ['uuid' => $gallery->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────
    public function backend_galleries_edit(Gallery $gallery): View
    {
        try {

            // Récupère les langues actives pour le formulaire d'édition
            $langs = getActiveLangs()['langs'];

            // Log de l'accès à la page d'édition réussie
            $this->logActivity(type: 'info', action: 'backend.galleries.edit', context: ['gallery_uuid' => $gallery->uuid, 'gallery_title' => $gallery->active_translation?->title], message: null, activity: __("{$this->backendLangPath}.edit.success", ['name' => $gallery->active_translation?->title ?? '']), is_success: true, loggable: $gallery);

            // Retourne la vue d'édition avec la galerie et les langues
            return view('v1.backend.pages.galleries.edit', compact('gallery', 'langs'));

        } catch (\Exception $e) {

            // Log de l'erreur d'accès à la page d'édition avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.galleries.edit', context: ['gallery_uuid' => $gallery->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.edit.error", ['uuid' => $gallery->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_galleries_update(UpdateGalleryRequest $request, Gallery $gallery): RedirectResponse
    {
        try {

            // Utilise le service pour mettre à jour la galerie avec les données validées
            $this->galleryService->backend_galleries_update($gallery, $request->validated());

            $gallery = $gallery->refresh(); // Rafraîchit l'instance du modèle pour obtenir les dernières données après la mise à jour

            $galleryName = $gallery->active_translation?->title ?? ''; // Récupère le titre de la galerie pour les logs et les messages flash

            // Log de l'activité de mise à jour réussie
            $this->logActivity(type: 'info', action: 'backend.galleries.update', context: ['gallery_uuid' => $gallery->uuid, 'gallery_title' => $galleryName, 'changes' => $gallery->getChanges()], message: null, activity: __("{$this->backendLangPath}.update.success", ['name' => $galleryName]), is_success: true, loggable: $gallery);

            // Redirige vers la page de détails de la galerie avec un message flash de succès
            return redirect()->route('backend.galleries.show', ['gallery' => $gallery->uuid, 'slug' => $gallery->active_translation?->slug ?? 'null'])->with('success', __("{$this->backendLangPath}.update.flash_success", ['name' => $galleryName]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur de mise à jour avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.galleries.update', context: ['gallery_uuid' => $gallery->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.warning_meta", ['uuid' => $gallery->uuid]), is_success: false);

            // Redirige en arrière avec un message d'erreur et les données du formulaire
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur de mise à jour avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.galleries.update', context: ['gallery_uuid' => $gallery->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.error", ['uuid' => $gallery->uuid]), is_success: false);

            // Redirige en arrière avec un message d'erreur et les données du formulaire
            return redirect()->back()->with('error', __("{$this->backendLangPath}.update.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // TOGGLE ACTIVE (Activer/Désactiver)
    // ─────────────────────────────────────────────
    public function backend_galleries_toggle_active(Request $request, Gallery $gallery): RedirectResponse
    {
        try {

            // Stocke l'état actif actuel avant de le changer
            $wasActive = $gallery->status === GalleryStatus::Active;

            // Utilise le service pour basculer le statut actif de la galerie
            $this->galleryService->backend_galleries_toggle_active($gallery);

            $galleryName = $gallery->active_translation?->title ?? ''; // Récupère le titre de la galerie pour les logs et les messages flash

            // Log de l'activité de basculement de statut réussie
            if ($wasActive) {
                // Si la galerie était active avant, elle est maintenant désactivée
                $this->logActivity(type: 'info', action: 'backend.galleries.toggle_active', context: ['gallery_uuid' => $gallery->uuid, 'gallery_title' => $galleryName], message: null, activity: __("{$this->backendLangPath}.toggle_active.success_deactivated", ['name' => $galleryName]), is_success: true, loggable: $gallery);

                // Redirige vers la page précédente avec un message flash de succès pour la désactivation
                return redirect()->back()->with('success', __("{$this->backendLangPath}.toggle_active.flash_success_deactivated", ['name' => $galleryName]));

            } else {
                // Si la galerie était inactive avant, elle est maintenant activée
                $this->logActivity(type: 'info', action: 'backend.galleries.toggle_active', context: ['gallery_uuid' => $gallery->uuid, 'gallery_title' => $galleryName], message: null, activity: __("{$this->backendLangPath}.toggle_active.success_activated", ['name' => $galleryName]), is_success: true, loggable: $gallery);

                // Redirige vers la page précédente avec un message flash de succès pour l'activation
                return redirect()->back()->with('success', __("{$this->backendLangPath}.toggle_active.flash_success_activated", ['name' => $galleryName]));
            }

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur de basculement de statut avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.galleries.toggle_active', context: ['gallery_uuid' => $gallery->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.toggle_active.warning_meta", ['uuid' => $gallery->uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'erreur métier
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur de basculement de statut avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.galleries.toggle_active', context: ['gallery_uuid' => $gallery->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.toggle_active.error", ['uuid' => $gallery->uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'échec du basculement
            return redirect()->back()->with('error', __("{$this->backendLangPath}.toggle_active.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_galleries_destroy(Gallery $gallery): RedirectResponse
    {
        try {

            // Récupère le titre de la galerie pour les logs et les messages flash
            $galleryName = $gallery->active_translation?->title ?? __("{$this->backendLangPath}.destroy.unknown");

            // Utilise le service pour supprimer la galerie
            $this->galleryService->backend_galleries_destroy($gallery);

            // Log de l'activité de suppression réussie
            $this->logActivity(type: 'info', action: 'backend.galleries.destroy', context: ['gallery_uuid' => $gallery->uuid, 'gallery_title' => $galleryName], message: null, activity: __("{$this->backendLangPath}.destroy.success", ['name' => $galleryName]), is_success: true, loggable: $gallery);

            // Redirige vers la liste des galeries avec un message flash de succès
            return redirect()->route('backend.galleries.index')->with('success', __("{$this->backendLangPath}.destroy.flash_success", ['name' => $galleryName]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur de suppression avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.galleries.destroy', context: ['gallery_uuid' => $gallery->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning_meta", ['uuid' => $gallery->uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'erreur métier
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur de suppression avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.galleries.destroy', context: ['gallery_uuid' => $gallery->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.error", ['uuid' => $gallery->uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'échec de la suppression
            return redirect()->back()->with('error', __("{$this->backendLangPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_galleries_trashed(): View
    {
        try {

            // Récupère toutes les galeries supprimées avec leur traduction active, triées par date de suppression décroissante
            $galleries = Gallery::onlyTrashed()->withTrashedActiveTranslation()->with(['user', 'cloudFiles'])->withCount(['audits', 'activityLogs'])->orderBy('deleted_at', 'desc')->get();

            // Log de l'activité d'affichage de la liste des galeries supprimées réussie
            $this->logActivity(type: 'info', action: 'backend.galleries.trashed', context: ['count' => $galleries->count()], message: null, activity: __("{$this->backendLangPath}.trashed.success"), is_success: true);

            // Retourne la vue avec les galeries supprimées
            return view('v1.backend.pages.galleries.trashed', compact('galleries'));

        } catch (\Exception $e) {

            // Log de l'erreur lors de l'affichage de la liste des galeries supprimées
            $this->logActivity(type: 'error', action: 'backend.galleries.trashed', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.trashed.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_galleries_restore(string $uuid): RedirectResponse
    {
        try {

            // Récupère la galerie supprimée par UUID ou échoue si non trouvée
            $gallery = Gallery::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Utilise le service pour restaurer la galerie
            $this->galleryService->backend_galleries_restore($gallery);

            $galleryName = $gallery->active_translation?->title ?? ''; // Récupère le titre de la galerie pour les logs et les messages flash

            // Log de l'activité de restauration réussie
            $this->logActivity(type: 'info', action: 'backend.galleries.restore', context: ['gallery_uuid' => $gallery->uuid, 'gallery_title' => $galleryName], message: null, activity: __("{$this->backendLangPath}.restore.success", ['name' => $galleryName]), is_success: true, loggable: $gallery);

            // Redirige vers la page précédente avec un message flash de succès pour la restauration
            return redirect()->back()->with('success', __("{$this->backendLangPath}.restore.flash_success", ['name' => $galleryName]));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur lors de la tentative de restauration d'une galerie non trouvée
            $this->logActivity(type: 'warning', action: 'backend.galleries.restore', context: ['gallery_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur lors de la tentative de restauration avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.galleries.restore', context: ['gallery_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'erreur métier
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur lors de la tentative de restauration avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.galleries.restore', context: ['gallery_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_galleries_force_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupère la galerie supprimée par UUID ou échoue si non trouvée
            $gallery = Gallery::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Utilise le service pour supprimer définitivement la galerie
            $this->galleryService->backend_galleries_force_destroy($gallery);

            // Log de l'activité de suppression définitive réussie
            $this->logActivity(type: 'info', action: 'backend.galleries.force_destroy', context: ['gallery_uuid' => $gallery->uuid], message: null, activity: __("{$this->backendLangPath}.force_destroy.success"), is_success: true, loggable: $gallery);

            // Redirige vers la page précédente avec un message flash de succès pour la suppression définitive
            return redirect()->back()->with('success', __("{$this->backendLangPath}.force_destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur lors de la tentative de suppression définitive d'une galerie non trouvée
            $this->logActivity(type: 'warning', action: 'backend.galleries.force_destroy', context: ['gallery_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur lors de la tentative de suppression définitive avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.galleries.force_destroy', context: ['gallery_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'erreur métier
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur lors de la tentative de suppression définitive avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.galleries.force_destroy', context: ['gallery_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'échec de la suppression définitive
            return redirect()->back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_error"));
        }
    }
}
