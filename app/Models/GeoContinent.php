<?php

namespace App\Models;

use App\Enums\Geos\Continents\GeoContinentStatus;
use App\Models\GeoContinentTranslation;
use App\Models\GeoCountry;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GeoContinent extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    // Table associée aux continents
    protected $table = 'geo_continents';

    // Clé étrangère utilisée pour les traductions de continent
    protected string $translationForeignKey = 'continent_id';

    // Modèle de traduction lié au continent (déclaration explicite, cohérence avec GeoCountry)
    protected string $translationModel = GeoContinentTranslation::class;

    // Relations enfants dont la présence bloque la suppression de ce continent (delete / force_delete) lors d'une supression temporaire (BackendHelper::destroy_function) ou définitive (BackendHelper::force_destroy_function)
    protected array $dependentRelations = ['countries'];

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'code',
        'population',
        'area_km2',
        'file_path',
        'sort_order',
        'status',
    ];

    // Casts pour les attributs du modèle
    protected $casts = [
        'status' => GeoContinentStatus::class,
        'population' => 'integer',
        // `float` (au lieu de `decimal:2`) : la valeur récupérée est un vrai nombre,
        // directement utilisable pour les comparaisons/calculs (decimal:2 renvoie une chaîne).
        'area_km2' => 'float',
    ];

    /**
     * Clé de liaison utilisée par le model binding implicite (routes {geoContinent}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Relation vers les pays appartenant à ce continent.
     */
    public function countries(): HasMany
    {
        return $this->hasMany(GeoCountry::class, 'continent_id');
    }

    /**
     * Scope pour récupérer uniquement les continents actifs.
     */
    public function scopeActive($query): Builder
    {
        return $query->where('status', GeoContinentStatus::Active);
    }
}
