<?php

namespace App\Models;

use App\Enums\Permissions\PermissionStatus;
use App\Models\Role;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    // Les permissions ne gèrent pas de traductions séparées
    protected bool $hasTranslations = false;

    /**
     * Clé de liaison utilisée par le model binding implicite (routes {permission}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'name',
        'guard_name',
        'status',
    ];

    // Casts pour les attributs du modèle
    protected $casts = [
        'status' => PermissionStatus::class,
    ];

    /**
     * Relation : une permission est attribuée à plusieurs rôles.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_has_permissions');
    }
}
