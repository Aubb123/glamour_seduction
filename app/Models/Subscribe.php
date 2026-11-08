<?php

namespace App\Models;

use App\Enums\Subscribes\SubscribeStatus;
use App\Models\SubscribeHistory;
use App\Models\User;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscribe extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    // Les abonnements ne gèrent pas de traductions séparées
    protected bool $hasTranslations = false;

    // Table associée aux abonnements
    protected $table = 'subscribes';

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'user_id',
        'email',
        'status',
        'verified_at',
    ];

    // Casts pour les attributs du modèle
    protected $casts = [
        'status' => SubscribeStatus::class,
        'verified_at' => 'datetime',
    ];

    // Relations enfants dont la présence bloque la suppression de cet abonnement (delete / force_delete) lors d'une supression temporaire (BackendHelper::destroy_function) ou définitive (BackendHelper::force_destroy_function)
    protected array $dependentRelations = ['histories'];

    // Clé de route utilisée pour le binding implicite ({subscribe} dans les URLs)
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Relation vers l'utilisateur lié à l'abonnement (optionnel).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation vers l'historique des actions d'abonnement.
     */
    public function histories(): HasMany
    {
        return $this->hasMany(SubscribeHistory::class);
    }

    /**
     * Scope pour récupérer uniquement les abonnements actifs.
     */
    public function scopeActive($query): Builder
    {
        return $query->where('status', SubscribeStatus::Active);
    }

    /**
     * Scope pour récupérer uniquement les abonnements vérifiés.
     */
    public function scopeVerified($query): Builder
    {
        return $query->whereNotNull('verified_at');
    }
}
