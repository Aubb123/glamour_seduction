<?php

namespace App\Enums\Products\Warehouses;

use function __;

enum ProductWarehouseStockTrackingMode: string
{
    case Warehouse = 'warehouse'; // Stock suivi directement au niveau de l'entrepôt
    case Location = 'location'; // Stock suivi par emplacement dans l'entrepôt

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
     * Libellé traduit du mode de suivi du stock.
     */
    public function label(): string
    {
        return match ($this) {
            self::Warehouse => __('Suivi global par entrepôt'),
            self::Location => __('Suivi détaillé par emplacement'),
        };
    }

    /**
     * Classe CSS du badge associé au mode de suivi du stock.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Warehouse => 'badge badge-soft-info',
            self::Location => 'badge badge-soft-primary',
        };
    }
}
