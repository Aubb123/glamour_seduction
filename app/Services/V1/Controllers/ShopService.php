<?php

namespace App\Services\V1\Controllers;

use App\Enums\Shops\Shops\ShopStatus;
use App\Models\ProductWarehouse;
use App\Models\Shop;
use App\Models\ShopTranslation;
use Illuminate\Support\Facades\DB;

use function __;

class ShopService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/shops/shops/messages/shops-messages-service.shops';

    // Chemin des messages génériques du backend helper (factorisés pour toutes les entités traduisibles)
    protected string $backendHelperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_shops_store(array $data): Shop
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        return DB::transaction(function () use ($data) {

            // La première boutique créée devient automatiquement la boutique par défaut
            $hasShops = Shop::count() > 0;
            $isDefault = ! $hasShops || ($data['is_default'] ?? null) === '1' || ($data['is_default'] ?? false) === true;

            // Création de la boutique (sauf les traductions et les entrepôts)
            $shop = Shop::create([
                'code' => $data['code'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'status' => ($data['status'] ?? null) === ShopStatus::Active->value ? ShopStatus::Active : ShopStatus::Inactive,
                'is_default' => $isDefault,
                'sort_order' => $data['sort_order'] ?? 0,
            ]);

            // Gère l'unicité de la boutique par défaut (une seule)
            if ($isDefault) {
                $this->unsetOtherDefaults($shop);
            }

            // Synchronisation des traductions (création)
            $this->syncTranslations($shop, $data['translations'] ?? []);

            // Synchronisation des entrepôts (relation obligatoire : au moins un entrepôt)
            $this->syncWarehouses($shop, $data['warehouses'] ?? [], $data['primary_warehouse_uuid'] ?? null);

            return $shop; // ← retourne la boutique créée avec ses traductions et entrepôts
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_shops_update(Shop $shop, array $data): Shop
    {
        return DB::transaction(function () use ($shop, $data) {

            $updateData = [];

            if (isset($data['code'])) {
                $updateData['code'] = $data['code'];
            }

            if (array_key_exists('email', $data)) {
                $updateData['email'] = $data['email'] ?: null;
            }

            if (array_key_exists('phone', $data)) {
                $updateData['phone'] = $data['phone'] ?: null;
            }

            if (array_key_exists('address', $data)) {
                $updateData['address'] = $data['address'] ?: null;
            }

            if (array_key_exists('city', $data)) {
                $updateData['city'] = $data['city'] ?: null;
            }

            if (isset($data['status'])) {
                $updateData['status'] = ($data['status'] ?? null) === ShopStatus::Active->value ? ShopStatus::Active : ShopStatus::Inactive;
            }

            if (isset($data['sort_order'])) {
                $updateData['sort_order'] = $data['sort_order'];
            }

            // Gestion du statut « boutique par défaut ».
            // NB : on ne peut pas retirer le statut « par défaut » à la dernière boutique
            // par défaut restante (sinon la plateforme n'a plus de boutique de référence).
            if (array_key_exists('is_default', $data)) {

                $wantsDefault = ($data['is_default'] ?? null) === '1' || ($data['is_default'] ?? false) === true;

                if (! $wantsDefault && $shop->is_default && $this->isLastDefault($shop)) {
                    throw new \InvalidArgumentException(__("{$this->backendLangPath}.update.default_forbidden"));
                }

                $updateData['is_default'] = $wantsDefault;
            }

            $shop->update($updateData);

            // Gère l'unicité de la boutique par défaut (une seule)
            if ($shop->is_default) {
                $this->unsetOtherDefaults($shop);
            }

            // Synchronisation des traductions (mise à jour)
            if (isset($data['translations'])) {
                $this->syncTranslations($shop, $data['translations']);
            }

            // Synchronisation des entrepôts (si fournie)
            if (isset($data['warehouses'])) {
                $this->syncWarehouses($shop, $data['warehouses'], $data['primary_warehouse_uuid'] ?? null);
            }

            return $shop;
        });
    }

    // ─────────────────────────────────────────────
    // UNSET OTHER DEFAULTS (privé) — une seule boutique par défaut
    // ─────────────────────────────────────────────
    private function unsetOtherDefaults(Shop $shop): void
    {
        Shop::where('id', '!=', $shop->id)
            ->where('is_default', true)
            ->update(['is_default' => false]);
    }

    // ─────────────────────────────────────────────
    // IS LAST DEFAULT (privé) — vrai si c'est la seule boutique par défaut
    // ─────────────────────────────────────────────
    private function isLastDefault(Shop $shop): bool
    {
        return Shop::where('id', '!=', $shop->id)
            ->where('is_default', true)
            ->doesntExist();
    }

    // ─────────────────────────────────────────────
    // SYNC WAREHOUSES (privé) — une boutique est toujours liée à ≥1 entrepôt
    // ─────────────────────────────────────────────
    /**
     * Synchronise les entrepôts de la boutique (pivot shop_warehouse).
     *
     * Règles métier :
     *  - la liste d'entrepôts est OBLIGATOIRE et doit contenir au moins un entrepôt ;
     *  - au plus un entrepôt porte `is_primary = true` (l'entrepôt principal
     *    d'approvisionnement) ; si `primary_warehouse_uuid` n'est pas fourni (ou
     *    n'appartient pas à la liste), on reprend l'entrepôt principal existant
     *    s'il est conservé, sinon le premier de la liste.
     *
     * @param  array<int, string>  $warehouseUuids  UUIDs des entrepôts à rattacher
     */
    private function syncWarehouses(Shop $shop, array $warehouseUuids, ?string $primaryWarehouseUuid = null): void
    {
        // Normalise : UUIDs non vides, uniques
        $warehouseUuids = array_values(array_unique(array_filter($warehouseUuids)));

        // Règle métier : pas de boutique sans entrepôt
        if (empty($warehouseUuids)) {
            throw new \InvalidArgumentException(__("{$this->backendLangPath}.warehouse_required"));
        }

        // Convertit les UUIDs en identifiants de modèles (le pivot stocke des IDs)
        $warehouseIds = ProductWarehouse::whereIn('uuid', $warehouseUuids)->pluck('id')->all();

        // Vérifie que tous les entrepôts fournis existent réellement
        if (count($warehouseIds) !== count($warehouseUuids)) {
            throw new \InvalidArgumentException(__("{$this->backendLangPath}.warehouse_not_found"));
        }

        // Détermine l'entrepôt principal de la nouvelle liste
        $primaryId = null;

        if ($primaryWarehouseUuid && in_array($primaryWarehouseUuid, $warehouseUuids, true)) {
            $primaryId = ProductWarehouse::where('uuid', $primaryWarehouseUuid)->value('id');
        } else {
            // Reprend l'actuel entrepôt principal s'il est conservé dans la nouvelle liste
            $currentPrimaryId = $shop->warehouses()->wherePivot('is_primary', true)->value('product_warehouses.id');

            $primaryId = ($currentPrimaryId && in_array($currentPrimaryId, $warehouseIds, true))
                ? $currentPrimaryId
                : $warehouseIds[0];
        }

        // Construit la charge de synchronisation : [warehouseId => ['is_primary' => bool]]
        $syncData = [];

        foreach ($warehouseIds as $warehouseId) {
            $syncData[$warehouseId] = ['is_primary' => $warehouseId === $primaryId];
        }

        $shop->warehouses()->sync($syncData);
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_shops_activate(Shop $shop): void
    {
        DB::transaction(function () use ($shop) {

            if ($shop->status === ShopStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.activate.already_active"));
            }

            $shop->update(['status' => ShopStatus::Active]);
        });
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_shops_deactivate(Shop $shop): void
    {
        DB::transaction(function () use ($shop) {

            if ($shop->status === ShopStatus::Inactive) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.deactivate.already_inactive"));
            }

            $shop->update(['status' => ShopStatus::Inactive]);
        });
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_shops_destroy(Shop $shop): void
    {
        DB::transaction(function () use ($shop) {

            // Empêche la suppression d'une boutique active (règle métier : la désactiver d'abord)
            if ($shop->status === ShopStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.active_forbidden"));
            }

            // Empêche la suppression double (soft delete)
            if ($shop->trashed()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.already_deleted"));
            }

            // Suppression temporaire (soft delete) + traductions associées
            destroy_function($shop);
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_shops_restore(Shop $shop): void
    {
        DB::transaction(function () use ($shop) {

            if ($shop->trashed()) {
                restore_function($shop);
            } else {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.restore.not_trashed_forbidden"));
            }
        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_shops_force_destroy(Shop $shop): void
    {
        DB::transaction(function () use ($shop) {

            if ($shop->trashed()) {
                // Une entité active ne peut pas être supprimée définitivement
                if ($shop->status === ShopStatus::Active) {
                    throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.active_forbidden"));
                }
                force_destroy_function($shop);
            } else {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.not_trashed_forbidden"));
            }
        });
    }

    // ─────────────────────────────────────────────
    // SYNC TRANSLATIONS (privé)
    // ─────────────────────────────────────────────
    private function syncTranslations(Shop $shop, array $translations): void
    {
        // Récupère les IDs des langues soumises
        $submittedLangIds = collect($translations)->pluck('lang_id')->toArray();

        // Supprime temporairement les traductions qui ne sont pas présentes dans les données soumises
        ShopTranslation::where('shop_id', $shop->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->delete();

        // Suppression définitive des traductions en corbeille retirées précédemment
        ShopTranslation::onlyTrashed()->where('shop_id', $shop->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->forceDelete();

        // Validation générique factorisée (BackendHelper) :
        //  - conflit nom/slug avec une autre boutique (active ou en corbeille) dans la même langue ;
        //  - conflit avec la propre traduction de la boutique, placée en corbeille.
        assertTranslationUniqueness(
            $shop,
            $translations,
            __('v1/backend/others/helpers/backend-helper-messages.entityLabels.shop'),
            $this->backendHelperLangPath,
            'name',
            'slug'
        );

        // Pour chaque traduction reçue, on crée ou met à jour la traduction correspondante
        foreach ($translations as $translation) {

            // Vérifie si une traduction existe déjà pour la boutique et la langue
            $existingTranslation = ShopTranslation::withTrashed()
                ->where('shop_id', $shop->id)
                ->where('lang_id', $translation['lang_id'])
                ->first();

            if ($existingTranslation) {

                // Une traduction en corbeille ne doit pas être modifiée silencieusement
                if ($existingTranslation->trashed()) {
                    throw new \InvalidArgumentException(
                        __("{$this->backendLangPath}.trashedTranslationConflict", [
                            'lang_id' => $translation['lang_id'],
                        ])
                    );
                }

                // Met à jour la traduction existante
                $existingTranslation->update([
                    'name' => $translation['name'],
                    'description' => $translation['description'] ?? null,
                ]);

            } else {

                // Sinon, on crée une nouvelle traduction
                ShopTranslation::create([
                    'shop_id' => $shop->id,
                    'lang_id' => $translation['lang_id'],
                    'name' => $translation['name'],
                    'description' => $translation['description'] ?? null,
                ]);
            }
        }
    }
}
