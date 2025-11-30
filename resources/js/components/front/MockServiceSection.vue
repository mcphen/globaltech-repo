<template>
    <section class="py-20 bg-white" :class="props.bgColor">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-serif font-bold text-gray-900">Nos Services</h2>
                <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
                    Explorez l’ensemble de nos services conçus pour accompagner votre entreprise vers la réussite.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-8">
                <!-- Service cards with mock data -->
                <div
                    v-for="service in services"
                    :key="service.id"
                    class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow border border-gray-100"
                >
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 flex items-center justify-center bg-primary/10 rounded-full mr-4">
                                <span class="text-2xl text-primary">{{ service.icon }}</span>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900">{{ service.title }}</h3>
                        </div>
                        <p class="text-gray-700 font-medium mb-3">{{ service.subtitle }}</p>
                        <div class="space-y-3">
                            <div v-for="(item, index) in service.items" :key="index" class="flex">
                                <div class="flex-shrink-0 mr-2">
                                    <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ item.title }}</p>
                                    <p class="text-gray-600 text-sm">{{ item.description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-12">
                <Link
                    :href="'/services'"
                    class="inline-block px-8 py-3 rounded-full border border-primary/30 text-primary hover:border-primary hover:bg-primary/5 font-medium transition-colors"
                >
                    En savoir plus sur nos services
                </Link>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

interface Props {
    bgColor?: string;
}

const props = defineProps<Props>();

// Interface for service data
interface ServiceItem {
    title: string;
    description: string;
}

interface Service {
    id: number;
    icon: string;
    title: string;
    subtitle: string;
    items: ServiceItem[];
}

// Mock data updated from the provided image: LES SERVICES
const services = ref<Service[]>([
    {
        id: 1,
        icon: "🛠️",
        title: "TRAVAUX",
        subtitle: "Conception, déploiement et maintenance d'infrastructures télécom et réseaux.",
        items: [
            {
                title: "Études topographiques et étude de pose de câbles FO",
                description: ""
            },
            {
                title: "Pose et raccordement de Câbles à Fibres Optiques",
                description: ""
            },
            {
                title: "Réalisation de Réseaux FTTH",
                description: ""
            },
            {
                title: "Réalisation de Réseaux informatiques câblés et sans fil",
                description: ""
            },
            {
                title: "Installation de Liaisons de Transmission Numérique (PDH/SDH)",
                description: ""
            },
            {
                title: "Installation et Maintenance de Réseaux (PABX/IPBX)",
                description: ""
            },
            {
                title: "Installation de liaisons à Faisceaux hertziens",
                description: ""
            }
        ]
    },
    {
        id: 2,
        icon: "📦",
        title: "FOURNITURE ET INSTALLATION D’ÉQUIPEMENTS",
        subtitle: "Fourniture et intégration d’équipements télécoms, informatiques et de sûreté.",
        items: [
            {
                title: "Fourniture de Matériels de Télécommunications",
                description: ""
            },
            {
                title: "Fourniture de Matériels Informatiques et Divers",
                description: ""
            },
            {
                title: "Fourniture de Terminaux Téléphoniques, de PABX et d’IPBX",
                description: ""
            },
            {
                title: "Fourniture de Matériels de sécurité : caméras, contrôle d’accès et système de gestion de temps (pointeuse)",
                description: ""
            }
        ]
    },
    {
        id: 3,
        icon: "🎓",
        title: "FORMATIONS",
        subtitle: "Formations opérationnelles autour des réseaux GPON et de la fibre optique.",
        items: [
            {
                title: "Construire de meilleurs réseaux GPON sur la plateforme IQGeo",
                description: ""
            },
            {
                title: "Construire de meilleurs réseaux GPON sur la plateforme ArcGIS",
                description: ""
            },
            {
                title: "Construire de meilleurs réseaux GPON sur la plateforme Setics Sttar",
                description: ""
            },
            {
                title: "Comprendre la technologie GPON",
                description: ""
            },
            {
                title: "Réaliser les dossiers d’études Fibre Optique",
                description: ""
            },
            {
                title: "Mettre en œuvre les travaux techniques de Fibre Optique",
                description: ""
            },
            {
                title: "Maintenir et Étendre les Réseaux Fibre Optique",
                description: ""
            }
        ]
    },
    {
        id: 4,
        icon: "🧠",
        title: "INGÉNIERIE, EXPERTISE ET CONSEILS",
        subtitle: "Études, optimisation et accompagnement stratégique des réseaux.",
        items: [
            {
                title: "Étude et Conception de Réseaux FTTx",
                description: ""
            },
            {
                title: "Dimensionnement et Restructuration de Réseaux 4G et 5G",
                description: ""
            },
            {
                title: "Évaluation et Dépollution d’Offres",
                description: ""
            },
            {
                title: "Gestion des flux de Trafic et Connaissances des causes d’inefficacité d’un Réseau",
                description: ""
            },
            {
                title: "Diagnostic et audit des imperfections d’un Réseau",
                description: ""
            }
        ]
    }
]);
</script>
