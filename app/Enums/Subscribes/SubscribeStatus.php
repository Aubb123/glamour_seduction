<?php

namespace App\Enums\Subscribes;

use function __;

enum SubscribeStatus: string
{
    case Active = 'active'; // Abonnement actif
    case Inactive = 'inactive'; // Abonnement désactivé (désabonné)

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
     * Libellé traduit du statut de l'abonnement.
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
