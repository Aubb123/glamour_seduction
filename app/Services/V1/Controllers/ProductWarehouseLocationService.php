<?php

namespace App\Services\V1\Controllers;

use App\Enums\Products\WarehouseLocations\ProductWarehouseLocationStatus;
use App\Enums\Products\WarehouseLocations\ProductWarehouseLocationType;
use App\Models\ProductWarehouseLocation;
use App\Models\ProductWarehouseLocationTranslation;
use Illuminate\Support\Facades\DB;

use function __;

class ProductWarehouseLocationService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/products/warehouse-locations/messages/warehouse-locations-messages-service.warehouseLocations';

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_locations_store(array $data): ProductWarehouseLocation
    {
        return DB::transaction(function () use ($data) {

            $type = ProductWarehouseLocationType::tryFrom($data['type'] ?? '') ?? ProductWarehouseLocationType::Shelf;
            $status = ProductWarehouseLocationStatus::tryFrom($data['status'] ?? '') ?? ProductWarehouseLocationStatus::Inactive;

            $location = ProductWarehouseLocation::create([
                'warehouse_id' => $data['warehouse_id'],
                'parent_id' => $data['parent_id'] ?? null,
                'type' => $type,
                'code' => $data['code'],
                'sort_order' => $data['sort_order'],
                'notes' => $data['notes'] ?? null,
                'status' => $status,
            ]);

            $this->syncTranslations($location, $data['translations']);

            return $location;
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_locations_update(ProductWarehouseLocation $location, array $data): ProductWarehouseLocation
    {
        return DB::transaction(function () use ($location, $data) {

            $type = ProductWarehouseLocationType::tryFrom($data['type'] ?? '') ?? $location->type;
            $status = ProductWarehouseLocationStatus::tryFrom($data['status'] ?? '') ?? $location->status;

            // Le code et l'entrepôt ne sont jamais mis à jour (immuables après création)
            $location->update([
                'parent_id' => $data['parent_id'] ?? null,
                'type' => $type,
                'sort_order' => $data['sort_order'] ?? $location->sort_order,
                'notes' => $data['notes'] ?? null,
                'status' => $status,
            ]);

            if (array_key_exists('translations', $data)) {
                $this->syncTranslations($location, $data['translations']);
            }

            return $location;
        });
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_locations_activate(ProductWarehouseLocation $location): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($location) {

            // Vérification que l'emplacement n'est pas déjà actif
            if ($location->status === ProductWarehouseLocationStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.activate.already_active"));
            }

            // Activation de l'emplacement sélectionné
            $location->update(['status' => ProductWarehouseLocationStatus::Active]);
        });
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_locations_deactivate(ProductWarehouseLocation $location): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($location) {

            // Vérification que l'emplacement n'est pas déjà inactif
            if ($location->status === ProductWarehouseLocationStatus::Inactive) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.deactivate.already_inactive"));
            }

            // Désactivation de l'emplacement sélectionné
            $location->update(['status' => ProductWarehouseLocationStatus::Inactive]);
        });
    }

    // ─────────────────────────────────────────────
    // DESTROY (soft delete)
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_locations_destroy(ProductWarehouseLocation $location): void
    {
        DB::transaction(function () use ($location) {

            // Un emplacement actif ne peut pas être supprimé
            if ($location->status === ProductWarehouseLocationStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.active_forbidden"));
            }

            if ($location->trashed()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.already_deleted"));
            }

            // destroy_function bloque aussi si des emplacements enfants existent
            // (voir $dependentRelations = ['children'] du modèle).
            destroy_function($location);
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_locations_restore(ProductWarehouseLocation $location): void
    {
        DB::transaction(function () use ($location) {

            if (! $location->trashed()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.restore.not_trashed_forbidden"));
            }

            // Le parent ne doit pas être en corbeille (arborescence incohérente sinon)
            if ($location->parent_id !== null) {
                $parent = ProductWarehouseLocation::withTrashed()->find($location->parent_id);

                if ($parent && $parent->trashed()) {
                    throw new \InvalidArgumentException(__("{$this->backendLangPath}.restore.parent_trashed"));
                }
            }

            // restore_function restaure l'emplacement ET ses traductions
            restore_function($location);
        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_warehouse_locations_force_destroy(ProductWarehouseLocation $location): void
    {
        DB::transaction(function () use ($location) {

            if (! $location->trashed()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.not_trashed_forbidden"));
            }

            // Un emplacement actif ne peut pas être supprimé définitivement
            if ($location->status === ProductWarehouseLocationStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.active_forbidden"));
            }

            force_destroy_function($location);
        });
    }

    // ─────────────────────────────────────────────
    // SYNC TRANSLATIONS (privé)
    // ─────────────────────────────────────────────
    private function syncTranslations(ProductWarehouseLocation $location, array $translations): void
    {
        // Récupère les IDs des langues soumises
        $submittedLangIds = collect($translations)->pluck('lang_id')->toArray();

        // Supprime temporairement les traductions qui ne sont pas présentes dans les données soumises
        ProductWarehouseLocationTranslation::where('location_id', $location->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->delete();
        // Suppression définitive des traductions supprimées (soft delete) qui sont en corbeille
        ProductWarehouseLocationTranslation::onlyTrashed()->where('location_id', $location->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->forceDelete();

        // Unicité du nom par entrepôt et par langue (corbeille incluse)
        $this->assertTranslationsUniqueInWarehouse($location, $translations);

        foreach ($translations as $translation) {

            $existingTranslation = ProductWarehouseLocationTranslation::withTrashed()
                ->where('location_id', $location->id)
                ->where('lang_id', $translation['lang_id'])
                ->first();

            // Si une traduction existe déjà pour cette langue, on la met à jour. Sinon, on la crée.
            if ($existingTranslation) {

                // Une traduction en corbeille ne doit pas être modifiée silencieusement
                if ($existingTranslation->trashed()) {
                    throw new \InvalidArgumentException(__("{$this->backendLangPath}.trashedTranslationConflict", ['lang_id' => $translation['lang_id']])
                    );
                }

                $existingTranslation->update([
                    'name' => $translation['name'],
                    'description' => $translation['description'],
                ]);

            } else {

                ProductWarehouseLocationTranslation::create([
                    'location_id' => $location->id,
                    'lang_id' => $translation['lang_id'],
                    'name' => $translation['name'],
                    'description' => $translation['description'],
                ]);
            }
        }
    }

    /**
     * Vérifie qu'aucun nom soumis n'entre en conflit avec une traduction existante
     * (corbeille incluse) du MÊME entrepôt et de la MÊME langue, à l'exclusion
     * de l'emplacement courant.
     *
     * NB : BackendHelper::assertTranslationUniqueness ne peut pas filtrer par entrepôt
     * (la table de traduction ne porte pas warehouse_id) — requête dédiée volontaire.
     */
    private function assertTranslationsUniqueInWarehouse(ProductWarehouseLocation $location, array $translations): void
    {
        foreach ($translations as $translation) {

            $name = trim((string) ($translation['name'] ?? ''));

            if ($name === '') {
                continue;
            }

            $exists = ProductWarehouseLocationTranslation::withTrashed()
                ->where('lang_id', $translation['lang_id'])
                ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
                ->whereHas('location', fn ($query) => $query->withTrashed()->where('warehouse_id', $location->warehouse_id))
                ->where('location_id', '!=', $location->id)
                ->exists();

            if ($exists) {
                throw new \InvalidArgumentException(
                    __("{$this->backendLangPath}.translations.name.unique_in_warehouse", ['name' => $name])
                );
            }
        }
    }
}
