<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Blogs\Tags\StoreBlogTagRequest;
use App\Http\Requests\V1\Blogs\Tags\UpdateBlogTagRequest;
use App\Models\BlogTag;
use App\Models\BlogTagTranslation;
use App\Services\V1\Controllers\BlogTagService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

use function __;

class BlogTagController extends Controller
{
    use LogActivity; // Trait pour le logging d'activité

    protected string $langPath = 'v1/backend/views/pages/blogs/tags/messages/tags-messages-controller.tags';

    public function __construct(protected BlogTagService $blogTagService) {}

    // ═══════════════════════════════════════════════════════════════
    // BACKEND
    // ═══════════════════════════════════════════════════════════════

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_blog_tags_index(): View
    {
        try {

            // ✅ Récupère tous les tags avec leurs traductions, triés par sort_order décroissant
            $tags = BlogTag::withActiveTranslation()->withCount(['posts', 'audits', 'activityLogs'])->orderBy('sort_order', 'desc')->get();

            // Log de l'accès à la liste des tags
            $this->logActivity(type: 'info', action: 'backend.blog-tags.index', context: ['count' => $tags->count()], message: null, activity: __("{$this->langPath}.index.success"), is_success: true);

            // Affiche la vue avec les tags récupérés
            return view('v1.backend.pages.blogs.tags.index', compact('tags'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur avec le message d'exception
            $this->logActivity(type: 'error', action: 'backend.blog-tags.index', context: [], message: $e->getMessage(), activity: __("{$this->langPath}.index.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────
    public function backend_blog_tags_create(): View
    {
        try {

            // Récupère les langues actives pour les passer à la vue
            $langs = getActiveLangs()['langs'];

            // Log de l'accès à la page de création d'un tag
            $this->logActivity(type: 'info', action: 'backend.blog-tags.create', context: [], message: null, activity: __("{$this->langPath}.create.success"), is_success: true);

            // Affiche la vue de création d'un tag
            return view('v1.backend.pages.blogs.tags.create', compact('langs'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de l'accès à la page de création d'un tag
            $this->logActivity(type: 'error', action: 'backend.blog-tags.create', context: [], message: $e->getMessage(), activity: __("{$this->langPath}.create.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_blog_tags_store(StoreBlogTagRequest $request): RedirectResponse
    {
        try {

            // Création du tag via le service, en passant les données validées du formulaire
            $tag = $this->blogTagService->store($request->validated());

            // Log de la création du tag avec le nom du tag créé
            $this->logActivity(type: 'info', action: 'backend.blog-tags.store', context: ['tag_uuid' => $tag->uuid, 'tag_name' => $tag->active_translation?->name], message: null, activity: __("{$this->langPath}.store.success", ['name' => $tag->active_translation?->name ?? '']), is_success: true, loggable: $tag);

            // Redirection vers la page de détail du tag avec un message de succès
            return redirect()->route('backend.blog-tags.show', ['blogTag' => $tag->uuid, 'slug' => $tag->active_translation?->slug ?? 'null'])->with('success', __("{$this->langPath}.store.flash_success", ['name' => $tag->active_translation?->name ?? '']));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la création du tag
            $this->logActivity(type: 'warning', action: 'backend.blog-tags.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->langPath}.store.error"), is_success: false);

            // Redirection avec le message métier explicite
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la création du tag
            $this->logActivity(type: 'error', action: 'backend.blog-tags.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->langPath}.store.error"), is_success: false);

            // Redirection avec un message d'erreur en cas de problème, en conservant les données du formulaire
            return redirect()->back()->with('error', __("{$this->langPath}.store.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // Résolution + vérification slug (helper privé)
    // ─────────────────────────────────────────────
    private function resolveTagWithSlugCheck(BlogTag $blogTag, string $slug, string $action, string $langKey): BlogTag
    {
        $expectedSlug = $blogTag->active_translation?->slug ?? 'null';

        if ($expectedSlug !== $slug) {
            // Log de l'erreur de correspondance du slug avec un message d'avertissement
            $this->logActivity(type: 'warning', action: $action, context: ['tag_uuid' => $blogTag->uuid, 'tag_slug' => $slug, 'expected_slug' => $expectedSlug], message: null, activity: __("{$this->langPath}.{$langKey}.slug_mismatch"), is_success: false, loggable: $blogTag);

            abort(404);
        }

        return $blogTag;
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function backend_blog_tags_show(BlogTag $blogTag, string $slug): View
    {
        try {

            $langs = getActiveLangs()['langs']; // Récupère les langues actives pour les passer à la vue

            // Vérifie que le slug correspond
            $tag = $this->resolveTagWithSlugCheck($blogTag, $slug, 'backend.blog-tags.show', 'show');

            // Charge les compteurs (withCount)
            $tag->loadCount(['posts', 'audits', 'activityLogs']);

            // Récupère toutes les traductions supprimées (soft deleted) de ce tag
            $blogTagTranslations = BlogTagTranslation::onlyTrashed()->where('tag_id', $tag->id)->get();

            // Récupère les articles de blog associés à ce tag
            $posts = $tag->posts()->withActiveTranslation()->with(['cloudFiles'])->withCount(['audits', 'activityLogs'])->orderBy('sort_order', 'desc')->get();

            $tagName = $tag->active_translation?->name ?? __("{$this->langPath}.show.no_name");

            // Log de la consultation du détail d'un tag
            $this->logActivity(type: 'info', action: 'backend.blog-tags.show', context: ['tag_uuid' => $tag->uuid, 'tag_name' => $tagName], message: null, activity: __("{$this->langPath}.show.success", ['name' => $tagName]), is_success: true, loggable: $tag);

            // Retourne la vue du tag avec les données nécessaires
            return view('v1.backend.pages.blogs.tags.show', compact('tag', 'langs', 'blogTagTranslations', 'posts'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la consultation du détail d'un tag
            $this->logActivity(type: 'error', action: 'backend.blog-tags.show', context: ['tag_uuid' => $blogTag->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.show.error", ['uuid' => $blogTag->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // POSTS
    // ─────────────────────────────────────────────
    public function backend_blog_tags_posts(BlogTag $blogTag, string $slug): View
    {
        try {

            // Vérifie que le slug correspond
            $tag = $this->resolveTagWithSlugCheck($blogTag, $slug, 'backend.blog-tags.posts', 'posts');

            // Charge les compteurs (withCount)
            $tag->loadCount(['posts', 'audits', 'activityLogs']);

            // Récupère les articles de blog associés à ce tag
            $posts = $tag->posts()->withActiveTranslation()->with(['cloudFiles'])->withCount(['audits', 'activityLogs'])->orderBy('sort_order', 'desc')->get();

            $tagName = $tag->active_translation?->name ?? __("{$this->langPath}.posts.no_name");

            // Log de la consultation des articles du tag
            $this->logActivity(type: 'info', action: 'backend.blog-tags.posts', context: ['tag_uuid' => $tag->uuid, 'tag_name' => $tagName], message: null, activity: __("{$this->langPath}.posts.success", ['name' => $tagName]), is_success: true, loggable: $tag);

            // Retourne la vue des articles du tag
            return view('v1.backend.pages.blogs.tags.others.posts', compact('tag', 'posts'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la consultation des articles d'un tag
            $this->logActivity(type: 'error', action: 'backend.blog-tags.posts', context: ['tag_uuid' => $blogTag->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.posts.error", ['uuid' => $blogTag->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // AUDITS
    // ─────────────────────────────────────────────
    public function backend_blog_tags_audits(BlogTag $blogTag, string $slug): View
    {
        try {

            // Vérifie que le slug correspond
            $tag = $this->resolveTagWithSlugCheck($blogTag, $slug, 'backend.blog-tags.audits', 'audits');

            // Charge les compteurs (withCount)
            $tag->loadCount(['posts', 'audits', 'activityLogs']);

            $tagName = $tag->active_translation?->name ?? '';

            $audits = $tag->audits()->with(['user', 'auditable'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.blog-tags.audits', context: ['tag_uuid' => $tag->uuid, 'tag_name' => $tagName], message: null, activity: __("{$this->langPath}.audits.success", ['name' => $tagName]), is_success: true, loggable: $tag);

            return view('v1.backend.pages.blogs.tags.others.audits', compact('tag', 'audits'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.blog-tags.audits', context: ['tag_uuid' => $blogTag->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.audits.error", ['uuid' => $blogTag->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVITY LOGS
    // ─────────────────────────────────────────────
    public function backend_blog_tags_activity_logs(BlogTag $blogTag, string $slug): View
    {
        try {

            // Vérifie que le slug correspond
            $tag = $this->resolveTagWithSlugCheck($blogTag, $slug, 'backend.blog-tags.activity-logs', 'activity-logs');

            // Charge les compteurs (withCount)
            $tag->loadCount(['posts', 'audits', 'activityLogs']);

            $tagName = $tag->active_translation?->name ?? '';

            $logs = $tag->activityLogs()->with(['user'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.blog-tags.activity-logs', context: ['tag_uuid' => $tag->uuid, 'tag_name' => $tagName], message: null, activity: __("{$this->langPath}.activity-logs.success", ['name' => $tagName]), is_success: true, loggable: $tag);

            return view('v1.backend.pages.blogs.tags.others.activity-logs', compact('tag', 'logs'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.blog-tags.activity-logs', context: ['tag_uuid' => $blogTag->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.activity-logs.error", ['uuid' => $blogTag->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────
    public function backend_blog_tags_edit(BlogTag $blogTag): View
    {
        try {

            $langs = getActiveLangs()['langs']; // Récupère les langues actives pour les passer à la vue

            // Log de l'accès à la page de modification d'un tag
            $this->logActivity(type: 'info', action: 'backend.blog-tags.edit', context: ['tag_uuid' => $blogTag->uuid, 'tag_name' => $blogTag->active_translation?->name], message: null, activity: __("{$this->langPath}.edit.success", ['name' => $blogTag->active_translation?->name ?? '']), is_success: true, loggable: $blogTag);

            $tag = $blogTag;

            // Retourne la vue d'édition du tag avec les données nécessaires
            return view('v1.backend.pages.blogs.tags.edit', compact('tag', 'langs'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de l'accès à la page de modification d'un tag
            $this->logActivity(type: 'error', action: 'backend.blog-tags.edit', context: ['tag_uuid' => $blogTag->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.edit.error", ['uuid' => $blogTag->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_blog_tags_update(UpdateBlogTagRequest $request, BlogTag $blogTag): RedirectResponse
    {
        try {

            // Mise à jour du tag via le service, en passant les données validées du formulaire
            $this->blogTagService->update($blogTag, $request->validated());

            // Récupère une instance fraîche pour capturer les vrais changements
            $tag = $blogTag->refresh();

            $tagName = $tag->active_translation?->name ?? '';

            // Log de la mise à jour du tag avec le uuid et les changements effectués
            $this->logActivity(type: 'info', action: 'backend.blog-tags.update', context: ['tag_uuid' => $tag->uuid, 'tag_name' => $tagName, 'changes' => $tag->getChanges()], message: null, activity: __("{$this->langPath}.update.success", ['name' => $tagName]), is_success: true, loggable: $tag);

            // Redirection vers la page de détail du tag avec un message de succès
            return redirect()->route('backend.blog-tags.show', ['blogTag' => $tag->uuid, 'slug' => $tag->active_translation?->slug ?? 'null'])->with('success', __("{$this->langPath}.update.flash_success", ['name' => $tagName]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la mise à jour du tag
            $this->logActivity(type: 'warning', action: 'backend.blog-tags.update', context: ['tag_uuid' => $blogTag->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.update.warning_meta", ['uuid' => $blogTag->uuid]), is_success: false);

            // Redirection avec le message métier explicite
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la mise à jour du tag
            $this->logActivity(type: 'error', action: 'backend.blog-tags.update', context: ['tag_uuid' => $blogTag->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.update.error", ['uuid' => $blogTag->uuid]), is_success: false);

            // Redirection avec un message d'erreur en cas de problème, en conservant les données du formulaire
            return back()->with('error', __("{$this->langPath}.update.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_blog_tags_activate(Request $request, BlogTag $blogTag): RedirectResponse
    {
        try {

            // Activation du tag via le service
            $this->blogTagService->activate($blogTag);

            $tag = $blogTag;
            $tagName = $tag->active_translation?->name ?? '';

            // Log de l'activation du tag
            $this->logActivity(type: 'info', action: 'backend.blog-tags.activate', context: ['tag_uuid' => $tag->uuid, 'tag_name' => $tagName], message: null, activity: __("{$this->langPath}.activate.success", ['name' => $tagName]), is_success: true, loggable: $tag);

            // Redirection avec un message de succès
            return back()->with('success', __("{$this->langPath}.activate.flash_success", ['name' => $tagName]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.blog-tags.activate', context: ['tag_uuid' => $blogTag->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.activate.warning_meta", ['uuid' => $blogTag->uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.blog-tags.activate', context: ['tag_uuid' => $blogTag->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.activate.error"), is_success: false);

            return back()->with('error', __("{$this->langPath}.activate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_blog_tags_deactivate(Request $request, BlogTag $blogTag): RedirectResponse
    {
        try {

            // Désactivation du tag via le service
            $this->blogTagService->deactivate($blogTag);

            $tag = $blogTag;
            $tagName = $tag->active_translation?->name ?? '';

            // Log de la désactivation du tag
            $this->logActivity(type: 'info', action: 'backend.blog-tags.deactivate', context: ['tag_uuid' => $tag->uuid, 'tag_name' => $tagName], message: null, activity: __("{$this->langPath}.deactivate.success", ['name' => $tagName]), is_success: true, loggable: $tag);

            // Redirection avec un message de succès
            return back()->with('success', __("{$this->langPath}.deactivate.flash_success", ['name' => $tagName]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.blog-tags.deactivate', context: ['tag_uuid' => $blogTag->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.deactivate.warning_meta", ['uuid' => $blogTag->uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.blog-tags.deactivate', context: ['tag_uuid' => $blogTag->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.deactivate.error"), is_success: false);

            return back()->with('error', __("{$this->langPath}.deactivate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_blog_tags_destroy(BlogTag $blogTag): RedirectResponse
    {
        try {

            $tag = $blogTag;

            $tagName = $tag->active_translation?->name ?? __("{$this->langPath}.destroy.unknown");

            // Suppression du tag via le service
            $this->blogTagService->destroy($blogTag);

            // Log de la suppression du tag
            $this->logActivity(type: 'info', action: 'backend.blog-tags.destroy', context: ['tag_uuid' => $tag->uuid, 'tag_name' => $tagName], message: null, activity: __("{$this->langPath}.destroy.success", ['name' => $tagName]), is_success: true, loggable: $tag);

            // Redirection vers la liste des tags avec un message de succès
            return redirect()->route('backend.blog-tags.index')->with('success', __("{$this->langPath}.destroy.flash_success", ['name' => $tagName]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la suppression du tag
            $this->logActivity(type: 'warning', action: 'backend.blog-tags.destroy', context: ['tag_uuid' => $blogTag->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.warning_meta", ['uuid' => $blogTag->uuid]), is_success: false);

            // Redirection vers la page précédente avec un message d'erreur métier explicite
            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la suppression du tag
            $this->logActivity(type: 'error', action: 'backend.blog-tags.destroy', context: ['tag_uuid' => $blogTag->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.error", ['uuid' => $blogTag->uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_blog_tags_trashed(): View
    {
        try {

            $tags = BlogTag::onlyTrashed()->withTrashedActiveTranslation()->withCount(['posts', 'audits', 'activityLogs'])->orderBy('deleted_at', 'desc')->get();

            // Log de la consultation de la corbeille des tags
            $this->logActivity(type: 'info', action: 'backend.blog-tags.trashed', context: ['count' => $tags->count()], message: null, activity: __("{$this->langPath}.trashed.success"), is_success: true);

            return view('v1.backend.pages.blogs.tags.trashed', compact('tags'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.blog-tags.trashed', context: [], message: $e->getMessage(), activity: __("{$this->langPath}.trashed.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_blog_tags_restore(string $uuid): RedirectResponse
    {
        try {

            // Récupère le tag supprimé par son UUID ou échoue
            $tag = BlogTag::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Restauration via le service
            $this->blogTagService->restore($tag);

            $tagName = $tag->active_translation?->name ?? '';

            // Log de la restauration du tag
            $this->logActivity(type: 'info', action: 'backend.blog-tags.restore', context: ['tag_uuid' => $tag->uuid, 'tag_name' => $tagName], message: null, activity: __("{$this->langPath}.restore.success", ['name' => $tagName]), is_success: true, loggable: $tag);

            return back()->with('success', __("{$this->langPath}.restore.flash_success", ['name' => $tagName]));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.blog-tags.restore', context: ['tag_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.blog-tags.restore', context: ['tag_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.blog-tags.restore', context: ['tag_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_blog_tags_force_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupère le tag supprimé par son UUID ou échoue
            $tag = BlogTag::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            $tagName = $tag->active_translation?->name ?? __("{$this->langPath}.force_destroy.unknown");

            // Suppression définitive via le service
            $this->blogTagService->forceDestroy($tag);

            // Log de la suppression définitive du tag
            $this->logActivity(type: 'info', action: 'backend.blog-tags.force_destroy', context: ['tag_uuid' => $tag->uuid, 'tag_name' => $tagName], message: null, activity: __("{$this->langPath}.force_destroy.success", ['name' => $tagName]), is_success: true, loggable: $tag);

            return back()->with('success', __("{$this->langPath}.force_destroy.flash_success", ['name' => $tagName]));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.blog-tags.force_destroy', context: ['tag_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.blog-tags.force_destroy', context: ['tag_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.blog-tags.force_destroy', context: ['tag_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.force_destroy.flash_error"));
        }
    }
}
