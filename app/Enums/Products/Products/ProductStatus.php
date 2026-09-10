<?php

namespace App\Enums\Products\Products;

use function __;

enum ProductStatus: string
{
    case Draft = 'draft'; // Brouillon — visible uniquement côté admin
    case Published = 'published'; // Publié — visible publiquement sur la boutique
    case Archived = 'archived'; // Archivé — retiré de la vente

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
     * Libellé traduit du statut du produit.
     */
    public function label(): string
    {
        return match ($this) {
            self::Draft => __('Brouillon'),
            self::Published => __('Publié'),
            self::Archived => __('Archivé'),
        };
    }

    /**
     * Classe CSS du badge associé au statut.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Draft => 'badge badge-soft-warning',
            self::Published => 'badge badge-soft-success',
            self::Archived => 'badge badge-soft-secondary',
        };
    }
}