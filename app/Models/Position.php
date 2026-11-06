<?php

namespace App\Models;

use App\Enums\Shops\Positions\PositionContext;
use App\Enums\Shops\Positions\PositionStatus;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    // Table associée aux postes du personnel
    protected $table = 'positions';

    // Clé étrangère utilisée pour les traductions du poste
    protected string $translationForeignKey = 'position_id';

    // Modèle de traduction lié au poste
    protected string $translationModel = PositionTranslation::class;

    // Relations enfants dont la présence bloque la suppression de ce poste
    // (delete / force_delete) via BackendHelper::destroy_function / force_destroy_function.
    // NB : on refuse de supprimer un poste encore affecté à du personnel
    // (boutiques ou entrepôts), afin de ne pas laisser d'affectations orphelines.
    protected array $dependentRelations = ['shopUsers', 'warehouseUsers'];

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'code',
        'context',
        'status',
        'sort_order',
    ];

    // Casts pour les attributs du modèle
    protected $casts = [
        'context' => PositionContext::class,
        'status' => PositionStatus::class,
        'sort_order' => 'integer',
    ];

    /**
     * Clé de liaison utilisée par le model binding implicite (routes {position}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Relation : un poste peut être affecté à plusieurs membres du personnel de boutique.
     */
    public function shopUsers(): HasMany
    {
        return $this->hasMany(ShopUser::class, 'position_id');
    }

    /**
     * Relation : un poste peut être affecté à plusieurs membres du personnel d'entrepôt.
     */
    public function warehouseUsers(): HasMany
    {
        return $this->hasMany(WarehouseUser::class, 'position_id');
    }

    /**
     * Scope pour récupérer uniquement les postes actifs.
     */
    public function scopeActive($query): Builder
    {
        return $query->where('status', PositionStatus::Active);
    }

    /**
     * Scope pour récupérer les postes utilisables dans un contexte donné
     * (une portée `both` est toujours incluse).
     */
    public function scopeForContext($query, PositionContext $context): Builder
    {
        return $query->whereIn('context', [$context->value, PositionContext::Both->value]);
    }
}
