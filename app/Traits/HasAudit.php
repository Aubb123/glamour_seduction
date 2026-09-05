<?php

namespace App\Traits;

use App\Enums\Audits\AuditAction;
use App\Models\Audit;

trait HasAudit
{
    public static function bootHasAudit(): void
    {
        // Lors de la création, on log l'action et les valeurs créées
        static::created(function ($model) {
            $model->logAudit(AuditAction::Created);
        });

        // Lors d'une mise à jour, on log uniquement si des champs ont réellement changé
        static::updated(function ($model) {
            // Une restauration de soft-delete déclenche aussi `updated` :
            // on laisse l'événement `restored` s'en charger pour éviter un doublon.
            if (method_exists($model, 'getDeletedAtColumn') && $model->wasChanged($model->getDeletedAtColumn())) {
                return;
            }

            if (empty($model->getDirty())) {
                return;
            }

            $model->logAudit(AuditAction::Updated);
        });

        // Lors d'un soft delete uniquement (pas lors d'un forceDelete)
        static::deleted(function ($model) {
            if ($model->isForceDeleting()) {
                return;
            } // ← on ignore les forceDelete ici
            $model->logAudit(AuditAction::Deleted);
        });

        // Lors d'une suppression définitive, on log séparément AVANT la suppression
        static::deleting(function ($model) {
            if (! $model->isForceDeleting()) {
                return;
            } // ← uniquement pour les forceDelete
            $model->logAudit(AuditAction::ForceDeleted);
        });

        // Lors d'une restauration de soft-delete
        static::restored(function ($model) {
            $model->logAudit(AuditAction::Restored);
        });
    }

    /**
     * Enregistre un audit pour l'action donnée.
     *
     * Les valeurs sont alignées sur les seuls champs réellement modifiés :
     *  - created  : new_values = état complet nouvellement créé ;
     *  - updated  : old_values = anciennes valeurs (uniquement champs modifiés),
     *               new_values = nouvelles valeurs (champs modifiés) ;
     *  - restored : new_values = état courant restauré ;
     *  - deleted / force_deleted : aucune valeur (l'entité disparaît).
     */
    public function logAudit(string|AuditAction $action): void
    {
        $action = $action instanceof AuditAction ? $action : AuditAction::tryFrom($action);

        // Valeur d'action invalide : on refuse de journaliser.
        if ($action === null) {
            return;
        }

        $changes = $this->getChanges();
        $oldValues = null;
        $newValues = null;

        if ($action === AuditAction::Created || $action === AuditAction::Restored) {
            $newValues = $this->getAttributes();
        } elseif ($action === AuditAction::Updated && ! empty($changes)) {
            $oldValues = array_intersect_key($this->getOriginal(), $changes);
            $newValues = $changes;
        }

        $this->audits()->create([
            'user_id' => auth()->check() ? auth()->id() : null,
            'action' => $action->value,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => app()->runningInConsole() ? '127.0.0.1' : request()->ip(),
        ]);
    }

    // Relation polymorphique vers la table audits
    public function audits()
    {
        return $this->morphMany(Audit::class, 'auditable');
    }
}
