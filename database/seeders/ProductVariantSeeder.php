<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProductVariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * NB : pas de données par défaut — les variantes sont créées
     * au fil de l'eau depuis l'interface d'administration des produits.
     * La variante « simple » d'un produit sans variantes est créée
     * automatiquement par le service lors de la première création.
     */
    public function run(): void
    {
        //
    }
}