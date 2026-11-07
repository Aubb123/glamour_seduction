<?php

namespace App\Models;

use App\Enums\Shops\Shops\ShopStatus;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Shop extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    // Table associée aux boutiques / points de vente
    protected $table = 'shops';

    // Clé étrangère utilisée pour les traductions de la boutique
    protected string $translationForeignKey = 'shop_id';

    // Modèle de traduction lié à la boutique
    protected string $translationModel = ShopTranslation::class;

    // Relations enfants dont la présence bloque la suppression de cette boutique
    // (delete / force_delete) via BackendHelper::destroy_function / force_destroy_function.
    // NB : la relation `users` (personnel via le pivot `shop_users`) bloque la suppression :
    // on refuse de supprimer une boutique tant que du personnel y est affecté.
    // Les entrepôts (pivot shop_warehouse) ne bloquent volontairement PAS.
    protected array $dependentRelations = ['users'];

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'code',
        'email',
        'phone',
        'address',
        'city',
        'status',
        'is_default',
        'sort_order',
    ];

    // Casts pour les attributs du modèle
    protected $casts = [
        'status' => ShopStatus::class,
        'is_default' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Clé de liaison utilisée par le model binding implicite (routes {shop}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Relation many-to-many vers les entrepôts dont la boutique s'approvisionne
     * (pivot `shop_warehouse`, attribut booléen `is_primary`).
     */
    public function warehouses(): BelongsToMany
    {
        return $this->belongsToMany(ProductWarehouse::class, 'shop_warehouse', 'shop_id', 'warehouse_id')
            ->withPivot(['is_primary'])
            ->withTimestamps();
    }

    /**
     * Relation many-to-many filtrée sur l'entrepôt principal d'approvisionnement
     * (pivot `is_primary = true`). Utiliser ->first() pour obtenir l'entrepôt.
     */
    public function primaryWarehouse(): BelongsToMany
    {
        return $this->warehouses()->wherePivot('is_primary', true);
    }

    /**
     * Relation many-to-many vers le personnel affecté à la boutique.
     *
     * Le pivot `shop_users` est enrichi (colonne `uuid`, `position_id`, `is_manager`),
     * exactement comme les autres pivots du projet (ex : blog_post_tag). Il n'y a
     * volontairement PAS de modèle ShopUser : on accède au pivot via `$user->pivot`.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'shop_users', 'shop_id', 'user_id')
            ->withPivot(['uuid', 'position_id', 'is_manager'])
            ->withTimestamps();
    }

    /**
     * Scope pour récupérer uniquement les boutiques actives.
     */
    public function scopeActive($query): Builder
    {
        return $query->where('status', ShopStatus::Active);
    }

    /**
     * Scope pour récupérer la boutique par défaut (une seule par plateforme).
     */
    public function scopeDefaultShop($query): Builder
    {
        return $query->where('is_default', true);
    }
}
