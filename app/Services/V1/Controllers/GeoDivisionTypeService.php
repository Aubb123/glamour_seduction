<?php

namespace App\Services\V1\Controllers;

use App\Enums\Geos\DivisionTypes\GeoDivisionTypeStatus;
use App\Models\GeoDivision;
use App\Models\GeoDivisionType;
use App\Models\GeoDivisionTypeTranslation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use function __;

class GeoDivisionTypeService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/geos/division-types/messages/division-types-messages-service.divisionTypes';

    // Chemin des messages génériques du backend helper
    protected string $backendHelperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_geodivisiontypes_store(array $data): GeoDivisionType
    {
        return DB::transaction(function () use ($data) {

            $status = GeoDivisionTypeStatus::tryFrom($data['status'] ?? '') ?? GeoDivisionTypeStatus::Inactive;

            $divisionType = GeoDivisionType::create([
                'code' => $data['code'],
                'level' => $data['level'],
                'sort_order' => $data['sort_order'],
                'status' => $status,
            ]);

            $this->syncTranslations($divisionType, $data['translations']);

            return $divisionType;
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_geodivisiontypes_update(GeoDivisionType $divisionType, array $data): GeoDivisionType
    {
        return DB::transaction(function () use ($divisionType, $data) {

            $status = GeoDivisionTypeStatus::tryFrom($data['status'] ?? '') ?? $divisionType->status;

            // Le code est immuable : jamais mis à jour
            $divisionType->update([
                'level' => $data['level'] ?? $divisionType->level,
                'sort_order' => $data['sort_order'] ?? $divisionType->sort_order,
                'status' => $status,
            ]);

            if (array_key_exists('translations', $data)) {
                $this->syncTranslations($divisionType, $data['translations']);
            }

            return $divisionType;
        });
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_geodivisiontypes_activate(GeoDivisionType $divisionType): void
    {
        DB::transaction(function () use ($divisionType) {

            if ($divisionType->status === GeoDivisionTypeStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.activate.already_active"));
            }

            $divisionType->update(['status' => GeoDivisionTypeStatus::Active]);
        });
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_geodivisiontypes_deactivate(GeoDivisionType $divisionType): void
    {
        DB::transaction(function () use ($divisionType) {

            if ($divisionType->status === GeoDivisionTypeStatus::Inactive) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.deactivate.already_inactive"));
            }

            $divisionType->update(['status' => GeoDivisionTypeStatus::Inactive]);
        });
    }

    // ─────────────────────────────────────────────
    // DESTROY (soft delete)
    // ─────────────────────────────────────────────
    public function backend_geodivisiontypes_destroy(GeoDivisionType $divisionType): void
    {
        DB::transaction(function () use ($divisionType) {

            if ($divisionType->status === GeoDivisionTypeStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.active_forbidden"));
            }

            if ($divisionType->trashed()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.already_deleted"));
            }

            // Verrou métier : un type utilisé par des divisions ne peut pas être supprimé.
            // NB : le modèle GeoDivision n'existe pas encore ; la garde class_exists évite
            // une \Error « Class not found » (non attrapable par catch (\Exception)).
            if (class_exists(GeoDivision::class) && Schema::hasTable('geo_divisions') && $divisionType->divisions()->exists()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.dependencies_forbidden"));
            }

            destroy_function($divisionType);
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_geodivisiontypes_restore(GeoDivisionType $divisionType): void
    {
        DB::transaction(function () use ($divisionType) {

            if (! $divisionType->trashed()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.restore.not_trashed_forbidden"));
            }

            // restore_function restaure le type ET ses traductions
            restore_function($divisionType);
        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_geodivisiontypes_force_destroy(GeoDivisionType $divisionType): void
    {
        DB::transaction(function () use ($divisionType) {

            if (! $divisionType->trashed()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.not_trashed_forbidden"));
            }

            if ($divisionType->status === GeoDivisionTypeStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.active_forbidden"));
            }

            if (class_exists(GeoDivision::class) && Schema::hasTable('geo_divisions') && $divisionType->divisions()->withTrashed()->exists()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.dependencies_forbidden"));
            }

            force_destroy_function($divisionType);
        });
    }

    // ─────────────────────────────────────────────
    // SYNC TRANSLATIONS (privé)
    // ─────────────────────────────────────────────
    private function syncTranslations(GeoDivisionType $divisionType, array $translations): void
    {
        // IDs des langues soumises
        $submittedLangIds = collect($translations)->pluck('lang_id')->toArray();

        // Supprime temporairement les traductions absentes du payload
        GeoDivisionTypeTranslation::where('division_type_id', $divisionType->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->delete();
        // Purge définitive des traductions déjà en corbeille et retirées du payload
        GeoDivisionTypeTranslation::onlyTrashed()->where('division_type_id', $divisionType->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->forceDelete();

        // Unicité du nom et du slug par langue (corbeille incluse).
        assertTranslationUniqueness(
            $divisionType,
            $translations,
            __('v1/backend/others/helpers/backend-helper-messages.entityLabels.divisionType'),
            $this->backendHelperLangPath,
            'name',
            'slug'
        );

        foreach ($translations as $translation) {

            $existingTranslation = GeoDivisionTypeTranslation::withTrashed()
                ->where('division_type_id', $divisionType->id)
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

                GeoDivisionTypeTranslation::create([
                    'division_type_id' => $divisionType->id,
                    'lang_id' => $translation['lang_id'],
                    'name' => $translation['name'],
                    'description' => $translation['description'] ?? null,
                ]);
            }
        }
    }
}
