<?php

namespace App\Enums\Clouds\Files;

use function __;

/**
 * Statut de synchronisation cloud d'un fichier (source unique de vérité).
 *
 * Les valeurs DOIVENT correspondre strictement à l'énum MySQL de la colonne
 * `cloud_files.sync_status` : pending, processing, synced, error.
 */
enum CloudFileStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Synced = 'synced';
    case Error = 'error';

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
     * Libellé traduit du statut de synchronisation.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending => __('En attente'),
            self::Processing => __('En cours'),
            self::Synced => __('Synchronisé'),
            self::Error => __('Erreur'),
        };
    }

    /**
     * Classe CSS du badge associé au statut.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending => 'badge-soft-warning',
            self::Processing => 'badge-soft-info',
            self::Synced => 'badge-soft-success',
            self::Error => 'badge-soft-danger',
        };
    }

    /**
     * Icône Feather associée au statut.
     */
    public function icon(): string
    {
        return match ($this) {
            self::Pending => 'feather-clock',
            self::Processing => 'feather-refresh-cw',
            self::Synced => 'feather-check-circle',
            self::Error => 'feather-alert-circle',
        };
    }
}
