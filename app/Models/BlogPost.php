<?php

namespace App\Models;

use App\Enums\Blogs\Posts\BlogPostStatus;
use App\Models\BlogCategory;
use App\Models\BlogPostComment;
use App\Models\BlogTag;
use App\Models\User;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogPost extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    // Nom de la table associée
    protected $table = 'blog_posts';

    // Clé étrangère utilisée pour les traductions du post
    protected string $translationForeignKey = 'post_id';

    // Relations enfants dont la présence bloque la suppression de cet article (delete / force_delete) lors d'une supression temporaire (BackendHelper::destroy_function) ou définitive (BackendHelper::force_destroy_function).
    // NB : `tags` (lien N-N via la table pivot `blog_post_tag`) ne bloque volontairement PAS :
    // un article a toujours au minimum un tag associé (règle métier), bloquer rendrait
    // la suppression de n'importe quel article impossible.
    protected array $dependentRelations = ['comments'];

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'category_id',
        'user_id',
        'views',
        'time_read',
        'status',
        'sort_order',
        'published_at',
    ];

    // Conversion automatique des types d'attributs
    protected $casts = [
        'status' => BlogPostStatus::class,
        'views' => 'integer',
        'time_read' => 'integer',
        'sort_order' => 'integer',
        'published_at' => 'datetime',
    ];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Relation vers la catégorie de l'article.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    /**
     * Relation vers l'utilisateur auteur de l'article.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation many-to-many vers les tags du post.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(BlogTag::class, 'blog_post_tag', 'post_id', 'tag_id');
    }

    /**
     * Relation vers les commentaires rattachés au post.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(BlogPostComment::class, 'post_id');
    }

    /**
     * Scope pour récupérer uniquement les posts publiés.
     * Un post est publié si status = published (et published_at passé ou null).
     */
    public function scopePublished($query): Builder
    {
        return $query->where('status', BlogPostStatus::Published)
            ->where(function (Builder $q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', getDateTime());
            });
    }

    /**
     * Scope pour récupérer uniquement les posts visibles publiquement.
     * (alias rétro-compatible de scopePublished pour ne pas casser le frontend)
     */
    public function scopeVisible($query): Builder
    {
        return $query->published();
    }

    /**
     * Scope pour récupérer uniquement les posts en brouillon.
     */
    public function scopeDraft($query): Builder
    {
        return $query->where('status', BlogPostStatus::Draft);
    }

    /**
     * Scope pour récupérer uniquement les posts archivés.
     */
    public function scopeArchived($query): Builder
    {
        return $query->where('status', BlogPostStatus::Archived);
    }

    /**
     * Scope pour récupérer uniquement les posts programmés.
     */
    public function scopeScheduled($query): Builder
    {
        return $query->where('status', BlogPostStatus::Scheduled);
    }

    /**
     * Vérifie si le post est publié.
     */
    public function isPublished(): bool
    {
        return $this->status === BlogPostStatus::Published
            && ($this->published_at === null || $this->published_at->isPast());
    }
}
