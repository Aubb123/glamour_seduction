<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Products\Products\StoreProductRequest;
use App\Http\Requests\V1\Products\Products\UpdateProductRequest;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\ProductTag;
use App\Models\ProductTranslation;
use App\Services\V1\Controllers\ProductService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Alias pour les chemins de traduction
use function __;

class ProductController extends Controller
{
    use LogActivity; // Trait pour le logging d'activité

    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/products/products/messages/products-messages-controller.products';

    // Injection de dépendance du service de gestion des produits
    public function __construct(protected ProductService $productService) {}

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_products_index(): View
    {
        try {

            // ✅ Récupère tous les produits avec leurs traductions, catégorie, marque et tags, triés par date de création décroissante
            $products = Product::withActiveTranslation()->with(['category', 'brand', 'tags'])->withCount(['audits', 'activityLogs'])->orderBy('created_at', 'desc')->get();

            // Log de l'accès à la liste des produits
            $this->logActivity(type: 'info', action: 'backend.products.index', context: ['count' => $products->count()], message: null, activity: __("{$this->backendLangPath}.index.success"), is_success: true);

            // Affiche la vue avec les produits récupérés
            return view('v1.backend.pages.products.products.index', compact('products'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur avec le message d'exception
            $this->logActivity(type: 'error', action: 'backend.products.index', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.index.error"), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération des produits
        }
    }

    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────
    public function backend_products_create(): View
    {
        try {

            $langs = getActiveLangs()['langs']; // Récupère les langues actives pour les passer à la vue

            // Données pour les selects du formulaire
            $categories = ProductCategory::withActiveTranslation()->orderBy('sort_order', 'desc')->get();
            $brands = ProductBrand::withActiveTranslation()->orderBy('sort_order', 'desc')->get();
            $tags = ProductTag::withActiveTranslation()->orderBy('sort_order', 'desc')->get();

            // Log de l'accès à la page de création d'un produit
            $this->logActivity(type: 'info', action: 'backend.products.create', context: [], message: null, activity: __("{$this->backendLangPath}.create.success"), is_success: true);

            // Affiche la vue de création d'un produit
            return view('v1.backend.pages.products.products.create', compact('langs', 'categories', 'brands', 'tags'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de l'accès à la page de création d'un produit
            $this->logActivity(type: 'error', action: 'backend.products.create', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.create.error"), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de l'accès à la page de création
        }
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_products_store(StoreProductRequest $request): RedirectResponse
    {
        try {

            // Création du produit via le service, en passant les données validées du formulaire
            $product = $this->productService->backend_products_store($request->validated());

            // Log de la création du produit avec le nom du produit créé
            $this->logActivity(type: 'info', action: 'backend.products.store', context: ['product_uuid' => $product->uuid, 'product_name' => $product->active_translation?->name], message: null, activity: __("{$this->backendLangPath}.store.success", ['name' => $product->active_translation?->name ?? '']), is_success: true, loggable: $product);

            // Redirection vers le show du produit avec un message de succès
            return redirect()->route('backend.products.show', ['product' => $product->uuid, 'slug' => $product->active_translation?->slug ?? 'null'])->with('success', __("{$this->backendLangPath}.store.flash_success", ['name' => $product->active_translation?->name ?? '']));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la création du produit
            $this->logActivity(type: 'warning', action: 'backend.products.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            // Redirection avec le message métier explicite
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la création du produit
            $this->logActivity(type: 'error', action: 'backend.products.store', context: ['data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            // Redirection avec un message d'erreur en cas de problème, en conservant les données du formulaire
            return redirect()->back()->with('error', __("{$this->backendLangPath}.store.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // Résolution + vérification slug (helper privé)
    // ─────────────────────────────────────────────
    private function resolveProductWithSlugCheck(Product $product, string $slug, string $action, string $langKey): Product
    {
        $expectedSlug = $product->slug ?? $product->active_translation?->slug ?? 'null';

        if ($expectedSlug !== $slug) {
            // Log de l'erreur de correspondance du slug avec un message d'avertissement
            $this->logActivity(type: 'warning', action: $action, context: ['product_uuid' => $product->uuid, 'product_slug' => $slug, 'expected_slug' => $expectedSlug], message: null, activity: __("{$this->backendLangPath}.{$langKey}.slug_mismatch"), is_success: false, loggable: $product);

            abort(404);
        }

        return $product;
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function backend_products_show(Product $product, string $slug): View
    {
        try {

            $langs = getActiveLangs()['langs']; // Récupère les langues actives pour les passer à la vue

            // Vérifie que le slug correspond
            $product = $this->resolveProductWithSlugCheck($product, $slug, 'backend.products.show', 'show');

            // Charge les compteurs (withCount)
            $product->loadCount(['audits', 'activityLogs']);

            // Récupère toutes les traductions supprimées (soft deleted) de ce produit
            $productTranslations = ProductTranslation::onlyTrashed()->where('product_id', $product->id)->get();

            // Charge les variantes du produit (onglet « Variantes »)
            $variants = $product->variants()->with(['attributeValues'])->withCount(['audits', 'activityLogs'])->orderBy('created_at', 'desc')->get();

            $productName = $product->active_translation?->name ?? __("{$this->backendLangPath}.show.no_name");

            // Log de la consultation du détail d'un produit
            $this->logActivity(type: 'info', action: 'backend.products.show', context: ['product_uuid' => $product->uuid, 'product_name' => $productName], message: null, activity: __("{$this->backendLangPath}.show.success", ['name' => $productName]), is_success: true, loggable: $product);

            // Retourne la vue du produit avec les données nécessaires
            return view('v1.backend.pages.products.products.show', compact('product', 'langs', 'productTranslations', 'variants'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la consultation du détail d'un produit
            $this->logActivity(type: 'error', action: 'backend.products.show', context: ['product_uuid' => $product->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.show.error", ['uuid' => $product->uuid]), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération du produit
        }
    }

    // ─────────────────────────────────────────────
    // AUDITS
    // ─────────────────────────────────────────────
    public function backend_products_audits(Product $product, string $slug): View
    {
        try {

            $product = $this->resolveProductWithSlugCheck($product, $slug, 'backend.products.audits', 'audits');

            // Récupère les audits du produit
            $audits = $product->audits()->orderBy('created_at', 'desc')->get();

            $productName = $product->active_translation?->name ?? __("{$this->backendLangPath}.show.no_name");

            // Log de la consultation des audits du produit
            $this->logActivity(type: 'info', action: 'backend.products.audits', context: ['product_uuid' => $product->uuid, 'product_name' => $productName], message: null, activity: __("{$this->backendLangPath}.audits.success", ['name' => $productName]), is_success: true, loggable: $product);

            return view('v1.backend.pages.products.products.others.audits', compact('product', 'audits'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.products.audits', context: ['product_uuid' => $product->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.audits.error", ['uuid' => $product->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVITY LOGS
    // ─────────────────────────────────────────────
    public function backend_products_activity_logs(Product $product, string $slug): View
    {
        try {

            $product = $this->resolveProductWithSlugCheck($product, $slug, 'backend.products.activity-logs', 'activity-logs');

            // Récupère les journaux d'activité du produit
            $logs = $product->activityLogs()->orderBy('created_at', 'desc')->get();

            $productName = $product->active_translation?->name ?? __("{$this->backendLangPath}.show.no_name");

            // Log de la consultation des journaux d'activité du produit
            $this->logActivity(type: 'info', action: 'backend.products.activity-logs', context: ['product_uuid' => $product->uuid, 'product_name' => $productName], message: null, activity: __("{$this->backendLangPath}.activity-logs.success", ['name' => $productName]), is_success: true, loggable: $product);

            return view('v1.backend.pages.products.products.others.activity-logs', compact('product', 'logs'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.products.activity-logs', context: ['product_uuid' => $product->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activity-logs.error", ['uuid' => $product->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_products_update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        try {

            $productName = $product->active_translation?->name ?? '';

            $this->productService->backend_products_update($product, $request->validated());

            $this->logActivity(type: 'info', action: 'backend.products.update', context: ['product_uuid' => $product->uuid, 'product_name' => $productName], message: null, activity: __("{$this->backendLangPath}.update.success", ['name' => $productName]), is_success: true, loggable: $product);

            return redirect()->route('backend.products.show', ['product' => $product->uuid, 'slug' => $product->active_translation?->slug ?? 'null'])->with('success', __("{$this->backendLangPath}.update.flash_success", ['name' => $productName]));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.products.update', context: ['product_uuid' => $product->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.warning", ['uuid' => $product->uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.products.update', context: ['product_uuid' => $product->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.warning_meta", ['uuid' => $product->uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.products.update', context: ['product_uuid' => $product->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.error", ['uuid' => $product->uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.update.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // PUBLISH
    // ─────────────────────────────────────────────
    public function backend_products_publish(Request $request, Product $product): RedirectResponse
    {
        try {

            $productName = $product->active_translation?->name ?? '';

            $this->productService->backend_products_publish($product);

            $this->logActivity(type: 'info', action: 'backend.products.publish', context: ['product_uuid' => $product->uuid, 'product_name' => $productName], message: null, activity: __("{$this->backendLangPath}.publish.success", ['name' => $productName]), is_success: true, loggable: $product);

            return back()->with('success', __("{$this->backendLangPath}.publish.flash_success", ['name' => $productName]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.products.publish', context: ['product_uuid' => $product->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.publish.warning_meta", ['uuid' => $product->uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.products.publish', context: ['product_uuid' => $product->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.publish.error", ['uuid' => $product->uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.publish.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────
    public function backend_products_edit(Product $product): View
    {
        try {

            $langs = getActiveLangs()['langs']; // Récupère les langues actives pour les passer à la vue

            // Données pour les selects du formulaire
            $categories = ProductCategory::withActiveTranslation()->orderBy('sort_order', 'desc')->get();
            $brands = ProductBrand::withActiveTranslation()->orderBy('sort_order', 'desc')->get();
            $tags = ProductTag::withActiveTranslation()->orderBy('sort_order', 'desc')->get();

            // Log de l'accès à la page de modification d'un produit
            $this->logActivity(type: 'info', action: 'backend.products.edit', context: ['product_uuid' => $product->uuid, 'product_name' => $product->active_translation?->name], message: null, activity: __("{$this->backendLangPath}.edit.success", ['name' => $product->active_translation?->name ?? '']), is_success: true, loggable: $product);

            return view('v1.backend.pages.products.products.edit', compact('product', 'langs', 'categories', 'brands', 'tags'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.products.edit', context: ['product_uuid' => $product->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.edit.error", ['uuid' => $product->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // UNPUBLISH
    // ─────────────────────────────────────────────
    public function backend_products_unpublish(Request $request, Product $product): RedirectResponse
    {
        try {

            $productName = $product->active_translation?->name ?? '';

            $this->productService->backend_products_unpublish($product);

            $this->logActivity(type: 'info', action: 'backend.products.unpublish', context: ['product_uuid' => $product->uuid, 'product_name' => $productName], message: null, activity: __("{$this->backendLangPath}.unpublish.success", ['name' => $productName]), is_success: true, loggable: $product);

            return back()->with('success', __("{$this->backendLangPath}.unpublish.flash_success", ['name' => $productName]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.products.unpublish', context: ['product_uuid' => $product->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.unpublish.warning_meta", ['uuid' => $product->uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.products.unpublish', context: ['product_uuid' => $product->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.unpublish.error", ['uuid' => $product->uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.unpublish.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // ARCHIVE
    // ─────────────────────────────────────────────
    public function backend_products_archive(Request $request, Product $product): RedirectResponse
    {
        try {

            $productName = $product->active_translation?->name ?? '';

            $this->productService->backend_products_archive($product);

            $this->logActivity(type: 'info', action: 'backend.products.archive', context: ['product_uuid' => $product->uuid, 'product_name' => $productName], message: null, activity: __("{$this->backendLangPath}.archive.success", ['name' => $productName]), is_success: true, loggable: $product);

            return back()->with('success', __("{$this->backendLangPath}.archive.flash_success", ['name' => $productName]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.products.archive', context: ['product_uuid' => $product->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.archive.warning_meta", ['uuid' => $product->uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.products.archive', context: ['product_uuid' => $product->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.archive.error", ['uuid' => $product->uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.archive.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_products_destroy(Product $product): RedirectResponse
    {
        try {

            $productName = $product->active_translation?->name ?? '';

            // Règle métier : impossible de supprimer un produit publié (gérée par le service)
            $this->productService->backend_products_destroy($product);

            $this->logActivity(type: 'warning', action: 'backend.products.destroy', context: ['product_uuid' => $product->uuid, 'product_name' => $productName], message: null, activity: __("{$this->backendLangPath}.destroy.success", ['name' => $productName]), is_success: true, loggable: $product);

            return redirect()->route('backend.products.index')->with('success', __("{$this->backendLangPath}.destroy.flash_success", ['name' => $productName]));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.products.destroy', context: ['product_uuid' => $product->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning", ['uuid' => $product->uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.products.destroy', context: ['product_uuid' => $product->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning_meta", ['uuid' => $product->uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.products.destroy', context: ['product_uuid' => $product->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.error", ['uuid' => $product->uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_products_trashed(): View
    {
        try {

            $products = Product::onlyTrashed()->withTrashedActiveTranslation()->with(['category', 'brand', 'tags'])->withCount(['audits', 'activityLogs'])->orderBy('deleted_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.products.trashed', context: ['count' => $products->count()], message: null, activity: __("{$this->backendLangPath}.trashed.success"), is_success: true);

            return view('v1.backend.pages.products.products.trashed', compact('products'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.products.trashed', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.trashed.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_products_restore(string $uuid): RedirectResponse
    {
        try {

            // Récupère le produit supprimé par son UUID ou échoue
            $product = Product::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            $productName = $product->active_translation?->name ?? __("{$this->backendLangPath}.restore.unknown");

            // Restauration via le service
            $this->productService->backend_products_restore($product);

            $this->logActivity(type: 'info', action: 'backend.products.restore', context: ['product_uuid' => $product->uuid, 'product_name' => $productName], message: null, activity: __("{$this->backendLangPath}.restore.success", ['name' => $productName]), is_success: true, loggable: $product);

            return back()->with('success', __("{$this->backendLangPath}.restore.flash_success", ['name' => $productName]));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.products.restore', context: ['product_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.products.restore', context: ['product_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.products.restore', context: ['product_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_products_force_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupère le produit supprimé par son UUID ou échoue
            $product = Product::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            $productName = $product->active_translation?->name ?? __("{$this->backendLangPath}.force_destroy.unknown");

            // Suppression définitive via le service
            $this->productService->backend_products_force_destroy($product);

            $this->logActivity(type: 'info', action: 'backend.products.force_destroy', context: ['product_uuid' => $product->uuid, 'product_name' => $productName], message: null, activity: __("{$this->backendLangPath}.force_destroy.success", ['name' => $productName]), is_success: true, loggable: $product);

            return back()->with('success', __("{$this->backendLangPath}.force_destroy.flash_success", ['name' => $productName]));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.products.force_destroy', context: ['product_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.products.force_destroy', context: ['product_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.products.force_destroy', context: ['product_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_error"));
        }
    }
}
