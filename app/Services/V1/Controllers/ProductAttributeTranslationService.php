<?php

namespace App\Services\V1\Controllers;

use App\Models\ProductAttributeTranslation;
use Illuminate\Support\Facades\DB;

use function __;

class ProductAttributeTranslationService
{
    // Chemin de base pour les messages de traduction (centralisé)
    protected string $langPath = 'v1/backend/views/pages/products/attributes/messages/attributes-messages-service.productAttributesTranslations';

    // Chemin des messages génériques du backend helper (factorisés pour toutes les entités traduisibles)
    protected string $helperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    // ─────────────────────────────────────────────
    // DESTROY — Soft delete d'une traduction d'attribut
    // ─────────────────────────────────────────────
    public function destroy(ProductAttributeTranslation $translation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($translation) {

            // Récupère l'attribut parent associé à cette traduction
            $attribute = $translation->attribute()->withTrashed()->first();

            // Règle métier : on ne peut pas supprimer la dernière traduction restante
            // d'un attribut, car chaque attribut doit conserver au moins une traduction.
            if ($attribute) {

                // Compte le nombre de traductions actives (non soft-deleted) de l'attribut
                $translationsCount = $attribute->translations()->count();

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
    public function restore(ProductAttributeTranslation $translation): void
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
                    __('v1/backend/others/helpers/backend-helper-messages.entityLabels.productAttribute'),
                    $this->helperLangPath,
                    'name',
                    'slug'
                );

                restore_function($translation); // ← restauration de la traduction du attribut sélectionné
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.restore.not_trashed_forbidden"));
            }

        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function forceDestroy(ProductAttributeTranslation $translation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($translation) {

            // Récupération du attribut associé à la traduction (même s'il est soft-deleted)
            $attribut = $translation->attribute()->withTrashed()->first();

            // Si le attribut existe, on vérifie si c'est la dernière traduction restante avant de permettre la suppression définitive
            if ($attribut) {

                // Compte le nombre de traductions associées au attribut, y compris les traductions soft-deleted
                $translationsCount = $attribut->translations()->withTrashed()->count();

                // Empêche la suppression définitive de la traduction si c'est la dernière traduction restante du attribut (même si le attribut est soft-deleted)
                if ($translationsCount <= 1) {
                    throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.last_translation_forbidden"));
                }
            }

            // Suppression définitive (forceDelete)
            if ($translation->trashed()) {
                force_destroy_function($translation); // ← suppression définitive de la traduction du attribut sélectionné
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.not_trashed_forbidden"));
            }

        });
    }
}
