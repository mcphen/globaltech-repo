<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        Product::query()->delete();

        // Seed 3 sample products
        $products = [
            [
                'title' => 'Pack Démarrage',
                'description' => "Offre d'entrée comprenant un audit rapide et des recommandations prioritaires pour lancer votre activité.",
                'image_path' => null, // vous pouvez remplacer par un chemin de stockage ex: 'products/starter.jpg'
                'price' => 9900, // en centimes si vous utilisez un entier ou 99.00 si vous utilisez decimal(8,2)
            ],
            [
                'title' => 'Accompagnement Croissance',
                'description' => "Programme d'accompagnement sur 3 mois incluant suivi mensuel et optimisation des processus clés.",
                'image_path' => null,
                'price' => 29900,
            ],
            [
                'title' => 'Audit Premium',
                'description' => "Audit complet comptable et financier avec rapport détaillé et plan d'action priorisé.",
                'image_path' => null,
                'price' => 49900,
            ],
        ];

        foreach ($products as $data) {
            Product::create($data);
        }
    }
}
