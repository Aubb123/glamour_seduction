<?php

namespace App\Enums\Geos\DivisionTypes;

use function __;

enum GeoDivisionTypeStatus: string
{
    case Active = 'active'; // Type utilisable pour rattacher des divisions
    case Inactive = 'inactive'; // Type masqué / non sélectionnable

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
     * Libellé traduit du statut du type de division.
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
