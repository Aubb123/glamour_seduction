<?php

namespace App\Services\V1\Controllers;

use App\Models\BlogPostCommentReply;
use Illuminate\Support\Facades\DB;

use function __;

class BlogPostCommentReplyService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/blogs/comment-replies/messages/replies-messages-service.replies';

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_blog_post_comment_replies_activate(BlogPostCommentReply $reply): void
    {
        DB::transaction(function () use ($reply) {

            // Vérification que la réponse n'est pas déjà active
            if ($reply->is_visible) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.activate.already_approved"));
            }

            // Activation de la réponse sélectionnée
            $reply->update([
                'is_visible' => true,
            ]);
        });
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_blog_post_comment_replies_deactivate(BlogPostCommentReply $reply): void
    {
        DB::transaction(function () use ($reply) {

            // Vérification que la réponse n'est pas déjà inactive
            if (! $reply->is_visible) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.deactivate.already_disapproved"));
            }

            // Désactivation de la réponse sélectionnée
            $reply->update([
                'is_visible' => false,
            ]);
        });
    }

    // ─────────────────────────────────────────────
    // DESTROY (soft delete)
    // ─────────────────────────────────────────────
    public function backend_blog_post_comment_replies_destroy(BlogPostCommentReply $reply): void
    {
        DB::transaction(function () use ($reply) {

            // Vérification : empêcher la suppression d'une réponse visible
            if ($reply->is_visible) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.visible_forbidden"));
            }

            // Suppression simple (soft delete)
            if ($reply->trashed()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.already_deleted"));
            }
            destroy_function($reply);
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_blog_post_comment_replies_restore(BlogPostCommentReply $reply): void
    {
        DB::transaction(function () use ($reply) {

            // Restauration simple (restore)
            if ($reply->trashed()) {
                restore_function($reply);
            } else {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.restore.not_trashed_forbidden"));
            }
        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_blog_post_comment_replies_force_destroy(BlogPostCommentReply $reply): void
    {
        DB::transaction(function () use ($reply) {

            // Suppression définitive (forceDelete)
            if ($reply->trashed()) {
                force_destroy_function($reply);
            } else {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.not_trashed_forbidden"));
            }
        });
    }
}
