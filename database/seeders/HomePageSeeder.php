<?php

namespace Database\Seeders;

use App\Models\HomePage;
use Illuminate\Database\Seeder;

class HomePageSeeder extends Seeder
{
    public function run(): void
    {
        HomePage::updateOrCreate([], [
            // Hero
            'hero_badge'    => 'Leader dans les formations certifiantes & consulting en Afrique',
            'hero_title_1'  => 'Certifiez',
            'hero_title_2'  => "l'Excellence",
            'hero_title_3'  => 'de vos équipes.',
            'hero_subtitle' => 'GLOBALTECH EDUCATION Africa est le leader panafricain des formations certifiantes en Technologie Numérique, en Management et en Leadership. <strong class="text-white/90">+5 000 professionnels certifiés</strong> dans 12 pays.',
            'hero_trust_badges' => ['CISCO', 'EC-COUNCIL', 'ISC²'],
            'hero_mini_stats'   => [
                ['v' => '12',   'l' => 'Pays'],
                ['v' => '500+', 'l' => 'Entreprises'],
                ['v' => '5K+',  'l' => 'Certifiés'],
            ],

            // Stats bar
            'stats' => [
                ['value' => 500,  'suffix' => '+', 'label' => 'Entreprises formées',      'icon' => 'bi-building'],
                ['value' => 5000, 'suffix' => '+', 'label' => 'Professionnels certifiés', 'icon' => 'bi-people'],
                ['value' => 12,   'suffix' => '',  'label' => 'Pays en Afrique',          'icon' => 'bi-globe-africa'],
                ['value' => 15,   'suffix' => '+', 'label' => "Années d'expertise",       'icon' => 'bi-award'],
            ],

            // Piliers
            'pillars_label'    => 'Nos Programmes',
            'pillars_title'    => "Trois piliers d'excellence",
            'pillars_subtitle' => 'Des programmes certifiants soigneusement conçus pour répondre aux besoins du marché africain et international.',

            // B2B
            'b2b_badge'    => 'Solution Entreprise',
            'b2b_title_1'  => 'Boostez la performance',
            'b2b_title_2'  => 'de vos équipes.',
            'b2b_subtitle' => "Vous êtes une entreprise, un groupe ou une institution ? Nous concevons des parcours de formation certifiante sur mesure pour vos collaborateurs, avec des solutions flexibles en présentiel, à distance ou en blended learning.",
            'b2b_list_items' => [
                "Programmes 100% personnalisés selon vos besoins métiers",
                "Certifications reconnues à l'international",
            ],
            'b2b_benefits' => [
                ['icon' => 'bi-calendar-check', 'title' => 'Planning flexible', 'desc' => 'Sessions adaptées à vos contraintes'],
                ['icon' => 'bi-translate',      'title' => 'Multilingue',       'desc' => 'Français, Anglais'],
                ['icon' => 'bi-laptop',         'title' => 'E-Learning',        'desc' => 'Plateforme LMS intégrée'],
                ['icon' => 'bi-bar-chart',      'title' => 'ROI Garanti',       'desc' => 'Suivi KPI et tableaux de bord'],
            ],

            // Why Us
            'why_label'    => 'Pourquoi Nous',
            'why_title'    => "L'excellence à chaque étape",
            'why_subtitle' => "Rejoignez les milliers de professionnels et d'entreprises qui font confiance à GLOBALTECH EDUCATION Africa.",

            // Portfolio
            'portfolio_label' => 'Nos Réalisations',
            'portfolio_title' => 'Des projets à impact',

            // Testimonials
            'testimonials_label' => 'Témoignages',
            'testimonials_title' => 'Ils nous font confiance',

            // News
            'news_label' => 'Actualités',
            'news_title' => 'Dernières nouvelles',

            // CTA final
            'cta_badge'         => "Démarrez aujourd'hui",
            'cta_title'         => 'Prêt à certifier votre',
            'cta_gradient_word' => 'excellence',
            'cta_subtitle'      => "Rejoignez plus de 5 000 professionnels certifiés à travers l'Afrique. Nos conseillers sont disponibles pour vous guider dans votre parcours.",
        ]);
    }
}
