<?php

namespace App\Models;

use App\Enums\Galleries\GalleryStatus;
use App\Models\User;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gallery extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    // Table associée aux galeries
    protected $table = 'galleries';

    // Clé étrangère utilisée pour les traductions de galerie
    protected string $translationForeignKey = 'gallery_id';

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'user_id',
        'sort_order',
        'status',
    ];

    // Casts pour les attributs du modèle
    protected $casts = [
        'status' => GalleryStatus::class,
        'sort_order' => 'integer',
    ];

    // Clé de route utilisée pour le binding implicite ({gallery} dans les URLs)
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Relation vers l'utilisateur propriétaire de la galerie.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope pour récupérer uniquement les galeries actives.
     */
    public function scopeActive($query): Builder
    {
        return $query->where('status', GalleryStatus::Active);
    }
}
