<?php

namespace App\Models;

use App\Models\Subscribe;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscribeHistory extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    // Les historiques d'abonnement ne gèrent pas de traductions séparées
    protected bool $hasTranslations = false;

    // Table associée aux historiques d'abonnement
    protected $table = 'subscribe_histories';

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'subscribe_id',
        'action',
        'acted_at',
        'ip_address',
    ];

    // Casts pour les attributs du modèle
    protected $casts = [
        'acted_at' => 'datetime',
    ];

    // Clé de route utilisée pour le binding implicite ({subscribeHistory} dans les URLs)
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Relation vers l'abonnement parent.
     */
    public function subscribe(): BelongsTo
    {
        return $this->belongsTo(Subscribe::class);
    }

    /**
     * Scope pour filtrer les historiques par action.
     */
    public function scopeAction($query, string $action): Builder
    {
        return $query->where('action', $action);
    }
}
