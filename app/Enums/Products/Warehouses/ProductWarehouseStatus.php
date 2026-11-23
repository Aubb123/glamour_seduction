<?php

namespace App\Enums\Products\Warehouses;

use function __;

enum ProductWarehouseStatus: string
{
    case Active = 'active'; // Entrepôt actif (utilisable)
    case Inactive = 'inactive'; // Entrepôt inactif (masqué des opérations)

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
     * Libellé traduit du statut de l'entrepôt.
     */
    public function label(): string
    {
        return match ($this) {
            self::Active => __('Actif'),
            self::Inactive => __('Inactif'),
        };
    }

    /**
     * Classe CSS du badge associé au statut de l'entrepôt.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Active => 'badge badge-soft-success',
            self::Inactive => 'badge badge-soft-danger',
        };
    }
}
