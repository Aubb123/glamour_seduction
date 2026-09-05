<?php

namespace App\Enums\Clouds\Folders;

use function __;

enum CloudFolderStatus: string
{
    case Active = 'active'; // Dossier opérationnel, utilisable pour stocker des fichiers
    case Inactive = 'inactive'; // Dossier masqué/désactivé temporairement

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
     * Libellé traduit du statut du dossier cloud.
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
