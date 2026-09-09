<?php

namespace App\Enums\Audits;

use function __;

/**
 * Actions de journalisation réalisées sur une entité auditée.
 *
 * Source unique de vérité pour la validation de la colonne `action`
 * de la table `audits` (déplacée d'un `enum` SQL vers une `string`).
 */
enum AuditAction: string
{
    case Created = 'created';
    case Updated = 'updated';
    case Deleted = 'deleted';
    case ForceDeleted = 'force_deleted';
    case Restored = 'restored';

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
     * Libellé traduit de l'action.
     */
    public function label(): string
    {
        return match ($this) {
            self::Created => __('Création'),
            self::Updated => __('Modification'),
            self::Deleted => __('Suppression'),
            self::ForceDeleted => __('Suppression définitive'),
            self::Restored => __('Restauration'),
        };
    }

    /**
     * Classe CSS du badge associé à l'action (hors préfixe `badge`).
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Created => 'badge-soft-success',
            self::Updated => 'badge-soft-info',
            self::Deleted => 'badge-soft-warning',
            self::ForceDeleted => 'badge-soft-danger',
            self::Restored => 'badge-soft-primary',
        };
    }

    /**
     * Icône Feather associée à l'action.
     */
    public function icon(): string
    {
        return match ($this) {
            self::Created => 'feather-plus-circle',
            self::Updated => 'feather-edit',
            self::Deleted => 'feather-trash',
            self::ForceDeleted => 'feather-x-circle',
            self::Restored => 'feather-refresh-cw',
        };
    }
}
