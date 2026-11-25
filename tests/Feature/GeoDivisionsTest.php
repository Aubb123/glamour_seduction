<?php

declare(strict_types=1);

use App\Models\GeoCountry;
use App\Models\GeoDivision;
use App\Models\GeoDivisionTranslation;
use App\Models\GeoDivisionType;
use App\Models\Lang;
use App\Models\User;
use Database\Seeders\GeoContinentSeeder;
use Database\Seeders\GeoCountrySeeder;
use Database\Seeders\GeoDivisionSeeder;
use Database\Seeders\GeoDivisionTypeSeeder;
use Database\Seeders\LangSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\PlatformVersionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Module « Divisions géographiques » (geo_divisions).
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

function geoDivisionAdmin(): User
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

function geoDivisionModerator(): User
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
 * Payload valide (pays Bénin + type department actif + 2 traductions).
 */
function geoDivisionPayload(array $overrides = []): array
{
    $langs = Lang::pluck('id', 'code');
    $benin = GeoCountry::where('code', 'BEN')->firstOrFail();
    $type = GeoDivisionType::where('code', 'department')->firstOrFail();

    return array_merge([
        'country_id' => $benin->id,
        'division_type_id' => $type->id,
        'parent_id' => null,
        'status' => 'active',
        'notes' => null,
        'translations' => [
            ['lang_id' => $langs['fr'] ?? 1, 'name' => 'Division FR '.uniqid(), 'description' => 'Description FR.'],
            ['lang_id' => $langs['en'] ?? 2, 'name' => 'Division EN '.uniqid(), 'description' => 'English description.'],
        ],
    ], $overrides);
}

it('liste les divisions (DataTables)', function () {
    $admin = geoDivisionAdmin();

    $this->actingAs($admin)
        ->get(route('backend.geodivisions.index'))
        ->assertOk()
        ->assertSee('divisions-table');
});

it('affiche la page de création de division', function () {
    $admin = geoDivisionAdmin();

    $this->actingAs($admin)
        ->get(route('backend.geodivisions.create'))
        ->assertOk();
});

it('crée une division racine avec traductions et slugs générés', function () {
    $admin = geoDivisionAdmin();

    $payload = geoDivisionPayload();

    $this->actingAs($admin)
        ->post(route('backend.geodivisions.store'), $payload)
        ->assertRedirect()
        ->assertSessionHas('success');

    $division = GeoDivision::firstOrFail();

    expect($division->level)->toBe(1);
    expect($division->translations()->count())->toBe(2);

    $frLangId = Lang::where('code', 'fr')->value('id');
    $slug = $division->translations()->where('lang_id', $frLangId)->value('slug');

    expect($slug)->not->toBeNull();
});

it('calcule le niveau (racine = 1, enfant = parent + 1)', function () {
    $admin = geoDivisionAdmin();

    // Parent racine
    $this->actingAs($admin)->post(route('backend.geodivisions.store'), geoDivisionPayload())->assertRedirect();
    $parent = GeoDivision::firstOrFail();

    // Enfant
    $childPayload = geoDivisionPayload(['parent_id' => $parent->id]);
    $this->actingAs($admin)->post(route('backend.geodivisions.store'), $childPayload)->assertRedirect();

    $child = GeoDivision::where('parent_id', $parent->id)->firstOrFail();
    expect($child->level)->toBe(2);
});

it('recalcule le niveau des descendants au changement de parent', function () {
    $admin = geoDivisionAdmin();

    // Deux racines
    $this->actingAs($admin)->post(route('backend.geodivisions.store'), geoDivisionPayload())->assertRedirect();
    $rootA = GeoDivision::orderBy('id', 'asc')->firstOrFail();

    $this->actingAs($admin)->post(route('backend.geodivisions.store'), geoDivisionPayload())->assertRedirect();
    $rootB = GeoDivision::orderBy('id', 'desc')->firstOrFail();

    // Enfant sous rootA (niveau 2)
    $this->actingAs($admin)->post(route('backend.geodivisions.store'), geoDivisionPayload(['parent_id' => $rootA->id]))->assertRedirect();
    $child = GeoDivision::where('parent_id', $rootA->id)->firstOrFail();
    expect($child->level)->toBe(2);

    // On rattache l'enfant à rootB
    $updatePayload = geoDivisionPayload(['parent_id' => $rootB->id]);
    $this->actingAs($admin)
        ->put(route('backend.geodivisions.update', ['geoDivision' => $child->uuid]), $updatePayload)
        ->assertRedirect();

    expect($child->fresh()->level)->toBe(2);
    expect($child->fresh()->parent_id)->toBe($rootB->id);
});

it('refuse un parent situé dans un autre pays', function () {
    $admin = geoDivisionAdmin();

    // Division du Bénin
    $this->actingAs($admin)->post(route('backend.geodivisions.store'), geoDivisionPayload())->assertRedirect();
    $beninDivision = GeoDivision::firstOrFail();

    // Autre pays actif
    $otherCountry = GeoCountry::active()->where('code', '!=', 'BEN')->firstOrFail();

    $payload = geoDivisionPayload([
        'country_id' => $otherCountry->id,
        'parent_id' => $beninDivision->id,
    ]);

    $this->actingAs($admin)
        ->post(route('backend.geodivisions.store'), $payload)
        ->assertSessionHasErrors('parent_id');
});

it('refuse le self-parent en update', function () {
    $admin = geoDivisionAdmin();

    $this->actingAs($admin)->post(route('backend.geodivisions.store'), geoDivisionPayload())->assertRedirect();
    $division = GeoDivision::firstOrFail();

    $payload = geoDivisionPayload(['parent_id' => $division->id]);

    $this->actingAs($admin)
        ->put(route('backend.geodivisions.update', ['geoDivision' => $division->uuid]), $payload)
        ->assertSessionHasErrors('parent_id');
});

it('refuse un cycle (parent = descendant)', function () {
    $admin = geoDivisionAdmin();

    // Racine
    $this->actingAs($admin)->post(route('backend.geodivisions.store'), geoDivisionPayload())->assertRedirect();
    $root = GeoDivision::firstOrFail();

    // Enfant
    $this->actingAs($admin)->post(route('backend.geodivisions.store'), geoDivisionPayload(['parent_id' => $root->id]))->assertRedirect();
    $child = GeoDivision::where('parent_id', $root->id)->firstOrFail();

    // Tenter de rattacher la racine à l'enfant (cycle)
    $payload = geoDivisionPayload(['parent_id' => $child->id]);

    $this->actingAs($admin)
        ->put(route('backend.geodivisions.update', ['geoDivision' => $root->uuid]), $payload)
        ->assertSessionHasErrors('parent_id');
});

it('garde le pays immuable en update', function () {
    $admin = geoDivisionAdmin();

    $this->actingAs($admin)->post(route('backend.geodivisions.store'), geoDivisionPayload())->assertRedirect();
    $division = GeoDivision::firstOrFail();
    $originalCountryId = $division->country_id;

    $otherCountry = GeoCountry::active()->where('code', '!=', 'BEN')->firstOrFail();

    // On tente de changer le pays (champ non soumis à la validation, ignoré)
    $payload = geoDivisionPayload(['country_id' => $otherCountry->id]);

    $this->actingAs($admin)
        ->put(route('backend.geodivisions.update', ['geoDivision' => $division->uuid]), $payload)
        ->assertRedirect();

    expect($division->fresh()->country_id)->toBe($originalCountryId);
});

it('renvoie 404 quand le slug ne correspond pas', function () {
    $admin = geoDivisionAdmin();

    $this->actingAs($admin)->post(route('backend.geodivisions.store'), geoDivisionPayload())->assertRedirect();
    $division = GeoDivision::firstOrFail();

    $this->actingAs($admin)
        ->get(route('backend.geodivisions.show', ['geoDivision' => $division->uuid, 'slug' => 'slug-incoherent']))
        ->assertNotFound();
});

it('affiche la fiche quand le slug correspond', function () {
    $admin = geoDivisionAdmin();

    $this->actingAs($admin)->post(route('backend.geodivisions.store'), geoDivisionPayload())->assertRedirect();
    $division = GeoDivision::firstOrFail();
    $slug = $division->active_translation?->slug ?? 'null';

    $this->actingAs($admin)
        ->get(route('backend.geodivisions.show', ['geoDivision' => $division->uuid, 'slug' => $slug]))
        ->assertOk();
});

it('refuse la suppression si la division est active', function () {
    $admin = geoDivisionAdmin();

    $this->actingAs($admin)->post(route('backend.geodivisions.store'), geoDivisionPayload(['status' => 'active']))->assertRedirect();
    $division = GeoDivision::firstOrFail();

    $this->actingAs($admin)
        ->delete(route('backend.geodivisions.destroy', ['geoDivision' => $division->uuid]))
        ->assertSessionHas('error');

    expect(GeoDivision::find($division->id))->not->toBeNull();
});

it('refuse la suppression si la division a des enfants', function () {
    $admin = geoDivisionAdmin();

    $this->actingAs($admin)->post(route('backend.geodivisions.store'), geoDivisionPayload(['status' => 'inactive']))->assertRedirect();
    $parent = GeoDivision::firstOrFail();

    $this->actingAs($admin)->post(route('backend.geodivisions.store'), geoDivisionPayload(['status' => 'inactive', 'parent_id' => $parent->id]))->assertRedirect();

    $this->actingAs($admin)
        ->delete(route('backend.geodivisions.destroy', ['geoDivision' => $parent->uuid]))
        ->assertSessionHas('error');

    expect(GeoDivision::find($parent->id))->not->toBeNull();
});

it('passe une division en corbeille puis la restaure', function () {
    $admin = geoDivisionAdmin();

    $this->actingAs($admin)->post(route('backend.geodivisions.store'), geoDivisionPayload(['status' => 'inactive']))->assertRedirect();
    $division = GeoDivision::firstOrFail();

    $this->actingAs($admin)
        ->delete(route('backend.geodivisions.destroy', ['geoDivision' => $division->uuid]))
        ->assertRedirect();

    expect(GeoDivision::find($division->id))->toBeNull();

    $this->actingAs($admin)
        ->patch(route('backend.geodivisions.restore', ['uuid' => $division->uuid]))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(GeoDivision::find($division->id))->not->toBeNull();
});

it('refuse la restauration si le parent est en corbeille', function () {
    $admin = geoDivisionAdmin();

    $this->actingAs($admin)->post(route('backend.geodivisions.store'), geoDivisionPayload(['status' => 'inactive']))->assertRedirect();
    $parent = GeoDivision::firstOrFail();

    $this->actingAs($admin)->post(route('backend.geodivisions.store'), geoDivisionPayload(['status' => 'inactive', 'parent_id' => $parent->id]))->assertRedirect();
    $child = GeoDivision::where('parent_id', $parent->id)->firstOrFail();

    // Mettre le parent en corbeille
    $this->actingAs($admin)->delete(route('backend.geodivisions.destroy', ['geoDivision' => $parent->uuid]))->assertRedirect();

    // Mettre l'enfant en corbeille
    $this->actingAs($admin)->delete(route('backend.geodivisions.destroy', ['geoDivision' => $child->uuid]))->assertRedirect();

    // Tenter de restaurer l'enfant alors que le parent est en corbeille
    $this->actingAs($admin)
        ->patch(route('backend.geodivisions.restore', ['uuid' => $child->uuid]))
        ->assertSessionHas('error');

    expect(GeoDivision::withTrashed()->find($child->id)->trashed())->toBeTrue();
});

it('force destroy supprime définitivement et refuse si enfants', function () {
    $admin = geoDivisionAdmin();

    $this->actingAs($admin)->post(route('backend.geodivisions.store'), geoDivisionPayload(['status' => 'inactive']))->assertRedirect();
    $parent = GeoDivision::firstOrFail();

    $this->actingAs($admin)->post(route('backend.geodivisions.store'), geoDivisionPayload(['status' => 'inactive', 'parent_id' => $parent->id]))->assertRedirect();
    $child = GeoDivision::where('parent_id', $parent->id)->firstOrFail();

    // Mettre les deux en corbeille
    $this->actingAs($admin)->delete(route('backend.geodivisions.destroy', ['geoDivision' => $child->uuid]))->assertRedirect();
    $this->actingAs($admin)->delete(route('backend.geodivisions.destroy', ['geoDivision' => $parent->uuid]))->assertRedirect();

    // Le parent a un enfant (même en corbeille) → force destroy refusé
    $this->actingAs($admin)
        ->delete(route('backend.geodivisions.force-destroy', ['uuid' => $parent->uuid]))
        ->assertSessionHas('error');

    expect(GeoDivision::withTrashed()->find($parent->id))->not->toBeNull();

    // L'enfant (sans enfant) peut être supprimé définitivement
    $this->actingAs($admin)
        ->delete(route('backend.geodivisions.force-destroy', ['uuid' => $child->uuid]))
        ->assertSessionHas('success');

    expect(GeoDivision::withTrashed()->find($child->id))->toBeNull();
});

it('corbeille des traductions : restore et force destroy, dernière protégée', function () {
    $admin = geoDivisionAdmin();

    $this->actingAs($admin)->post(route('backend.geodivisions.store'), geoDivisionPayload())->assertRedirect();
    $division = GeoDivision::firstOrFail();

    $translation = $division->translations()->where('lang_id', Lang::where('code', 'fr')->value('id'))->first();

    // Suppression de la traduction FR
    $this->actingAs($admin)
        ->delete(route('backend.geodivisions.translation.destroy', ['geoDivisionTranslation' => $translation->uuid]))
        ->assertRedirect();

    // Corbeille des traductions
    $this->actingAs($admin)
        ->get(route('backend.geodivisions.translation.trashed', ['geoDivision' => $division->uuid]))
        ->assertOk()
        ->assertSee($translation->name);

    // Restauration
    $this->actingAs($admin)
        ->patch(route('backend.geodivisions.translation.restore', ['uuid' => $translation->uuid]))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($translation->fresh()->trashed())->toBeFalse();

    // Suppression définitive de la traduction EN (il reste FR, donc autorisé)
    $enTranslation = $division->translations()->where('lang_id', Lang::where('code', 'en')->value('id'))->first();
    $this->actingAs($admin)->delete(route('backend.geodivisions.translation.destroy', ['geoDivisionTranslation' => $enTranslation->uuid]))->assertRedirect();
    $this->actingAs($admin)->delete(route('backend.geodivisions.translation.force_destroy', ['uuid' => $enTranslation->uuid]))->assertRedirect();

    expect(GeoDivisionTranslation::withTrashed()->where('id', $enTranslation->id)->exists())->toBeFalse();
});

it('interdit la suppression de la dernière traduction', function () {
    $admin = geoDivisionAdmin();

    // Une seule traduction (fr)
    $langs = Lang::pluck('id', 'code');
    $benin = GeoCountry::where('code', 'BEN')->firstOrFail();
    $type = GeoDivisionType::where('code', 'department')->firstOrFail();

    $this->actingAs($admin)->post(route('backend.geodivisions.store'), [
        'country_id' => $benin->id,
        'division_type_id' => $type->id,
        'status' => 'active',
        'translations' => [
            ['lang_id' => $langs['fr'], 'name' => 'Unique FR '.uniqid(), 'description' => 'FR'],
        ],
    ])->assertRedirect();

    $division = GeoDivision::firstOrFail();
    $translation = $division->translations()->first();

    $this->actingAs($admin)
        ->delete(route('backend.geodivisions.translation.destroy', ['geoDivisionTranslation' => $translation->uuid]))
        ->assertSessionHas('error');
});

it('refuse l\'accès au modérateur (403 sur destroy, trashed)', function () {
    $moderator = geoDivisionModerator();

    $this->actingAs($moderator)
        ->get(route('backend.geodivisions.trashed'))
        ->assertForbidden();

    $division = GeoDivision::firstOrFail();

    $this->actingAs($moderator)
        ->delete(route('backend.geodivisions.destroy', ['geoDivision' => $division->uuid]))
        ->assertForbidden();
});

it('n\'affiche aucune clé de traduction brute', function () {
    $admin = geoDivisionAdmin();

    $this->actingAs($admin)
        ->get(route('backend.geodivisions.index'))
        ->assertOk()
        ->assertDontSee('divisions-messages')
        ->assertDontSee('ui.index');
});

it('seed du Bénin : 12 départements (niveau 1) et 77 communes (niveau 2)', function () {
    // Seeder les divisions du Bénin
    $this->seed(GeoDivisionSeeder::class);

    $departments = GeoDivision::where('level', 1)->get();
    $communes = GeoDivision::where('level', 2)->get();

    expect($departments)->toHaveCount(12);
    expect($communes)->toHaveCount(77);

    // Chaque commune a un parent
    foreach ($communes as $commune) {
        expect($commune->parent_id)->not->toBeNull();
    }

    // Toutes les divisions ont des traductions fr/en avec slug non nul
    $frLangId = Lang::where('code', 'fr')->value('id');
    $enLangId = Lang::where('code', 'en')->value('id');

    foreach (GeoDivision::all() as $division) {
        expect($division->translations()->where('lang_id', $frLangId)->value('slug'))->not->toBeNull();
        expect($division->translations()->where('lang_id', $enLangId)->value('slug'))->not->toBeNull();
    }
});