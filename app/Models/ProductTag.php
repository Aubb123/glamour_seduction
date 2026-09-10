<?php

namespace App\Models;

use App\Enums\Products\Tags\ProductTagStatus;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductTag extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    // Table associée aux tags de produits
    protected $table = 'product_tags';

    // Clé étrangère utilisée pour les traductions de tag
    protected string $translationForeignKey = 'tag_id';

    // Modèle de traduction lié au tag
    protected string $translationModel = ProductTagTranslation::class;

    // Relations enfants dont la présence bloque la suppression de ce tag
    // (delete / force_delete) lors d'une suppression temporaire (BackendHelper::destroy_function)
    // ou définitive (BackendHelper::force_destroy_function).
    // NB : 'products' (pivot product_tag) sera ajouté lors de l'intégration du module Produits.
    protected array $dependentRelations = [];

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'sort_order',
        'status',
    ];

    // Casts pour les attributs du modèle
    protected $casts = [
        'status' => ProductTagStatus::class,
    ];

    /**
     * Clé de liaison utilisée par le model binding implicite (routes {productTag}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Relation : un tag sera rattaché à plusieurs produits via le pivot product_tag
     * (pivot créé au module Produits).
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_tag', 'tag_id', 'product_id');
    }

    /**
     * Scope pour récupérer uniquement les tags actifs.
     */
    public function scopeActive($query): Builder
    {
        return $query->where('status', ProductTagStatus::Active);
    }
}
