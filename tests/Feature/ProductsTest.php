<?php

declare(strict_types=1);

use App\Models\GeoCountry;
use App\Models\Lang;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\ProductTag;
use App\Models\User;
use Database\Seeders\GeoContinentSeeder;
use Database\Seeders\GeoCountrySeeder;
use Database\Seeders\LangSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\PlatformVersionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Module Produits — Produits (`products` + `product_translations` + pivot `product_tag`).
 */
beforeEach(function () {
    $this->seed(LangSeeder::class);
    $this->seed(PlatformVersionSeeder::class);
    $this->seed(GeoContinentSeeder::class);
    $this->seed(GeoCountrySeeder::class);
    $this->seed(RoleSeeder::class);
    $this->seed(PermissionSeeder::class);
    bootV1WebRoutes();
});

function productAdmin(): User
{
    $country = GeoCountry::where('code', 'BEN')->firstOrFail();

    $admin = User::create([
        'country_id' => $country->id,
        'first_name' => 'Alice', 'last_name' => 'Martin',
        'email' => 'admin-products@site.com', 'gender' => 'female',
        'phone' => '+22902222222', 'password' => 'password',
        'status' => 'active',
    ]);

    $admin->forceFill([
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
    ])->save();

    $admin->assignRole('administrator');

    return $admin;
}

function productSetup(): array
{
    $fr = Lang::where('code', 'fr')->firstOrFail();
    $en = Lang::where('code', 'en')->firstOrFail();

    $category = ProductCategory::create(['sort_order' => random_int(1, 999), 'status' => 'active']);
    $category->translations()->create(['lang_id' => $fr->id, 'name' => 'Cat FR '.uniqid(), 'description' => 'Desc FR.']);
    $category->translations()->create(['lang_id' => $en->id, 'name' => 'Cat EN '.uniqid(), 'description' => 'Desc EN.']);

    $brand = ProductBrand::create(['sort_order' => random_int(1, 999), 'status' => 'active']);
    $brand->translations()->create(['lang_id' => $fr->id, 'name' => 'Marque FR '.uniqid(), 'description' => 'Desc FR.']);
    $brand->translations()->create(['lang_id' => $en->id, 'name' => 'Brand EN '.uniqid(), 'description' => 'Desc EN.']);

    $tag = ProductTag::create(['sort_order' => random_int(1, 999), 'status' => 'active']);
    $tag->translations()->create(['lang_id' => $fr->id, 'name' => 'Tag FR '.uniqid(), 'description' => 'Desc FR.']);
    $tag->translations()->create(['lang_id' => $en->id, 'name' => 'Tag EN '.uniqid(), 'description' => 'Desc EN.']);

    return ['fr' => $fr, 'en' => $en, 'category' => $category, 'brand' => $brand, 'tag' => $tag];
}

function productPayload(array $setup, array $overrides = []): array
{
    $uid = uniqid();

    return array_merge([
        'category_id' => $setup['category']->id,
        'brand_id' => $setup['brand']->id,
        'status' => 'published',
        'is_featured' => true,
        'requires_discreet_packaging' => 1,
        'weight' => 0.35,
        'base_price' => 24990,
        'compare_at_price' => 29990,
        'tags' => [$setup['tag']->id],
        'translations' => [
            ['lang_id' => $setup['fr']->id, 'name' => 'Produit FR '.$uid, 'short_description' => 'Short FR.', 'description' => 'Desc FR longue.'],
            ['lang_id' => $setup['en']->id, 'name' => 'Product EN '.$uid, 'short_description' => 'Short EN.', 'description' => 'Long EN description.'],
        ],
    ], $overrides);
}

it('liste les produits avec DataTables', function () {
    $admin = productAdmin();

    $html = $this->actingAs($admin)
        ->get(route('backend.products.index'))
        ->assertOk()
        ->getContent();

    expect($html)->toContain('products-table');
});

it('affiche la page de création de produit', function () {
    $admin = productAdmin();

    $this->actingAs($admin)
        ->get(route('backend.products.create'))
        ->assertOk();
});

it('crée un produit avec traductions et tags (pivot)', function () {
    $admin = productAdmin();
    $setup = productSetup();

    $this->actingAs($admin)
        ->post(route('backend.products.store'), productPayload($setup))
        ->assertRedirect()
        ->assertSessionHas('success');

    $product = Product::firstOrFail();

    expect(Product::count())->toBe(1);
    expect($product->translations()->count())->toBe(2);
    expect($product->tags()->count())->toBe(1);
    expect($product->base_price)->toBe('24990.00');
    expect($product->status->value)->toBe('published');
});

it('refuse un produit sans base_price', function () {
    $admin = productAdmin();
    $setup = productSetup();

    $payload = productPayload($setup);
    unset($payload['base_price']);

    $this->actingAs($admin)
        ->post(route('backend.products.store'), $payload)
        ->assertRedirect()
        ->assertSessionHasErrors(['base_price']);

    expect(Product::count())->toBe(0);
});

it('refuse un produit avec base_price non positif', function () {
    $admin = productAdmin();
    $setup = productSetup();

    $this->actingAs($admin)
        ->post(route('backend.products.store'), productPayload($setup, ['base_price' => 0]))
        ->assertRedirect()
        ->assertSessionHasErrors(['base_price']);

    expect(Product::count())->toBe(0);
});

it('affiche le détail d\'un produit (show avec slug)', function () {
    $admin = productAdmin();
    $setup = productSetup();

    $this->actingAs($admin)->post(route('backend.products.store'), productPayload($setup));
    $product = Product::firstOrFail();

    $this->actingAs($admin)
        ->get(route('backend.products.show', ['product' => $product->uuid, 'slug' => $product->active_translation?->slug ?? 'null']))
        ->assertOk();
});

it('met à jour un produit et synchronise ses tags', function () {
    $admin = productAdmin();
    $setup = productSetup();

    // Second tag pour le test de synchronisation
    $secondTag = ProductTag::create(['sort_order' => random_int(1000, 9999), 'status' => 'active']);
    $secondTag->translations()->create(['lang_id' => $setup['fr']->id, 'name' => 'Tag FR 2 '.uniqid(), 'description' => 'Desc FR.']);

    $this->actingAs($admin)->post(route('backend.products.store'), productPayload($setup));
    $product = Product::firstOrFail();

    $payload = productPayload($setup, ['tags' => [$secondTag->id], 'is_featured' => false]);
    $this->actingAs($admin)
        ->put(route('backend.products.update', ['product' => $product->uuid]), $payload)
        ->assertRedirect()
        ->assertSessionHas('success');

    $product->refresh();
    expect($product->tags()->count())->toBe(1);
    expect($product->tags()->first()->id)->toBe($secondTag->id);
    expect($product->is_featured)->toBeFalse();
});

it('publie, dépublic et archive un produit', function () {
    $admin = productAdmin();
    $setup = productSetup();

    $this->actingAs($admin)->post(route('backend.products.store'), productPayload($setup, ['status' => 'draft']));
    $product = Product::firstOrFail();

    // Publish
    $this->actingAs($admin)
        ->patch(route('backend.products.publish', ['product' => $product->uuid]))
        ->assertRedirect();
    expect($product->fresh()->status->value)->toBe('published');

    // Unpublish
    $this->actingAs($admin)
        ->patch(route('backend.products.unpublish', ['product' => $product->uuid]))
        ->assertRedirect();
    expect($product->fresh()->status->value)->toBe('draft');

    // Archive
    $this->actingAs($admin)
        ->patch(route('backend.products.archive', ['product' => $product->uuid]))
        ->assertRedirect();
    expect($product->fresh()->status->value)->toBe('archived');
});

it('refuse la suppression d\'un produit publié puis autorise après dépublication', function () {
    $admin = productAdmin();
    $setup = productSetup();

    $this->actingAs($admin)->post(route('backend.products.store'), productPayload($setup, ['status' => 'published']));
    $product = Product::firstOrFail();

    // Publié → suppression refusée
    $this->actingAs($admin)
        ->from(route('backend.products.index'))
        ->delete(route('backend.products.destroy', ['product' => $product->uuid]))
        ->assertRedirect(route('backend.products.index'))
        ->assertSessionHas('error');

    expect(Product::find($product->id))->not->toBeNull();

    // Dépublié → suppression autorisée
    $product->update(['status' => 'draft']);
    $this->actingAs($admin)
        ->delete(route('backend.products.destroy', ['product' => $product->uuid]))
        ->assertRedirect();

    expect(Product::find($product->id))->toBeNull();
});

it('passe un produit en corbeille puis le restaure', function () {
    $admin = productAdmin();
    $setup = productSetup();

    $this->actingAs($admin)->post(route('backend.products.store'), productPayload($setup, ['status' => 'draft']));
    $product = Product::firstOrFail();

    $this->actingAs($admin)
        ->delete(route('backend.products.destroy', ['product' => $product->uuid]))
        ->assertRedirect();

    expect(Product::find($product->id))->toBeNull();

    $this->actingAs($admin)
        ->from(route('backend.products.trashed'))
        ->patch(route('backend.products.restore', ['uuid' => $product->uuid]))
        ->assertRedirect();

    expect(Product::find($product->id))->not->toBeNull();
});

it('refuse la suppression d\'une catégorie possédant des produits (dependentRelations)', function () {
    $admin = productAdmin();
    $setup = productSetup();

    $this->actingAs($admin)->post(route('backend.products.store'), productPayload($setup));
    $product = Product::firstOrFail();
    $product->update(['status' => 'draft']);

    $this->actingAs($admin)
        ->from(route('backend.product-categories.index'))
        ->delete(route('backend.product-categories.destroy', ['productCategory' => $setup['category']->uuid]))
        ->assertRedirect(route('backend.product-categories.index'))
        ->assertSessionHas('error');

    expect(ProductCategory::find($setup['category']->id))->not->toBeNull();
});

it('refuse la suppression d\'une marque possédant des produits (dependentRelations)', function () {
    $admin = productAdmin();
    $setup = productSetup();

    $this->actingAs($admin)->post(route('backend.products.store'), productPayload($setup));
    $product = Product::firstOrFail();
    $product->update(['status' => 'draft']);

    $this->actingAs($admin)
        ->from(route('backend.product-brands.index'))
        ->delete(route('backend.product-brands.destroy', ['productBrand' => $setup['brand']->uuid]))
        ->assertRedirect(route('backend.product-brands.index'))
        ->assertSessionHas('error');

    expect(ProductBrand::find($setup['brand']->id))->not->toBeNull();
});

it('déclare variants comme relation bloquante pour Product (dependentRelations, module 6)', function () {
    $product = new Product;

    $property = new ReflectionProperty($product, 'dependentRelations');
    expect($property->getValue($product))->toBe(['variants']);
});