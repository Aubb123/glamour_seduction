<?php

namespace App\Models;

use App\Models\BlogPostComment;
use App\Models\User;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogPostCommentReply extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    // Les réponses de commentaires ne gèrent pas de traductions séparées
    protected bool $hasTranslations = false;

    // Nom de la table associée aux réponses de commentaires
    protected $table = 'blog_post_comment_replies';

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'comment_id',
        'user_id',
        'content',
        'is_visible',
    ];

    // Casts pour les attributs du modèle
    protected $casts = [
        'is_visible' => 'boolean',
    ];

    // Clé de route utilisée pour le binding implicite ({blogPostCommentReply} dans les URLs)
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Relation vers le commentaire parent de cette réponse.
     */
    public function comment(): BelongsTo
    {
        return $this->belongsTo(BlogPostComment::class, 'comment_id');
    }

    /**
     * Relation vers l'utilisateur auteur de la réponse.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
