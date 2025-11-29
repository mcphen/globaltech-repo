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

// Mock data for TONGOLO TECH services
const services = ref<Service[]>([
    {
        id: 1,
        icon: "💼",
        title: "CONSEIL",
        subtitle: "Vous accompagner à chaque étape de votre développement avec une stratégie sur mesure.",
        items: [
            {
                title: "Accompagnement en création d'entreprise",
                description: "Nous vous guidons de l'idée au lancement : choix de la forme juridique, démarches administratives, business plan, stratégie de financement."
            },
            {
                title: "Optimisation des coûts",
                description: "Analyse des charges, redéfinition des priorités budgétaires, rationalisation des ressources pour maximiser vos marges."
            },
            {
                title: "Définition et fixation d'objectifs",
                description: "Mise en place d'indicateurs clairs, atteignables et mesurables pour orienter votre performance."
            },
            {
                title: "Transformation numérique",
                description: "Digitalisation des opérations, adoption d'outils technologiques adaptés à vos enjeux."
            }
        ]
    },
    {
        id: 2,
        icon: "📊",
        title: "ASSISTANCE COMPTABLE",
        subtitle: "Vous libérer des contraintes comptables tout en assurant la conformité et la transparence financière.",
        items: [
            {
                title: "Assistance comptable complète",
                description: "Tenue de votre comptabilité et gestion de l'ensemble des déclarations obligatoires (TVA, IS, etc.)."
            },
            {
                title: "Élaboration des états financiers annuels",
                description: "Production des bilans, comptes de résultat et annexes dans le respect des normes comptables."
            },
            {
                title: "Conseil spécifique aux dirigeants",
                description: "Analyse personnalisée de votre situation financière pour une prise de décision éclairée."
            },
            {
                title: "Gestion de la paie",
                description: "Externalisation complète de la paie avec conformité aux obligations sociales et fiscales."
            }
        ]
    },
    {
        id: 3,
        icon: "🔍",
        title: "AUDIT COMPTABLE ET FINANCIER",
        subtitle: "Apporter une vision claire, objective et fiable de votre situation financière.",
        items: [
            {
                title: "Gestion des données financières",
                description: "Organisation, traitement et sécurisation des données pour une information de qualité."
            },
            {
                title: "Vérification de la sincérité des états comptables",
                description: "Audit indépendant pour garantir la régularité et la fiabilité des comptes."
            },
            {
                title: "Gestion de la trésorerie",
                description: "Analyse des flux financiers, optimisation des disponibilités et anticipation des besoins de liquidité."
            },
            {
                title: "Évaluation du contrôle interne",
                description: "Analyse du dispositif de contrôle pour prévenir les erreurs et les fraudes."
            }
        ]
    },
    {
        id: 4,
        icon: "🛡️",
        title: "GOUVERNANCE & RISQUES",
        subtitle: "Structurer la gouvernance et anticiper les risques pour sécuriser et dynamiser l'entreprise.",
        items: [
            {
                title: "Diagnostic de l'entreprise",
                description: "Évaluation globale des forces, faiblesses et opportunités pour une vision stratégique."
            },
            {
                title: "Production de la cartographie des risques",
                description: "Identification, hiérarchisation et documentation des principaux risques internes et externes."
            },
            {
                title: "Mise en place d'un plan de mitigation",
                description: "Développement de stratégies concrètes pour réduire ou maîtriser les risques identifiés."
            },
            {
                title: "Élaboration des budgets",
                description: "Préparation de budgets prévisionnels alignés sur les objectifs stratégiques."
            }
        ]
    }
]);
</script>
