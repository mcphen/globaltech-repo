<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterclassPage;
use App\Models\MasterclassStat;
use App\Models\MasterclassDomaine;
use App\Models\MasterclassFormat;
use App\Models\MasterclassPartenaire;
use App\Models\MasterclassCertification;
use App\Models\MasterclassPublicCible;
use App\Models\MasterclassSession;

class MasterclassSeeder extends Seeder
{
    public function run(): void
    {
        // Page settings (single row)
        MasterclassPage::truncate();
        MasterclassPage::create([
            'hero_badge'          => "Programme d'Excellence — Edition 2025",
            'hero_title'          => 'Executive Masterclass',
            'hero_subtitle'       => 'Africa Leadership Series',
            'hero_description'    => "Des programmes d'élite conçus pour les cadres et dirigeants africains. Développez votre leadership, maîtrisez les outils stratégiques et obtenez des certifications reconnues internationalement.",
            'section_titre'       => "Un parcours d'excellence taillé pour l'Afrique",
            'section_description' => "L'Executive Masterclass de GlobalTECH EDUCATION Africa est un écosystème de formations haut niveau pensé pour les décideurs et cadres supérieurs du continent africain. Nos programmes combinent les meilleures pratiques mondiales avec les réalités du terrain africain.",
            'approche'            => '70% pratique / 30% théorie',
            'langues'             => 'Français & Anglais',
            'cohortes'            => '4 sessions par an',
            'duree_info'          => '3 à 10 jours selon programme',
            'financement_info'    => 'CPF, FDFP, Entreprises',
        ]);

        // Stats
        MasterclassStat::truncate();
        $stats = [
            ['valeur' => '1 200+', 'label' => 'Cadres formés',             'sort_order' => 1],
            ['valeur' => '18',     'label' => 'Pays représentés',          'sort_order' => 2],
            ['valeur' => '96%',   'label' => 'Taux de satisfaction',       'sort_order' => 3],
            ['valeur' => '87%',   'label' => 'Promotion rapide post-formation', 'sort_order' => 4],
        ];
        foreach ($stats as $s) { MasterclassStat::create($s); }

        // Domaines
        MasterclassDomaine::truncate();
        $domaines = [
            [
                'icon' => 'bi-bar-chart-line', 'color' => '#E8A020',
                'titre' => 'Management Stratégique',
                'description' => "Leadership transformationnel, gouvernance d'entreprise, planification stratégique et gestion du changement pour dirigeants.",
                'niveau' => 'C-Level', 'duree' => '5 jours',
                'modules' => ['Leadership & Vision', 'Gouvernance d\'entreprise', 'Gestion du changement', 'Balanced Scorecard'],
                'sort_order' => 1,
            ],
            [
                'icon' => 'bi-cpu', 'color' => '#2563EB',
                'titre' => 'Transformation Digitale',
                'description' => "Piloter la transition numérique de votre organisation : IA, Cloud, Data Analytics et cybersécurité pour décideurs.",
                'niveau' => 'Directeurs IT', 'duree' => '4 jours',
                'modules' => ['Intelligence Artificielle & IA Générative', 'Cloud Strategy', 'Data-Driven Decision Making', 'Cybersécurité stratégique'],
                'sort_order' => 2,
            ],
            [
                'icon' => 'bi-shield-check', 'color' => '#10B981',
                'titre' => 'Gestion de Projets (PMP®)',
                'description' => "Préparation intensive à la certification PMP® du PMI. Méthodologies agiles, waterfall et hybrides pour chefs de projets.",
                'niveau' => 'Chefs de Projets', 'duree' => '10 jours',
                'modules' => ['PMBOK® Guide 7e édition', 'Agile & Scrum', 'Gestion des risques', 'Simulation d\'examen PMP®'],
                'sort_order' => 3,
            ],
            [
                'icon' => 'bi-people', 'color' => '#8B5CF6',
                'titre' => 'Ressources Humaines Stratégiques',
                'description' => "Transformation RH, talent management, HRBP, marque employeur et performance organisationnelle en contexte africain.",
                'niveau' => 'DRH & RH', 'duree' => '4 jours',
                'modules' => ['HRBP & People Analytics', 'Talent Acquisition & Retention', 'Culture organisationnelle', 'Droit du travail OHADA'],
                'sort_order' => 4,
            ],
            [
                'icon' => 'bi-cash-stack', 'color' => '#EF4444',
                'titre' => 'Finance & Gestion des Risques',
                'description' => "Analyse financière avancée, gestion des risques, conformité réglementaire et financement de projets en Afrique.",
                'niveau' => 'DAF & Contrôleurs', 'duree' => '5 jours',
                'modules' => ['Analyse financière avancée', 'Risk Management', 'Normes IFRS', 'Financement de projets'],
                'sort_order' => 5,
            ],
            [
                'icon' => 'bi-globe-americas', 'color' => '#F59E0B',
                'titre' => 'Marketing Digital & Growth',
                'description' => "Stratégies de croissance digitale, branding, e-commerce, réseaux sociaux et analytics pour marchés africains et internationaux.",
                'niveau' => 'Directeurs Marketing', 'duree' => '3 jours',
                'modules' => ['Stratégie de contenu', 'SEO & SEA', 'Social Media Marketing', 'E-commerce & Growth Hacking'],
                'sort_order' => 6,
            ],
        ];
        foreach ($domaines as $d) { MasterclassDomaine::create($d); }

        // Formats
        MasterclassFormat::truncate();
        $formats = [
            [
                'icon' => 'bi-people-fill', 'titre' => 'Présentiel Intensif', 'couleur' => '#0B1437',
                'description' => "Immersion complète dans nos campus. Échanges entre pairs, études de cas africains, mises en situation réelles.",
                'tags' => ['3 à 10 jours', 'Groupe de 10–20 personnes', 'Abidjan, Dakar, Bamako'],
                'sort_order' => 1,
            ],
            [
                'icon' => 'bi-laptop', 'titre' => 'Live Online', 'couleur' => '#2563EB',
                'description' => "Formations en direct via plateforme dédiée. Interaction en temps réel avec formateurs et participants à travers l'Afrique.",
                'tags' => ['Flexible', 'Sessions de 3h', 'Classes virtuelles synchrones'],
                'sort_order' => 2,
            ],
            [
                'icon' => 'bi-building-gear', 'titre' => 'Intra-Entreprise', 'couleur' => '#10B981',
                'description' => "Programme entièrement personnalisé pour vos équipes. Cas pratiques tirés de votre activité, formateurs dédiés.",
                'tags' => ['Sur mesure', 'Dans vos locaux', 'Contenu adapté à votre secteur'],
                'sort_order' => 3,
            ],
            [
                'icon' => 'bi-person-workspace', 'titre' => 'Blended Learning', 'couleur' => '#8B5CF6',
                'description' => "Parcours long terme combinant modules e-learning, sessions présentielles et coaching individuel de suivi.",
                'tags' => ['Hybride', '50% présentiel / 50% digital', '3 à 6 mois'],
                'sort_order' => 4,
            ],
        ];
        foreach ($formats as $f) { MasterclassFormat::create($f); }

        // Partenaires
        MasterclassPartenaire::truncate();
        $partenaires = [
            ['nom' => 'Project Management Institute (PMI)', 'logo_icon' => 'bi-award', 'pays' => 'États-Unis', 'description' => "Autorité mondiale en gestion de projets — certification PMP® officielle.", 'sort_order' => 1],
            ['nom' => 'Microsoft Africa', 'logo_icon' => 'bi-microsoft', 'pays' => 'Afrique', 'description' => "Partenaire technologique pour les formations Cloud Azure et Digital Transformation.", 'sort_order' => 2],
            ['nom' => 'Université Félix Houphouët-Boigny', 'logo_icon' => 'bi-mortarboard', 'pays' => "Côte d'Ivoire", 'description' => "Partenariat académique pour la validation universitaire des parcours Executive.", 'sort_order' => 3],
            ['nom' => 'CGECI — Patronat Ivoirien', 'logo_icon' => 'bi-building-check', 'pays' => "Côte d'Ivoire", 'description' => "Collaboration pour les programmes de développement du leadership en entreprise.", 'sort_order' => 4],
            ['nom' => 'African Development Bank (AfDB)', 'logo_icon' => 'bi-bank', 'pays' => 'Pan-Africain', 'description' => "Soutien institutionnel aux programmes de renforcement des capacités managériales.", 'sort_order' => 5],
            ['nom' => 'ACCA', 'logo_icon' => 'bi-patch-check', 'pays' => 'Royaume-Uni', 'description' => "Partenariat pour les certifications en finance et comptabilité internationale.", 'sort_order' => 6],
        ];
        foreach ($partenaires as $p) { MasterclassPartenaire::create($p); }

        // Certifications
        MasterclassCertification::truncate();
        $certifications = [
            ['titre' => 'Certificat GlobalTECH Executive', 'description' => "Certificat professionnel reconnu par le réseau d'entreprises partenaires GlobalTECH en Afrique de l'Ouest et Centrale.", 'badge_icon' => 'bi-patch-check-fill', 'couleur' => '#E8A020', 'sort_order' => 1],
            ['titre' => 'PMP® — Project Management Professional', 'description' => "La certification la plus reconnue au monde en gestion de projet, délivrée par le Project Management Institute (PMI, USA).", 'badge_icon' => 'bi-award-fill', 'couleur' => '#2563EB', 'sort_order' => 2],
            ['titre' => 'Attestation Universitaire UFHB', 'description' => "Attestation de formation continue en partenariat avec l'Université Félix Houphouët-Boigny d'Abidjan.", 'badge_icon' => 'bi-mortarboard-fill', 'couleur' => '#10B981', 'sort_order' => 3],
            ['titre' => 'Badge Digital Vérifiable', 'description' => "Badge numérique partageable sur LinkedIn, vérifiable par les employeurs via notre plateforme sécurisée.", 'badge_icon' => 'bi-shield-fill-check', 'couleur' => '#8B5CF6', 'sort_order' => 4],
        ];
        foreach ($certifications as $c) { MasterclassCertification::create($c); }

        // Public cibles
        MasterclassPublicCible::truncate();
        $cibles = [
            ['icon' => 'bi-person-badge', 'titre' => 'PDG & Directeurs Généraux', 'description' => "Leaders souhaitant renforcer leur vision stratégique et leur capacité à piloter la transformation.", 'sort_order' => 1],
            ['icon' => 'bi-briefcase', 'titre' => 'Cadres Supérieurs & Middle Management', 'description' => "Managers en transition vers des postes de direction cherchant à développer des compétences transversales.", 'sort_order' => 2],
            ['icon' => 'bi-building', 'titre' => 'Entrepreneurs & Fondateurs de Startups', 'description' => "Dirigeants d'entreprises souhaitant structurer et accélérer la croissance de leur organisation.", 'sort_order' => 3],
            ['icon' => 'bi-bank', 'titre' => 'Cadres du Secteur Public & Institutionnel', 'description' => "Responsables gouvernementaux et institutionnels engagés dans la modernisation de leurs structures.", 'sort_order' => 4],
            ['icon' => 'bi-mortarboard', 'titre' => 'Professionnels en Reconversion', 'description' => "Experts métiers souhaitant élargir leur champ de compétences vers le management et la direction.", 'sort_order' => 5],
        ];
        foreach ($cibles as $c) { MasterclassPublicCible::create($c); }

        // Session CTA (single row)
        MasterclassSession::truncate();
        MasterclassSession::create([
            'badge_text'       => 'Prochaine session — Juillet 2025',
            'cta_titre'        => "Rejoignez l'élite managériale africaine",
            'cta_description'  => "Places limitées à 20 participants par cohorte. Candidatez dès maintenant pour intégrer notre prochaine session Executive Masterclass.",
            'date_session'     => '14 Juillet 2025 — Abidjan',
            'places_total'     => 20,
            'places_restantes' => 7,
            'prix_a_partir'    => '450 000 XOF',
            'financement'      => 'Prise en charge FDFP & entreprises',
            'is_active'        => true,
        ]);
    }
}
