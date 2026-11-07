<?php

namespace App\Enums\Products\Attributes;

use function __;

/**
 * Type de valeur d'un attribut de produit.
 *
 * - Text  : la valeur est une simple clé/libellé (ex : "S", "M", "XL").
 * - Color : la valeur doit impérativement porter un code couleur HEX
 *           (ex : "#FF0000"), stocké sur product_attribute_values.hex_color.
 */
enum ProductAttributeType: string
{
    case Text = 'text';
    case Color = 'color';

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
     * Libellé traduit du type d'attribut de produits.
     */
    public function label(): string
    {
        return match ($this) {
            self::Text => __('Texte'),
            self::Color => __('Couleur'),
        };
    }

    /**
     * Indique si ce type impose un code couleur HEX par valeur.
     */
    public function requiresHexColor(): bool
    {
        return $this === self::Color;
    }
}
