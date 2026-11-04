<?php

namespace App\Services\V1\Controllers;

use App\Enums\Blogs\Categories\BlogCategoryStatus;
use App\Models\BlogCategory;
use App\Models\BlogCategoryTranslation;
use Illuminate\Support\Facades\DB;

use function __;

class BlogCategoryService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/blogs/categories/messages/categories-messages-service.categories';

    // Chemin des messages génériques du backend helper (factorisés pour toutes les entités traduisibles)
    protected string $helperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    // Service pour gérer le téléchargement d'images vers le cloud ou le stockage local
    public function __construct(protected CloudFileService $cloudFileService) {}

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_blog_categories_store(array $data): BlogCategory
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        return DB::transaction(function () use ($data) {

            // Création de la catégorie (sauf les traductions)
            $category = BlogCategory::create([
                'sort_order' => $data['sort_order'],
                'status' => ($data['status'] ?? null) === BlogCategoryStatus::Active->value ? BlogCategoryStatus::Active : BlogCategoryStatus::Inactive,
            ]);

            // Synchronisation des traductions (création ou mise à jour)
            $this->syncTranslations($category, $data['translations']);

            // Si une image est fournie
            if (isset($data['image'])) {

                // Si $data['image'] existe et que $data['cloud_platform_uuid'] n'est pas défini, on lance une exception
                if (! isset($data['cloud_platform_uuid'])) {
                    throw new \InvalidArgumentException(__("{$this->langPath}.store.cloud_platform_uuid_required"));
                }

                // Appel du service pour gérer le téléchargement de l'image vers le cloud ou le stockage local, en associant l'image à la catégorie créée
                $this->cloudFileService->uploadEntityImage(
                    $data['image'],
                    $data['cloud_platform_uuid'],
                    $category,
                    $category->getTable(),
                    $this->langPath,
                );
            }

            return $category; // ← retourne la catégorie créée avec ses traductions
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_blog_categories_update(BlogCategory $category, array $data): BlogCategory
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        return DB::transaction(function () use ($category, $data) {

            // $updateData contiendra uniquement les champs à mettre à jour
            $updateData = [];

            // Si sort_order est défini dans les données, on l'ajoute à $updateData pour la mise à jour
            if (isset($data['sort_order'])) {
                $updateData['sort_order'] = $data['sort_order'];
            }

            // Détermine le statut à mettre à jour : si le statut envoyé est "Active", on le met à jour en tant que tel,
            // sinon on le met à "Inactive"
            $updateData['status'] = ($data['status'] ?? null) === BlogCategoryStatus::Active->value ? BlogCategoryStatus::Active : BlogCategoryStatus::Inactive;

            // Si $updateData n'est pas vide, on met à jour la catégorie avec les champs fournis
            if (! empty($updateData)) {
                $category->update($updateData);
            }

            // Synchronisation des traductions (création ou mise à jour)
            if (isset($data['translations'])) {
                $this->syncTranslations($category, $data['translations']);
            }

            // Si une image est fournie
            if (isset($data['image'])) {

                // Si $data['image'] existe et que $data['cloud_platform_uuid'] n'est pas défini, on lance une exception
                if (! isset($data['cloud_platform_uuid'])) {
                    throw new \InvalidArgumentException(__("{$this->langPath}.update.cloud_platform_uuid_required"));
                }

                // Appel du service pour gérer le téléchargement de l'image vers le cloud ou le stockage local, en associant l'image à la catégorie mise à jour
                $this->cloudFileService->uploadEntityImage(
                    $data['image'],
                    $data['cloud_platform_uuid'],
                    $category,
                    $category->getTable(),
                    $this->langPath,
                );
            }

            return $category;
        });
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_blog_categories_destroy(BlogCategory $category): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($category) {

            // Vérification : empêcher la suppression d'une catégorie active
            if ($category->status === BlogCategoryStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.active_forbidden"));
            }

            // Suppression simple (soft delete)
            if ($category->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.already_deleted"));
            }

            // ← suppression de la catégorie et de ses traductions associées
            destroy_function($category);

        });
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_blog_categories_activate(BlogCategory $category): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($category) {

            // Vérification que la catégorie n'est pas déjà active
            if ($category->status === BlogCategoryStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->langPath}.activate.already_active"));
            }

            // Activation de la catégorie sélectionnée
            $category->update([
                'status' => BlogCategoryStatus::Active,
            ]);

        });
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_blog_categories_deactivate(BlogCategory $category): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($category) {

            // Vérification que la catégorie n'est pas déjà inactive
            if ($category->status === BlogCategoryStatus::Inactive) {
                throw new \InvalidArgumentException(__("{$this->langPath}.deactivate.already_inactive"));
            }

            // Désactivation de la catégorie sélectionnée
            $category->update([
                'status' => BlogCategoryStatus::Inactive,
            ]);
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_blog_categories_restore(BlogCategory $category): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($category) {

            // Restauration simple (restore)
            if ($category->trashed()) {
                restore_function($category); // ← restauration de la catégorie et de ses traductions associées
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.restore.not_trashed_forbidden"));
            }

        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_blog_categories_force_destroy(BlogCategory $category): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($category) {

            // Suppression définitive (forceDelete)
            if ($category->trashed()) {
                force_destroy_function($category); // ← suppression définitive de la catégorie et de ses traductions associées
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.not_trashed_forbidden"));
            }

        });
    }

    // ─────────────────────────────────────────────
    // SYNC TRANSLATIONS (privé)
    // ─────────────────────────────────────────────
    private function syncTranslations(BlogCategory $category, array $translations): void
    {
        // Récupère les IDs des langues soumises
        $submittedLangIds = collect($translations)->pluck('lang_id')->toArray();

        // Supprime temporairement les traductions qui ne sont pas présentes dans les données soumises
        BlogCategoryTranslation::where('category_id', $category->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->delete();
        // Suppression définitive des traductions supprimées (soft delete) qui sont en corbeille, supprimer précédemment
        BlogCategoryTranslation::onlyTrashed()->where('category_id', $category->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->forceDelete();

        // Validation générique factorisée (BackendHelper) :
        //  - conflit nom/slug avec une autre entité (active ou en corbeille) dans la même langue ;
        //  - conflit avec la propre traduction de l'entité, placée en corbeille.
        // Lève une InvalidArgumentException avec un message explicite si besoin.
        assertTranslationUniqueness(
            $category,
            $translations,
            __('v1/backend/others/helpers/backend-helper-messages.entityLabels.category'),
            $this->helperLangPath,
            'name',
            'slug'
        );

        // Pour chaque traduction reçue, on utilise updateOrCreate pour créer ou mettre à jour la traduction correspondante
        foreach ($translations as $translation) {

            // Vérifie si une traduction existe déjà pour la catégorie et la langue
            $existingTranslation = BlogCategoryTranslation::withTrashed()
                ->where('category_id', $category->id)
                ->where('lang_id', $translation['lang_id'])
                ->first();

            // Si une traduction existante est trouvée,
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
                    'description' => $translation['description'] ?? null,
                ]);

            } else {

                // Sinon, on crée une nouvelle traduction
                BlogCategoryTranslation::create([
                    'category_id' => $category->id,
                    'lang_id' => $translation['lang_id'],
                    'name' => $translation['name'],
                    'description' => $translation['description'] ?? null,
                ]);

            }

        }
    }
}
