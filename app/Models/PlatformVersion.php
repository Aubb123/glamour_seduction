<?php

namespace App\Models;

use App\Enums\PlatformVersions\PlatformVersionStatus;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PlatformVersion extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    /**
     * Désactive la génération automatique du slug pour PlatformVersion.
     * Le trait HasSlug (inclus via HasModelBehaviors) est volontairement neutralisé ici :
     * le champ "code" ("v1", "v2", ...) est un champ métier saisi et validé manuellement
     * dans les FormRequests, et ne doit pas être réécrit depuis un champ "name" (qui
     * n'existe pas sur ce modèle, les noms vivant dans les traductions).
     */
    public static function bootHasSlug(): void
    {
        // No-op : on ne branche pas les hooks Eloquent de génération de slug.
    }

    // Modèle de traduction lié à cette version
    protected string $translationModel = PlatformVersionTranslation::class;

    // Clé étrangère utilisée pour les traductions de version
    protected string $translationForeignKey = 'platform_version_id';

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'code',
        'status',
        'is_published',
        'is_default',
        'released_at',
    ];

    // Casts pour les types de données
    protected $casts = [
        'released_at' => 'datetime',
        'status' => PlatformVersionStatus::class,
        'is_published' => 'boolean',
        'is_default' => 'boolean',
    ];

    /**
     * Clé de liaison utilisée par le model binding implicite (routes {platformVersion}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Vérifie si une version donnée est publiée.
     */
    public static function isPublished(string $code): bool
    {
        return static::where('code', $code)->where('is_published', true)->exists();
    }

    /**
     * Scope pour récupérer uniquement les versions actives.
     */
    public function scopeActive($query): Builder
    {
        return $query->where('status', PlatformVersionStatus::Active);
    }

    /**
     * Scope pour récupérer uniquement la version par défaut.
     */
    public function scopeDefaultVersion($query): Builder
    {
        return $query->where('is_default', true);
    }
}
