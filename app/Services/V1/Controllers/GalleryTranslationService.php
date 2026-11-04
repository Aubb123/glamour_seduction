<?php

namespace App\Services\V1\Controllers;

use App\Models\Gallery;
use App\Models\GalleryTranslation;
use Illuminate\Support\Facades\DB;

use function __;

class GalleryTranslationService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/galleries/messages/galleries-messages-service.galleries.translations';

    // Chemin des messages génériques du backend helper (factorisés pour toutes les entités traduisibles)
    protected string $backendHelperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_galleries_translations_destroy(GalleryTranslation $galleryTranslation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($galleryTranslation) {

            // 1. Verrou sur la galerie (même soft-deleted)
            $gallery = Gallery::withTrashed()
                ->lockForUpdate()
                ->find($galleryTranslation->gallery_id);

            if (! $gallery) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.gallery_not_found"));
            }

            // 2. Relecture verrouillée de la traduction
            $galleryTranslation = GalleryTranslation::withTrashed()
                ->lockForUpdate()
                ->find($galleryTranslation->id);

            if (! $galleryTranslation) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.translation_not_found"));
            }

            // 3. Déjà en corbeille
            if ($galleryTranslation->trashed()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.already_deleted"));
            }

            // 4. Dernière traduction active
            if ($gallery->translations()->count() <= 1) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.last_translation_forbidden"));
            }

            // 5. Suppression
            destroy_function($galleryTranslation); // ← suppression de la traduction et de ses dépendances associées
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_galleries_translations_restore(GalleryTranslation $galleryTranslation): void
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
                    $this->backendHelperLangPath,
                    'title',
                    'slug'
                );

                restore_function($galleryTranslation);
            } else {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.restore.not_trashed_forbidden"));
            }

        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_galleries_translations_force_destroy(GalleryTranslation $galleryTranslation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($galleryTranslation) {

            // 1. Verrou sur la galerie (même soft-deleted), toujours en premier
            //    pour garder le même ordre de verrouillage que destroy() et syncTranslations()
            $gallery = Gallery::withTrashed()
                ->lockForUpdate()
                ->find($galleryTranslation->gallery_id);

            if (! $gallery) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.gallery_not_found"));
            }

            // 2. Relecture verrouillée de la traduction (le binding peut être périmé)
            $galleryTranslation = GalleryTranslation::withTrashed()
                ->lockForUpdate()
                ->find($galleryTranslation->id);

            if (! $galleryTranslation) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.translation_not_found"));
            }

            // 3. Seule une traduction en corbeille peut être supprimée définitivement
            if (! $galleryTranslation->trashed()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.not_trashed_forbidden"));
            }

            // 4. Interdit de supprimer la dernière traduction de la galerie (corbeille incluse)
            if ($gallery->translations()->withTrashed()->count() <= 1) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.last_translation_forbidden"));
            }

            // 5. Suppression définitive de la traduction et de ses dépendances
            force_destroy_function($galleryTranslation);
        });
    }
}
