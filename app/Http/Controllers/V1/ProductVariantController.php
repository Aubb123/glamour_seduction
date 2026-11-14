<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Products\Variants\StoreProductVariantRequest;
use App\Http\Requests\V1\Products\Variants\UpdateProductVariantRequest;
use App\Models\CloudFile;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductVariant;
use App\Services\V1\Controllers\ProductVariantService;
use App\Traits\LogActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

use function __;

class ProductVariantController extends Controller
{
    use LogActivity;

    // Chemin de base pour les traductions du contrôleur.
    protected string $backendLangPath = 'v1/backend/views/pages/products/variants/messages/variants-messages-controller.productVariants';

    // Injection du service qui porte les opérations métier des variantes.
    public function __construct(protected ProductVariantService $productVariantService) {}

    // ═══════════════════════════════════════════════════════════════
    // BACKEND
    // ═══════════════════════════════════════════════════════════════

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_product_variants_index(): View
    {
        try {
            // Charge les variantes avec leur produit, leurs attributs et les compteurs d'historique.
            $variants = ProductVariant::with(['product', 'attributeValues'])
                ->withCount(['audits', 'activityLogs'])
                ->orderByDesc('created_at')
                ->get();

            $this->logActivity(type: 'info', action: 'backend.product-variants.index', context: ['count' => $variants->count()], message: null, activity: __("{$this->backendLangPath}.index.success"), is_success: true);

            return view('v1.backend.pages.products.variants.index', compact('variants'));
        } catch (\Exception $e) {
            $this->logActivity(type: 'error', action: 'backend.product-variants.index', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.index.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────
    public function backend_product_variants_create(Request $request): View
    {
        // Le formulaire est ouvert depuis un produit : son UUID est transmis en query string.
        $product = Product::where('uuid', $request->query('product'))->firstOrFail();

        try {
            // Charge les attributs actifs et leurs valeurs traduites pour construire les options.
            $attributes = ProductAttribute::withActiveTranslation()
                ->orderBy('sort_order', 'desc')
                ->with(['values' => fn ($query) => $query->withActiveTranslation()])
                ->get();

            $this->logActivity(type: 'info', action: 'backend.product-variants.create', context: ['product_uuid' => $product->uuid], message: null, activity: __("{$this->backendLangPath}.create.success"), is_success: true, loggable: $product);

            return view('v1.backend.pages.products.variants.create', compact('product', 'attributes'));
        } catch (\Exception $e) {
            $this->logActivity(type: 'error', action: 'backend.product-variants.create', context: ['product_uuid' => $product->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.create.error"), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_product_variants_store(StoreProductVariantRequest $request): RedirectResponse
    {
        $product = Product::where('uuid', $request->query('product'))->firstOrFail();

        try {
            // Le service crée la variante, synchronise ses attributs et ajoute ses images facultatives.
            $variant = $this->productVariantService->backend_product_variants_store(array_merge($request->validated(), ['product_id' => $product->id]));

            $this->logActivity(type: 'info', action: 'backend.product-variants.store', context: ['variant_uuid' => $variant->uuid, 'variant_sku' => $variant->sku, 'product_uuid' => $product->uuid], message: null, activity: __("{$this->backendLangPath}.store.success", ['sku' => $variant->sku]), is_success: true, loggable: $variant);

            // Après création, revient à l'onglet Variantes de la fiche produit.
            return redirect()->to(route('backend.products.show', ['product' => $product->uuid, 'slug' => $product->active_translation?->slug ?? 'null']).'#tab_variants')
                ->with('success', __("{$this->backendLangPath}.store.flash_success", ['sku' => $variant->sku]));
        } catch (\InvalidArgumentException $e) {
            $this->logActivity(type: 'warning', action: 'backend.product-variants.store', context: ['product_uuid' => $product->uuid, 'data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            return redirect()->back()->with('error', $e->getMessage())->withInput();
        } catch (\Exception $e) {
            $this->logActivity(type: 'error', action: 'backend.product-variants.store', context: ['product_uuid' => $product->uuid, 'data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.store.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function backend_product_variants_show(ProductVariant $productVariant): View
    {
        try {
            // Charge les informations affichées sur la fiche et les compteurs d'historique.
            $productVariant->load(['product', 'attributeValues', 'cloudFiles'])->loadCount(['audits', 'activityLogs']);

            $this->logActivity(type: 'info', action: 'backend.product-variants.show', context: ['variant_uuid' => $productVariant->uuid, 'variant_sku' => $productVariant->sku], message: null, activity: __("{$this->backendLangPath}.show.success", ['sku' => $productVariant->sku]), is_success: true, loggable: $productVariant);

            return view('v1.backend.pages.products.variants.show', compact('productVariant'));
        } catch (\Exception $e) {
            $this->logActivity(type: 'error', action: 'backend.product-variants.show', context: ['variant_uuid' => $productVariant->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.show.error", ['uuid' => $productVariant->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // AUDITS
    // ─────────────────────────────────────────────
    public function backend_product_variants_audits(ProductVariant $productVariant): View
    {
        try {
            $productVariant->loadCount(['audits', 'activityLogs']);
            $audits = $productVariant->audits()->with(['user', 'auditable'])->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.product-variants.audits', context: ['variant_uuid' => $productVariant->uuid, 'variant_sku' => $productVariant->sku], message: null, activity: __("{$this->backendLangPath}.audits.success", ['sku' => $productVariant->sku]), is_success: true, loggable: $productVariant);

            return view('v1.backend.pages.products.variants.others.audits', compact('productVariant', 'audits'));
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
            $productVariant->loadCount(['audits', 'activityLogs']);
            $logs = $productVariant->activityLogs()->with('user')->orderBy('created_at', 'desc')->get();

            $this->logActivity(type: 'info', action: 'backend.product-variants.activity-logs', context: ['variant_uuid' => $productVariant->uuid, 'variant_sku' => $productVariant->sku], message: null, activity: __("{$this->backendLangPath}.activity-logs.success", ['sku' => $productVariant->sku]), is_success: true, loggable: $productVariant);

            return view('v1.backend.pages.products.variants.others.activity-logs', compact('productVariant', 'logs'));
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
            // Précharge le produit, les attributs sélectionnés et la galerie conservée à l'édition.
            $productVariant->load(['product', 'attributeValues', 'cloudFiles']);
            $attributes = ProductAttribute::withActiveTranslation()
                ->orderBy('sort_order', 'desc')
                ->with(['values' => fn ($query) => $query->withActiveTranslation()])
                ->get();

            $this->logActivity(type: 'info', action: 'backend.product-variants.edit', context: ['variant_uuid' => $productVariant->uuid, 'variant_sku' => $productVariant->sku], message: null, activity: __("{$this->backendLangPath}.edit.success", ['sku' => $productVariant->sku]), is_success: true, loggable: $productVariant);

            return view('v1.backend.pages.products.variants.edit', compact('productVariant', 'attributes'));
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
            // Le service applique les champs transmis, les attributs et les changements d'images.
            $this->productVariantService->backend_product_variants_update($productVariant, $request->validated());
            $changes = $productVariant->getChanges();
            $productVariant->refresh()->load(['product', 'attributeValues', 'cloudFiles']);

            $this->logActivity(type: 'info', action: 'backend.product-variants.update', context: ['variant_uuid' => $productVariant->uuid, 'variant_sku' => $productVariant->sku, 'changes' => $changes], message: null, activity: __("{$this->backendLangPath}.update.success", ['sku' => $productVariant->sku]), is_success: true, loggable: $productVariant);

            return redirect()->route('backend.product-variants.show', ['productVariant' => $productVariant->uuid])
                ->with('success', __("{$this->backendLangPath}.update.flash_success", ['sku' => $productVariant->sku]));
        } catch (\InvalidArgumentException $e) {
            $this->logActivity(type: 'warning', action: 'backend.product-variants.update', context: ['variant_uuid' => $productVariant->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.warning_meta", ['uuid' => $productVariant->uuid]), is_success: false);

            return redirect()->back()->with('error', $e->getMessage())->withInput();
        } catch (\Exception $e) {
            $this->logActivity(type: 'error', action: 'backend.product-variants.update', context: ['variant_uuid' => $productVariant->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.error", ['uuid' => $productVariant->uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.update.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_product_variants_activate(ProductVariant $productVariant): RedirectResponse
    {
        try {
            $this->productVariantService->backend_product_variants_activate($productVariant);
            $productVariant->refresh();

            $this->logActivity(type: 'info', action: 'backend.product-variants.activate', context: ['variant_uuid' => $productVariant->uuid, 'variant_sku' => $productVariant->sku], message: null, activity: __("{$this->backendLangPath}.activate.success", ['sku' => $productVariant->sku]), is_success: true, loggable: $productVariant);

            return redirect()->back()->with('success', __("{$this->backendLangPath}.activate.flash_success", ['sku' => $productVariant->sku]));
        } catch (\InvalidArgumentException $e) {
            $this->logActivity(type: 'warning', action: 'backend.product-variants.activate', context: ['variant_uuid' => $productVariant->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activate.warning_meta", ['uuid' => $productVariant->uuid]), is_success: false);

            return redirect()->back()->with('error', $e->getMessage())->withInput();
        } catch (\Exception $e) {
            $this->logActivity(type: 'error', action: 'backend.product-variants.activate', context: ['variant_uuid' => $productVariant->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activate.error", ['uuid' => $productVariant->uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.activate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_product_variants_deactivate(ProductVariant $productVariant): RedirectResponse
    {
        try {
            $this->productVariantService->backend_product_variants_deactivate($productVariant);
            $productVariant->refresh();

            $this->logActivity(type: 'info', action: 'backend.product-variants.deactivate', context: ['variant_uuid' => $productVariant->uuid, 'variant_sku' => $productVariant->sku], message: null, activity: __("{$this->backendLangPath}.deactivate.success", ['sku' => $productVariant->sku]), is_success: true, loggable: $productVariant);

            return redirect()->back()->with('success', __("{$this->backendLangPath}.deactivate.flash_success", ['sku' => $productVariant->sku]));
        } catch (\InvalidArgumentException $e) {
            $this->logActivity(type: 'warning', action: 'backend.product-variants.deactivate', context: ['variant_uuid' => $productVariant->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.deactivate.warning_meta", ['uuid' => $productVariant->uuid]), is_success: false);

            return redirect()->back()->with('error', $e->getMessage())->withInput();
        } catch (\Exception $e) {
            $this->logActivity(type: 'error', action: 'backend.product-variants.deactivate', context: ['variant_uuid' => $productVariant->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.deactivate.error", ['uuid' => $productVariant->uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.deactivate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY (soft delete)
    // ─────────────────────────────────────────────
    public function backend_product_variants_destroy(ProductVariant $productVariant): RedirectResponse
    {
        try {
            $this->productVariantService->backend_product_variants_destroy($productVariant);

            $this->logActivity(type: 'info', action: 'backend.product-variants.destroy', context: ['variant_uuid' => $productVariant->uuid, 'variant_sku' => $productVariant->sku], message: null, activity: __("{$this->backendLangPath}.destroy.success", ['sku' => $productVariant->sku]), is_success: true, loggable: $productVariant);

            return redirect()->route('backend.product-variants.index')->with('success', __("{$this->backendLangPath}.destroy.flash_success", ['sku' => $productVariant->sku]));
        } catch (\InvalidArgumentException $e) {
            $this->logActivity(type: 'warning', action: 'backend.product-variants.destroy', context: ['variant_uuid' => $productVariant->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.warning_meta", ['uuid' => $productVariant->uuid]), is_success: false);

            return redirect()->back()->with('error', $e->getMessage())->withInput();
        } catch (\Exception $e) {
            $this->logActivity(type: 'error', action: 'backend.product-variants.destroy', context: ['variant_uuid' => $productVariant->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy.error", ['uuid' => $productVariant->uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_product_variants_trashed(): View
    {
        try {
            $variants = ProductVariant::onlyTrashed()
                ->with(['product', 'attributeValues'])
                ->withCount(['audits', 'activityLogs'])
                ->orderBy('deleted_at', 'desc')
                ->get();

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
            // La corbeille utilise l'UUID pour retrouver uniquement la variante supprimée.
            $variant = ProductVariant::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
            $this->productVariantService->backend_product_variants_restore($variant);

            $this->logActivity(type: 'info', action: 'backend.product-variants.restore', context: ['variant_uuid' => $variant->uuid, 'variant_sku' => $variant->sku], message: null, activity: __("{$this->backendLangPath}.restore.success", ['sku' => $variant->sku]), is_success: true, loggable: $variant);

            return redirect()->back()->with('success', __("{$this->backendLangPath}.restore.flash_success", ['sku' => $variant->sku]));
        } catch (\InvalidArgumentException $e) {
            $this->logActivity(type: 'warning', action: 'backend.product-variants.restore', context: ['variant_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            return redirect()->back()->with('error', $e->getMessage())->withInput();
        } catch (\Exception $e) {
            $this->logActivity(type: 'error', action: 'backend.product-variants.restore', context: ['variant_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_product_variants_force_destroy(string $uuid): RedirectResponse
    {
        try {
            $variant = ProductVariant::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
            $this->productVariantService->backend_product_variants_force_destroy($variant);

            $this->logActivity(type: 'info', action: 'backend.product-variants.force_destroy', context: ['variant_uuid' => $uuid, 'variant_sku' => $variant->sku], message: null, activity: __("{$this->backendLangPath}.force_destroy.success", ['sku' => $variant->sku]), is_success: true, loggable: $variant);

            return redirect()->back()->with('success', __("{$this->backendLangPath}.force_destroy.flash_success", ['sku' => $variant->sku]));
        } catch (\InvalidArgumentException $e) {
            $this->logActivity(type: 'warning', action: 'backend.product-variants.force_destroy', context: ['variant_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return redirect()->back()->with('error', $e->getMessage())->withInput();
        } catch (\Exception $e) {
            $this->logActivity(type: 'error', action: 'backend.product-variants.force_destroy', context: ['variant_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.force_destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY FILE
    // ─────────────────────────────────────────────
    public function backend_product_variants_destroy_file(ProductVariant $productVariant, CloudFile $cloudFile): RedirectResponse
    {
        try {
            // Refuse toute suppression si le fichier n'est pas une image rattachée à cette variante.
            if ($cloudFile->fileable_type !== $productVariant->getMorphClass() || (int) $cloudFile->fileable_id !== (int) $productVariant->id || $cloudFile->file_type !== 'image') {
                throw new \InvalidArgumentException(__('The selected image does not belong to this variant.'));
            }

            destroy_function($cloudFile);

            $this->logActivity(type: 'info', action: 'backend.product-variants.destroy_file', context: ['variant_uuid' => $productVariant->uuid, 'variant_sku' => $productVariant->sku, 'cloud_file_uuid' => $cloudFile->uuid], message: null, activity: __("{$this->backendLangPath}.destroy_file.success", ['sku' => $productVariant->sku]), is_success: true, loggable: $productVariant);

            return redirect()->back()->with('success', __("{$this->backendLangPath}.destroy_file.flash_success"));
        } catch (\InvalidArgumentException $e) {
            $this->logActivity(type: 'warning', action: 'backend.product-variants.destroy_file', context: ['variant_uuid' => $productVariant->uuid, 'cloud_file_uuid' => $cloudFile->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy_file.error"), is_success: false);

            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            $this->logActivity(type: 'error', action: 'backend.product-variants.destroy_file', context: ['variant_uuid' => $productVariant->uuid, 'cloud_file_uuid' => $cloudFile->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy_file.error"), is_success: false);

            return redirect()->back()->with('error', __("{$this->backendLangPath}.destroy_file.flash_error"));
        }
    }
}
