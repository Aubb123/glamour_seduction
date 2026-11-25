<?php

namespace App\Models;

use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeoDivisionTranslation extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, soft deletes, etc.)
    use HasModelBehaviors;

    // Table de traduction des divisions géographiques
    protected $table = 'geo_division_translations';

    // Les traductions gèrent les timestamps
    public $timestamps = true;

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'division_id',
        'lang_id',
        'name',
        'slug',
        'description',
    ];

    /**
     * Clé de liaison utilisée par le model binding implicite (routes {geoDivisionTranslation}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Relation vers la division parente.
     */
    public function division(): BelongsTo
    {
        return $this->belongsTo(GeoDivision::class, 'division_id');
    }

    /**
     * Relation vers la langue de cette traduction.
     */
    public function lang(): BelongsTo
    {
        return $this->belongsTo(Lang::class);
    }
}