<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Lang;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\ProductTag;
use App\Models\ProductTranslation;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ── Idempotence : skip si le produit de référence existe déjà ──
        $existingFrTranslation = ProductTranslation::where('name', 'Vibromasseur Magic')->first();

        if ($existingFrTranslation) {
            $this->command->info('ProductSeeder : le produit « Vibromasseur Magic » existe déjà, seeder ignoré.');

            return;
        }

        // ── Dépendances : catégorie, marque et tags de référence ──
        $category = ProductCategory::first();
        if (! $category) {
            $cli = $this->command;
            $cli->warn('Aucune catégorie de produits trouvée. Création d\'une catégorie « Vibromasseurs » de secours.');

            $category = ProductCategory::create(['sort_order' => 999, 'status' => 'active']);

            $langFr = Lang::where('code', 'fr')->where('is_active', 1)->first();
            $langEn = Lang::where('code', 'en')->where('is_active', 1)->first();

            if ($langFr) {
                $category->translations()->create([
                    'lang_id' => $langFr->id,
                    'name' => 'Vibromasseurs',
                    'description' => 'Catégorie des vibromasseurs.',
                ]);
            }

            if ($langEn) {
                $category->translations()->create([
                    'lang_id' => $langEn->id,
                    'name' => 'Vibrators',
                    'description' => 'Vibrators category.',
                ]);
            }
        }

        $brand = ProductBrand::first();
        if (! $brand) {
            $this->command->warn('Aucune marque de produits trouvée. Les produits seront créés sans marque.');
        }

        $tags = ProductTag::limit(2)->pluck('id')->all();

        // ── Données des produits (prix en unités majeures, devise par défaut de la boutique) ──
        $products = [
            [
                'status' => 'published',
                'is_featured' => true,
                'weight' => 0.35,
                'base_price' => 249.90,
                'compare_at_price' => 299.90,
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Vibromasseur Magic',  'short_description' => 'Vibromasseur discret et élégant.', 'description' => 'Le vibromasseur Magic allie puissance et discrétion pour des moments inoubliables.'],
                    ['lang_code' => 'en', 'name' => 'Magic Vibrator',       'short_description' => 'Discreet and elegant vibrator.',  'description' => 'The Magic vibrator combines power and discretion for unforgettable moments.'],
                ],
            ],
            [
                'status' => 'published',
                'is_featured' => false,
                'weight' => 0.22,
                'base_price' => 159.90,
                'compare_at_price' => null,
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Bague de massage',    'short_description' => 'Bague de massage flexible.',        'description' => 'Une bague de massage flexible pour enrichir les moments à deux.'],
                    ['lang_code' => 'en', 'name' => 'Massage Ring',         'short_description' => 'Flexible massage ring.',            'description' => 'A flexible massage ring to spice up couples\' moments.'],
                ],
            ],
            [
                'status' => 'draft',
                'is_featured' => false,
                'weight' => 0.50,
                'base_price' => 349.90,
                'compare_at_price' => 399.90,
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Coffret Luxe Découverte', 'short_description' => 'Coffret découverte complet.', 'description' => 'Un coffret complet pour découvrir l\'univers glamour avec style.'],
                    ['lang_code' => 'en', 'name' => 'Luxury Discovery Set',     'short_description' => 'Complete discovery set.',    'description' => 'A complete set to explore the glamour universe in style.'],
                ],
            ],
        ];

        foreach ($products as $data) {

            $translations = $data['translations'];
            unset($data['translations']);

            $product = Product::create(array_merge([
                'category_id' => $category->id,
                'brand_id' => $brand?->id ?? null,
            ], $data));

            // Traductions fr/en
            foreach ($translations as $translation) {

                $lang = Lang::where('code', $translation['lang_code'])->where('is_active', 1)->first();

                if (! $lang) {
                    $this->command->error('Langue non trouvée pour le code : '.$translation['lang_code']."\n");

                    continue;
                }

                ProductTranslation::create([
                    'product_id' => $product->id,
                    'lang_id' => $lang->id,
                    'name' => $translation['name'],
                    'short_description' => $translation['short_description'],
                    'description' => $translation['description'],
                ]);
            }

            // Attache les tags récupérés
            if (! empty($tags)) {
                $product->tags()->attach($tags);
            }
        }
    }
}
