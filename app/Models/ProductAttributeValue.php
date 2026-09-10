<?php

namespace App\Models;

use App\Enums\Products\Attributes\ProductAttributeValueStatus;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    // NB : 'variants' (pivot product_variant_attribute_value) sera ajouté lors de
    // l'intégration du module Variantes.
    protected array $dependentRelations = [];

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'attribute_id',
        'value',
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
     * Scope pour récupérer uniquement les valeurs actives.
     */
    public function scopeActive($query): Builder
    {
        return $query->where('status', ProductAttributeValueStatus::Active);
    }
}