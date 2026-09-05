<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\BlogPostTranslation;
use App\Services\V1\Controllers\BlogPostTranslationService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

use function __;

class BlogPostTranslationController extends Controller
{
    use LogActivity;

    public function __construct(
        protected BlogPostTranslationService $blogPostTranslationService
    ) {}

    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/blogs/posts/messages/posts-messages-controller.posts';

    // ─────────────────────────────────────────────
    // DESTROY (soft delete d'une traduction)
    // ─────────────────────────────────────────────
    public function backend_blog_post_translations_destroy(BlogPostTranslation $translation): RedirectResponse
    {
        try {

            // Traduction résolue par le binding implicite sur uuid (404 automatique si introuvable)
            // Suppression via le service (soft delete) — le service vérifie si c'est la dernière traduction
            $this->blogPostTranslationService->destroy($translation);

            // Log de la suppression réussie
            $this->logActivity(type: 'info', action: 'backend.blog-post-translations.destroy', context: ['translation_uuid' => $translation->uuid], message: null, activity: __("{$this->langPath}.translations.destroy.success", ['uuid' => $translation->uuid]), status: true);

            // Redirige vers la page précédente avec un message flash de succès pour la suppression
            return back()->with('success', __("{$this->langPath}.translations.destroy.flash_success"));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur lors de la tentative de suppression avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.blog-post-translations.destroy', context: ['translation_uuid' => $translation->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.translations.destroy.warning_meta", ['uuid' => $translation->uuid]), status: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'erreur métier
            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            // Log de l'erreur lors de la tentative de suppression avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.blog-post-translations.destroy', context: ['translation_uuid' => $translation->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.translations.destroy.error", ['uuid' => $translation->uuid]), status: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'échec de la suppression
            return back()->with('error', __("{$this->langPath}.translations.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED (Corbeille des traductions d'un article)
    // ─────────────────────────────────────────────
    public function backend_blog_post_translations_trashed(BlogPost $post): View
    {
        try {

            // Article résolu par le binding implicite sur uuid (route {blogPost} avec ->withTrashed())
            $postTitle = $post->active_translation?->title ?? ''; // Récupère le titre de l'article pour les logs et les messages flash

            // Récupère toutes les traductions supprimées (soft deleted) de cet article
            $blogPostTranslations = BlogPostTranslation::onlyTrashed()->where('post_id', $post->id)->get();

            // Log de l'accès à la corbeille des traductions pour cet article
            $this->logActivity(type: 'info', action: 'backend.blog-post-translations.trashed', context: ['post_uuid' => $post->uuid, 'post_title' => $postTitle], message: null, activity: __("{$this->langPath}.translations.trashed.accessed", ['name' => $postTitle]), status: true);

            // Retourne la vue avec les traductions supprimées et l'article correspondant
            return view('v1.backend.pages.blogs.posts.translations.trashed', compact('post', 'blogPostTranslations'));

        } catch (\Exception $e) {

            //  Log de l'erreur lors de la tentative d'accès à la corbeille des traductions pour un article avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.blog-post-translations.trashed', context: ['post_uuid' => $post->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.translations.trashed.error", ['uuid' => $post->uuid]), status: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_blog_post_translations_restore(string $uuid): RedirectResponse
    {
        try {

            // Récupère la traduction supprimée (soft deleted) par UUID ou échoue si non trouvée
            $translation = BlogPostTranslation::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Restaure la traduction via le service
            $this->blogPostTranslationService->restore($translation);

            // Log de la restauration réussie
            $this->logActivity(type: 'info', action: 'backend.blog-post-translations.restore', context: ['translation_uuid' => $uuid], message: null, activity: __("{$this->langPath}.translations.restore.success", ['uuid' => $uuid]), status: true);

            // Redirige vers la page précédente avec un message flash de succès pour la restauration
            return back()->with('success', __("{$this->langPath}.translations.restore.flash_success"));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur lors de la tentative de restauration d'une traduction non trouvée
            $this->logActivity(type: 'warning', action: 'backend.blog-post-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.translations.restore.warning", ['uuid' => $uuid]), status: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour la traduction introuvable
            return back()->with('error', __("{$this->langPath}.translations.restore.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur lors de la tentative de restauration avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.blog-post-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.translations.restore.warning_meta", ['uuid' => $uuid]), status: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'erreur métier
            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            // Log de l'erreur lors de la tentative de restauration avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.blog-post-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.translations.restore.error", ['uuid' => $uuid]), status: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'échec de la restauration
            return back()->with('error', __("{$this->langPath}.translations.restore.flash_error"));
        }

    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_blog_post_translations_force_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupère la traduction supprimée (soft deleted) par UUID ou échoue si non trouvée
            $translation = BlogPostTranslation::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Suppression définitive via le service — le service vérifie si c'est la dernière traduction
            $this->blogPostTranslationService->forceDestroy($translation);

            // Log de la suppression définitive réussie
            $this->logActivity(type: 'info', action: 'backend.blog-post-translations.force_destroy', context: ['translation_uuid' => $uuid], message: null, activity: __("{$this->langPath}.translations.force_destroy.success", ['uuid' => $uuid]), status: true);

            // Redirige vers la page précédente avec un message flash de succès pour la suppression définitive
            return back()->with('success', __("{$this->langPath}.translations.force_destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            // Log de l'erreur lors de la tentative de suppression définitive d'une traduction non trouvée
            $this->logActivity(type: 'warning', action: 'backend.blog-post-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.translations.force_destroy.warning", ['uuid' => $uuid]), status: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour la traduction introuvable
            return back()->with('error', __("{$this->langPath}.translations.force_destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur lors de la tentative de suppression définitive avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.blog-post-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.translations.force_destroy.warning_meta", ['uuid' => $uuid]), status: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'erreur métier
            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            // Log de l'erreur lors de la tentative de suppression définitive avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.blog-post-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.translations.force_destroy.error", ['uuid' => $uuid]), status: false);

        }
    }
}
