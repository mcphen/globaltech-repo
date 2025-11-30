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

        // Seed services and their items from MockServiceSection.vue
        $services = [
            [
                'id' => 1,
                'icon' => "🛠️",
                'title' => "TRAVAUX",
                'subtitle' => "Conception, déploiement et maintenance d'infrastructures télécom et réseaux.",
                'items' => [
                    [ 'title' => "Études topographiques et étude de pose de câbles FO", 'description' => "" ],
                    [ 'title' => "Pose et raccordement de Câbles à Fibres Optiques", 'description' => "" ],
                    [ 'title' => "Réalisation de Réseaux FTTH", 'description' => "" ],
                    [ 'title' => "Réalisation de Réseaux informatiques câblés et sans fil", 'description' => "" ],
                    [ 'title' => "Installation de Liaisons de Transmission Numérique (PDH/SDH)", 'description' => "" ],
                    [ 'title' => "Installation et Maintenance de Réseaux (PABX/IPBX)", 'description' => "" ],
                    [ 'title' => "Installation de liaisons à Faisceaux hertziens", 'description' => "" ],
                ],
            ],
            [
                'id' => 2,
                'icon' => "📦",
                'title' => "FOURNITURE ET INSTALLATION D’ÉQUIPEMENTS",
                'subtitle' => "Fourniture et intégration d’équipements télécoms, informatiques et de sûreté.",
                'items' => [
                    [ 'title' => "Fourniture de Matériels de Télécommunications", 'description' => "" ],
                    [ 'title' => "Fourniture de Matériels Informatiques et Divers", 'description' => "" ],
                    [ 'title' => "Fourniture de Terminaux Téléphoniques, de PABX et d’IPBX", 'description' => "" ],
                    [ 'title' => "Fourniture de Matériels de sécurité : caméras, contrôle d’accès et système de gestion de temps (pointeuse)", 'description' => "" ],
                ],
            ],
            [
                'id' => 3,
                'icon' => "🎓",
                'title' => "FORMATIONS",
                'subtitle' => "Formations opérationnelles autour des réseaux GPON et de la fibre optique.",
                'items' => [
                    [ 'title' => "Construire de meilleurs réseaux GPON sur la plateforme IQGeo", 'description' => "" ],
                    [ 'title' => "Construire de meilleurs réseaux GPON sur la plateforme ArcGIS", 'description' => "" ],
                    [ 'title' => "Construire de meilleurs réseaux GPON sur la plateforme Setics Sttar", 'description' => "" ],
                    [ 'title' => "Comprendre la technologie GPON", 'description' => "" ],
                    [ 'title' => "Réaliser les dossiers d’études Fibre Optique", 'description' => "" ],
                    [ 'title' => "Mettre en œuvre les travaux techniques de Fibre Optique", 'description' => "" ],
                    [ 'title' => "Maintenir et Étendre les Réseaux Fibre Optique", 'description' => "" ],
                ],
            ],
            [
                'id' => 4,
                'icon' => "🧠",
                'title' => "INGÉNIERIE, EXPERTISE ET CONSEILS",
                'subtitle' => "Études, optimisation et accompagnement stratégique des réseaux.",
                'items' => [
                    [ 'title' => "Étude et Conception de Réseaux FTTx", 'description' => "" ],
                    [ 'title' => "Dimensionnement et Restructuration de Réseaux 4G et 5G", 'description' => "" ],
                    [ 'title' => "Évaluation et Dépollution d’Offres", 'description' => "" ],
                    [ 'title' => "Gestion des flux de Trafic et Connaissances des causes d’inefficacité d’un Réseau", 'description' => "" ],
                    [ 'title' => "Diagnostic et audit des imperfections d’un Réseau", 'description' => "" ],
                ],
            ],
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
