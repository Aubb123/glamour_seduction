<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ClearAllTablesSeeder::class,
            CloudPlatformSeeder::class,
            CloudFolderSeeder::class,
            LangSeeder::class,
            PlatformVersionSeeder::class,
            PlatformVersionTranslationSeeder::class,
            SettingSeeder::class,
            SettingTranslationSeeder::class,
            GeoContinentSeeder::class,
            GeoContinentTranslationSeeder::class,
            GeoCountrySeeder::class,
            GeoCountryTranslationSeeder::class,
            RoleSeeder::class,
            RoleTranslationSeeder::class,
            PermissionSeeder::class,
            UserSeeder::class,

            BlogCategorySeeder::class,
            BlogCategoryTranslationSeeder::class,
            BlogTagSeeder::class,
            BlogTagTranslationSeeder::class,
            BlogPostSeeder::class,
            BlogPostTranslationSeeder::class,
            BlogPostCommentSeeder::class,
            BlogPostCommentReplySeeder::class,

            ContactSeeder::class,
            SubscribeSeeder::class,
            SubscribeHistorySeeder::class,
            FaqSeeder::class,
            FaqTranslationSeeder::class,
            GallerySeeder::class,
            GalleryTranslationSeeder::class,
        ]);

    }
}
