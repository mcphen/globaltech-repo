<template>
    <section class="py-20 bg-white" :class="props.bgColor">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-serif font-bold text-gray-900">Nos Services</h2>
                <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
                    Découvrez comment nous pouvons transformer votre vision en un événement mémorable
                </p>
            </div>
            <div v-if="isLoading" class="flex justify-center items-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary"></div>
            </div>
            <div v-else-if="error" class="text-center text-red-500 py-8">
                {{ error }}
            </div>
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Service cards dynamically generated with flip animation -->
                <div
                    v-for="(service, index) in services"
                    :key="service.id"
                    class="flip-card h-96"
                >
                    <div class="flip-card-inner">
                        <!-- Front of the card -->
                        <div class="flip-card-front rounded-xl overflow-hidden shadow-sm border border-gray-100">
                            <!-- Card header with icon and title on alternating blue/orange background -->
                            <div
                                class="p-6 text-white flex items-center"
                                :class="index % 2 === 0 ? 'bg-blue-600' : 'bg-orange-500'"
                            >
                                <div v-if="service.icon" class="mr-4 text-2xl">
                                    <i :class="`bi ${service.icon}`"></i>
                                </div>
                                <div v-else class="mr-4 text-2xl">
                                    <i class="bi bi-star-fill"></i>
                                </div>
                                <h3 class="text-xl font-semibold">{{ service.title }}</h3>
                            </div>

                            <!-- Card content -->
                            <div class="p-6 bg-white h-full">
                                <p v-if="service.subtitle" class="text-gray-700 font-medium mb-4">{{ service.subtitle }}</p>
                                <p class="text-gray-600" v-if="service.description" v-html="truncateHtml(service.description)"></p>
                                <div v-if="service.min_price" class="mt-4 text-primary font-medium">
                                    À partir de {{ service.min_price }} €
                                </div>
                            </div>
                        </div>

                        <!-- Back of the card -->
                        <div
                            class="flip-card-back rounded-xl overflow-hidden shadow-sm border border-gray-100 p-6"
                            :class="index % 2 === 0 ? 'bg-blue-50' : 'bg-orange-50'"
                        >
                            <h3
                                class="text-xl font-semibold mb-4"
                                :class="index % 2 === 0 ? 'text-blue-600' : 'text-orange-500'"
                            >
                                {{ service.title }}
                            </h3>

                            <!-- Service items -->
                            <div class="overflow-y-auto h-full">
                                <div v-if="service.items && service.items.length > 0">
                                    <div
                                        v-for="(item, itemIndex) in service.items"
                                        :key="itemIndex"
                                        class="mb-4"
                                    >
                                        <h4 class="font-medium mb-1">{{ item.title }}</h4>
                                        <p class="text-sm text-gray-600">{{ item.description }}</p>
                                    </div>
                                </div>
                                <div v-else class="text-gray-500 italic">
                                    Aucun détail disponible
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
                    Tous nos services
                </Link>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
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
    title: string;
    description?: string;
    image_url?: string | null;
    min_price?: number | null;
    icon?: string | null;
    subtitle?: string | null;
    items?: ServiceItem[];
}

// Reactive state
const services = ref<Service[]>([]);
const isLoading = ref(true);
const error = ref<string | null>(null);

// Function to fetch services from API
const fetchServices = async () => {
    try {
        isLoading.value = true;
        error.value = null;

        // API call to get services with items
        const response = await axios.get('/api/services/all-with-items');

        if (response.status === 200 && response.data) {
            services.value = response.data;
        } else {
            error.value = 'Impossible de charger les données des services';
        }
    } catch (err) {
        console.error('Erreur lors du chargement des services:', err);
        error.value = 'Une erreur est survenue lors du chargement des données';
    } finally {
        isLoading.value = false;
    }
};

// Fonction pour tronquer le HTML tout en conservant la structure
const truncateHtml = (html, maxLength = 120) => {
    // Retirer les balises HTML pour compter les caractères
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = html;
    const textContent = tempDiv.textContent || tempDiv.innerText || '';

    if (textContent.length <= maxLength) {
        return html;
    }

    // Chercher où couper tout en gardant les balises intactes
    let truncated = '';
    let charCount = 0;
    let inTag = false;

    for (let i = 0; i < html.length; i++) {
        const char = html[i];

        if (char === '<') {
            inTag = true;
            truncated += char;
        } else if (char === '>') {
            inTag = false;
            truncated += char;
        } else if (!inTag) {
            // On compte seulement les caractères hors des balises
            if (charCount < maxLength) {
                truncated += char;
                charCount++;
            } else if (charCount === maxLength) {
                truncated += '...';
                charCount++;
            }
        } else {
            // Caractère à l'intérieur d'une balise
            truncated += char;
        }
    }

    // Assurer que toutes les balises sont fermées correctement
    const openTags = [];
    const regex = /<([^\/\s>]+)([^>]*)>/g;
    const closeRegex = /<\/([^>]+)>/g;
    let match;

    while ((match = regex.exec(truncated)) !== null) {
        // Ignorer les balises auto-fermantes comme <img/>
        if (!/\/>$/.test(match[0])) {
            openTags.push(match[1]);
        }
    }

    while ((match = closeRegex.exec(truncated)) !== null) {
        // Retirer la dernière occurrence de cette balise
        const tagIndex = openTags.lastIndexOf(match[1]);
        if (tagIndex !== -1) {
            openTags.splice(tagIndex, 1);
        }
    }

    // Fermer les balises restantes dans l'ordre inverse
    while (openTags.length) {
        truncated += `</${openTags.pop()}>`;
    }

    return truncated;
};

onMounted(() => {
    // Load services when component is mounted
    fetchServices();
});
</script>

<style scoped>
/* Additional animations */
.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

/* Flip card animation */
.flip-card {
    perspective: 1000px;
    background-color: transparent;
}

.flip-card-inner {
    position: relative;
    width: 100%;
    height: 100%;
    text-align: left;
    transition: transform 0.6s;
    transform-style: preserve-3d;
}

.flip-card:hover .flip-card-inner {
    transform: rotateY(180deg);
}

.flip-card-front, .flip-card-back {
    position: absolute;
    width: 100%;
    height: 100%;
    -webkit-backface-visibility: hidden;
    backface-visibility: hidden;
    display: flex;
    flex-direction: column;
}

.flip-card-front {
    background-color: white;
}

.flip-card-back {
    transform: rotateY(180deg);
    overflow-y: auto;
}
</style>
