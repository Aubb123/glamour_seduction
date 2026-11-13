<?php

use App\Http\Controllers\V1\ActivityLogController as V1ActivityLogController;
use App\Http\Controllers\V1\AuditController as V1AuditController;
use App\Http\Controllers\V1\AuthController as V1AuthController;
use App\Http\Controllers\V1\BackendController as V1BackendController;
use App\Http\Controllers\V1\BlogCategoryController as V1BlogCategoryController;
use App\Http\Controllers\V1\BlogCategoryTranslationController as V1BlogCategoryTranslationController;
use App\Http\Controllers\V1\BlogPostCommentController as V1BlogPostCommentController;
use App\Http\Controllers\V1\BlogPostCommentReplyController as V1BlogPostCommentReplyController;
use App\Http\Controllers\V1\BlogPostController as V1BlogPostController;
use App\Http\Controllers\V1\BlogPostTranslationController as V1BlogPostTranslationController;
use App\Http\Controllers\V1\BlogTagController as V1BlogTagController;
use App\Http\Controllers\V1\BlogTagTranslationController as V1BlogTagTranslationController;
use App\Http\Controllers\V1\CloudFileController as V1CloudFileController;
use App\Http\Controllers\V1\CloudFolderController as V1CloudFolderController;
use App\Http\Controllers\V1\CloudPlatformController as V1CloudPlatformController;
use App\Http\Controllers\V1\ContactController as V1ContactController;
use App\Http\Controllers\V1\CurrencyController as V1CurrencyController;
use App\Http\Controllers\V1\CurrencyTranslationController as V1CurrencyTranslationController;
use App\Http\Controllers\V1\FaqController as V1FaqController;
use App\Http\Controllers\V1\FaqTranslationController as V1FaqTranslationController;
use App\Http\Controllers\V1\FrontendController as V1FrontendController;
use App\Http\Controllers\V1\GalleryController as V1GalleryController;
use App\Http\Controllers\V1\GalleryTranslationController as V1GalleryTranslationController;
use App\Http\Controllers\V1\GeoContinentController as V1GeoContinentController;
use App\Http\Controllers\V1\GeoContinentTranslationController as V1GeoContinentTranslationController;
use App\Http\Controllers\V1\GeoCountryController as V1GeoCountryController;
use App\Http\Controllers\V1\GeoCountryTranslationController as V1GeoCountryTranslationController;
use App\Http\Controllers\V1\LangController as V1LangController;
use App\Http\Controllers\V1\PermissionController as V1PermissionController;
use App\Http\Controllers\V1\PlatformVersionController as V1PlatformVersionController;
use App\Http\Controllers\V1\PlatformVersionTranslationController as V1PlatformVersionTranslationController;
use App\Http\Controllers\V1\PositionController as V1PositionController;
use App\Http\Controllers\V1\PositionTranslationController as V1PositionTranslationController;
use App\Http\Controllers\V1\ProductAttributeController as V1ProductAttributeController;
use App\Http\Controllers\V1\ProductAttributeTranslationController as V1ProductAttributeTranslationController;
use App\Http\Controllers\V1\ProductAttributeValueController as V1ProductAttributeValueController;
use App\Http\Controllers\V1\ProductAttributeValueTranslationController as V1ProductAttributeValueTranslationController;
use App\Http\Controllers\V1\ProductBrandController as V1ProductBrandController;
use App\Http\Controllers\V1\ProductBrandTranslationController as V1ProductBrandTranslationController;
use App\Http\Controllers\V1\ProductCategoryController as V1ProductCategoryController;
use App\Http\Controllers\V1\ProductCategoryTranslationController as V1ProductCategoryTranslationController;
use App\Http\Controllers\V1\ProductController as V1ProductController;
use App\Http\Controllers\V1\ProductStockMovementController as V1ProductStockMovementController;
use App\Http\Controllers\V1\ProductTagController as V1ProductTagController;
use App\Http\Controllers\V1\ProductTagTranslationController as V1ProductTagTranslationController;
use App\Http\Controllers\V1\ProductTranslationController as V1ProductTranslationController;
use App\Http\Controllers\V1\ProductVariantController as V1ProductVariantController;
use App\Http\Controllers\V1\ProductWarehouseController as V1ProductWarehouseController;
use App\Http\Controllers\V1\ProductWarehouseStockController as V1ProductWarehouseStockController;
use App\Http\Controllers\V1\ProductWarehouseTranslationController as V1ProductWarehouseTranslationController;
use App\Http\Controllers\V1\RoleController as V1RoleController;
use App\Http\Controllers\V1\RoleTranslationController as V1RoleTranslationController;
use App\Http\Controllers\V1\SettingController as V1SettingController;
use App\Http\Controllers\V1\ShopController as V1ShopController;
use App\Http\Controllers\V1\ShopTranslationController as V1ShopTranslationController;
use App\Http\Controllers\V1\ShopUserController as V1ShopUserController;
use App\Http\Controllers\V1\SubscribeController as V1SubscribeController;
use App\Http\Controllers\V1\SubscribeHistoryController as V1SubscribeHistoryController;
use App\Http\Controllers\V1\UserController as V1UserController;
use App\Http\Controllers\V1\WarehouseUserController as V1WarehouseUserController;
use App\Http\Controllers\V2\FrontendController as V2FrontendController;
use App\Services\V1\VersionService;
use Illuminate\Support\Facades\Route;

$version = app(VersionService::class)->getActive();
// Routes pour V1 et V2 demeurent versionnées et les définitions de modules existantes ne sont pas écrasées.
if ($version && $version->code === 'v1') {
    Route::controller(V1AuthController::class)->group(function () {
        Route::get('/', 'frontend_index')->name('frontend.index');
        Route::post('/frontend/currencies/switch', 'frontend_currencies_switch')->name('frontend.currencies.switch');
        Route::get('/login', 'login')->name('login');
        Route::post('/login', 'authenticate')->name('authenticate');
        Route::post('/logout', 'logout')->name('logout');
    });
    Route::controller(V1ProductVariantController::class)->group(function () {
        Route::delete('/backend/product-variants/{productVariant}/images/{cloudFile}', 'backend_product_variants_destroy_file')->middleware(['permission:product-variants.update'])->name('backend.product-variants.destroy_file');
    });
}
if ($version && $version->code === 'v2') {
    Route::controller(V2FrontendController::class)->group(function () {
        Route::get('/', 'frontend_index')->name('frontend.index');
    });
}
