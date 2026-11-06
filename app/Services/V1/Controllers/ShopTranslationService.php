<?php

namespace App\Services\V1\Controllers;

use App\Models\Shop;
use App\Models\ShopTranslation;
use Illuminate\Support\Facades\DB;

use function __;

class ShopTranslationService
{
    // Chemin de base pour les messages
    protected string $backendLangPath = 'v1/backend/views/pages/shops/shops/messages/shops-messages-service.shopsTranslations';

    // Chemin des messages génériques du backend helper (factorisés pour toutes les entités traduisibles)
    protected string $backendHelperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_shop_translations_destroy(ShopTranslation $translation): void
    {
        DB::transaction(function () use ($translation) {

            // 1. Verrou sur la boutique (même soft-deleted)
            $shop = Shop::withTrashed()
                ->lockForUpdate()
                ->find($translation->shop_id);

            if (! $shop) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.shop_not_found"));
            }

            // 2. Relecture verrouillée de la traduction
            $translation = ShopTranslation::withTrashed()
                ->lockForUpdate()
                ->find($translation->id);

            if (! $translation) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.translation_not_found"));
            }

            // 3. Déjà en corbeille
            if ($translation->trashed()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.already_deleted"));
            }

            // 4. Dernière traduction active
            if ($shop->translations()->count() <= 1) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.last_translation_forbidden"));
            }

            // 5. Suppression
            destroy_function($translation);
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_shop_translations_restore(ShopTranslation $translation): void
    {
        DB::transaction(function () use ($translation) {

            if ($translation->trashed()) {

                // Validation générique factorisée (BackendHelper) : refuse de restaurer
                // une traduction dont le nom/slug entre en conflit avec une autre traduction
                // (active ou en corbeille) dans la même langue.
                assertTranslationRestore(
                    $translation,
                    __('v1/backend/others/helpers/backend-helper-messages.entityLabels.shop'),
                    $this->backendHelperLangPath,
                    'name',
                    'slug'
                );

                restore_function($translation);

            } else {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.restore.not_trashed_forbidden"));
            }
        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_shop_translations_force_destroy(ShopTranslation $translation): void
    {
        DB::transaction(function () use ($translation) {

            // 1. Verrou sur la boutique (même soft-deleted), toujours en premier
            //    pour garder le même ordre de verrouillage que destroy() et syncTranslations()
            $shop = Shop::withTrashed()
                ->lockForUpdate()
                ->find($translation->shop_id);

            if (! $shop) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.shop_not_found"));
            }

            // 2. Relecture verrouillée de la traduction
            $translation = ShopTranslation::withTrashed()
                ->lockForUpdate()
                ->find($translation->id);

            if (! $translation) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.translation_not_found"));
            }

            // 3. Seule une traduction en corbeille peut être supprimée définitivement
            if (! $translation->trashed()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.not_trashed_forbidden"));
            }

            // 4. Interdit de supprimer la dernière traduction de la boutique (corbeille incluse)
            if ($shop->translations()->withTrashed()->count() <= 1) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.last_translation_forbidden"));
            }

            // 5. Suppression définitive de la traduction et de ses dépendances
            force_destroy_function($translation);
        });
    }
}
