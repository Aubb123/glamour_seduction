<?php

namespace App\Services\V1\Controllers;

use App\Models\BlogCategory;
use App\Models\BlogCategoryTranslation;
use Illuminate\Support\Facades\DB;

use function __;

class BlogCategoryTranslationService
{
    // Chemin de base pour les messages
    protected string $langPath = 'v1/backend/views/pages/blogs/categories/messages/categories-messages-service.translations';

    // Chemin des messages génériques du backend helper (factorisés pour toutes les entités traduisibles)
    protected string $helperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_blog_category_translations_destroy(BlogCategoryTranslation $translation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($translation) {

            // 1. Verrou sur la catégorie (même soft-deleted)
            $category = BlogCategory::withTrashed()
                ->lockForUpdate()
                ->find($translation->category_id);

            if (! $category) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.category_not_found"));
            }

            // 2. Relecture verrouillée de la traduction
            $translation = BlogCategoryTranslation::withTrashed()
                ->lockForUpdate()
                ->find($translation->id);

            if (! $translation) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.translation_not_found"));
            }

            // 3. Déjà en corbeille
            if ($translation->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.already_deleted"));
            }

            // 4. Dernière traduction active
            if ($category->translations()->count() <= 1) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.last_translation_forbidden"));
            }

            // 5. Suppression
            destroy_function($translation); // ← suppression de la traduction et de ses dépendances associées
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_blog_category_translations_restore(BlogCategoryTranslation $translation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($translation) {

            // Restauration simple (restore)
            if ($translation->trashed()) {

                // Validation générique factorisée (BackendHelper) : refuse de restaurer
                // une traduction dont le nom/slug entre en conflit avec une autre traduction
                // (active ou en corbeille) dans la même langue.
                assertTranslationRestore(
                    $translation,
                    __('v1/backend/others/helpers/backend-helper-messages.entityLabels.category'),
                    $this->helperLangPath,
                    'name',
                    'slug'
                );

                // ← restauration de la traduction de la catégorie sélectionnée
                restore_function($translation);

            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.restore.not_trashed_forbidden"));
            }

        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_blog_category_translations_force_destroy(BlogCategoryTranslation $translation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($translation) {

            // 1. Verrou sur la catégorie (même soft-deleted), toujours en premier
            //    pour garder le même ordre de verrouillage que destroy() et syncTranslations()
            $category = BlogCategory::withTrashed()
                ->lockForUpdate()
                ->find($translation->category_id);

            if (! $category) {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.category_not_found"));
            }

            // 2. Relecture verrouillée de la traduction (le binding peut être périmé)
            $translation = BlogCategoryTranslation::withTrashed()
                ->lockForUpdate()
                ->find($translation->id);

            if (! $translation) {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.translation_not_found"));
            }

            // 3. Seule une traduction en corbeille peut être supprimée définitivement
            if (! $translation->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.not_trashed_forbidden"));
            }

            // 4. Interdit de supprimer la dernière traduction de la catégorie (corbeille incluse)
            if ($category->translations()->withTrashed()->count() <= 1) {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.last_translation_forbidden"));
            }

            // 5. Suppression définitive de la traduction et de ses dépendances
            // ← suppression définitive de la traduction de la catégorie sélectionnée
            force_destroy_function($translation);

        });
    }
}
