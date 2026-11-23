<?php

namespace App\Enums\Products\Warehouses;

use function __;

enum ProductWarehouseType: string
{
    case Storage = 'storage'; // Entrepôt de stockage principal
    case Returns = 'returns'; // Entrepôt dédié aux retours
    case Transit = 'transit'; // Entrepôt de transit / expédition

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
     * Libellé traduit du type d'entrepôt.
     */
    public function label(): string
    {
        return match ($this) {
            self::Storage => __('Stockage'),
            self::Returns => __('Retours'),
            self::Transit => __('Transit'),
        };
    }

    /**
     * Classe CSS du badge associé au type d'entrepôt.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Storage => 'badge badge-soft-success',
            self::Returns => 'badge badge-soft-warning',
            self::Transit => 'badge badge-soft-info',
        };
    }
}
