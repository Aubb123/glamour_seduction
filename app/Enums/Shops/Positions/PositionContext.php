<?php

namespace App\Enums\Shops\Positions;

use function __;

enum PositionContext: string
{
    case Shop = 'shop'; // Poste réservé aux boutiques
    case Warehouse = 'warehouse'; // Poste réservé aux entrepôts
    case Both = 'both'; // Poste utilisable dans les deux

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
     * Libellé traduit de la portée du poste.
     */
    public function label(): string
    {
        return match ($this) {
            self::Shop => __('Boutique'),
            self::Warehouse => __('Entrepôt'),
            self::Both => __('Boutique et entrepôt'),
        };
    }

    /**
     * Classe CSS du badge associé à la portée.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Shop => 'badge badge-soft-primary',
            self::Warehouse => 'badge badge-soft-info',
            self::Both => 'badge badge-soft-secondary',
        };
    }
}
