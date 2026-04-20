<?php

namespace Database\Seeders;

use App\Models\Formation;
use App\Models\FormationCategory;
use Illuminate\Database\Seeder;

class FormationSeeder extends Seeder
{
    public function run(): void
    {
        $it         = FormationCategory::where('slug', 'it')->first();
        $pmp        = FormationCategory::where('slug', 'pmp')->first();
        $management = FormationCategory::where('slug', 'management')->first();
        $finance    = FormationCategory::where('slug', 'finance')->first();
        $other      = FormationCategory::where('slug', 'other')->first();

        $formations = [
            // ── Informatique & IT ──────────────────────────────────────
            [
                'title'              => 'CompTIA A+ : Technicien Support IT',
                'description'        => "Préparez et obtenez la certification CompTIA A+, la référence mondiale du support informatique. Ce parcours couvre le matériel, les systèmes d'exploitation, la sécurité et le dépannage réseau.",
                'category_id'        => $it?->id,
                'certification_type' => 'CompTIA A+',
                'level'              => 'beginner',
                'is_featured'        => true,
                'language'           => 'fr',
                'date_mode'          => 'range',
                'start_date'         => '2026-05-05',
                'end_date'           => '2026-05-16',
                'duration_hours'     => 40,
                'duration_per_day_hours' => 4,
                'price'              => 350000,
                'currency'           => 'XOF',
            ],
            [
                'title'              => 'Cisco CCNA : Réseaux & Commutation',
                'description'        => "Maîtrisez les fondamentaux des réseaux IP, la configuration des équipements Cisco, le routage OSPF/EIGRP et les VLAN. Préparation complète à l'examen CCNA 200-301.",
                'category_id'        => $it?->id,
                'certification_type' => 'Cisco CCNA',
                'level'              => 'intermediate',
                'is_featured'        => true,
                'language'           => 'fr',
                'date_mode'          => 'range',
                'start_date'         => '2026-06-02',
                'end_date'           => '2026-06-20',
                'duration_hours'     => 60,
                'duration_per_day_hours' => 6,
                'price'              => 480000,
                'currency'           => 'XOF',
            ],
            [
                'title'              => 'Cybersécurité CompTIA Security+',
                'description'        => "Acquérez les compétences essentielles en cybersécurité : gestion des menaces, cryptographie, identité et accès, sécurité réseau et cloud. Préparation à la certification CompTIA Security+.",
                'category_id'        => $it?->id,
                'certification_type' => 'CompTIA Security+',
                'level'              => 'intermediate',
                'is_featured'        => false,
                'language'           => 'fr',
                'date_mode'          => 'range',
                'start_date'         => '2026-07-07',
                'end_date'           => '2026-07-25',
                'duration_hours'     => 56,
                'duration_per_day_hours' => 7,
                'price'              => 520000,
                'currency'           => 'XOF',
            ],

            // ── PMP & Gestion de Projet ────────────────────────────────
            [
                'title'              => 'Certification PMP® — Préparation Complète',
                'description'        => "Le programme de référence pour l'obtention de la certification PMP® du PMI. Couvre les 5 groupes de processus, les 10 domaines de connaissance du PMBOK® Guide et les approches Agile/hybrides.",
                'category_id'        => $pmp?->id,
                'certification_type' => 'PMP',
                'level'              => 'advanced',
                'is_featured'        => true,
                'language'           => 'fr',
                'date_mode'          => 'range',
                'start_date'         => '2026-05-12',
                'end_date'           => '2026-05-30',
                'duration_hours'     => 35,
                'duration_per_day_hours' => 7,
                'price'              => 600000,
                'currency'           => 'XOF',
            ],
            [
                'title'              => 'PRINCE2® Foundation & Practitioner',
                'description'        => "Maîtrisez la méthode PRINCE2® de gestion de projet structurée. Formation intensive couvrant les deux niveaux Foundation et Practitioner pour une double certification.",
                'category_id'        => $pmp?->id,
                'certification_type' => 'PRINCE2',
                'level'              => 'intermediate',
                'is_featured'        => false,
                'language'           => 'fr',
                'date_mode'          => 'range',
                'start_date'         => '2026-08-03',
                'end_date'           => '2026-08-14',
                'duration_hours'     => 40,
                'duration_per_day_hours' => 8,
                'price'              => 550000,
                'currency'           => 'XOF',
            ],

            // ── Management & Leadership ────────────────────────────────
            [
                'title'              => 'Leadership & Management Stratégique',
                'description'        => "Développez vos compétences de leader : vision stratégique, gestion des équipes, communication managériale, prise de décision et conduite du changement dans un contexte africain.",
                'category_id'        => $management?->id,
                'certification_type' => null,
                'level'              => 'intermediate',
                'is_featured'        => true,
                'language'           => 'fr',
                'date_mode'          => 'range',
                'start_date'         => '2026-05-19',
                'end_date'           => '2026-05-23',
                'duration_hours'     => 30,
                'duration_per_day_hours' => 6,
                'price'              => 280000,
                'currency'           => 'XOF',
            ],
            [
                'title'              => 'Gestion des Ressources Humaines',
                'description'        => "Acquérez les fondamentaux RH : recrutement, évaluation des performances, droit du travail, formation et développement des talents. Adapté aux contextes OHADA et Afrique subsaharienne.",
                'category_id'        => $management?->id,
                'certification_type' => null,
                'level'              => 'beginner',
                'is_featured'        => false,
                'language'           => 'fr',
                'date_mode'          => 'range',
                'start_date'         => '2026-09-07',
                'end_date'           => '2026-09-11',
                'duration_hours'     => 25,
                'duration_per_day_hours' => 5,
                'price'              => 220000,
                'currency'           => 'XOF',
            ],

            // ── Finance & Comptabilité ─────────────────────────────────
            [
                'title'              => 'Comptabilité SYSCOHADA & Reporting Financier',
                'description'        => "Maîtrisez le Système Comptable OHADA révisé (SYSCOHADA), la production des états financiers, l'analyse financière et les normes IFRS pour les entreprises africaines.",
                'category_id'        => $finance?->id,
                'certification_type' => 'SYSCOHADA',
                'level'              => 'intermediate',
                'is_featured'        => false,
                'language'           => 'fr',
                'date_mode'          => 'range',
                'start_date'         => '2026-06-15',
                'end_date'           => '2026-06-26',
                'duration_hours'     => 40,
                'duration_per_day_hours' => 8,
                'price'              => 320000,
                'currency'           => 'XOF',
            ],

            // ── Autres Certifications ──────────────────────────────────
            [
                'title'              => 'ISO 9001 : Systèmes de Management de la Qualité',
                'description'        => "Comprenez les exigences de la norme ISO 9001:2015, apprenez à mettre en place un SMQ efficace et préparez-vous à l'audit de certification. Formation avec exercices pratiques.",
                'category_id'        => $other?->id,
                'certification_type' => 'ISO 9001',
                'level'              => 'intermediate',
                'is_featured'        => false,
                'language'           => 'fr',
                'date_mode'          => 'range',
                'start_date'         => '2026-10-05',
                'end_date'           => '2026-10-09',
                'duration_hours'     => 24,
                'duration_per_day_hours' => 6,
                'price'              => 260000,
                'currency'           => 'XOF',
            ],
        ];

        foreach ($formations as $data) {
            Formation::firstOrCreate(
                ['title' => $data['title']],
                $data
            );
        }
    }
}
