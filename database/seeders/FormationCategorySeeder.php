<?php

namespace Database\Seeders;

use App\Models\FormationCategory;
use Illuminate\Database\Seeder;

class FormationCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name'        => 'Informatique & IT',
                'slug'        => 'it',
                'icon'        => 'bi-cpu',
                'color'       => '#2563EB',
                'background'  => '#EFF6FF',
                'description' => 'Certifications CompTIA, Cisco, Microsoft, Linux et cybersécurité.',
                'is_featured' => true,
                'sort_order'  => 1,
            ],
            [
                'name'        => 'PMP & Gestion de Projet',
                'slug'        => 'pmp',
                'icon'        => 'bi-diagram-3',
                'color'       => '#16A34A',
                'background'  => '#F0FDF4',
                'description' => 'Préparation PMP, PRINCE2, Agile/Scrum et gestion de portefeuille.',
                'is_featured' => true,
                'sort_order'  => 2,
            ],
            [
                'name'        => 'Management & Leadership',
                'slug'        => 'management',
                'icon'        => 'bi-people',
                'color'       => '#D97706',
                'background'  => '#FFFBEB',
                'description' => 'Leadership, gouvernance d\'entreprise, RH et conduite du changement.',
                'is_featured' => true,
                'sort_order'  => 3,
            ],
            [
                'name'        => 'Finance & Comptabilité',
                'slug'        => 'finance',
                'icon'        => 'bi-graph-up',
                'color'       => '#7C3AED',
                'background'  => '#F5F3FF',
                'description' => 'ACCA, audit financier, contrôle de gestion et fiscalité.',
                'is_featured' => false,
                'sort_order'  => 4,
            ],
            [
                'name'        => 'Autres Certifications',
                'slug'        => 'other',
                'icon'        => 'bi-award',
                'color'       => '#475569',
                'background'  => '#F8FAFC',
                'description' => 'Toutes les autres certifications professionnelles reconnues.',
                'is_featured' => false,
                'sort_order'  => 5,
            ],
        ];

        foreach ($categories as $data) {
            FormationCategory::firstOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }
}
