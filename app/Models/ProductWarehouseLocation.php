<?php

namespace App\Models;

use App\Enums\Products\WarehouseLocations\ProductWarehouseLocationStatus;
use App\Enums\Products\WarehouseLocations\ProductWarehouseLocationType;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductWarehouseLocation extends Model
{
    // Traits pour les comportements communs (UUID, timestamps, traductions, soft deletes, etc.)
    use HasModelBehaviors;

    // Table associée aux emplacements d'entrepôt
    protected $table = 'product_warehouse_locations';

    // Clé étrangère utilisée pour les traductions de l'emplacement
    protected string $translationForeignKey = 'location_id';

    // Modèle de traduction lié à l'emplacement
    protected string $translationModel = ProductWarehouseLocationTranslation::class;

    // Relations enfants dont la présence bloquera la suppression de cet emplacement
    // (delete / force_delete) lors d'une suppression temporaire (BackendHelper::destroy_function)
    // ou définitive (BackendHelper::force_destroy_function).
    // NB : les futurs modules « stocks » et « mouvements » ajouteront leurs relations ici.
    protected array $dependentRelations = ['children'];

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'warehouse_id',
        'parent_id',
        'type',
        'code',
        'sort_order',
        'notes',
        'status',
    ];

    // Casts pour les attributs du modèle
    protected $casts = [
        'type' => ProductWarehouseLocationType::class,
        'status' => ProductWarehouseLocationStatus::class,
        'sort_order' => 'integer',
    ];

    /**
     * Clé de liaison utilisée par le model binding implicite (routes {productWarehouseLocation}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Relation vers l'entrepôt de rattachement.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(ProductWarehouse::class, 'warehouse_id');
    }

    /**
     * Relation vers l'emplacement parent (null = emplacement racine).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ProductWarehouseLocation::class, 'parent_id');
    }

    /**
     * Relation vers les emplacements enfants de cet emplacement.
     */
    public function children(): HasMany
    {
        return $this->hasMany(ProductWarehouseLocation::class, 'parent_id');
    }

    /**
     * Relation vers les soldes de stock détaillés rattachés à cet emplacement.
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(ProductWarehouseLocationStock::class, 'location_id');
    }

    /**
     * Scope pour récupérer uniquement les emplacements actifs.
     */
    public function scopeActive($query): Builder
    {
        return $query->where('status', ProductWarehouseLocationStatus::Active);
    }

    /**
     * Indique si cet emplacement est un emplacement racine (sans parent).
     */
    public function isRoot(): bool
    {
        return $this->parent_id === null;
    }
}
