<?php

namespace App\Models;

use App\Enums\Countries\CountryType;
use App\Enums\Geos\Countries\GeoCountryStatus;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GeoCountry extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    // Table associée aux pays
    protected $table = 'geo_countries';

    // Clé étrangère utilisée pour les traductions de pays
    protected string $translationForeignKey = 'country_id';

    // Modèle de traduction lié au pays
    protected string $translationModel = GeoCountryTranslation::class;

    // Relations enfants dont la présence bloque la suppression de ce pays (delete / force_delete) lors d'une supression temporaire (BackendHelper::destroy_function) ou définitive (BackendHelper::force_destroy_function)
    protected array $dependentRelations = ['territories', 'users'];

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'continent_id',
        'type',
        'sovereign_id',
        'code',
        'code_2',
        'phone_code',
        'currency_id',
        'file_path',
        'sort_order',
        'status',
    ];

    // Casts pour les attributs du modèle
    protected $casts = [
        'status' => GeoCountryStatus::class,
        'type' => 'string',
    ];

    /**
     * Clé de liaison utilisée par le model binding implicite (routes {geoCountry}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Relation vers le continent du pays.
     */
    public function continent(): BelongsTo
    {
        return $this->belongsTo(GeoContinent::class, 'continent_id');
    }

    /**
     * Relation vers le pays souverain lorsque ce pays est une dépendance.
     */
    public function sovereign(): BelongsTo
    {
        return $this->belongsTo(GeoCountry::class, 'sovereign_id');
    }

    /**
     * Relation vers les territoires ou dépendances rattachés à ce pays.
     */
    public function territories(): HasMany
    {
        return $this->hasMany(GeoCountry::class, 'sovereign_id');
    }

    /**
     * Relation vers la devise principale du pays (normalisée via la table currencies).
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    /**
     * Relation : un pays a plusieurs utilisateurs.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'country_id');
    }

    /**
     * Retourne la liste complète des libellés de types de pays.
     * Source unique de vérité : l'enum CountryType.
     */
    public static function typeLabels(): array
    {
        return collect(CountryType::cases())
            ->mapWithKeys(fn (CountryType $type) => [$type->value => $type->label()])
            ->all();
    }

    /**
     * Accesseur : libellé traduit du type de pays.
     */
    public function getTypeLabelAttribute(): string
    {
        return CountryType::tryFrom((string) $this->type)?->label() ?? $this->type;
    }

    /**
     * Scope pour récupérer uniquement les pays actifs.
     */
    public function scopeActive($query): Builder
    {
        return $query->where('status', GeoCountryStatus::Active);
    }
}
