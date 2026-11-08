<?php

namespace App\Models;

use App\Models\Lang;
use App\Models\Position;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PositionTranslation extends Model
{
    // Comportements partagés entre modèles (ex. UUID, slug, timestamps personnalisés)
    use HasModelBehaviors;

    // Table de traduction des postes
    protected $table = 'position_translations';

    // Gestion automatique de created_at / updated_at (la table possède ces colonnes via $table->timestamps())
    public $timestamps = true;

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'position_id', // clé étrangère vers positions
        'lang_id',     // clé étrangère vers langs
        'name',        // nom du poste traduit
        'slug',        // slug SEO du poste traduit
        'description', // description traduite du poste
    ];

    /**
     * Clé de liaison utilisée par le model binding implicite ({positionTranslation}).
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
        return 'name';
    }

    /**
     * Champs surveillés pour régénérer le slug lors d'une mise à jour.
     */
    protected static function getSlugWatchedFields(): array
    {
        return ['name'];
    }

    /**
     * Retourne le poste parent de cette traduction.
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    /**
     * Retourne la langue de cette traduction.
     */
    public function lang(): BelongsTo
    {
        return $this->belongsTo(Lang::class);
    }
}
