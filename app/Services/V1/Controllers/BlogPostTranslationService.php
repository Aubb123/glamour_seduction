<?php

namespace App\Services\V1\Controllers;

use App\Models\BlogPost;
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
    public function backend_blog_post_translations_destroy(BlogPostTranslation $blogPostTranslation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($blogPostTranslation) {

            // 1. Verrou sur le post (même soft-deleted)
            $post = BlogPost::withTrashed()
                ->lockForUpdate()
                ->find($blogPostTranslation->post_id);

            if (! $post) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.post_not_found"));
            }

            // 2. Relecture verrouillée de la traduction
            $blogPostTranslation = BlogPostTranslation::withTrashed()
                ->lockForUpdate()
                ->find($blogPostTranslation->id);

            if (! $blogPostTranslation) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.translation_not_found"));
            }

            // 3. Déjà en corbeille
            if ($blogPostTranslation->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.already_deleted"));
            }

            // 4. Dernière traduction active
            if ($post->translations()->count() <= 1) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.last_translation_forbidden"));
            }

            // 5. Suppression
            destroy_function($blogPostTranslation); // ← suppression de la traduction et de ses dépendances associées + gestion de suppression des fichiers associés si besoin
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_blog_post_translations_restore(BlogPostTranslation $blogPostTranslation): void
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

                restore_function($blogPostTranslation); // ← restauration de la traduction du post sélectionné
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.restore.not_trashed_forbidden"));
            }

        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_blog_post_translations_force_destroy(BlogPostTranslation $blogPostTranslation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($blogPostTranslation) {

            // 1. Verrou sur le post (même soft-deleted), toujours en premier
            //    pour garder le même ordre de verrouillage que destroy() et syncTranslations()
            $post = BlogPost::withTrashed()
                ->lockForUpdate()
                ->find($blogPostTranslation->post_id);

            if (! $post) {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.post_not_found"));
            }

            // 2. Relecture verrouillée de la traduction (le binding peut être périmé)
            $blogPostTranslation = BlogPostTranslation::withTrashed()
                ->lockForUpdate()
                ->find($blogPostTranslation->id);

            if (! $blogPostTranslation) {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.translation_not_found"));
            }

            // 3. Seule une traduction en corbeille peut être supprimée définitivement
            if (! $blogPostTranslation->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.not_trashed_forbidden"));
            }

            // 4. Interdit de supprimer la dernière traduction du post (corbeille incluse)
            if ($post->translations()->withTrashed()->count() <= 1) {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.last_translation_forbidden"));
            }

            // 5. Suppression définitive de la traduction et de ses dépendances
            force_destroy_function($blogPostTranslation); // ← suppression définitive de la traduction du post sélectionné
        });
    }
}
