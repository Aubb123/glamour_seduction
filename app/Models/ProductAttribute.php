<?php

namespace App\Models;

use App\Enums\Products\Attributes\ProductAttributeStatus;
use App\Enums\Products\Attributes\ProductAttributeType;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductAttribute extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    // Table associée aux attributs de produits
    protected $table = 'product_attributes';

    // Clé étrangère utilisée pour les traductions d'attribut
    protected string $translationForeignKey = 'attribute_id';

    // Modèle de traduction lié à l'attribut
    protected string $translationModel = ProductAttributeTranslation::class;

    // Relations enfants dont la présence bloque la suppression de cet attribut
    // (delete / force_delete) lors d'une suppression temporaire (BackendHelper::destroy_function)
    // ou définitive (BackendHelper::force_destroy_function).
    // NB : 'products' (pivot product_attribute_value) sera ajouté lors de l'intégration du module Produits.
    protected array $dependentRelations = ['values'];

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'sort_order',
        'status',
        'type',
    ];

    // Casts pour les attributs du modèle
    protected $casts = [
        'status' => ProductAttributeStatus::class,
        'type' => ProductAttributeType::class,
    ];

    /**
     * Clé de liaison utilisée par le model binding implicite (routes {productAttribute}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Relation : un attribut possède plusieurs valeurs (ex : Couleur → rouge, rose, noir).
     */
    public function values(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class, 'attribute_id');
    }

    /**
     * Scope pour récupérer uniquement les attributs actifs.
     */
    public function scopeActive($query): Builder
    {
        return $query->where('status', ProductAttributeStatus::Active);
    }

    /**
     * Indique si cet attribut est de type "couleur" et impose donc un code HEX par valeur.
     */
    public function isColor(): bool
    {
        return $this->type === ProductAttributeType::Color;
    }
}
