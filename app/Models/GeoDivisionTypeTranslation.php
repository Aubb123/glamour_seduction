<?php

namespace App\Models;

use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeoDivisionTypeTranslation extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, soft deletes, etc.)
    use HasModelBehaviors;

    // Table de traduction des types de division géographique
    protected $table = 'geo_division_type_translations';

    // Les traductions gèrent les timestamps
    public $timestamps = true;

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'division_type_id',
        'lang_id',
        'name',
        'slug',
        'description',
    ];

    /**
     * Clé de liaison utilisée par le model binding implicite (routes {geoDivisionTypeTranslation}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Relation vers le type de division parent.
     */
    public function divisionType(): BelongsTo
    {
        return $this->belongsTo(GeoDivisionType::class, 'division_type_id');
    }

    /**
     * Relation vers la langue de cette traduction.
     */
    public function lang(): BelongsTo
    {
        return $this->belongsTo(Lang::class);
    }
}
