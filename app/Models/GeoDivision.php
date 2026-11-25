<?php

namespace App\Models;

use App\Enums\Geos\Divisions\GeoDivisionStatus;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GeoDivision extends Model
{
    // Traits pour les comportements communs (UUID, timestamps, traductions, soft deletes, etc.)
    use HasModelBehaviors;

    // Table associée aux divisions géographiques
    protected $table = 'geo_divisions';

    // Clé étrangère utilisée pour les traductions de la division
    protected string $translationForeignKey = 'division_id';

    // Modèle de traduction lié à la division
    protected string $translationModel = GeoDivisionTranslation::class;

    // Relations enfants dont la présence bloquera la suppression de cette division
    // (une division ayant des sous-divisions non supprimées ne peut pas être supprimée).
    protected array $dependentRelations = ['children'];

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'country_id',
        'parent_id',
        'division_type_id',
        'level',
        'notes',
        'status',
    ];

    // Casts pour les attributs du modèle
    protected $casts = [
        'status' => GeoDivisionStatus::class,
        'level' => 'integer',
    ];

    /**
     * Clé de liaison utilisée par le model binding implicite (routes {geoDivision}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Relation vers le pays de rattachement de la division.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(GeoCountry::class, 'country_id');
    }

    /**
     * Relation vers la division parente (auto-référencée).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(GeoDivision::class, 'parent_id');
    }

    /**
     * Relation vers les sous-divisions de cette division.
     */
    public function children(): HasMany
    {
        return $this->hasMany(GeoDivision::class, 'parent_id');
    }

    /**
     * Relation vers le type de division.
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(GeoDivisionType::class, 'division_type_id');
    }

    /**
     * Scope pour récupérer uniquement les divisions actives.
     */
    public function scopeActive($query): Builder
    {
        return $query->where('status', GeoDivisionStatus::Active);
    }
}