<?php

namespace App\Models;

use App\Enums\Clouds\Files\CloudFileStatus;
use App\Enums\Clouds\Files\CloudFileType;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CloudFile extends Model
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    // Les fichiers cloud ne gèrent pas de traductions séparées
    protected bool $hasTranslations = false;

    // Table associée aux fichiers cloud
    protected $table = 'cloud_files';

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'cloud_folder_id',
        'fileable_type',
        'fileable_id',
        'cloud_file_id', // Réservé : toujours null (le chemin cloud est stocké dans remote_path)
        'remote_path',
        'sync_status',
        'local_path',
        'last_sync_attempt_at',
        'file_name',
        'file_type',
        'file_size',
        'mime_type',
        'shareable_link',
        'hash',
        'metadata',
    ];

    // Casts pour les attributs du modèle
    protected $casts = [
        'metadata' => 'array',
        'file_size' => 'integer',
        'last_sync_attempt_at' => 'datetime',
        'sync_status' => CloudFileStatus::class,
    ];

    // Clé de route utilisée pour le binding implicite ({cloudFile})
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Relation vers le dossier cloud contenant ce fichier.
     */
    public function cloudFolder(): BelongsTo
    {
        return $this->belongsTo(CloudFolder::class, 'cloud_folder_id');
    }

    /**
     * Relation polymorphique vers l'entité associée (User, BlogPost, Gallery, etc.)
     * Un fichier cloud appartient à une entité via la relation polymorphique fileable.
     */
    public function fileable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Accesseur : retourne le libellé lisible du type de fichier (délégué à l'enum CloudFileType).
     */
    public function getFileTypeLabelAttribute(): string
    {
        return CloudFileType::tryFrom($this->file_type)?->label() ?? ucfirst($this->file_type);
    }

    /**
     * Accesseur : retourne la classe CSS du badge selon le type de fichier (délégué à l'enum CloudFileType).
     */
    public function getFileTypeBadgeAttribute(): string
    {
        return CloudFileType::tryFrom($this->file_type)?->badgeClass() ?? 'badge-soft-secondary';
    }

    /**
     * Scope : fichiers éligibles à une migration cloud (retries bornés).
     *
     * Seuls les fichiers en "pending"/"error" dont la dernière tentative est absente
     * ou plus vieille que « retryAfterMinutes » sont traités, afin d'éviter les
     * boucles infinies sur les fichiers en échec permanent.
     */
    public function scopeRetryable(Builder $query, ?int $retryAfterMinutes = null): Builder
    {
        // Seuil par défaut piloté par le setting "cloudfiles.retry_sync_after_minutes".
        $retryAfterMinutes ??= (int) (getSettingValue('retry_sync_after_minutes', app()->getLocale()));

        return $query->whereIn('sync_status', [CloudFileStatus::Pending, CloudFileStatus::Error])
            ->where(function (Builder $query) use ($retryAfterMinutes) {
                $query->whereNull('last_sync_attempt_at')
                    ->orWhere('last_sync_attempt_at', '<', now()->subMinutes($retryAfterMinutes));
            });
    }

    /**
     * Repasse en "pending" les fichiers restés bloqués en "processing" depuis trop
     * longtemps (processus interrompu ou crash). Retourne le nombre de fichiers réinitialisés.
     */
    public static function resetStuckProcessing(?int $minutes = null): int
    {
        // Seuil par défaut piloté par le setting "cloudfiles.reset_stuck_processing_after_minutes".
        $minutes ??= (int) (getSettingValue('reset_stuck_processing_after_minutes', app()->getLocale()));

        return static::query()
            ->where('sync_status', CloudFileStatus::Processing)
            ->where('last_sync_attempt_at', '<', now()->subMinutes($minutes))
            ->update(['sync_status' => CloudFileStatus::Pending]);
    }

    /**
     * Accesseur : retourne la taille du fichier formatée lisiblement.
     */
    public function getFormattedSizeAttribute(): string
    {
        if (! $this->file_size) {
            return '—';
        }

        $size = $this->file_size;
        $units = ['o', 'Ko', 'Mo', 'Go', 'To'];

        foreach ($units as $unit) {
            if ($size < 1024) {
                return round($size, 2).' '.$unit;
            }
            $size /= 1024;
        }

        return round($size, 2).' '.end($units);
    }
}
