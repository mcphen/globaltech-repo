<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceItem;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        ServiceItem::query()->delete();
        Service::query()->delete();

        // Seed services and their items
        $services = [
            [
                'id' => 1,
                'icon' => "💼",
                'title' => "CONSEIL",
                'subtitle' => "Vous accompagner à chaque étape de votre développement avec une stratégie sur mesure.",
                'items' => [
                    [
                        'title' => "Accompagnement en création d'entreprise",
                        'description' => "Nous vous guidons de l'idée au lancement : choix de la forme juridique, démarches administratives, business plan, stratégie de financement."
                    ],
                    [
                        'title' => "Optimisation des coûts",
                        'description' => "Analyse des charges, redéfinition des priorités budgétaires, rationalisation des ressources pour maximiser vos marges."
                    ],
                    [
                        'title' => "Définition et fixation d'objectifs",
                        'description' => "Mise en place d'indicateurs clairs, atteignables et mesurables pour orienter votre performance."
                    ],
                    [
                        'title' => "Transformation numérique",
                        'description' => "Digitalisation des opérations, adoption d'outils technologiques adaptés à vos enjeux."
                    ]
                ]
            ],
            [
                'id' => 2,
                'icon' => "📊",
                'title' => "ASSISTANCE COMPTABLE",
                'subtitle' => "Vous libérer des contraintes comptables tout en assurant la conformité et la transparence financière.",
                'items' => [
                    [
                        'title' => "Assistance comptable complète",
                        'description' => "Tenue de votre comptabilité et gestion de l'ensemble des déclarations obligatoires (TVA, IS, etc.)."
                    ],
                    [
                        'title' => "Élaboration des états financiers annuels",
                        'description' => "Production des bilans, comptes de résultat et annexes dans le respect des normes comptables."
                    ],
                    [
                        'title' => "Conseil spécifique aux dirigeants",
                        'description' => "Analyse personnalisée de votre situation financière pour une prise de décision éclairée."
                    ],
                    [
                        'title' => "Gestion de la paie",
                        'description' => "Externalisation complète de la paie avec conformité aux obligations sociales et fiscales."
                    ]
                ]
            ],
            [
                'id' => 3,
                'icon' => "🔍",
                'title' => "AUDIT COMPTABLE ET FINANCIER",
                'subtitle' => "Apporter une vision claire, objective et fiable de votre situation financière.",
                'items' => [
                    [
                        'title' => "Gestion des données financières",
                        'description' => "Organisation, traitement et sécurisation des données pour une information de qualité."
                    ],
                    [
                        'title' => "Vérification de la sincérité des états comptables",
                        'description' => "Audit indépendant pour garantir la régularité et la fiabilité des comptes."
                    ],
                    [
                        'title' => "Gestion de la trésorerie",
                        'description' => "Analyse des flux financiers, optimisation des disponibilités et anticipation des besoins de liquidité."
                    ],
                    [
                        'title' => "Évaluation du contrôle interne",
                        'description' => "Analyse du dispositif de contrôle pour prévenir les erreurs et les fraudes."
                    ]
                ]
            ],
            [
                'id' => 4,
                'icon' => "🛡️",
                'title' => "GOUVERNANCE & RISQUES",
                'subtitle' => "Structurer la gouvernance et anticiper les risques pour sécuriser et dynamiser l'entreprise.",
                'items' => [
                    [
                        'title' => "Diagnostic de l'entreprise",
                        'description' => "Évaluation globale des forces, faiblesses et opportunités pour une vision stratégique."
                    ],
                    [
                        'title' => "Production de la cartographie des risques",
                        'description' => "Identification, hiérarchisation et documentation des principaux risques internes et externes."
                    ],
                    [
                        'title' => "Mise en place d'un plan de mitigation",
                        'description' => "Développement de stratégies concrètes pour réduire ou maîtriser les risques identifiés."
                    ],
                    [
                        'title' => "Élaboration des budgets",
                        'description' => "Préparation de budgets prévisionnels alignés sur les objectifs stratégiques."
                    ]
                ]
            ]
        ];

        foreach ($services as $serviceData) {
            $items = $serviceData['items'];
            unset($serviceData['items']);

            $service = Service::create($serviceData);

            foreach ($items as $index => $itemData) {
                $itemData['order'] = $index;
                $service->items()->create($itemData);
            }
        }
    }
}
