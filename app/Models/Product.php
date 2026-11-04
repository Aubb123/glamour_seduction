<?php

namespace App\Models;

use App\Enums\Products\Products\ProductStatus;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    // Table associée aux produits
    protected $table = 'products';

    // Clé étrangère utilisée pour les traductions de produit
    protected string $translationForeignKey = 'product_id';

    // Modèle de traduction lié au produit
    protected string $translationModel = ProductTranslation::class;

    // Relations enfants dont la présence bloque la suppression de ce produit
    // (delete / force_delete) lors d'une suppression temporaire (BackendHelper::destroy_function)
    // ou définitive (BackendHelper::force_destroy_function).
    // NB : 'tags' (lien N-N via la table pivot `product_tag`) ne bloque volontairement PAS :
    // les tags sont optionnels et peuvent être détachés sans bloquer la suppression.
    protected array $dependentRelations = ['variants'];

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'category_id',
        'brand_id',
        'status',
        'is_featured',
        'requires_discreet_packaging',
        'views_count',
        'weight',
        'base_price',
        'compare_at_price',
    ];

    // Casts pour les attributs du modèle
    protected $casts = [
        'status' => ProductStatus::class,
        'is_featured' => 'boolean',
        'requires_discreet_packaging' => 'boolean',
        'views_count' => 'integer',
        'weight' => 'decimal:2',
        'base_price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
    ];

    /**
     * Clé de liaison utilisée par le model binding implicite (routes {product}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Relation : un produit appartient à une seule catégorie (obligatoire).
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    /**
     * Relation : un produit appartient à une seule marque (optionnelle).
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(ProductBrand::class, 'brand_id');
    }

    /**
     * Relation many-to-many vers les tags du produit (pivot `product_tag`).
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(ProductTag::class, 'product_tag', 'product_id', 'tag_id');
    }

    /**
     * Relation : un produit possède plusieurs variantes (module Variantes).
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }

    /**
     * Scope pour récupérer uniquement les produits publiés.
     */
    public function scopePublished($query): Builder
    {
        return $query->where('status', ProductStatus::Published);
    }

    /**
     * Scope pour récupérer uniquement les produits mis en avant.
     */
    public function scopeFeatured($query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope pour récupérer uniquement les produits en brouillon.
     */
    public function scopeDraft($query): Builder
    {
        return $query->where('status', ProductStatus::Draft);
    }

    /**
     * Scope pour récupérer uniquement les produits archivés.
     */
    public function scopeArchived($query): Builder
    {
        return $query->where('status', ProductStatus::Archived);
    }

    /**
     * Vérifie si le produit est publié.
     */
    public function isPublished(): bool
    {
        return $this->status === ProductStatus::Published;
    }
}
