<?php

declare(strict_types=1);

use App\Models\GeoCountry;
use App\Models\Lang;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeTranslation;
use App\Models\ProductAttributeValue;
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
 * Module Produits — Attributs + Valeurs d'attributs (`product-attributes`, `product-attribute-values`).
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
        'email' => 'admin-attributes@site.com', 'gender' => 'female',
        'phone' => '+22901111111', 'password' => 'password',
        'status' => 'active',
    ]);

    $admin->forceFill([
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
    ])->save();

    $admin->assignRole('administrator');

    return $admin;
}

function productAttributePayload(array $overrides = []): array
{
    $langFr = Lang::where('code', 'fr')->firstOrFail();
    $langEn = Lang::where('code', 'en')->firstOrFail();
    $uid = uniqid();

    return array_merge([
        'sort_order' => random_int(1000, 99999),
        'status' => 'active',
        'translations' => [
            ['lang_id' => $langFr->id, 'name' => 'Attribut FR '.$uid, 'description' => 'Description FR.'],
            ['lang_id' => $langEn->id, 'name' => 'Attribute EN '.$uid, 'description' => 'English description.'],
        ],
    ], $overrides);
}

function createAttribute(): ProductAttribute
{
    $langFr = Lang::where('code', 'fr')->firstOrFail();

    $attribute = ProductAttribute::create(['sort_order' => random_int(1000, 99999), 'status' => 'active']);

    ProductAttributeTranslation::create([
        'attribute_id' => $attribute->id,
        'lang_id' => $langFr->id,
        'name' => 'Couleur '.uniqid(),
        'description' => 'Description de test.',
    ]);

    return $attribute;
}

it('liste les attributs de produits avec DataTables', function () {
    $admin = productAdmin();

    $html = $this->actingAs($admin)
        ->get(route('backend.product-attributes.index'))
        ->assertOk()
        ->getContent();

    expect($html)->toContain('product-attributes-table');
});

it('affiche la page de création d\'attribut', function () {
    $admin = productAdmin();

    $this->actingAs($admin)
        ->get(route('backend.product-attributes.create'))
        ->assertOk();
});

it('crée un attribut avec ses traductions (slug auto-généré)', function () {
    $admin = productAdmin();

    $this->actingAs($admin)
        ->post(route('backend.product-attributes.store'), productAttributePayload())
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(ProductAttribute::count())->toBe(1);
    expect(ProductAttribute::first()->translations()->count())->toBe(2);
    expect(ProductAttribute::first()->translations()->first()->slug)->not->toBeNull();
});

it('affiche le détail d\'un attribut (show avec slug)', function () {
    $admin = productAdmin();
    $attribute = createAttribute();
    $slug = $attribute->active_translation?->slug ?? 'null';

    $this->actingAs($admin)
        ->get(route('backend.product-attributes.show', ['productAttribute' => $attribute->uuid, 'slug' => $slug]))
        ->assertOk();
});

it('met à jour un attribut et ses traductions', function () {
    $admin = productAdmin();
    $this->actingAs($admin)->post(route('backend.product-attributes.store'), productAttributePayload());
    $attribute = ProductAttribute::firstOrFail();

    $payload = productAttributePayload([
        'sort_order' => random_int(1000, 99999),
        'status' => 'inactive',
    ]);

    $this->actingAs($admin)
        ->put(route('backend.product-attributes.update', ['productAttribute' => $attribute->uuid]), $payload)
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($attribute->fresh()->status->value)->toBe('inactive');
});

it('active et désactive un attribut', function () {
    $admin = productAdmin();
    $this->actingAs($admin)->post(route('backend.product-attributes.store'), productAttributePayload());
    $attribute = ProductAttribute::firstOrFail();

    $this->actingAs($admin)
        ->patch(route('backend.product-attributes.deactivate', ['productAttribute' => $attribute->uuid]))
        ->assertRedirect();

    expect($attribute->fresh()->status->value)->toBe('inactive');

    $this->actingAs($admin)
        ->patch(route('backend.product-attributes.activate', ['productAttribute' => $attribute->uuid]))
        ->assertRedirect();

    expect($attribute->fresh()->status->value)->toBe('active');
});

it('refuse la suppression d\'un attribut actif puis autorise après désactivation', function () {
    $admin = productAdmin();
    $this->actingAs($admin)->post(route('backend.product-attributes.store'), productAttributePayload());
    $attribute = ProductAttribute::firstOrFail();

    // Actif → suppression refusée (règle du ProductAttributeService::destroy)
    $this->actingAs($admin)
        ->from(route('backend.product-attributes.index'))
        ->delete(route('backend.product-attributes.destroy', ['productAttribute' => $attribute->uuid]))
        ->assertRedirect(route('backend.product-attributes.index'))
        ->assertSessionHas('error');

    expect(ProductAttribute::find($attribute->id))->not->toBeNull();

    // Désactivé → suppression autorisée
    $attribute->update(['status' => 'inactive']);

    $this->actingAs($admin)
        ->delete(route('backend.product-attributes.destroy', ['productAttribute' => $attribute->uuid]))
        ->assertRedirect();

    expect(ProductAttribute::find($attribute->id))->toBeNull();
});

it('refuse la suppression d\'un attribut possédant des valeurs (dependentRelations)', function () {
    $admin = productAdmin();
    $attribute = createAttribute();
    $attribute->update(['status' => 'inactive']);

    ProductAttributeValue::create([
        'attribute_id' => $attribute->id,
        'value' => 'rouge-'.uniqid(),
        'sort_order' => 1,
        'status' => 'active',
    ]);

    $this->actingAs($admin)
        ->from(route('backend.product-attributes.index'))
        ->delete(route('backend.product-attributes.destroy', ['productAttribute' => $attribute->uuid]))
        ->assertRedirect(route('backend.product-attributes.index'))
        ->assertSessionHas('error');

    expect(ProductAttribute::find($attribute->id))->not->toBeNull();
});

it('declare dependentRelations = [values] pour ProductAttribute', function () {
    $admin = productAdmin();
    $attribute = createAttribute();

    $property = new ReflectionProperty($attribute, 'dependentRelations');
    expect($property->getValue($attribute))->toBe(['values']);
});

function productAttributeValuePayload(int $attributeId, array $overrides = []): array
{
    $langFr = Lang::where('code', 'fr')->firstOrFail();
    $langEn = Lang::where('code', 'en')->firstOrFail();
    $uid = uniqid();

    return array_merge([
        'attribute_id' => $attributeId,
        'value' => 'cle-'.strtolower($uid),
        'sort_order' => random_int(1, 999),
        'status' => 'active',
        'translations' => [
            ['lang_id' => $langFr->id, 'label' => 'Libellé FR '.$uid],
            ['lang_id' => $langEn->id, 'label' => 'Label EN '.$uid],
        ],
    ], $overrides);
}

it('liste les valeurs d\'attributs avec DataTables', function () {
    $admin = productAdmin();

    $html = $this->actingAs($admin)
        ->get(route('backend.product-attribute-values.index'))
        ->assertOk()
        ->getContent();

    expect($html)->toContain('product-attribute-values-table');
});

it('affiche la page de création de valeur d\'attribut', function () {
    $admin = productAdmin();

    $this->actingAs($admin)
        ->get(route('backend.product-attribute-values.create'))
        ->assertOk();
});

it('crée une valeur d\'attribut avec ses traductions', function () {
    $admin = productAdmin();
    $attribute = createAttribute();

    $this->actingAs($admin)
        ->post(route('backend.product-attribute-values.store'), productAttributeValuePayload($attribute->id))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(ProductAttributeValue::count())->toBe(1);
    expect(ProductAttributeValue::first()->translations()->count())->toBe(2);
});

it('refuse la création d\'une valeur sans attribut parent valide', function () {
    $admin = productAdmin();

    $this->actingAs($admin)
        ->post(route('backend.product-attribute-values.store'), productAttributeValuePayload(999999))
        ->assertRedirect()
        ->assertSessionHasErrors(['attribute_id']);

    expect(ProductAttributeValue::count())->toBe(0);
});