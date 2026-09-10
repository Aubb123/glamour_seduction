<?php

namespace App\Models;

use App\Enums\Products\Categories\ProductCategoryStatus;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductCategory extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    // Table associée aux catégories de produits
    protected $table = 'product_categories';

    // Clé étrangère utilisée pour les traductions de catégorie
    protected string $translationForeignKey = 'category_id';

    // Modèle de traduction lié à la catégorie
    protected string $translationModel = ProductCategoryTranslation::class;

    // Relations enfants dont la présence bloque la suppression de cette catégorie
    // (delete / force_delete) lors d'une suppression temporaire (BackendHelper::destroy_function)
    // ou définitive (BackendHelper::force_destroy_function).
    protected array $dependentRelations = ['products', 'children'];

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'parent_id',
        'sort_order',
        'status',
    ];

    // Casts pour les attributs du modèle
    protected $casts = [
        'status' => ProductCategoryStatus::class,
    ];

    /**
     * Clé de liaison utilisée par le model binding implicite (routes {productCategory}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Relation vers la catégorie parente (null = catégorie racine).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    /**
     * Relation vers les sous-catégories de cette catégorie.
     */
    public function children(): HasMany
    {
        return $this->hasMany(ProductCategory::class, 'parent_id');
    }

    /**
     * Relation : une catégorie possède plusieurs produits.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    /**
     * Scope pour récupérer uniquement les catégories actives.
     */
    public function scopeActive($query): Builder
    {
        return $query->where('status', ProductCategoryStatus::Active);
    }

    /**
     * Scope pour récupérer uniquement les catégories racines (sans parent).
     */
    public function scopeRoots($query): Builder
    {
        return $query->whereNull('parent_id');
    }
}
