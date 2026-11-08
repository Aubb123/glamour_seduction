<?php

namespace App\Models;

use App\Enums\Clouds\Platforms\CloudPlatformStatus;
use App\Models\CloudFile;
use App\Models\CloudFolder;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class CloudPlatform extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    // Les plateformes cloud ne gèrent pas de traductions séparées
    protected bool $hasTranslations = false;

    // Table associée aux plateformes cloud
    protected $table = 'cloud_platforms';

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'name',
        'label',
        'api_endpoint',
        'disk_name',
        'api_key',
        'api_secret',
        'description',
        'metadata',
        'status',
    ];

    // Attributs à cacher lors de la sérialisation
    protected $hidden = [
        'api_key',
        'api_secret',
    ];

    // Casts pour les attributs du modèle
    protected $casts = [
        'status' => CloudPlatformStatus::class,
        'metadata' => 'array',
    ];

    // Relations enfants dont la présence bloque la suppression de cette plateforme (delete / force_delete) lors d'une supression temporaire (BackendHelper::destroy_function) ou définitive (BackendHelper::force_destroy_function)
    protected array $dependentRelations = ['folders'];

    // Clé de route utilisée pour le binding implicite ({cloudPlatform})
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function getSlugSourceField(): string
    {
        return 'label';
    }

    protected static function getSlugWatchedFields(): array
    {
        return ['label'];
    }

    // ─────────────────────────────────────────────
    // RELATIONS
    // ─────────────────────────────────────────────

    /**
     * Relation vers les dossiers rattachés à cette plateforme.
     */
    public function folders(): HasMany
    {
        return $this->hasMany(CloudFolder::class, 'cloud_platform_id');
    }

    /**
     * Relation vers les fichiers rattachés à cette plateforme via les dossiers.
     */
    public function files(): HasManyThrough
    {
        return $this->hasManyThrough(CloudFile::class, CloudFolder::class, 'cloud_platform_id', 'cloud_folder_id');
    }

    // ─────────────────────────────────────────────
    // SCOPES
    // ─────────────────────────────────────────────

    /**
     * Scope pour récupérer uniquement les plateformes actives.
     */
    public function scopeActive($query): Builder
    {
        return $query->where('status', CloudPlatformStatus::Active);
    }
}
