<?php

namespace App\Enums\Permissions;

use function __;

enum PermissionStatus: string
{
    case Active = 'active'; // Permission active (effective)
    case Inactive = 'inactive'; // Permission désactivée (sans effet)

    /**
     * Liste des valeurs acceptées (source unique de vérité pour la validation).
     *
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Libellé traduit du statut de la permission.
     */
    public function label(): string
    {
        return match ($this) {
            self::Active => __('Actif'),
            self::Inactive => __('Inactif'),
        };
    }

    /**
     * Classe CSS du badge associé au statut.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Active => 'badge badge-soft-success',
            self::Inactive => 'badge badge-soft-danger',
        };
    }
}
