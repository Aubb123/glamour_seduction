<?php

namespace App\Models;

use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Lang extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    // Les langues ne gèrent pas de traductions séparées
    protected bool $hasTranslations = false;

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'code',
        'name',
        'native',
        'direction',
        'file_path',
        'is_default',
        'is_active',
        'sort_order',
    ];

    // Casts pour les attributs du modèle
    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Clé de route utilisée pour le binding implicite (UUID).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Scope pour récupérer uniquement les langues actives.
     */
    public function scopeActive($query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour récupérer la langue par défaut.
     */
    public function scopeDefault($query): Builder
    {
        return $query->where('is_default', true);
    }
}
