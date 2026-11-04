<?php

namespace App\Models;

use App\Enums\Currencies\CurrencyStatus;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    // Table associée aux devises
    protected $table = 'currencies';

    // Clé étrangère utilisée pour les traductions de devise
    protected string $translationForeignKey = 'currency_id';

    // Modèle de traduction lié à la devise
    protected string $translationModel = CurrencyTranslation::class;

    // Relations enfants dont la présence bloque la suppression de cette devise
    // (delete / force_delete) lors d'une suppression temporaire (BackendHelper::destroy_function)
    // ou définitive (BackendHelper::force_destroy_function).
    protected array $dependentRelations = ['countries'];

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'code',
        'symbol',
        'decimal_places',
        'numeric_code',
        'sort_order',
        'is_default',
        'exchange_rate',
        'status',
    ];

    // Casts pour les attributs du modèle
    protected $casts = [
        'status' => CurrencyStatus::class,
        'decimal_places' => 'integer',
        'is_default' => 'boolean',
        'exchange_rate' => 'decimal:6',
    ];

    /**
     * Clé de liaison utilisée par le model binding implicite (routes {currency}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Relation : une devise est utilisée par plusieurs pays.
     */
    public function countries(): HasMany
    {
        return $this->hasMany(GeoCountry::class, 'currency_id');
    }

    /**
     * Scope pour récupérer uniquement les devises actives.
     */
    public function scopeActive($query): Builder
    {
        return $query->where('status', CurrencyStatus::Active);
    }

    /**
     * Scope pour récupérer la devise marquée par défaut.
     */
    public function scopeDefaultCurrency($query): Builder
    {
        return $query->where('is_default', true);
    }

    /**
     * Accesseur : libellé traduit de la devise via la traduction active.
     */
    public function getDisplayNameAttribute(): ?string
    {
        return $this->active_translation?->name ?? $this->code;
    }
}
