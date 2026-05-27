<?php

namespace Database\Seeders;

use App\Models\About;
use Illuminate\Database\Seeder;

class AboutSeeder extends Seeder
{
    public function run(): void
    {
        About::updateOrCreate([], [
            // Hero
            'hero_badge'    => 'Présent dans 12 pays africains',
            'hero_title'    => 'À propos de GlobalTECH',
            'hero_subtitle' => 'Depuis 2010, nous formons et certifions les talents d\'Afrique aux standards internationaux les plus exigeants.',

            // Notre Histoire
            'history_label' => 'Notre Histoire',
            'history_title' => 'Former l\'Afrique de demain',
            'content'       => '<p><strong>GlobalTECH EDUCATION Africa</strong> est une institution de formation certifiante de référence, fondée en 2010 à Abidjan, Côte d\'Ivoire. Notre mission : élever le niveau de compétence professionnelle sur le continent africain en dispensant des formations certifiantes reconnues mondialement.</p><p>Nous intervenons dans trois domaines d\'excellence : l\'Informatique & IT, le Management de Projet (PMP), et le Management & Leadership. Nos programmes sont conçus par des experts praticiens et alignés sur les standards PMI, CompTIA, Microsoft, Cisco et AWS.</p><p>Avec plus de <strong>5 000 professionnels certifiés</strong> et <strong>500 entreprises clientes</strong> dans 12 pays africains, GlobalTECH est aujourd\'hui le partenaire de formation de référence des grandes entreprises, institutions et groupes internationaux opérant en Afrique.</p>',

            'stats' => [
                ['value' => '500+', 'label' => 'Entreprises'],
                ['value' => '5K+',  'label' => 'Certifiés'],
                ['value' => '12',   'label' => 'Pays'],
            ],

            'values' => [
                ['icon' => 'bi-award-fill',   'color' => '#2563EB', 'title' => 'Excellence',       'desc' => 'Standards internationaux dans chaque formation'],
                ['icon' => 'bi-globe-africa', 'color' => '#16A34A', 'title' => 'Afrique d\'abord', 'desc' => 'Des contenus adaptés au contexte africain'],
                ['icon' => 'bi-people-fill',  'color' => '#D97706', 'title' => 'Impact humain',    'desc' => 'Le développement des talents au cœur de tout'],
                ['icon' => 'bi-shield-check', 'color' => '#7C3AED', 'title' => 'Intégrité',        'desc' => 'Certifications officielles et transparentes'],
            ],

            // Timeline
            'timeline_label' => 'Notre Parcours',
            'timeline_title' => '15 ans d\'excellence',
            'milestones'     => [
                ['year' => '2010', 'event' => 'Création de GlobalTECH EDUCATION Africa à Abidjan'],
                ['year' => '2013', 'event' => 'Accréditation PMI — premier centre agréé en Afrique de l\'Ouest'],
                ['year' => '2016', 'event' => 'Ouverture des bureaux de Dakar et Bamako'],
                ['year' => '2018', 'event' => 'Partenariat stratégique Microsoft et Cisco'],
                ['year' => '2020', 'event' => 'Lancement de la plateforme e-Learning panafricaine'],
                ['year' => '2022', 'event' => '5 000 professionnels certifiés — cap historique'],
                ['year' => '2024', 'event' => 'Expansion au Cameroun, Burkina Faso et Togo'],
            ],

            // Bureaux
            'offices_title'    => 'Nos bureaux en Afrique',
            'offices_subtitle' => 'Une présence locale forte pour mieux servir nos clients partout sur le continent.',
            'offices'          => [
                ['country' => 'Côte d\'Ivoire', 'city' => 'Abidjan',    'flag' => '🇨🇮', 'role' => 'Siège Social',    'address' => 'Plateau, Abidjan'],
                ['country' => 'Sénégal',        'city' => 'Dakar',       'flag' => '🇸🇳', 'role' => 'Bureau Régional', 'address' => 'Plateau, Dakar'],
                ['country' => 'Mali',           'city' => 'Bamako',      'flag' => '🇲🇱', 'role' => 'Bureau Régional', 'address' => 'ACI 2000, Bamako'],
                ['country' => 'Cameroun',       'city' => 'Douala',      'flag' => '🇨🇲', 'role' => 'Bureau Régional', 'address' => 'Bonanjo, Douala'],
                ['country' => 'Burkina Faso',   'city' => 'Ouagadougou', 'flag' => '🇧🇫', 'role' => 'Bureau Régional', 'address' => 'Ouaga 2000'],
                ['country' => 'Togo',           'city' => 'Lomé',        'flag' => '🇹🇬', 'role' => 'Représentation',  'address' => 'Centre-ville, Lomé'],
            ],

            // CTA
            'cta_title'    => 'Faisons connaissance',
            'cta_subtitle' => 'Contactez nos conseillers pour discuter de vos besoins en formation et certification.',
        ]);
    }
}
