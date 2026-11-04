<?php

declare(strict_types=1);

use App\Models\ActivityLog;
use App\Models\Audit;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
use App\Models\BlogCategory;
use App\Models\BlogCategoryTranslation;
use App\Models\BlogPost;
use App\Models\BlogPostComment;
use App\Models\BlogPostCommentReply;
use App\Models\BlogPostTranslation;
use App\Models\BlogTag;
use App\Models\BlogTagTranslation;
use App\Models\CloudFile;
use App\Models\CloudFolder;
use App\Models\CloudPlatform;
use App\Models\Contact;
use App\Models\Faq;
use App\Models\FaqTranslation;
use App\Models\Gallery;
use App\Models\GalleryTranslation;
use App\Models\GeoContinent;
use App\Models\GeoContinentTranslation;
use App\Models\GeoCountry;
use App\Models\GeoCountryTranslation;
use App\Models\Lang;
use App\Models\Permission;
use App\Models\PlatformVersion;
use App\Models\PlatformVersionTranslation;
use App\Models\Role;
use App\Models\RoleTranslation;
use App\Models\Setting;
use App\Models\SettingTranslation;
use App\Models\Subscribe;
use App\Models\SubscribeHistory;
use App\Models\User;
use App\Traits\HasAuditModelBehaviors;
use App\Traits\HasModelBehaviors;
use Illuminate\Support\Facades\Schema;

it('les 33 modèles du projet existent', function () {
    $models = [
        ActivityLog::class, Audit::class,
        BlogCategory::class, BlogCategoryTranslation::class,
        BlogPost::class, BlogPostComment::class, BlogPostCommentReply::class,
        BlogPostTranslation::class,
        BlogTag::class, BlogTagTranslation::class,
        CloudFile::class, CloudFolder::class, CloudPlatform::class,
        Contact::class,
        Faq::class, FaqTranslation::class,
        Gallery::class, GalleryTranslation::class,
        GeoContinent::class, GeoContinentTranslation::class,
        GeoCountry::class, GeoCountryTranslation::class,
        Lang::class,
        Permission::class,
        PlatformVersion::class, PlatformVersionTranslation::class,
        Role::class, RoleTranslation::class,
        Setting::class, SettingTranslation::class,
        Subscribe::class, SubscribeHistory::class,
        User::class,
    ];

    expect($models)->toHaveCount(33);

    foreach ($models as $model) {
        expect(class_exists($model))->toBeTrue("Le modèle {$model} n'existe pas");
    }
});

it('les modèles utilisent les traits de comportements attendus et une clé de route uuid', function () {
    // Les modèles « logs » (ActivityLog, Audit) utilisent un trait allégé
    // HasAuditModelBehaviors, tous les autres utilisent HasModelBehaviors.
    $logModels = [ActivityLog::class, Audit::class];

    $entityModels = array_values(array_filter([
        BlogCategory::class, BlogCategoryTranslation::class,
        BlogPost::class, BlogPostComment::class, BlogPostCommentReply::class,
        BlogPostTranslation::class,
        BlogTag::class, BlogTagTranslation::class,
        CloudFile::class, CloudFolder::class, CloudPlatform::class,
        Contact::class,
        Faq::class, FaqTranslation::class,
        Gallery::class, GalleryTranslation::class,
        GeoContinent::class, GeoContinentTranslation::class,
        GeoCountry::class, GeoCountryTranslation::class,
        Lang::class,
        Permission::class,
        PlatformVersion::class, PlatformVersionTranslation::class,
        Role::class, RoleTranslation::class,
        Setting::class, SettingTranslation::class,
        Subscribe::class, SubscribeHistory::class,
        User::class,
    ], fn ($m) => ! in_array($m, $logModels, true)));

    foreach ($logModels as $model) {
        expect(in_array(
            HasAuditModelBehaviors::class,
            class_uses_recursive($model),
            true
        ))->toBeTrue("{$model} n'utilise pas HasAuditModelBehaviors");
    }

    foreach ($entityModels as $model) {
        expect(in_array(
            HasModelBehaviors::class,
            class_uses_recursive($model),
            true
        ))->toBeTrue("{$model} n'utilise pas HasModelBehaviors");
    }

    foreach (array_merge($logModels, $entityModels) as $model) {
        $expectedKey = in_array($model, [
            // Ces modèles de base ne surchargent PAS getRouteKeyName()
            // (binding implicite par 'id' pour les pages qui les concernent).
            BlogCategoryTranslation::class,
            BlogTagTranslation::class,
            FaqTranslation::class,
            GalleryTranslation::class,
            Setting::class,
            SettingTranslation::class,
        ], true) ? 'id' : 'uuid';

        expect((new $model)->getRouteKeyName())->toBe($expectedKey);
        expect(Schema::hasColumn((new $model)->getTable(), 'uuid'))
            ->toBeTrue("{$model} : colonne uuid absente");
    }
});
