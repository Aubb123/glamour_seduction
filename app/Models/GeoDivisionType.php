<?php

namespace App\Models;

use App\Enums\Geos\DivisionTypes\GeoDivisionTypeStatus;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GeoDivisionType extends Model
{
    // Traits pour les comportements communs (UUID, timestamps, traductions, soft deletes, etc.)
    use HasModelBehaviors;

    // Table associée aux types de division géographique
    protected $table = 'geo_division_types';

    // Clé étrangère utilisée pour les traductions du type
    protected string $translationForeignKey = 'division_type_id';

    // Modèle de traduction lié au type
    protected string $translationModel = GeoDivisionTypeTranslation::class;

    // Relations enfants dont la présence bloquera la suppression de ce type
    // (un type utilisé par des divisions ne peut pas être supprimé).
    protected array $dependentRelations = ['divisions'];

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'code',
        'level',
        'sort_order',
        'status',
    ];

    // Casts pour les attributs du modèle
    protected $casts = [
        'status' => GeoDivisionTypeStatus::class,
        'level' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Clé de liaison utilisée par le model binding implicite (routes {geoDivisionType}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Relation vers les divisions rattachées à ce type (prête pour la suite du module géo).
     */
    public function divisions(): HasMany
    {
        return $this->hasMany(GeoDivision::class, 'division_type_id');
    }

    /**
     * Scope pour récupérer uniquement les types actifs.
     */
    public function scopeActive($query): Builder
    {
        return $query->where('status', GeoDivisionTypeStatus::Active);
    }
}
