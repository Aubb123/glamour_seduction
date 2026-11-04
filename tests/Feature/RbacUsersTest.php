<?php

declare(strict_types=1);

use App\Enums\Users\UserStatus;
use App\Models\GeoCountry;
use App\Models\Permission;
use App\Models\Role;
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
 * Étape 9 — RBAC (`roles`, `permissions`) + Utilisateurs (`users`).
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

function rbacTestAdmin(): User
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

function rbacTestUser(): User
{
    $country = GeoCountry::where('code', 'BEN')->firstOrFail();

    $user = User::create([
        'country_id' => $country->id,
        'first_name' => 'Léa', 'last_name' => 'Fontaine',
        'email' => 'user@site.com', 'gender' => 'female',
        'phone' => '+22911111111', 'password' => 'password',
        'status' => 'active',
    ]);

    $user->forceFill([
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
    ])->save();

    $user->assignRole('user');

    return $user;
}

// ─────────────────────────────────────────────
// ROLES
// ─────────────────────────────────────────────

it('liste les rôles avec leurs statistiques', function () {
    $admin = rbacTestAdmin();

    $this->actingAs($admin)
        ->get(route('backend.roles.index'))
        ->assertOk()
        ->assertSee('administrator')
        ->assertSee('moderator');
});

it('affiche le détail d’un rôle (show avec slug)', function () {
    $admin = rbacTestAdmin();

    $role = Role::where('name', 'administrator')->firstOrFail();
    $slug = $role->active_translation?->slug ?? $role->name;

    $this->actingAs($admin)
        ->get(route('backend.roles.show', ['role' => $role->uuid, 'slug' => $slug]))
        ->assertOk();
});

it('active et désactive un rôle', function () {
    $admin = rbacTestAdmin();

    $role = Role::where('name', 'user')->firstOrFail();

    $this->actingAs($admin)
        ->patch(route('backend.roles.deactivate', ['role' => $role->uuid]))
        ->assertRedirect();

    expect($role->fresh()->status->value)->toBe('inactive');

    $this->actingAs($admin)
        ->patch(route('backend.roles.activate', ['role' => $role->uuid]))
        ->assertRedirect();

    expect($role->fresh()->status->value)->toBe('active');
});

it('affiche la page d’édition d’un rôle', function () {
    $admin = rbacTestAdmin();

    $role = Role::where('name', 'moderator')->firstOrFail();

    $this->actingAs($admin)
        ->get(route('backend.roles.edit', ['role' => $role->uuid]))
        ->assertOk();
});

// ─────────────────────────────────────────────
// PERMISSIONS
// ─────────────────────────────────────────────

it('liste les permissions', function () {
    $admin = rbacTestAdmin();

    $this->actingAs($admin)
        ->get(route('backend.permissions.index'))
        ->assertOk()
        ->assertSee('users.index');
});

it('crée une nouvelle permission', function () {
    $admin = rbacTestAdmin();

    $this->actingAs($admin)
        ->post(route('backend.permissions.store'), [
            'name' => 'custom.action',
            'guard_name' => 'web',
            'status' => 'active',
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(Permission::where('name', 'custom.action')->exists())->toBeTrue();
});

it('synchronise les rôles d’une permission (cœur du RBAC)', function () {
    $admin = rbacTestAdmin();

    $permission = Permission::where('name', 'users.index')->firstOrFail();
    $role = Role::where('name', 'user')->firstOrFail();

    $this->actingAs($admin)
        ->put(route('backend.permissions.sync_roles', ['permission' => $permission->uuid]), [
            'role_ids' => [$role->id],
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($permission->roles()->pluck('roles.id')->contains($role->id))->toBeTrue();
});

it('affiche la page d’assignation d’une permission', function () {
    $admin = rbacTestAdmin();

    $permission = Permission::where('name', 'users.index')->firstOrFail();

    $this->actingAs($admin)
        ->get(route('backend.permissions.assign', ['permission' => $permission->uuid]))
        ->assertOk();
});

// ─────────────────────────────────────────────
// USERS
// ─────────────────────────────────────────────

it('liste les utilisateurs', function () {
    $admin = rbacTestAdmin();
    rbacTestUser();

    $this->actingAs($admin)
        ->get(route('backend.users.index'))
        ->assertOk()
        ->assertSee('admin@site.com')
        ->assertSee('user@site.com');
});

it('affiche le détail d’un utilisateur (show avec slug)', function () {
    $admin = rbacTestAdmin();
    $user = rbacTestUser();

    $this->actingAs($admin)
        ->get(route('backend.users.show', ['user' => $user->uuid, 'slug' => $user->slug]))
        ->assertOk()
        ->assertSee('user@site.com');
});

it('suspend puis réactive un utilisateur actif', function () {
    $admin = rbacTestAdmin();
    $user = rbacTestUser();
    $user->update(['status' => UserStatus::Active]);

    $this->actingAs($admin)
        ->patch(route('backend.users.suspend', ['user' => $user->uuid]))
        ->assertRedirect();

    expect($user->fresh()->status->value)->toBe('suspended');

    $this->actingAs($admin)
        ->patch(route('backend.users.activate', ['user' => $user->uuid]))
        ->assertRedirect();

    expect($user->fresh()->status->value)->toBe('active');
});

it('bannit un utilisateur et interdit sa réactivation (règle métier)', function () {
    $admin = rbacTestAdmin();
    $user = rbacTestUser();
    $user->update(['status' => UserStatus::Active]);

    $this->actingAs($admin)
        ->patch(route('backend.users.ban', ['user' => $user->uuid]))
        ->assertRedirect();

    expect($user->fresh()->status->value)->toBe('banned');

    // Un utilisateur banni ne peut pas être réactivé (règle du service UserService::activate).
    $this->actingAs($admin)
        ->from(route('backend.users.index'))
        ->patch(route('backend.users.activate', ['user' => $user->uuid]))
        ->assertRedirect(route('backend.users.index'))
        ->assertSessionHas('error');

    expect($user->fresh()->status->value)->toBe('banned');
});

it('affiche la page d’édition d’un utilisateur', function () {
    $admin = rbacTestAdmin();
    $user = rbacTestUser();

    $this->actingAs($admin)
        ->get(route('backend.users.edit', ['user' => $user->uuid]))
        ->assertOk();
});

it('protege l’accès aux modules RBAC (non connecté → login)', function () {
    $this->get('/backend/roles/index')->assertRedirect(route('login'));
    $this->get('/backend/permissions/index')->assertRedirect(route('login'));
    $this->get('/backend/users/index')->assertRedirect(route('login'));
});
