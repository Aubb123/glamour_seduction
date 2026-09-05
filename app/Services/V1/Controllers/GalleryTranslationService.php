<?php

namespace App\Services\V1\Controllers;

use App\Models\GalleryTranslation;
use Illuminate\Support\Facades\DB;

use function __;

class GalleryTranslationService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/galleries/messages/galleries-messages-service.galleries.translations';

    // Chemin des messages génériques du backend helper (factorisés pour toutes les entités traduisibles)
    protected string $helperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function destroy(GalleryTranslation $galleryTranslation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($galleryTranslation) {

            // Récupération de la galerie associée à la traduction (même si la galerie est soft-deleted)
            $gallery = $galleryTranslation->gallery()->withTrashed()->first();

            // Empêche la suppression de la traduction si c'est la dernière traduction restante de la galerie
            if ($gallery) {

                // Compte le nombre de traductions associées à la galerie, en excluant les traductions soft-deleted
                $translationsCount = $gallery->translations()->count();

                if ($translationsCount <= 1) {
                    throw new \InvalidArgumentException(__("{$this->langPath}.destroy.last_translation_forbidden", ['name' => $galleryTranslation->title]));
                }
            }

            // Suppression simple (deleted_at)
            if ($galleryTranslation->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.already_deleted"));
            }
            destroy_function($galleryTranslation);
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function restore(GalleryTranslation $galleryTranslation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($galleryTranslation) {

            // Restauration simple (restore)
            if ($galleryTranslation->trashed()) {

                // Validation générique factorisée (BackendHelper) : refuse de restaurer
                // une traduction dont le titre/slug entre en conflit avec une autre traduction
                // (active ou en corbeille) dans la même langue.
                assertTranslationRestore(
                    $galleryTranslation,
                    __('v1/backend/others/helpers/backend-helper-messages.entityLabels.gallery'),
                    $this->helperLangPath,
                    'title',
                    'slug'
                );

                restore_function($galleryTranslation);
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.restore.not_trashed_forbidden"));
            }
        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function forceDestroy(GalleryTranslation $galleryTranslation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($galleryTranslation) {

            // Empêche la suppression définitive de la traduction si c'est la dernière traduction restante de la galerie
            $gallery = $galleryTranslation->gallery()->withTrashed()->first();

            if ($gallery) {

                // Compte le nombre de traductions associées à la galerie, y compris les traductions soft-deleted
                $translationsCount = $gallery->translations()->withTrashed()->count();

                if ($translationsCount <= 1) {
                    throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.last_translation_forbidden", ['name' => $galleryTranslation->title]));
                }
            }

            // Suppression définitive (forceDelete)
            if ($galleryTranslation->trashed()) {
                force_destroy_function($galleryTranslation);
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.not_trashed_forbidden"));
            }
        });
    }
}
