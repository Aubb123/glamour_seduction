<?php

namespace App\Models;

use App\Models\Position;
use App\Models\ProductWarehouse;
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

class WarehouseUser extends Model
{
    // Comportements communs, MAIS SANS SoftDeletes.
    //
    // La table `warehouse_users` ne possède PAS de colonne `deleted_at` : une
    // affectation est un simple lien, supprimé PHYSIQUEMENT
    // (cf. WarehouseUserService::backend_warehouse_users_destroy()).
    //
    // NB : utiliser HasModelBehaviors ici ajoutait le global scope SoftDeletes,
    // ce qui faisait planter TOUTE requête sur ce modèle — notamment la relation
    // ProductWarehouse::warehouseUsers chargée par la fiche entrepôt (show/edit) —
    // avec : « Unknown column 'warehouse_users.deleted_at' in 'where clause' » (500).
    // Le destroy (delete()) échouait de la même façon (UPDATE sur une colonne absente).
    use HasActivityLogs;
    use HasAudit;
    use HasCloudFiles;
    use HasDatetime;
    use HasFactory;
    use HasSlug;
    use HasTranslations;
    use HasUuid;

    // Table pivot enrichie : personnel affecté aux entrepôts
    protected $table = 'warehouse_users';

    // NB : les affectations ne sont PAS traduites.
    protected bool $hasTranslations = false;

    // Aucune relation enfant ne bloque la suppression d'une affectation.
    protected array $dependentRelations = [];

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'warehouse_id',
        'user_id',
        'position_id',
        'is_manager',
    ];

    // Casts pour les attributs du modèle
    protected $casts = [
        'is_manager' => 'boolean',
    ];

    /**
     * Clé de liaison utilisée par le model binding implicite (routes {warehouseUser}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Relation : l'affectation concerne un entrepôt.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(ProductWarehouse::class, 'warehouse_id');
    }

    /**
     * Relation : l'affectation concerne un utilisateur.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relation : l'affectation porte un poste (optionnel).
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }
}
