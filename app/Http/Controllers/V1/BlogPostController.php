<?php

namespace App\Http\Controllers\V1;

use App\Enums\Blogs\Posts\BlogPostStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Blogs\Posts\StoreBlogPostRequest;
use App\Http\Requests\V1\Blogs\Posts\UpdateBlogPostRequest;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogPostTranslation;
use App\Models\BlogTag;
use App\Services\V1\Controllers\BlogPostService;
use App\Traits\LogActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

use function __;

class BlogPostController extends Controller
{
    use LogActivity;

    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/blogs/posts/messages/posts-messages-controller.posts';

    // Injection de dépendance du service de gestion des articles
    public function __construct(protected BlogPostService $blogPostService) {}

    // ═══════════════════════════════════════════════════════════════
    // BACKEND
    // ═══════════════════════════════════════════════════════════════

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_blog_posts_index(): View
    {
        try {

            // Récupère tous les articles avec leur traduction active, catégorie et auteur, triés par ordre de tri décroissant
            $posts = BlogPost::withActiveTranslation()->with(['cloudFiles', 'category.translations.lang', 'user'])->withCount(['audits', 'activityLogs'])->orderBy('sort_order', 'desc')->get();

            // Log de l'activité
            $this->logActivity(type: 'info', action: 'backend.blog-posts.index', context: ['count' => $posts->count()], message: null, activity: __("{$this->backendLangPath}.index.success"), is_success: true);

            // Retourne la vue avec les articles
            return view('v1.backend.pages.blogs.posts.index', compact('posts'));

        } catch (\Exception $e) {

            // Log de l'erreur
            $this->logActivity(type: 'error', action: 'backend.blog-posts.index', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.index.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────
    public function backend_blog_posts_create(): View
    {
        try {

            // Récupère les langues actives, les catégories et les tags
            $langs = getActiveLangs()['langs'];

            //  Récupère les catégories et les tags actifs pour le formulaire d'édition
            $categories = BlogCategory::withActiveTranslation()->active()->get();
            $tags = BlogTag::withActiveTranslation()->active()->get();

            // Log de l'accès à la page de création
            $this->logActivity(type: 'info', action: 'backend.blog-posts.create', context: [], message: null, activity: __("{$this->backendLangPath}.create.success"), is_success: true);

            // Retourne la vue de création avec les données nécessaires
            return view('v1.backend.pages.blogs.posts.create', compact('langs', 'categories', 'tags'));

        } catch (\Exception $e) {

            // Log de l'erreur lors de l'accès à la page de création
            $this->logActivity(type: 'error', action: 'backend.blog-posts.create', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.create.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_blog_posts_store(StoreBlogPostRequest $request): RedirectResponse
    {
        try {

            // Utilise le service pour créer un nouvel article de blog avec les données validées
            $post = $this->blogPostService->backend_blog_posts_store($request->validated());

            // Récupère le titre de l'article pour les logs et les messages flash
            $postName = $post->active_translation?->title ?? '';

            //  Log de l'activité de création réussie
            $this->logActivity(type: 'info', action: 'backend.blog-posts.store', context: ['post_uuid' => $post->uuid, 'post_title' => $postName], message: null, activity: __("{$this->backendLangPath}.store.success", ['name' => $postName]), is_success: true, loggable: $post);

            // Redirige vers la page de détails de l'article avec un message flash de succès
            return redirect()->route('backend.blog-posts.show', ['blogPost' => $post->uuid, 'slug' => $post->active_translation?->slug ?? 'null'])->with('success', __("{$this->backendLangPath}.store.flash_success", ['name' => $postName]));

        } catch (\InvalidArgumentException $e) {

            //  Log de l'erreur de création avec un message d'avertissement
            $this->logActivity(type: 'warning', action: 'backend.blog-posts.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            // Redirige en arrière avec un message d'erreur et les données du formulaire
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur de création avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.blog-posts.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            // Redirige en arrière avec un message d'erreur et les données du formulaire
            return redirect()->back()->with('error', __("{$this->backendLangPath}.store.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // Résolution + vérification slug (helper privé)
    // ─────────────────────────────────────────────
    private function resolvePostWithSlugCheck(BlogPost $blogPost, string $slug, string $action, string $langKey): BlogPost
    {
        $expectedSlug = $blogPost->active_translation?->slug ?? 'null';

        if ($expectedSlug !== $slug) {
            // Log de l'erreur de correspondance du slug avec un message d'avertissement
            $this->logActivity(type: 'warning', action: $action, context: ['post_uuid' => $blogPost->uuid, 'post_slug' => $slug, 'expected_slug' => $expectedSlug], message: null, activity: __("{$this->backendLangPath}.{$langKey}.slug_mismatch"), is_success: false);

            abort(404);
        }

        return $blogPost;
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function backend_blog_posts_show(BlogPost $blogPost, string $slug): View
    {
        try {

            // Vérifie que le slug correspond
            $post = $this->resolvePostWithSlugCheck($blogPost, $slug, 'backend.blog-posts.show', 'show');

            // Charge les traductions non supprimées avec leur langue (pour l'affichage de toutes les langues)
            $post->load(['translations' => fn ($q) => $q->whereNull('deleted_at')->with('lang'), 'tags'])->loadCount(['audits', 'activityLogs']);

            // Récupère les langues actives pour l'affichage
            $langs = getActiveLangs()['langs'];

            // Récupère les traductions supprimées
            $blogPostTranslations = BlogPostTranslation::onlyTrashed()->where('post_id', $post->id)->get();

            // Récupère les commentaires liés à cet article, triés par date de création décroissante
            $comments = $post->comments()->orderBy('created_at', 'desc')->get();

            // Récupère le titre de l'article pour les logs et les messages flash
            $postTitle = $post->active_translation?->title ?? __("{$this->backendLangPath}.show.no_name");

            //  Log de l'activité de visualisation réussie
            $this->logActivity(type: 'info', action: 'backend.blog-posts.show', context: ['post_uuid' => $post->uuid, 'post_title' => $postTitle], message: null, activity: __("{$this->backendLangPath}.show.success", ['name' => $postTitle]), is_success: true, loggable: $post);

            // Retourne la vue de détails avec l'article, les langues, les traductions supprimées et les commentaires
            return view('v1.backend.pages.blogs.posts.show', compact('post', 'langs', 'blogPostTranslations', 'comments'));

        } catch (\Exception $e) {

            // Log de l'erreur de visualisation avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.blog-posts.show', context: ['post_uuid' => $blogPost->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.show.error", ['uuid' => $blogPost->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // AUDITS
    // ─────────────────────────────────────────────
    public function backend_blog_posts_audits(BlogPost $blogPost, string $slug): View
    {
        try {

            // Vérifie que le slug correspond
            $post = $this->resolvePostWithSlugCheck($blogPost, $slug, 'backend.blog-posts.audits', 'audits');

            // Charge la traduction active pour l'affichage du titre/slug
            $post->load(['translations' => fn ($q) => $q->whereNull('deleted_at')->with('lang')])->loadCount(['audits', 'activityLogs']);

            $postTitle = $post->active_translation?->title ?? '';

            $audits = $post->audits()->with(['user', 'auditable'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.blog-posts.audits', context: ['post_uuid' => $post->uuid, 'post_title' => $postTitle], message: null, activity: __("{$this->backendLangPath}.audits.success", ['name' => $postTitle]), is_success: true, loggable: $post);

            return view('v1.backend.pages.blogs.posts.others.audits', compact('post', 'audits'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.blog-posts.audits', context: ['post_uuid' => $blogPost->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.audits.error", ['uuid' => $blogPost->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVITY LOGS
    // ─────────────────────────────────────────────
    public function backend_blog_posts_activity_logs(BlogPost $blogPost, string $slug): View
    {
        try {

            // Vérifie que le slug correspond
            $post = $this->resolvePostWithSlugCheck($blogPost, $slug, 'backend.blog-posts.activity-logs', 'activity-logs');

            // Charge la traduction active pour l'affichage du titre/slug
            $post->load(['translations' => fn ($q) => $q->whereNull('deleted_at')->with('lang')])->loadCount(['audits', 'activityLogs']);

            $postTitle = $post->active_translation?->title ?? '';

            $logs = $post->activityLogs()->with(['user'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.blog-posts.activity-logs', context: ['post_uuid' => $post->uuid, 'post_title' => $postTitle], message: null, activity: __("{$this->backendLangPath}.activity-logs.success", ['name' => $postTitle]), is_success: true, loggable: $post);

            return view('v1.backend.pages.blogs.posts.others.activity-logs', compact('post', 'logs'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.blog-posts.activity-logs', context: ['post_uuid' => $blogPost->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activity-logs.error", ['uuid' => $blogPost->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────
    public function backend_blog_posts_edit(BlogPost $blogPost): View
    {
        try {

            // Récupère les langues actives, les catégories et les tags pour le formulaire d'édition
            $langs = getActiveLangs()['langs'];

            //  Récupère les catégories et les tags actifs pour le formulaire d'édition
            $categories = BlogCategory::withActiveTranslation()->active()->get();
            $tags = BlogTag::withActiveTranslation()->active()->get();

            // Récupère l'article de blog avec ses traductions et tags
            $post = $blogPost->load(['translations.lang', 'tags']);
            // Log de l'accès à la page d'édition réussie
            $this->logActivity(type: 'info', action: 'backend.blog-posts.edit', context: ['post_uuid' => $post->uuid, 'post_title' => $post->active_translation?->title], message: null, activity: __("{$this->backendLangPath}.edit.success", ['name' => $post->active_translation?->title ?? '']), is_success: true, loggable: $post);

            // Préparation des IDs des tags pour la vue
            $postTagIds = $post->tags->pluck('id')->toArray();

            $editMode = true;

            // Retourne la vue d'édition avec l'article, les langues, les catégories et les tags
            return view('v1.backend.pages.blogs.posts.edit', compact('post', 'langs', 'categories', 'tags', 'postTagIds', 'editMode'));

        } catch (\Exception $e) {

            // Log de l'erreur d'accès à la page d'édition avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.blog-posts.edit', context: ['post_uuid' => $blogPost->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.edit.error", ['uuid' => $blogPost->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_blog_posts_update(UpdateBlogPostRequest $request, BlogPost $blogPost): RedirectResponse
    {
        try {

            // Utilise le service pour mettre à jour l'article de blog avec les données validées
            $this->blogPostService->backend_blog_posts_update($blogPost, $request->validated());

            // Capturer les changements après la mise à jour (avant le refresh)
            $changes = $blogPost->getChanges();

            $blogPost->refresh()->load(['translations.lang']); // Rafraîchit l'instance du modèle pour obtenir les dernières données après la mise à jour

            $postTitle = $blogPost->active_translation?->title ?? ''; // Récupère le titre de l'article pour les logs et les messages flash

            // Log de l'activité de mise à jour réussie
            $this->logActivity(type: 'info', action: 'backend.blog-posts.update', context: ['post_uuid' => $blogPost->uuid, 'post_title' => $postTitle, 'changes' => $changes], message: null, activity: __("{$this->backendLangPath}.update.success", ['name' => $postTitle]), is_success: true, loggable: $blogPost);

            // Redirige vers la page de détails de l'article avec un message flash de succès
            return redirect()->route('backend.blog-posts.show', ['blogPost' => $blogPost->uuid, 'slug' => $blogPost->active_translation?->slug ?? 'null'])->with('success', __("{$this->backendLangPath}.update.flash_success", ['name' => $postTitle]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur de mise à jour avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.blog-posts.update', context: ['post_uuid' => $blogPost->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.warning_meta", ['uuid' => $blogPost->uuid]), is_success: false);

            // Redirige en arrière avec un message d'erreur et les données du formulaire
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur de mise à jour avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.blog-posts.update', context: ['post_uuid' => $blogPost->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.error", ['uuid' => $blogPost->uuid]), is_success: false);

            // Redirige en arrière avec un message d'erreur et les données du formulaire
            return redirect()->back()->with('error', __("{$this->backendLangPath}.update.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // CHANGE STATUS (draft, published, archived, scheduled)
    // ─────────────────────────────────────────────
    public function backend_blog_posts_change_status(Request $request, BlogPost $blogPost): RedirectResponse
    {
        try {

            $newStatus = BlogPostStatus::tryFrom($request->input('status', ''));

            if ($newStatus === null) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.change_status.invalid_status"));
            }

            // Ancien statut pour le log
            $oldStatus = $blogPost->status;

            // Utilise le service pour changer le statut de l'article
            $this->blogPostService->backend_blog_posts_change_status($blogPost, $newStatus, $request->input('published_at'));

            $blogPost->refresh()->load(['translations.lang']); // Rafraîchit pour obtenir le titre mis à jour

            $postName = $blogPost->active_translation?->title ?? ''; // Récupère le titre pour les logs et les messages flash

            // Log de l'activité de changement de statut réussie
            $this->logActivity(type: 'info', action: 'backend.blog-posts.change_status', context: ['post_uuid' => $blogPost->uuid, 'post_title' => $postName, 'old_status' => $oldStatus->value, 'new_status' => $newStatus->value], message: null, activity: __("{$this->backendLangPath}.change_status.success", ['name' => $postName, 'from' => $oldStatus->label(), 'to' => $newStatus->label()]), is_success: true, loggable: $blogPost);

            // Redirige vers la page précédente avec un message flash de succès
            return redirect()->back()->with('success', __("{$this->backendLangPath}.change_status.flash_success", ['name' => $postName, 'to' => $newStatus->label()]));

        } catch (\InvalidArgumentException $e) {
            // Log de l'erreur de changement de statut avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.blog-posts.change_status', context: ['post_uuid' => $blogPost->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.change_status.warning_meta", ['uuid' => $blogPost->uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'erreur métier
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {
            // Log de l'erreur de changement de statut avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.blog-posts.change_status', context: ['post_uuid' => $blogPost->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.change_status.error", ['uuid' => $blogPost->uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'échec du changement de statut
            return redirect()->back()->with('error', __("{$this->backendLangPath}.change_status.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY (soft delete)
    // ─────────────────────────────────────────────
    public function backend_blog_posts_destroy(BlogPost $blogPost): RedirectResponse
    {
        try {

            // Récupère le titre de l'article pour les logs et les messages flash
            $postTitle = $blogPost->active_translation?->title ?? __("{$this->backendLangPath}.destroy.unknown");

            // Utilise le service pour supprimer l'article de blog
            $this->blogPostService->backend_blog_posts_destroy($blogPost);

            // Log de l'activité de suppression réussie
            $this->logActivity(type: 'info', action: 'backend.blog-posts.destroy', context: ['post_uuid' => $blogPost->uuid, 'post_title' => $postTitle], message: null, activity: __("{$this->backendLangPath}.destroy.success", ['name' => $postTitle]), is_success: true, loggable: $blogPost);

            // Redirige vers la liste des articles avec un message flash de succès
            return redirect()->route('backend.blog-posts.index')->with('success', __("{$this->backendLangPath}.destroy.flash_success", ['name' => $postTitle]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur de suppression avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.blog-posts.destroy', context: ['post_uuid' => $blogPost->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning_meta", ['uuid' => $blogPost->uuid]), is_success: false);

            //  Redirige vers la page précédente avec un message flash d'erreur pour l'erreur métier
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur de suppression avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.blog-posts.destroy', context: ['post_uuid' => $blogPost->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.error", ['uuid' => $blogPost->uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'échec de la suppression
            return redirect()->back()->with('error', __("{$this->backendLangPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_blog_posts_trashed(): View
    {
        try {

            // Récupère tous les articles supprimés avec leur traduction active, triés par date de suppression décroissante
            $posts = BlogPost::onlyTrashed()->withTrashedActiveTranslation()->with(['cloudFiles'])->withCount(['audits', 'activityLogs'])->orderBy('deleted_at', 'desc')->get();

            // Log de l'activité d'affichage de la liste des articles supprimés réussie
            $this->logActivity(type: 'info', action: 'backend.blog-posts.trashed', context: ['count' => $posts->count()], message: null, activity: __("{$this->backendLangPath}.trashed.success"), is_success: true);

            // Retourne la vue avec les articles supprimés
            return view('v1.backend.pages.blogs.posts.trashed', compact('posts'));

        } catch (\Exception $e) {

            // Log de l'erreur lors de l'affichage de la liste des articles supprimés
            $this->logActivity(type: 'error', action: 'backend.blog-posts.trashed', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.trashed.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_blog_posts_restore(string $uuid): RedirectResponse
    {
        try {

            // Récupère l'article de blog supprimé par UUID ou échoue si non trouvé
            $blogPost = BlogPost::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Utilise le service pour restaurer l'article de blog
            $this->blogPostService->backend_blog_posts_restore($blogPost);

            $postTitle = $blogPost->active_translation?->title ?? '';  // Récupère le titre de l'article pour les logs et les messages flash

            // Log de l'activité de restauration réussie
            $this->logActivity(type: 'info', action: 'backend.blog-posts.restore', context: ['post_uuid' => $blogPost->uuid, 'post_title' => $postTitle], message: null, activity: __("{$this->backendLangPath}.restore.success", ['name' => $postTitle]), is_success: true, loggable: $blogPost);

            // Redirige vers la page précédente avec un message flash de succès pour la restauration
            return redirect()->back()->with('success', __("{$this->backendLangPath}.restore.flash_success", ['name' => $postTitle]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur lors de la tentative de restauration avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.blog-posts.restore', context: ['post_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'erreur métier
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            //  Log de l'erreur lors de la tentative de restauration avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.blog-posts.restore', context: ['post_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_blog_posts_force_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupère l'article de blog supprimé par UUID ou échoue si non trouvé
            $blogPost = BlogPost::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Récupère le titre de l'article pour les logs et les messages flash (avant suppression définitive)
            $postTitle = $blogPost->active_translation?->title ?? __("{$this->backendLangPath}.force_destroy.unknown");

            // Utilise le service pour supprimer définitivement l'article de blog
            $this->blogPostService->backend_blog_posts_force_destroy($blogPost);

            // Log de l'activité de suppression définitive réussie
            $this->logActivity(type: 'info', action: 'backend.blog-posts.force_destroy', context: ['post_uuid' => $blogPost->uuid, 'post_title' => $postTitle], message: null, activity: __("{$this->backendLangPath}.force_destroy.success", ['name' => $postTitle]), is_success: true, loggable: $blogPost);

            // Redirige vers la page précédente avec un message flash de succès pour la suppression définitive
            return redirect()->back()->with('success', __("{$this->backendLangPath}.force_destroy.flash_success", ['name' => $postTitle]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur lors de la tentative de suppression définitive avec un message d'avertissement spécifique
            $this->logActivity(type: 'warning', action: 'backend.blog-posts.force_destroy', context: ['post_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'erreur métier
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur lors de la tentative de suppression définitive avec un message d'erreur
            $this->logActivity(type: 'error', action: 'backend.blog-posts.force_destroy', context: ['post_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            // Redirige vers la page précédente avec un message flash d'erreur pour l'échec de la suppression définitive
            return redirect()->back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_error"));
        }
    }
}
