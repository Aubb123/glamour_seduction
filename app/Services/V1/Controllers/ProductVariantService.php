<?php

namespace App\Services\V1\Controllers;

use App\Enums\Products\Variants\ProductVariantStatus;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

use function __;

class ProductVariantService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/products/variants/messages/variants-messages-service.productVariants';

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_product_variants_store(array $data): ProductVariant
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        return DB::transaction(function () use ($data) {

            $product = Product::findOrFail($data['product_id']);

            // La première variante créée pour un produit sans variantes devient automatiquement la variante par défaut
            $hasVariants = $product->variants()->exists();
            $isDefault = ! $hasVariants || ($data['is_default'] ?? null) === '1' || ($data['is_default'] ?? false) === true;

            // Création de la variante
            $variant = ProductVariant::create([
                'product_id' => $product->id,
                'sku' => $data['sku'],
                'barcode' => $data['barcode'] ?? null,
                'price' => $data['price'] ?? null,
                'status' => ($data['status'] ?? null) === ProductVariantStatus::Inactive->value ? ProductVariantStatus::Inactive : ProductVariantStatus::Active,
                'is_default' => $isDefault,
            ]);

            // Gère l'unicité de la variante par défaut (une seule is_default par produit)
            if ($isDefault) {
                $this->unsetOtherDefaults($variant);
            }

            // Synchronisation des valeurs d'attributs (pivot product_variant_attribute_value)
            $this->syncAttributeValues($variant, $data['attribute_value_ids'] ?? []);

            return $variant;
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_product_variants_update(ProductVariant $variant, array $data): ProductVariant
    {
        return DB::transaction(function () use ($variant, $data) {

            $updateData = [];

            if (isset($data['product_id'])) {
                $updateData['product_id'] = $data['product_id'];
            }

            if (isset($data['sku'])) {
                $updateData['sku'] = $data['sku'];
            }

            if (array_key_exists('barcode', $data)) {
                $updateData['barcode'] = $data['barcode'] ?: null;
            }

            if (array_key_exists('price', $data)) {
                $updateData['price'] = $data['price'] ?: null;
            }

            if (isset($data['status'])) {
                $updateData['status'] = ($data['status'] ?? null) === ProductVariantStatus::Inactive->value ? ProductVariantStatus::Inactive : ProductVariantStatus::Active;
            }

            if (array_key_exists('is_default', $data)) {
                $updateData['is_default'] = ($data['is_default'] ?? null) === '1' || ($data['is_default'] ?? false) === true;
            }

            $variant->update($updateData);

            // Gère l'unicité de la variante par défaut (une seule is_default par produit)
            if ($variant->is_default) {
                $this->unsetOtherDefaults($variant);
            }

            if (array_key_exists('attribute_value_ids', $data)) {
                $this->syncAttributeValues($variant, $data['attribute_value_ids'] ?? []);
            }

            return $variant;
        });
    }

    // ─────────────────────────────────────────────
    // UNSET OTHER DEFAULTS (privé) — un seul is_default par produit
    // ─────────────────────────────────────────────
    private function unsetOtherDefaults(ProductVariant $variant): void
    {
        ProductVariant::where('product_id', $variant->product_id)
            ->where('id', '!=', $variant->id)
            ->where('is_default', true)
            ->update(['is_default' => false]);
    }

    // ─────────────────────────────────────────────
    // SYNC ATTRIBUTE VALUES (privé) — pivot product_variant_attribute_value
    // ─────────────────────────────────────────────
    private function syncAttributeValues(ProductVariant $variant, array $attributeValueIds): void
    {
        // Filtre les IDs vides puis attache/détache
        $attributeValueIds = collect($attributeValueIds)->filter()->unique()->values()->toArray();

        $variant->attributeValues()->sync($attributeValueIds);
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_product_variants_activate(ProductVariant $variant): void
    {
        DB::transaction(function () use ($variant) {

            if ($variant->status === ProductVariantStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->langPath}.activate.already_active"));
            }

            $variant->update(['status' => ProductVariantStatus::Active]);
        });
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_product_variants_deactivate(ProductVariant $variant): void
    {
        DB::transaction(function () use ($variant) {

            if ($variant->status === ProductVariantStatus::Inactive) {
                throw new \InvalidArgumentException(__("{$this->langPath}.deactivate.already_inactive"));
            }

            $variant->update(['status' => ProductVariantStatus::Inactive]);
        });
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_variants_destroy(ProductVariant $variant): void
    {
        DB::transaction(function () use ($variant) {

            // Empêche la suppression d'une variante active (règle métier : la désactiver d'abord)
            if ($variant->status === ProductVariantStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.active_forbidden"));
            }

            // NB : destroy refusé si stocks/mouvements (relations ajoutées aux modules 8/9)

            // Empêche la suppression double (soft delete)
            if ($variant->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.already_deleted"));
            }

            destroy_function($variant);
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_product_variants_restore(ProductVariant $variant): void
    {
        DB::transaction(function () use ($variant) {

            if ($variant->trashed()) {
                restore_function($variant);
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.restore.not_trashed_forbidden"));
            }
        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_variants_force_destroy(ProductVariant $variant): void
    {
        DB::transaction(function () use ($variant) {

            if ($variant->trashed()) {
                force_destroy_function($variant);
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.not_trashed_forbidden"));
            }
        });
    }
}