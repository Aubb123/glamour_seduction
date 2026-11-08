<?php

namespace App\Models;

use App\Models\BlogPost;
use App\Models\Lang;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogPostTranslation extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    // Table de traduction des articles
    protected $table = 'blog_post_translations';

    // Gestion automatique de created_at / updated_at (la table possède ces colonnes via $table->timestamps())
    public $timestamps = true;

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'post_id',
        'lang_id',
        'title',
        'content',
    ];

    /**
     * Clé de liaison utilisée par le model binding implicite (routes {blogPostTranslation}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Source utilisée pour générer automatiquement le slug.
     */
    protected static function getSlugSourceField(): string
    {
        return 'title';
    }

    /**
     * Champs surveillés pour régénérer le slug lors d'une mise à jour.
     */
    protected static function getSlugWatchedFields(): array
    {
        return ['title'];
    }

    /**
     * Relation vers l'article parent.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(BlogPost::class, 'post_id');
    }

    /**
     * Relation vers la langue de cette traduction.
     */
    public function lang(): BelongsTo
    {
        return $this->belongsTo(Lang::class);
    }
}
