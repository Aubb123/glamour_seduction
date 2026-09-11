<?php

namespace App\Models;

use App\Enums\Products\Warehouses\ProductWarehouseStatus;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ProductWarehouse extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    // Table associée aux entrepôts de produits
    protected $table = 'product_warehouses';

    // NB : les entrepôts ne sont PAS traduits (donnée de gestion interne).
    // On désactive explicitement les traductions pour que la relation `translations()`
    // (utilisée par BackendHelper::destroy_function) retourne une relation vide
    // au lieu de chercher une classe ProductWarehouseTranslation inexistante.
    protected bool $hasTranslations = false;

    // Relations enfants dont la présence bloque la suppression de cet entrepôt.
    // NB : destroy refusé si des stocks ou des mouvements existent (relations ajoutées aux modules 8-9).
    protected array $dependentRelations = ['stocks', 'movements'];

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'code',
        'name',
        'address',
        'city',
        'phone',
        'status',
        'is_default',
    ];

    // Casts pour les attributs du modèle
    protected $casts = [
        'status' => ProductWarehouseStatus::class,
        'is_default' => 'boolean',
    ];

    /**
     * Clé de liaison utilisée par le model binding implicite (routes {productWarehouse}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Scope pour récupérer uniquement les entrepôts actifs.
     */
    public function scopeActive($query): Builder
    {
        return $query->where('status', ProductWarehouseStatus::Active);
    }

    /**
     * Scope pour récupérer l'entrepôt par défaut (un seul par boutique).
     */
    public function scopeDefaultWarehouse($query): Builder
    {
        return $query->where('is_default', true);
    }
}