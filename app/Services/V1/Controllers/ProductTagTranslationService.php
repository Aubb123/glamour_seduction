<?php

namespace App\Services\V1\Controllers;

use App\Models\ProductTagTranslation;
use Illuminate\Support\Facades\DB;

use function __;

class ProductTagTranslationService
{
    // Chemin de base pour les messages
    protected string $langPath = 'v1/backend/views/pages/products/tags/messages/tags-messages-service.productTagsTranslations';

    // Chemin des messages génériques du backend helper (factorisés pour toutes les entités traduisibles)
    protected string $helperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function destroy(ProductTagTranslation $translation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($translation) {

            // Récupération du tag associé à la traduction (même s'il est soft-deleted)
            $tag = $translation->tag()->withTrashed()->first();

            // Empêche la suppression de la traduction si c'est la dernière traduction restante du tag (même si le tag est soft-deleted)
            if ($tag) {

                // Compte le nombre de traductions associées au tag, en excluant les traductions soft-deleted
                $translationsCount = $tag->translations()->count();

                if ($translationsCount <= 1) {
                    throw new \InvalidArgumentException(__("{$this->langPath}.destroy.last_translation_forbidden"));
                }
            }

            // Suppression simple (deleted_at)
            if ($translation->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.already_deleted"));
            }
            destroy_function($translation); // ← suppression de la traduction et de ses dépendances associées

        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function restore(ProductTagTranslation $translation): void
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
                    __('v1/backend/others/helpers/backend-helper-messages.entityLabels.productTag'),
                    $this->helperLangPath,
                    'name',
                    'slug'
                );

                restore_function($translation); // ← restauration de la traduction du tag sélectionné
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.restore.not_trashed_forbidden"));
            }

        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function forceDestroy(ProductTagTranslation $translation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($translation) {

            // Récupération du tag associé à la traduction (même s'il est soft-deleted)
            $tag = $translation->tag()->withTrashed()->first();

            // Si le tag existe, on vérifie si c'est la dernière traduction restante avant de permettre la suppression définitive
            if ($tag) {

                // Compte le nombre de traductions associées au tag, y compris les traductions soft-deleted
                $translationsCount = $tag->translations()->withTrashed()->count();

                // Empêche la suppression définitive de la traduction si c'est la dernière traduction restante du tag (même si le tag est soft-deleted)
                if ($translationsCount <= 1) {
                    throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.last_translation_forbidden"));
                }
            }

            // Suppression définitive (forceDelete)
            if ($translation->trashed()) {
                force_destroy_function($translation); // ← suppression définitive de la traduction du tag sélectionné
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.not_trashed_forbidden"));
            }

        });
    }
}
