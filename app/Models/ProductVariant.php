<?php

namespace App\Models;

use App\Enums\Products\Variants\ProductVariantStatus;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
    // NB : 'stocks' (module 8) et 'movements' (module 9) seront ajoutés plus tard.
    protected array $dependentRelations = [];

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