<?php

namespace App\Enums\Products\Categories;

use function __;

enum ProductCategoryStatus: string
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
        // Retourne les valeurs de l'énumération (source unique de vérité pour la validation)
        return array_column(self::cases(), 'value'); // ['active', 'inactive']
    }

    /**
     * Libellé traduit du statut de la catégorie de produits.
     */
    public function label(): string
    {
        // Retourne le libellé traduit du statut de la catégorie de produits
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
        // Retourne la classe CSS du badge associé au statut de la catégorie de produits
        return match ($this) {
            self::Active => 'badge badge-soft-success',
            self::Inactive => 'badge badge-soft-danger',
        };
    }
}
