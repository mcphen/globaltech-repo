<template>
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-serif font-bold text-gray-900">Les valeurs de NG CONSULTING</h2>
                <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
                    Nos valeurs fondamentales guident chacune de nos actions et interactions
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div v-for="value in companyValues" :key="value.id" class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-all duration-300 border border-gray-100">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 flex items-center justify-center bg-primary/10 rounded-full mr-4">
                            <span class="text-2xl">{{ value.icon }}</span>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">{{ value.title }}</h3>
                    </div>
                    <p class="text-gray-600">{{ value.description }}</p>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { ref } from 'vue';

interface CompanyValue {
    id: number;
    icon: string;
    title: string;
    description: string;
}

// Mock data for company values
const companyValues = ref<CompanyValue[]>([
    {
        id: 1,
        icon: "🌟",
        title: "Excellence",
        description: "Nous visons le plus haut niveau de qualité dans toutes nos interventions. Notre rigueur dans l'analyse, notre précision dans les livrables et notre professionnalisme au quotidien garantissent des résultats concrets et durables."
    },
    {
        id: 2,
        icon: "🤝",
        title: "Intégrité",
        description: "La transparence, la confidentialité et le respect des normes professionnelles sont non négociables. Nous agissons toujours dans l'intérêt de nos clients, avec objectivité et honnêteté. La confiance se construit sur la vérité."
    },
    {
        id: 3,
        icon: "💪",
        title: "Engagement",
        description: "Chaque projet est une mission que nous portons avec responsabilité et implication. Nous nous investissons pleinement pour comprendre vos besoins, anticiper les enjeux et accompagner votre croissance, comme si c'était la nôtre."
    },
    {
        id: 4,
        icon: "👥",
        title: "Proximité",
        description: "Un accompagnement efficace passe par la compréhension fine de votre réalité de terrain. Être proche de nos clients, c'est être à leur écoute, adapter nos méthodes à leur contexte, et construire ensemble des solutions sur mesure."
    },
    {
        id: 5,
        icon: "💡",
        title: "Innovation",
        description: "Nous intégrons les outils numériques, les meilleures pratiques et les approches modernes pour renforcer la compétitivité de nos clients. Nous cherchons constamment à innover pour vous offrir un avantage stratégique."
    },
    {
        id: 6,
        icon: "📐",
        title: "Rigueur",
        description: "Nos missions comptables, financières ou d'audit exigent une grande exactitude. La rigueur est pour nous une discipline quotidienne : elle se traduit par des processus maîtrisés et un contrôle qualité systématique."
    },
    {
        id: 7,
        icon: "🌱",
        title: "Pérennité",
        description: "Au-delà des solutions rapides, nous visons des résultats durables. Nos recommandations sont pensées pour s'ancrer dans le temps, s'adapter à l'évolution de votre environnement, et soutenir la croissance stable de votre organisation."
    }
]);
</script>
