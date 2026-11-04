<?php

namespace App\Services\V1\Controllers;

use App\Models\Faq;
use App\Models\FaqTranslation;
use Illuminate\Support\Facades\DB;

use function __;

class FaqTranslationService
{
    // Chemin vers le fichier de traduction pour les messages liés aux FAQs
    protected string $langPath = 'v1/backend/views/pages/faqs/messages/faqs-messages-service.faqs.translations';

    // Chemin des messages génériques du backend helper (factorisés pour toutes les entités traduisibles)
    protected string $helperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_faqs_translations_destroy(FaqTranslation $faqTranslation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($faqTranslation) {

            // 1. Verrou sur la FAQ (même soft-deleted)
            $faq = Faq::withTrashed()
                ->lockForUpdate()
                ->find($faqTranslation->faq_id);

            if (! $faq) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.faq_not_found"));
            }

            // 2. Relecture verrouillée de la traduction
            $faqTranslation = FaqTranslation::withTrashed()
                ->lockForUpdate()
                ->find($faqTranslation->id);

            if (! $faqTranslation) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.translation_not_found"));
            }

            // 3. Déjà en corbeille
            if ($faqTranslation->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.already_deleted"));
            }

            // 4. Dernière traduction active
            if ($faq->translations()->count() <= 1) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.last_translation_forbidden"));
            }

            // 5. Suppression
            destroy_function($faqTranslation); // ← suppression de la traduction et de ses dépendances associées
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_faqs_translations_restore(FaqTranslation $faqTranslation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($faqTranslation) {

            // Restauration simple (restore)
            if ($faqTranslation->trashed()) {

                // Validation générique factorisée (BackendHelper) : refuse de restaurer
                // une traduction dont la question/slug entre en conflit avec une autre traduction
                // (active ou en corbeille) dans la même langue.
                assertTranslationRestore(
                    $faqTranslation,
                    __('v1/backend/others/helpers/backend-helper-messages.entityLabels.faq'),
                    $this->helperLangPath,
                    'question',
                    'slug'
                );

                restore_function($faqTranslation);
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.restore.not_trashed_forbidden"));
            }

        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_faqs_translations_force_destroy(FaqTranslation $faqTranslation): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($faqTranslation) {

            // 1. Verrou sur la FAQ (même soft-deleted), toujours en premier
            //    pour garder le même ordre de verrouillage que destroy() et syncTranslations()
            $faq = Faq::withTrashed()
                ->lockForUpdate()
                ->find($faqTranslation->faq_id);

            if (! $faq) {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.faq_not_found"));
            }

            // 2. Relecture verrouillée de la traduction (le binding peut être périmé)
            $faqTranslation = FaqTranslation::withTrashed()
                ->lockForUpdate()
                ->find($faqTranslation->id);

            if (! $faqTranslation) {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.translation_not_found"));
            }

            // 3. Seule une traduction en corbeille peut être supprimée définitivement
            if (! $faqTranslation->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.not_trashed_forbidden"));
            }

            // 4. Interdit de supprimer la dernière traduction de la FAQ (corbeille incluse)
            if ($faq->translations()->withTrashed()->count() <= 1) {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.last_translation_forbidden"));
            }

            // 5. Suppression définitive de la traduction et de ses dépendances
            force_destroy_function($faqTranslation);
        });
    }
}
