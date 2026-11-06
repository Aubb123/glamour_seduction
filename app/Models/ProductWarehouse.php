<?php

namespace App\Models;

use App\Enums\Products\Warehouses\ProductWarehouseStatus;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    // Relations enfants dont la présence bloque la suppression de cet entrepôt
    // (delete / force_delete) lors d'une suppression temporaire (BackendHelper::destroy_function)
    // ou définitive (BackendHelper::force_destroy_function).
    // NB : chaque relation listée ici DOIT avoir une méthode de relation correspondante
    // sur le modèle, sinon destroy_function lève une BadMethodCallException.
    protected array $dependentRelations = ['stocks', 'movements', 'warehouseUsers'];

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

    /**
     * Relation : un entrepôt possède plusieurs lignes de stock (module 8).
     * NB : bloque la suppression d'un entrepôt qui a encore des stocks associés.
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(ProductWarehouseStock::class, 'warehouse_id');
    }

    /**
     * Relation : un entrepôt possède plusieurs mouvements de stock (module 9).
     * NB : bloque la suppression d'un entrepôt qui a encore des mouvements associés.
     */
    public function movements(): HasMany
    {
        return $this->hasMany(ProductStockMovement::class, 'warehouse_id');
    }

    /**
     * Relation : personnel affecté à l'entrepôt (pivot enrichi `warehouse_users`).
     * NB : bloque la suppression d'un entrepôt tant que du personnel y est affecté.
     */
    public function warehouseUsers(): HasMany
    {
        return $this->hasMany(WarehouseUser::class, 'warehouse_id');
    }
}
