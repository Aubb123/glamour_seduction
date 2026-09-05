<?php

namespace App\Services\V1\Controllers;

use App\Models\BlogPostTranslation;
use Illuminate\Support\Facades\DB;

use function __;

class BlogPostTranslationService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/blogs/posts/messages/posts-messages-service.posts.translations';

    // Chemin des messages génériques du backend helper (factorisés pour toutes les entités traduisibles)
    protected string $helperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function destroy(BlogPostTranslation $blogPostTranslation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($blogPostTranslation) {

            // Récupération du post associé à la traduction (même si le post est soft-deleted)
            $post = $blogPostTranslation->post()->withTrashed()->first();

            // Empêche la suppression de la traduction si c'est la dernière traduction restante du post (même si le post est soft-deleted)
            if ($post) {

                // Compte le nombre de traductions associées au post, y compris les traductions soft-deleted
                // $translationsCount = $post->translations()->withTrashed()->count();

                // Compte le nombre de traductions associées au post, en excluant les traductions soft-deleted
                $translationsCount = $post->translations()->count();

                if ($translationsCount <= 1) {
                    throw new \InvalidArgumentException(__("{$this->langPath}.destroy.last_translation_forbidden"));
                }
            }

            // Suppression simple (deleted_at)
            if ($blogPostTranslation->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.already_deleted"));
            }
            destroy_function($blogPostTranslation); // ← suppression de la traduction et de ses dépendances associées + gestion de suppression des fichiers associés si besoin

        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function restore(BlogPostTranslation $blogPostTranslation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($blogPostTranslation) {

            // Restauration simple (restore)
            if ($blogPostTranslation->trashed()) {

                // Validation générique factorisée (BackendHelper) : refuse de restaurer
                // une traduction dont le titre/slug entre en conflit avec une autre traduction
                // (active ou en corbeille) dans la même langue.
                assertTranslationRestore(
                    $blogPostTranslation,
                    __('v1/backend/others/helpers/backend-helper-messages.entityLabels.post'),
                    $this->helperLangPath,
                    'title',
                    'slug'
                );

                restore_function($blogPostTranslation); // <- restauration de la traduction du post sélectionné
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.restore.not_trashed_forbidden"));
            }

        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function forceDestroy(BlogPostTranslation $blogPostTranslation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($blogPostTranslation) {

            // Empêche la suppression définitive de la traduction si la traduction est la dernière traduction restante du post (même si le post est soft-deleted)
            $post = $blogPostTranslation->post()->withTrashed()->first();

            if ($post) {

                // Compte le nombre de traductions associées au post, y compris les traductions soft-deleted
                $translationsCount = $post->translations()->withTrashed()->count();

                if ($translationsCount <= 1) {
                    throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.last_translation_forbidden"));
                }
            }

            // Suppression définitive (forceDelete)
            if ($blogPostTranslation->trashed()) {
                force_destroy_function($blogPostTranslation); // <- suppression définitive de la traduction du post sélectionné
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.not_trashed_forbidden"));
            }

        });
    }
}
