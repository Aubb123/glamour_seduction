<?php

namespace App\Models;

use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Solde de stock détaillé d'une variante dans un emplacement précis d'un entrepôt.
 *
 * Cette table représente un solde courant (pas d'historique) : une seule ligne
 * par couple (emplacement, variante), garantie par la contrainte d'unicité en base.
 * L'entrepôt est retrouvé via la relation `location.warehouse` (pas de warehouse_id redondant).
 * Le stock par emplacement n'est permis que pour un entrepôt en mode « location ».
 */
class ProductWarehouseLocationStock extends Model
{
    // Traits pour les comportements communs (UUID, audit, logs, factory, etc.)
    use HasModelBehaviors;

    // Table associée aux stocks par emplacement
    protected $table = 'product_warehouse_location_stocks';

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'location_id',
        'product_variant_id',
        'quantity',
    ];

    // Casts pour les attributs du modèle
    protected $casts = [
        'quantity' => 'integer',
    ];

    /**
     * Clé de liaison utilisée par le model binding implicite (routes {productWarehouseLocationStock}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Relation vers l'emplacement de rattachement.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(ProductWarehouseLocation::class, 'location_id');
    }

    /**
     * Relation vers la variante de produit concernée.
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * Récupère l'entrepôt parent via l'emplacement (pas de warehouse_id redondant).
     */
    public function warehouse(): ?ProductWarehouse
    {
        return $this->location?->warehouse;
    }

    /**
     * Scope pour récupérer uniquement les stocks des entrepôts en mode « location ».
     */
    public function scopeLocationTracked($query): Builder
    {
        return $query->whereHas('location.warehouse', fn ($query) => $query
            ->where('stock_tracking_mode', 'location'));
    }
}
