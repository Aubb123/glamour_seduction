<?php

namespace App\Enums\Countries;

use function __;

enum CountryType: string
{
    case Country = 'country'; // État souverain reconnu (France, Japon...)
    case Territory = 'territory'; // Territoire/dépendance (Guam, Nouvelle-Calédonie...)
    case SpecialAdministrativeRegion = 'special_administrative_region'; // Région administrative spéciale (Hong Kong, Macao...)
    case Disputed = 'disputed'; // Reconnaissance limitée/contestée (Taïwan, Kosovo...)

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
     * Libellé traduit du type de pays.
     */
    public function label(): string
    {
        return __("v1/backend/views/pages/geos/countries/messages/countries-messages-model.typeLabels.{$this->value}");
    }
}
