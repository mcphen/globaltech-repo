<?php

namespace Database\Seeders;

use App\Models\ServicePage;
use Illuminate\Database\Seeder;

class ServicePageSeeder extends Seeder
{
    public function run(): void
    {
        ServicePage::updateOrCreate([], [
            // Hero
            'hero_badge'         => 'Conseil & Expertise',
            'hero_title'         => 'Consulting',
            'hero_gradient_word' => 'à impact',
            'hero_subtitle'      => "Nos experts vous accompagnent dans vos projets de transformation, de renforcement des capacités et d'implémentation de systèmes en Afrique.",
            'hero_stats'         => [
                ['n' => '200+', 'l' => 'Missions réalisées',   'icon' => 'bi-briefcase'],
                ['n' => '12',   'l' => "Pays d'intervention",  'icon' => 'bi-globe-africa'],
                ['n' => '50+',  'l' => 'Experts consultants',  'icon' => 'bi-people'],
                ['n' => '98%',  'l' => 'Satisfaction clients', 'icon' => 'bi-star'],
            ],

            // Services DB
            'services_label' => 'Nos Services',
            'services_title' => 'Ce que nous offrons',

            // Offres de conseil
            'consulting_label'    => 'Nos Expertises',
            'consulting_title'    => 'Nos offres de conseil',
            'consulting_subtitle' => 'Des solutions sur mesure pour entreprises, institutions et organisations internationales.',
            'consulting_offers'   => [
                [
                    'icon'        => 'bi-lightbulb-fill',
                    'color'       => '#2563EB',
                    'title'       => 'Conseil Stratégique IT',
                    'desc'        => "Accompagnement stratégique en transformation digitale, architecture IT et gouvernance des systèmes d'information.",
                    'deliverables' => ['Audit SI', 'Feuille de route digitale', 'Gouvernance IT', 'KPIs & tableaux de bord'],
                    'featured'    => false,
                ],
                [
                    'icon'        => 'bi-diagram-3-fill',
                    'color'       => '#16A34A',
                    'title'       => 'Management de Projet',
                    'desc'        => 'Pilotage de projets complexes selon les standards PMI, PRINCE2 et méthodes agiles. De la planification à la livraison.',
                    'deliverables' => ['PMO externalisé', 'Plan de projet', 'Gestion des risques', 'Reporting exécutif'],
                    'featured'    => true,
                ],
                [
                    'icon'        => 'bi-people-fill',
                    'color'       => '#D97706',
                    'title'       => 'Capital Humain & RH',
                    'desc'        => "Stratégie RH, gestion des talents, plans de développement des compétences et transformation organisationnelle.",
                    'deliverables' => ['Audit RH', 'Plans de formation', 'Évaluation des compétences', 'Change management'],
                    'featured'    => false,
                ],
                [
                    'icon'        => 'bi-shield-check',
                    'color'       => '#7C3AED',
                    'title'       => 'Cybersécurité & Conformité',
                    'desc'        => "Évaluation des risques cyber, mise en conformité réglementaire et implémentation des bonnes pratiques de sécurité.",
                    'deliverables' => ['Audit sécurité', 'RSSI externalisé', 'ISO 27001', 'Politique de sécurité'],
                    'featured'    => false,
                ],
                [
                    'icon'        => 'bi-globe-africa',
                    'color'       => '#0891B2',
                    'title'       => 'Projets Institutionnels',
                    'desc'        => "Réponse aux appels d'offres, implémentation de projets pour ministères, organisations internationales et ONG.",
                    'deliverables' => ["Ingénierie de projet", "Coordination d'équipes", 'Reporting bailleurs', "Évaluation d'impact"],
                    'featured'    => false,
                ],
                [
                    'icon'        => 'bi-graph-up-arrow',
                    'color'       => '#DC2626',
                    'title'       => 'Audit & Performance',
                    'desc'        => "Diagnostic organisationnel, audit des processus, identification des leviers de performance et plan d'optimisation.",
                    'deliverables' => ['Audit opérationnel', 'Benchmarking', "Plan d'amélioration", 'Suivi des actions'],
                    'featured'    => false,
                ],
            ],

            // Process
            'process_label' => 'Notre Approche',
            'process_title' => 'Comment nous travaillons',
            'process_steps' => [
                ['step' => '01', 'icon' => 'bi-telephone',         'title' => 'Diagnostic',   'desc' => 'Échange initial pour comprendre vos enjeux et définir vos objectifs.'],
                ['step' => '02', 'icon' => 'bi-file-earmark-text', 'title' => 'Proposition',  'desc' => "Élaboration d'une offre sur mesure avec planning et budget détaillés."],
                ['step' => '03', 'icon' => 'bi-people',            'title' => 'Intervention', 'desc' => "Déploiement de notre équipe d'experts sur le terrain ou à distance."],
                ['step' => '04', 'icon' => 'bi-bar-chart',         'title' => 'Résultats',    'desc' => 'Livraison des livrables, formation des équipes et transfert de compétences.'],
            ],

            // CTA
            'cta_title'    => 'Un projet en Afrique ?',
            'cta_subtitle' => 'Nos équipes sont prêtes à intervenir dans 12 pays. Décrivez-nous votre besoin et recevez une proposition sous 48h.',
        ]);
    }
}
