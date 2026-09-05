<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\BlogPostCommentReply;
use App\Services\V1\Controllers\BlogPostCommentReplyService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

use function __;

class BlogPostCommentReplyController extends Controller
{
    use LogActivity;

    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/blogs/comment-replies/messages/replies-messages-controller.replies';

    // Injection de dépendance du service de gestion des réponses aux commentaires
    public function __construct(protected BlogPostCommentReplyService $blogPostCommentReplyService) {}

    // ═══════════════════════════════════════════════════════════════
    // BACKEND
    // ═══════════════════════════════════════════════════════════════

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_blog_post_comment_replies_index(): View
    {
        try {

            // Récupère toutes les réponses avec leurs relations, triées par date de création décroissante
            $replies = BlogPostCommentReply::withCount(['audits', 'activityLogs'])->orderBy('created_at', 'desc')->get();

            // Log de l'activité
            $this->logActivity(type: 'info', action: 'backend.blog-post-comment-replies.index', context: ['count' => $replies->count()], message: null, activity: __("{$this->langPath}.index.success"), is_success: true);

            // Retourne la vue avec les réponses
            return view('v1.backend.pages.blogs.replies.index', compact('replies'));

        } catch (\Exception $e) {

            // Log de l'erreur
            $this->logActivity(type: 'error', action: 'backend.blog-post-comment-replies.index', context: [], message: $e->getMessage(), activity: __("{$this->langPath}.index.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function backend_blog_post_comment_replies_show(BlogPostCommentReply $blogPostCommentReply): View
    {
        try {

            // Instance bindée + relations et compteurs
            $reply = $blogPostCommentReply;
            $reply->load(['comment', 'user'])->loadCount(['audits', 'activityLogs']);

            // Récupère le contenu de la réponse pour les logs
            $replyContent = Str::limit($reply->content, 50);

            // Log de l'activité de visualisation réussie
            $this->logActivity(type: 'info', action: 'backend.blog-post-comment-replies.show', context: ['reply_uuid' => $reply->uuid, 'reply_content' => $replyContent], message: null, activity: __("{$this->langPath}.show.success", ['content' => $replyContent]), is_success: true, loggable: $reply);

            // Retourne la vue de détails avec la réponse
            return view('v1.backend.pages.blogs.replies.show', compact('reply'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur
            $this->logActivity(type: 'error', action: 'backend.blog-post-comment-replies.show', context: ['reply_uuid' => $blogPostCommentReply->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.show.error", ['uuid' => $blogPostCommentReply->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_blog_post_comment_replies_activate(Request $request, BlogPostCommentReply $blogPostCommentReply): RedirectResponse
    {
        try {

            // Active la réponse via le service
            $this->blogPostCommentReplyService->activate($blogPostCommentReply);

            $reply = $blogPostCommentReply;
            $replyContent = Str::limit($reply->content, 50);

            // Log de l'activation
            $this->logActivity(type: 'info', action: 'backend.blog-post-comment-replies.activate', context: ['reply_uuid' => $reply->uuid, 'reply_content' => $replyContent], message: null, activity: __("{$this->langPath}.activate.success", ['content' => $replyContent]), is_success: true, loggable: $reply);

            return back()->with('success', __("{$this->langPath}.activate.flash_success", ['content' => $replyContent]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.blog-post-comment-replies.activate', context: ['reply_uuid' => $blogPostCommentReply->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.activate.warning_meta", ['uuid' => $blogPostCommentReply->uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.blog-post-comment-replies.activate', context: ['reply_uuid' => $blogPostCommentReply->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.activate.error", ['uuid' => $blogPostCommentReply->uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.activate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_blog_post_comment_replies_deactivate(Request $request, BlogPostCommentReply $blogPostCommentReply): RedirectResponse
    {
        try {

            // Désactive la réponse via le service
            $this->blogPostCommentReplyService->deactivate($blogPostCommentReply);

            $reply = $blogPostCommentReply;
            $replyContent = Str::limit($reply->content, 50);

            // Log de la désactivation
            $this->logActivity(type: 'info', action: 'backend.blog-post-comment-replies.deactivate', context: ['reply_uuid' => $reply->uuid, 'reply_content' => $replyContent], message: null, activity: __("{$this->langPath}.deactivate.success", ['content' => $replyContent]), is_success: true, loggable: $reply);

            return back()->with('success', __("{$this->langPath}.deactivate.flash_success", ['content' => $replyContent]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.blog-post-comment-replies.deactivate', context: ['reply_uuid' => $blogPostCommentReply->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.deactivate.warning_meta", ['uuid' => $blogPostCommentReply->uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.blog-post-comment-replies.deactivate', context: ['reply_uuid' => $blogPostCommentReply->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.deactivate.error", ['uuid' => $blogPostCommentReply->uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.deactivate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_blog_post_comment_replies_destroy(BlogPostCommentReply $blogPostCommentReply): RedirectResponse
    {
        try {

            $reply = $blogPostCommentReply;

            $replyContent = Str::limit($reply->content, 50);

            // Suppression via le service
            $this->blogPostCommentReplyService->destroy($reply);

            // Log de la suppression
            $this->logActivity(type: 'info', action: 'backend.blog-post-comment-replies.destroy', context: ['reply_uuid' => $reply->uuid, 'reply_content' => $replyContent], message: null, activity: __("{$this->langPath}.destroy.success", ['content' => $replyContent]), is_success: true, loggable: $reply);

            return redirect()->route('backend.blog-post-comment-replies.index')->with('success', __("{$this->langPath}.destroy.flash_success", ['content' => $replyContent]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la suppression de la réponse
            $this->logActivity(type: 'warning', action: 'backend.blog-post-comment-replies.destroy', context: ['reply_uuid' => $blogPostCommentReply->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.warning_meta", ['uuid' => $blogPostCommentReply->uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.blog-post-comment-replies.destroy', context: ['reply_uuid' => $blogPostCommentReply->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.error", ['uuid' => $blogPostCommentReply->uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_blog_post_comment_replies_trashed(): View
    {
        try {

            // Récupère toutes les réponses supprimées, triées par date de suppression décroissante
            $replies = BlogPostCommentReply::onlyTrashed()->withCount(['audits', 'activityLogs'])->orderBy('deleted_at', 'desc')->get();

            // Log de l'activité
            $this->logActivity(type: 'info', action: 'backend.blog-post-comment-replies.trashed', context: ['count' => $replies->count()], message: null, activity: __("{$this->langPath}.trashed.success"), is_success: true);

            return view('v1.backend.pages.blogs.replies.trashed', compact('replies'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.blog-post-comment-replies.trashed', context: [], message: $e->getMessage(), activity: __("{$this->langPath}.trashed.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_blog_post_comment_replies_restore(string $uuid): RedirectResponse
    {
        try {

            // Récupère la réponse supprimée par UUID ou échoue
            $reply = BlogPostCommentReply::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Restauration via le service
            $this->blogPostCommentReplyService->restore($reply);

            $replyContent = Str::limit($reply->content, 50);

            // Log de la restauration
            $this->logActivity(type: 'info', action: 'backend.blog-post-comment-replies.restore', context: ['reply_uuid' => $reply->uuid, 'reply_content' => $replyContent], message: null, activity: __("{$this->langPath}.restore.success", ['content' => $replyContent]), is_success: true, loggable: $reply);

            return back()->with('success', __("{$this->langPath}.restore.flash_success", ['content' => $replyContent]));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.blog-post-comment-replies.restore', context: ['reply_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.blog-post-comment-replies.restore', context: ['reply_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.blog-post-comment-replies.restore', context: ['reply_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_blog_post_comment_replies_force_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupère la réponse supprimée par UUID ou échoue
            $reply = BlogPostCommentReply::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Suppression définitive via le service
            $this->blogPostCommentReplyService->forceDestroy($reply);

            // Log de la suppression définitive
            $this->logActivity(type: 'info', action: 'backend.blog-post-comment-replies.force_destroy', context: ['reply_uuid' => $reply->uuid], message: null, activity: __("{$this->langPath}.force_destroy.success"), is_success: true, loggable: $reply);

            return back()->with('success', __("{$this->langPath}.force_destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.blog-post-comment-replies.force_destroy', context: ['reply_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.blog-post-comment-replies.force_destroy', context: ['reply_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.blog-post-comment-replies.force_destroy', context: ['reply_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.force_destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // AUDITS
    // ─────────────────────────────────────────────
    public function backend_blog_post_comment_replies_audits(BlogPostCommentReply $blogPostCommentReply): View
    {
        try {

            // Instance bindée + compteurs
            $reply = $blogPostCommentReply;
            $reply->loadCount(['audits', 'activityLogs']);

            $replyContent = $reply->content;

            $audits = $reply->audits()->with(['user', 'auditable'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.blog-post-comment-replies.audits', context: ['reply_uuid' => $reply->uuid, 'reply_content' => $replyContent], message: null, activity: __("{$this->langPath}.audits.success", ['content' => $replyContent]), is_success: true, loggable: $reply);

            return view('v1.backend.pages.blogs.replies.others.audits', compact('reply', 'audits'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.blog-post-comment-replies.audits', context: ['reply_uuid' => $blogPostCommentReply->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.audits.error", ['uuid' => $blogPostCommentReply->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVITY LOGS
    // ─────────────────────────────────────────────
    public function backend_blog_post_comment_replies_activity_logs(BlogPostCommentReply $blogPostCommentReply): View
    {
        try {

            // Instance bindée + compteurs
            $reply = $blogPostCommentReply;
            $reply->loadCount(['audits', 'activityLogs']);

            $replyContent = $reply->content;

            $logs = $reply->activityLogs()->with(['user'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.blog-post-comment-replies.activity-logs', context: ['reply_uuid' => $reply->uuid, 'reply_content' => $replyContent], message: null, activity: __("{$this->langPath}.activity-logs.success", ['content' => $replyContent]), is_success: true, loggable: $reply);

            return view('v1.backend.pages.blogs.replies.others.activity-logs', compact('reply', 'logs'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.blog-post-comment-replies.activity-logs', context: ['reply_uuid' => $blogPostCommentReply->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.activity-logs.error", ['uuid' => $blogPostCommentReply->uuid]), is_success: false);

            abort(500);
        }
    }
}
