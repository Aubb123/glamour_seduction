<?php

namespace App\Models;

use App\Models\BlogTag;
use App\Models\Lang;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogTagTranslation extends Model
{
    // Traits pour les comportements communs (UUID, Slug, Timestamps, SoftDeletes, etc.)
    use HasModelBehaviors;

    // Table de traduction des tags de blog
    protected $table = 'blog_tag_translations';

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'tag_id',
        'lang_id',
        'name',
    ];

    // ─────────────────────────────────────────────
    // RELATIONS
    // ─────────────────────────────────────────────

    /**
     * Relation vers le tag parent de cette traduction.
     */
    public function tag(): BelongsTo
    {
        return $this->belongsTo(BlogTag::class, 'tag_id');
    }

    /**
     * Relation vers la langue de cette traduction.
     */
    public function lang(): BelongsTo
    {
        return $this->belongsTo(Lang::class);
    }
}
