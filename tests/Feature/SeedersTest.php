<?php

declare(strict_types=1);

use Illuminate\Filesystem\Filesystem;

/**
 * Étape 4 — Seeders : présence des 35 seeders (contrat 1:1 avec base).
 */
it('contient les 35 seeders de base', function () {
    $filesystem = new Filesystem;

    $seeders = collect($filesystem->files(database_path('seeders')))
        ->map(fn ($file) => $file->getFilename())
        ->filter(fn ($name) => str_ends_with($name, '.php'))
        ->map(fn ($name) => substr($name, 0, -4))
        ->sort()
        ->values()
        ->all();

    expect($seeders)->toHaveCount(37);

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
        'RoleSeeder', 'RoleTranslationSeeder',
        'SettingSeeder', 'SettingTranslationSeeder',
        'SubscribeHistorySeeder', 'SubscribeSeeder',
        'UserSeeder',
    ];

    sort($expected);

    expect($seeders)->toBe($expected);
});
