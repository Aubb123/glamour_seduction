<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\BlogPostComment;
use App\Services\V1\Controllers\BlogPostCommentService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

use function __;

class BlogPostCommentController extends Controller
{
    use LogActivity;

    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/blogs/comments/messages/comments-messages-controller.comments';

    // Injection de dépendance du service de gestion des commentaires
    public function __construct(protected BlogPostCommentService $blogPostCommentService) {}

    // ═══════════════════════════════════════════════════════════════
    // BACKEND
    // ═══════════════════════════════════════════════════════════════

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_blog_post_comments_index(): View
    {
        try {

            // Récupère tous les commentaires avec leurs relations et compteurs, triés par date de création décroissante
            $comments = BlogPostComment::withCount(['audits', 'activityLogs', 'replies'])->orderBy('created_at', 'desc')->get();

            // Log de l'activité
            $this->logActivity(type: 'info', action: 'backend.blog-post-comments.index', context: ['count' => $comments->count()], message: null, activity: __("{$this->langPath}.index.success"), is_success: true);

            // Retourne la vue avec les commentaires
            return view('v1.backend.pages.blogs.comments.index', compact('comments'));

        } catch (\Exception $e) {

            // Log de l'erreur
            $this->logActivity(type: 'error', action: 'backend.blog-post-comments.index', context: [], message: $e->getMessage(), activity: __("{$this->langPath}.index.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function backend_blog_post_comments_show(BlogPostComment $blogPostComment): View
    {
        try {

            // Instance bindée + compteurs
            $comment = $blogPostComment;
            $comment->loadCount(['audits', 'activityLogs', 'replies']);

            // Récupère les réponses liées à ce commentaire, triées par date de création décroissante
            $replies = $comment->replies()->orderBy('created_at', 'desc')->get();

            // Récupère le contenu du commentaire pour les logs
            $commentContent = Str::limit($comment->content, 50);

            // Log de l'activité de visualisation réussie
            $this->logActivity(type: 'info', action: 'backend.blog-post-comments.show', context: ['comment_uuid' => $comment->uuid, 'comment_content' => $commentContent], message: null, activity: __("{$this->langPath}.show.success", ['content' => $commentContent]), is_success: true, loggable: $comment);

            // Retourne la vue de détails avec le commentaire et ses réponses
            return view('v1.backend.pages.blogs.comments.show', compact('comment', 'replies'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur
            $this->logActivity(type: 'error', action: 'backend.blog-post-comments.show', context: ['comment_uuid' => $blogPostComment->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.show.error", ['uuid' => $blogPostComment->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // REPLIES
    // ─────────────────────────────────────────────
    public function backend_blog_post_comments_replies(BlogPostComment $blogPostComment): View
    {
        try {

            // Instance bindée + compteurs
            $comment = $blogPostComment;
            $comment->loadCount(['audits', 'activityLogs', 'replies']);

            // Récupère les réponses liées à ce commentaire, triées par date de création décroissante
            $replies = $comment->replies()->orderBy('created_at', 'desc')->get();

            // Récupère le contenu du commentaire pour les logs
            $commentContent = Str::limit($comment->content, 50);

            // Log de l'activité de visualisation réussie
            $this->logActivity(type: 'info', action: 'backend.blog-post-comments.replies', context: ['comment_uuid' => $comment->uuid, 'comment_content' => $commentContent], message: null, activity: __("{$this->langPath}.replies.success", ['content' => $commentContent]), is_success: true, loggable: $comment);

            return view('v1.backend.pages.blogs.comments.others.replies', compact('comment', 'replies'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur
            $this->logActivity(type: 'error', action: 'backend.blog-post-comments.replies', context: ['comment_uuid' => $blogPostComment->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.replies.error", ['uuid' => $blogPostComment->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // AUDITS
    // ─────────────────────────────────────────────
    public function backend_blog_post_comments_audits(BlogPostComment $blogPostComment): View
    {
        try {

            // Instance bindée + compteurs
            $comment = $blogPostComment;
            $comment->loadCount(['audits', 'activityLogs', 'replies']);

            $commentContent = $comment->content;

            $audits = $comment->audits()->with(['user', 'auditable'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.blog-post-comments.audits', context: ['comment_uuid' => $comment->uuid, 'comment_content' => $commentContent], message: null, activity: __("{$this->langPath}.audits.success", ['content' => $commentContent]), is_success: true, loggable: $comment);

            return view('v1.backend.pages.blogs.comments.others.audits', compact('comment', 'audits'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.blog-post-comments.audits', context: ['comment_uuid' => $blogPostComment->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.audits.error", ['uuid' => $blogPostComment->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVITY LOGS
    // ─────────────────────────────────────────────
    public function backend_blog_post_comments_activity_logs(BlogPostComment $blogPostComment): View
    {
        try {

            // Instance bindée + compteurs
            $comment = $blogPostComment;
            $comment->loadCount(['audits', 'activityLogs', 'replies']);

            $commentContent = $comment->content;

            $logs = $comment->activityLogs()->with(['user'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.blog-post-comments.activity-logs', context: ['comment_uuid' => $comment->uuid, 'comment_content' => $commentContent], message: null, activity: __("{$this->langPath}.activity-logs.success", ['content' => $commentContent]), is_success: true, loggable: $comment);

            return view('v1.backend.pages.blogs.comments.others.activity-logs', compact('comment', 'logs'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.blog-post-comments.activity-logs', context: ['comment_uuid' => $blogPostComment->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.activity-logs.error", ['uuid' => $blogPostComment->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_blog_post_comments_activate(Request $request, BlogPostComment $blogPostComment): RedirectResponse
    {
        try {

            // Active le commentaire via le service
            $this->blogPostCommentService->backend_blog_post_comments_activate($blogPostComment);

            $comment = $blogPostComment;
            $commentContent = Str::limit($comment->content, 50);

            // Log de l'activation
            $this->logActivity(type: 'info', action: 'backend.blog-post-comments.activate', context: ['comment_uuid' => $comment->uuid, 'comment_content' => $commentContent], message: null, activity: __("{$this->langPath}.activate.success", ['content' => $commentContent]), is_success: true, loggable: $comment);

            return back()->with('success', __("{$this->langPath}.activate.flash_success", ['content' => $commentContent]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.blog-post-comments.activate', context: ['comment_uuid' => $blogPostComment->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.activate.warning_meta", ['uuid' => $blogPostComment->uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.blog-post-comments.activate', context: ['comment_uuid' => $blogPostComment->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.activate.error", ['uuid' => $blogPostComment->uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.activate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_blog_post_comments_deactivate(Request $request, BlogPostComment $blogPostComment): RedirectResponse
    {
        try {

            // Désactive le commentaire via le service
            $this->blogPostCommentService->backend_blog_post_comments_deactivate($blogPostComment);

            $comment = $blogPostComment;
            $commentContent = Str::limit($comment->content, 50);

            // Log de la désactivation
            $this->logActivity(type: 'info', action: 'backend.blog-post-comments.deactivate', context: ['comment_uuid' => $comment->uuid, 'comment_content' => $commentContent], message: null, activity: __("{$this->langPath}.deactivate.success", ['content' => $commentContent]), is_success: true, loggable: $comment);

            return back()->with('success', __("{$this->langPath}.deactivate.flash_success", ['content' => $commentContent]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.blog-post-comments.deactivate', context: ['comment_uuid' => $blogPostComment->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.deactivate.warning_meta", ['uuid' => $blogPostComment->uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.blog-post-comments.deactivate', context: ['comment_uuid' => $blogPostComment->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.deactivate.error", ['uuid' => $blogPostComment->uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.deactivate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_blog_post_comments_destroy(BlogPostComment $blogPostComment): RedirectResponse
    {
        try {

            $comment = $blogPostComment;

            $commentContent = Str::limit($comment->content, 50);

            // Suppression via le service
            $this->blogPostCommentService->backend_blog_post_comments_destroy($comment);

            // Log de la suppression
            $this->logActivity(type: 'info', action: 'backend.blog-post-comments.destroy', context: ['comment_uuid' => $comment->uuid, 'comment_content' => $commentContent], message: null, activity: __("{$this->langPath}.destroy.success", ['content' => $commentContent]), is_success: true, loggable: $comment);

            return redirect()->route('backend.blog-post-comments.index')->with('success', __("{$this->langPath}.destroy.flash_success", ['content' => $commentContent]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la suppression du commentaire
            $this->logActivity(type: 'warning', action: 'backend.blog-post-comments.destroy', context: ['comment_uuid' => $blogPostComment->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.warning_meta", ['uuid' => $blogPostComment->uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.blog-post-comments.destroy', context: ['comment_uuid' => $blogPostComment->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.error", ['uuid' => $blogPostComment->uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_blog_post_comments_trashed(): View
    {
        try {

            // Récupère tous les commentaires supprimés, triés par date de suppression décroissante
            $comments = BlogPostComment::onlyTrashed()->withCount(['audits', 'activityLogs', 'replies'])->orderBy('deleted_at', 'desc')->get();

            // Log de l'activité
            $this->logActivity(type: 'info', action: 'backend.blog-post-comments.trashed', context: ['count' => $comments->count()], message: null, activity: __("{$this->langPath}.trashed.success"), is_success: true);

            return view('v1.backend.pages.blogs.comments.trashed', compact('comments'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.blog-post-comments.trashed', context: [], message: $e->getMessage(), activity: __("{$this->langPath}.trashed.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_blog_post_comments_restore(string $uuid): RedirectResponse
    {
        try {

            // Récupère le commentaire supprimé par UUID ou échoue
            $comment = BlogPostComment::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Restauration via le service
            $this->blogPostCommentService->backend_blog_post_comments_restore($comment);

            $commentContent = Str::limit($comment->content, 50);

            // Log de la restauration
            $this->logActivity(type: 'info', action: 'backend.blog-post-comments.restore', context: ['comment_uuid' => $comment->uuid, 'comment_content' => $commentContent], message: null, activity: __("{$this->langPath}.restore.success", ['content' => $commentContent]), is_success: true, loggable: $comment);

            return back()->with('success', __("{$this->langPath}.restore.flash_success", ['content' => $commentContent]));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.blog-post-comments.restore', context: ['comment_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.blog-post-comments.restore', context: ['comment_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.blog-post-comments.restore', context: ['comment_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_blog_post_comments_force_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupère le commentaire supprimé par UUID ou échoue
            $comment = BlogPostComment::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Suppression définitive via le service
            $this->blogPostCommentService->backend_blog_post_comments_force_destroy($comment);

            // Log de la suppression définitive
            $this->logActivity(type: 'info', action: 'backend.blog-post-comments.force_destroy', context: ['comment_uuid' => $comment->uuid], message: null, activity: __("{$this->langPath}.force_destroy.success"), is_success: true, loggable: $comment);

            return back()->with('success', __("{$this->langPath}.force_destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.blog-post-comments.force_destroy', context: ['comment_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.blog-post-comments.force_destroy', context: ['comment_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.blog-post-comments.force_destroy', context: ['comment_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.force_destroy.flash_error"));
        }
    }
}
