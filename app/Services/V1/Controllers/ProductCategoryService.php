<?php

namespace App\Services\V1\Controllers;

use App\Enums\Products\Categories\ProductCategoryStatus;
use App\Models\ProductCategory;
use App\Models\ProductCategoryTranslation;
use Illuminate\Support\Facades\DB;

use function __;

class ProductCategoryService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/products/categories/messages/categories-messages-service.productCategories';

    // Chemin des messages génériques du backend helper (factorisés pour toutes les entités traduisibles)
    protected string $helperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    public function __construct(protected CloudFileService $cloudFileService) {}

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function store(array $data): ProductCategory
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        return DB::transaction(function () use ($data) {

            // Création de la catégorie (sauf les traductions)
            $category = ProductCategory::create([
                'parent_id' => $data['parent_id'] ?? null,
                'sort_order' => $data['sort_order'],
                'status' => ($data['status'] ?? null) === ProductCategoryStatus::Active->value ? ProductCategoryStatus::Active : ProductCategoryStatus::Inactive,
            ]);

            // Synchronisation des traductions (création ou mise à jour)
            $this->syncTranslations($category, $data['translations']);

            // Si une image est fournie
            if (isset($data['image'])) {

                // Si $data['image'] existe et que $data['cloud_platform_uuid'] n'est pas défini, on lance une exception
                if (! isset($data['cloud_platform_uuid'])) {
                    throw new \InvalidArgumentException(__("{$this->langPath}.store.cloud_platform_uuid_required"));
                }

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
    public function update(ProductCategory $category, array $data): ProductCategory
    {
        return DB::transaction(function () use ($category, $data) {

            // Le parent fourni ne doit pas être la catégorie elle-même
            // ni l'un de ses descendants (prévention des cycles dans l'arborescence)
            $this->assertValidParent($category, $data['parent_id'] ?? null);

            $updateData = [
                'parent_id' => $data['parent_id'] ?? null,
            ];

            if (isset($data['sort_order'])) {
                $updateData['sort_order'] = $data['sort_order'];
            }

            $updateData['status'] = ($data['status'] ?? null) === ProductCategoryStatus::Active->value
                ? ProductCategoryStatus::Active
                : ProductCategoryStatus::Inactive;

            $category->update($updateData);

            if (isset($data['translations'])) {
                $this->syncTranslations($category, $data['translations']);
            }

            // Si une image est fournie
            if (isset($data['image'])) {

                // Si $data['image'] existe et que $data['cloud_platform_uuid'] n'est pas défini, on lance une exception
                if (! isset($data['cloud_platform_uuid'])) {
                    throw new \InvalidArgumentException(__("{$this->langPath}.update.cloud_platform_uuid_required"));
                }

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
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function activate(ProductCategory $category): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($category) {

            // Vérification que la catégorie n'est pas déjà active
            if ($category->status === ProductCategoryStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->langPath}.activate.already_active"));
            }

            // Activation de la catégorie sélectionnée
            $category->update([
                'status' => ProductCategoryStatus::Active,
            ]);
        });
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function deactivate(ProductCategory $category): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($category) {

            // Vérification que la catégorie n'est pas déjà inactive
            if ($category->status === ProductCategoryStatus::Inactive) {
                throw new \InvalidArgumentException(__("{$this->langPath}.deactivate.already_inactive"));
            }

            // Désactivation de la catégorie sélectionnée
            $category->update([
                'status' => ProductCategoryStatus::Inactive,
            ]);
        });
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function destroy(ProductCategory $category): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($category) {

            // Vérification : empêcher la suppression d'une catégorie active
            if ($category->status === ProductCategoryStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.active_forbidden"));
            }

            // Une catégorie parente ne peut pas être supprimée tant qu'elle a des sous-catégories
            if ($category->children()->exists()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.has_children_forbidden"));
            }

            // Suppression simple (soft delete)
            if ($category->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.already_deleted"));
            }
            destroy_function($category); // ← suppression de la catégorie et de ses traductions associées

        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function restore(ProductCategory $category): void
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
    public function forceDestroy(ProductCategory $category): void
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
    // VALIDATION DU PARENT (privé)
    // ─────────────────────────────────────────────
    /**
     * Vérifie que le parent proposé ne crée pas de cycle dans l'arborescence :
     * une catégorie ne peut pas devenir son propre parent ni être rattachée
     * à l'un de ses propres descendants.
     */
    private function assertValidParent(ProductCategory $category, ?int $parentId): void
    {
        // null = catégorie racine : toujours valide
        if ($parentId === null) {
            return;
        }

        // La catégorie ne peut pas devenir son propre parent
        if ($parentId === $category->id) {
            throw new \InvalidArgumentException(__("{$this->langPath}.update.parent_self_forbidden"));
        }

        // On remonte la chaîne des parents du candidat : si on retombe sur la
        // catégorie courante, alors le candidat est l'un de ses descendants.
        $ancestor = ProductCategory::find($parentId);

        while ($ancestor !== null) {
            if ($ancestor->id === $category->id) {
                throw new \InvalidArgumentException(__("{$this->langPath}.update.parent_descendant_forbidden"));
            }

            $ancestor = $ancestor->parent_id !== null ? ProductCategory::find($ancestor->parent_id) : null;
        }
    }

    // ─────────────────────────────────────────────
    // SYNC TRANSLATIONS (privé)
    // ─────────────────────────────────────────────
    private function syncTranslations(ProductCategory $category, array $translations): void
    {
        // Récupère les IDs des langues soumises
        $submittedLangIds = collect($translations)->pluck('lang_id')->toArray();

        // Supprime temporairement les traductions qui ne sont pas présentes dans les données soumises
        ProductCategoryTranslation::where('category_id', $category->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->delete();
        // Suppression définitive des traductions supprimées (soft delete) qui sont en corbeille, supprimer précédemment
        ProductCategoryTranslation::onlyTrashed()->where('category_id', $category->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->forceDelete();

        // Validation générique factorisée (BackendHelper) :
        //  - conflit nom/slug avec une autre entité (active ou en corbeille) dans la même langue ;
        //  - conflit avec la propre traduction de l'entité, placée en corbeille.
        // Lève une InvalidArgumentException avec un message explicite si besoin.
        assertTranslationUniqueness(
            $category,
            $translations,
            __('v1/backend/others/helpers/backend-helper-messages.entityLabels.productCategory'),
            $this->helperLangPath,
            'name',
            'slug'
        );

        // Pour chaque traduction reçue, on utilise updateOrCreate pour créer ou mettre à jour la traduction correspondante
        foreach ($translations as $translation) {

            // Vérifie si une traduction existe déjà pour la catégorie et la langue
            $existingTranslation = ProductCategoryTranslation::withTrashed()
                ->where('category_id', $category->id)
                ->where('lang_id', $translation['lang_id'])
                ->first();

            // Si une traduction existante est trouvée,
            if ($existingTranslation) {

                // Met à jour la traduction existante avec les nouvelles données
                $existingTranslation->update([
                    'name' => $translation['name'],
                    'description' => $translation['description'] ?? null,
                ]);

            } else {

                // Sinon, on crée une nouvelle traduction
                ProductCategoryTranslation::create([
                    'category_id' => $category->id,
                    'lang_id' => $translation['lang_id'],
                    'name' => $translation['name'],
                    'description' => $translation['description'] ?? null,
                ]);

            }

        }
    }
}
