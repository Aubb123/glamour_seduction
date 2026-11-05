<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Products\Variants\StoreProductVariantRequest;
use App\Http\Requests\V1\Products\Variants\UpdateProductVariantRequest;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductVariant;
use App\Services\V1\Controllers\ProductVariantService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Alias pour les chemins de traduction
use function __;

class ProductVariantController extends Controller
{
    use LogActivity; // Trait pour le logging d'activité

    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/products/variants/messages/variants-messages-controller.productVariants';

    // Injection de dépendance du service de gestion des variantes
    public function __construct(protected ProductVariantService $productVariantService) {}

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_product_variants_index(): View
    {
        try {

            // ✅ Récupère toutes les variantes avec leur produit, triées par date de création décroissante
            $variants = ProductVariant::with(['product', 'attributeValues'])->withCount(['audits', 'activityLogs'])->orderBy('created_at', 'desc')->get();

            // Log de l'accès à la liste des variantes
            $this->logActivity(type: 'info', action: 'backend.product-variants.index', context: ['count' => $variants->count()], message: null, activity: __("{$this->backendLangPath}.index.success"), is_success: true);

            // Affiche la vue avec les variantes récupérées
            return view('v1.backend.pages.products.variants.index', compact('variants'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur avec le message d'exception
            $this->logActivity(type: 'error', action: 'backend.product-variants.index', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.index.error"), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération des variantes
        }
    }

    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────
    // NB : la création d'une variante se fait TOUJOURS depuis la fiche du produit
    // (route liée à {product}) — le produit parent est fourni par l'URL, jamais choisi dans un select.
    public function backend_product_variants_create(Product $product): View
    {
        try {

            // Attributs (et leurs valeurs) pour les cases à cocher du formulaire
            $attributes = ProductAttribute::withActiveTranslation()->orderBy('sort_order', 'desc')->with(['values' => fn ($q) => $q->withActiveTranslation()])->get();

            // Log de l'accès à la page de création d'une variante
            $this->logActivity(type: 'info', action: 'backend.product-variants.create', context: ['product_uuid' => $product->uuid], message: null, activity: __("{$this->backendLangPath}.create.success"), is_success: true, loggable: $product);

            // Affiche la vue de création d'une variante, dans le contexte du produit parent
            return view('v1.backend.pages.products.variants.create', compact('product', 'attributes'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de l'accès à la page de création d'une variante
            $this->logActivity(type: 'error', action: 'backend.product-variants.create', context: ['product_uuid' => $product->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.create.error"), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de l'accès à la page de création
        }
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    // NB : le produit parent vient de la route (jamais du formulaire) — impossible de créer
    // une variante pour un autre produit que celui affiché sur la fiche d'origine.
    public function backend_product_variants_store(StoreProductVariantRequest $request, Product $product): RedirectResponse
    {
        try {

            // Création de la variante via le service : le produit parent est injecté depuis la route
            $variant = $this->productVariantService->backend_product_variants_store(array_merge($request->validated(), ['product_id' => $product->id]));

            // Log de la création de la variante avec le SKU de la variante créée
            $this->logActivity(type: 'info', action: 'backend.product-variants.store', context: ['variant_uuid' => $variant->uuid, 'variant_sku' => $variant->sku, 'product_uuid' => $product->uuid], message: null, activity: __("{$this->backendLangPath}.store.success", ['sku' => $variant->sku]), is_success: true, loggable: $variant);

            // Redirection vers la fiche du produit parent (onglet Variantes) avec un message de succès
            return redirect()->route('backend.products.show', ['product' => $product->uuid, 'slug' => $product->active_translation?->slug ?? 'null'])->with('success', __("{$this->backendLangPath}.store.flash_success", ['sku' => $variant->sku]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la création de la variante
            $this->logActivity(type: 'warning', action: 'backend.product-variants.store', context: ['product_uuid' => $product->uuid, 'data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            // Redirection avec le message métier explicite
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la création de la variante
            $this->logActivity(type: 'error', action: 'backend.product-variants.store', context: ['product_uuid' => $product->uuid, 'data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            // Redirection avec un message d'erreur en cas de problème, en conservant les données du formulaire
            return redirect()->back()->with('error', __("{$this->backendLangPath}.store.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function backend_product_variants_show(ProductVariant $productVariant): View
    {
        try {

            // Charge les compteurs (withCount)
            $productVariant->loadCount(['audits', 'activityLogs']);

            // Log de la consultation des détails d'une variante
            $this->logActivity(type: 'info', action: 'backend.product-variants.show', context: ['variant_uuid' => $productVariant->uuid, 'variant_sku' => $productVariant->sku], message: null, activity: __("{$this->backendLangPath}.show.success", ['sku' => $productVariant->sku]), is_success: true, loggable: $productVariant);

            $variant = $productVariant;

            // Retourne la vue de la variante avec les données nécessaires
            return view('v1.backend.pages.products.variants.show', compact('variant'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la consultation des détails d'une variante
            $this->logActivity(type: 'error', action: 'backend.product-variants.show', context: ['variant_uuid' => $productVariant->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.show.error", ['uuid' => $productVariant->uuid]), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération de la variante
        }
    }

    // ─────────────────────────────────────────────
    // AUDITS
    // ─────────────────────────────────────────────
    public function backend_product_variants_audits(ProductVariant $productVariant): View
    {
        try {

            // Récupère les audits de la variante
            $audits = $productVariant->audits()->orderBy('created_at', 'desc')->get();

            // Log de la consultation des audits de la variante
            $this->logActivity(type: 'info', action: 'backend.product-variants.audits', context: ['variant_uuid' => $productVariant->uuid, 'variant_sku' => $productVariant->sku], message: null, activity: __("{$this->backendLangPath}.audits.success", ['sku' => $productVariant->sku]), is_success: true, loggable: $productVariant);

            $variant = $productVariant;

            return view('v1.backend.pages.products.variants.others.audits', compact('variant', 'audits'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-variants.audits', context: ['variant_uuid' => $productVariant->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.audits.error", ['uuid' => $productVariant->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVITY LOGS
    // ─────────────────────────────────────────────
    public function backend_product_variants_activity_logs(ProductVariant $productVariant): View
    {
        try {

            // Récupère les journaux d'activité de la variante
            $logs = $productVariant->activityLogs()->orderBy('created_at', 'desc')->get();

            // Log de la consultation des journaux d'activité de la variante
            $this->logActivity(type: 'info', action: 'backend.product-variants.activity-logs', context: ['variant_uuid' => $productVariant->uuid, 'variant_sku' => $productVariant->sku], message: null, activity: __("{$this->backendLangPath}.activity-logs.success", ['sku' => $productVariant->sku]), is_success: true, loggable: $productVariant);

            $variant = $productVariant;

            return view('v1.backend.pages.products.variants.others.activity-logs', compact('variant', 'logs'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-variants.activity-logs', context: ['variant_uuid' => $productVariant->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activity-logs.error", ['uuid' => $productVariant->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────
    public function backend_product_variants_edit(ProductVariant $productVariant): View
    {
        try {

            // Attributs (et leurs valeurs) pour les cases à cocher du formulaire
            $attributes = ProductAttribute::withActiveTranslation()->orderBy('sort_order', 'desc')->with(['values' => fn ($q) => $q->withActiveTranslation()])->get();

            // Log de l'accès à la page de modification d'une variante
            $this->logActivity(type: 'info', action: 'backend.product-variants.edit', context: ['variant_uuid' => $productVariant->uuid, 'variant_sku' => $productVariant->sku], message: null, activity: __("{$this->backendLangPath}.edit.success", ['sku' => $productVariant->sku]), is_success: true, loggable: $productVariant);

            $variant = $productVariant;

            return view('v1.backend.pages.products.variants.edit', compact('variant', 'attributes'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-variants.edit', context: ['variant_uuid' => $productVariant->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.edit.error", ['uuid' => $productVariant->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_product_variants_update(UpdateProductVariantRequest $request, ProductVariant $productVariant): RedirectResponse
    {
        try {

            $variantSku = $productVariant->sku;

            $this->productVariantService->backend_product_variants_update($productVariant, $request->validated());

            $this->logActivity(type: 'info', action: 'backend.product-variants.update', context: ['variant_uuid' => $productVariant->uuid, 'variant_sku' => $variantSku], message: null, activity: __("{$this->backendLangPath}.update.success", ['sku' => $variantSku]), is_success: true, loggable: $productVariant);

            // Redirection vers la fiche du produit parent (onglet Variantes) avec un message de succès
            return redirect()->route('backend.products.show', ['product' => $productVariant->product->uuid, 'slug' => $productVariant->product->active_translation?->slug ?? 'null'])->with('success', __("{$this->backendLangPath}.update.flash_success", ['sku' => $variantSku]));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-variants.update', context: ['variant_uuid' => $productVariant->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.warning", ['uuid' => $productVariant->uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-variants.update', context: ['variant_uuid' => $productVariant->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.warning_meta", ['uuid' => $productVariant->uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-variants.update', context: ['variant_uuid' => $productVariant->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.error", ['uuid' => $productVariant->uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.update.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_product_variants_activate(Request $request, ProductVariant $productVariant): RedirectResponse
    {
        try {

            $variantSku = $productVariant->sku;

            $this->productVariantService->backend_product_variants_activate($productVariant);

            $this->logActivity(type: 'info', action: 'backend.product-variants.activate', context: ['variant_uuid' => $productVariant->uuid, 'variant_sku' => $variantSku], message: null, activity: __("{$this->backendLangPath}.activate.success", ['sku' => $variantSku]), is_success: true, loggable: $productVariant);

            return back()->with('success', __("{$this->backendLangPath}.activate.flash_success", ['sku' => $variantSku]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-variants.activate', context: ['variant_uuid' => $productVariant->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activate.warning_meta", ['uuid' => $productVariant->uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-variants.activate', context: ['variant_uuid' => $productVariant->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activate.error", ['uuid' => $productVariant->uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.activate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_product_variants_deactivate(Request $request, ProductVariant $productVariant): RedirectResponse
    {
        try {

            $variantSku = $productVariant->sku;

            $this->productVariantService->backend_product_variants_deactivate($productVariant);

            $this->logActivity(type: 'info', action: 'backend.product-variants.deactivate', context: ['variant_uuid' => $productVariant->uuid, 'variant_sku' => $variantSku], message: null, activity: __("{$this->backendLangPath}.deactivate.success", ['sku' => $variantSku]), is_success: true, loggable: $productVariant);

            return back()->with('success', __("{$this->backendLangPath}.deactivate.flash_success", ['sku' => $variantSku]));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-variants.deactivate', context: ['variant_uuid' => $productVariant->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.deactivate.warning_meta", ['uuid' => $productVariant->uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-variants.deactivate', context: ['variant_uuid' => $productVariant->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.deactivate.error", ['uuid' => $productVariant->uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.deactivate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_variants_destroy(ProductVariant $productVariant): RedirectResponse
    {
        try {

            $variantSku = $productVariant->sku;

            $this->productVariantService->backend_product_variants_destroy($productVariant);

            $this->logActivity(type: 'warning', action: 'backend.product-variants.destroy', context: ['variant_uuid' => $productVariant->uuid, 'variant_sku' => $variantSku], message: null, activity: __("{$this->backendLangPath}.destroy.success", ['sku' => $variantSku]), is_success: true, loggable: $productVariant);

            return redirect()->route('backend.product-variants.index')->with('success', __("{$this->backendLangPath}.destroy.flash_success", ['sku' => $variantSku]));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-variants.destroy', context: ['variant_uuid' => $productVariant->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning", ['uuid' => $productVariant->uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-variants.destroy', context: ['variant_uuid' => $productVariant->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning_meta", ['uuid' => $productVariant->uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-variants.destroy', context: ['variant_uuid' => $productVariant->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.error", ['uuid' => $productVariant->uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_product_variants_trashed(): View
    {
        try {

            $variants = ProductVariant::onlyTrashed()->with(['product', 'attributeValues'])->withCount(['audits', 'activityLogs'])->orderBy('deleted_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.product-variants.trashed', context: ['count' => $variants->count()], message: null, activity: __("{$this->backendLangPath}.trashed.success"), is_success: true);

            return view('v1.backend.pages.products.variants.trashed', compact('variants'));

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-variants.trashed', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.trashed.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_product_variants_restore(string $uuid): RedirectResponse
    {
        try {

            // Récupère la variante supprimée par son UUID ou échoue
            $variant = ProductVariant::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            $variantSku = $variant->sku;

            $this->productVariantService->backend_product_variants_restore($variant);

            $this->logActivity(type: 'info', action: 'backend.product-variants.restore', context: ['variant_uuid' => $variant->uuid, 'variant_sku' => $variantSku], message: null, activity: __("{$this->backendLangPath}.restore.success", ['sku' => $variantSku]), is_success: true, loggable: $variant);

            return back()->with('success', __("{$this->backendLangPath}.restore.flash_success", ['sku' => $variantSku]));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-variants.restore', context: ['variant_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-variants.restore', context: ['variant_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-variants.restore', context: ['variant_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_variants_force_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupère la variante supprimée par son UUID ou échoue
            $variant = ProductVariant::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            $variantSku = $variant->sku;

            $this->productVariantService->backend_product_variants_force_destroy($variant);

            $this->logActivity(type: 'info', action: 'backend.product-variants.force_destroy', context: ['variant_uuid' => $variant->uuid, 'variant_sku' => $variantSku], message: null, activity: __("{$this->backendLangPath}.force_destroy.success", ['sku' => $variantSku]), is_success: true, loggable: $variant);

            return back()->with('success', __("{$this->backendLangPath}.force_destroy.flash_success", ['sku' => $variantSku]));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-variants.force_destroy', context: ['variant_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            abort(404);

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.product-variants.force_destroy', context: ['variant_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.product-variants.force_destroy', context: ['variant_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_error"));
        }
    }
}
