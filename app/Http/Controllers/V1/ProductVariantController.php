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
            $variants = ProductVariant::with(['product', 'attributeValues'])->withCount(['audits', 'activityLogs'])->orderByDesc('created_at')->get();
            $this->logActivity(type: 'info', action: 'backend.product-variants.index', context: ['count' => $variants->count()], message: null, activity: __("{$this->backendLangPath}.index.success"), is_success: true);
            return view('v1.backend.pages.products.variants.index', compact('variants'));
        } catch (\Exception $e) {
            $this->logActivity(type: 'error', action: 'backend.product-variants.index', context: [], message: $e->getMessage(), activity: __("{$this->backendLangPath}.index.error"), is_success: false);
            abort(500);
        }
    }

    public function backend_product_variants_create(Request $request): View
    {
        $product = Product::where('uuid', $request->query('product'))->firstOrFail();
        try {
            $attributes = ProductAttribute::withActiveTranslation()->orderBy('sort_order', 'desc')->with(['values' => fn ($query) => $query->withActiveTranslation()])->get();
            $this->logActivity(type: 'info', action: 'backend.product-variants.create', context: ['product_uuid' => $product->uuid], message: null, activity: __("{$this->backendLangPath}.create.success"), is_success: true, loggable: $product);
            return view('v1.backend.pages.products.variants.create', compact('product', 'attributes'));
        } catch (\Exception $e) {
            $this->logActivity(type: 'error', action: 'backend.product-variants.create', context: ['product_uuid' => $product->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.create.error"), is_success: false);
            abort(500);
        }
    }

    public function backend_product_variants_store(StoreProductVariantRequest $request): RedirectResponse
    {
        $product = Product::where('uuid', $request->query('product'))->firstOrFail();
        try {
            $variant = $this->productVariantService->backend_product_variants_store(array_merge($request->validated(), ['product_id' => $product->id]));
            $this->logActivity(type: 'info', action: 'backend.product-variants.store', context: ['variant_uuid' => $variant->uuid, 'variant_sku' => $variant->sku, 'product_uuid' => $product->uuid], message: null, activity: __("{$this->backendLangPath}.store.success", ['sku' => $variant->sku]), is_success: true, loggable: $variant);
            return redirect()->to(route('backend.products.show', ['product' => $product->uuid, 'slug' => $product->active_translation?->slug ?? 'null']).'#tab_variants')->with('success', __("{$this->backendLangPath}.store.flash_success", ['sku' => $variant->sku]));
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
            $this->logActivity(type: 'error', action: 'backend.product-variants.show', context: ['variant_uuid' => $productVariant->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.show.error", ['sku' => $productVariant->sku]), is_success: false);
            abort(500);
        }
    }

    public function backend_product_variants_edit(ProductVariant $productVariant): View
    {
        try {
            $attributes = ProductAttribute::withActiveTranslation()->orderBy('sort_order', 'desc')->with(['values' => fn ($query) => $query->withActiveTranslation()])->get();
            $productVariant->load(['cloudFiles.cloudFolder.platform', 'attributeValues']);
            $variant = $productVariant;
            return view('v1.backend.pages.products.variants.edit', compact('variant', 'attributes'));
        } catch (\Exception $e) {
            $this->logActivity(type: 'error', action: 'backend.product-variants.edit', context: ['variant_uuid' => $productVariant->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.edit.error", ['sku' => $productVariant->sku]), is_success: false);
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
            return redirect()->to(route('backend.products.show', ['product' => $product?->uuid, 'slug' => $product?->active_translation?->slug ?? 'null']).'#tab_variants')->with('success', __("{$this->backendLangPath}.update.flash_success", ['sku' => $variantSku]));
        } catch (\InvalidArgumentException $e) {
            $this->logActivity(type: 'warning', action: 'backend.product-variants.update', context: ['variant_uuid' => $productVariant->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.error", ['sku' => $productVariant->sku]), is_success: false);
            return back()->with('error', $e->getMessage())->withInput();
        } catch (\Exception $e) {
            $this->logActivity(type: 'error', action: 'backend.product-variants.update', context: ['variant_uuid' => $productVariant->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.update.error", ['sku' => $productVariant->sku]), is_success: false);
            return back()->with('error', __("{$this->backendLangPath}.update.flash_error", ['sku' => $productVariant->sku]))->withInput();
        }
    }

    public function backend_product_variants_destroy_file(ProductVariant $productVariant, CloudFile $cloudFile): RedirectResponse
    {
        try {
            if ($cloudFile->fileable_type !== $productVariant->getMorphClass() || (int) $cloudFile->fileable_id !== (int) $productVariant->id || $cloudFile->file_type !== 'image') {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy_file.not_found"));
            }
            destroy_function($cloudFile);
            return back()->with('success', __('Variant image removed.'));
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            $this->logActivity(type: 'error', action: 'backend.product-variants.destroy_file', context: ['variant_uuid' => $productVariant->uuid, 'cloud_file_uuid' => $cloudFile->uuid], message: $e->getMessage(), activity: __("{$this->backendLangPath}.destroy_file.error"), is_success: false);
            return back()->with('error', __("{$this->backendLangPath}.destroy_file.error"));
        }
    }

    public function backend_product_variants_activate(ProductVariant $productVariant): RedirectResponse
    {
        try {
            $this->productVariantService->backend_product_variants_activate($productVariant);
            return back()->with('success', __("{$this->backendLangPath}.activate.flash_success", ['sku' => $productVariant->sku]));
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function backend_product_variants_deactivate(ProductVariant $productVariant): RedirectResponse
    {
        try {
            $this->productVariantService->backend_product_variants_deactivate($productVariant);
            return back()->with('success', __("{$this->backendLangPath}.deactivate.flash_success", ['sku' => $productVariant->sku]));
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function backend_product_variants_destroy(ProductVariant $productVariant): RedirectResponse
    {
        try {
            $this->productVariantService->backend_product_variants_destroy($productVariant);
            return back()->with('success', __("{$this->backendLangPath}.destroy.flash_success", ['sku' => $productVariant->sku]));
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function backend_product_variants_restore(string $uuid): RedirectResponse
    {
        try {
            $productVariant = ProductVariant::withTrashed()->where('uuid', $uuid)->firstOrFail();
            $this->productVariantService->backend_product_variants_restore($productVariant);
            return back()->with('success', __("{$this->backendLangPath}.restore.flash_success", ['sku' => $productVariant->sku]));
        } catch (ModelNotFoundException $e) {
            return back()->with('error', __("{$this->backendLangPath}.not_found", ['uuid' => $uuid]));
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function backend_product_variants_trashed(): View
    {
        $variants = ProductVariant::onlyTrashed()->with('product')->withCount(['audits', 'activityLogs'])->orderByDesc('deleted_at')->get();
        return view('v1.backend.pages.products.variants.trashed', compact('variants'));
    }
}
