<?php

namespace App\Models;

use App\Enums\Roles\RoleStatus;
use App\Models\Permission;
use App\Models\RoleTranslation;
use App\Traits\HasModelBehaviors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    // Traits pour les comportements communs (UUID, Slug, timestamps, etc.)
    use HasModelBehaviors;

    // Attributs autorisés pour l'assignation de masse
    protected $fillable = [
        'name', // Colonne "name" requise par Spatie
        'guard_name', // Colonne "guard_name" requise par Spatie
        'status',
    ];

    // Casts pour les attributs du modèle
    protected $casts = [
        'status' => RoleStatus::class,
    ];

    // Clé étrangère utilisée pour les traductions de rôle
    protected string $translationForeignKey = 'role_id';

    /**
     * Clé de liaison utilisée par le model binding implicite (routes {role}).
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Scope pour récupérer uniquement les rôles actifs.
     */
    public function scopeActive($query): Builder
    {
        return $query->where('status', RoleStatus::Active);
    }

    /**
     * Relation vers les traductions de ce rôle.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(RoleTranslation::class);
    }

    /**
     * Relation : un rôle a plusieurs permissions.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions');
    }
}
