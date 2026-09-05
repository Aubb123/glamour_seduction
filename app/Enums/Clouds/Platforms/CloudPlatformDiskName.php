<?php

namespace App\Enums\Clouds\Platforms;

enum CloudPlatformDiskName: string
{
    case B2 = 'b2';

    /**
     * Liste des valeurs acceptées (source unique de vérité pour la validation).
     *
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
