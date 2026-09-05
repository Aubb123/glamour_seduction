<?php

namespace App\Models;

use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeoContinentTranslation extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    // Table de traduction des continents
    protected $table = 'geo_continent_translations';

    // Les traductions gèrent les timestamps
    public $timestamps = true;

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'continent_id',
        'lang_id',
        'name',
        'description',
    ];

    /**
     * Clé de liaison utilisée par le model binding implicite (routes {geoContinentTranslation}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Relation vers le continent parent.
     */
    public function continent(): BelongsTo
    {
        return $this->belongsTo(GeoContinent::class, 'continent_id');
    }

    /**
     * Relation vers la langue de cette traduction.
     */
    public function lang(): BelongsTo
    {
        return $this->belongsTo(Lang::class);
    }
}
