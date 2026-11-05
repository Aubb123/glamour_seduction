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

use function __;

class ProductVariantController extends Controller
{
    use LogActivity;

    protected string $backendLangPath = 'v1/backend/views/pages/products/variants/messages/variants-messages-controller.productVariants';

    public function __construct(protected ProductVariantService $productVariantService) {}

    public function backend_product_variants_index(): View
    {
        try {
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

    /** Create is only reachable from a product details page. The existing route carries the UUID as a query parameter. */
    public function backend_product_variants_create(Request $request): View
    {
        $product = Product::where('uuid', $request->query('product'))->firstOrFail();

        try {
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

    /** The parent product is resolved from the create URL, never from a selectable form field. */
    public function backend_product_variants_store(StoreProductVariantRequest $request): RedirectResponse
    {
        $product = Product::where('uuid', $request->query('product'))->firstOrFail();

        try {
            $variant = $this->productVariantService->backend_product_variants_store(array_merge(
                $request->validated(),
                ['product_id' => $product->id],
            ));

            $this->logActivity(type: 'info', action: 'backend.product-variants.store', context: ['variant_uuid' => $variant->uuid, 'variant_sku' => $variant->sku, 'product_uuid' => $product->uuid], message: null, activity: __("{$this->backendLangPath}.store.success", ['sku' => $variant->sku]), is_success: true, loggable: $variant);

            return redirect()->to(route('backend.products.show', ['product' => $product->uuid, 'slug' => $product->active_translation?->slug ?? 'null']).'#tab_variants')
                ->with('success', __("{$this->backendLangPath}.store.flash_success", ['sku' => $variant->sku]));
        } catch (\InvalidArgumentException $e) {
            $this->logActivity(type: 'warning', action: 'backend.product-variants.store', context: ['product_uuid' => $product->uuid, 'data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();
        } catch (\Exception $e) {
            $this->logActivity(type: 'error', action: 'backend.product-variants.store', context: ['product_uuid' => $product->uuid, 'data' => $request->validated()], message: $e->getMessage(), activity: __("{$this->backendLangPath}.store.error"), is_success: false);

            return back()->with('error', __("{$this->backendLangPath}.store.flash_error"))->withInput();
        }
    }

    public function backend_product_variants_show(ProductVariant $productVariant): View
    {
        try {
            $productVariant->loadCount(['audits', 'activityLogs']);
            $this->logActivity(type: 'info', action: 'backend.product-variants.show', context: ['variant_uuid' => $productVariant->uuid, 'variant_sku' => $productVariant->sku], message: null, activity: __("{$this->backendLangPath}.show.success", ['sku' => $productVariant->sku]), is_success: true, loggable: $productVariant);

            $variant = $productVariant;

            return view('v1.backend.pages.products.variants.show', compact('variant'));
        } catch (\Exception $e) {
            $this->logActivity(type: 'error', action: 'backend.product-variants.show', context: ['variant_uuid' => $productVariant->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.show.error", ['uuid' => $productVariant->uuid]), is_success: false);
            abort(500);
        }
    }

    public function backend_product_variants_audits(ProductVariant $productVariant): View
    {
        try {
            $audits = $productVariant->audits()->orderByDesc('created_at')->get();
            $this->logActivity(type: 'info', action: 'backend.product-variants.audits', context: ['variant_uuid' => $productVariant->uuid, 'variant_sku' => $productVariant->sku], message: null, activity: __("{$this->backendLangPath}.audits.success", ['sku' => $productVariant->sku]), is_success: true, loggable: $productVariant);
            $variant = $productVariant;

            return view('v1.backend.pages.products.variants.others.audits', compact('variant', 'audits'));
        } catch (\Exception $e) {
            $this->logActivity(type: 'error', action: 'backend.product-variants.audits', context: ['variant_uuid' => $productVariant->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.audits.error", ['uuid' => $productVariant->uuid]), is_success: false);
            abort(500);
        }
    }

    public function backend_product_variants_activity_logs(ProductVariant $productVariant): View
    {
        try {
            $logs = $productVariant->activityLogs()->orderByDesc('created_at')->get();
            $this->logActivity(type: 'info', action: 'backend.product-variants.activity-logs', context: ['variant_uuid' => $productVariant->uuid, 'variant_sku' => $productVariant->sku], message: null, activity: __("{$this->backendLangPath}.activity-logs.success", ['sku' => $productVariant->sku]), is_success: true, loggable: $productVariant);
            $variant = $productVariant;

            return view('v1.backend.pages.products.variants.others.activity-logs', compact('variant', 'logs'));
        } catch (\Exception $e) {
            $this->logActivity(type: 'error', action: 'backend.product-variants.activity-logs', context: ['variant_uuid' => $productVariant->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.activity-logs.error", ['uuid' => $productVariant->uuid]), is_success: false);
            abort(500);
        }
    }

    public function backend_product_variants_edit(ProductVariant $productVariant): View
    {
        try {
            $attributes = ProductAttribute::withActiveTranslation()
                ->orderBy('sort_order', 'desc')
                ->with(['values' => fn ($query) => $query->withActiveTranslation()])
                ->get();
            $productVariant->loadMissing(['product']);
            $this->logActivity(type: 'info', action: 'backend.product-variants.edit', context: ['variant_uuid' => $productVariant->uuid, 'variant_sku' => $productVariant->sku], message: null, activity: __("{$this->backendLangPath}.edit.success", ['sku' => $productVariant->sku]), is_success: true, loggable: $productVariant);
            $variant = $productVariant;

            return view('v1.backend.pages.products.variants.edit', compact('variant', 'attributes'));
        } catch (\Exception $e) {
            $this->logActivity(type: 'error', action: 'backend.product-variants.edit', context: ['variant_uuid' => $productVariant->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.edit.error", ['uuid' => $productVariant->uuid]), is_success: false);
            abort(500);
        }
    }

    public function backend_product_variants_update(UpdateProductVariantRequest $request, ProductVariant $productVariant): RedirectResponse
    {
        try {
            $variantSku = $productVariant->sku;
            $this->productVariantService->backend_product_variants_update($productVariant, $request->validated());
            $product = $productVariant->product;
            $this->logActivity(type: 'info', action: 'backend.product-variants.update', context: ['variant_uuid' => $productVariant->uuid, 'variant_sku' => $variantSku], message: null, activity: __("{$this->backendLangPath}.update.success", ['sku' => $variantSku]), is_success: true, loggable: $productVariant);

            return redirect()->to(route('backend.products.show', ['product' => $product->uuid, 'slug' => $product->active_translation?->slug ?? 'null']).'#tab_variants')
                ->with('success', __("{$this->backendLangPath}.update.flash_success", ['sku' => $variantSku]));
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

    public function backend_product_variants_trashed(): View
    {
        try {
            $variants = ProductVariant::onlyTrashed()->with(['product', 'attributeValues'])->withCount(['audits', 'activityLogs'])->orderByDesc('deleted_at')->get();
            $this->logActivity(type: 'info', action: 'backend.product-variants.trashed', context: ['count' => $variants->count()], message: null, activity: __("{$this->backendLangPath}.trashed.success"), is_success: true);

            return view('v1.backend.pages.products.variants.trashed', compact('variants'));
        } catch (\Exception $e) {
            $this->logActivity(type: 'error', action: 'backend.product-variants.trashed', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.trashed.error"), is_success: false);
            abort(500);
        }
    }

    public function backend_product_variants_restore(string $uuid): RedirectResponse
    {
        try {
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

    public function backend_product_variants_force_destroy(string $uuid): RedirectResponse
    {
        try {
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
