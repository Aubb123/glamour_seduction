<?php

namespace App\Enums\Galleries;

use function __;

enum GalleryStatus: string
{
    case Active = 'active'; // Visible publiquement
    case Inactive = 'inactive'; // Masquée temporairement

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
     * Libellé traduit du statut de la galerie.
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
            self::Active => 'badge bg-soft-success',
            self::Inactive => 'badge bg-soft-danger',
        };
    }
}
