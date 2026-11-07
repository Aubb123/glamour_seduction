<?php

declare(strict_types=1);

use Illuminate\Filesystem\Filesystem;

/**
 * Étape 4 — Seeders : présence des seeders attendus (contrat 1:1 avec base + modules Boutique).
 */
it('contient les 57 seeders attendus', function () {
    $filesystem = new Filesystem;

    $seeders = collect($filesystem->files(database_path('seeders')))
        ->map(fn ($file) => $file->getFilename())
        ->filter(fn ($name) => str_ends_with($name, '.php'))
        ->map(fn ($name) => substr($name, 0, -4))
        ->sort()
        ->values()
        ->all();

    expect($seeders)->toHaveCount(57);

    $expected = [
        'ActivityLogSeeder', 'AuditSeeder',
        'BlogCategorySeeder', 'BlogCategoryTranslationSeeder',
        'BlogPostCommentReplySeeder', 'BlogPostCommentSeeder',
        'BlogPostSeeder', 'BlogPostTranslationSeeder',
        'BlogTagSeeder', 'BlogTagTranslationSeeder',
        'ClearAllTablesSeeder',
        'CurrencySeeder', 'CurrencyTranslationSeeder',
        'CloudFileSeeder', 'CloudFolderSeeder', 'CloudPlatformSeeder',
        'ContactSeeder', 'DatabaseSeeder',
        'FaqSeeder', 'FaqTranslationSeeder',
        'GallerySeeder', 'GalleryTranslationSeeder',
        'GeoContinentSeeder', 'GeoContinentTranslationSeeder',
        'GeoCountrySeeder', 'GeoCountryTranslationSeeder',
        'LangSeeder',
        'PermissionSeeder',
        'PlatformVersionSeeder', 'PlatformVersionTranslationSeeder',
        'PositionSeeder',
        'ProductAttributeSeeder', 'ProductAttributeTranslationSeeder',
        'ProductAttributeValueSeeder', 'ProductAttributeValueTranslationSeeder',
        'ProductBrandSeeder', 'ProductBrandTranslationSeeder',
        'ProductCategorySeeder', 'ProductCategoryTranslationSeeder',
        'ProductTagSeeder', 'ProductTagTranslationSeeder',
        'ProductTranslationSeeder', 'ProductSeeder',
        'ProductVariantSeeder',
        'ProductWarehouseSeeder', 'ProductWarehouseTranslationSeeder',
        'ProductWarehouseStockSeeder', 'ProductStockMovementSeeder',
        'RoleSeeder', 'RoleTranslationSeeder',
        'SettingSeeder', 'SettingTranslationSeeder',
        'ShopSeeder', 'ShopTranslationSeeder',
        'SubscribeHistorySeeder', 'SubscribeSeeder',
        'UserSeeder',
    ];

    sort($expected);

    expect($seeders)->toBe($expected);
});
