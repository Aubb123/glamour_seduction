<?php

namespace App\Models;

use App\Enums\Shops\Positions\PositionContext;
use App\Enums\Shops\Positions\PositionStatus;
use App\Models\PositionTranslation;
use App\Models\User;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
    // NB : on refuse de supprimer un poste encore affecté à du personnel (boutiques
    // ou entrepôts), via les pivots `shop_users` / `warehouse_users`.
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
     * Personnel de boutique rattaché à ce poste (via le pivot `shop_users`).
     *
     * Relation many-to-many vers User : la FK du pivot est `position_id` (côté poste)
     * et `user_id` (côté utilisateur). Aucun modèle ShopUser n'est nécessaire.
     */
    public function shopUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'shop_users', 'position_id', 'user_id')
            ->withPivot(['uuid', 'shop_id', 'is_manager'])
            ->withTimestamps();
    }

    /**
     * Personnel d'entrepôt rattaché à ce poste (via le pivot `warehouse_users`).
     */
    public function warehouseUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'warehouse_users', 'position_id', 'user_id')
            ->withPivot(['uuid', 'warehouse_id', 'is_manager'])
            ->withTimestamps();
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
