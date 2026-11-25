<?php

namespace App\Services\V1\Controllers;

use App\Enums\Geos\Divisions\GeoDivisionStatus;
use App\Models\GeoDivision;
use App\Models\GeoDivisionTranslation;
use Illuminate\Support\Facades\DB;

use function __;

class GeoDivisionService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/geos/divisions/messages/divisions-messages-service.divisions';

    // Chemin des messages génériques du backend helper
    protected string $backendHelperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_geodivisions_store(array $data): GeoDivision
    {
        return DB::transaction(function () use ($data) {

            $status = GeoDivisionStatus::tryFrom($data['status'] ?? '') ?? GeoDivisionStatus::Inactive;

            // Niveau calculé automatiquement : parent.level + 1, ou 1 à la racine
            $level = $this->resolveLevel($data['parent_id'] ?? null);

            $division = GeoDivision::create([
                'country_id' => $data['country_id'],
                'parent_id' => $data['parent_id'] ?? null,
                'division_type_id' => $data['division_type_id'],
                'level' => $level,
                'notes' => $data['notes'] ?? null,
                'status' => $status,
            ]);

            $this->syncTranslations($division, $data['translations']);

            return $division;
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_geodivisions_update(GeoDivision $division, array $data): GeoDivision
    {
        return DB::transaction(function () use ($division, $data) {

            $status = GeoDivisionStatus::tryFrom($data['status'] ?? '') ?? $division->status;

            // Le pays est immuable : jamais mis à jour.
            // Si le parent change, on recalcule le niveau de la division et de tous ses descendants.
            $parentChanged = array_key_exists('parent_id', $data)
                && (int) ($data['parent_id'] ?? 0) !== (int) ($division->parent_id ?? 0);

            $updateData = [
                'division_type_id' => $data['division_type_id'],
                'status' => $status,
            ];

            if (array_key_exists('notes', $data)) {
                $updateData['notes'] = $data['notes'];
            }

            if ($parentChanged) {
                $updateData['parent_id'] = $data['parent_id'] ?? null;
                $updateData['level'] = $this->resolveLevel($data['parent_id'] ?? null);
            }

            $division->update($updateData);

            // Recalcul en cascade des niveaux des descendants si le parent a changé
            if ($parentChanged) {
                $this->recalculateDescendantLevels($division);
            }

            if (array_key_exists('translations', $data)) {
                $this->syncTranslations($division, $data['translations']);
            }

            return $division;
        });
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_geodivisions_activate(GeoDivision $division): void
    {
        DB::transaction(function () use ($division) {

            if ($division->status === GeoDivisionStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.activate.already_active"));
            }

            $division->update(['status' => GeoDivisionStatus::Active]);
        });
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_geodivisions_deactivate(GeoDivision $division): void
    {
        DB::transaction(function () use ($division) {

            if ($division->status === GeoDivisionStatus::Inactive) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.deactivate.already_inactive"));
            }

            $division->update(['status' => GeoDivisionStatus::Inactive]);
        });
    }

    // ─────────────────────────────────────────────
    // DESTROY (soft delete)
    // ─────────────────────────────────────────────
    public function backend_geodivisions_destroy(GeoDivision $division): void
    {
        DB::transaction(function () use ($division) {

            if ($division->status === GeoDivisionStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.active_forbidden"));
            }

            if ($division->trashed()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.already_deleted"));
            }

            destroy_function($division);
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_geodivisions_restore(GeoDivision $division): void
    {
        DB::transaction(function () use ($division) {

            if (! $division->trashed()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.restore.not_trashed_forbidden"));
            }

            // Refuse la restauration si le parent est en corbeille
            if ($division->parent_id) {

                $parent = GeoDivision::withTrashed()->find($division->parent_id);

                if ($parent && $parent->trashed()) {
                    throw new \InvalidArgumentException(__("{$this->backendLangPath}.restore.parent_trashed"));
                }
            }

            // restore_function restaure la division ET ses traductions
            restore_function($division);
        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_geodivisions_force_destroy(GeoDivision $division): void
    {
        DB::transaction(function () use ($division) {

            if (! $division->trashed()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.not_trashed_forbidden"));
            }

            if ($division->status === GeoDivisionStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.active_forbidden"));
            }

            force_destroy_function($division);
        });
    }

    // ─────────────────────────────────────────────
    // RESOLVE LEVEL (privé)
    // ─────────────────────────────────────────────
    private function resolveLevel(?int $parentId): int
    {
        if (! $parentId) {
            return 1;
        }

        $parent = GeoDivision::find($parentId);

        return $parent ? $parent->level + 1 : 1;
    }

    // ─────────────────────────────────────────────
    // RECALCULATE DESCENDANT LEVELS (privé)
    // Explication : si une division change de parent, son niveau change, et tous ses descendants doivent être recalculés.
    // ─────────────────────────────────────────────
    private function recalculateDescendantLevels(GeoDivision $division): void
    {
        foreach ($division->children()->get() as $child) {

            $child->update(['level' => $division->level + 1]);

            $this->recalculateDescendantLevels($child);
        }
    }

    // ─────────────────────────────────────────────
    // SYNC TRANSLATIONS (privé)
    // ─────────────────────────────────────────────
    private function syncTranslations(GeoDivision $division, array $translations): void
    {
        // IDs des langues soumises
        $submittedLangIds = collect($translations)->pluck('lang_id')->toArray();

        // Supprime temporairement les traductions absentes du payload
        GeoDivisionTranslation::where('division_id', $division->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->delete();
        // Purge définitive des traductions déjà en corbeille et retirées du payload
        GeoDivisionTranslation::onlyTrashed()->where('division_id', $division->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->forceDelete();

        // NB : le nom n'est PAS unique (un même nom peut exister dans plusieurs pays).
        // L'unicité du slug par langue est garantie automatiquement par le trait HasSlug
        // (suffixe -1, -2, …) et la contrainte DB unique(['slug', 'lang_id']).

        foreach ($translations as $translation) {

            $existingTranslation = GeoDivisionTranslation::withTrashed()
                ->where('division_id', $division->id)
                ->where('lang_id', $translation['lang_id'])
                ->first();

            if ($existingTranslation) {

                // Une traduction en corbeille ne doit pas être modifiée silencieusement
                if ($existingTranslation->trashed()) {
                    throw new \InvalidArgumentException(
                        __("{$this->backendLangPath}.trashedTranslationConflict", ['lang_id' => $translation['lang_id']])
                    );
                }

                $existingTranslation->update([
                    'name' => $translation['name'],
                    'description' => $translation['description'] ?? null,
                ]);

            } else {

                GeoDivisionTranslation::create([
                    'division_id' => $division->id,
                    'lang_id' => $translation['lang_id'],
                    'name' => $translation['name'],
                    'description' => $translation['description'] ?? null,
                ]);
            }
        }
    }
}