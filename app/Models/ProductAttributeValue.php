<?php

namespace App\Models;

use App\Enums\Products\Attributes\ProductAttributeType;
use App\Enums\Products\Attributes\ProductAttributeValueStatus;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

class ProductAttributeValue extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    // Table associée aux valeurs d'attributs de produits
    protected $table = 'product_attribute_values';

    // Clé étrangère utilisée pour les traductions de valeur d'attribut
    protected string $translationForeignKey = 'attribute_value_id';

    // Modèle de traduction lié à la valeur d'attribut
    protected string $translationModel = ProductAttributeValueTranslation::class;

    // Relations enfants dont la présence bloque la suppression de cette valeur.
    // NB : contrairement aux tags des articles (voir BlogPost), le pivot
    // product_variant_attribute_value n'est PAS volontairement bloquant :
    // la suppression d'une valeur reste possible même si des variantes la
    // référencent (comportement identique à BlogPost::tags).
    protected array $dependentRelations = [];

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'attribute_id',
        'value',
        'hex_color',
        'sort_order',
        'status',
    ];

    // Casts pour les attributs du modèle
    protected $casts = [
        'status' => ProductAttributeValueStatus::class,
    ];

    /**
     * Clé de liaison utilisée par le model binding implicite (routes {productAttributeValue}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Relation : une valeur appartient à un seul attribut.
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(ProductAttribute::class, 'attribute_id');
    }

    /**
     * Relation many-to-many vers les variantes de produits utilisant cette valeur
     * (pivot `product_variant_attribute_value`).
     */
    public function variants(): BelongsToMany
    {
        // Relation many-to-many vers les variantes de produits utilisant cette valeur
        return $this->belongsToMany(ProductVariant::class, 'product_variant_attribute_value', 'attribute_value_id', 'product_variant_id');
    }

    /**
     * Scope pour récupérer uniquement les valeurs actives.
     */
    public function scopeActive($query): Builder
    {
        return $query->where('status', ProductAttributeValueStatus::Active);
    }

    /**
     * Nombre de produits associés à cette valeur (produits dont une variante porte cette valeur).
     */
    public function productsCount(): int
    {
        // Un produit est compté dès qu'une de ses variantes porte cette valeur d'attribut.
        return Product::query()
            ->whereHas('variants.attributeValues', fn (Builder $query) => $query->where('product_attribute_values.id', $this->getKey()))
            ->count();
    }

    /**
     * Scope : ajoute `products_count` à la requête (sous-requête corrélée, sans N+1).
     *
     * NB : la table externe est `product_attribute_values`, d'où l'écriture SQL manuelle
     * (un `whereColumn` Eloquent serait ambigu entre la table interne et externe).
     */
    public function scopeWithProductsCount(Builder $query): Builder
    {
        return $query->addSelect([
            'products_count' => DB::table('product_variant_attribute_value as pvav')
                ->selectRaw('count(distinct product_variants.product_id)')
                ->join('product_variants', 'product_variants.id', '=', 'pvav.product_variant_id')
                ->join('products', 'products.id', '=', 'product_variants.product_id')
                ->whereColumn('pvav.attribute_value_id', 'product_attribute_values.id')
                ->whereNull('product_variants.deleted_at')
                ->whereNull('products.deleted_at'),
        ]);
    }

    /**
     * Accesseur : indique si la valeur porte un code couleur HEX.
     * (détermine si l'attribut parent est de type "color")
     */
    public function getIsColorAttribute(): bool
    {
        // Vérifie si l'attribut parent est de type "color" pour déterminer si la valeur doit porter un code couleur HEX.
        return $this->attribute?->type === ProductAttributeType::Color; // NB : l'attribut parent peut être null si la valeur est orpheline (attribut supprimé)
    }
}
