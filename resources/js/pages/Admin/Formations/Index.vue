<script setup lang="ts">
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { ref, computed, onMounted } from 'vue';

interface Formation {
    id: number;
    title: string;
    slug: string;
    description?: string | null;
    image_path?: string | null;
    date_mode: 'single' | 'range';
    date?: string | null;
    start_date?: string | null;
    end_date?: string | null;
    duration_hours?: number | null;
    duration_per_day_hours?: number | null;
    price?: number | null;
    currency?: string | null;
    leads_count: number;
    created_at: string;
}

interface Pagination {
    current_page: number;
    data: Formation[];
    from: number;
    last_page: number;
    links: Array<{ url: string | null; label: string; active: boolean }>;
    per_page: number;
    to: number;
    total: number;
}

interface Props {
    formations: Pagination;
}

const props = defineProps<Props>();
const page = usePage();

// Accéder aux formations
const formationsList = computed(() => props.formations.data);

// Définition des fil d'Ariane
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Formations', href: route('admin.formations.index') }
];

// État de confirmation de suppression
const formationToDelete = ref<Formation | null>(null);
const showDeleteModal = ref(false);

// Mode d'affichage (grille ou liste)
const viewMode = ref<'grid' | 'list'>('list');

// Statistiques
const stats = ref({
    totalFormations: 0,
    totalParticipants: 0,
    upcomingFormations: 0,
    averagePrice: 0
});

// Fonctions
function confirmDelete(formation: Formation) {
    formationToDelete.value = formation;
    showDeleteModal.value = true;
}

function cancelDelete() {
    formationToDelete.value = null;
    showDeleteModal.value = false;
}

function deleteFormation() {
    if (formationToDelete.value) {
        router.delete(route('admin.formations.destroy', formationToDelete.value.id));
    }
    showDeleteModal.value = false;
}

// Formatage de la date
function formatDate(dateString: string): string {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    }).format(date);
}

// Formatage du prix
function formatPrice(price: number, currency: string = 'FCFA'): string {
    return new Intl.NumberFormat('fr-FR', { 
        style: 'currency', 
        currency: currency === 'FCFA' ? 'XOF' : currency 
    }).format(price);
}

// Extraire le texte brut du HTML et le tronquer
function stripAndTruncateHtml(html: string | null, maxLength: number = 100): string {
    if (!html) return '';

    const tempDiv = document.createElement("div");
    tempDiv.innerHTML = html;
    const text = tempDiv.textContent || tempDiv.innerText || "";

    if (text.length > maxLength) {
        return text.substring(0, maxLength) + '...';
    }

    return text;
}

// Vérifier si une formation est à venir
function isUpcoming(formation: Formation): boolean {
    const today = new Date();
    if (formation.date_mode === 'single' && formation.date) {
        return new Date(formation.date) >= today;
    } else if (formation.date_mode === 'range' && formation.start_date) {
        return new Date(formation.start_date) >= today;
    }
    return false;
}

// Calculer les statistiques
function calculateStats() {
    const totalFormations = props.formations.total;
    const totalParticipants = formationsList.value.reduce((sum, formation) => sum + formation.leads_count, 0);
    
    const totalPrice = formationsList.value.reduce((sum, formation) => {
        return sum + (formation.price ? Number(formation.price) : 0);
    }, 0);
    const averagePrice = totalFormations > 0 ? totalPrice / totalFormations : 0;
    
    const upcomingFormations = formationsList.value.filter(isUpcoming).length;

    stats.value = {
        totalFormations,
        totalParticipants,
        upcomingFormations,
        averagePrice
    };
}

onMounted(() => {
    calculateStats();
});
</script>

<template>
    <Head title="Gestion des formations" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm">
            <!-- En-tête avec titre et bouton d'ajout -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 border-b pb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Gestion des formations</h2>
                    <p class="text-gray-500 mt-1">
                        Gérez votre catalogue de formations et consultez les statistiques
                    </p>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Sélecteur de vue -->
                    <div class="flex bg-gray-100 rounded-md p-1">
                        <button
                            @click="viewMode = 'grid'"
                            :class="[
                                'px-3 py-1.5 rounded-md flex items-center gap-2 transition text-sm',
                                viewMode === 'grid'
                                    ? 'bg-white shadow-sm text-blue-600'
                                    : 'text-gray-600 hover:bg-gray-200'
                            ]"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            Grille
                        </button>
                        <button
                            @click="viewMode = 'list'"
                            :class="[
                                'px-3 py-1.5 rounded-md flex items-center gap-2 transition text-sm',
                                viewMode === 'list'
                                    ? 'bg-white shadow-sm text-blue-600'
                                    : 'text-gray-600 hover:bg-gray-200'
                            ]"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                            </svg>
                            Liste
                        </button>
                    </div>

                    <Link
                        :href="route('admin.formations.create')"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition flex items-center gap-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Nouvelle formation
                    </Link>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-blue-600">Total formations</p>
                            <p class="text-2xl font-bold text-blue-900">{{ stats.totalFormations }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-green-600">Total participants</p>
                            <p class="text-2xl font-bold text-green-900">{{ stats.totalParticipants }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-orange-100 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-orange-600">Formations à venir</p>
                            <p class="text-2xl font-bold text-orange-900">{{ stats.upcomingFormations }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-purple-600">Prix moyen</p>
                            <p class="text-2xl font-bold text-purple-900">{{ formatPrice(stats.averagePrice) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Message flash -->
            <!-- <div v-if="page.props.flash && page.props.flash.success" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md mb-4">
                {{ page.props.flash.success }}
            </div> -->
            <div v-if="page.props.flash && (page.props.flash as any).success" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md mb-4">
                {{ (page.props.flash as any).success }}
            </div>

            <!-- Vue en liste (table) - PAR DÉFAUT -->
            <div v-if="viewMode === 'list'" class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Formation</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durée</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Participants</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="formation in formationsList" :key="formation.id" class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <img
                                        v-if="formation.image_path"
                                        :src="formation.image_path"
                                        :alt="formation.title"
                                        class="w-16 h-16 object-cover rounded-md border border-gray-200"
                                    >
                                    <div v-else class="w-16 h-16 bg-gray-100 flex items-center justify-center rounded-md border border-gray-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ formation.title }}</div>
                                        <!-- <div v-if="formation.description" class="text-sm text-gray-500 max-w-xs">
                                            {{ stripAndTruncateHtml(formation.description, 120) }}
                                        </div> -->
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <template v-if="formation.date_mode === 'single'">
                                        {{ formatDate(formation.date!) }}
                                    </template>
                                    <template v-else>
                                        Du {{ formatDate(formation.start_date!) }}<br>au {{ formatDate(formation.end_date!) }}
                                    </template>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <template v-if="formation.date_mode === 'single'">
                                        {{ formation.duration_hours }}h
                                    </template>
                                    <template v-else>
                                        {{ formation.duration_per_day_hours }}h/jour
                                    </template>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div v-if="formation.price" class="text-sm font-medium text-blue-600">
                                    {{ formatPrice(formation.price, formation.currency || 'FCFA') }}
                                </div>
                                <div v-else class="text-sm text-gray-400 italic">
                                    Gratuit
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                                    {{ formation.leads_count }} participant{{ formation.leads_count !== 1 ? 's' : '' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                <div class="flex justify-center gap-2">
                                    <Link
                                        :href="route('admin.formations.show', formation.id)"
                                        class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 transition inline-flex items-center text-sm"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                        </svg>
                                        Détails
                                    </Link>
                                    <Link
                                        :href="route('admin.formations.edit', formation.id)"
                                        class="px-3 py-1.5 bg-yellow-50 text-yellow-700 rounded-md hover:bg-yellow-100 transition inline-flex items-center text-sm"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                        Modifier
                                    </Link>
                                    <Link
                                        :href="route('formations.show', formation.slug)"
                                        target="_blank"
                                        class="px-3 py-1.5 bg-green-50 text-green-700 rounded-md hover:bg-green-100 transition inline-flex items-center text-sm"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM6.75 9.25a.75.75 0 000 1.5h6.5a.75.75 0 000-1.5h-6.5z" clip-rule="evenodd" />
                                        </svg>
                                        Public
                                    </Link>
                                    <button
                                        @click="confirmDelete(formation)"
                                        class="px-3 py-1.5 bg-red-50 text-red-700 rounded-md hover:bg-red-100 transition inline-flex items-center text-sm"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        Supprimer
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="formationsList.length === 0">
                            <td colspan="6" class="px-6 py-10 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                <p class="text-gray-500">Aucune formation trouvée. Commencez par en créer une !</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex items-center justify-between border-t border-gray-200 px-4 py-3 sm:px-6">
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Affichage de <span class="font-medium">{{ props.formations.from }}</span> à
                            <span class="font-medium">{{ props.formations.to }}</span> sur
                            <span class="font-medium">{{ props.formations.total }}</span> formations
                        </p>
                    </div>
                    <div>
                        <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                            <Link
                                v-for="(link, i) in props.formations.links"
                                :key="i"
                                :href="link.url || '#'"
                                v-html="link.label"
                                :class="[
                                    'relative inline-flex items-center px-4 py-2 text-sm',
                                    link.url === null
                                        ? 'text-gray-300 cursor-not-allowed'
                                        : 'text-gray-500 hover:bg-gray-50',
                                    link.active ? 'bg-blue-50 text-blue-600 font-medium border-blue-500 z-10' : 'border-gray-300',
                                    i === 0 ? 'rounded-l-md' : '',
                                    i === props.formations.links.length - 1 ? 'rounded-r-md' : '',
                                    'border'
                                ]"
                            />
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>

    <!-- Modal de confirmation de suppression -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
            <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 text-red-500 mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </div>
            <h3 class="text-xl font-semibold mb-2 text-center">Confirmer la suppression</h3>
            <p class="mb-6 text-gray-600 text-center">
                Êtes-vous sûr de vouloir supprimer la formation "<span class="font-medium">{{ formationToDelete?.title }}</span>" ?
                <br>Cette action est irréversible.
            </p>
            <div class="flex gap-3 justify-center">
                <button
                    @click="cancelDelete"
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition flex-1 max-w-xs"
                >
                    Annuler
                </button>
                <button
                    @click="deleteFormation"
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition flex-1 max-w-xs"
                >
                    Supprimer
                </button>
            </div>
        </div>
    </div>
</template>