<?php

namespace App\Services\V1\Controllers;

use App\Models\BlogPostComment;
use Illuminate\Support\Facades\DB;

use function __;

class BlogPostCommentService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/blogs/comments/messages/comments-messages-service.comments';

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_blog_post_comments_activate(BlogPostComment $comment): void
    {
        DB::transaction(function () use ($comment) {

            if ($comment->is_visible) {
                throw new \InvalidArgumentException(__("{$this->langPath}.activate.already_approved"));
            }

            $comment->update([
                'is_visible' => true,
            ]);
        });
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_blog_post_comments_deactivate(BlogPostComment $comment): void
    {
        DB::transaction(function () use ($comment) {

            if (! $comment->is_visible) {
                throw new \InvalidArgumentException(__("{$this->langPath}.deactivate.already_disapproved"));
            }

            $comment->update([
                'is_visible' => false,
            ]);
        });
    }

    // ─────────────────────────────────────────────
    // DESTROY (soft delete)
    // ─────────────────────────────────────────────
    public function backend_blog_post_comments_destroy(BlogPostComment $comment): void
    {
        DB::transaction(function () use ($comment) {

            if ($comment->is_visible) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.visible_forbidden"));
            }

            if ($comment->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.already_deleted"));
            }
            destroy_function($comment);
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_blog_post_comments_restore(BlogPostComment $comment): void
    {
        DB::transaction(function () use ($comment) {
            if ($comment->trashed()) {
                restore_function($comment);
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.restore.not_trashed_forbidden"));
            }
        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_blog_post_comments_force_destroy(BlogPostComment $comment): void
    {
        DB::transaction(function () use ($comment) {
            if ($comment->trashed()) {
                force_destroy_function($comment);
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.not_trashed_forbidden"));
            }
        });
    }
}
