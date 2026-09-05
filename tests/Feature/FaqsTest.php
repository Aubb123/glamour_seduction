<?php

declare(strict_types=1);

use App\Models\Faq;
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
 * Étape 10 — Module FAQ (`faqs`).
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

function faqsTestAdmin(): User
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

function faqTestPayload(array $overrides = []): array
{
    $langFr = \App\Models\Lang::where('code', 'fr')->firstOrFail();
    $langEn = \App\Models\Lang::where('code', 'en')->firstOrFail();

    return array_merge([
        'sort_order' => random_int(1000, 99999),
        'status' => 'active',
        'translations' => [
            ['lang_id' => $langFr->id, 'question' => 'Question '.uniqid(), 'answer' => 'Réponse FR.'],
            ['lang_id' => $langEn->id, 'question' => 'Question '.uniqid(), 'answer' => 'English answer.'],
        ],
    ], $overrides);
}

it('liste les FAQ avec DataTables', function () {
    $admin = faqsTestAdmin();

    $html = $this->actingAs($admin)
        ->get(route('backend.faqs.index'))
        ->assertOk()
        ->getContent();

    expect($html)->toContain('faqs-table');
    expect($html)->toContain("$('#faqs-table').DataTable");
});

it('affiche la page de création d’une FAQ', function () {
    $admin = faqsTestAdmin();

    $this->actingAs($admin)
        ->get(route('backend.faqs.create'))
        ->assertOk();
});

it('crée une FAQ avec ses traductions', function () {
    $admin = faqsTestAdmin();

    $this->actingAs($admin)
        ->post(route('backend.faqs.store'), faqTestPayload())
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(Faq::count())->toBe(1);
    expect(Faq::first()->translations()->count())->toBe(2);
});

it('bascule l’état actif/inactif d’une FAQ (toggle-active)', function () {
    $admin = faqsTestAdmin();

    $faq = Faq::create(['user_id' => $admin->id, 'sort_order' => random_int(1000, 99999), 'status' => 'active']);

    $this->actingAs($admin)
        ->patch(route('backend.faqs.toggle_active', ['faq' => $faq->uuid]))
        ->assertRedirect();

    expect($faq->fresh()->status->value)->toBe('inactive');
});

it('protege l’accès au module FAQ (non connecté → login)', function () {
    $this->get('/backend/faqs/index')->assertRedirect(route('login'));
});