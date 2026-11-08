<?php

namespace App\Models;

use App\Models\Gallery;
use App\Models\Lang;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GalleryTranslation extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    // Table de traduction des galeries
    protected $table = 'gallery_translations';

    // Les colonnes created_at / updated_at existent en base (migration)
    // et sont alimentées automatiquement (cohérent avec les autres tables de traduction).
    public $timestamps = true;

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'gallery_id',
        'lang_id',
        'title',
        'description',
    ];

    /**
     * Retourne le champ source utilisé par HasSlug pour générer le slug.
     */
    protected static function getSlugSourceField(): string
    {
        return 'title';
    }

    /**
     * Champs à surveiller pour régénérer le slug lorsque le titre change.
     */
    protected static function getSlugWatchedFields(): array
    {
        return ['title'];
    }

    /**
     * Relation vers la galerie parent.
     */
    public function gallery(): BelongsTo
    {
        return $this->belongsTo(Gallery::class);
    }

    /**
     * Relation vers la langue de cette traduction.
     */
    public function lang(): BelongsTo
    {
        return $this->belongsTo(Lang::class);
    }
}
