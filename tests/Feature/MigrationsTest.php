<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

/**
 * Étape 2 — Migrations.
 *
 * Valide que le clone reproduit bien le schéma de `base` (contrat 1:1) :
 *  - toutes les tables métier sont créées ;
 *  - les colonnes clés (uuid, enum status, softDeletes, contraintes) sont présentes.
 */
it('crée toutes les tables du schéma de base', function () {
    $expectedTables = [
        'langs', 'platform_versions', 'platform_version_translations',
        'geo_continents', 'geo_continent_translations',
        'geo_countries', 'geo_country_translations',
        'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs',
        'roles', 'permissions', 'model_has_permissions', 'model_has_roles', 'role_has_permissions',
        'role_translations', 'users', 'password_reset_tokens', 'account_reset_tokens', 'sessions',
        'settings', 'setting_translations',
        'cloud_platforms', 'cloud_folders', 'cloud_files',
        'blog_categories', 'blog_category_translations',
        'blog_tags', 'blog_tag_translations',
        'blog_posts', 'blog_post_translations', 'blog_post_tag',
        'blog_post_comments', 'blog_post_comment_replies',
        'contacts', 'subscribes', 'subscribe_histories',
        'faqs', 'faq_translations',
        'galleries', 'gallery_translations',
        'audits', 'activity_logs',
        'product_categories', 'product_category_translations',
        'product_brands', 'product_brand_translations',
        'product_tags', 'product_tag_translations',
        'product_attributes', 'product_attribute_translations',
        'product_attribute_values', 'product_attribute_value_translations',
        'products', 'product_translations', 'product_tag',
        'product_variants', 'product_variant_attribute_value',
        'product_warehouses', 'product_warehouse_translations',
        'product_warehouse_stocks',
        'product_stock_movements',
        'shops', 'shop_translations', 'shop_warehouse',
        'positions', 'position_translations', 'shop_users', 'warehouse_users',
    ];

    $actualTables = Schema::getTableListing(null, false);

    sort($actualTables);

    foreach ($expectedTables as $table) {
        expect(in_array($table, $actualTables, true))->toBeTrue("La table « {$table} » n'existe pas");
    }
});

it('reproduit les colonnes clés du contrat de schéma', function () {
    // UUID + softDeletes + enum status sur les entités principales
    Schema::hasColumn('langs', 'uuid') === true;
    expect(Schema::hasColumn('langs', 'uuid'))->toBeTrue();
    expect(Schema::hasColumn('langs', 'deleted_at'))->toBeTrue();

    expect(Schema::hasColumn('users', 'uuid'))->toBeTrue();
    expect(Schema::hasColumn('users', 'country_id'))->toBeTrue();
    expect(Schema::hasColumn('users', 'slug'))->toBeTrue();
    expect(Schema::hasColumn('users', 'deleted_at'))->toBeTrue();

    expect(Schema::hasColumn('roles', 'uuid'))->toBeTrue();
    expect(Schema::hasColumn('roles', 'status'))->toBeTrue();
    expect(Schema::hasColumn('roles', 'deleted_at'))->toBeTrue();

    expect(Schema::hasColumn('permissions', 'uuid'))->toBeTrue();
    expect(Schema::hasColumn('permissions', 'status'))->toBeTrue();

    expect(Schema::hasColumn('geo_countries', 'type'))->toBeTrue();
    expect(Schema::hasColumn('geo_countries', 'code'))->toBeTrue();

    expect(Schema::hasColumn('blog_posts', 'status'))->toBeTrue();
    expect(Schema::hasColumn('blog_posts', 'published_at'))->toBeTrue();

    expect(Schema::hasColumn('cloud_files', 'sync_status'))->toBeTrue();
    expect(Schema::hasColumn('cloud_files', 'fileable_type'))->toBeTrue();
    expect(Schema::hasColumn('cloud_files', 'fileable_id'))->toBeTrue();

    expect(Schema::hasColumn('audits', 'auditable_type'))->toBeTrue();
    expect(Schema::hasColumn('audits', 'action'))->toBeTrue();

    expect(Schema::hasColumn('activity_logs', 'loggable_type'))->toBeTrue();
    expect(Schema::hasColumn('activity_logs', 'is_success'))->toBeTrue();

    expect(Schema::hasColumn('shops', 'uuid'))->toBeTrue();
    expect(Schema::hasColumn('shops', 'code'))->toBeTrue();
    expect(Schema::hasColumn('shops', 'status'))->toBeTrue();
    expect(Schema::hasColumn('shops', 'is_default'))->toBeTrue();
    expect(Schema::hasColumn('shops', 'deleted_at'))->toBeTrue();

    expect(Schema::hasColumn('shop_translations', 'shop_id'))->toBeTrue();
    expect(Schema::hasColumn('shop_translations', 'slug'))->toBeTrue();

    expect(Schema::hasColumn('shop_warehouse', 'shop_id'))->toBeTrue();
    expect(Schema::hasColumn('shop_warehouse', 'warehouse_id'))->toBeTrue();
    expect(Schema::hasColumn('shop_warehouse', 'is_primary'))->toBeTrue();

    expect(Schema::hasColumn('positions', 'uuid'))->toBeTrue();
    expect(Schema::hasColumn('positions', 'code'))->toBeTrue();
    expect(Schema::hasColumn('positions', 'context'))->toBeTrue();
    expect(Schema::hasColumn('positions', 'status'))->toBeTrue();
    expect(Schema::hasColumn('positions', 'deleted_at'))->toBeTrue();

    expect(Schema::hasColumn('position_translations', 'position_id'))->toBeTrue();
    expect(Schema::hasColumn('position_translations', 'slug'))->toBeTrue();

    expect(Schema::hasColumn('shop_users', 'shop_id'))->toBeTrue();
    expect(Schema::hasColumn('shop_users', 'user_id'))->toBeTrue();
    expect(Schema::hasColumn('shop_users', 'position_id'))->toBeTrue();
    expect(Schema::hasColumn('shop_users', 'is_manager'))->toBeTrue();

    expect(Schema::hasColumn('warehouse_users', 'warehouse_id'))->toBeTrue();
    expect(Schema::hasColumn('warehouse_users', 'user_id'))->toBeTrue();
    expect(Schema::hasColumn('warehouse_users', 'position_id'))->toBeTrue();
    expect(Schema::hasColumn('warehouse_users', 'is_manager'))->toBeTrue();

    // Entrepôts : données non traduites uniquement sur la table principale
    // (nom/slug/adresse/ville/téléphone portés par product_warehouse_translations).
    // La colonne globale is_default a disparu : « entrepôt par défaut » est une
    // notion PAR BOUTIQUE (pivot shop_warehouse.is_primary).
    expect(Schema::hasColumn('product_warehouses', 'uuid'))->toBeTrue();
    expect(Schema::hasColumn('product_warehouses', 'code'))->toBeTrue();
    expect(Schema::hasColumn('product_warehouses', 'status'))->toBeTrue();
    expect(Schema::hasColumn('product_warehouses', 'is_default'))->toBeFalse();
    expect(Schema::hasColumn('product_warehouses', 'name'))->toBeFalse();
    expect(Schema::hasColumn('product_warehouses', 'address'))->toBeFalse();
    expect(Schema::hasColumn('product_warehouses', 'city'))->toBeFalse();
    expect(Schema::hasColumn('product_warehouses', 'phone'))->toBeFalse();
    expect(Schema::hasColumn('product_warehouses', 'deleted_at'))->toBeTrue();

    expect(Schema::hasColumn('product_warehouse_translations', 'warehouse_id'))->toBeTrue();
    expect(Schema::hasColumn('product_warehouse_translations', 'lang_id'))->toBeTrue();
    expect(Schema::hasColumn('product_warehouse_translations', 'name'))->toBeTrue();
    expect(Schema::hasColumn('product_warehouse_translations', 'slug'))->toBeTrue();
    expect(Schema::hasColumn('product_warehouse_translations', 'address'))->toBeTrue();
    expect(Schema::hasColumn('product_warehouse_translations', 'city'))->toBeTrue();
    expect(Schema::hasColumn('product_warehouse_translations', 'phone'))->toBeTrue();

    // Contraintes uniques de traduction : un seul (name/lang_id) par langue
    $indexes = Schema::getIndexes('role_translations');
    expect($indexes)->toBeArray();

    $uniqueIndexes = array_column($indexes, 'columns', 'name');
    expect($uniqueIndexes)->not->toBeEmpty();
});

it('exécute exactement 35 migrations (le fichier .txt n’est pas une migration)', function () {
    $status = Artisan::call('migrate:status', ['--no-ansi' => true]);
    expect($status)->toBe(0);
});
