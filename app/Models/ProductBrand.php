<?php

namespace App\Models;

use App\Enums\Products\Brands\ProductBrandStatus;
use App\Models\Product;
use App\Models\ProductBrandTranslation;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductBrand extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    // Table associée aux marques de produits
    protected $table = 'product_brands';

    // Clé étrangère utilisée pour les traductions de marque
    protected string $translationForeignKey = 'brand_id';

    // Modèle de traduction lié à la marque
    protected string $translationModel = ProductBrandTranslation::class;

    // Relations enfants dont la présence bloque la suppression de cette marque
    // (delete / force_delete) lors d'une suppression temporaire (BackendHelper::destroy_function)
    // ou définitive (BackendHelper::force_destroy_function).
    protected array $dependentRelations = ['products'];

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'sort_order',
        'status',
    ];

    // Casts pour les attributs du modèle
    protected $casts = [
        'status' => ProductBrandStatus::class,
    ];

    /**
     * Clé de liaison utilisée par le model binding implicite (routes {productBrand}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Relation : une marque aura plusieurs produits (ajoutée au module Produits).
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'brand_id');
    }

    /**
     * Scope pour récupérer uniquement les marques actives.
     */
    public function scopeActive($query): Builder
    {
        return $query->where('status', ProductBrandStatus::Active);
    }
}
