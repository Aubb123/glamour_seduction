<?php

namespace App\Services\V1\Controllers;

use App\Enums\Products\Brands\ProductBrandStatus;
use App\Models\ProductBrand;
use App\Models\ProductBrandTranslation;
use Illuminate\Support\Facades\DB;

use function __;

class ProductBrandService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/products/brands/messages/brands-messages-service.productBrands';

    // Chemin des messages génériques du backend helper (factorisés pour toutes les entités traduisibles)
    protected string $helperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    public function __construct(protected CloudFileService $cloudFileService) {}

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function store(array $data): ProductBrand
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        return DB::transaction(function () use ($data) {

            // Création de la marque (sauf les traductions)
            $brand = ProductBrand::create([
                'sort_order' => $data['sort_order'],
                'status' => ($data['status'] ?? null) === ProductBrandStatus::Active->value ? ProductBrandStatus::Active : ProductBrandStatus::Inactive,
            ]);

            // Synchronisation des traductions (création ou mise à jour)
            $this->syncTranslations($brand, $data['translations']);

            // Si une image est fournie
            if (isset($data['image'])) {

                // Si $data['image'] existe et que $data['cloud_platform_uuid'] n'est pas défini, on lance une exception
                if (! isset($data['cloud_platform_uuid'])) {
                    throw new \InvalidArgumentException(__("{$this->langPath}.store.cloud_platform_uuid_required"));
                }

                $this->cloudFileService->uploadEntityImage(
                    $data['image'],
                    $data['cloud_platform_uuid'],
                    $brand,
                    $brand->getTable(),
                    $this->langPath,
                );
            }

            return $brand; // ← retourne la marque créée avec ses traductions
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function update(ProductBrand $brand, array $data): ProductBrand
    {
        return DB::transaction(function () use ($brand, $data) {

            $updateData = [];

            if (isset($data['sort_order'])) {
                $updateData['sort_order'] = $data['sort_order'];
            }

            $updateData['status'] = ($data['status'] ?? null) === ProductBrandStatus::Active->value
                ? ProductBrandStatus::Active
                : ProductBrandStatus::Inactive;

            $brand->update($updateData);

            if (isset($data['translations'])) {
                $this->syncTranslations($brand, $data['translations']);
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
                    $brand,
                    $brand->getTable(),
                    $this->langPath,
                );
            }

            return $brand;
        });
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function activate(ProductBrand $brand): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($brand) {

            // Vérification que la marque n'est pas déjà active
            if ($brand->status === ProductBrandStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->langPath}.activate.already_active"));
            }

            // Activation de la marque sélectionnée
            $brand->update([
                'status' => ProductBrandStatus::Active,
            ]);
        });
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function deactivate(ProductBrand $brand): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($brand) {

            // Vérification que la marque n'est pas déjà inactive
            if ($brand->status === ProductBrandStatus::Inactive) {
                throw new \InvalidArgumentException(__("{$this->langPath}.deactivate.already_inactive"));
            }

            // Désactivation de la marque sélectionnée
            $brand->update([
                'status' => ProductBrandStatus::Inactive,
            ]);
        });
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function destroy(ProductBrand $brand): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($brand) {

            // Vérification : empêcher la suppression d'une marque active
            if ($brand->status === ProductBrandStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.active_forbidden"));
            }

            // Suppression simple (soft delete)
            if ($brand->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.already_deleted"));
            }
            destroy_function($brand); // ← suppression de la marque et de ses traductions associées

        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function restore(ProductBrand $brand): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($brand) {

            // Restauration simple (restore)
            if ($brand->trashed()) {
                restore_function($brand); // ← restauration de la marque et de ses traductions associées
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.restore.not_trashed_forbidden"));
            }

        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function forceDestroy(ProductBrand $brand): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($brand) {

            // Suppression définitive (forceDelete)
            if ($brand->trashed()) {
                force_destroy_function($brand); // ← suppression définitive de la marque et de ses traductions associées
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.not_trashed_forbidden"));
            }

        });
    }

    // ─────────────────────────────────────────────
    // SYNC TRANSLATIONS (privé)
    // ─────────────────────────────────────────────
    private function syncTranslations(ProductBrand $brand, array $translations): void
    {
        // Récupère les IDs des langues soumises
        $submittedLangIds = collect($translations)->pluck('lang_id')->toArray();

        // Supprime temporairement les traductions qui ne sont pas présentes dans les données soumises
        ProductBrandTranslation::where('brand_id', $brand->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->delete();
        // Suppression définitive des traductions supprimées (soft delete) qui sont en corbeille, supprimer précédemment
        ProductBrandTranslation::onlyTrashed()->where('brand_id', $brand->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->forceDelete();

        // Validation générique factorisée (BackendHelper) :
        //  - conflit nom/slug avec une autre entité (active ou en corbeille) dans la même langue ;
        //  - conflit avec la propre traduction de l'entité, placée en corbeille.
        // Lève une InvalidArgumentException avec un message explicite si besoin.
        assertTranslationUniqueness(
            $brand,
            $translations,
            __('v1/backend/others/helpers/backend-helper-messages.entityLabels.productBrand'),
            $this->helperLangPath,
            'name',
            'slug'
        );

        // Pour chaque traduction reçue, on utilise updateOrCreate pour créer ou mettre à jour la traduction correspondante
        foreach ($translations as $translation) {

            // Vérifie si une traduction existe déjà pour la marque et la langue
            $existingTranslation = ProductBrandTranslation::withTrashed()
                ->where('brand_id', $brand->id)
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
                ProductBrandTranslation::create([
                    'brand_id' => $brand->id,
                    'lang_id' => $translation['lang_id'],
                    'name' => $translation['name'],
                    'description' => $translation['description'] ?? null,
                ]);

            }

        }
    }
}
