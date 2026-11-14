<?php

namespace App\Models;

use App\Models\Shop;
use App\Models\User;
use App\Traits\HasActivityLogs;
use App\Traits\HasAudit;
use App\Traits\HasCloudFiles;
use App\Traits\HasDatetime;
use App\Traits\HasSlug;
use App\Traits\HasTranslations;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopUser extends Model
{
    // Comportements communs, MAIS SANS SoftDeletes.
    //
    // La table `shop_users` ne possède PAS de colonne `deleted_at` : une
    // affectation est un simple lien, supprimé PHYSIQUEMENT
    // (cf. ShopUserService::backend_shop_users_destroy()).
    //
    // NB : utiliser HasModelBehaviors ici ajoutait le global scope SoftDeletes,
    // ce qui faisait planter toute requête sur ce modèle — notamment la relation
    // Shop::shopUsers chargée par la fiche boutique — avec :
    // « Unknown column 'shop_users.deleted_at' in 'where clause' ».
    use HasActivityLogs;
    use HasAudit;
    use HasCloudFiles;
    use HasDatetime;
    use HasFactory;
    use HasSlug;
    use HasTranslations;
    use HasUuid;

    // Table pivot enrichie : personnel affecté aux boutiques
    protected $table = 'shop_users';

    // NB : les affectations ne sont PAS traduites.
    protected bool $hasTranslations = false;

    // Aucune relation enfant ne bloque la suppression d'une affectation.
    protected array $dependentRelations = [];

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'shop_id',
        'user_id',
        'is_manager',
    ];

    // Casts pour les attributs du modèle
    protected $casts = [
        'is_manager' => 'boolean',
    ];

    /**
     * Clé de liaison utilisée par le model binding implicite (routes {shopUser}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Relation : l'affectation concerne une boutique.
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    /**
     * Relation : l'affectation concerne un utilisateur.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
