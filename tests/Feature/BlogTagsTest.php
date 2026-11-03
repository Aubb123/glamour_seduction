<?php

declare(strict_types=1);

use App\Models\BlogTag;
use App\Models\GeoCountry;
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
 * Étape 12 — Blog : Tags (`blog-tags`).
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

function tagAdmin(): User
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

function tagPayload(array $overrides = []): array
{
    $langFr = \App\Models\Lang::where('code', 'fr')->firstOrFail();
    $langEn = \App\Models\Lang::where('code', 'en')->firstOrFail();
    $uid = uniqid();

    return array_merge([
        'sort_order' => random_int(1000, 99999),
        'status' => 'active',
        'translations' => [
            ['lang_id' => $langFr->id, 'name' => 'Tag '.$uid],
            ['lang_id' => $langEn->id, 'name' => 'Tag '.$uid],
        ],
    ], $overrides);
}

it('liste les tags avec DataTables', function () {
    $admin = tagAdmin();

    $html = $this->actingAs($admin)
        ->get(route('backend.blog-tags.index'))
        ->assertOk()
        ->getContent();

    expect($html)->toContain('blog-tags-table');
    expect($html)->toContain("$('#blog-tags-table').DataTable");
});

it('affiche la page de création de tag', function () {
    $admin = tagAdmin();

    $this->actingAs($admin)
        ->get(route('backend.blog-tags.create'))
        ->assertOk();
});

it('crée un tag avec ses traductions', function () {
    $admin = tagAdmin();

    $this->actingAs($admin)
        ->post(route('backend.blog-tags.store'), tagPayload())
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(BlogTag::count())->toBe(1);
    expect(BlogTag::first()->translations()->count())->toBe(2);
});

it('bloque deux tags avec le même nom dans la même langue (unicité par langue)', function () {
    $admin = tagAdmin();

    $payload = tagPayload();
    $payload['sort_order'] = 5001;

    $this->actingAs($admin)->post(route('backend.blog-tags.store'), $payload);

    // Second post : mêmes noms/langues, sort_order différent
    $payload2 = tagPayload();
    $payload2['translations'][0]['name'] = $payload['translations'][0]['name'];
    $payload2['translations'][1]['name'] = $payload['translations'][1]['name'];
    $payload2['sort_order'] = 5002;

    $this->actingAs($admin)
        ->post(route('backend.blog-tags.store'), $payload2)
        ->assertSessionHasErrors();

    expect(BlogTag::count())->toBe(1);
});

it('active et désactive un tag', function () {
    $admin = tagAdmin();

    $this->actingAs($admin)->post(route('backend.blog-tags.store'), tagPayload());
    $tag = BlogTag::firstOrFail();

    $this->actingAs($admin)
        ->patch(route('backend.blog-tags.deactivate', ['blogTag' => $tag->uuid]))
        ->assertRedirect();

    expect($tag->fresh()->status->value)->toBe('inactive');

    $this->actingAs($admin)
        ->patch(route('backend.blog-tags.activate', ['blogTag' => $tag->uuid]))
        ->assertRedirect();

    expect($tag->fresh()->status->value)->toBe('active');
});

it('refuse la suppression d’un tag actif puis autorise après désactivation', function () {
    $admin = tagAdmin();

    $this->actingAs($admin)->post(route('backend.blog-tags.store'), tagPayload());
    $tag = BlogTag::firstOrFail();

    // Actif → suppression refusée (règle du BlogTagService::backend_blog_tags_destroy)
    $this->actingAs($admin)
        ->from(route('backend.blog-tags.index'))
        ->delete(route('backend.blog-tags.destroy', ['blogTag' => $tag->uuid]))
        ->assertRedirect(route('backend.blog-tags.index'))
        ->assertSessionHas('error');

    expect(BlogTag::find($tag->id))->not->toBeNull();

    // Désactivé → suppression autorisée
    $tag->update(['status' => 'inactive']);

    $this->actingAs($admin)
        ->delete(route('backend.blog-tags.destroy', ['blogTag' => $tag->uuid]))
        ->assertRedirect();

    expect(BlogTag::find($tag->id))->toBeNull();
});

it('passe un tag en corbeille puis le restaure', function () {
    $admin = tagAdmin();

    $this->actingAs($admin)->post(route('backend.blog-tags.store'), tagPayload());
    $tag = BlogTag::firstOrFail();
    $tag->update(['status' => 'inactive']);

    $this->actingAs($admin)
        ->delete(route('backend.blog-tags.destroy', ['blogTag' => $tag->uuid]))
        ->assertRedirect();

    expect(BlogTag::find($tag->id))->toBeNull();

    $this->actingAs($admin)
        ->from(route('backend.blog-tags.trashed'))
        ->patch(route('backend.blog-tags.restore', ['uuid' => $tag->uuid]))
        ->assertRedirect();

    expect(BlogTag::find($tag->id))->not->toBeNull();
});
