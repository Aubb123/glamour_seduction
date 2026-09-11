<?php

namespace App\Enums\Products\StockMovements;

use function __;

enum ProductStockMovementType: string
{
    case In = 'in'; // Entrée de stock (réassort, production)
    case Out = 'out'; // Sortie de stock (vente, expédition)
    case Adjustment = 'adjustment'; // Correction d'inventaire (diminue le stock)
    case TransferIn = 'transfer_in'; // Entrée par transfert (depuis un autre entrepôt)
    case TransferOut = 'transfer_out'; // Sortie par transfert (vers un autre entrepôt)
    case Return = 'return'; // Retour client (réintègre le stock)

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
     * Libellé traduit du type de mouvement.
     */
    public function label(): string
    {
        return match ($this) {
            self::In => __('Entrée'),
            self::Out => __('Sortie'),
            self::Adjustment => __('Ajustement'),
            self::TransferIn => __('Transfert (entrée)'),
            self::TransferOut => __('Transfert (sortie)'),
            self::Return => __('Retour'),
        };
    }

    /**
     * Classe CSS du badge associé au type.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::In, self::TransferIn, self::Return => 'badge badge-soft-success',
            self::Out, self::TransferOut, self::Adjustment => 'badge badge-soft-danger',
        };
    }

    /**
     * Indique si le mouvement fait DIMINUER le stock de l'entrepôt.
     * (in / return / transfer_in augmentent le stock ; les autres le diminuent.)
     */
    public function decreasesStock(): bool
    {
        return match ($this) {
            self::In, self::TransferIn, self::Return => false,
            self::Out, self::Adjustment, self::TransferOut => true,
        };
    }
}
