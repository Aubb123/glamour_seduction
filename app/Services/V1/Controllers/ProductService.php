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
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/products/products/messages/products-messages-service.products';

    // Chemin des messages génériques du backend helper (factorisés pour toutes les entités traduisibles)
    protected string $backendHelperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    public function __construct(protected CloudFileService $cloudFileService) {}

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_products_store(array $data): Product
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        return DB::transaction(function () use ($data) {

            // Création du produit (sauf les traductions et les tags)
            // La devise est automatiquement associée : la devise par défaut de la plateforme
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

            // Synchronisation des traductions (création ou mise à jour)
            $this->syncTranslations($product, $data['translations']);

            // Synchronisation des tags (pivot product_tag)
            $this->syncTags($product, $data['tags'] ?? []);

            // Images principales du produit (jusqu'à 4, pattern woononvi driver_documents)
            $this->attachFiles($product, $data['files'], $data['cloud_platform_uuid']);

            return $product; // ← retourne le produit créé avec ses traductions, ses tags et ses images
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_products_update(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {

            // La limite s'applique au nombre total d'images conservées après l'ajout.
            if (! empty($data['files'])) {
                $existingImageCount = $product->cloudFiles()->where('file_type', 'image')->count();
                if ($existingImageCount + count($data['files']) > 4) {
                    throw new \InvalidArgumentException(__("{$this->backendLangPath}.update.image_limit_exceeded"));
                }
            }

            $updateData = [];

            if (isset($data['category_id'])) {
                $updateData['category_id'] = $data['category_id'];
            }

            if (array_key_exists('brand_id', $data)) {
                $updateData['brand_id'] = $data['brand_id'] ?: null;
            }

            if (isset($data['weight'])) {
                $updateData['weight'] = $data['weight'];
            }

            if (isset($data['base_price'])) {
                $updateData['base_price'] = $data['base_price'];
            }

            if (array_key_exists('compare_at_price', $data)) {
                $updateData['compare_at_price'] = $data['compare_at_price'] ?: null;
            }

            if (array_key_exists('is_featured', $data)) {
                // Convertit la valeur en booléen pour le champ is_featured
                $updateData['is_featured'] = ($data['is_featured'] ?? null) === '1' || ($data['is_featured'] ?? false) === true;
            }

            if (isset($data['status'])) {
                $updateData['status'] = $data['status'];
            }

            $product->update($updateData);

            if (isset($data['translations'])) {
                $this->syncTranslations($product, $data['translations']);
            }

            if (array_key_exists('tags', $data)) {
                $this->syncTags($product, $data['tags'] ?? []);
            }

            // Images principales du produit (jusqu'à 4, pattern woononvi driver_documents)
            // Logique purement additive : les nouvelles images s'ajoutent aux existantes.
            if (isset($data['files'])) {

                // Si des images sont fournies sans plateforme cloud, on refuse explicitement
                if (! isset($data['cloud_platform_uuid'])) {
                    throw new \InvalidArgumentException(__("{$this->backendLangPath}.update.cloud_platform_uuid_required"));
                }

                $this->attachFiles($product, $data['files'], $data['cloud_platform_uuid']);

                // Rétablit la numérotation 1..N après ajout
                $this->renumberPages($product);
            }

            return $product;
        });
    }

    // ─────────────────────────────────────────────
    // PUBLISH
    // ─────────────────────────────────────────────
    public function backend_products_publish(Product $product): void
    {
        DB::transaction(function () use ($product) {

            if ($product->status === ProductStatus::Published) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.publish.already_published"));
            }

            $product->update(['status' => ProductStatus::Published]);
        });
    }

    // ─────────────────────────────────────────────
    // UNPUBLISH
    // ─────────────────────────────────────────────
    public function backend_products_unpublish(Product $product): void
    {
        DB::transaction(function () use ($product) {

            if ($product->status !== ProductStatus::Published) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.unpublish.not_published"));
            }

            // Dépublication du produit (retour au brouillon)
            $product->update(['status' => ProductStatus::Draft]);
        });
    }

    // ─────────────────────────────────────────────
    // ARCHIVE
    // ─────────────────────────────────────────────
    public function backend_products_archive(Product $product): void
    {
        DB::transaction(function () use ($product) {

            if ($product->status === ProductStatus::Archived) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.archive.already_archived"));
            }

            $product->update(['status' => ProductStatus::Archived]);
        });
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_products_destroy(Product $product): void
    {
        DB::transaction(function () use ($product) {

            // Empêche la suppression d'un produit publié (règle métier : dépublier d'abord)
            if ($product->status === ProductStatus::Published) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.published_forbidden"));
            }

            // Empêche la suppression double (soft delete)
            if ($product->trashed()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.already_deleted"));
            }

            destroy_function($product);
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_products_restore(Product $product): void
    {
        DB::transaction(function () use ($product) {

            if ($product->trashed()) {
                restore_function($product);
            } else {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.restore.not_trashed_forbidden"));
            }
        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_products_force_destroy(Product $product): void
    {
        DB::transaction(function () use ($product) {

            if ($product->trashed()) {
                // Une entité active ne peut pas être supprimée définitivement
                if ($product->status === ProductStatus::Published) {
                    throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.active_forbidden"));
                }
                force_destroy_function($product);
            } else {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.not_trashed_forbidden"));
            }
        });
    }

    // ─────────────────────────────────────────────
    // SYNC TAGS (privé) — pivot `product_tag`
    // ─────────────────────────────────────────────
    private function syncTags(Product $product, array $tagIds): void
    {
        // Filtre les IDs vides (select « — Aucun tag — ») puis attache/détache
        $tagIds = collect($tagIds)->filter()->unique()->values()->toArray();

        $product->tags()->sync($tagIds);
    }

    // ─────────────────────────────────────────────
    // SYNC TRANSLATIONS (privé)
    // ─────────────────────────────────────────────
    private function syncTranslations(Product $product, array $translations): void
    {
        // Récupère les IDs des langues soumises
        $submittedLangIds = collect($translations)->pluck('lang_id')->toArray();

        // Supprime temporairement les traductions qui ne sont pas présentes dans les données soumises
        ProductTranslation::where('product_id', $product->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->delete();
        // Suppression définitive des traductions supprimées (soft delete) qui sont en corbeille, supprimer précédemment
        ProductTranslation::onlyTrashed()->where('product_id', $product->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->forceDelete();

        // Validation générique factorisée (BackendHelper) :
        //  - conflit nom/slug avec une autre entité (active ou en corbeille) dans la même langue ;
        //  - conflit avec la propre traduction du produit, placée en corbeille.
        // Lève une InvalidArgumentException avec un message explicite si besoin.
        assertTranslationUniqueness(
            $product,
            $translations,
            __('v1/backend/others/helpers/backend-helper-messages.entityLabels.product'),
            $this->backendHelperLangPath,
            'name',
            'slug'
        );

        // Pour chaque traduction reçue, on utilise updateOrCreate pour créer ou mettre à jour la traduction correspondante
        foreach ($translations as $translation) {

            // Vérifie si une traduction existe déjà pour le produit et la langue
            $existingTranslation = ProductTranslation::withTrashed()
                ->where('product_id', $product->id)
                ->where('lang_id', $translation['lang_id'])
                ->first();

            // Si une traduction existante est trouvée,
            if ($existingTranslation) {

                // Une traduction en corbeille ne doit pas être modifiée silencieusement
                if ($existingTranslation->trashed()) {
                    throw new \InvalidArgumentException(
                        __("{$this->backendLangPath}.trashedTranslationConflict", [
                            'lang_id' => $translation['lang_id'],
                        ])
                    );
                }

                // Le slug est généré automatiquement par le trait HasSlug (événement updating)
                // via le champ source 'name'. On ne le passe pas manuellement ici.
                $existingTranslation->update([
                    'name' => $translation['name'],
                    'short_description' => $translation['short_description'] ?? null,
                    'description' => clean($translation['description'] ?? null), // Nettoyage du contenu HTML pour éviter les scripts malveillants
                ]);

            } else {

                // Le slug est généré automatiquement par le trait HasSlug (événement creating)
                // via le champ source 'name'. On ne le passe pas manuellement ici.
                ProductTranslation::create([
                    'product_id' => $product->id,
                    'lang_id' => $translation['lang_id'],
                    'name' => $translation['name'],
                    'short_description' => $translation['short_description'] ?? null,
                    'description' => clean($translation['description'] ?? null), // Nettoyage du contenu HTML pour éviter les scripts malveillants
                ]);

            }

        }
    }

    // ─────────────────────────────────────────────
    // ATTACH FILES (ajout d'images — pattern woononvi driver_documents)
    // ─────────────────────────────────────────────
    /**
     * Attache plusieurs images au produit via CloudFileService.
     *
     * @param  array<int, UploadedFile>  $uploadedFiles  Les fichiers images uploadés
     */
    private function attachFiles(Product $product, array $uploadedFiles, string $cloudPlatformUuid): void
    {
        $this->cloudFileService->uploadEntityFiles(
            $uploadedFiles,
            $cloudPlatformUuid,
            $product,
            $product->getTable(),
            $this->backendLangPath,
        );
    }

    // ─────────────────────────────────────────────
    // RENUMBER PAGES (rétablit l'ordre 1..N des images du produit)
    // ─────────────────────────────────────────────
    private function renumberPages(Product $product): void
    {
        $images = $product->cloudFiles()
            ->where('file_type', 'image')
            ->orderBy('metadata->page', 'asc')
            ->get();

        foreach ($images as $index => $image) {
            $image->update([
                'metadata' => array_merge($image->metadata ?? [], ['page' => $index + 1]),
            ]);
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY FILE (retrait d'une image individuelle — pattern woononvi driver_documents)
    // ─────────────────────────────────────────────
    public function backend_products_destroy_file(Product $product, CloudFile $cloudFile): void
    {
        DB::transaction(function () use ($product, $cloudFile) {

            // Vérifie que le fichier cloud appartient bien à CE produit
            if ($cloudFile->fileable_type !== $product->getMorphClass() || $cloudFile->fileable_id !== $product->id) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy_file.not_found"));
            }

            // Empêche de supprimer la dernière image : au moins une image doit rester
            if ($product->cloudFiles()->where('file_type', 'image')->count() <= 1) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy_file.last_image_forbidden"));
            }

            destroy_function($cloudFile);

            // Rétablit la numérotation 1..N après retrait
            $this->renumberPages($product);
        });
    }
}
