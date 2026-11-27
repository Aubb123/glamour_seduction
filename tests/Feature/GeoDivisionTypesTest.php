<?php

declare(strict_types=1);

use App\Models\GeoDivisionType;
use App\Models\GeoDivisionTypeTranslation;
use App\Models\Lang;
use App\Models\User;
use Database\Seeders\GeoContinentSeeder;
use Database\Seeders\GeoCountrySeeder;
use Database\Seeders\GeoDivisionTypeSeeder;
use Database\Seeders\LangSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\PlatformVersionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Module « Types de division géographique » (geo_division_types).
 */
beforeEach(function () {
    $this->seed(LangSeeder::class);
    $this->seed(SettingSeeder::class);
    $this->seed(PlatformVersionSeeder::class);
    $this->seed(GeoContinentSeeder::class);
    $this->seed(GeoCountrySeeder::class);
    $this->seed(GeoDivisionTypeSeeder::class);
    $this->seed(RoleSeeder::class);
    $this->seed(PermissionSeeder::class);
    bootV1WebRoutes();
});

function geoDivisionTypeAdmin(): User
{
    $admin = User::create([
        'first_name' => 'Alice', 'last_name' => 'Martin',
        'email' => 'admin@site.com', 'gender' => 'female',
        'phone' => '+22900000000', 'password' => 'password',
        'status' => 'active',
    ]);
    $admin->forceFill(['email_verified_at' => now(), 'phone_verified_at' => now()])->save();
    $admin->assignRole('administrator');

    return $admin;
}

function geoDivisionTypeModerator(): User
{
    $moderator = User::create([
        'first_name' => 'Bob', 'last_name' => 'Durand',
        'email' => 'moderator@site.com', 'gender' => 'male',
        'phone' => '+22900000001', 'password' => 'password',
        'status' => 'active',
    ]);
    $moderator->forceFill(['email_verified_at' => now(), 'phone_verified_at' => now()])->save();
    $moderator->assignRole('moderator');

    return $moderator;
}

/**
 * Payload valide (code et libellés propres à chaque test via $suffix).
 */
function geoDivisionTypePayload(string $suffix = 'new', int $sortOrder = 100): array
{
    $langs = Lang::pluck('id', 'code');

    return [
        'code' => 'division_'.$suffix,
        'level' => 1,
        'sort_order' => $sortOrder,
        'status' => 'active',
        'translations' => [
            [
                'lang_id' => $langs['fr'] ?? 1,
                'name' => 'Libellé '.$suffix,
                'description' => 'Description '.$suffix,
            ],
            [
                'lang_id' => $langs['en'] ?? 2,
                'name' => 'Label '.$suffix,
                'description' => 'Description '.$suffix,
            ],
        ],
    ];
}

function createDivisionType(array $overrides = []): GeoDivisionType
{
    $type = GeoDivisionType::create(array_merge([
        'code' => 'test_type',
        'level' => 1,
        'sort_order' => 1,
        'status' => 'active',
    ], $overrides));

    $langs = Lang::pluck('id', 'code');

    foreach ([['fr', 'Type test'], ['en', 'Test type']] as [$langCode, $name]) {
        if (isset($langs[$langCode])) {
            GeoDivisionTypeTranslation::create([
                'division_type_id' => $type->id,
                'lang_id' => $langs[$langCode],
                'name' => $name,
                'description' => 'Description '.$name,
            ]);
        }
    }

    return $type;
}

it('liste les types de division (DataTables)', function () {
    $admin = geoDivisionTypeAdmin();

    $this->actingAs($admin)
        ->get(route('backend.geodivisiontypes.index'))
        ->assertOk()
        ->assertSee('department')
        ->assertSee('État')
        ->assertSee('Région');
});

it('crée un type de division avec traductions et slugs générés', function () {
    $admin = geoDivisionTypeAdmin();

    $this->actingAs($admin)
        ->post(route('backend.geodivisiontypes.store'), geoDivisionTypePayload('alpha'))
        ->assertRedirect()
        ->assertSessionHas('success');

    $type = GeoDivisionType::where('code', 'division_alpha')->firstOrFail();
    $frLangId = Lang::where('code', 'fr')->value('id');

    expect($type->translations()->where('lang_id', $frLangId)->value('name'))->toBe('Libellé alpha');
    expect($type->translations()->where('lang_id', $frLangId)->value('slug'))->toBe('libelle-alpha');
});

it('propagate sort_order et level (création)', function () {
    $admin = geoDivisionTypeAdmin();

    $payload = geoDivisionTypePayload('beta', 5);

    $this->actingAs($admin)
        ->post(route('backend.geodivisiontypes.store'), $payload)
        ->assertRedirect();

    $type = GeoDivisionType::where('code', 'division_beta')->firstOrFail();
    expect($type->sort_order)->toBe(5);
    expect($type->level)->toBe(1);
});

it('refuse un code en doublon', function () {
    $admin = geoDivisionTypeAdmin();

    $this->actingAs($admin)
        ->post(route('backend.geodivisiontypes.store'), geoDivisionTypePayload('gamma'))
        ->assertRedirect();

    $this->actingAs($admin)
        ->post(route('backend.geodivisiontypes.store'), geoDivisionTypePayload('gamma'))
        ->assertSessionHasErrors('code');
});

it('refuse un code avec regex invalide', function () {
    $admin = geoDivisionTypeAdmin();

    $payload = geoDivisionTypePayload('delta');
    $payload['code'] = 'INVALID-CODE';

    $this->actingAs($admin)
        ->post(route('backend.geodivisiontypes.store'), $payload)
        ->assertSessionHasErrors('code');
});

it('refuse un libellé déjà utilisé pour la même langue', function () {
    $admin = geoDivisionTypeAdmin();

    $this->actingAs($admin)
        ->post(route('backend.geodivisiontypes.store'), geoDivisionTypePayload('echo'))
        ->assertRedirect();

    $payload = geoDivisionTypePayload('foxtrot');
    $payload['translations'][0]['name'] = 'Libellé echo';

    $this->actingAs($admin)
        ->post(route('backend.geodivisiontypes.store'), $payload)
        ->assertSessionHasErrors('translations.0.name');
});

it('refuse une description manquante', function () {
    $admin = geoDivisionTypeAdmin();

    $payload = geoDivisionTypePayload('mike');
    $payload['translations'][0]['description'] = '';
    unset($payload['translations'][1]['description']);

    $this->actingAs($admin)
        ->post(route('backend.geodivisiontypes.store'), $payload)
        ->assertSessionHasErrors([
            'translations.0.description',
            'translations.1.description',
        ]);
});

it('le code est immuable en update', function () {
    $admin = geoDivisionTypeAdmin();
    $type = createDivisionType(['code' => 'region']);

    $payload = geoDivisionTypePayload('hotel');
    $payload['code'] = 'tentative_modification';

    $this->actingAs($admin)
        ->put(route('backend.geodivisiontypes.update', ['geoDivisionType' => $type->uuid]), $payload)
        ->assertRedirect();

    expect($type->fresh()->code)->toBe('region');
});

it('met à jour le slug quand le libellé change', function () {
    $admin = geoDivisionTypeAdmin();
    $type = createDivisionType(['code' => 'india']);

    $langs = Lang::pluck('id', 'code');

    $payload = geoDivisionTypePayload('india');
    $payload['translations'][0]['name'] = 'Type renommé';
    $payload['translations'][1]['name'] = 'Renamed type';

    $this->actingAs($admin)
        ->put(route('backend.geodivisiontypes.update', ['geoDivisionType' => $type->uuid]), $payload)
        ->assertRedirect();

    expect($type->fresh()->translations()->where('lang_id', $langs['fr'])->value('slug'))->toBe('type-renomme');
});

it('refuse un mauvais identifiant (404)', function () {
    $admin = geoDivisionTypeAdmin();

    $this->actingAs($admin)
        ->get(route('backend.geodivisiontypes.show', ['geoDivisionType' => 'uuid-inexistant', 'slug' => 'slug-inexistant']))
        ->assertNotFound();
});

it('show : affiche la page quand le slug correspond', function () {
    $admin = geoDivisionTypeAdmin();
    $type = GeoDivisionType::where('code', 'department')->firstOrFail();
    $frSlug = $type->translations()->where('lang_id', Lang::where('code', 'fr')->value('id'))->value('slug');

    $this->actingAs($admin)
        ->get(route('backend.geodivisiontypes.show', ['geoDivisionType' => $type->uuid, 'slug' => $frSlug ?? $type->code]))
        ->assertOk()
        ->assertSee($frSlug ?? $type->code);
});

it('show : renvoie 404 quand le slug ne correspond pas', function () {
    $admin = geoDivisionTypeAdmin();
    $type = GeoDivisionType::where('code', 'department')->firstOrFail();

    $this->actingAs($admin)
        ->get(route('backend.geodivisiontypes.show', ['geoDivisionType' => $type->uuid, 'slug' => 'slug-incoherent']))
        ->assertNotFound();
});

it('active et désactive un type de division', function () {
    $admin = geoDivisionTypeAdmin();
    $type = createDivisionType(['status' => 'inactive']);

    $this->actingAs($admin)
        ->patch(route('backend.geodivisiontypes.activate', ['geoDivisionType' => $type->uuid]))
        ->assertRedirect();

    expect($type->fresh()->status->value)->toBe('active');

    $this->actingAs($admin)
        ->patch(route('backend.geodivisiontypes.deactivate', ['geoDivisionType' => $type->uuid]))
        ->assertRedirect();

    expect($type->fresh()->status->value)->toBe('inactive');
});

it('refuse la suppression si le type est actif', function () {
    $admin = geoDivisionTypeAdmin();
    $type = createDivisionType(['status' => 'active']);

    $this->actingAs($admin)
        ->delete(route('backend.geodivisiontypes.destroy', ['geoDivisionType' => $type->uuid]))
        ->assertRedirect();

    expect(GeoDivisionType::where('code', 'test_type')->exists())->toBeTrue();
});

it('corbeille : trashed puis restore', function () {
    $admin = geoDivisionTypeAdmin();
    $type = createDivisionType(['status' => 'inactive']);

    $this->actingAs($admin)
        ->delete(route('backend.geodivisiontypes.destroy', ['geoDivisionType' => $type->uuid]))
        ->assertRedirect();

    $this->actingAs($admin)
        ->get(route('backend.geodivisiontypes.trashed'))
        ->assertOk()
        ->assertSee($type->code);

    $this->actingAs($admin)
        ->patch(route('backend.geodivisiontypes.restore', ['uuid' => $type->uuid]))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(GeoDivisionType::withTrashed()->where('code', 'test_type')->first()->trashed())->toBeFalse();
});

it('force destroy supprime définitivement le type et ses traductions', function () {
    $admin = geoDivisionTypeAdmin();
    $type = createDivisionType(['status' => 'inactive']);

    $this->actingAs($admin)
        ->delete(route('backend.geodivisiontypes.destroy', ['geoDivisionType' => $type->uuid]))
        ->assertRedirect();

    $this->actingAs($admin)
        ->delete(route('backend.geodivisiontypes.force-destroy', ['uuid' => $type->uuid]))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(GeoDivisionType::withTrashed()->where('code', 'test_type')->exists())->toBeFalse();
    expect(GeoDivisionTypeTranslation::withTrashed()->where('division_type_id', $type->id)->exists())->toBeFalse();
});

it('corbeille des traductions : restore et force destroy', function () {
    $admin = geoDivisionTypeAdmin();
    $type = createDivisionType(['status' => 'inactive']);

    $translation = $type->translations()->where('lang_id', Lang::where('code', 'fr')->value('id'))->first();

    $this->actingAs($admin)
        ->delete(route('backend.geodivisiontypes.translation.destroy', ['geoDivisionTypeTranslation' => $translation->uuid]))
        ->assertRedirect();

    $this->actingAs($admin)
        ->get(route('backend.geodivisiontypes.translation.trashed', ['geoDivisionType' => $type->uuid]))
        ->assertOk()
        ->assertSee($translation->name)
        ->assertSee($translation->slug);

    // Restaure
    $this->actingAs($admin)
        ->patch(route('backend.geodivisiontypes.translation.restore', ['uuid' => $translation->uuid]))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($translation->fresh()->trashed())->toBeFalse();

    // Force destroy
    $this->actingAs($admin)
        ->delete(route('backend.geodivisiontypes.translation.destroy', ['geoDivisionTypeTranslation' => $translation->uuid]))
        ->assertRedirect();

    $this->actingAs($admin)
        ->delete(route('backend.geodivisiontypes.translation.force-destroy', ['uuid' => $translation->uuid]))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(GeoDivisionTypeTranslation::withTrashed()->where('id', $translation->id)->exists())->toBeFalse();
});

it('refuse l’accès au modérateur (403 sur activate, trashed, destroy)', function () {
    $moderator = geoDivisionTypeModerator();
    $type = createDivisionType(['status' => 'active']);

    $this->actingAs($moderator)
        ->patch(route('backend.geodivisiontypes.activate', ['geoDivisionType' => $type->uuid]))
        ->assertForbidden();

    $this->actingAs($moderator)
        ->get(route('backend.geodivisiontypes.trashed'))
        ->assertForbidden();

    $this->actingAs($moderator)
        ->delete(route('backend.geodivisiontypes.destroy', ['geoDivisionType' => $type->uuid]))
        ->assertForbidden();
});

it('n’affiche aucune clé de traduction brute', function () {
    $admin = geoDivisionTypeAdmin();

    $this->actingAs($admin)
        ->get(route('backend.geodivisiontypes.index'))
        ->assertOk()
        ->assertDontSee('division-types-messages')
        ->assertDontSee('ui.index');

    $type = GeoDivisionType::first();
    $slug = $type->active_translation?->slug ?? $type->code;

    $this->actingAs($admin)
        ->get(route('backend.geodivisiontypes.show', ['geoDivisionType' => $type->uuid, 'slug' => $slug]))
        ->assertOk()
        ->assertDontSee('division-types-messages')
        ->assertDontSee('ui.show');
});
