<?php

namespace App\Models;

use App\Enums\Products\Warehouses\ProductWarehouseStatus;
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
    // (delete / force_delete) lors d'une suppression temporaire (BackendHelper::destroy_function)
    // ou définitive (BackendHelper::force_destroy_function).
    // NB : chaque relation listée ici DOIT avoir une méthode de relation correspondante
    // sur le modèle, sinon destroy_function lève une BadMethodCallException.
    // NB : les boutiques (pivot `shop_warehouse`) ne bloquent volontairement PAS :
    // un simple détachement suffit.
    protected array $dependentRelations = ['stocks', 'movements', 'warehouseUsers'];

    // NB : seules les données NON traduites vivent sur la table principale.
    // `code` reste unique et non traduit (donnée de gestion interne) ;
    // `name`, `address`, `city`, `phone` sont portés par product_warehouse_translations.
    // NB : « entrepôt par défaut » est une notion PAR BOUTIQUE, portée par le pivot
    // `shop_warehouse.is_primary` (voir la relation shops()) — il n'y a plus de
    // colonne is_default globale.
    protected $fillable = [
        'code',
        'status',
    ];

    // Casts pour les attributs du modèle
    protected $casts = [
        'status' => ProductWarehouseStatus::class,
    ];

    /**
     * Clé de liaison utilisée par le model binding implicite (routes {productWarehouse}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Accesseur : nom d'affichage de l'entrepôt.
     *
     * La colonne `name` a été retirée de la table `product_warehouses` (le nom vit
     * désormais dans product_warehouse_translations, traduit par langue). Cet accesseur
     * expose `$warehouse->name` pour toutes les vues/contrôleurs qui l'affichent encore,
     * en résolvant le nom traduit actif et en repliant sur le code si aucune traduction
     * n'existe. Il évite ainsi les « Undefined column 'name' » / vides à l'affichage.
     *
     * NB : il ne crée PAS d'attribut en base (pas de setter) — il est en lecture seule.
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
     * Scope de tri par nom d'affichage (« code » étant la seule colonne triable en
     * base, le nom traduit n'étant pas sur la table principale) :
     * on trie sur le code, ce qui reste stable et non ambigu.
     */
    public function scopeOrderedByName($query): Builder
    {
        return $query->orderBy('code');
    }

    /**
     * Relation many-to-many vers les boutiques approvisionnées par cet entrepôt
     * (pivot `shop_warehouse`, attribut booléen `is_primary`).
     *
     * NB : `is_primary = true` désigne l'entrepôt PRINCIPAL (par défaut) d'une
     * boutique donnée — un seul par boutique (garanti applicativement par
     * ShopService::syncWarehouses()).
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
     * NB : bloque la suppression d'un entrepôt qui a encore des stocks associés.
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(ProductWarehouseStock::class, 'warehouse_id');
    }

    /**
     * Relation : un entrepôt possède plusieurs mouvements de stock (module 9).
     * NB : bloque la suppression d'un entrepôt qui a encore des mouvements associés.
     */
    public function movements(): HasMany
    {
        return $this->hasMany(ProductStockMovement::class, 'warehouse_id');
    }

    /**
     * Relation : personnel affecté à l'entrepôt (pivot enrichi `warehouse_users`).
     * NB : bloque la suppression d'un entrepôt tant que du personnel y est affecté.
     */
    public function warehouseUsers(): HasMany
    {
        return $this->hasMany(WarehouseUser::class, 'warehouse_id');
    }
}
