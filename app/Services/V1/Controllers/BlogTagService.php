<?php

namespace App\Services\V1\Controllers;

use App\Enums\Blogs\Tags\BlogTagStatus;
use App\Models\BlogTag;
use App\Models\BlogTagTranslation;
use Illuminate\Support\Facades\DB;

use function __;

class BlogTagService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/blogs/tags/messages/tags-messages-service.tags';

    // Chemin des messages génériques du backend helper (factorisés pour toutes les entités traduisibles)
    protected string $helperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function store(array $data): BlogTag
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        return DB::transaction(function () use ($data) {

            // Création du tag (sauf les traductions)
            $tag = BlogTag::create([
                'sort_order' => $data['sort_order'],
                'status' => ($data['status'] ?? null) === BlogTagStatus::Active->value
                    ? BlogTagStatus::Active
                    : BlogTagStatus::Inactive,
            ]);

            // Synchronisation des traductions (création ou mise à jour)
            $this->syncTranslations($tag, $data['translations']);

            return $tag; // ← retourne le tag créé avec ses traductions
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function update(BlogTag $tag, array $data): BlogTag
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        return DB::transaction(function () use ($tag, $data) {

            // Mise à jour des champs du tag (sauf les traductions)
            $updateData = [];

            if (isset($data['sort_order'])) {
                $updateData['sort_order'] = $data['sort_order'];
            }

            $updateData['status'] = ($data['status'] ?? null) === BlogTagStatus::Active->value
                ? BlogTagStatus::Active
                : BlogTagStatus::Inactive;

            if (! empty($updateData)) {
                $tag->update($updateData);
            }

            // Synchronisation des traductions (création ou mise à jour)
            if (isset($data['translations'])) {
                $this->syncTranslations($tag, $data['translations']);
            }

            return $tag;
        });
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function activate(BlogTag $tag): void
    {
        DB::transaction(function () use ($tag) {

            // Vérification : empêcher l'activation d'un tag déjà actif
            if ($tag->status === BlogTagStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->langPath}.activate.already_active"));
            }

            // Activation du tag
            $tag->update(['status' => BlogTagStatus::Active]);
        });
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function deactivate(BlogTag $tag): void
    {
        DB::transaction(function () use ($tag) {

            // Vérification : empêcher la désactivation d'un tag déjà inactif
            if ($tag->status === BlogTagStatus::Inactive) {
                throw new \InvalidArgumentException(__("{$this->langPath}.deactivate.already_inactive"));
            }

            // Désactivation du tag
            $tag->update(['status' => BlogTagStatus::Inactive]);
        });
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function destroy(BlogTag $tag): void
    {
        DB::transaction(function () use ($tag) {

            // Vérification : empêcher la suppression d'un tag actif
            if ($tag->status === BlogTagStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.active_forbidden"));
            }

            // Suppression simple (soft delete)
            if ($tag->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.already_deleted"));
            }
            destroy_function($tag); // ← suppression du tag et de ses traductions associées
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function restore(BlogTag $tag): void
    {
        DB::transaction(function () use ($tag) {

            // Restauration simple (restore)
            if ($tag->trashed()) {
                restore_function($tag); // ← restauration du tag et de ses traductions associées
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.restore.not_trashed_forbidden"));
            }

        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function forceDestroy(BlogTag $tag): void
    {
        DB::transaction(function () use ($tag) {

            // Suppression définitive (forceDelete)
            if ($tag->trashed()) {
                force_destroy_function($tag); // ← suppression définitive du tag et de ses traductions associées
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.not_trashed_forbidden"));
            }

        });
    }

    // ─────────────────────────────────────────────
    // SYNC TRANSLATIONS (privé)
    // ─────────────────────────────────────────────
    private function syncTranslations(BlogTag $tag, array $translations): void
    {

        // Récupère les IDs des langues soumises
        $submittedLangIds = collect($translations)->pluck('lang_id')->toArray();

        // Supprime les traductions qui ne sont pas présentes dans les données soumises
        BlogTagTranslation::where('tag_id', $tag->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->delete();
        // Suppression définitive des traductions supprimées (soft delete) qui sont en corbeille, supprimer précédemment
        BlogTagTranslation::onlyTrashed()->where('tag_id', $tag->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->forceDelete();

        // Validation générique factorisée (BackendHelper) :
        //  - conflit nom/slug avec une autre entité (active ou en corbeille) dans la même langue ;
        //  - conflit avec la propre traduction de l'entité, placée en corbeille.
        // Lève une InvalidArgumentException avec un message explicite si besoin.
        assertTranslationUniqueness(
            $tag,
            $translations,
            __('v1/backend/others/helpers/backend-helper-messages.entityLabels.tag'),
            $this->helperLangPath,
            'name',
            'slug'
        );

        // Pour chaque traduction reçue, on utilise updateOrCreate pour créer ou mettre à jour la traduction correspondante
        foreach ($translations as $translation) {

            // Vérifie si une traduction existe déjà pour le tag et la langue
            $existingTranslation = BlogTagTranslation::withTrashed()
                ->where('tag_id', $tag->id)
                ->where('lang_id', $translation['lang_id'])
                ->first();

            // Si une traduction existante est trouvée
            if ($existingTranslation) {

                // Une traduction en corbeille ne doit pas être modifiée silencieusement
                if ($existingTranslation->trashed()) {
                    throw new \InvalidArgumentException(
                        __("{$this->langPath}.trashedTranslationConflict", [
                            'lang_id' => $translation['lang_id'],
                        ])
                    );
                }

                // Met à jour la traduction existante avec les nouvelles données
                $existingTranslation->update([
                    'name' => $translation['name'],
                ]);

            } else {
                // Si aucune traduction existante n'est trouvée, on crée une nouvelle traduction
                BlogTagTranslation::create([
                    'tag_id' => $tag->id,
                    'lang_id' => $translation['lang_id'],
                    'name' => $translation['name'],
                ]);
            }
        }
    }
}
