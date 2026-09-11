<?php

namespace App\Models;

use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductWarehouseStock extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    // Table associée aux stocks par entrepôt / variante
    protected $table = 'product_warehouse_stocks';

    // NB : les stocks ne sont PAS traduits (données de gestion interne).
    protected bool $hasTranslations = false;

    // Relations enfants dont la présence bloque la suppression de ce stock.
    // (Aucune : les mouvements référencent l'entrepôt + la variante, pas la ligne de stock.)
    protected array $dependentRelations = [];

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'warehouse_id',
        'product_variant_id',
        'quantity',
        'reserved_quantity',
        'low_stock_threshold',
    ];

    // Casts pour les attributs du modèle
    protected $casts = [
        'quantity' => 'integer',
        'reserved_quantity' => 'integer',
        'low_stock_threshold' => 'integer',
    ];

    /**
     * Clé de liaison utilisée par le model binding implicite (routes {productWarehouseStock}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Relation : un stock appartient à un seul entrepôt.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(ProductWarehouse::class, 'warehouse_id');
    }

    /**
     * Relation : un stock concerne une seule variante de produit.
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * Quantité réellement disponible (stock physique − quantité réservée).
     */
    public function availableQuantity(): int
    {
        return $this->quantity - $this->reserved_quantity;
    }

    /**
     * Scope : stock bas (quantité physique <= seuil d'alerte).
     */
    public function scopeLowStock($query): Builder
    {
        return $query->whereColumn('quantity', '<=', 'low_stock_threshold');
    }
}