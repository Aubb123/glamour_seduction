<?php

namespace App\Services\V1\Controllers;

use App\Enums\Products\Variants\ProductVariantStatus;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

use function __;

class ProductVariantService
{
    protected string $backendLangPath = 'v1/backend/views/pages/products/variants/messages/variants-messages-service.productVariants';

    public function __construct(protected CloudFileService $cloudFileService) {}

    public function backend_product_variants_store(array $data): ProductVariant
    {
        return DB::transaction(function () use ($data) {
            $product = Product::findOrFail($data['product_id']);
            $hasVariants = $product->variants()->exists();
            $isDefault = ! $hasVariants || ($data['is_default'] ?? null) === '1' || ($data['is_default'] ?? false) === true;

            $variant = ProductVariant::create([
                'product_id' => $product->id,
                'sku' => $data['sku'],
                'barcode' => $data['barcode'] ?? null,
                'price' => $data['price'] ?? null,
                'status' => ($data['status'] ?? null) === ProductVariantStatus::Inactive->value ? ProductVariantStatus::Inactive : ProductVariantStatus::Active,
                'is_default' => $isDefault,
            ]);

            if ($isDefault) {
                $this->unsetOtherDefaults($variant);
            }

            $this->syncAttributeValues($variant, $data['attribute_value_ids'] ?? []);
            $this->attachImages($variant, $data);

            return $variant;
        });
    }

    public function backend_product_variants_update(ProductVariant $variant, array $data): ProductVariant
    {
        $newImages = $data['files'] ?? [];
        $existingImageCount = $variant->cloudFiles()->where('file_type', 'image')->count();
        if ($existingImageCount + count($newImages) > 4) {
            throw new \InvalidArgumentException(__('Une variante ne peut pas avoir plus de 4 images. Supprimez une image avant d’en ajouter.'));
        }

        return DB::transaction(function () use ($variant, $data) {
            $updateData = [];
            if (isset($data['sku'])) $updateData['sku'] = $data['sku'];
            if (array_key_exists('barcode', $data)) $updateData['barcode'] = $data['barcode'] ?: null;
            if (array_key_exists('price', $data)) $updateData['price'] = $data['price'] ?: null;
            if (isset($data['status'])) {
                $updateData['status'] = ($data['status'] ?? null) === ProductVariantStatus::Inactive->value ? ProductVariantStatus::Inactive : ProductVariantStatus::Active;
            }
            if (array_key_exists('is_default', $data)) {
                $updateData['is_default'] = ($data['is_default'] ?? null) === '1' || ($data['is_default'] ?? false) === true;
            }

            $variant->update($updateData);
            if ($variant->is_default) $this->unsetOtherDefaults($variant);
            if (array_key_exists('attribute_value_ids', $data)) $this->syncAttributeValues($variant, $data['attribute_value_ids'] ?? []);
            $this->attachImages($variant, $data);

            return $variant;
        });
    }

    /** Upload is optional; each variant owns a separate polymorphic gallery capped at four images. */
    private function attachImages(ProductVariant $variant, array $data): void
    {
        $files = array_values(array_filter($data['files'] ?? []));
        if ($files === []) return;

        $platformUuid = $data['cloud_platform_uuid'] ?? null;
        if (! $platformUuid) {
            throw new \InvalidArgumentException(__('Sélectionnez une plateforme cloud pour téléverser les images.'));
        }

        $currentCount = $variant->cloudFiles()->where('file_type', 'image')->count();
        if ($currentCount + count($files) > 4) {
            throw new \InvalidArgumentException(__('Une variante ne peut pas avoir plus de 4 images.'));
        }

        $this->cloudFileService->uploadEntityFiles(
            $files,
            $platformUuid,
            $variant,
            'product_variants',
            $this->backendLangPath,
        );
    }

    private function unsetOtherDefaults(ProductVariant $variant): void
    {
        ProductVariant::where('product_id', $variant->product_id)
            ->where('id', '!=', $variant->id)
            ->where('is_default', true)
            ->update(['is_default' => false]);
    }

    private function syncAttributeValues(ProductVariant $variant, array $attributeValueIds): void
    {
        $attributeValueIds = collect($attributeValueIds)->filter()->unique()->values()->toArray();
        $variant->attributeValues()->sync($attributeValueIds);
    }

    public function backend_product_variants_activate(ProductVariant $variant): void
    {
        DB::transaction(function () use ($variant) {
            if ($variant->status === ProductVariantStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.activate.already_active"));
            }
            $variant->update(['status' => ProductVariantStatus::Active]);
        });
    }

    public function backend_product_variants_deactivate(ProductVariant $variant): void
    {
        DB::transaction(function () use ($variant) {
            if ($variant->status === ProductVariantStatus::Inactive) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.deactivate.already_inactive"));
            }
            $variant->update(['status' => ProductVariantStatus::Inactive]);
        });
    }

    public function backend_product_variants_destroy(ProductVariant $variant): void
    {
        DB::transaction(function () use ($variant) {
            if ($variant->status === ProductVariantStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.active_forbidden"));
            }
            if ($variant->trashed()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.already_deleted"));
            }
            destroy_function($variant);
        });
    }

    public function backend_product_variants_restore(ProductVariant $variant): void
    {
        DB::transaction(function () use ($variant) {
            if ($variant->trashed()) restore_function($variant);
            else throw new \InvalidArgumentException(__("{$this->backendLangPath}.restore.not_trashed_forbidden"));
        });
    }

    public function backend_product_variants_force_destroy(ProductVariant $variant): void
    {
        DB::transaction(function () use ($variant) {
            if ($variant->trashed()) {
                if ($variant->status === ProductVariantStatus::Active) {
                    throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.active_forbidden"));
                }
                force_destroy_function($variant);
            } else {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.not_trashed_forbidden"));
            }
        });
    }
}
