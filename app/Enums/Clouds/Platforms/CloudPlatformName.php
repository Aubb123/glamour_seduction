<?php

namespace App\Enums\Clouds\Platforms;

enum CloudPlatformName: string
{
    case BackblazeB2 = 'backblaze_b2';

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
