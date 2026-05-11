<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed core reference data
        $this->call([
            UserSeeder::class,
            LeadSeeder::class,
            ServiceSeeder::class,
            ProductSeeder::class,
            FormationCategorySeeder::class,
            FormationSeeder::class,
            MasterclassSeeder::class,
        ]);
    }
}
