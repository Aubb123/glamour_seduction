<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProductWarehouseTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * NB : les traductions des entrepôts (name/address/city/phone) sont créées
     * directement dans ProductWarehouseSeeder (comme ProductCategorySeeder le fait
     * pour ProductCategoryTranslationSeeder). Ce seeder est conservé comme point
     * d'extension dédié, pour rester 1:1 avec ProductCategoryTranslationSeeder.
     */
    public function run(): void
    {
        //
    }
}
