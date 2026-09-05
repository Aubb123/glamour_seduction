<?php

namespace App\Services\V1\Controllers;

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
    public function destroy(FaqTranslation $faqTranslation): void
    {
        DB::transaction(function () use ($faqTranslation) {

            $faq = $faqTranslation->faq()->withTrashed()->first();

            if ($faq) {
                $translationsCount = $faq->translations()->count();

                if ($translationsCount <= 1) {
                    throw new \InvalidArgumentException(__("{$this->langPath}.destroy.last_translation_forbidden"));
                }
            }

            if ($faqTranslation->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.already_deleted"));
            }
            destroy_function($faqTranslation);
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function restore(FaqTranslation $faqTranslation): void
    {
        DB::transaction(function () use ($faqTranslation) {
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
    public function forceDestroy(FaqTranslation $faqTranslation): void
    {
        DB::transaction(function () use ($faqTranslation) {

            $faq = $faqTranslation->faq()->withTrashed()->first();

            if ($faq) {
                $translationsCount = $faq->translations()->withTrashed()->count();

                if ($translationsCount <= 1) {
                    throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.last_translation_forbidden"));
                }
            }

            if ($faqTranslation->trashed()) {
                force_destroy_function($faqTranslation);
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.not_trashed_forbidden"));
            }
        });
    }
}
