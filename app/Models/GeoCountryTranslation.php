<?php

namespace App\Models;

use App\Models\GeoCountry;
use App\Models\Lang;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeoCountryTranslation extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    // Table de traduction des pays
    protected $table = 'geo_country_translations';

    // Les traductions gèrent les timestamps
    public $timestamps = true;

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'country_id',
        'lang_id',
        'name',
        'slug',
        'description',
    ];

    /**
     * Clé de liaison utilisée par le model binding implicite (routes {geoCountryTranslation}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Relation vers le pays parent.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(GeoCountry::class, 'country_id');
    }

    /**
     * Relation vers la langue de cette traduction.
     */
    public function lang(): BelongsTo
    {
        return $this->belongsTo(Lang::class);
    }
}
