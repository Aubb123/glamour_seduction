<?php

namespace App\Models;

use App\Enums\Products\Warehouses\ProductWarehouseStatus;
use App\Enums\Products\Warehouses\ProductWarehouseStockTrackingMode;
use App\Enums\Products\Warehouses\ProductWarehouseType;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductWarehouse extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    // Table associée aux entrepôts de produits
    protected $table = 'product_warehouses';

    // Clé étrangère utilisée pour les traductions de l'entrepôt
    protected string $translationForeignKey = 'warehouse_id';

    // Modèle de traduction lié à l'entrepôt
    protected string $translationModel = ProductWarehouseTranslation::class;

    // Relations enfants dont la présence bloquera la suppression de cet entrepôt
    // (delete / force_delete) lors d'une suppression temporaire (BackendHelper::destroy_function)
    // ou définitive (BackendHelper::force_destroy_function).
    // NB : le module « stocks » ajoutera 'stocks' ici lorsqu'il existera.
    protected array $dependentRelations = ['locations'];

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'sort_order',
        'code',
        'type',
        'stock_tracking_mode',
        'division_id',
        'city',
        'address',
        'latitude',
        'longitude',
        'phone',
        'email',
        'notes',
        'additional_info',
        'status',
        'is_default',
    ];

    // Casts pour les attributs du modèle
    protected $casts = [
        'sort_order' => 'integer',
        'type' => ProductWarehouseType::class,
        'stock_tracking_mode' => ProductWarehouseStockTrackingMode::class,
        'status' => ProductWarehouseStatus::class,
        'is_default' => 'boolean',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'additional_info' => 'array',
    ];

    /**
     * Clé de liaison utilisée par le model binding implicite (routes {productWarehouse}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Relation vers la division de rattachement de l'entrepôt
     * (dernier niveau administratif : commune, état, région, province…).
     */
    public function division(): BelongsTo
    {
        return $this->belongsTo(GeoDivision::class, 'division_id');
    }

    /**
     * Relation vers les emplacements physiques rattachés à cet entrepôt.
     */
    public function locations(): HasMany
    {
        return $this->hasMany(ProductWarehouseLocation::class, 'warehouse_id');
    }

    /**
     * Scope pour récupérer uniquement les entrepôts actifs.
     */
    public function scopeActive($query): Builder
    {
        return $query->where('status', ProductWarehouseStatus::Active);
    }

    /**
     * Indique si cet entrepôt est l'entrepôt par défaut.
     */
    public function isDefault(): bool
    {
        return (bool) $this->is_default;
    }
}
