<?php

namespace App\Enums\Users;

use function __;

enum UserStatus: string
{
    case Active = 'active'; // Actif
    case Suspended = 'suspended'; // Suspendu
    case Banned = 'banned'; // Banni

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
     * Libellé traduit du statut.
     */
    public function label(): string
    {
        return match ($this) {
            self::Active => __('Actif'),
            self::Suspended => __('Suspendu'),
            self::Banned => __('Banni'),
        };
    }

    /**
     * Classe CSS du badge associé au statut.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Active => 'badge bg-soft-success',
            self::Suspended => 'badge bg-soft-warning',
            self::Banned => 'badge bg-soft-danger',
        };
    }
}
