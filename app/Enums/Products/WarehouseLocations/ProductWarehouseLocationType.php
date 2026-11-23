<?php

namespace App\Enums\Products\WarehouseLocations;

use function __;

enum ProductWarehouseLocationType: string
{
    case Zone = 'zone'; // Zone logistique (regroupement de plusieurs allées)
    case Aisle = 'aisle'; // Allée de circulation dans une zone
    case Shelf = 'shelf'; // Étagère / rayonnage dans une allée
    case Bin = 'bin'; // Bac / casier de stockage sur une étagère

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
     * Libellé traduit du type d'emplacement.
     */
    public function label(): string
    {
        return match ($this) {
            self::Zone => __('Zone'),
            self::Aisle => __('Allée'),
            self::Shelf => __('Étagère'),
            self::Bin => __('Bac'),
        };
    }

    /**
     * Classe CSS du badge associé au type d'emplacement.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Zone => 'badge badge-soft-primary',
            self::Aisle => 'badge badge-soft-info',
            self::Shelf => 'badge badge-soft-success',
            self::Bin => 'badge badge-soft-warning',
        };
    }
}
