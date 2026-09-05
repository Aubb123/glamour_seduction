<?php

namespace App\Services\V1\Controllers;

use App\Enums\Faqs\FaqStatus;
use App\Models\Faq;
use App\Models\FaqTranslation;
use Illuminate\Support\Facades\DB;

use function __;

class FaqService
{
    protected string $langPath = 'v1/backend/views/pages/faqs/messages/faqs-messages-service.faqs';

    // Chemin des messages génériques du backend helper (factorisés pour toutes les entités traduisibles)
    protected string $helperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function store(array $data): Faq
    {
        return DB::transaction(function () use ($data) {

            $faq = Faq::create([
                'user_id' => auth()->id(),
                'sort_order' => $data['sort_order'],
                'status' => ($data['status'] ?? null) === FaqStatus::Active->value
                    ? FaqStatus::Active
                    : FaqStatus::Inactive,
            ]);

            $this->syncTranslations($faq, $data['translations']);

            return $faq;
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function update(Faq $faq, array $data): Faq
    {
        return DB::transaction(function () use ($faq, $data) {

            $updateData = [];

            if (isset($data['sort_order'])) {
                $updateData['sort_order'] = $data['sort_order'];
            }

            $updateData['status'] = ($data['status'] ?? null) === FaqStatus::Active->value
                ? FaqStatus::Active
                : FaqStatus::Inactive;

            if (! empty($updateData)) {
                $faq->update($updateData);
            }

            if (isset($data['translations'])) {
                $this->syncTranslations($faq, $data['translations']);
            }

            return $faq;
        });
    }

    // ─────────────────────────────────────────────
    // TOGGLE ACTIVE (Activer/Désactiver)
    // ─────────────────────────────────────────────
    public function toggleActive(Faq $faq): void
    {
        DB::transaction(function () use ($faq) {
            $faq->update([
                // Inverse le statut actuel (active ↔ inactive)
                'status' => $faq->status === FaqStatus::Active
                    ? FaqStatus::Inactive
                    : FaqStatus::Active,
            ]);
        });
    }

    // ─────────────────────────────────────────────
    // DESTROY (soft delete)
    // ─────────────────────────────────────────────
    public function destroy(Faq $faq): void
    {
        DB::transaction(function () use ($faq) {

            // Seule les faqs qui ne sont pas actives peuvent être supprimées
            if ($faq->status === FaqStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.active_faq_forbidden"));
            }

            if ($faq->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.already_deleted"));
            }
            destroy_function($faq);
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function restore(Faq $faq): void
    {
        DB::transaction(function () use ($faq) {
            if ($faq->trashed()) {
                restore_function($faq);
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.restore.not_trashed_forbidden"));
            }
        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function forceDestroy(Faq $faq): void
    {
        DB::transaction(function () use ($faq) {

            // Seule les faqs qui ne sont pas actives peuvent être supprimées
            if ($faq->status === FaqStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.active_faq_forbidden"));
            }

            if ($faq->trashed()) {
                force_destroy_function($faq);
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.not_trashed_forbidden"));
            }
        });
    }

    // ─────────────────────────────────────────────
    // SYNC TRANSLATIONS (privé)
    // ─────────────────────────────────────────────
    private function syncTranslations(Faq $faq, array $translations): void
    {

        // Récupère les IDs des langues soumises
        $submittedLangIds = collect($translations)->pluck('lang_id')->toArray();

        // Supprime les traductions qui ne sont pas présentes dans les données soumises
        FaqTranslation::where('faq_id', $faq->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->delete();
        // Suppression définitive des traductions supprimées (soft delete) qui sont en corbeille, supprimer précédemment
        FaqTranslation::onlyTrashed()->where('faq_id', $faq->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->forceDelete();

        // Validation générique factorisée (BackendHelper) :
        //  - conflit question/slug avec une autre FAQ (active ou en corbeille) dans la même langue ;
        //  - conflit avec la propre traduction de la FAQ, placée en corbeille.
        // Lève une InvalidArgumentException avec un message explicite si besoin.
        assertTranslationUniqueness(
            $faq,
            $translations,
            __('v1/backend/others/helpers/backend-helper-messages.entityLabels.faq'),
            $this->helperLangPath,
            'question',
            'slug'
        );

        foreach ($translations as $translation) {

            // Vérifie si une traduction existe déjà pour la faq et la langue
            $existingTranslation = FaqTranslation::withTrashed()
                ->where('faq_id', $faq->id)
                ->where('lang_id', $translation['lang_id'])
                ->first();

            // Si une traduction existante est trouvée
            if ($existingTranslation) {

                // Met à jour la traduction existante
                $existingTranslation->update([
                    'question' => $translation['question'],
                    'answer' => $translation['answer'],
                ]);

            } else {
                // Crée une nouvelle traduction
                FaqTranslation::create([
                    'faq_id' => $faq->id,
                    'lang_id' => $translation['lang_id'],
                    'question' => $translation['question'],
                    'answer' => $translation['answer'],
                ]);
            }
        }
    }
}
