<?php

namespace App\Enums\Shops\Shops;

use function __;

enum ShopStatus: string
{
    case Active = 'active'; // Boutique ouverte / opérationnelle
    case Inactive = 'inactive'; // Boutique fermée / suspendue

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
     * Libellé traduit du statut de la boutique.
     */
    public function label(): string
    {
        return match ($this) {
            self::Active => __('Active'),
            self::Inactive => __('Inactive'),
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
