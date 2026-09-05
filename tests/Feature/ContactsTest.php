<?php

declare(strict_types=1);

use App\Models\Contact;
use App\Models\GeoContinent;
use App\Models\GeoCountry;
use App\Models\User;
use Database\Seeders\LangSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\PlatformVersionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Étape 11 — Contacts (partie 1) & Subscribes (voir ContactsSubscribesTest.php pour la suite).
 */

beforeEach(function () {
    $this->seed(LangSeeder::class);
    $this->seed(PlatformVersionSeeder::class);
    $this->seed(RoleSeeder::class);
    $this->seed(PermissionSeeder::class);
    bootV1WebRoutes();
});

function contactTestAdmin(): User
{
    $continent = GeoContinent::create([
        'code' => 'AFT', 'population' => 1000000, 'area_km2' => 100000,
        'file_path' => 'ct.svg', 'sort_order' => 903, 'status' => 'active',
    ]);

    $country = GeoCountry::create([
        'continent_id' => $continent->id, 'code' => 'BEN3', 'code_2' => 'B3',
        'file_path' => 'f3.svg', 'sort_order' => 901, 'status' => 'active',
    ]);

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

it('liste les contacts', function () {
    $admin = contactTestAdmin();

    Contact::create([
        'fullname' => 'Jean Visiteur', 'email' => 'visiteur@mail.com',
        'subject' => 'Question', 'message' => 'Bonjour, une question.',
        'is_read' => false,
    ]);

    $this->actingAs($admin)
        ->get(route('backend.contacts.index'))
        ->assertOk()
        ->assertSee('visiteur@mail.com');
});

it('affiche le détail d’un contact', function () {
    $admin = contactTestAdmin();

    $contact = Contact::create([
        'fullname' => 'Marie Visiteuse', 'email' => 'marie@mail.com',
        'subject' => 'Info', 'message' => 'Bonjour.', 'is_read' => false,
    ]);

    $this->actingAs($admin)
        ->get(route('backend.contacts.show', ['contact' => $contact->uuid]))
        ->assertOk()
        ->assertSee('marie@mail.com');
});

it('marque un contact comme lu puis non lu', function () {
    $admin = contactTestAdmin();

    $contact = Contact::create([
        'fullname' => 'Test Lecture', 'email' => 'lecture@mail.com',
        'message' => 'Bonjour.', 'is_read' => false,
    ]);

    $this->actingAs($admin)
        ->patch(route('backend.contacts.mark_as_read', ['contact' => $contact->uuid]))
        ->assertRedirect();

    expect($contact->fresh()->is_read)->toBeTrue();

    $this->actingAs($admin)
        ->patch(route('backend.contacts.mark_as_unread', ['contact' => $contact->uuid]))
        ->assertRedirect();

    expect($contact->fresh()->is_read)->toBeFalse();
});

it('refuse la suppression d’un contact non lu puis autorise après lecture', function () {
    $admin = contactTestAdmin();

    $contact = Contact::create([
        'fullname' => 'Suppression', 'email' => 'suppr@mail.com',
        'message' => 'Bonjour.', 'is_read' => false,
    ]);

    // Non lu → suppression refusée (règle du ContactService::destroy)
    $this->actingAs($admin)
        ->from(route('backend.contacts.index'))
        ->delete(route('backend.contacts.destroy', ['contact' => $contact->uuid]))
        ->assertRedirect(route('backend.contacts.index'))
        ->assertSessionHas('error');

    expect(Contact::find($contact->id))->not->toBeNull();

    // Lu → suppression autorisée
    $contact->update(['is_read' => true]);

    $this->actingAs($admin)
        ->delete(route('backend.contacts.destroy', ['contact' => $contact->uuid]))
        ->assertRedirect();

    expect(Contact::find($contact->id))->toBeNull();
});

it('passe un contact en corbeille puis le restaure', function () {
    $admin = contactTestAdmin();

    $contact = Contact::create([
        'fullname' => 'Corbeille', 'email' => 'corbeille@mail.com',
        'message' => 'Bonjour.', 'is_read' => true,
    ]);

    $this->actingAs($admin)
        ->delete(route('backend.contacts.destroy', ['contact' => $contact->uuid]))
        ->assertRedirect();

    $this->actingAs($admin)
        ->from(route('backend.contacts.trashed'))
        ->patch(route('backend.contacts.restore', ['uuid' => $contact->uuid]))
        ->assertRedirect();

    expect(Contact::find($contact->id))->not->toBeNull();
});

it('protege l’accès aux contacts (non connecté → login)', function () {
    $this->get('/backend/contacts/index')->assertRedirect(route('login'));
});