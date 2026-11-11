<?php

namespace App\Services\V1\Controllers;

use App\Enums\Shops\Positions\PositionContext;
use App\Enums\Shops\Positions\PositionStatus;
use App\Models\Position;
use App\Models\PositionTranslation;
use Illuminate\Support\Facades\DB;

use function __;

class PositionService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/shops/positions/messages/positions-messages-service.positions';

    // Chemin des messages génériques du backend helper (factorisés pour toutes les entités traduisibles)
    protected string $backendHelperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_positions_store(array $data): Position
    {
        return DB::transaction(function () use ($data) {

            // Création du poste (sauf les traductions)
            $position = Position::create([
                'code' => $data['code'],
                'context' => PositionContext::tryFrom($data['context'] ?? '') ?? PositionContext::Both,
                'status' => ($data['status'] ?? null) === PositionStatus::Active->value ? PositionStatus::Active : PositionStatus::Inactive,
                'sort_order' => $data['sort_order'] ?? 0,
            ]);

            // Synchronisation des traductions (création)
            $this->syncTranslations($position, $data['translations'] ?? []);

            return $position;
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_positions_update(Position $position, array $data): Position
    {
        return DB::transaction(function () use ($position, $data) {

            $updateData = [];

            if (isset($data['code'])) {
                $updateData['code'] = $data['code'];
            }

            if (isset($data['context'])) {
                $updateData['context'] = PositionContext::tryFrom($data['context']) ?? PositionContext::Both;
            }

            if (isset($data['status'])) {
                $updateData['status'] = ($data['status'] ?? null) === PositionStatus::Active->value ? PositionStatus::Active : PositionStatus::Inactive;
            }

            if (isset($data['sort_order'])) {
                $updateData['sort_order'] = $data['sort_order'];
            }

            if (! empty($updateData)) {
                $position->update($updateData);
            }

            // Synchronisation des traductions (mise à jour)
            if (isset($data['translations'])) {
                $this->syncTranslations($position, $data['translations']);
            }

            return $position;
        });
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_positions_activate(Position $position): void
    {
        DB::transaction(function () use ($position) {

            if ($position->status === PositionStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.activate.already_active"));
            }

            $position->update(['status' => PositionStatus::Active]);
        });
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_positions_deactivate(Position $position): void
    {
        DB::transaction(function () use ($position) {

            if ($position->status === PositionStatus::Inactive) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.deactivate.already_inactive"));
            }

            $position->update(['status' => PositionStatus::Inactive]);
        });
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_positions_destroy(Position $position): void
    {
        DB::transaction(function () use ($position) {

            // Empêche la suppression d'un poste actif (règle métier : le désactiver d'abord)
            if ($position->status === PositionStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.active_forbidden"));
            }

            // NB : destroy refusé si le poste est encore affecté à du personnel
            // (géré par BackendHelper::destroy_function via dependentRelations).

            if ($position->trashed()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.already_deleted"));
            }

            destroy_function($position);
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_positions_restore(Position $position): void
    {
        DB::transaction(function () use ($position) {

            if ($position->trashed()) {
                restore_function($position);
            } else {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.restore.not_trashed_forbidden"));
            }
        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_positions_force_destroy(Position $position): void
    {
        DB::transaction(function () use ($position) {

            if ($position->trashed()) {
                // Une entité active ne peut pas être supprimée définitivement
                if ($position->status === PositionStatus::Active) {
                    throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.active_forbidden"));
                }
                force_destroy_function($position);
            } else {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.not_trashed_forbidden"));
            }
        });
    }

    // ─────────────────────────────────────────────
    // SYNC TRANSLATIONS (privé)
    // ─────────────────────────────────────────────
    private function syncTranslations(Position $position, array $translations): void
    {
        // Récupère les IDs des langues soumises
        $submittedLangIds = collect($translations)->pluck('lang_id')->toArray();

        // Supprime temporairement les traductions qui ne sont pas présentes dans les données soumises
        PositionTranslation::where('position_id', $position->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->delete();

        // Suppression définitive des traductions en corbeille retirées précédemment
        PositionTranslation::onlyTrashed()->where('position_id', $position->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->forceDelete();

        // Validation générique factorisée (BackendHelper) : unicité nom/slug par langue
        assertTranslationUniqueness(
            $position,
            $translations,
            __('v1/backend/others/helpers/backend-helper-messages.entityLabels.position'),
            $this->backendHelperLangPath,
            'name',
            'slug'
        );

        foreach ($translations as $translation) {

            $existingTranslation = PositionTranslation::withTrashed()
                ->where('position_id', $position->id)
                ->where('lang_id', $translation['lang_id'])
                ->first();

            if ($existingTranslation) {

                // Une traduction en corbeille ne doit pas être modifiée silencieusement
                if ($existingTranslation->trashed()) {
                    throw new \InvalidArgumentException(
                        __("{$this->backendLangPath}.trashedTranslationConflict", [
                            'lang_id' => $translation['lang_id'],
                        ])
                    );
                }

                $existingTranslation->update([
                    'name' => $translation['name'],
                    'description' => $translation['description'] ?? null,
                ]);

            } else {

                PositionTranslation::create([
                    'position_id' => $position->id,
                    'lang_id' => $translation['lang_id'],
                    'name' => $translation['name'],
                    'description' => $translation['description'] ?? null,
                ]);
            }
        }
    }
}
