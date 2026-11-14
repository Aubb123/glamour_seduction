<?php

namespace App\Services\V1\Controllers;

use App\Enums\Products\Variants\ProductVariantStatus;
use App\Models\CloudFile;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

use function __;

class ProductVariantService
{
    // Chemin centralisé vers les messages de traduction du service.
    protected string $backendLangPath = 'v1/backend/views/pages/products/variants/messages/variants-messages-service.productVariants';

    // Service partagé avec les autres entités pour envoyer les fichiers vers le cloud.
    public function __construct(
        protected CloudFileService $cloudFileService,
    ) {}

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_product_variants_store(array $data): ProductVariant
    {
        return DB::transaction(function () use ($data) {

            // Récupère le produit parent et détermine si cette variante doit être la valeur par défaut.
            $product = Product::findOrFail($data['product_id']);
            $hasVariants = $product->variants()->exists();
            $isDefault = ! $hasVariants
                || ($data['is_default'] ?? null) === '1'
                || ($data['is_default'] ?? false) === true;

            // Crée la variante avec les données déjà validées par la requête.
            $variant = ProductVariant::create([
                'product_id' => $product->id,
                'sku' => $data['sku'],
                'barcode' => $data['barcode'] ?? null,
                'price' => $data['price'] ?? null,
                'status' => ($data['status'] ?? null) === ProductVariantStatus::Inactive->value
                    ? ProductVariantStatus::Inactive
                    : ProductVariantStatus::Active,
                'is_default' => $isDefault,
            ]);

            // Maintient une seule variante par défaut pour ce produit.
            if ($isDefault) {
                $this->unsetOtherDefaults($variant);
            }

            // Synchronise les options choisies et ajoute les images facultatives.
            $this->syncAttributeValues($variant, $data['attribute_value_ids'] ?? []);
            $this->attachImages($variant, $data);

            return $variant;
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_product_variants_update(ProductVariant $variant, array $data): ProductVariant
    {
        // Déduplique les images demandées en suppression, puis vérifie qu'elles appartiennent à cette variante.
        $removeUuids = collect($data['remove_cloud_file_uuids'] ?? [])
            ->filter()
            ->unique()
            ->values();

        $imagesToRemove = $variant->cloudFiles()
            ->where('file_type', 'image')
            ->whereIn('uuid', $removeUuids)
            ->get();

        if ($imagesToRemove->count() !== $removeUuids->count()) {
            throw new \InvalidArgumentException(__('One or more selected images do not belong to this variant.'));
        }

        // Vérifie la limite de quatre images en tenant compte des suppressions demandées.
        $newFiles = array_values(array_filter($data['files'] ?? []));
        $existingImageCount = $variant->cloudFiles()
            ->where('file_type', 'image')
            ->count();

        if ($existingImageCount - $imagesToRemove->count() + count($newFiles) > 4) {
            throw new \InvalidArgumentException(__('A variant can have at most four images. Remove an image before adding another.'));
        }

        return DB::transaction(function () use ($variant, $data, $imagesToRemove) {

            $updateData = [];

            // Seuls les champs réellement transmis sont modifiés.
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
                $updateData['status'] = ($data['status'] ?? null) === ProductVariantStatus::Inactive->value
                    ? ProductVariantStatus::Inactive
                    : ProductVariantStatus::Active;
            }

            if (array_key_exists('is_default', $data)) {
                $updateData['is_default'] = ($data['is_default'] ?? null) === '1'
                    || ($data['is_default'] ?? false) === true;
            }

            $variant->update($updateData);

            // Répercute un éventuel changement de variante par défaut.
            if ($variant->is_default) {
                $this->unsetOtherDefaults($variant);
            }

            // Ne remplace les options que si le formulaire les a explicitement transmises.
            if (array_key_exists('attribute_value_ids', $data)) {
                $this->syncAttributeValues($variant, $data['attribute_value_ids'] ?? []);
            }

            // Supprime les images sélectionnées en utilisant le mécanisme commun de suppression logique.
            foreach ($imagesToRemove as $image) {
                if (! $image->trashed()) {
                    destroy_function($image);
                }
            }

            // Ajoute les nouveaux fichiers sans supprimer les images conservées.
            $this->attachImages($variant, $data);

            return $variant;
        });
    }

    // ─────────────────────────────────────────────
    // ATTACH IMAGES (privé)
    // ─────────────────────────────────────────────
    /** Ajoute un lot facultatif d'images, sans remplacer la galerie existante. */
    private function attachImages(ProductVariant $variant, array $data): void
    {
        $files = array_values(array_filter($data['files'] ?? []));

        if ($files === []) {
            return;
        }

        $platformUuid = $data['cloud_platform_uuid'] ?? null;

        if (! $platformUuid) {
            throw new \InvalidArgumentException(__('Select a cloud platform to upload variant images.'));
        }

        $this->cloudFileService->uploadEntityFiles(
            $files,
            $platformUuid,
            $variant,
            'product_variants',
            $this->backendLangPath,
        );
    }

    // ─────────────────────────────────────────────
    // DEFAULT VARIANT (privé)
    // ─────────────────────────────────────────────
    /** Retire le statut par défaut aux autres variantes du même produit. */
    private function unsetOtherDefaults(ProductVariant $variant): void
    {
        ProductVariant::where('product_id', $variant->product_id)
            ->where('id', '!=', $variant->id)
            ->where('is_default', true)
            ->update(['is_default' => false]);
    }

    // ─────────────────────────────────────────────
    // ATTRIBUTE VALUES (privé)
    // ─────────────────────────────────────────────
    /** Synchronise les valeurs d'attribut sélectionnées dans le formulaire. */
    private function syncAttributeValues(ProductVariant $variant, array $attributeValueIds): void
    {
        $variant->attributeValues()->sync(
            collect($attributeValueIds)
                ->filter()
                ->unique()
                ->values()
                ->toArray()
        );
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_product_variants_activate(ProductVariant $variant): void
    {
        DB::transaction(function () use ($variant) {

            if ($variant->status === ProductVariantStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.activate.already_active"));
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
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.deactivate.already_inactive"));
            }

            $variant->update(['status' => ProductVariantStatus::Inactive]);
        });
    }

    // ─────────────────────────────────────────────
    // DESTROY (soft delete)
    // ─────────────────────────────────────────────
    public function backend_product_variants_destroy(ProductVariant $variant): void
    {
        DB::transaction(function () use ($variant) {

            // Une variante active doit d'abord être désactivée.
            if ($variant->status === ProductVariantStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.active_forbidden"));
            }

            if ($variant->trashed()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.already_deleted"));
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
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.restore.not_trashed_forbidden"));
            }
        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_variants_force_destroy(ProductVariant $variant): void
    {
        DB::transaction(function () use ($variant) {

            if (! $variant->trashed()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.not_trashed_forbidden"));
            }

            // Une variante active ne doit jamais être supprimée définitivement.
            if ($variant->status === ProductVariantStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.active_forbidden"));
            }

            force_destroy_function($variant);
        });
    }
}
