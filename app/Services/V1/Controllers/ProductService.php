<?php

namespace App\Services\V1\Controllers;

use App\Enums\Products\Products\ProductStatus;
use App\Models\CloudFile;
use App\Models\Currency;
use App\Models\Product;
use App\Models\ProductTranslation;
use Illuminate\Support\Facades\DB;

use function __;

class ProductService
{
    protected string $backendLangPath = 'v1/backend/views/pages/products/products/messages/products-messages-service.products';
    protected string $backendHelperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    public function __construct(protected CloudFileService $cloudFileService) {}

    public function backend_products_store(array $data): Product
    {
        return DB::transaction(function () use ($data): Product {
            $product = Product::create([
                'category_id' => $data['category_id'],
                'brand_id' => $data['brand_id'] ?? null,
                'currency_id' => Currency::defaultCurrency()->first()?->id,
                'status' => ($data['status'] ?? null) === ProductStatus::Published->value ? ProductStatus::Published : ProductStatus::Draft,
                'is_featured' => ($data['is_featured'] ?? null) === '1' || ($data['is_featured'] ?? false) === true,
                'weight' => $data['weight'] ?? null,
                'base_price' => $data['base_price'],
                'compare_at_price' => $data['compare_at_price'] ?? null,
            ]);

            $this->syncTranslations($product, $data['translations']);
            $this->syncTags($product, $data['tags'] ?? []);
            $this->attachFiles($product, $data['files'], $data['cloud_platform_uuid']);

            return $product;
        });
    }

    public function backend_products_update(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data): Product {
            // La limite porte sur le total conservé + ajouté, pas seulement sur les fichiers de la requête.
            if (! empty($data['files'])) {
                $existingImageCount = $product->cloudFiles()->where('file_type', 'image')->count();
                if ($existingImageCount + count($data['files']) > 4) {
                    throw new \InvalidArgumentException('Un produit ne peut pas comporter plus de 4 images au total.');
                }
            }

            $updateData = [];
            if (isset($data['category_id'])) $updateData['category_id'] = $data['category_id'];
            if (array_key_exists('brand_id', $data)) $updateData['brand_id'] = $data['brand_id'] ?: null;
            if (isset($data['weight'])) $updateData['weight'] = $data['weight'];
            if (isset($data['base_price'])) $updateData['base_price'] = $data['base_price'];
            if (array_key_exists('compare_at_price', $data)) $updateData['compare_at_price'] = $data['compare_at_price'] ?: null;
            if (array_key_exists('is_featured', $data)) {
                $updateData['is_featured'] = ($data['is_featured'] ?? null) === '1' || ($data['is_featured'] ?? false) === true;
            }
            if (isset($data['status'])) $updateData['status'] = $data['status'];

            $product->update($updateData);
            if (isset($data['translations'])) $this->syncTranslations($product, $data['translations']);
            if (array_key_exists('tags', $data)) $this->syncTags($product, $data['tags'] ?? []);

            if (isset($data['files'])) {
                if (! isset($data['cloud_platform_uuid'])) {
                    throw new \InvalidArgumentException(__("{$this->backendLangPath}.update.cloud_platform_uuid_required"));
                }
                $this->attachFiles($product, $data['files'], $data['cloud_platform_uuid']);
                $this->renumberPages($product);
            }

            return $product;
        });
    }

    public function backend_products_publish(Product $product): void
    {
        DB::transaction(function () use ($product) {
            if ($product->status === ProductStatus::Published) throw new \InvalidArgumentException(__("{$this->backendLangPath}.publish.already_published"));
            $product->update(['status' => ProductStatus::Published]);
        });
    }

    public function backend_products_unpublish(Product $product): void
    {
        DB::transaction(function () use ($product) {
            if ($product->status !== ProductStatus::Published) throw new \InvalidArgumentException(__("{$this->backendLangPath}.unpublish.not_published"));
            $product->update(['status' => ProductStatus::Draft]);
        });
    }

    public function backend_products_archive(Product $product): void
    {
        DB::transaction(function () use ($product) {
            if ($product->status === ProductStatus::Archived) throw new \InvalidArgumentException(__("{$this->backendLangPath}.archive.already_archived"));
            $product->update(['status' => ProductStatus::Archived]);
        });
    }

    public function backend_products_destroy(Product $product): void
    {
        DB::transaction(function () use ($product) {
            if ($product->status === ProductStatus::Published) throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.published_forbidden"));
            if ($product->trashed()) throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.already_deleted"));
            destroy_function($product);
        });
    }

    public function backend_products_restore(Product $product): void
    {
        DB::transaction(function () use ($product) {
            if ($product->trashed()) restore_function($product);
            else throw new \InvalidArgumentException(__("{$this->backendLangPath}.restore.not_trashed_forbidden"));
        });
    }

    public function backend_products_force_destroy(Product $product): void
    {
        DB::transaction(function () use ($product) {
            if (! $product->trashed()) throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.not_trashed_forbidden"));
            if ($product->status === ProductStatus::Published) throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.active_forbidden"));
            force_destroy_function($product);
        });
    }

    private function syncTags(Product $product, array $tagIds): void
    {
        $product->tags()->sync(collect($tagIds)->filter()->unique()->values()->toArray());
    }

    private function syncTranslations(Product $product, array $translations): void
    {
        $submittedLangIds = collect($translations)->pluck('lang_id')->toArray();
        ProductTranslation::where('product_id', $product->id)->whereNotIn('lang_id', $submittedLangIds)->delete();
        ProductTranslation::onlyTrashed()->where('product_id', $product->id)->whereNotIn('lang_id', $submittedLangIds)->forceDelete();

        assertTranslationUniqueness(
            $product,
            $translations,
            __('v1/backend/others/helpers/backend-helper-messages.entityLabels.product'),
            $this->backendHelperLangPath,
            'name',
            'slug'
        );

        foreach ($translations as $translation) {
            $existingTranslation = ProductTranslation::withTrashed()
                ->where('product_id', $product->id)
                ->where('lang_id', $translation['lang_id'])
                ->first();

            if ($existingTranslation) {
                if ($existingTranslation->trashed()) {
                    throw new \InvalidArgumentException(__("{$this->backendLangPath}.trashedTranslationConflict", ['lang_id' => $translation['lang_id']]));
                }
                $existingTranslation->update([
                    'name' => $translation['name'],
                    'short_description' => $translation['short_description'] ?? null,
                    'description' => clean($translation['description'] ?? null),
                ]);
            } else {
                ProductTranslation::create([
                    'product_id' => $product->id,
                    'lang_id' => $translation['lang_id'],
                    'name' => $translation['name'],
                    'short_description' => $translation['short_description'] ?? null,
                    'description' => clean($translation['description'] ?? null),
                ]);
            }
        }
    }

    /** @param array<int, \Illuminate\Http\UploadedFile> $uploadedFiles */
    private function attachFiles(Product $product, array $uploadedFiles, string $cloudPlatformUuid): void
    {
        $this->cloudFileService->uploadEntityFiles($uploadedFiles, $cloudPlatformUuid, $product, $product->getTable(), $this->backendLangPath);
    }

    private function renumberPages(Product $product): void
    {
        $images = $product->cloudFiles()->where('file_type', 'image')->orderBy('metadata->page', 'asc')->get();
        foreach ($images as $index => $image) {
            $image->update(['metadata' => array_merge($image->metadata ?? [], ['page' => $index + 1])]);
        }
    }

    public function backend_products_destroy_file(Product $product, CloudFile $cloudFile): void
    {
        DB::transaction(function () use ($product, $cloudFile) {
            if ($cloudFile->fileable_type !== $product->getMorphClass() || $cloudFile->fileable_id !== $product->id) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy_file.not_found"));
            }
            if ($product->cloudFiles()->where('file_type', 'image')->count() <= 1) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy_file.last_image_forbidden"));
            }
            destroy_function($cloudFile);
            $this->renumberPages($product);
        });
    }
}
