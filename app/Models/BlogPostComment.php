<?php

namespace App\Models;

use App\Models\BlogPost;
use App\Models\BlogPostCommentReply;
use App\Models\User;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogPostComment extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    // Les commentaires ne gèrent pas de traductions séparées
    protected bool $hasTranslations = false;

    // Nom de la table associée aux commentaires de blog
    protected $table = 'blog_post_comments';

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'post_id',
        'user_id',
        'content',
        'is_visible',
    ];

    // Casts pour les attributs du modèle
    protected $casts = [
        'is_visible' => 'boolean',
    ];

    // Relations enfants dont la présence bloque la suppression de ce commentaire (delete / force_delete) lors d'une supression temporaire (BackendHelper::destroy_function) ou définitive (BackendHelper::force_destroy_function)
    protected array $dependentRelations = ['replies'];

    // Clé de route utilisée pour le binding implicite ({blogPostComment} dans les URLs)
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Relation vers le post auquel appartient ce commentaire.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(BlogPost::class, 'post_id');
    }

    /**
     * Relation vers l'utilisateur auteur du commentaire.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation vers les réponses de ce commentaire.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(BlogPostCommentReply::class, 'comment_id');
    }
}
