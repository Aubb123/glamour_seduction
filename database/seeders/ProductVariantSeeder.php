<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductAttributeValue;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductVariantSeeder extends Seeder
{
    /**
     * Seed a small, repeatable set of demo variants for the demo product.
     *
     * The variants are not translated. Their selected attribute values are
     * stored on the product_variant_attribute_value pivot.
     */
    public function run(): void
    {
        $product = Product::whereHas('translations', function ($query) {
            $query->where('name', 'Vibromasseur Magic')
                ->whereHas('lang', fn ($langQuery) => $langQuery->where('code', 'fr'));
        })->first();

        if (! $product) {
            $this->command?->warn('ProductVariantSeeder : produit « Vibromasseur Magic » introuvable; aucune variante créée.');

            return;
        }

        $variantDefinitions = [
            [
                'sku' => 'MAGIC-ROUGE',
                'colour' => 'rouge',
            ],
            [
                'sku' => 'MAGIC-NOIR',
                'colour' => 'noir',
            ],
            [
                'sku' => 'MAGIC-BLANC',
                'colour' => 'blanc',
            ],
        ];

        $variantsWithValues = [];

        foreach ($variantDefinitions as $definition) {
            $value = ProductAttributeValue::where('value', $definition['colour'])
                ->whereHas('attribute.translations', function ($query) {
                    $query->where('name', 'Couleur');
                })
                ->first();

            if (! $value) {
                $this->command?->warn("ProductVariantSeeder : valeur de couleur « {$definition['colour']} » introuvable; aucune variante créée.");

                return;
            }

            $variantsWithValues[] = [$definition, $value];
        }

        DB::transaction(function () use ($product, $variantsWithValues): void {
            $hasDefaultVariant = $product->variants()->where('is_default', true)->exists();

            foreach ($variantsWithValues as [$definition, $value]) {
                $variant = ProductVariant::firstOrNew(['sku' => $definition['sku']]);

                if ($variant->exists && (int) $variant->product_id !== (int) $product->id) {
                    $this->command?->warn("ProductVariantSeeder : SKU « {$definition['sku']} » déjà utilisé par un autre produit; variante ignorée.");

                    continue;
                }

                if (! $variant->exists) {
                    $variant->product_id = $product->id;
                    $variant->barcode = null;
                    $variant->price = null; // Hérite du prix de base du produit.
                    $variant->status = 'active';
                    $variant->is_default = ! $hasDefaultVariant;
                    $variant->save();
                    $hasDefaultVariant = true;
                }

                // Idempotent : le même couple variante/valeur n'est pas dupliqué.
                $variant->attributeValues()->syncWithoutDetaching([$value->id]);
            }
        });
    }
}
