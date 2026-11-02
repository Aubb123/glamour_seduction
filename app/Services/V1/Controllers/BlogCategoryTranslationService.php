<?php

namespace App\Services\V1\Controllers;

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
    public function destroy(BlogCategoryTranslation $translation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($translation) {

            // Récupération de la catégorie associée à la traduction (même si elle est soft-deleted)
            $category = $translation->category()->withTrashed()->first();

            // Empêche la suppression de la traduction si c'est la dernière traduction restante de la catégorie (même si la catégorie est soft-deleted)
            if ($category) {

                // Compte le nombre de traductions associées à la catégorie, en excluant les traductions soft-deleted
                $translationsCount = $category->translations()->count();

                // Si c'est la dernière traduction restante, on empêche la suppression pour éviter de laisser la catégorie sans aucune traduction
                if ($translationsCount <= 1) {
                    throw new \InvalidArgumentException(__("{$this->langPath}.destroy.last_translation_forbidden"));
                }
            }

            // Suppression simple (deleted_at)
            if ($translation->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.already_deleted"));
            }
            
            // ← suppression de la traduction et de ses dépendances associées
            destroy_function($translation); 

        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function restore(BlogCategoryTranslation $translation): void
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
    public function forceDestroy(BlogCategoryTranslation $translation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($translation) {

            // Récupération de la catégorie associée à la traduction (même si elle est soft-deleted)
            $category = $translation->category()->withTrashed()->first();

            // Si la catégorie existe, on vérifie si c'est la dernière traduction restante avant de permettre la suppression définitive
            if ($category) {

                // Compte le nombre de traductions associées à la catégorie, y compris les traductions soft-deleted
                $translationsCount = $category->translations()->withTrashed()->count();

                // Empêche la suppression définitive de la traduction si c'est la dernière traduction restante de la catégorie (même si la catégorie est soft-deleted)
                if ($translationsCount <= 1) {
                    throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.last_translation_forbidden"));
                }
            }

            // Suppression définitive (forceDelete)
            if ($translation->trashed()) {
                
                // ← suppression définitive de la traduction de la catégorie sélectionnée
                force_destroy_function($translation); 

            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.not_trashed_forbidden"));
            }

        });
    }
}
