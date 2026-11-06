<?php

namespace App\Models;

use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopUser extends Model
{
    // Traits pour les comportements communs (UUID, timestamps, etc.)
    use HasModelBehaviors;

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
        'position_id',
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

    /**
     * Relation : l'affectation porte un poste (optionnel).
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }
}
