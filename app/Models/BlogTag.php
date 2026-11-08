<?php

namespace App\Models;

use App\Enums\Blogs\Tags\BlogTagStatus;
use App\Models\BlogPost;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BlogTag extends Model
{
    // Traits pour les comportements communs (UUID, Slug, Timestamps, SoftDeletes, etc.)
    use HasModelBehaviors;

    // Table associée aux tags de blog
    protected $table = 'blog_tags';

    // Clé étrangère utilisée par le modèle de traduction
    protected string $translationForeignKey = 'tag_id';

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'sort_order',
        'status',
    ];

    // Casts pour les attributs du modèle
    protected $casts = [
        'status' => BlogTagStatus::class,
    ];

    // Clé de route utilisée pour le binding implicite ({blogTag} dans les URLs)
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    // ─────────────────────────────────────────────
    // RELATIONS
    // ─────────────────────────────────────────────

    /**
     * Relation many-to-many vers les posts associés à ce tag.
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(BlogPost::class, 'blog_post_tag', 'tag_id', 'post_id');
    }

    // ─────────────────────────────────────────────
    // SCOPES
    // ─────────────────────────────────────────────

    /**
     * Scope pour récupérer uniquement les tags actifs.
     */
    public function scopeActive($query): Builder
    {
        return $query->where('status', BlogTagStatus::Active);
    }
}
