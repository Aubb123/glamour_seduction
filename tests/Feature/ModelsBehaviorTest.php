<?php

declare(strict_types=1);

use App\Enums\Blogs\Posts\BlogPostStatus;
use App\Enums\Users\UserStatus;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\CloudFolder;
use App\Models\CloudPlatform;
use App\Models\Faq;
use App\Models\Gallery;
use App\Models\GeoContinent;
use App\Models\GeoCountry;
use App\Models\Lang;
use App\Models\Permission;
use App\Models\PlatformVersion;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Helpers de données (les factories de base ont des définitions vides)
|--------------------------------------------------------------------------
*/
function makeContinent(string $code = 'AF'): GeoContinent
{
    static $i = 0;
    $i++;

    return GeoContinent::create([
        'code' => $code.$i,
        'population' => 1000000,
        'area_km2' => 100000,
        'file_path' => 'continents/'.$i.'.svg',
        'sort_order' => $i,
        'status' => 'active',
    ]);
}

function makeCountry(int $continentId, string $code2 = 'BJ'): GeoCountry
{
    static $i = 0;
    $i++;

    $unique = substr(md5(uniqid((string) $i, true)), 0, 6);

    return GeoCountry::create([
        'continent_id' => $continentId,
        'code' => $code2.$unique,
        'code_2' => $code2.$unique,
        'file_path' => 'flags/'.$i.'.svg',
        'sort_order' => $i,
        'status' => 'active',
    ]);
}

function makeUser(int $countryId, string $email = 'user@example.com'): User
{
    static $i = 0;
    $i++;

    return User::create([
        'country_id' => $countryId,
        'first_name' => 'User'.$i,
        'last_name' => 'Test'.$i,
        'email' => $email.$i,
        'gender' => 'male',
        'password' => 'secret1234',
        'status' => UserStatus::Active,
    ]);
}

function makePlatform(): CloudPlatform
{
    static $i = 0;
    $i++;

    return CloudPlatform::create([
        'name' => 'backblaze_b2',
        'disk_name' => 'b2',
        'label' => 'Backblaze B2 '.$i,
        'api_endpoint' => 'https://s3.us-west-004.backblazeb2.com',
        'api_key' => 'key'.$i,
        'api_secret' => 'secret'.$i,
        'description' => 'Test platform '.$i,
        'status' => 'active',
    ]);
}

function makeFolder(int $platformId, string $name, ?int $parentId = null): CloudFolder
{
    return CloudFolder::create([
        'cloud_platform_id' => $platformId,
        'cloud_folder_id' => $parentId,
        'folder_name' => $name,
        'path' => '/'.$name,
        'status' => 'active',
    ]);
}

/**
 * Étape 3 — Comportements métier des modèles du clone.
 */
it('génère un uuid ordonné à la création (Lang)', function () {
    $lang = Lang::factory()->create();

    expect($lang->uuid)->not->toBeNull();
    expect($lang->uuid)->toMatch('/^[0-9]{8}-[0-9]{4}-[0-9]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/');
    expect($lang->getRouteKey() === $lang->uuid)->toBeTrue();
});

it('expose les scopes actifs et la langue par défaut (Lang)', function () {
    $en = Lang::factory()->create(['code' => 'en', 'is_default' => true, 'is_active' => true]);
    Lang::factory()->create(['code' => 'fr', 'is_active' => false]);

    expect(Lang::active()->count())->toBe(1);
    expect(Lang::default()->count())->toBe(1);
    expect(Lang::default()->first()->code)->toBe('en');
});

it('crée un continent et un pays avec leurs relations et scopes', function () {
    $africa = makeContinent();
    $benin = makeCountry($africa->id);

    expect($benin->continent()->first()->code)->toBe($africa->code);
    expect($africa->countries()->count())->toBe(1);
    expect(GeoCountry::active()->count())->toBe(1);
    expect(GeoContinent::active()->count())->toBe(1);
});

it('détermine la clé de route par uuid pour les modèles géo', function () {
    expect((new GeoContinent)->getRouteKeyName())->toBe('uuid');
    expect((new GeoCountry)->getRouteKeyName())->toBe('uuid');
});

it('crée un utilisateur avec slug automatique dérivé du prénom+nom', function () {
    $country = makeCountry(makeContinent()->id);
    $user = makeUser($country->id, 'jean.dupont@example.com');

    expect($user->uuid)->not->toBeNull();
    expect($user->slug)->toBe('user'.$user->id.'-test'.$user->id);
    expect($user->first_last)->toBe('User'.$user->id.' Test'.$user->id);
    expect($user->status)->toBeInstanceOf(UserStatus::class);
    expect($user->status->value)->toBe('active');

    expect(User::active()->count())->toBe(1);
    expect(User::notActive()->count())->toBe(0);
});

it('active/désactive et masque les secrets d’une plateforme cloud', function () {
    $platform = makePlatform();

    $array = $platform->toArray();
    expect($array)->not->toHaveKey('api_key');
    expect($array)->not->toHaveKey('api_secret');

    expect(CloudPlatform::active()->count())->toBe(1);
});

it('gère la hiérarchie des dossiers cloud et leurs fichiers', function () {
    $platform = makePlatform();
    $root = makeFolder($platform->id, 'root');
    $child = makeFolder($platform->id, 'images', $root->id);

    expect($root->children()->count())->toBe(1);
    expect($child->parent()->first()->id)->toBe($root->id);
    expect($platform->folders()->count())->toBe(2);
});

it('crée un rôle et une permission Spatie avec uuid', function () {
    $role = Role::create(['name' => 'administrator'.uniqid(), 'guard_name' => 'web', 'status' => 'active']);
    $permission = Permission::create(['name' => 'view dashboard', 'guard_name' => 'web', 'status' => 'active']);

    expect($role->uuid)->not->toBeNull();
    expect($permission->uuid)->not->toBeNull();

    $role->givePermissionTo($permission->name);
    expect($role->hasPermissionTo($permission->name))->toBeTrue();
    it('gère la visibilité des articles de blog (scopes + isPublished)', function () {
        $lang = Lang::factory()->create(['code' => 'en', 'is_default' => true, 'is_active' => true]);
        $country = makeCountry(makeContinent()->id);
        $user = makeUser($country->id, 'jane@example.com');
        $category = BlogCategory::create(['sort_order' => 1, 'status' => 'active']);

        BlogPost::create([
            'category_id' => $category->id,
            'user_id' => $user->id,
            'status' => BlogPostStatus::Draft,
            'published_at' => null,
            'sort_order' => 1,
        ]);

        BlogPost::create([
            'category_id' => $category->id,
            'user_id' => $user->id,
            'status' => BlogPostStatus::Published,
            'published_at' => now()->subDay(),
            'sort_order' => 2,
        ]);

        expect(BlogPost::count())->toBe(2);
        expect(BlogPost::draft()->count())->toBe(1);
        expect(BlogPost::published()->count())->toBe(1);
        expect(BlogPost::archived()->count())->toBe(0);
        expect(BlogPost::scheduled()->count())->toBe(0);
    });

    it('mappe la valeur avec cast des paramètres (Setting::getValue)', function () {
        $int = Setting::create([
            'key' => 'max_retry_'.uniqid(),
            'value' => '5',
            'type' => 'integer',
            'is_public' => true,
            'is_translatable' => false,
            'is_editable' => true,
        ]);

        $bool = Setting::create([
            'key' => 'maintenance_'.uniqid(),
            'value' => 'true',
            'type' => 'boolean',
            'is_public' => true,
            'is_translatable' => false,
            'is_editable' => true,
        ]);

        expect($int->getValue())->toBe(5);
        expect($bool->getValue())->toBeTrue();
    });

    it('gère les versions de plateforme et leurs statuts', function () {
        $v1 = PlatformVersion::create([
            'code' => 'v1_'.uniqid(),
            'status' => 'active',
            'is_published' => true,
            'is_default' => true,
            'released_at' => now(),
        ]);

        expect($v1->uuid)->not->toBeNull();
        expect((string) $v1->status)->toBe('active');
    });

    it('crée des entités de contenu simple (Faq, Gallery)', function () {
        $country = makeCountry(makeContinent()->id);
        $user = makeUser($country->id, 'content@example.com');

        $faq = Faq::create(['user_id' => $user->id, 'sort_order' => 1, 'status' => 'active']);
        $gallery = Gallery::create(['user_id' => $user->id, 'sort_order' => 2, 'status' => 'active']);

        expect($faq->uuid)->not->toBeNull();
        expect($gallery->uuid)->not->toBeNull();
    });

    it('valide le soft delete et la restauration sur une entité', function () {
        $country = makeCountry(makeContinent()->id);
        $user = makeUser($country->id, 'soft@example.com');

        expect($user->trashed())->toBeFalse();

        $user->delete();
        expect($user->trashed())->toBeTrue();
        expect(User::find($user->id))->toBeNull();

        $user->restore();
        expect(User::find($user->id)->id)->toBe($user->id);
    });
});
