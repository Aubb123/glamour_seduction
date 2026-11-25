<?php

namespace App\Traits;

use App\Enums\Audits\AuditAction;
use App\Models\Audit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

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

        // Lors d'une suppression DÉFINITIVE (forceDelete) : on log AVANT la suppression.
        //
        // NB : `isForceDeleting()` n'existe QUE si le modèle utilise SoftDeletes.
        // Les modèles sans soft delete (ex : ShopUser) font une
        // suppression physique : `isForceDeleting()` est alors absent → on ne
        // journalise pas ici, l'événement `deleted` s'en charge ci-dessous.
        static::deleting(function ($model) {
            if (method_exists($model, 'isForceDeleting') && $model->isForceDeleting()) {
                $model->logAudit(AuditAction::ForceDeleted);
            }
        });

        // Suppression : `deleted` est déclenché pour un soft delete ET pour une
        // suppression physique. On ignore le cas forceDelete (déjà loggué dans
        // `deleting`) pour éviter un doublon.
        static::deleted(function ($model) {
            if (method_exists($model, 'isForceDeleting') && $model->isForceDeleting()) {
                return; // ← déjà journalisé côté `deleting`
            }

            $model->logAudit(AuditAction::Deleted);
        });

        // Lors d'une restauration de soft-delete (événement absent sans SoftDeletes)
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

    // ─────────────────────────────────────────────
    // REQUÊTES AGRÉGÉES : entité + traductions
    // ─────────────────────────────────────────────
    // Les modifications de contenu (nom, titre, description…) sont journalisées
    // sur le modèle de traduction, pas sur l'entité parente : ces helpers
    // agrègent les deux. La corbeille est incluse (une traduction supprimée
    // conserve ses audits).

    /**
     * Audits de l'entité ET de ses traductions.
     *
     * Retourne un Builder (et non une Relation) : à réserver aux listes
     * d'audits d'une fiche détail — ne pas utiliser avec withCount().
     */
    public function auditsWithTranslations(): Builder
    {
        $translationModel = $this->translationModelNameForAudits();

        return Audit::query()->where(fn ($query) => $query
            ->where(fn ($parent) => $parent
                ->where('auditable_type', static::class)
                ->where('auditable_id', $this->getKey()))
            ->when($translationModel, fn ($query) => $query->orWhere(fn ($translation) => $translation
                ->where('auditable_type', $translationModel)
                ->whereIn('auditable_id', $this->translations()->withTrashed()->select('id')))));
    }

    /**
     * Nombre total d'audits (entité + traductions), calculé à la demande.
     */
    public function auditsCountTotal(): int
    {
        return (int) $this->auditsWithTranslations()->count();
    }

    /**
     * Badge `audits_count` (listes, navigation, statistiques).
     *
     * Prioritaire sur la valeur brute fournie par withCount()/loadCount() :
     * celle-ci n'englobe pas les audits des traductions. Si le total a déjà été
     * calculé en SQL par scopeWithAuditsCountTotal(), il est réutilisé.
     */
    public function getAuditsCountAttribute($value): int
    {
        return (int) ($this->getAttributes()['audits_count_total'] ?? $this->auditsCountTotal());
    }

    /**
     * Calcule le total en une seule requête (une sous-requête par source),
     * exposé dans l'attribut `audits_count_total` — aucune requête par ligne :
     *   BlogTag::withAuditsCountTotal()->withCount([...])->get()
     */
    public function scopeWithAuditsCountTotal($query)
    {
        $parent = Audit::query()->selectRaw('count(*)')
            ->where('auditable_type', static::class)
            ->whereColumn('auditable_id', $this->getTable().'.id');

        $translationModel = $this->translationModelNameForAudits();

        if ($translationModel === null) {
            $sql = '('.$parent->toSql().')';
            $bindings = $parent->getBindings();
        } else {
            $foreignKey = $this->getTranslationForeignKey();
            $translationTable = (new $translationModel)->getTable();

            $translation = Audit::query()->selectRaw('count(*)')
                ->where('auditable_type', $translationModel)
                ->whereIn('auditable_id', fn ($sub) => $sub->select('id')->from($translationTable)
                    ->whereColumn($foreignKey, $this->getTable().'.id'));

            $sql = '(('.$parent->toSql().') + ('.$translation->toSql().'))';
            $bindings = array_merge($parent->getBindings(), $translation->getBindings());
        }

        return $query->addSelect($this->getTable().'.*')
            ->addSelect(DB::raw($sql.' as audits_count_total'))
            ->addBinding($bindings, 'select');
    }

    /**
     * Modèle de traduction à agréger, ou null si l'entité n'en a pas.
     */
    protected function translationModelNameForAudits(): ?string
    {
        if (! method_exists($this, 'getTranslationModelName')) {
            return null;
        }

        $model = $this->getTranslationModelName();

        return class_exists($model) && str_ends_with($model, 'Translation') ? $model : null;
    }
}
