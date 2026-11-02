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
use App\Http\Controllers\V1\ProductBrandController as V1ProductBrandController;
use App\Http\Controllers\V1\ProductBrandTranslationController as V1ProductBrandTranslationController;
use App\Http\Controllers\V1\ProductCategoryController as V1ProductCategoryController;
use App\Http\Controllers\V1\ProductCategoryTranslationController as V1ProductCategoryTranslationController;
use App\Http\Controllers\V1\ProductAttributeController as V1ProductAttributeController;
use App\Http\Controllers\V1\ProductAttributeTranslationController as V1ProductAttributeTranslationController;
use App\Http\Controllers\V1\ProductAttributeValueController as V1ProductAttributeValueController;
use App\Http\Controllers\V1\ProductAttributeValueTranslationController as V1ProductAttributeValueTranslationController;
use App\Http\Controllers\V1\ProductController as V1ProductController;
use App\Http\Controllers\V1\ProductTranslationController as V1ProductTranslationController;
use App\Http\Controllers\V1\ProductTagController as V1ProductTagController;
use App\Http\Controllers\V1\ProductTagTranslationController as V1ProductTagTranslationController;
use App\Http\Controllers\V1\ProductVariantController as V1ProductVariantController;
use App\Http\Controllers\V1\ProductWarehouseController as V1ProductWarehouseController;
use App\Http\Controllers\V1\ProductWarehouseStockController as V1ProductWarehouseStockController;
use App\Http\Controllers\V1\ProductStockMovementController as V1ProductStockMovementController;
use App\Http\Controllers\V1\RoleController as V1RoleController;
use App\Http\Controllers\V1\RoleTranslationController as V1RoleTranslationController;
use App\Http\Controllers\V1\SettingController as V1SettingController;
use App\Http\Controllers\V1\SubscribeController as V1SubscribeController;
use App\Http\Controllers\V1\SubscribeHistoryController as V1SubscribeHistoryController;
use App\Http\Controllers\V1\UserController as V1UserController;
use App\Http\Controllers\V2\FrontendController as V2FrontendController;
use App\Services\V1\VersionService;
use Illuminate\Support\Facades\Route;

$version = app(VersionService::class)->getActive();

// NB : la version "courante" exposée est celle résolue par VersionService::getActive()
// (is_active + is_published + released_at, avec priorité à la version marquée is_default).
// Plusieurs versions peuvent être marquées actives simultanément (multi-actif assumé) ;
// la version réellement servie ici est celle que getActive() renvoie.
// En production, penser à re-générer le cache des routes (route:clear + route:cache)
// après chaque bascule de version active.

// ─── Version 1 ───────────────────────────────────────────
if ($version && $version->code === 'v1') {

    Route::controller(V1AuthController::class)->prefix('auth')->group(function () {

        // -----------------------------------------------------------------------
        // Guest
        // -----------------------------------------------------------------------
        Route::middleware(['v1.web.guest'])->group(function () {
            // Connexion
            Route::get('/login', 'login')->name('login');
            Route::post('/login', 'loginStore')->name('login.store');
        });

        // -----------------------------------------------------------------------
        // Auth
        // -----------------------------------------------------------------------
        Route::middleware(['v1.web.auth'])->group(function () {
            // Déconnexion
            Route::post('/logout', 'logout')->name('logout');
        });

    });

    // -----------------------------------------------------------------------
    // Pages de statut / vérification du compte
    // (accessibles publiquement pour éviter toute boucle de redirection)
    // -----------------------------------------------------------------------
    Route::controller(V1AuthController::class)->group(function () {
        Route::get('/auth/account/disabled', 'accountDisabled')->name('account.disabled');
        Route::get('/auth/email/verification/otp', 'emailVerificationOtp')->name('email.verifie.use-otp');
        Route::get('/auth/phone/verification/otp', 'phoneVerificationOtp')->name('phone.verifie.use-otp');
    });

    // -----------------------------------------------------------------------
    // Proxy public des fichiers cloud (servi SANS authentification).
    // Hors du groupe admin afin de rester accessible au frontend public.
    // -----------------------------------------------------------------------
    Route::get('/cloud-file/{uuid}', [V1CloudFileController::class, 'show'])->name('cloud-file.show');

    // Route backend ://///////////////////////////////////////////////////////////////////////////////////////////////////////////
    // ///////////////////  Route backend ///////////////////////////////////////////////////////////////////////////////////////////
    // //////////////////////////////////////// Route backend ///////////////////////////////////////////////////////////////////////
    // //////////////////////////////////////////////////////////// Route backend ///////////////////////////////////////////////////
    // //////////////////////////////////////////////////////////////////////////////// Route backend ///////////////////////////////
    Route::middleware(['v1.web.auth', 'v1.web.check.active.user.account', 'v1.web.check.user.email', 'v1.web.check.user.phone', 'role:administrator|developer|moderator|support', 'v1.web.check.active.role', 'v1.web.check.cloud.files.sync'])->group(function () {
        Route::controller(V1BackendController::class)->group(function () {
            Route::get('/backend', 'backend_index')->name('backend.index');
            Route::get('/backend/dashboard-analytics', 'backend_analytics')->name('backend.analytics');
        });

        Route::controller(V1LangController::class)->group(function () {
            Route::get('/backend/langs/index', 'backend_langs_index')->middleware(['permission:langs.index'])->name('backend.langs.index');
            // Route supplémentaire
            Route::get('/backend/language/{lang}', 'backend_language_switch')->name('backend.language.switch');
            // Activer / Désactiver une langue
            Route::patch('/backend/langs/activate/{lang}', 'backend_langs_activate')->middleware(['permission:langs.activate'])->name('backend.langs.activate');
            Route::patch('/backend/langs/deactivate/{lang}', 'backend_langs_deactivate')->middleware(['permission:langs.deactivate'])->name('backend.langs.deactivate');
        });

        // Routes pour : Gestion des versions du site (v1, v2, etc.)
        Route::controller(V1PlatformVersionController::class)->group(function () {
            Route::get('/backend/versions/index', 'backend_versions_index')->middleware(['permission:platform-versions.index'])->name('backend.versions.index');
            Route::get('/backend/versions/create', 'backend_versions_create')->middleware(['permission:platform-versions.create'])->name('backend.versions.create');
            Route::post('/backend/versions/store', 'backend_versions_store')->middleware(['permission:platform-versions.store'])->name('backend.versions.store');
            Route::get('/backend/versions/show/{platformVersion}/{slug}', 'backend_versions_show')->middleware(['permission:platform-versions.show'])->name('backend.versions.show');
            Route::get('/backend/versions/audits/{platformVersion}/{slug}', 'backend_versions_audits')->middleware(['permission:platform-versions.audits'])->name('backend.versions.audits');
            Route::get('/backend/versions/activity-logs/{platformVersion}/{slug}', 'backend_versions_activity_logs')->middleware(['permission:platform-versions.activity_logs'])->name('backend.versions.activity-logs');
            Route::get('/backend/versions/edit/{platformVersion}', 'backend_versions_edit')->middleware(['permission:platform-versions.edit'])->name('backend.versions.edit');
            Route::put('/backend/versions/update/{platformVersion}', 'backend_versions_update')->middleware(['permission:platform-versions.update'])->name('backend.versions.update');
            Route::delete('/backend/versions/destroy/{platformVersion}', 'backend_versions_destroy')->middleware(['permission:platform-versions.destroy'])->name('backend.versions.destroy');
            Route::patch('/backend/versions/activate/{platformVersion}', 'backend_versions_activate')->middleware(['permission:platform-versions.activate'])->name('backend.versions.activate');
            Route::patch('/backend/versions/deactivate/{platformVersion}', 'backend_versions_deactivate')->middleware(['permission:platform-versions.deactivate'])->name('backend.versions.deactivate');
            Route::patch('/backend/versions/publish/{platformVersion}', 'backend_versions_publish')->middleware(['permission:platform-versions.publish'])->name('backend.versions.publish');
            Route::patch('/backend/versions/unpublish/{platformVersion}', 'backend_versions_unpublish')->middleware(['permission:platform-versions.unpublish'])->name('backend.versions.unpublish');
            Route::get('/backend/versions/trashed', 'backend_versions_trashed')->middleware(['permission:platform-versions.trashed'])->name('backend.versions.trashed');
            Route::patch('/backend/versions/restore/{uuid}', 'backend_versions_restore')->middleware(['permission:platform-versions.restore'])->name('backend.versions.restore');
            Route::delete('/backend/versions/force-destroy/{uuid}', 'backend_versions_force_destroy')->middleware(['permission:platform-versions.force_destroy'])->name('backend.versions.force_destroy');
        });
        Route::controller(V1PlatformVersionTranslationController::class)->group(function () {
            Route::delete('/backend/platform-version-translations/destroy/{platformVersionTranslation}', 'backend_platform_version_translations_destroy')->middleware(['permission:platform-version-translations.destroy'])->name('backend.platform-version-translations.destroy');
            Route::get('/backend/platform-version-translations/trashed/{platformVersion}', 'backend_platform_version_translations_trashed')->middleware(['permission:platform-version-translations.trashed'])->name('backend.platform-version-translations.trashed')->withTrashed();
            Route::patch('/backend/platform-version-translations/restore/{uuid}', 'backend_platform_version_translations_restore')->middleware(['permission:platform-version-translations.restore'])->name('backend.platform-version-translations.restore');
            Route::delete('/backend/platform-version-translations/force-destroy/{uuid}', 'backend_platform_version_translations_force_destroy')->middleware(['permission:platform-version-translations.force_destroy'])->name('backend.platform-version-translations.force_destroy');
        });

        // Routes pour : Gestion des Continents
        Route::controller(V1GeoContinentController::class)->group(function () {
            Route::get('/backend/geo-continents/index', 'backend_geocontinents_index')->middleware(['permission:geo-continents.index'])->name('backend.geocontinents.index');
            Route::get('/backend/geo-continents/show/{geoContinent}/{slug}', 'backend_geocontinents_show')->middleware(['permission:geo-continents.show'])->name('backend.geocontinents.show');
            Route::get('/backend/geo-continents/countries/{geoContinent}/{slug}', 'backend_geocontinents_countries')->middleware(['permission:geo-continents.show'])->name('backend.geocontinents.countries');
            Route::get('/backend/geo-continents/audits/{geoContinent}/{slug}', 'backend_geocontinents_audits')->middleware(['permission:geo-continents.audits'])->name('backend.geocontinents.audits');
            Route::get('/backend/geo-continents/activity-logs/{geoContinent}/{slug}', 'backend_geocontinents_activity_logs')->middleware(['permission:geo-continents.activity_logs'])->name('backend.geocontinents.activity-logs');
            Route::get('/backend/geo-continents/edit/{geoContinent}', 'backend_geocontinents_edit')->middleware(['permission:geo-continents.edit'])->name('backend.geocontinents.edit');
            Route::put('/backend/geo-continents/update/{geoContinent}', 'backend_geocontinents_update')->middleware(['permission:geo-continents.update'])->name('backend.geocontinents.update');
            Route::patch('/backend/geo-continents/activate/{geoContinent}', 'backend_geocontinents_activate')->middleware(['permission:geo-continents.activate'])->name('backend.geocontinents.activate');
            Route::patch('/backend/geo-continents/deactivate/{geoContinent}', 'backend_geocontinents_deactivate')->middleware(['permission:geo-continents.deactivate'])->name('backend.geocontinents.deactivate');
            Route::get('/backend/geo-continents/trashed', 'backend_geocontinents_trashed')->middleware(['permission:geo-continents.trashed'])->name('backend.geocontinents.trashed');
        });
        Route::controller(V1GeoContinentTranslationController::class)->group(function () {
            Route::delete('/backend/geo-continents-translations/destroy/{geoContinentTranslation}', 'backend_geocontinents_translations_destroy')->middleware(['permission:geo-continents-translations.destroy'])->name('backend.geocontinents.translation.destroy');
            Route::get('/backend/geo-continents-translations/trashed/{geoContinent}', 'backend_geocontinents_translations_trashed')->middleware(['permission:geo-continents-translations.trashed'])->name('backend.geocontinents.translation.trashed')->withTrashed();
            Route::patch('/backend/geo-continents-translations/restore/{uuid}', 'backend_geocontinents_translations_restore')->middleware(['permission:geo-continents-translations.restore'])->name('backend.geocontinents.translation.restore');
            Route::delete('/backend/geo-continents-translations/force-destroy/{uuid}', 'backend_geocontinents_translations_force_destroy')->middleware(['permission:geo-continents-translations.force_destroy'])->name('backend.geocontinents.translation.force_destroy');
        });

        // Routes pour : Gestion des Pays
        Route::controller(V1GeoCountryController::class)->group(function () {
            Route::get('/backend/geo-countries/index', 'backend_geocountries_index')->middleware(['permission:geo-countries.index'])->name('backend.geocountries.index');
            Route::get('/backend/geo-countries/show/{geoCountry}/{slug}', 'backend_geocountries_show')->middleware(['permission:geo-countries.show'])->name('backend.geocountries.show');
            Route::get('/backend/geo-countries/users/{geoCountry}/{slug}', 'backend_geocountries_users')->middleware(['permission:geo-countries.show'])->name('backend.geocountries.users');
            Route::get('/backend/geo-countries/audits/{geoCountry}/{slug}', 'backend_geocountries_audits')->middleware(['permission:geo-countries.audits'])->name('backend.geocountries.audits');
            Route::get('/backend/geo-countries/activity-logs/{geoCountry}/{slug}', 'backend_geocountries_activity_logs')->middleware(['permission:geo-countries.activity_logs'])->name('backend.geocountries.activity-logs');
            Route::get('/backend/geo-countries/edit/{geoCountry}', 'backend_geocountries_edit')->middleware(['permission:geo-countries.edit'])->name('backend.geocountries.edit');
            Route::put('/backend/geo-countries/update/{geoCountry}', 'backend_geocountries_update')->middleware(['permission:geo-countries.update'])->name('backend.geocountries.update');
            Route::patch('/backend/geo-countries/activate/{geoCountry}', 'backend_geocountries_activate')->middleware(['permission:geo-countries.activate'])->name('backend.geocountries.activate');
            Route::patch('/backend/geo-countries/deactivate/{geoCountry}', 'backend_geocountries_deactivate')->middleware(['permission:geo-countries.deactivate'])->name('backend.geocountries.deactivate');
            Route::get('/backend/geo-countries/trashed', 'backend_geocountries_trashed')->middleware(['permission:geo-countries.trashed'])->name('backend.geocountries.trashed');
        });
        Route::controller(V1GeoCountryTranslationController::class)->group(function () {
            Route::delete('/backend/geo-countries-translations/destroy/{geoCountryTranslation}', 'backend_geocountries_translations_destroy')->middleware(['permission:geo-countries-translations.destroy'])->name('backend.geocountries.translation.destroy');
            Route::get('/backend/geo-countries-translations/trashed/{geoCountry}', 'backend_geocountries_translations_trashed')->middleware(['permission:geo-countries-translations.trashed'])->name('backend.geocountries.translation.trashed')->withTrashed();
            Route::patch('/backend/geo-countries-translations/restore/{uuid}', 'backend_geocountries_translations_restore')->middleware(['permission:geo-countries-translations.restore'])->name('backend.geocountries.translation.restore');
            Route::delete('/backend/geo-countries-translations/force-destroy/{uuid}', 'backend_geocountries_translations_force_destroy')->middleware(['permission:geo-countries-translations.force_destroy'])->name('backend.geocountries.translation.force_destroy');

            // Routes pour : Gestion des Devises
            Route::controller(V1CurrencyController::class)->group(function () {
                Route::get('/backend/currencies/index', 'backend_currencies_index')->middleware(['permission:currencies.index'])->name('backend.currencies.index');
                Route::get('/backend/currencies/show/{currency}/{slug}', 'backend_currencies_show')->middleware(['permission:currencies.show'])->name('backend.currencies.show');
                Route::get('/backend/currencies/countries/{currency}/{slug}', 'backend_currencies_countries')->middleware(['permission:currencies.show'])->name('backend.currencies.countries');
                Route::get('/backend/currencies/audits/{currency}/{slug}', 'backend_currencies_audits')->middleware(['permission:currencies.audits'])->name('backend.currencies.audits');
                Route::get('/backend/currencies/activity-logs/{currency}/{slug}', 'backend_currencies_activity_logs')->middleware(['permission:currencies.activity_logs'])->name('backend.currencies.activity-logs');
                Route::get('/backend/currencies/edit/{currency}', 'backend_currencies_edit')->middleware(['permission:currencies.edit'])->name('backend.currencies.edit');
                Route::put('/backend/currencies/update/{currency}', 'backend_currencies_update')->middleware(['permission:currencies.update'])->name('backend.currencies.update');
                Route::patch('/backend/currencies/activate/{currency}', 'backend_currencies_activate')->middleware(['permission:currencies.activate'])->name('backend.currencies.activate');
                Route::patch('/backend/currencies/deactivate/{currency}', 'backend_currencies_deactivate')->middleware(['permission:currencies.deactivate'])->name('backend.currencies.deactivate');
                Route::delete('/backend/currencies/destroy/{currency}', 'backend_currencies_destroy')->middleware(['permission:currencies.destroy'])->name('backend.currencies.destroy');
                Route::get('/backend/currencies/trashed', 'backend_currencies_trashed')->middleware(['permission:currencies.trashed'])->name('backend.currencies.trashed');
                Route::patch('/backend/currencies/restore/{uuid}', 'backend_currencies_restore')->middleware(['permission:currencies.restore'])->name('backend.currencies.restore');
                Route::delete('/backend/currencies/force-destroy/{uuid}', 'backend_currencies_force_destroy')->middleware(['permission:currencies.force_destroy'])->name('backend.currencies.force_destroy');
            });
            Route::controller(V1CurrencyTranslationController::class)->group(function () {
                Route::delete('/backend/currency-translations/destroy/{currencyTranslation}', 'backend_currencies_translations_destroy')->middleware(['permission:currency-translations.destroy'])->name('backend.currencies.translation.destroy');
                Route::get('/backend/currency-translations/trashed/{currency}', 'backend_currencies_translations_trashed')->middleware(['permission:currency-translations.trashed'])->name('backend.currencies.translation.trashed')->withTrashed();
                Route::patch('/backend/currency-translations/restore/{uuid}', 'backend_currencies_translations_restore')->middleware(['permission:currency-translations.restore'])->name('backend.currencies.translation.restore');
                Route::delete('/backend/currency-translations/force-destroy/{uuid}', 'backend_currencies_translations_force_destroy')->middleware(['permission:currency-translations.force_destroy'])->name('backend.currencies.translation.force_destroy');
            });
        });

        // Routes pour : Gestion des Roles et Permissions
        Route::controller(V1RoleController::class)->group(function () {
            Route::get('/backend/roles/index', 'backend_roles_index')->middleware(['permission:roles.index'])->name('backend.roles.index');
            Route::get('/backend/roles/show/{role}/{slug}', 'backend_roles_show')->middleware(['permission:roles.show'])->name('backend.roles.show');
            Route::get('/backend/roles/permissions/{role}/{slug}', 'backend_roles_permissions')->middleware(['permission:roles.show'])->name('backend.roles.permissions');
            Route::get('/backend/roles/users/{role}/{slug}', 'backend_roles_users')->middleware(['permission:roles.show'])->name('backend.roles.users');
            Route::get('/backend/roles/audits/{role}/{slug}', 'backend_roles_audits')->middleware(['permission:roles.audits'])->name('backend.roles.audits');
            Route::get('/backend/roles/activity-logs/{role}/{slug}', 'backend_roles_activity_logs')->middleware(['permission:roles.activity_logs'])->name('backend.roles.activity-logs');
            Route::get('/backend/roles/edit/{role}', 'backend_roles_edit')->middleware(['permission:roles.edit'])->name('backend.roles.edit');
            Route::put('/backend/roles/update/{role}', 'backend_roles_update')->middleware(['permission:roles.update'])->name('backend.roles.update');
            Route::patch('/backend/roles/activate/{role}', 'backend_roles_activate')->middleware(['permission:roles.activate'])->name('backend.roles.activate');
            Route::patch('/backend/roles/deactivate/{role}', 'backend_roles_deactivate')->middleware(['permission:roles.deactivate'])->name('backend.roles.deactivate');
            Route::get('/backend/roles/trashed', 'backend_roles_trashed')->middleware(['permission:roles.trashed'])->name('backend.roles.trashed');
        });
        Route::controller(V1RoleTranslationController::class)->group(function () {
            Route::delete('/backend/roles-translations/destroy/{roleTranslation}', 'backend_roles_translations_destroy')->middleware(['permission:roles-translations.destroy'])->name('backend.roles.translations.destroy');
            Route::get('/backend/roles-translations/trashed/{role}', 'backend_roles_translations_trashed')->middleware(['permission:roles-translations.trashed'])->name('backend.roles.translations.trashed')->withTrashed();
            Route::patch('/backend/roles-translations/restore/{uuid}', 'backend_roles_translations_restore')->middleware(['permission:roles-translations.restore'])->name('backend.roles.translations.restore');
            Route::delete('/backend/roles-translations/force-destroy/{uuid}', 'backend_roles_translations_force_destroy')->middleware(['permission:roles-translations.force_destroy'])->name('backend.roles.translations.force_destroy');
        });

        // Routes pour : Gestion des Permissions
        Route::controller(V1PermissionController::class)->group(function () {
            Route::get('/backend/permissions/index', 'backend_permissions_index')->middleware(['permission:permissions.index'])->name('backend.permissions.index');
            Route::get('/backend/permissions/create', 'backend_permissions_create')->middleware(['permission:permissions.create'])->name('backend.permissions.create');
            Route::post('/backend/permissions/store', 'backend_permissions_store')->middleware(['permission:permissions.store'])->name('backend.permissions.store');
            Route::get('/backend/permissions/show/{permission}/{slug}', 'backend_permissions_show')->middleware(['permission:permissions.show'])->name('backend.permissions.show');
            Route::get('/backend/permissions/roles/{permission}/{slug}', 'backend_permissions_roles')->middleware(['permission:permissions.show'])->name('backend.permissions.roles');
            Route::get('/backend/permissions/audits/{permission}/{slug}', 'backend_permissions_audits')->middleware(['permission:permissions.audits'])->name('backend.permissions.audits');
            Route::get('/backend/permissions/activity-logs/{permission}/{slug}', 'backend_permissions_activity_logs')->middleware(['permission:permissions.activity_logs'])->name('backend.permissions.activity-logs');
            Route::get('/backend/permissions/edit/{permission}', 'backend_permissions_edit')->middleware(['permission:permissions.edit'])->name('backend.permissions.edit');
            Route::put('/backend/permissions/update/{permission}', 'backend_permissions_update')->middleware(['permission:permissions.update'])->name('backend.permissions.update');
            Route::patch('/backend/permissions/activate/{permission}', 'backend_permissions_activate')->middleware(['permission:permissions.activate'])->name('backend.permissions.activate');
            Route::patch('/backend/permissions/deactivate/{permission}', 'backend_permissions_deactivate')->middleware(['permission:permissions.deactivate'])->name('backend.permissions.deactivate');
            Route::get('/backend/permissions/assign/{permission}', 'backend_permissions_assign')->middleware(['permission:permissions.assign'])->name('backend.permissions.assign');
            Route::put('/backend/permissions/sync-roles/{permission}', 'backend_permissions_sync_roles')->middleware(['permission:permissions.sync_roles'])->name('backend.permissions.sync_roles');
            Route::delete('/backend/permissions/destroy/{permission}', 'backend_permissions_destroy')->middleware(['permission:permissions.destroy'])->name('backend.permissions.destroy');
            Route::get('/backend/permissions/trashed', 'backend_permissions_trashed')->middleware(['permission:permissions.trashed'])->name('backend.permissions.trashed');
            Route::patch('/backend/permissions/restore/{uuid}', 'backend_permissions_restore')->middleware(['permission:permissions.restore'])->name('backend.permissions.restore');
            Route::delete('/backend/permissions/force-destroy/{uuid}', 'backend_permissions_force_destroy')->middleware(['permission:permissions.force_destroy'])->name('backend.permissions.force_destroy');
        });

        // Routes pour : Gestion des Utilisateurs
        Route::controller(V1UserController::class)->group(function () {
            Route::get('/backend/users/index', 'backend_users_index')->middleware(['permission:users.index'])->name('backend.users.index');
            Route::get('/backend/users/show/{user}/{slug}', 'backend_users_show')->middleware(['permission:users.show'])->name('backend.users.show');
            Route::get('/backend/users/edit/{user}', 'backend_users_edit')->middleware(['permission:users.edit'])->name('backend.users.edit');
            Route::put('/backend/users/update/{user}', 'backend_users_update')->middleware(['permission:users.update'])->name('backend.users.update');
            Route::patch('/backend/users/activate/{user}', 'backend_users_activate')->middleware(['permission:users.activate'])->name('backend.users.activate');
            Route::patch('/backend/users/suspend/{user}', 'backend_users_suspend')->middleware(['permission:users.suspend'])->name('backend.users.suspend');
            Route::patch('/backend/users/ban/{user}', 'backend_users_ban')->middleware(['permission:users.ban'])->name('backend.users.ban');
            // Autres routes pour la gestion des utilisateurs
            Route::get('/backend/users/activity-logs/{user}/{slug}', 'backend_users_activity_logs')->middleware(['permission:users.activity_logs'])->name('backend.users.activity-logs');
            Route::get('/backend/users/subscribes/{user}/{slug}', 'backend_users_subscribes')->middleware(['permission:users.subscribes'])->name('backend.users.subscribes');
            Route::get('/backend/users/galleries/{user}/{slug}', 'backend_users_galleries')->middleware(['permission:users.galleries'])->name('backend.users.galleries');
            Route::get('/backend/users/contacts/{user}/{slug}', 'backend_users_contacts')->middleware(['permission:users.contacts'])->name('backend.users.contacts');
            Route::get('/backend/users/faqs/{user}/{slug}', 'backend_users_faqs')->middleware(['permission:users.faqs'])->name('backend.users.faqs');
            Route::get('/backend/users/audits/{user}/{slug}', 'backend_users_audits')->middleware(['permission:users.audits'])->name('backend.users.audits');
            Route::get('/backend/users/blog-posts/{user}/{slug}', 'backend_users_blog_posts')->middleware(['permission:users.blog_posts'])->name('backend.users.blog-posts');
            Route::get('/backend/users/blog-post-comments/{user}/{slug}', 'backend_users_blog_post_comments')->middleware(['permission:users.blog_post_comments'])->name('backend.users.blog-post-comments');
            Route::get('/backend/users/blog-post-comment-replies/{user}/{slug}', 'backend_users_blog_post_comment_replies')->middleware(['permission:users.blog_post_comment_replies'])->name('backend.users.blog-post-comment-replies');
        });

        
        // Routes pour : Gestion des Catégories de Produits (module Boutique)
        Route::controller(V1ProductCategoryController::class)->group(function () {
            Route::get('/backend/product-categories/index', 'backend_product_categories_index')->middleware(['permission:product-categories.index'])->name('backend.product-categories.index');
            Route::get('/backend/product-categories/create', 'backend_product_categories_create')->middleware(['permission:product-categories.create'])->name('backend.product-categories.create');
            Route::post('/backend/product-categories/store', 'backend_product_categories_store')->middleware(['permission:product-categories.store'])->name('backend.product-categories.store');
            Route::get('/backend/product-categories/show/{productCategory}/{slug}', 'backend_product_categories_show')->middleware(['permission:product-categories.show'])->name('backend.product-categories.show');
            Route::get('/backend/product-categories/audits/{productCategory}/{slug}', 'backend_product_categories_audits')->middleware(['permission:product-categories.audits'])->name('backend.product-categories.audits');
            Route::get('/backend/product-categories/activity-logs/{productCategory}/{slug}', 'backend_product_categories_activity_logs')->middleware(['permission:product-categories.activity_logs'])->name('backend.product-categories.activity-logs');
            Route::get('/backend/product-categories/edit/{productCategory}', 'backend_product_categories_edit')->middleware(['permission:product-categories.edit'])->name('backend.product-categories.edit');
            Route::put('/backend/product-categories/update/{productCategory}', 'backend_product_categories_update')->middleware(['permission:product-categories.update'])->name('backend.product-categories.update');
            Route::patch('/backend/product-categories/activate/{productCategory}', 'backend_product_categories_activate')->middleware(['permission:product-categories.activate'])->name('backend.product-categories.activate');
            Route::patch('/backend/product-categories/deactivate/{productCategory}', 'backend_product_categories_deactivate')->middleware(['permission:product-categories.deactivate'])->name('backend.product-categories.deactivate');
            Route::delete('/backend/product-categories/destroy/{productCategory}', 'backend_product_categories_destroy')->middleware(['permission:product-categories.destroy'])->name('backend.product-categories.destroy');
            Route::get('/backend/product-categories/trashed', 'backend_product_categories_trashed')->middleware(['permission:product-categories.trashed'])->name('backend.product-categories.trashed');
            Route::patch('/backend/product-categories/restore/{uuid}', 'backend_product_categories_restore')->middleware(['permission:product-categories.restore'])->name('backend.product-categories.restore');
            Route::delete('/backend/product-categories/force-destroy/{uuid}', 'backend_product_categories_force_destroy')->middleware(['permission:product-categories.force_destroy'])->name('backend.product-categories.force_destroy');
        });
        Route::controller(V1ProductCategoryTranslationController::class)->group(function () {
            Route::delete('/backend/product-category-translations/destroy/{uuid}', 'backend_product_category_translations_destroy')->middleware(['permission:product-category-translations.destroy'])->name('backend.product-category-translations.destroy');
            Route::get('/backend/product-category-translations/trashed/{category_uuid}', 'backend_product_category_translations_trashed')->middleware(['permission:product-category-translations.trashed'])->name('backend.product-category-translations.trashed');
            Route::patch('/backend/product-category-translations/restore/{uuid}', 'backend_product_category_translations_restore')->middleware(['permission:product-category-translations.restore'])->name('backend.product-category-translations.restore');
            Route::delete('/backend/product-category-translations/force-destroy/{uuid}', 'backend_product_category_translations_force_destroy')->middleware(['permission:product-category-translations.force_destroy'])->name('backend.product-category-translations.force_destroy');
        });

        // Routes pour : Gestion des Marques de Produits (module Boutique)
        Route::controller(V1ProductBrandController::class)->group(function () {
            Route::get('/backend/product-brands/index', 'backend_product_brands_index')->middleware(['permission:product-brands.index'])->name('backend.product-brands.index');
            Route::get('/backend/product-brands/create', 'backend_product_brands_create')->middleware(['permission:product-brands.create'])->name('backend.product-brands.create');
            Route::post('/backend/product-brands/store', 'backend_product_brands_store')->middleware(['permission:product-brands.store'])->name('backend.product-brands.store');
            Route::get('/backend/product-brands/show/{productBrand}/{slug}', 'backend_product_brands_show')->middleware(['permission:product-brands.show'])->name('backend.product-brands.show');
            Route::get('/backend/product-brands/audits/{productBrand}/{slug}', 'backend_product_brands_audits')->middleware(['permission:product-brands.audits'])->name('backend.product-brands.audits');
            Route::get('/backend/product-brands/activity-logs/{productBrand}/{slug}', 'backend_product_brands_activity_logs')->middleware(['permission:product-brands.activity_logs'])->name('backend.product-brands.activity-logs');
            Route::get('/backend/product-brands/edit/{productBrand}', 'backend_product_brands_edit')->middleware(['permission:product-brands.edit'])->name('backend.product-brands.edit');
            Route::put('/backend/product-brands/update/{productBrand}', 'backend_product_brands_update')->middleware(['permission:product-brands.update'])->name('backend.product-brands.update');
            Route::patch('/backend/product-brands/activate/{productBrand}', 'backend_product_brands_activate')->middleware(['permission:product-brands.activate'])->name('backend.product-brands.activate');
            Route::patch('/backend/product-brands/deactivate/{productBrand}', 'backend_product_brands_deactivate')->middleware(['permission:product-brands.deactivate'])->name('backend.product-brands.deactivate');
            Route::delete('/backend/product-brands/destroy/{productBrand}', 'backend_product_brands_destroy')->middleware(['permission:product-brands.destroy'])->name('backend.product-brands.destroy');
            Route::get('/backend/product-brands/trashed', 'backend_product_brands_trashed')->middleware(['permission:product-brands.trashed'])->name('backend.product-brands.trashed');
            Route::patch('/backend/product-brands/restore/{uuid}', 'backend_product_brands_restore')->middleware(['permission:product-brands.restore'])->name('backend.product-brands.restore');
            Route::delete('/backend/product-brands/force-destroy/{uuid}', 'backend_product_brands_force_destroy')->middleware(['permission:product-brands.force_destroy'])->name('backend.product-brands.force_destroy');
        });
        Route::controller(V1ProductBrandTranslationController::class)->group(function () {
            Route::delete('/backend/product-brand-translations/destroy/{uuid}', 'backend_product_brand_translations_destroy')->middleware(['permission:product-brand-translations.destroy'])->name('backend.product-brand-translations.destroy');
            Route::get('/backend/product-brand-translations/trashed/{brand_uuid}', 'backend_product_brand_translations_trashed')->middleware(['permission:product-brand-translations.trashed'])->name('backend.product-brand-translations.trashed');
            Route::patch('/backend/product-brand-translations/restore/{uuid}', 'backend_product_brand_translations_restore')->middleware(['permission:product-brand-translations.restore'])->name('backend.product-brand-translations.restore');
            Route::delete('/backend/product-brand-translations/force-destroy/{uuid}', 'backend_product_brand_translations_force_destroy')->middleware(['permission:product-brand-translations.force_destroy'])->name('backend.product-brand-translations.force_destroy');
        });

        // Routes pour : Gestion des Tags de Produits (module Boutique)
        Route::controller(V1ProductTagController::class)->group(function () {
            Route::get('/backend/product-tags/index', 'backend_product_tags_index')->middleware(['permission:product-tags.index'])->name('backend.product-tags.index');
            Route::get('/backend/product-tags/create', 'backend_product_tags_create')->middleware(['permission:product-tags.create'])->name('backend.product-tags.create');
            Route::post('/backend/product-tags/store', 'backend_product_tags_store')->middleware(['permission:product-tags.store'])->name('backend.product-tags.store');
            Route::get('/backend/product-tags/show/{productTag}/{slug}', 'backend_product_tags_show')->middleware(['permission:product-tags.show'])->name('backend.product-tags.show');
            Route::get('/backend/product-tags/audits/{productTag}/{slug}', 'backend_product_tags_audits')->middleware(['permission:product-tags.audits'])->name('backend.product-tags.audits');
            Route::get('/backend/product-tags/activity-logs/{productTag}/{slug}', 'backend_product_tags_activity_logs')->middleware(['permission:product-tags.activity_logs'])->name('backend.product-tags.activity-logs');
            Route::get('/backend/product-tags/edit/{productTag}', 'backend_product_tags_edit')->middleware(['permission:product-tags.edit'])->name('backend.product-tags.edit');
            Route::put('/backend/product-tags/update/{productTag}', 'backend_product_tags_update')->middleware(['permission:product-tags.update'])->name('backend.product-tags.update');
            Route::patch('/backend/product-tags/activate/{productTag}', 'backend_product_tags_activate')->middleware(['permission:product-tags.activate'])->name('backend.product-tags.activate');
            Route::patch('/backend/product-tags/deactivate/{productTag}', 'backend_product_tags_deactivate')->middleware(['permission:product-tags.deactivate'])->name('backend.product-tags.deactivate');
            Route::delete('/backend/product-tags/destroy/{productTag}', 'backend_product_tags_destroy')->middleware(['permission:product-tags.destroy'])->name('backend.product-tags.destroy');
            Route::get('/backend/product-tags/trashed', 'backend_product_tags_trashed')->middleware(['permission:product-tags.trashed'])->name('backend.product-tags.trashed');
            Route::patch('/backend/product-tags/restore/{uuid}', 'backend_product_tags_restore')->middleware(['permission:product-tags.restore'])->name('backend.product-tags.restore');
            Route::delete('/backend/product-tags/force-destroy/{uuid}', 'backend_product_tags_force_destroy')->middleware(['permission:product-tags.force_destroy'])->name('backend.product-tags.force_destroy');
        });
        Route::controller(V1ProductTagTranslationController::class)->group(function () {
            Route::delete('/backend/product-tag-translations/destroy/{uuid}', 'backend_product_tag_translations_destroy')->middleware(['permission:product-tag-translations.destroy'])->name('backend.product-tag-translations.destroy');
            Route::get('/backend/product-tag-translations/trashed/{tag_uuid}', 'backend_product_tag_translations_trashed')->middleware(['permission:product-tag-translations.trashed'])->name('backend.product-tag-translations.trashed');
            Route::patch('/backend/product-tag-translations/restore/{uuid}', 'backend_product_tag_translations_restore')->middleware(['permission:product-tag-translations.restore'])->name('backend.product-tag-translations.restore');
            Route::delete('/backend/product-tag-translations/force-destroy/{uuid}', 'backend_product_tag_translations_force_destroy')->middleware(['permission:product-tag-translations.force_destroy'])->name('backend.product-tag-translations.force_destroy');
        });

        // Routes pour : Gestion des Attributs de Produits (module Boutique)
        Route::controller(V1ProductAttributeController::class)->group(function () {
            Route::get('/backend/product-attributes/index', 'backend_product_attributes_index')->middleware(['permission:product-attributes.index'])->name('backend.product-attributes.index');
            Route::get('/backend/product-attributes/create', 'backend_product_attributes_create')->middleware(['permission:product-attributes.create'])->name('backend.product-attributes.create');
            Route::post('/backend/product-attributes/store', 'backend_product_attributes_store')->middleware(['permission:product-attributes.store'])->name('backend.product-attributes.store');
            Route::get('/backend/product-attributes/show/{productAttribute}/{slug}', 'backend_product_attributes_show')->middleware(['permission:product-attributes.show'])->name('backend.product-attributes.show');
            Route::get('/backend/product-attributes/audits/{productAttribute}/{slug}', 'backend_product_attributes_audits')->middleware(['permission:product-attributes.audits'])->name('backend.product-attributes.audits');
            Route::get('/backend/product-attributes/activity-logs/{productAttribute}/{slug}', 'backend_product_attributes_activity_logs')->middleware(['permission:product-attributes.activity_logs'])->name('backend.product-attributes.activity-logs');
            Route::get('/backend/product-attributes/edit/{productAttribute}', 'backend_product_attributes_edit')->middleware(['permission:product-attributes.edit'])->name('backend.product-attributes.edit');
            Route::put('/backend/product-attributes/update/{productAttribute}', 'backend_product_attributes_update')->middleware(['permission:product-attributes.update'])->name('backend.product-attributes.update');
            Route::patch('/backend/product-attributes/activate/{productAttribute}', 'backend_product_attributes_activate')->middleware(['permission:product-attributes.activate'])->name('backend.product-attributes.activate');
            Route::patch('/backend/product-attributes/deactivate/{productAttribute}', 'backend_product_attributes_deactivate')->middleware(['permission:product-attributes.deactivate'])->name('backend.product-attributes.deactivate');
            Route::delete('/backend/product-attributes/destroy/{productAttribute}', 'backend_product_attributes_destroy')->middleware(['permission:product-attributes.destroy'])->name('backend.product-attributes.destroy');
            Route::get('/backend/product-attributes/trashed', 'backend_product_attributes_trashed')->middleware(['permission:product-attributes.trashed'])->name('backend.product-attributes.trashed');
            Route::patch('/backend/product-attributes/restore/{uuid}', 'backend_product_attributes_restore')->middleware(['permission:product-attributes.restore'])->name('backend.product-attributes.restore');
            Route::delete('/backend/product-attributes/force-destroy/{uuid}', 'backend_product_attributes_force_destroy')->middleware(['permission:product-attributes.force_destroy'])->name('backend.product-attributes.force_destroy');
        });
        Route::controller(V1ProductAttributeTranslationController::class)->group(function () {
            Route::delete('/backend/product-attribute-translations/destroy/{uuid}', 'backend_product_attribute_translations_destroy')->middleware(['permission:product-attribute-translations.destroy'])->name('backend.product-attribute-translations.destroy');
            Route::get('/backend/product-attribute-translations/trashed/{attribute_uuid}', 'backend_product_attribute_translations_trashed')->middleware(['permission:product-attribute-translations.trashed'])->name('backend.product-attribute-translations.trashed');
            Route::patch('/backend/product-attribute-translations/restore/{uuid}', 'backend_product_attribute_translations_restore')->middleware(['permission:product-attribute-translations.restore'])->name('backend.product-attribute-translations.restore');
            Route::delete('/backend/product-attribute-translations/force-destroy/{uuid}', 'backend_product_attribute_translations_force_destroy')->middleware(['permission:product-attribute-translations.force_destroy'])->name('backend.product-attribute-translations.force_destroy');
        });

        // Routes pour : Gestion des Valeurs d'Attributs de Produits (module Boutique)
        Route::controller(V1ProductAttributeValueController::class)->group(function () {
            Route::get('/backend/product-attribute-values/index', 'backend_product_attribute_values_index')->middleware(['permission:product-attribute-values.index'])->name('backend.product-attribute-values.index');
            Route::get('/backend/product-attribute-values/create', 'backend_product_attribute_values_create')->middleware(['permission:product-attribute-values.create'])->name('backend.product-attribute-values.create');
            Route::post('/backend/product-attribute-values/store', 'backend_product_attribute_values_store')->middleware(['permission:product-attribute-values.store'])->name('backend.product-attribute-values.store');
            Route::get('/backend/product-attribute-values/show/{productAttributeValue}/{slug}', 'backend_product_attribute_values_show')->middleware(['permission:product-attribute-values.show'])->name('backend.product-attribute-values.show');
            Route::get('/backend/product-attribute-values/audits/{productAttributeValue}/{slug}', 'backend_product_attribute_values_audits')->middleware(['permission:product-attribute-values.audits'])->name('backend.product-attribute-values.audits');
            Route::get('/backend/product-attribute-values/activity-logs/{productAttributeValue}/{slug}', 'backend_product_attribute_values_activity_logs')->middleware(['permission:product-attribute-values.activity_logs'])->name('backend.product-attribute-values.activity-logs');
            Route::get('/backend/product-attribute-values/edit/{productAttributeValue}', 'backend_product_attribute_values_edit')->middleware(['permission:product-attribute-values.edit'])->name('backend.product-attribute-values.edit');
            Route::put('/backend/product-attribute-values/update/{productAttributeValue}', 'backend_product_attribute_values_update')->middleware(['permission:product-attribute-values.update'])->name('backend.product-attribute-values.update');
            Route::patch('/backend/product-attribute-values/activate/{productAttributeValue}', 'backend_product_attribute_values_activate')->middleware(['permission:product-attribute-values.activate'])->name('backend.product-attribute-values.activate');
            Route::patch('/backend/product-attribute-values/deactivate/{productAttributeValue}', 'backend_product_attribute_values_deactivate')->middleware(['permission:product-attribute-values.deactivate'])->name('backend.product-attribute-values.deactivate');
            Route::delete('/backend/product-attribute-values/destroy/{productAttributeValue}', 'backend_product_attribute_values_destroy')->middleware(['permission:product-attribute-values.destroy'])->name('backend.product-attribute-values.destroy');
            Route::get('/backend/product-attribute-values/trashed', 'backend_product_attribute_values_trashed')->middleware(['permission:product-attribute-values.trashed'])->name('backend.product-attribute-values.trashed');
            Route::patch('/backend/product-attribute-values/restore/{uuid}', 'backend_product_attribute_values_restore')->middleware(['permission:product-attribute-values.restore'])->name('backend.product-attribute-values.restore');
            Route::delete('/backend/product-attribute-values/force-destroy/{uuid}', 'backend_product_attribute_values_force_destroy')->middleware(['permission:product-attribute-values.force_destroy'])->name('backend.product-attribute-values.force_destroy');
        });
        Route::controller(V1ProductAttributeValueTranslationController::class)->group(function () {
            Route::delete('/backend/product-attribute-value-translations/destroy/{uuid}', 'backend_product_attribute_value_translations_destroy')->middleware(['permission:product-attribute-value-translations.destroy'])->name('backend.product-attribute-value-translations.destroy');
            Route::get('/backend/product-attribute-value-translations/trashed/{attribute_value_uuid}', 'backend_product_attribute_value_translations_trashed')->middleware(['permission:product-attribute-value-translations.trashed'])->name('backend.product-attribute-value-translations.trashed');
            Route::patch('/backend/product-attribute-value-translations/restore/{uuid}', 'backend_product_attribute_value_translations_restore')->middleware(['permission:product-attribute-value-translations.restore'])->name('backend.product-attribute-value-translations.restore');
            Route::delete('/backend/product-attribute-value-translations/force-destroy/{uuid}', 'backend_product_attribute_value_translations_force_destroy')->middleware(['permission:product-attribute-value-translations.force_destroy'])->name('backend.product-attribute-value-translations.force_destroy');
        });

        // Routes pour : Gestion des Produits (module Boutique)
        Route::controller(V1ProductController::class)->group(function () {
            Route::get('/backend/products/index', 'backend_products_index')->middleware(['permission:products.index'])->name('backend.products.index');
            Route::get('/backend/products/create', 'backend_products_create')->middleware(['permission:products.create'])->name('backend.products.create');
            Route::post('/backend/products/store', 'backend_products_store')->middleware(['permission:products.store'])->name('backend.products.store');
            Route::get('/backend/products/show/{product}/{slug}', 'backend_products_show')->middleware(['permission:products.show'])->name('backend.products.show');
            Route::get('/backend/products/audits/{product}/{slug}', 'backend_products_audits')->middleware(['permission:products.audits'])->name('backend.products.audits');
            Route::get('/backend/products/activity-logs/{product}/{slug}', 'backend_products_activity_logs')->middleware(['permission:products.activity_logs'])->name('backend.products.activity-logs');
            Route::get('/backend/products/edit/{product}', 'backend_products_edit')->middleware(['permission:products.edit'])->name('backend.products.edit');
            Route::put('/backend/products/update/{product}', 'backend_products_update')->middleware(['permission:products.update'])->name('backend.products.update');
            Route::patch('/backend/products/publish/{product}', 'backend_products_publish')->middleware(['permission:products.publish'])->name('backend.products.publish');
            Route::patch('/backend/products/unpublish/{product}', 'backend_products_unpublish')->middleware(['permission:products.unpublish'])->name('backend.products.unpublish');
            Route::patch('/backend/products/archive/{product}', 'backend_products_archive')->middleware(['permission:products.archive'])->name('backend.products.archive');
            Route::delete('/backend/products/destroy/{product}', 'backend_products_destroy')->middleware(['permission:products.destroy'])->name('backend.products.destroy');
            Route::get('/backend/products/trashed', 'backend_products_trashed')->middleware(['permission:products.trashed'])->name('backend.products.trashed');
            Route::patch('/backend/products/restore/{uuid}', 'backend_products_restore')->middleware(['permission:products.restore'])->name('backend.products.restore');
            Route::delete('/backend/products/force-destroy/{uuid}', 'backend_products_force_destroy')->middleware(['permission:products.force_destroy'])->name('backend.products.force_destroy');
        });
        Route::controller(V1ProductTranslationController::class)->group(function () {
            Route::delete('/backend/product-translations/destroy/{uuid}', 'backend_product_translations_destroy')->middleware(['permission:product-translations.destroy'])->name('backend.product-translations.destroy');
            Route::get('/backend/product-translations/trashed/{product_uuid}', 'backend_product_translations_trashed')->middleware(['permission:product-translations.trashed'])->name('backend.product-translations.trashed');
            Route::patch('/backend/product-translations/restore/{uuid}', 'backend_product_translations_restore')->middleware(['permission:product-translations.restore'])->name('backend.product-translations.restore');
            Route::delete('/backend/product-translations/force-destroy/{uuid}', 'backend_product_translations_force_destroy')->middleware(['permission:product-translations.force_destroy'])->name('backend.product-translations.force_destroy');
        });

        // Routes pour : Gestion des Variantes de Produits (module Boutique)
        Route::controller(V1ProductVariantController::class)->group(function () {
            Route::get('/backend/product-variants/index', 'backend_product_variants_index')->middleware(['permission:product-variants.index'])->name('backend.product-variants.index');
            Route::get('/backend/product-variants/create', 'backend_product_variants_create')->middleware(['permission:product-variants.create'])->name('backend.product-variants.create');
            Route::post('/backend/product-variants/store', 'backend_product_variants_store')->middleware(['permission:product-variants.store'])->name('backend.product-variants.store');
            Route::get('/backend/product-variants/show/{productVariant}', 'backend_product_variants_show')->middleware(['permission:product-variants.show'])->name('backend.product-variants.show');
            Route::get('/backend/product-variants/audits/{productVariant}', 'backend_product_variants_audits')->middleware(['permission:product-variants.audits'])->name('backend.product-variants.audits');
            Route::get('/backend/product-variants/activity-logs/{productVariant}', 'backend_product_variants_activity_logs')->middleware(['permission:product-variants.activity_logs'])->name('backend.product-variants.activity-logs');
            Route::get('/backend/product-variants/edit/{productVariant}', 'backend_product_variants_edit')->middleware(['permission:product-variants.edit'])->name('backend.product-variants.edit');
            Route::put('/backend/product-variants/update/{productVariant}', 'backend_product_variants_update')->middleware(['permission:product-variants.update'])->name('backend.product-variants.update');
            Route::patch('/backend/product-variants/activate/{productVariant}', 'backend_product_variants_activate')->middleware(['permission:product-variants.activate'])->name('backend.product-variants.activate');
            Route::patch('/backend/product-variants/deactivate/{productVariant}', 'backend_product_variants_deactivate')->middleware(['permission:product-variants.deactivate'])->name('backend.product-variants.deactivate');
            Route::delete('/backend/product-variants/destroy/{productVariant}', 'backend_product_variants_destroy')->middleware(['permission:product-variants.destroy'])->name('backend.product-variants.destroy');
            Route::get('/backend/product-variants/trashed', 'backend_product_variants_trashed')->middleware(['permission:product-variants.trashed'])->name('backend.product-variants.trashed');
            Route::patch('/backend/product-variants/restore/{uuid}', 'backend_product_variants_restore')->middleware(['permission:product-variants.restore'])->name('backend.product-variants.restore');
            Route::delete('/backend/product-variants/force-destroy/{uuid}', 'backend_product_variants_force_destroy')->middleware(['permission:product-variants.force_destroy'])->name('backend.product-variants.force_destroy');
        });

        // Routes pour : Gestion des Stocks (entrepôt × variante, module Boutique)
        Route::controller(V1ProductWarehouseStockController::class)->group(function () {
            Route::get('/backend/product-warehouse-stocks/index', 'backend_product_warehouse_stocks_index')->middleware(['permission:product-warehouse-stocks.index'])->name('backend.product-warehouse-stocks.index');
            Route::get('/backend/product-warehouse-stocks/create', 'backend_product_warehouse_stocks_create')->middleware(['permission:product-warehouse-stocks.create'])->name('backend.product-warehouse-stocks.create');
            Route::post('/backend/product-warehouse-stocks/store', 'backend_product_warehouse_stocks_store')->middleware(['permission:product-warehouse-stocks.store'])->name('backend.product-warehouse-stocks.store');
            Route::get('/backend/product-warehouse-stocks/show/{productWarehouseStock}', 'backend_product_warehouse_stocks_show')->middleware(['permission:product-warehouse-stocks.show'])->name('backend.product-warehouse-stocks.show');
            Route::get('/backend/product-warehouse-stocks/audits/{productWarehouseStock}', 'backend_product_warehouse_stocks_audits')->middleware(['permission:product-warehouse-stocks.audits'])->name('backend.product-warehouse-stocks.audits');
            Route::get('/backend/product-warehouse-stocks/activity-logs/{productWarehouseStock}', 'backend_product_warehouse_stocks_activity_logs')->middleware(['permission:product-warehouse-stocks.activity_logs'])->name('backend.product-warehouse-stocks.activity-logs');
            Route::get('/backend/product-warehouse-stocks/edit/{productWarehouseStock}', 'backend_product_warehouse_stocks_edit')->middleware(['permission:product-warehouse-stocks.edit'])->name('backend.product-warehouse-stocks.edit');
            Route::put('/backend/product-warehouse-stocks/update/{productWarehouseStock}', 'backend_product_warehouse_stocks_update')->middleware(['permission:product-warehouse-stocks.update'])->name('backend.product-warehouse-stocks.update');
            Route::delete('/backend/product-warehouse-stocks/destroy/{productWarehouseStock}', 'backend_product_warehouse_stocks_destroy')->middleware(['permission:product-warehouse-stocks.destroy'])->name('backend.product-warehouse-stocks.destroy');
            Route::get('/backend/product-warehouse-stocks/trashed', 'backend_product_warehouse_stocks_trashed')->middleware(['permission:product-warehouse-stocks.trashed'])->name('backend.product-warehouse-stocks.trashed');
            Route::patch('/backend/product-warehouse-stocks/restore/{uuid}', 'backend_product_warehouse_stocks_restore')->middleware(['permission:product-warehouse-stocks.restore'])->name('backend.product-warehouse-stocks.restore');
            Route::delete('/backend/product-warehouse-stocks/force-destroy/{uuid}', 'backend_product_warehouse_stocks_force_destroy')->middleware(['permission:product-warehouse-stocks.force_destroy'])->name('backend.product-warehouse-stocks.force-destroy');
        });

        // Routes pour : Gestion des Mouvements de stock (module Boutique)
        Route::controller(V1ProductStockMovementController::class)->group(function () {
            Route::get('/backend/product-stock-movements/index', 'backend_product_stock_movements_index')->middleware(['permission:product-stock-movements.index'])->name('backend.product-stock-movements.index');
            Route::get('/backend/product-stock-movements/create', 'backend_product_stock_movements_create')->middleware(['permission:product-stock-movements.create'])->name('backend.product-stock-movements.create');
            Route::post('/backend/product-stock-movements/store', 'backend_product_stock_movements_store')->middleware(['permission:product-stock-movements.store'])->name('backend.product-stock-movements.store');
            Route::get('/backend/product-stock-movements/show/{productStockMovement}', 'backend_product_stock_movements_show')->middleware(['permission:product-stock-movements.show'])->name('backend.product-stock-movements.show');
            Route::delete('/backend/product-stock-movements/destroy/{productStockMovement}', 'backend_product_stock_movements_destroy')->middleware(['permission:product-stock-movements.destroy'])->name('backend.product-stock-movements.destroy');
            Route::get('/backend/product-stock-movements/trashed', 'backend_product_stock_movements_trashed')->middleware(['permission:product-stock-movements.trashed'])->name('backend.product-stock-movements.trashed');
            Route::patch('/backend/product-stock-movements/restore/{uuid}', 'backend_product_stock_movements_restore')->middleware(['permission:product-stock-movements.restore'])->name('backend.product-stock-movements.restore');
            Route::delete('/backend/product-stock-movements/force-destroy/{uuid}', 'backend_product_stock_movements_force_destroy')->middleware(['permission:product-stock-movements.force_destroy'])->name('backend.product-stock-movements.force-destroy');
        });

        // Routes pour : Gestion des Entrepôts (module Boutique)
        Route::controller(V1ProductWarehouseController::class)->group(function () {
            Route::get('/backend/product-warehouses/index', 'backend_product_warehouses_index')->middleware(['permission:product-warehouses.index'])->name('backend.product-warehouses.index');
            Route::get('/backend/product-warehouses/create', 'backend_product_warehouses_create')->middleware(['permission:product-warehouses.create'])->name('backend.product-warehouses.create');
            Route::post('/backend/product-warehouses/store', 'backend_product_warehouses_store')->middleware(['permission:product-warehouses.store'])->name('backend.product-warehouses.store');
            Route::get('/backend/product-warehouses/show/{productWarehouse}', 'backend_product_warehouses_show')->middleware(['permission:product-warehouses.show'])->name('backend.product-warehouses.show');
            Route::get('/backend/product-warehouses/audits/{productWarehouse}', 'backend_product_warehouses_audits')->middleware(['permission:product-warehouses.audits'])->name('backend.product-warehouses.audits');
            Route::get('/backend/product-warehouses/activity-logs/{productWarehouse}', 'backend_product_warehouses_activity_logs')->middleware(['permission:product-warehouses.activity_logs'])->name('backend.product-warehouses.activity-logs');
            Route::get('/backend/product-warehouses/edit/{productWarehouse}', 'backend_product_warehouses_edit')->middleware(['permission:product-warehouses.edit'])->name('backend.product-warehouses.edit');
            Route::put('/backend/product-warehouses/update/{productWarehouse}', 'backend_product_warehouses_update')->middleware(['permission:product-warehouses.update'])->name('backend.product-warehouses.update');
            Route::patch('/backend/product-warehouses/activate/{productWarehouse}', 'backend_product_warehouses_activate')->middleware(['permission:product-warehouses.activate'])->name('backend.product-warehouses.activate');
            Route::patch('/backend/product-warehouses/deactivate/{productWarehouse}', 'backend_product_warehouses_deactivate')->middleware(['permission:product-warehouses.deactivate'])->name('backend.product-warehouses.deactivate');
            Route::delete('/backend/product-warehouses/destroy/{productWarehouse}', 'backend_product_warehouses_destroy')->middleware(['permission:product-warehouses.destroy'])->name('backend.product-warehouses.destroy');
            Route::get('/backend/product-warehouses/trashed', 'backend_product_warehouses_trashed')->middleware(['permission:product-warehouses.trashed'])->name('backend.product-warehouses.trashed');
            Route::patch('/backend/product-warehouses/restore/{uuid}', 'backend_product_warehouses_restore')->middleware(['permission:product-warehouses.restore'])->name('backend.product-warehouses.restore');
            Route::delete('/backend/product-warehouses/force-destroy/{uuid}', 'backend_product_warehouses_force_destroy')->middleware(['permission:product-warehouses.force_destroy'])->name('backend.product-warehouses.force_destroy');
        });

        // Routes pour : Gestion des Catégories de Blog
        Route::controller(V1BlogCategoryController::class)->group(function () {
            Route::get('/backend/blog-categories/index', 'backend_blog_categories_index')->middleware(['permission:blog-categories.index'])->name('backend.blog-categories.index');
            Route::get('/backend/blog-categories/create', 'backend_blog_categories_create')->middleware(['permission:blog-categories.create'])->name('backend.blog-categories.create');
            Route::post('/backend/blog-categories/store', 'backend_blog_categories_store')->middleware(['permission:blog-categories.store'])->name('backend.blog-categories.store');
            Route::get('/backend/blog-categories/show/{blogCategory}/{slug}', 'backend_blog_categories_show')->middleware(['permission:blog-categories.show'])->name('backend.blog-categories.show');
            Route::get('/backend/blog-categories/posts/{blogCategory}/{slug}', 'backend_blog_categories_posts')->middleware(['permission:blog-categories.show'])->name('backend.blog-categories.posts');
            Route::get('/backend/blog-categories/audits/{blogCategory}/{slug}', 'backend_blog_categories_audits')->middleware(['permission:blog-categories.audits'])->name('backend.blog-categories.audits');
            Route::get('/backend/blog-categories/activity-logs/{blogCategory}/{slug}', 'backend_blog_categories_activity_logs')->middleware(['permission:blog-categories.activity_logs'])->name('backend.blog-categories.activity-logs');
            Route::get('/backend/blog-categories/edit/{blogCategory}', 'backend_blog_categories_edit')->middleware(['permission:blog-categories.edit'])->name('backend.blog-categories.edit');
            Route::put('/backend/blog-categories/update/{blogCategory}', 'backend_blog_categories_update')->middleware(['permission:blog-categories.update'])->name('backend.blog-categories.update');
            Route::patch('/backend/blog-categories/activate/{blogCategory}', 'backend_blog_categories_activate')->middleware(['permission:blog-categories.activate'])->name('backend.blog-categories.activate');
            Route::patch('/backend/blog-categories/deactivate/{blogCategory}', 'backend_blog_categories_deactivate')->middleware(['permission:blog-categories.deactivate'])->name('backend.blog-categories.deactivate');
            Route::delete('/backend/blog-categories/destroy/{blogCategory}', 'backend_blog_categories_destroy')->middleware(['permission:blog-categories.destroy'])->name('backend.blog-categories.destroy');
            Route::get('/backend/blog-categories/trashed', 'backend_blog_categories_trashed')->middleware(['permission:blog-categories.trashed'])->name('backend.blog-categories.trashed');
            Route::patch('/backend/blog-categories/restore/{uuid}', 'backend_blog_categories_restore')->middleware(['permission:blog-categories.restore'])->name('backend.blog-categories.restore');
            Route::delete('/backend/blog-categories/force-destroy/{uuid}', 'backend_blog_categories_force_destroy')->middleware(['permission:blog-categories.force_destroy'])->name('backend.blog-categories.force_destroy');
        });
        Route::controller(V1BlogCategoryTranslationController::class)->group(function () {
            Route::delete('/backend/blog-category-translations/destroy/{uuid}', 'backend_blog_category_translations_destroy')->middleware(['permission:blog-category-translations.destroy'])->name('backend.blog-category-translations.destroy');
            Route::get('/backend/blog-category-translations/trashed/{category_uuid}', 'backend_blog_category_translations_trashed')->middleware(['permission:blog-category-translations.trashed'])->name('backend.blog-category-translations.trashed');
            Route::patch('/backend/blog-category-translations/restore/{uuid}', 'backend_blog_category_translations_restore')->middleware(['permission:blog-category-translations.restore'])->name('backend.blog-category-translations.restore');
            Route::delete('/backend/blog-category-translations/force-destroy/{uuid}', 'backend_blog_category_translations_force_destroy')->middleware(['permission:blog-category-translations.force_destroy'])->name('backend.blog-category-translations.force_destroy');
        });

        // Routes pour : Gestion des Tags de Blog
        Route::controller(V1BlogTagController::class)->group(function () {
            Route::get('/backend/blog-tags/index', 'backend_blog_tags_index')->middleware(['permission:blog-tags.index'])->name('backend.blog-tags.index');
            Route::get('/backend/blog-tags/create', 'backend_blog_tags_create')->middleware(['permission:blog-tags.create'])->name('backend.blog-tags.create');
            Route::post('/backend/blog-tags/store', 'backend_blog_tags_store')->middleware(['permission:blog-tags.store'])->name('backend.blog-tags.store');
            Route::get('/backend/blog-tags/show/{blogTag}/{slug}', 'backend_blog_tags_show')->middleware(['permission:blog-tags.show'])->name('backend.blog-tags.show');
            Route::get('/backend/blog-tags/posts/{blogTag}/{slug}', 'backend_blog_tags_posts')->middleware(['permission:blog-tags.show'])->name('backend.blog-tags.posts');
            Route::get('/backend/blog-tags/audits/{blogTag}/{slug}', 'backend_blog_tags_audits')->middleware(['permission:blog-tags.audits'])->name('backend.blog-tags.audits');
            Route::get('/backend/blog-tags/activity-logs/{blogTag}/{slug}', 'backend_blog_tags_activity_logs')->middleware(['permission:blog-tags.activity_logs'])->name('backend.blog-tags.activity-logs');
            Route::get('/backend/blog-tags/edit/{blogTag}', 'backend_blog_tags_edit')->middleware(['permission:blog-tags.edit'])->name('backend.blog-tags.edit');
            Route::put('/backend/blog-tags/update/{blogTag}', 'backend_blog_tags_update')->middleware(['permission:blog-tags.update'])->name('backend.blog-tags.update');
            Route::patch('/backend/blog-tags/activate/{blogTag}', 'backend_blog_tags_activate')->middleware(['permission:blog-tags.activate'])->name('backend.blog-tags.activate');
            Route::patch('/backend/blog-tags/deactivate/{blogTag}', 'backend_blog_tags_deactivate')->middleware(['permission:blog-tags.deactivate'])->name('backend.blog-tags.deactivate');
            Route::delete('/backend/blog-tags/destroy/{blogTag}', 'backend_blog_tags_destroy')->middleware(['permission:blog-tags.destroy'])->name('backend.blog-tags.destroy');
            Route::get('/backend/blog-tags/trashed', 'backend_blog_tags_trashed')->middleware(['permission:blog-tags.trashed'])->name('backend.blog-tags.trashed');
            Route::patch('/backend/blog-tags/restore/{uuid}', 'backend_blog_tags_restore')->middleware(['permission:blog-tags.restore'])->name('backend.blog-tags.restore');
            Route::delete('/backend/blog-tags/force-destroy/{uuid}', 'backend_blog_tags_force_destroy')->middleware(['permission:blog-tags.force_destroy'])->name('backend.blog-tags.force_destroy');
        });
        Route::controller(V1BlogTagTranslationController::class)->group(function () {
            Route::delete('/backend/blog-tag-translations/destroy/{uuid}', 'backend_blog_tag_translations_destroy')->middleware(['permission:blog-tag-translations.destroy'])->name('backend.blog-tag-translations.destroy');
            Route::get('/backend/blog-tag-translations/trashed/{tag_uuid}', 'backend_blog_tag_translations_trashed')->middleware(['permission:blog-tag-translations.trashed'])->name('backend.blog-tag-translations.trashed');
            Route::patch('/backend/blog-tag-translations/restore/{uuid}', 'backend_blog_tag_translations_restore')->middleware(['permission:blog-tag-translations.restore'])->name('backend.blog-tag-translations.restore');
            Route::delete('/backend/blog-tag-translations/force-destroy/{uuid}', 'backend_blog_tag_translations_force_destroy')->middleware(['permission:blog-tag-translations.force_destroy'])->name('backend.blog-tag-translations.force_destroy');
        });

        // Routes pour : Gestion des Articles (Posts) de Blog
        Route::controller(V1BlogPostController::class)->group(function () {
            Route::get('/backend/blog-posts/index', 'backend_blog_posts_index')->middleware(['permission:blog-posts.index'])->name('backend.blog-posts.index');
            Route::get('/backend/blog-posts/create', 'backend_blog_posts_create')->middleware(['permission:blog-posts.create'])->name('backend.blog-posts.create');
            Route::post('/backend/blog-posts/store', 'backend_blog_posts_store')->middleware(['permission:blog-posts.store'])->name('backend.blog-posts.store');
            Route::get('/backend/blog-posts/show/{blogPost}/{slug}', 'backend_blog_posts_show')->middleware(['permission:blog-posts.show'])->name('backend.blog-posts.show');
            Route::get('/backend/blog-posts/audits/{blogPost}/{slug}', 'backend_blog_posts_audits')->middleware(['permission:blog-posts.audits'])->name('backend.blog-posts.audits');
            Route::get('/backend/blog-posts/activity-logs/{blogPost}/{slug}', 'backend_blog_posts_activity_logs')->middleware(['permission:blog-posts.activity_logs'])->name('backend.blog-posts.activity-logs');
            Route::get('/backend/blog-posts/edit/{blogPost}', 'backend_blog_posts_edit')->middleware(['permission:blog-posts.edit'])->name('backend.blog-posts.edit');
            Route::put('/backend/blog-posts/update/{blogPost}', 'backend_blog_posts_update')->middleware(['permission:blog-posts.update'])->name('backend.blog-posts.update');
            Route::patch('/backend/blog-posts/change-status/{blogPost}', 'backend_blog_posts_change_status')->middleware(['permission:blog-posts.toggle_visibility'])->name('backend.blog-posts.change_status');
            Route::delete('/backend/blog-posts/destroy/{blogPost}', 'backend_blog_posts_destroy')->middleware(['permission:blog-posts.destroy'])->name('backend.blog-posts.destroy');
            Route::get('/backend/blog-posts/trashed', 'backend_blog_posts_trashed')->middleware(['permission:blog-posts.trashed'])->name('backend.blog-posts.trashed');
            Route::patch('/backend/blog-posts/restore/{uuid}', 'backend_blog_posts_restore')->middleware(['permission:blog-posts.restore'])->name('backend.blog-posts.restore');
            Route::delete('/backend/blog-posts/force-destroy/{uuid}', 'backend_blog_posts_force_destroy')->middleware(['permission:blog-posts.force_destroy'])->name('backend.blog-posts.force_destroy');
        });
        Route::controller(V1BlogPostTranslationController::class)->group(function () {
            Route::delete('/backend/blog-post-translations/destroy/{uuid}', 'backend_blog_post_translations_destroy')->middleware(['permission:blog-post-translations.destroy'])->name('backend.blog-post-translations.destroy');
            Route::get('/backend/blog-post-translations/trashed/{post_uuid}', 'backend_blog_post_translations_trashed')->middleware(['permission:blog-post-translations.trashed'])->name('backend.blog-post-translations.trashed');
            Route::patch('/backend/blog-post-translations/restore/{uuid}', 'backend_blog_post_translations_restore')->middleware(['permission:blog-post-translations.restore'])->name('backend.blog-post-translations.restore');
            Route::delete('/backend/blog-post-translations/force-destroy/{uuid}', 'backend_blog_post_translations_force_destroy')->middleware(['permission:blog-post-translations.force_destroy'])->name('backend.blog-post-translations.force_destroy');
        });

        // Routes pour : Gestion des Commentaires de Blog
        Route::controller(V1BlogPostCommentController::class)->group(function () {
            Route::get('/backend/blog-post-comments/index', 'backend_blog_post_comments_index')->middleware(['permission:blog-post-comments.index'])->name('backend.blog-post-comments.index');
            Route::get('/backend/blog-post-comments/show/{blogPostComment}', 'backend_blog_post_comments_show')->middleware(['permission:blog-post-comments.show'])->name('backend.blog-post-comments.show');
            Route::get('/backend/blog-post-comments/replies/{blogPostComment}', 'backend_blog_post_comments_replies')->middleware(['permission:blog-post-comments.replies'])->name('backend.blog-post-comments.replies');
            Route::get('/backend/blog-post-comments/audits/{blogPostComment}', 'backend_blog_post_comments_audits')->middleware(['permission:blog-post-comments.audits'])->name('backend.blog-post-comments.audits');
            Route::get('/backend/blog-post-comments/activity-logs/{blogPostComment}', 'backend_blog_post_comments_activity_logs')->middleware(['permission:blog-post-comments.activity_logs'])->name('backend.blog-post-comments.activity-logs');
            Route::patch('/backend/blog-post-comments/activate/{blogPostComment}', 'backend_blog_post_comments_activate')->middleware(['permission:blog-post-comments.activate'])->name('backend.blog-post-comments.activate');
            Route::patch('/backend/blog-post-comments/deactivate/{blogPostComment}', 'backend_blog_post_comments_deactivate')->middleware(['permission:blog-post-comments.deactivate'])->name('backend.blog-post-comments.deactivate');
            Route::delete('/backend/blog-post-comments/destroy/{blogPostComment}', 'backend_blog_post_comments_destroy')->middleware(['permission:blog-post-comments.destroy'])->name('backend.blog-post-comments.destroy');
            Route::get('/backend/blog-post-comments/trashed', 'backend_blog_post_comments_trashed')->middleware(['permission:blog-post-comments.trashed'])->name('backend.blog-post-comments.trashed');
            Route::patch('/backend/blog-post-comments/restore/{uuid}', 'backend_blog_post_comments_restore')->middleware(['permission:blog-post-comments.restore'])->name('backend.blog-post-comments.restore');
            Route::delete('/backend/blog-post-comments/force-destroy/{uuid}', 'backend_blog_post_comments_force_destroy')->middleware(['permission:blog-post-comments.force_destroy'])->name('backend.blog-post-comments.force_destroy');
        });

        // Routes pour : Gestion des Réponses aux Commentaires de Blog
        Route::controller(V1BlogPostCommentReplyController::class)->group(function () {
            Route::get('/backend/blog-post-comment-replies/index', 'backend_blog_post_comment_replies_index')->middleware(['permission:blog-post-comment-replies.index'])->name('backend.blog-post-comment-replies.index');
            Route::get('/backend/blog-post-comment-replies/show/{blogPostCommentReply}', 'backend_blog_post_comment_replies_show')->middleware(['permission:blog-post-comment-replies.show'])->name('backend.blog-post-comment-replies.show');
            Route::get('/backend/blog-post-comment-replies/audits/{blogPostCommentReply}', 'backend_blog_post_comment_replies_audits')->middleware(['permission:blog-post-comment-replies.audits'])->name('backend.blog-post-comment-replies.audits');
            Route::get('/backend/blog-post-comment-replies/activity-logs/{blogPostCommentReply}', 'backend_blog_post_comment_replies_activity_logs')->middleware(['permission:blog-post-comment-replies.activity_logs'])->name('backend.blog-post-comment-replies.activity-logs');
            Route::patch('/backend/blog-post-comment-replies/activate/{blogPostCommentReply}', 'backend_blog_post_comment_replies_activate')->middleware(['permission:blog-post-comment-replies.activate'])->name('backend.blog-post-comment-replies.activate');
            Route::patch('/backend/blog-post-comment-replies/deactivate/{blogPostCommentReply}', 'backend_blog_post_comment_replies_deactivate')->middleware(['permission:blog-post-comment-replies.deactivate'])->name('backend.blog-post-comment-replies.deactivate');
            Route::delete('/backend/blog-post-comment-replies/destroy/{blogPostCommentReply}', 'backend_blog_post_comment_replies_destroy')->middleware(['permission:blog-post-comment-replies.destroy'])->name('backend.blog-post-comment-replies.destroy');
            Route::get('/backend/blog-post-comment-replies/trashed', 'backend_blog_post_comment_replies_trashed')->middleware(['permission:blog-post-comment-replies.trashed'])->name('backend.blog-post-comment-replies.trashed');
            Route::patch('/backend/blog-post-comment-replies/restore/{uuid}', 'backend_blog_post_comment_replies_restore')->middleware(['permission:blog-post-comment-replies.restore'])->name('backend.blog-post-comment-replies.restore');
            Route::delete('/backend/blog-post-comment-replies/force-destroy/{uuid}', 'backend_blog_post_comment_replies_force_destroy')->middleware(['permission:blog-post-comment-replies.force_destroy'])->name('backend.blog-post-comment-replies.force_destroy');
        });

        // Routes pour : Gestion des Galeries
        Route::controller(V1GalleryController::class)->group(function () {
            Route::get('/backend/galleries/index', 'backend_galleries_index')->middleware(['permission:galleries.index'])->name('backend.galleries.index');
            Route::get('/backend/galleries/create', 'backend_galleries_create')->middleware(['permission:galleries.create'])->name('backend.galleries.create');
            Route::post('/backend/galleries/store', 'backend_galleries_store')->middleware(['permission:galleries.store'])->name('backend.galleries.store');
            Route::get('/backend/galleries/show/{gallery}/{slug}', 'backend_galleries_show')->middleware(['permission:galleries.show'])->name('backend.galleries.show');
            Route::get('/backend/galleries/audits/{gallery}/{slug}', 'backend_galleries_audits')->middleware(['permission:galleries.audits'])->name('backend.galleries.audits');
            Route::get('/backend/galleries/activity-logs/{gallery}/{slug}', 'backend_galleries_activity_logs')->middleware(['permission:galleries.activity_logs'])->name('backend.galleries.activity-logs');
            Route::get('/backend/galleries/edit/{gallery}', 'backend_galleries_edit')->middleware(['permission:galleries.edit'])->name('backend.galleries.edit');
            Route::put('/backend/galleries/update/{gallery}', 'backend_galleries_update')->middleware(['permission:galleries.update'])->name('backend.galleries.update');
            Route::patch('/backend/galleries/toggle-active/{gallery}', 'backend_galleries_toggle_active')->middleware(['permission:galleries.toggle_active'])->name('backend.galleries.toggle_active');
            Route::delete('/backend/galleries/destroy/{gallery}', 'backend_galleries_destroy')->middleware(['permission:galleries.destroy'])->name('backend.galleries.destroy');
            Route::get('/backend/galleries/trashed', 'backend_galleries_trashed')->middleware(['permission:galleries.trashed'])->name('backend.galleries.trashed');
            Route::patch('/backend/galleries/restore/{uuid}', 'backend_galleries_restore')->middleware(['permission:galleries.restore'])->name('backend.galleries.restore');
            Route::delete('/backend/galleries/force-destroy/{uuid}', 'backend_galleries_force_destroy')->middleware(['permission:galleries.force_destroy'])->name('backend.galleries.force_destroy');
        });
        Route::controller(V1GalleryTranslationController::class)->group(function () {
            Route::delete('/backend/gallery-translations/destroy/{uuid}', 'backend_gallery_translations_destroy')->middleware(['permission:gallery-translations.destroy'])->name('backend.gallery-translations.destroy');
            Route::get('/backend/gallery-translations/trashed/{gallery_uuid}', 'backend_gallery_translations_trashed')->middleware(['permission:gallery-translations.trashed'])->name('backend.gallery-translations.trashed');
            Route::patch('/backend/gallery-translations/restore/{uuid}', 'backend_gallery_translations_restore')->middleware(['permission:gallery-translations.restore'])->name('backend.gallery-translations.restore');
            Route::delete('/backend/gallery-translations/force-destroy/{uuid}', 'backend_gallery_translations_force_destroy')->middleware(['permission:gallery-translations.force_destroy'])->name('backend.gallery-translations.force_destroy');
        });

        // Routes pour : Gestion des FAQs
        Route::controller(V1FaqController::class)->group(function () {
            Route::get('/backend/faqs/index', 'backend_faqs_index')->middleware(['permission:faqs.index'])->name('backend.faqs.index');
            Route::get('/backend/faqs/create', 'backend_faqs_create')->middleware(['permission:faqs.create'])->name('backend.faqs.create');
            Route::post('/backend/faqs/store', 'backend_faqs_store')->middleware(['permission:faqs.store'])->name('backend.faqs.store');
            Route::get('/backend/faqs/show/{faq}/{slug}', 'backend_faqs_show')->middleware(['permission:faqs.show'])->name('backend.faqs.show');
            Route::get('/backend/faqs/audits/{faq}/{slug}', 'backend_faqs_audits')->middleware(['permission:faqs.audits'])->name('backend.faqs.audits');
            Route::get('/backend/faqs/activity-logs/{faq}/{slug}', 'backend_faqs_activity_logs')->middleware(['permission:faqs.activity_logs'])->name('backend.faqs.activity-logs');
            Route::get('/backend/faqs/edit/{faq}', 'backend_faqs_edit')->middleware(['permission:faqs.edit'])->name('backend.faqs.edit');
            Route::put('/backend/faqs/update/{faq}', 'backend_faqs_update')->middleware(['permission:faqs.update'])->name('backend.faqs.update');
            Route::patch('/backend/faqs/toggle-active/{faq}', 'backend_faqs_toggle_active')->middleware(['permission:faqs.toggle_active'])->name('backend.faqs.toggle_active');
            Route::delete('/backend/faqs/destroy/{faq}', 'backend_faqs_destroy')->middleware(['permission:faqs.destroy'])->name('backend.faqs.destroy');
            Route::get('/backend/faqs/trashed', 'backend_faqs_trashed')->middleware(['permission:faqs.trashed'])->name('backend.faqs.trashed');
            Route::patch('/backend/faqs/restore/{uuid}', 'backend_faqs_restore')->middleware(['permission:faqs.restore'])->name('backend.faqs.restore');
            Route::delete('/backend/faqs/force-destroy/{uuid}', 'backend_faqs_force_destroy')->middleware(['permission:faqs.force_destroy'])->name('backend.faqs.force_destroy');
        });
        Route::controller(V1FaqTranslationController::class)->group(function () {
            Route::delete('/backend/faq-translations/destroy/{uuid}', 'backend_faq_translations_destroy')->middleware(['permission:faq-translations.destroy'])->name('backend.faq-translations.destroy');
            Route::get('/backend/faq-translations/trashed/{faq_uuid}', 'backend_faq_translations_trashed')->middleware(['permission:faq-translations.trashed'])->name('backend.faq-translations.trashed');
            Route::patch('/backend/faq-translations/restore/{uuid}', 'backend_faq_translations_restore')->middleware(['permission:faq-translations.restore'])->name('backend.faq-translations.restore');
            Route::delete('/backend/faq-translations/force-destroy/{uuid}', 'backend_faq_translations_force_destroy')->middleware(['permission:faq-translations.force_destroy'])->name('backend.faq-translations.force_destroy');
        });

        // Routes pour : Gestion des Abonnés (Newsletter)
        Route::controller(V1SubscribeController::class)->group(function () {
            Route::get('/backend/subscribes/index', 'backend_subscribes_index')->middleware(['permission:subscribes.index'])->name('backend.subscribes.index');
            Route::get('/backend/subscribes/show/{subscribe}', 'backend_subscribes_show')->middleware(['permission:subscribes.show'])->name('backend.subscribes.show');
            Route::get('/backend/subscribes/audits/{subscribe}', 'backend_subscribes_audits')->middleware(['permission:subscribes.audits'])->name('backend.subscribes.audits');
            Route::get('/backend/subscribes/activity-logs/{subscribe}', 'backend_subscribes_activity_logs')->middleware(['permission:subscribes.activity_logs'])->name('backend.subscribes.activity-logs');
            Route::patch('/backend/subscribes/activate/{subscribe}', 'backend_subscribes_activate')->middleware(['permission:subscribes.activate'])->name('backend.subscribes.activate');
            Route::patch('/backend/subscribes/deactivate/{subscribe}', 'backend_subscribes_deactivate')->middleware(['permission:subscribes.deactivate'])->name('backend.subscribes.deactivate');
            Route::delete('/backend/subscribes/destroy/{subscribe}', 'backend_subscribes_destroy')->middleware(['permission:subscribes.destroy'])->name('backend.subscribes.destroy');
            Route::get('/backend/subscribes/trashed', 'backend_subscribes_trashed')->middleware(['permission:subscribes.trashed'])->name('backend.subscribes.trashed');
            Route::patch('/backend/subscribes/restore/{uuid}', 'backend_subscribes_restore')->middleware(['permission:subscribes.restore'])->name('backend.subscribes.restore');
            Route::delete('/backend/subscribes/force-destroy/{uuid}', 'backend_subscribes_force_destroy')->middleware(['permission:subscribes.force_destroy'])->name('backend.subscribes.force_destroy');
        });
        Route::controller(V1SubscribeHistoryController::class)->group(function () {
            Route::get('/backend/subscribe-histories/index', 'backend_subscribes_histories_index')->middleware(['permission:subscribe-histories.index'])->name('backend.subscribe-histories.index');
            Route::get('/backend/subscribe-histories/show/{subscribeHistory}', 'backend_subscribes_histories_show')->middleware(['permission:subscribe-histories.show'])->name('backend.subscribe-histories.show');
            Route::delete('/backend/subscribe-histories/destroy/{subscribeHistory}', 'backend_subscribes_histories_destroy')->middleware(['permission:subscribe-histories.destroy'])->name('backend.subscribe-histories.destroy');
            Route::get('/backend/subscribe-histories/trashed', 'backend_subscribes_histories_trashed')->middleware(['permission:subscribe-histories.trashed'])->name('backend.subscribe-histories.trashed');
            Route::patch('/backend/subscribe-histories/restore/{uuid}', 'backend_subscribes_histories_restore')->middleware(['permission:subscribe-histories.restore'])->name('backend.subscribe-histories.restore');
            Route::delete('/backend/subscribe-histories/force-destroy/{uuid}', 'backend_subscribes_histories_force_destroy')->middleware(['permission:subscribe-histories.force_destroy'])->name('backend.subscribe-histories.force_destroy');
        });

        // Routes pour : Gestion des Plateformes Cloud
        Route::controller(V1CloudPlatformController::class)->group(function () {
            Route::get('/backend/cloud-platforms/index', 'backend_cloud_platforms_index')->middleware(['permission:cloud-platforms.index'])->name('backend.cloud-platforms.index');
            Route::get('/backend/cloud-platforms/create', 'backend_cloud_platforms_create')->middleware(['permission:cloud-platforms.create'])->name('backend.cloud-platforms.create');
            Route::post('/backend/cloud-platforms/store', 'backend_cloud_platforms_store')->middleware(['permission:cloud-platforms.store'])->name('backend.cloud-platforms.store');
            Route::get('/backend/cloud-platforms/show/{cloudPlatform}/{slug?}', 'backend_cloud_platforms_show')->middleware(['permission:cloud-platforms.show'])->name('backend.cloud-platforms.show');
            Route::get('/backend/cloud-platforms/folders/{cloudPlatform}/{slug?}', 'backend_cloud_platforms_folders')->middleware(['permission:cloud-platforms.show'])->name('backend.cloud-platforms.folders');
            Route::get('/backend/cloud-platforms/files/{cloudPlatform}/{slug?}', 'backend_cloud_platforms_files')->middleware(['permission:cloud-platforms.show'])->name('backend.cloud-platforms.files');
            Route::get('/backend/cloud-platforms/audits/{cloudPlatform}/{slug?}', 'backend_cloud_platforms_audits')->middleware(['permission:cloud-platforms.audits'])->name('backend.cloud-platforms.audits');
            Route::get('/backend/cloud-platforms/activity-logs/{cloudPlatform}/{slug?}', 'backend_cloud_platforms_activity_logs')->middleware(['permission:cloud-platforms.activity_logs'])->name('backend.cloud-platforms.activity-logs');
            Route::get('/backend/cloud-platforms/edit/{cloudPlatform}', 'backend_cloud_platforms_edit')->middleware(['permission:cloud-platforms.edit'])->name('backend.cloud-platforms.edit');
            Route::put('/backend/cloud-platforms/update/{cloudPlatform}', 'backend_cloud_platforms_update')->middleware(['permission:cloud-platforms.update'])->name('backend.cloud-platforms.update');
            Route::patch('/backend/cloud-platforms/toggle-active/{cloudPlatform}', 'backend_cloud_platforms_toggle_active')->middleware(['permission:cloud-platforms.toggle_active'])->name('backend.cloud-platforms.toggle_active');
            Route::delete('/backend/cloud-platforms/destroy/{cloudPlatform}', 'backend_cloud_platforms_destroy')->middleware(['permission:cloud-platforms.destroy'])->name('backend.cloud-platforms.destroy');
            Route::get('/backend/cloud-platforms/trashed', 'backend_cloud_platforms_trashed')->middleware(['permission:cloud-platforms.trashed'])->name('backend.cloud-platforms.trashed');
            Route::patch('/backend/cloud-platforms/restore/{uuid}', 'backend_cloud_platforms_restore')->middleware(['permission:cloud-platforms.restore'])->name('backend.cloud-platforms.restore');
            Route::delete('/backend/cloud-platforms/force-destroy/{uuid}', 'backend_cloud_platforms_force_destroy')->middleware(['permission:cloud-platforms.force_destroy'])->name('backend.cloud-platforms.force_destroy');
            Route::post('/backend/cloud-platforms/{cloudPlatform}/architecture-store', 'backend_cloud_platforms_architecture_store')->middleware(['permission:cloud-platforms.architecture_store'])->name('backend.cloud-platforms.architecture_store');
        });

        // Routes pour : Gestion des Dossiers Cloud
        Route::controller(V1CloudFolderController::class)->group(function () {
            Route::get('/backend/cloud-folders/index', 'backend_cloud_folders_index')->middleware(['permission:cloud-folders.index'])->name('backend.cloud-folders.index');
            Route::get('/backend/cloud-folders/show/{cloudFolder}', 'backend_cloud_folders_show')->middleware(['permission:cloud-folders.show'])->name('backend.cloud-folders.show');
            Route::get('/backend/cloud-folders/files/{cloudFolder}', 'backend_cloud_folders_files')->middleware(['permission:cloud-folders.show'])->name('backend.cloud-folders.files');
            Route::get('/backend/cloud-folders/subfolders/{cloudFolder}', 'backend_cloud_folders_subfolders')->middleware(['permission:cloud-folders.show'])->name('backend.cloud-folders.subfolders');
            Route::get('/backend/cloud-folders/audits/{cloudFolder}', 'backend_cloud_folders_audits')->middleware(['permission:cloud-folders.audits'])->name('backend.cloud-folders.audits');
            Route::get('/backend/cloud-folders/activity-logs/{cloudFolder}', 'backend_cloud_folders_activity_logs')->middleware(['permission:cloud-folders.activity_logs'])->name('backend.cloud-folders.activity-logs');
            Route::get('/backend/cloud-folders/edit/{cloudFolder}', 'backend_cloud_folders_edit')->middleware(['permission:cloud-folders.edit'])->name('backend.cloud-folders.edit');
            Route::put('/backend/cloud-folders/update/{cloudFolder}', 'backend_cloud_folders_update')->middleware(['permission:cloud-folders.update'])->name('backend.cloud-folders.update');
            Route::patch('/backend/cloud-folders/toggle-active/{cloudFolder}', 'backend_cloud_folders_toggle_active')->middleware(['permission:cloud-folders.toggle_active'])->name('backend.cloud-folders.toggle_active');
        });

        // Routes pour : Fichiers Cloud
        Route::controller(V1CloudFileController::class)->group(function () {
            Route::get('/backend/cloud-files/index', 'backend_cloud_files_index')->middleware(['permission:cloud-files.index'])->name('backend.cloud-files.index');
            Route::get('/backend/cloud-files/show/{cloudFile}', 'backend_cloud_files_show')->middleware(['permission:cloud-files.show'])->name('backend.cloud-files.show');
            Route::get('/backend/cloud-files/audits/{cloudFile}', 'backend_cloud_files_audits')->middleware(['permission:cloud-files.audits'])->name('backend.cloud-files.audits');
            Route::get('/backend/cloud-files/activity-logs/{cloudFile}', 'backend_cloud_files_activity_logs')->middleware(['permission:cloud-files.activity_logs'])->name('backend.cloud-files.activity-logs');
            Route::delete('/backend/cloud-files/destroy/{cloudFile}', 'backend_cloud_files_destroy')->middleware(['permission:cloud-files.destroy'])->name('backend.cloud-files.destroy');
            // Synchronisation des fichiers cloud non synchronisés
            Route::get('/backend/cloud-files/sync', 'backend_cloud_files_sync_index')->middleware(['permission:cloud-files.index'])->name('backend.cloud-files.sync');
            Route::post('/backend/cloud-files/sync-selected', 'backend_cloud_files_sync_selected')->middleware(['permission:cloud-files.index'])->name('backend.cloud-files.sync-selected');
            Route::delete('/backend/cloud-files/force-destroy/{uuid}', 'backend_cloud_files_force_destroy')->middleware(['permission:cloud-files.force_destroy'])->name('backend.cloud-files.force_destroy');
            Route::get('/backend/cloud-files/trashed', 'backend_cloud_files_trashed')->middleware(['permission:cloud-files.trashed'])->name('backend.cloud-files.trashed');
            Route::patch('/backend/cloud-files/restore/{uuid}', 'backend_cloud_files_restore')->middleware(['permission:cloud-files.restore'])->name('backend.cloud-files.restore');
        });

        // Routes pour : Gestion des Contacts (Messages reçus)
        Route::controller(V1ContactController::class)->group(function () {
            Route::get('/backend/contacts/index', 'backend_contacts_index')->middleware(['permission:contacts.index'])->name('backend.contacts.index');
            Route::get('/backend/contacts/show/{contact}', 'backend_contacts_show')->middleware(['permission:contacts.show'])->name('backend.contacts.show');
            Route::get('/backend/contacts/audits/{contact}', 'backend_contacts_audits')->middleware(['permission:contacts.audits'])->name('backend.contacts.audits');
            Route::get('/backend/contacts/activity-logs/{contact}', 'backend_contacts_activity_logs')->middleware(['permission:contacts.activity_logs'])->name('backend.contacts.activity-logs');
            Route::patch('/backend/contacts/mark-as-read/{contact}', 'backend_contacts_mark_as_read')->middleware(['permission:contacts.mark_as_read'])->name('backend.contacts.mark_as_read');
            Route::patch('/backend/contacts/mark-as-unread/{contact}', 'backend_contacts_mark_as_unread')->middleware(['permission:contacts.mark_as_unread'])->name('backend.contacts.mark_as_unread');
            Route::delete('/backend/contacts/destroy/{contact}', 'backend_contacts_destroy')->middleware(['permission:contacts.destroy'])->name('backend.contacts.destroy');
            Route::get('/backend/contacts/trashed', 'backend_contacts_trashed')->middleware(['permission:contacts.trashed'])->name('backend.contacts.trashed');
            Route::patch('/backend/contacts/restore/{uuid}', 'backend_contacts_restore')->middleware(['permission:contacts.restore'])->name('backend.contacts.restore');
            Route::delete('/backend/contacts/force-destroy/{uuid}', 'backend_contacts_force_destroy')->middleware(['permission:contacts.force_destroy'])->name('backend.contacts.force_destroy');
        });

        // Routes pour : Gestion des Audits
        Route::controller(V1AuditController::class)->group(function () {
            Route::get('/backend/audits/index', 'backend_audits_index')->middleware(['permission:audits.index'])->name('backend.audits.index');
            Route::get('/backend/audits/show/{audit}', 'backend_audits_show')->middleware(['permission:audits.show'])->name('backend.audits.show');
            Route::delete('/backend/audits/destroy/{audit}', 'backend_audits_destroy')->middleware(['permission:audits.destroy'])->name('backend.audits.destroy');
            Route::delete('/backend/audits/clear', 'backend_audits_clear')->middleware(['permission:audits.clear'])->name('backend.audits.clear');
        });

        // Routes pour : Gestion des Journaux d'Activité
        Route::controller(V1ActivityLogController::class)->group(function () {
            Route::get('/backend/activity-logs/index', 'backend_activity_logs_index')->middleware(['permission:activity-logs.index'])->name('backend.activity-logs.index');
            Route::get('/backend/activity-logs/show/{activityLog}', 'backend_activity_logs_show')->middleware(['permission:activity-logs.show'])->name('backend.activity-logs.show');
            Route::delete('/backend/activity-logs/destroy/{activityLog}', 'backend_activity_logs_destroy')->middleware(['permission:activity-logs.destroy'])->name('backend.activity-logs.destroy');
            Route::delete('/backend/activity-logs/clear', 'backend_activity_logs_clear')->middleware(['permission:activity-logs.clear'])->name('backend.activity-logs.clear');
        });

        // Routes pour : Gestion des Paramètres (Settings)
        Route::controller(V1SettingController::class)->group(function () {
            Route::get('/backend/settings/index', 'backend_settings_index')->middleware(['permission:settings.index'])->name('backend.settings.index');
            Route::get('/backend/settings/audits', 'backend_settings_audits')->middleware(['permission:settings.audits'])->name('backend.settings.audits');
            Route::get('/backend/settings/activity-logs', 'backend_settings_activity_logs')->middleware(['permission:settings.activity_logs'])->name('backend.settings.activity-logs');
            Route::put('/backend/settings/update', 'backend_settings_update')->middleware(['permission:settings.update'])->name('backend.settings.update');
        });

    });

    // Route frontend ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // /////////////////// Route frontend ////////////////////////////////////////////////////////////////////////////////////////////
    // //////////////////////////////////////// Route frontend ///////////////////////////////////////////////////////////////////////
    // //////////////////////////////////////////////////////////// Route frontend ///////////////////////////////////////////////////
    // //////////////////////////////////////////////////////////////////////////////// Route frontend ///////////////////////////////
    Route::controller(V1FrontendController::class)->group(function () {
        Route::get('/', 'frontend_index')->name('frontend.index');

        // Module 10 : sélecteur de devise du frontend (utilisateur connecté → users.currency_id ; visiteur → session)
        Route::post('/currency/switch', 'frontend_currencies_switch')->name('frontend.currencies.switch');
    });

}

// ─── Version 2 ───────────────────────────────────────────
if ($version && $version->code === 'v2') {

    Route::controller(V2FrontendController::class)->group(function () {
        Route::get('/', 'frontend_index')->name('frontend.index');
    });

}
