<?php

namespace App\Services\V1\Controllers;

use App\Models\BlogTagTranslation;
use Illuminate\Support\Facades\DB;

use function __;

class BlogTagTranslationService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/blogs/tags/messages/tags-messages-service.tags.translations';

    // Chemin des messages génériques du backend helper (factorisés pour toutes les entités traduisibles)
    protected string $helperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function destroy(BlogTagTranslation $translation): void
    {
        DB::transaction(function () use ($translation) {

            // Récupération du tag associé à la traduction (même si soft-deleted)
            $tag = $translation->tag()->withTrashed()->first();

            // Empêche la suppression de la traduction si c'est la dernière traduction restante du tag
            if ($tag) {
                $translationsCount = $tag->translations()->count();

                if ($translationsCount <= 1) {
                    throw new \InvalidArgumentException(__("{$this->langPath}.destroy.last_translation_forbidden"));
                }
            }

            // Suppression simple (soft delete)
            if ($translation->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.already_deleted"));
            }
            destroy_function($translation);
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function restore(BlogTagTranslation $translation): void
    {
        DB::transaction(function () use ($translation) {

            // Restauration simple (restore)
            if ($translation->trashed()) {

                // Validation générique factorisée (BackendHelper) : refuse de restaurer
                // une traduction dont le nom/slug entre en conflit avec une autre traduction
                // (active ou en corbeille) dans la même langue.
                assertTranslationRestore(
                    $translation,
                    __('v1/backend/others/helpers/backend-helper-messages.entityLabels.tag'),
                    $this->helperLangPath,
                    'name',
                    'slug'
                );

                restore_function($translation);
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.restore.not_trashed_forbidden"));
            }
        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function forceDestroy(BlogTagTranslation $translation): void
    {
        DB::transaction(function () use ($translation) {

            // Empêche la suppression définitive si c'est la dernière traduction restante du tag
            $tag = $translation->tag()->withTrashed()->first();

            if ($tag) {
                $translationsCount = $tag->translations()->withTrashed()->count();

                if ($translationsCount <= 1) {
                    throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.last_translation_forbidden"));
                }
            }

            // Suppression définitive (forceDelete)
            if ($translation->trashed()) {
                force_destroy_function($translation);
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.not_trashed_forbidden"));
            }
        });
    }
}
