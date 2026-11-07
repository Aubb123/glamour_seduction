<?php

declare(strict_types=1);

use App\Models\GeoCountry;
use App\Models\Lang;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\ProductVariant;
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
 * Module Produits — Consommation du code couleur HEX côté Variantes.
 *
 * Vérifie que la pastille de couleur (rendue par le partial _color-swatch)
 * apparaît bien sur la fiche d'une variante dont une valeur d'attribut de
 * type « color » porte un code HEX.
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

function colorHexVariantAdmin(): User
{
    $country = GeoCountry::where('code', 'BEN')->firstOrFail();

    $admin = User::create([
        'country_id' => $country->id,
        'first_name' => 'Alice', 'last_name' => 'Martin',
        'email' => 'admin-color-hex@site.com', 'gender' => 'female',
        'phone' => '+22909999999', 'password' => 'password',
        'status' => 'active',
    ]);

    $admin->forceFill([
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
    ])->save();

    $admin->assignRole('administrator');

    return $admin;
}

/**
 * Crée un produit + une valeur d'attribut de type « color » portant un HEX,
 * puis une variante rattachée à cette valeur.
 */
function colorHexVariantSetup(string $hex = '#ff0000'): array
{
    $fr = Lang::where('code', 'fr')->firstOrFail();

    $category = ProductCategory::create(['sort_order' => random_int(1, 999), 'status' => 'active']);
    $category->translations()->create(['lang_id' => $fr->id, 'name' => 'Cat HEX '.uniqid(), 'description' => 'Desc.']);

    $brand = ProductBrand::create(['sort_order' => random_int(1, 999), 'status' => 'active']);
    $brand->translations()->create(['lang_id' => $fr->id, 'name' => 'Marque HEX '.uniqid(), 'description' => 'Desc.']);

    $product = Product::create([
        'category_id' => $category->id,
        'brand_id' => $brand->id,
        'status' => 'draft',
        'base_price' => 19990,
    ]);
    $product->translations()->create(['lang_id' => $fr->id, 'name' => 'Produit HEX '.uniqid(), 'short_description' => 'Short.', 'description' => 'Desc.']);

    // Attribut de type couleur
    $attribute = ProductAttribute::create([
        'sort_order' => random_int(1, 999),
        'status' => 'active',
        'type' => 'color',
    ]);
    $attribute->translations()->create(['lang_id' => $fr->id, 'name' => 'Couleur HEX '.uniqid(), 'description' => 'Desc.']);

    // Valeur portant un code HEX
    $value = ProductAttributeValue::create([
        'attribute_id' => $attribute->id,
        'value' => 'rouge-'.uniqid(),
        'hex_color' => $hex,
        'sort_order' => 1,
        'status' => 'active',
    ]);
    $value->translations()->create(['lang_id' => $fr->id, 'label' => 'Rouge HEX '.uniqid()]);

    // Variante rattachée à la valeur couleur
    $variant = ProductVariant::create([
        'product_id' => $product->id,
        'sku' => 'SKU-HEX-'.strtoupper(uniqid()),
        'status' => 'active',
        'is_default' => true,
    ]);
    $variant->attributeValues()->sync([$value->id]);

    return ['product' => $product, 'attribute' => $attribute, 'value' => $value, 'variant' => $variant];
}

it('affiche la pastille du code HEX sur la fiche de la variante', function () {
    $admin = colorHexVariantAdmin();
    $setup = colorHexVariantSetup('#ff0000');

    $html = $this->actingAs($admin)
        ->get(route('backend.product-variants.show', ['productVariant' => $setup['variant']->uuid]))
        ->assertOk()
        ->getContent();

    // La pastille (_color-swatch) rend le code HEX dans son style de fond.
    expect($html)->toContain('background:#ff0000');
});

it('n\'affiche aucune pastille pour une valeur sans code HEX', function () {
    $admin = colorHexVariantAdmin();
    $setup = colorHexVariantSetup();

    // Retire le HEX de la valeur
    $setup['value']->update(['hex_color' => null]);

    $html = $this->actingAs($admin)
        ->get(route('backend.product-variants.show', ['productVariant' => $setup['variant']->uuid]))
        ->assertOk()
        ->getContent();

    expect($html)->not->toContain('background:#ff0000');
});
