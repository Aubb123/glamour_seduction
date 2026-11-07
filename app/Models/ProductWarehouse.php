<?php

namespace App\Models;

use App\Enums\Products\Warehouses\ProductWarehouseStatus;
use App\Models\Builders\ProductWarehouseBuilder;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductWarehouse extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    // Table associée aux entrepôts de produits
    protected $table = 'product_warehouses';

    // Clé étrangère utilisée pour les traductions d'entrepôt (product_warehouse_translations.warehouse_id)
    protected string $translationForeignKey = 'warehouse_id';

    // Modèle de traduction lié à l'entrepôt
    protected string $translationModel = ProductWarehouseTranslation::class;

    // Relations enfants dont la présence bloque la suppression de cet entrepôt
    // (delete / force_delete) via BackendHelper::destroy_function / force_destroy_function.
    // NB : la relation `warehouseUsers` (personnel via le pivot `warehouse_users`)
    // bloque la suppression.
    protected array $dependentRelations = ['stocks', 'movements', 'warehouseUsers'];

    // NB : seules les données NON traduites vivent sur la table principale.
    // `code` reste unique et non traduit ; `name`, `address`, `city`, `phone` sont
    // portés par product_warehouse_translations. « Entrepôt par défaut » est une
    // notion PAR BOUTIQUE, portée par le pivot `shop_warehouse.is_primary`.
    protected $fillable = [
        'code',
        'status',
    ];

    // Casts pour les attributs du modèle
    protected $casts = [
        'status' => ProductWarehouseStatus::class,
    ];

    /**
     * Builder Eloquent dédié : redirige `orderBy('name')` vers `code`
     * (la colonne `name` n'existe plus sur la table principale).
     */
    public function newEloquentBuilder($query): ProductWarehouseBuilder
    {
        return new ProductWarehouseBuilder($query);
    }

    /**
     * Clé de liaison utilisée par le model binding implicite (routes {productWarehouse}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Accesseur : nom d'affichage de l'entrepôt (nom traduit actif, repli sur le code).
     */
    public function getNameAttribute(): string
    {
        return $this->active_translation?->name ?? $this->code;
    }

    /**
     * Scope pour récupérer uniquement les entrepôts actifs.
     */
    public function scopeActive($query): Builder
    {
        return $query->where('status', ProductWarehouseStatus::Active);
    }

    /**
     * Relation many-to-many vers les boutiques approvisionnées par cet entrepôt
     * (pivot `shop_warehouse`, attribut booléen `is_primary`).
     */
    public function shops(): BelongsToMany
    {
        return $this->belongsToMany(Shop::class, 'shop_warehouse', 'warehouse_id', 'shop_id')
            ->withPivot(['is_primary'])
            ->withTimestamps();
    }

    /**
     * Indique si cet entrepôt est l'entrepôt principal (par défaut) de la boutique fournie.
     */
    public function isPrimaryFor(Shop $shop): bool
    {
        return $this->shops()
            ->wherePivot('is_primary', true)
            ->whereKey($shop->getKey())
            ->exists();
    }

    /**
     * Relation : un entrepôt possède plusieurs lignes de stock (module 8).
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(ProductWarehouseStock::class, 'warehouse_id');
    }

    /**
     * Relation : un entrepôt possède plusieurs mouvements de stock (module 9).
     */
    public function movements(): HasMany
    {
        return $this->hasMany(ProductStockMovement::class, 'warehouse_id');
    }

    /**
     * Relation many-to-many vers le personnel affecté à l'entrepôt.
     *
     * Le pivot `warehouse_users` est une table pivot enrichie (colonnes `uuid`,
     * `position_id`, `is_manager`), au même titre que `blog_post_tag`. Il n'y a
     * volontairement PLUS de modèle WarehouseUser : on manipule le pivot via les
     * méthodes Eloquent (attach/updateExistingPivot/detach) et on lit les colonnes
     * enrichies via `$user->pivot`.
     */
    public function warehouseUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'warehouse_users', 'warehouse_id', 'user_id')
            ->withPivot(['uuid', 'position_id', 'is_manager'])
            ->withTimestamps();
    }
}
