<?php

namespace App\Models;

use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Solde de stock global d'une variante dans un entrepôt.
 *
 * Cette table représente un solde courant (pas d'historique) : une seule ligne
 * par couple (entrepôt, variante), garantie par la contrainte d'unicité en base.
 * Le suivi détaillé par emplacement sera traité dans une étape ultérieure.
 */
class ProductWarehouseStock extends Model
{
    // Traits pour les comportements communs (UUID, audit, logs, factory, etc.)
    use HasModelBehaviors;

    // Table associée aux stocks par entrepôt
    protected $table = 'product_warehouse_stocks';

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'warehouse_id',
        'product_variant_id',
        'quantity',
    ];

    // Casts pour les attributs du modèle
    protected $casts = [
        'quantity' => 'integer',
    ];

    /**
     * Clé de liaison utilisée par le model binding implicite (routes {productWarehouseStock}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Relation vers l'entrepôt de rattachement.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(ProductWarehouse::class, 'warehouse_id');
    }

    /**
     * Relation vers la variante de produit concernée.
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * Scope pour récupérer uniquement les stocks des entrepôts en mode « warehouse ».
     */
    public function scopeWarehouseTracked($query): Builder
    {
        return $query->whereHas('warehouse', fn ($query) => $query
            ->where('stock_tracking_mode', 'warehouse'));
    }
}
