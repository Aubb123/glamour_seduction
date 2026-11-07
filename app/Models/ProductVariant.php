<?php

namespace App\Models;

use App\Enums\Products\Variants\ProductVariantStatus;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    // Table associée aux variantes de produits
    protected $table = 'product_variants';

    // NB : les variantes ne sont PAS traduites (données techniques : sku, barcode, price).
    // On désactive explicitement les traductions pour que la relation `translations()`
    // (utilisée par BackendHelper::destroy_function) retourne une relation vide
    // au lieu de chercher une classe ProductVariantTranslation inexistante.
    protected bool $hasTranslations = false;

    // Relations enfants dont la présence bloque la suppression de cette variante.
    // NB : 'stocks' (module 8) et 'movements' (module 9) existent désormais et bloquent
    // la suppression tant qu'un stock ou un mouvement référence la variante.
    // Chaque relation listée DOIT avoir une méthode correspondante sur le modèle,
    // sinon destroy_function lève une BadMethodCallException.
    protected array $dependentRelations = ['stocks', 'movements'];

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'product_id',
        'sku',
        'barcode',
        'price',
        'status',
        'is_default',
    ];

    // Casts pour les attributs du modèle
    protected $casts = [
        'status' => ProductVariantStatus::class,
        'is_default' => 'boolean',
        'price' => 'decimal:2',
    ];

    /**
     * Clé de liaison utilisée par le model binding implicite (routes {productVariant}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Relation : une variante appartient à un seul produit.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Relation many-to-many vers les valeurs d'attributs (pivot `product_variant_attribute_value`).
     */
    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(ProductAttributeValue::class, 'product_variant_attribute_value', 'product_variant_id', 'attribute_value_id');
    }

    /**
     * Relation : une variante possède plusieurs lignes de stock (module 8).
     * NB : bloque la suppression d'une variante qui a encore des stocks associés.
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(ProductWarehouseStock::class, 'product_variant_id');
    }

    /**
     * Relation : une variante possède plusieurs mouvements de stock (module 9).
     * NB : bloque la suppression d'une variante qui a encore des mouvements associés.
     */
    public function movements(): HasMany
    {
        return $this->hasMany(ProductStockMovement::class, 'product_variant_id');
    }

    /**
     * Prix effectif de la variante : price si défini, sinon base_price du produit.
     */
    public function effectivePrice(): string
    {
        return $this->price ?? $this->product?->base_price ?? '0.00';
    }

    /**
     * Scope pour récupérer uniquement les variantes actives.
     */
    public function scopeActive($query): Builder
    {
        return $query->where('status', ProductVariantStatus::Active);
    }
}
