<?php

namespace App\Models;

use App\Enums\Faqs\FaqStatus;
use App\Models\User;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Faq extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    // Table associée aux FAQ
    protected $table = 'faqs';

    // Clé étrangère utilisée pour les traductions de FAQ
    protected string $translationForeignKey = 'faq_id';

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'user_id',
        'sort_order',
        'status',
    ];

    // Casts pour les attributs du modèle
    protected $casts = [
        'status' => FaqStatus::class,
        'sort_order' => 'integer',
    ];

    // Clé de route utilisée pour le binding implicite ({faq} dans les URLs)
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Relation vers l'utilisateur qui a créé la FAQ.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope pour récupérer uniquement les FAQ actives.
     */
    public function scopeActive($query): Builder
    {
        return $query->where('status', FaqStatus::Active);
    }
}
