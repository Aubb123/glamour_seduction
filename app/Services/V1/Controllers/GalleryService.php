<?php

namespace App\Services\V1\Controllers;

use App\Enums\Galleries\GalleryStatus;
use App\Models\Gallery;
use App\Models\GalleryTranslation;
use Illuminate\Support\Facades\DB;

use function __;

class GalleryService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/galleries/messages/galleries-messages-service.galleries';

    // Chemin des messages génériques du backend helper (factorisés pour toutes les entités traduisibles)
    protected string $helperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    public function __construct(
        protected CloudFileService $cloudFileService,
    ) {}

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function store(array $data): Gallery
    {
        return DB::transaction(function () use ($data) {

            $gallery = Gallery::create([
                'user_id' => auth()->id(),
                'sort_order' => $data['sort_order'],
                'status' => ($data['status'] ?? null) === GalleryStatus::Active->value
                    ? GalleryStatus::Active
                    : GalleryStatus::Inactive,
            ]);

            // Sync des traductions si présentes
            if (! empty($data['translations'])) {
                $this->syncTranslations($gallery, $data['translations']);
            }

            // Si une image est fournie
            if (isset($data['image'])) {

                // Si $data['image'] existe et que $data['cloud_platform_uuid'] n'est pas défini, on lance une exception
                if (! isset($data['cloud_platform_uuid'])) {
                    throw new \InvalidArgumentException(__("{$this->langPath}.store.cloud_platform_uuid_required"));
                }

                // Appel du service pour gérer le téléchargement de l'image vers le cloud ou le stockage local, en associant l'image à la galerie créée
                $this->cloudFileService->uploadEntityImage(
                    $data['image'],
                    $data['cloud_platform_uuid'],
                    $gallery,
                    $gallery->getTable(),
                    $this->langPath,
                );
            }

            return $gallery;
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function update(Gallery $gallery, array $data): Gallery
    {
        return DB::transaction(function () use ($gallery, $data) {

            $updateData = [];

            if (isset($data['sort_order'])) {
                $updateData['sort_order'] = $data['sort_order'];
            }

            $updateData['status'] = ($data['status'] ?? null) === GalleryStatus::Active->value
                ? GalleryStatus::Active
                : GalleryStatus::Inactive;

            if (! empty($updateData)) {
                $gallery->update($updateData);
            }

            // Mise à jour des traductions si présentes
            if (isset($data['translations'])) {
                $this->syncTranslations($gallery, $data['translations']);
            }

            // Upload de l'image si fournie
            // Si une image est fournie
            if (isset($data['image'])) {

                // Si $data['image'] existe et que $data['cloud_platform_uuid'] n'est pas défini, on lance une exception
                if (! isset($data['cloud_platform_uuid'])) {
                    throw new \InvalidArgumentException(__("{$this->langPath}.update.cloud_platform_uuid_required"));
                }

                // Appel du service pour gérer le téléchargement de l'image vers le cloud ou le stockage local, en associant l'image à la galerie mise à jour
                $this->cloudFileService->uploadEntityImage(
                    $data['image'],
                    $data['cloud_platform_uuid'],
                    $gallery,
                    $gallery->getTable(),
                    $this->langPath,
                );
            }

            return $gallery;
        });
    }

    // ─────────────────────────────────────────────
    // TOGGLE ACTIVE (Activer/Désactiver)
    // ─────────────────────────────────────────────
    public function toggleActive(Gallery $gallery): void
    {
        DB::transaction(function () use ($gallery) {
            // Inverse le statut actuel (active ↔ inactive)
            $gallery->update([
                'status' => $gallery->status === GalleryStatus::Active
                    ? GalleryStatus::Inactive
                    : GalleryStatus::Active,
            ]);
        });
    }

    // ─────────────────────────────────────────────
    // DESTROY (soft delete)
    // ─────────────────────────────────────────────
    public function destroy(Gallery $gallery): void
    {
        DB::transaction(function () use ($gallery) {

            // Seule les galeries qui ne sont pas actives peuvent être supprimées
            if ($gallery->status === GalleryStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.active_gallery_forbidden"));
            }

            if ($gallery->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.already_deleted"));
            }
            destroy_function($gallery);
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function restore(Gallery $gallery): void
    {
        DB::transaction(function () use ($gallery) {
            if ($gallery->trashed()) {
                restore_function($gallery);
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.restore.not_trashed_forbidden"));
            }
        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function forceDestroy(Gallery $gallery): void
    {
        DB::transaction(function () use ($gallery) {

            // Seule les galeries qui sont supprimées peuvent être détruites définitivement
            if (! $gallery->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.not_trashed_gallery_forbidden"));
            }

            // Seule les galeries qui ne sont pas actives peuvent être détruites définitivement
            if ($gallery->status === GalleryStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.active_gallery_forbidden"));
            }

            // Suppression définitive de la galerie et de ses traductions associées
            if ($gallery->trashed()) {
                force_destroy_function($gallery);
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.not_trashed_forbidden"));
            }
        });
    }

    // ─────────────────────────────────────────────
    // SYNC TRANSLATIONS (privé)
    // ─────────────────────────────────────────────
    private function syncTranslations(Gallery $gallery, array $translations): void
    {

        // Récupère les IDs des langues soumises
        $submittedLangIds = collect($translations)->pluck('lang_id')->toArray();

        // Supprime les traductions qui ne sont pas présentes dans les données soumises.
        GalleryTranslation::where('gallery_id', $gallery->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->delete();
        // Suppression définitive des traductions supprimées (soft delete) qui sont en corbeille, supprimer précédemment
        GalleryTranslation::onlyTrashed()->where('gallery_id', $gallery->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->forceDelete();

        // Validation générique factorisée (BackendHelper) :
        //  - conflit titre/slug avec une autre galerie (active ou en corbeille) dans la même langue ;
        //  - conflit avec la propre traduction de la galerie, placée en corbeille.
        // Lève une InvalidArgumentException avec un message explicite si besoin.
        assertTranslationUniqueness(
            $gallery,
            $translations,
            __('v1/backend/others/helpers/backend-helper-messages.entityLabels.gallery'),
            $this->helperLangPath,
            'title',
            'slug'
        );

        foreach ($translations as $translation) {

            // Vérifie si une traduction existe déjà pour la galerie et la langue
            $existingTranslation = GalleryTranslation::withTrashed()
                ->where('gallery_id', $gallery->id)
                ->where('lang_id', $translation['lang_id'])
                ->first();

            // Si une traduction existe déjà
            if ($existingTranslation) {

                // Met à jour la traduction existante
                $existingTranslation->update([
                    'title' => $translation['title'],
                    'description' => $translation['description'] ?? null,
                ]);

            } else {
                // Crée une nouvelle traduction
                GalleryTranslation::create([
                    'gallery_id' => $gallery->id,
                    'lang_id' => $translation['lang_id'],
                    'title' => $translation['title'],
                    'description' => $translation['description'] ?? null,
                ]);
            }
        }
    }
}
