<?php

declare(strict_types=1);

use App\Models\GeoCountry;
use App\Models\Position;
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
 * Module Boutique — Postes du personnel (`positions`).
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

function positionAdmin(): User
{
    $country = GeoCountry::where('code', 'BEN')->firstOrFail();

    $admin = User::create([
        'country_id' => $country->id,
        'first_name' => 'Alice', 'last_name' => 'Martin',
        'email' => 'admin-positions@site.com', 'gender' => 'female',
        'phone' => '+22904455555', 'password' => 'password',
        'status' => 'active',
    ]);

    $admin->forceFill([
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
    ])->save();

    $admin->assignRole('administrator');

    return $admin;
}

function positionPayload(array $overrides = []): array
{
    return array_merge([
        'code' => 'POS-'.strtoupper(uniqid()),
        'context' => 'both',
        'status' => 'inactive',
        'sort_order' => 0,
        'translations' => [
            ['lang_id' => \App\Models\Lang::where('code', 'fr')->value('id'), 'name' => 'Poste '.uniqid(), 'description' => 'Description de test'],
        ],
    ], $overrides);
}

it('liste les postes', function () {
    $admin = positionAdmin();

    $this->actingAs($admin)->get(route('backend.positions.index'))->assertOk();
});

it('crée un poste avec sa traduction', function () {
    $admin = positionAdmin();

    $this->actingAs($admin)
        ->post(route('backend.positions.store'), positionPayload())
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(Position::count())->toBe(1);
    expect(Position::firstOrFail()->translations()->count())->toBe(1);
});

it('refuse un code dupliqué', function () {
    $admin = positionAdmin();

    $payload = positionPayload();
    $this->actingAs($admin)->post(route('backend.positions.store'), $payload);

    $this->actingAs($admin)
        ->post(route('backend.positions.store'), positionPayload(['code' => $payload['code']]))
        ->assertSessionHasErrors(['code']);

    expect(Position::count())->toBe(1);
});

it('active et désactive un poste', function () {
    $admin = positionAdmin();

    $this->actingAs($admin)->post(route('backend.positions.store'), positionPayload());
    $position = Position::firstOrFail();

    $this->actingAs($admin)->patch(route('backend.positions.activate', ['position' => $position->uuid]));
    expect($position->fresh()->status->value)->toBe('active');

    $this->actingAs($admin)->patch(route('backend.positions.deactivate', ['position' => $position->uuid]));
    expect($position->fresh()->status->value)->toBe('inactive');
});

it('refuse la suppression d\'un poste actif', function () {
    $admin = positionAdmin();

    $this->actingAs($admin)->post(route('backend.positions.store'), positionPayload(['status' => 'active']));
    $position = Position::firstOrFail();

    $this->actingAs($admin)
        ->delete(route('backend.positions.destroy', ['position' => $position->uuid]))
        ->assertSessionHas('error');

    expect(Position::find($position->id))->not->toBeNull();
});

it('déclare dependentRelations = [shopUsers, warehouseUsers] pour Position', function () {
    $property = new ReflectionProperty(new Position, 'dependentRelations');
    expect($property->getValue(new Position))->toBe(['shopUsers', 'warehouseUsers']);
});
