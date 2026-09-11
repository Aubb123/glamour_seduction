<?php

namespace App\Models;

use App\Enums\Products\StockMovements\ProductStockMovementType;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ProductStockMovement extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    // Table associée aux mouvements de stock
    protected $table = 'product_stock_movements';

    // NB : les mouvements ne sont PAS traduits (données d'audit interne).
    protected bool $hasTranslations = false;

    // Relations enfants dont la présence bloque la suppression de ce mouvement.
    // (Aucune : les mouvements sont des feuilles de l'historique d'audit.)
    protected array $dependentRelations = [];

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'warehouse_id',
        'product_variant_id',
        'type',
        'quantity',
        'reference_type',
        'reference_id',
        'note',
        'created_by',
    ];

    // Casts pour les attributs du modèle
    protected $casts = [
        'type' => ProductStockMovementType::class,
        'quantity' => 'integer',
    ];

    /**
     * Clé de liaison utilisée par le model binding implicite (routes {productStockMovement}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Relation : un mouvement est enregistré dans un seul entrepôt.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(ProductWarehouse::class, 'warehouse_id');
    }

    /**
     * Relation : un mouvement concerne une seule variante de produit.
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * Relation : un mouvement est enregistré par un utilisateur (nullable).
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relation polymorphique : référence optionnelle du mouvement
     * (commande, réassort, transfert…).
     */
    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Sens du mouvement : +quantity (entrée) ou -quantity (sortie).
     */
    public function signedQuantity(): int
    {
        return $this->type->decreasesStock() ? -$this->quantity : $this->quantity;
    }
}
