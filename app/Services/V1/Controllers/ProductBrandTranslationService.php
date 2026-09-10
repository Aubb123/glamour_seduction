<?php

namespace App\Services\V1\Controllers;

use App\Models\ProductBrandTranslation;
use Illuminate\Support\Facades\DB;

use function __;

class ProductBrandTranslationService
{
    // Chemin de base pour les messages
    protected string $langPath = 'v1/backend/views/pages/products/brands/messages/brands-messages-service.productBrandsTranslations';

    // Chemin des messages génériques du backend helper (factorisés pour toutes les entités traduisibles)
    protected string $helperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function destroy(ProductBrandTranslation $translation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($translation) {

            // Récupération de la marque associée à la traduction (même si elle est soft-deleted)
            $brand = $translation->brand()->withTrashed()->first();

            // Empêche la suppression de la traduction si c'est la dernière traduction restante de la marque (même si la marque est soft-deleted)
            if ($brand) {

                // Compte le nombre de traductions associées à la marque, en excluant les traductions soft-deleted
                $translationsCount = $brand->translations()->count();

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
    public function restore(ProductBrandTranslation $translation): void
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
                    __('v1/backend/others/helpers/backend-helper-messages.entityLabels.productBrand'),
                    $this->helperLangPath,
                    'name',
                    'slug'
                );

                restore_function($translation); // ← restauration de la traduction de la marque sélectionnée
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.restore.not_trashed_forbidden"));
            }

        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function forceDestroy(ProductBrandTranslation $translation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($translation) {

            // Récupération de la marque associée à la traduction (même si elle est soft-deleted)
            $brand = $translation->brand()->withTrashed()->first();

            // Si la marque existe, on vérifie si c'est la dernière traduction restante avant de permettre la suppression définitive
            if ($brand) {

                // Compte le nombre de traductions associées à la marque, y compris les traductions soft-deleted
                $translationsCount = $brand->translations()->withTrashed()->count();

                // Empêche la suppression définitive de la traduction si c'est la dernière traduction restante de la marque (même si la marque est soft-deleted)
                if ($translationsCount <= 1) {
                    throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.last_translation_forbidden"));
                }
            }

            // Suppression définitive (forceDelete)
            if ($translation->trashed()) {
                force_destroy_function($translation); // ← suppression définitive de la traduction de la marque sélectionnée
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.not_trashed_forbidden"));
            }

        });
    }
}
