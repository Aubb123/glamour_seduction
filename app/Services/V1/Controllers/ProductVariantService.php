<?php

namespace App\Services\V1\Controllers;

use App\Enums\Products\Variants\ProductVariantStatus;
use App\Models\Product;
use App\Models\ProductAttributeValue;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

            // Refuse une combinaison d'options déjà utilisée par une autre variante du produit.
            $this->ensureAttributeCombinationIsUnique($product->id, $data['attribute_value_ids'] ?? []);

            // Crée la variante avec les données déjà validées par la requête.
            // SKU et code-barres sont générés automatiquement lorsque le formulaire les laisse vides.
            $sku = trim((string) ($data['sku'] ?? ''));
            $barcode = trim((string) ($data['barcode'] ?? ''));

            $variant = ProductVariant::create([
                'product_id' => $product->id,
                'sku' => $sku !== '' ? $sku : $this->generateSku($product, $data['attribute_value_ids'] ?? []),
                'barcode' => $barcode !== '' ? $barcode : $this->generateBarcode(),
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
        // Invariant : un produit doit toujours conserver une variante par défaut.
        if (array_key_exists('is_default', $data)) {
            $wantsDefault = ($data['is_default'] ?? null) === '1'
                || ($data['is_default'] ?? null) === true;

            if (! $wantsDefault && $variant->is_default) {
                $hasOtherDefault = ProductVariant::where('product_id', $variant->product_id)
                    ->where('id', '!=', $variant->id)
                    ->where('is_default', true)
                    ->exists();

                if (! $hasOtherDefault) {
                    throw new \InvalidArgumentException(__("{$this->backendLangPath}.default.last_default_forbidden"));
                }
            }
        }

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
            throw new \InvalidArgumentException(__("{$this->backendLangPath}.images.not_owned"));
        }

        // Vérifie la limite d'images configurée en tenant compte des suppressions demandées.
        $maxVariantImages = (int) getRequiredSettingValue('max_product_variant_images', app()->getLocale());
        $newFiles = array_values(array_filter($data['files'] ?? []));
        $existingImageCount = $variant->cloudFiles()
            ->where('file_type', 'image')
            ->count();

        if ($existingImageCount - $imagesToRemove->count() + count($newFiles) > $maxVariantImages) {
            throw new \InvalidArgumentException(__("{$this->backendLangPath}.images.limit_exceeded", ['max' => $maxVariantImages]));
        }

        return DB::transaction(function () use ($variant, $data, $imagesToRemove) {

            $updateData = [];

            // Seuls les champs réellement transmis sont modifiés.
            // Un SKU laissé vide conserve la valeur existante.
            if (! empty($data['sku'])) {
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
                $this->ensureAttributeCombinationIsUnique($variant->product_id, $data['attribute_value_ids'] ?? [], $variant->id);
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
            throw new \InvalidArgumentException(__("{$this->backendLangPath}.images.cloud_platform_required"));
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

    /**
     * Refuse une combinaison de valeurs d'attributs déjà utilisée par une variante du produit.
     * Défense en profondeur : la validation des FormRequests couvre déjà ce cas côté HTTP.
     */
    private function ensureAttributeCombinationIsUnique(int $productId, array $attributeValueIds, ?int $exceptId = null): void
    {
        if (ProductVariant::attributeCombinationExists($productId, $attributeValueIds, $exceptId)) {
            throw new \InvalidArgumentException(__("{$this->backendLangPath}.attribute_values.duplicate_combination"));
        }
    }

    // ─────────────────────────────────────────────
    // GÉNÉRATION AUTOMATIQUE (SKU / CODE-BARRES)
    // ─────────────────────────────────────────────
    /**
     * Génère un SKU lisible et unique à partir du produit parent et des valeurs
     * d'attributs sélectionnées (ex. « TSHIRT-ROUGE-XL »), suffixé si nécessaire.
     */
    private function generateSku(Product $product, array $attributeValueIds): string
    {
        $parts = [$product->active_translation?->name ?? ('PRODUIT-'.$product->id)];

        $ids = array_filter(array_map('intval', $attributeValueIds));

        if ($ids !== []) {
            $labels = ProductAttributeValue::whereIn('id', $ids)
                ->get()
                ->map(fn (ProductAttributeValue $value) => $value->active_translation?->label ?? $value->value);

            $parts = array_merge($parts, $labels->all());
        }

        $base = Str::upper(Str::slug(implode(' ', array_filter($parts))));
        $base = $base !== '' ? Str::limit($base, 80, '') : 'SKU';

        // Le SKU est unique globalement, y compris vis-à-vis des variantes en corbeille.
        $candidate = $base;
        $suffix = 1;

        while (ProductVariant::withTrashed()->where('sku', $candidate)->exists()) {
            $candidate = Str::limit($base, 75, '').'-'.str_pad((string) $suffix, 3, '0', STR_PAD_LEFT);
            $suffix++;
        }

        return $candidate;
    }

    /**
     * Génère un code-barres EAN-13 unique (préfixe 20 = usage interne / circulation restreinte).
     */
    private function generateBarcode(): string
    {
        do {
            $digits = '20'.str_pad((string) random_int(0, 9999999999), 10, '0', STR_PAD_LEFT);
            $barcode = $digits.$this->ean13CheckDigit($digits);
        } while (ProductVariant::withTrashed()->where('barcode', $barcode)->exists());

        return $barcode;
    }

    /** Calcule la clé de contrôle EAN-13 des 12 premiers chiffres. */
    private function ean13CheckDigit(string $digits): string
    {
        $sum = 0;

        foreach (str_split($digits) as $index => $digit) {
            $sum += (int) $digit * ($index % 2 === 0 ? 1 : 3);
        }

        return (string) ((10 - ($sum % 10)) % 10);
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

            // Une variante par défaut ne peut pas être supprimée : il faut d'abord en désigner une autre.
            if ($variant->is_default) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.default_forbidden"));
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
