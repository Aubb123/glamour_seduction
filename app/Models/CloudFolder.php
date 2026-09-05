<?php

namespace App\Models;

use App\Enums\Clouds\Folders\CloudFolderStatus;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CloudFolder extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    // Les dossiers cloud ne gèrent pas de traductions séparées
    protected bool $hasTranslations = false;

    // Table associée aux dossiers cloud
    protected $table = 'cloud_folders';

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'cloud_platform_id',
        'cloud_folder_id',
        'folder_name',
        'path',
        'folder_id',
        'entity_table',
        'content_type',
        'metadata',
        'status',
    ];

    // Casts pour les attributs du modèle
    protected $casts = [
        'status' => CloudFolderStatus::class,
        'content_type' => 'array',
        'metadata' => 'array',
    ];

    // Relations enfants dont la présence bloque la suppression de ce dossier (delete / force_delete) lors d'une supression temporaire (BackendHelper::destroy_function) ou définitive (BackendHelper::force_destroy_function)
    protected array $dependentRelations = ['children', 'files'];

    // Clé de route utilisée pour le binding implicite ({cloudFolder})
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Relation vers la plateforme cloud parente.
     */
    public function platform(): BelongsTo
    {
        return $this->belongsTo(CloudPlatform::class, 'cloud_platform_id');
    }

    /**
     * Relation vers le dossier parent, si présent.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(CloudFolder::class, 'cloud_folder_id');
    }

    /**
     * Relation vers les sous-dossiers de ce dossier.
     */
    public function children(): HasMany
    {
        return $this->hasMany(CloudFolder::class, 'cloud_folder_id');
    }

    /**
     * Relation vers les fichiers contenus dans ce dossier.
     */
    public function files(): HasMany
    {
        return $this->hasMany(CloudFile::class, 'cloud_folder_id');
    }

    /**
     * Scope pour récupérer uniquement les dossiers actifs.
     */
    public function scopeActive($query): Builder
    {
        return $query->where('status', CloudFolderStatus::Active);
    }
}
