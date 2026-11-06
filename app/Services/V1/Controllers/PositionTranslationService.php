<?php

namespace App\Services\V1\Controllers;

use App\Models\Position;
use App\Models\PositionTranslation;
use Illuminate\Support\Facades\DB;

use function __;

class PositionTranslationService
{
    // Chemin de base pour les messages
    protected string $backendLangPath = 'v1/backend/views/pages/shops/positions/messages/positions-messages-service.positionsTranslations';

    // Chemin des messages génériques du backend helper (factorisés pour toutes les entités traduisibles)
    protected string $backendHelperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_position_translations_destroy(PositionTranslation $translation): void
    {
        DB::transaction(function () use ($translation) {

            $position = Position::withTrashed()->lockForUpdate()->find($translation->position_id);

            if (! $position) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.position_not_found"));
            }

            $translation = PositionTranslation::withTrashed()->lockForUpdate()->find($translation->id);

            if (! $translation) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.translation_not_found"));
            }

            if ($translation->trashed()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.already_deleted"));
            }

            if ($position->translations()->count() <= 1) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.last_translation_forbidden"));
            }

            destroy_function($translation);
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_position_translations_restore(PositionTranslation $translation): void
    {
        DB::transaction(function () use ($translation) {

            if ($translation->trashed()) {

                assertTranslationRestore(
                    $translation,
                    __('v1/backend/others/helpers/backend-helper-messages.entityLabels.position'),
                    $this->backendHelperLangPath,
                    'name',
                    'slug'
                );

                restore_function($translation);

            } else {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.restore.not_trashed_forbidden"));
            }
        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_position_translations_force_destroy(PositionTranslation $translation): void
    {
        DB::transaction(function () use ($translation) {

            $position = Position::withTrashed()->lockForUpdate()->find($translation->position_id);

            if (! $position) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.position_not_found"));
            }

            $translation = PositionTranslation::withTrashed()->lockForUpdate()->find($translation->id);

            if (! $translation) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.translation_not_found"));
            }

            if (! $translation->trashed()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.not_trashed_forbidden"));
            }

            if ($position->translations()->withTrashed()->count() <= 1) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.last_translation_forbidden"));
            }

            force_destroy_function($translation);
        });
    }
}
