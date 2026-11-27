<?php

namespace App\Services\V1\Controllers;

use App\Enums\Products\Warehouses\ProductWarehouseStatus;
use App\Enums\Products\Warehouses\ProductWarehouseStockTrackingMode;
use App\Enums\Products\Warehouses\ProductWarehouseType;
use App\Models\ProductWarehouse;
use App\Models\ProductWarehouseTranslation;
use Illuminate\Support\Facades\DB;

use function __;

class ProductWarehouseService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/products/warehouses/messages/warehouses-messages-service.warehouses';

    // Chemin des messages génériques du backend helper (factorisés pour toutes les entités traduisibles)
    protected string $backendHelperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_store(array $data): ProductWarehouse
    {
        return DB::transaction(function () use ($data) {

            $type = ProductWarehouseType::tryFrom($data['type'] ?? '') ?? ProductWarehouseType::Storage;
            $stockTrackingMode = ProductWarehouseStockTrackingMode::tryFrom($data['stock_tracking_mode'] ?? '') ?? ProductWarehouseStockTrackingMode::Warehouse;
            $status = ProductWarehouseStatus::tryFrom($data['status'] ?? '') ?? ProductWarehouseStatus::Inactive;
            $isDefault = (bool) ($data['is_default'] ?? false);

            if ($isDefault) {
                $this->assertCanBeDefault($type, $status);

                // Bascule de l'ancien entrepôt par défaut
                ProductWarehouse::where('is_default', true)->update(['is_default' => false]);
            }

            $warehouse = ProductWarehouse::create([
                'sort_order' => $data['sort_order'],
                'code' => $data['code'],
                'type' => $type,
                'stock_tracking_mode' => $stockTrackingMode,
                'division_id' => $data['division_id'] ?? null,
                'city' => $data['city'] ?? null,
                'address' => $data['address'] ?? null,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'] ?? null,
                'notes' => $data['notes'] ?? null,
                'status' => $status,
                'is_default' => $isDefault,
            ]);

            $this->syncTranslations($warehouse, $data['translations']);

            return $warehouse;
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_update(ProductWarehouse $warehouse, array $data): ProductWarehouse
    {
        return DB::transaction(function () use ($warehouse, $data) {

            $updateData = [];

            // Le code n'est jamais mis à jour (immuable après création)
            foreach (['sort_order', 'type', 'stock_tracking_mode', 'division_id', 'city', 'address', 'latitude', 'longitude', 'phone', 'email', 'notes'] as $field) {
                if (array_key_exists($field, $data)) {
                    $updateData[$field] = $data[$field];
                }
            }

            $newType = ProductWarehouseType::tryFrom($data['type'] ?? '') ?? $warehouse->type;
            $newStatus = ProductWarehouseStatus::tryFrom($data['status'] ?? '') ?? $warehouse->status;

            $updateData['type'] = $newType;
            $updateData['status'] = $newStatus;

            if ($warehouse->is_default) {

                // Si l'entrepôt est par défaut, il ne peut pas être désactivé ni changer de type.
                if (array_key_exists('is_default', $data) && ! $data['is_default']) {
                    throw new \InvalidArgumentException(__("{$this->backendLangPath}.update.default_release_forbidden"));
                }

                if ($newStatus !== ProductWarehouseStatus::Active) {
                    throw new \InvalidArgumentException(__("{$this->backendLangPath}.update.default_deactivate_forbidden"));
                }

                if ($newType !== ProductWarehouseType::Storage) {
                    throw new \InvalidArgumentException(__("{$this->backendLangPath}.update.default_type_forbidden"));
                }
            }

            if (array_key_exists('is_default', $data)) {

                // Si l'entrepôt n'est pas par défaut, il peut devenir par défaut si le type et le statut sont corrects.
                $isDefault = (bool) $data['is_default'];

                if ($isDefault && ! $warehouse->is_default) {
                    $this->assertCanBeDefault($newType, $newStatus);

                    ProductWarehouse::where('is_default', true)->whereKeyNot($warehouse->id)->update(['is_default' => false]);
                }

                $updateData['is_default'] = $isDefault;
            }

            $warehouse->update($updateData);

            if (isset($data['translations'])) {
                $this->syncTranslations($warehouse, $data['translations']);
            }

            return $warehouse;
        });
    }

    // ─────────────────────────────────────────────
    // CHANGE STATUS
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_change_status(ProductWarehouse $warehouse, ProductWarehouseStatus $newStatus): void
    {
        DB::transaction(function () use ($warehouse, $newStatus) {

            // Un entrepôt par défaut ne peut pas être désactivé
            if ($warehouse->is_default && $newStatus !== ProductWarehouseStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.change_status.default_deactivate_forbidden"));
            }

            $warehouse->update(['status' => $newStatus]);
        });
    }

    // ─────────────────────────────────────────────
    // SET DEFAULT
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_set_default(ProductWarehouse $warehouse): void
    {
        DB::transaction(function () use ($warehouse) {

            $this->assertCanBeDefault($warehouse->type, $warehouse->status);

            // Bascule de l'ancien entrepôt par défaut
            ProductWarehouse::where('is_default', true)->whereKeyNot($warehouse->id)->update(['is_default' => false]);

            $warehouse->update(['is_default' => true]);
        });
    }

    // ─────────────────────────────────────────────
    // DESTROY (soft delete)
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_destroy(ProductWarehouse $warehouse): void
    {
        DB::transaction(function () use ($warehouse) {

            // Un entrepôt actif ne peut pas être supprimé
            if ($warehouse->status === ProductWarehouseStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.active_forbidden"));
            }

            if ($warehouse->trashed()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.already_deleted"));
            }

            // destroy_function bloque aussi si des relations dépendantes existent
            // (aucune pour l'instant — voir $dependentRelations du modèle).
            destroy_function($warehouse);
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_restore(ProductWarehouse $warehouse): void
    {
        DB::transaction(function () use ($warehouse) {
            if ($warehouse->trashed()) {
                // restore_function restaure l'entrepôt ET ses traductions
                restore_function($warehouse);
            } else {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.restore.not_trashed_forbidden"));
            }
        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_warehouses_force_destroy(ProductWarehouse $warehouse): void
    {
        DB::transaction(function () use ($warehouse) {
            if ($warehouse->trashed()) {

                if ($warehouse->status === ProductWarehouseStatus::Active) {
                    throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.active_forbidden"));
                }

                force_destroy_function($warehouse);
            } else {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.not_trashed_forbidden"));
            }
        });
    }

    // ─────────────────────────────────────────────
    // ASSERT CAN BE DEFAULT (privé)
    // ─────────────────────────────────────────────
    private function assertCanBeDefault(ProductWarehouseType $type, ProductWarehouseStatus $status): void
    {
        // Si l'entrepôt n'est pas de type "Storage" ou n'est pas actif, il ne peut pas être
        // défini comme par défaut.
        if ($type !== ProductWarehouseType::Storage) {
            throw new \InvalidArgumentException(__("{$this->backendLangPath}.default.storage_required"));
        }

        if ($status !== ProductWarehouseStatus::Active) {
            throw new \InvalidArgumentException(__("{$this->backendLangPath}.default.active_required"));
        }
    }

    // ─────────────────────────────────────────────
    // SYNC TRANSLATIONS (privé)
    // ─────────────────────────────────────────────
    private function syncTranslations(ProductWarehouse $warehouse, array $translations): void
    {
        // Récupère les IDs des langues soumises
        $submittedLangIds = collect($translations)->pluck('lang_id')->toArray();

        // Supprime temporairement les traductions qui ne sont pas présentes dans les données soumises
        ProductWarehouseTranslation::where('warehouse_id', $warehouse->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->delete();
        // Suppression définitive des traductions supprimées (soft delete) qui sont en corbeille, supprimer précédemment
        ProductWarehouseTranslation::onlyTrashed()->where('warehouse_id', $warehouse->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->forceDelete();

        // Validation générique factorisée (BackendHelper) :
        //  - conflit nom/slug avec un autre entrepôt (actif ou en corbeille) dans la même langue ;
        //  - conflit avec la propre traduction de l'entrepôt, placée en corbeille.
        assertTranslationUniqueness(
            $warehouse,
            $translations,
            __('v1/backend/others/helpers/backend-helper-messages.entityLabels.warehouse'),
            $this->backendHelperLangPath,
            'name',
            'slug'
        );

        foreach ($translations as $translation) {

            $existingTranslation = ProductWarehouseTranslation::withTrashed()->where('warehouse_id', $warehouse->id)->where('lang_id', $translation['lang_id'])->first();

            // Si une traduction existe déjà pour cette langue, on la met à jour. Sinon, on la crée.
            if ($existingTranslation) {

                // Une traduction en corbeille ne doit pas être modifiée silencieusement
                if ($existingTranslation->trashed()) {
                    throw new \InvalidArgumentException(
                        __("{$this->backendLangPath}.trashedTranslationConflict", [
                            'lang_id' => $translation['lang_id'],
                        ])
                    );
                }

                $existingTranslation->update([
                    'name' => $translation['name'],
                    'description' => $translation['description'],
                ]);

            } else {

                ProductWarehouseTranslation::create([
                    'warehouse_id' => $warehouse->id,
                    'lang_id' => $translation['lang_id'],
                    'name' => $translation['name'],
                    'description' => $translation['description'],
                ]);
            }
        }
    }
}
