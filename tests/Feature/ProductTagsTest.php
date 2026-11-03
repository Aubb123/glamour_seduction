<?php

declare(strict_types=1);

use App\Models\GeoCountry;
use App\Models\Lang;
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
 * Module Produits — Tags (`product-tags`).
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

function productTagAdmin(): User
{
    $country = GeoCountry::where('code', 'BEN')->firstOrFail();

    $admin = User::create([
        'country_id' => $country->id,
        'first_name' => 'Alice', 'last_name' => 'Martin',
        'email' => 'admin@site.com', 'gender' => 'female',
        'phone' => '+22900000000', 'password' => 'password',
        'status' => 'active',
    ]);

    $admin->forceFill([
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
    ])->save();

    $admin->assignRole('administrator');

    return $admin;
}

function productTagPayload(array $overrides = []): array
{
    $langFr = Lang::where('code', 'fr')->firstOrFail();
    $langEn = Lang::where('code', 'en')->firstOrFail();
    $uid = uniqid();

    return array_merge([
        'sort_order' => random_int(1000, 99999),
        'status' => 'active',
        'translations' => [
            ['lang_id' => $langFr->id, 'name' => 'Tag FR '.$uid, 'slug' => 'tag-fr-'.$uid, 'description' => 'Description FR.'],
            ['lang_id' => $langEn->id, 'name' => 'Tag EN '.$uid, 'slug' => 'tag-en-'.$uid, 'description' => 'English description.'],
        ],
    ], $overrides);
}

it('liste les tags de produits avec DataTables', function () {
    $admin = productTagAdmin();

    $html = $this->actingAs($admin)
        ->get(route('backend.product-tags.index'))
        ->assertOk()
        ->getContent();

    expect($html)->toContain('product-tags-table');
    expect($html)->toContain("$('#product-tags-table').DataTable");
});

it('affiche la page de création de tag', function () {
    $admin = productTagAdmin();

    $this->actingAs($admin)
        ->get(route('backend.product-tags.create'))
        ->assertOk();
});

it('crée un tag avec ses traductions', function () {
    $admin = productTagAdmin();

    $this->actingAs($admin)
        ->post(route('backend.product-tags.store'), productTagPayload())
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(ProductTag::count())->toBe(1);
    expect(ProductTag::first()->translations()->count())->toBe(2);
});

it('affiche le détail d’un tag (show avec slug)', function () {
    $admin = productTagAdmin();

    $this->actingAs($admin)->post(route('backend.product-tags.store'), productTagPayload());

    $tag = ProductTag::firstOrFail();
    $slug = $tag->active_translation?->slug ?? 'null';

    $this->actingAs($admin)
        ->get(route('backend.product-tags.show', ['productTag' => $tag->uuid, 'slug' => $slug]))
        ->assertOk();
});

it('met à jour un tag et ses traductions', function () {
    $admin = productTagAdmin();

    $this->actingAs($admin)->post(route('backend.product-tags.store'), productTagPayload());
    $tag = ProductTag::firstOrFail();

    $payload = productTagPayload([
        'sort_order' => random_int(1000, 99999),
        'status' => 'inactive',
    ]);

    $this->actingAs($admin)
        ->put(route('backend.product-tags.update', ['productTag' => $tag->uuid]), $payload)
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($tag->fresh()->status->value)->toBe('inactive');
});

it('active et désactive un tag', function () {
    $admin = productTagAdmin();

    $this->actingAs($admin)->post(route('backend.product-tags.store'), productTagPayload());
    $tag = ProductTag::firstOrFail();

    $this->actingAs($admin)
        ->patch(route('backend.product-tags.deactivate', ['productTag' => $tag->uuid]))
        ->assertRedirect();

    expect($tag->fresh()->status->value)->toBe('inactive');

    $this->actingAs($admin)
        ->patch(route('backend.product-tags.activate', ['productTag' => $tag->uuid]))
        ->assertRedirect();

    expect($tag->fresh()->status->value)->toBe('active');
});

it('refuse la suppression d’un tag actif puis autorise après désactivation', function () {
    $admin = productTagAdmin();

    $this->actingAs($admin)->post(route('backend.product-tags.store'), productTagPayload());
    $tag = ProductTag::firstOrFail();

    // Actif → suppression refusée (règle du ProductTagService::backend_product_tags_destroy)
    $this->actingAs($admin)
        ->from(route('backend.product-tags.index'))
        ->delete(route('backend.product-tags.destroy', ['productTag' => $tag->uuid]))
        ->assertRedirect(route('backend.product-tags.index'))
        ->assertSessionHas('error');

    expect(ProductTag::find($tag->id))->not->toBeNull();

    // Désactivé → suppression autorisée
    $tag->update(['status' => 'inactive']);

    $this->actingAs($admin)
        ->delete(route('backend.product-tags.destroy', ['productTag' => $tag->uuid]))
        ->assertRedirect();

    expect(ProductTag::find($tag->id))->toBeNull();
});

it('passe un tag en corbeille puis le restaure', function () {
    $admin = productTagAdmin();

    $this->actingAs($admin)->post(route('backend.product-tags.store'), productTagPayload());
    $tag = ProductTag::firstOrFail();
    $tag->update(['status' => 'inactive']);

    $this->actingAs($admin)
        ->delete(route('backend.product-tags.destroy', ['productTag' => $tag->uuid]))
        ->assertRedirect();

    expect(ProductTag::find($tag->id))->toBeNull();

    $this->actingAs($admin)
        ->from(route('backend.product-tags.trashed'))
        ->patch(route('backend.product-tags.restore', ['uuid' => $tag->uuid]))
        ->assertRedirect();

    expect(ProductTag::find($tag->id))->not->toBeNull();
});

it('déclare une liste vide de dependentRelations (étendue au pivot du module Produits)', function () {
    $admin = productTagAdmin();

    $this->actingAs($admin)->post(route('backend.product-tags.store'), productTagPayload());
    $tag = ProductTag::firstOrFail();

    $property = new ReflectionProperty($tag, 'dependentRelations');
    expect($property->getValue($tag))->toBe([]);
});
