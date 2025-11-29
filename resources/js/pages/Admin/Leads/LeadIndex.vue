<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';
import { ref, computed } from 'vue';

// Type pour un lead
interface Lead {
    id: number;
    first_name: string;
    last_name: string;
    full_name: string;
    phone: string | null;
    company: string | null;
    email: string;
    consent_at: string | null;
    created_at: string;
    updated_at: string;
    formations_count: number;
    appointments_count: number;
    user: {
        id: number;
        name: string;
        email: string;
        created_at: string;
    } | null;
}

// Props
interface Props {
    leads: Lead[];
    stats: {
        total_leads: number;
        with_consent: number;
        with_formations: number;
        with_appointments: number;
    };
    flash?: { success?: string; error?: string };
}

const props = defineProps<Props>();

// Définition des fil d'Ariane
const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Prospects', href: route('admin.leads.index') }
];

// État de confirmation de suppression
const leadToDelete = ref<Lead | null>(null);
const showDeleteModal = ref(false);

// Statistiques calculées
const globalStats = computed(() => [
    {
        title: 'Total Prospects',
        value: props.stats.total_leads,
        icon: '👥',
        color: 'text-blue-600 bg-blue-50',
        description: 'Nombre total de prospects'
    },
   
    {
        title: 'Avec Formations',
        value: props.stats.with_formations,
        percentage: props.stats.total_leads > 0 ? Math.round((props.stats.with_formations / props.stats.total_leads) * 100) : 0,
        icon: '🎓',
        color: 'text-purple-600 bg-purple-50',
        description: 'Prospects inscrits aux formations'
    },
    {
        title: 'Avec Rendez-vous',
        value: props.stats.with_appointments,
        percentage: props.stats.total_leads > 0 ? Math.round((props.stats.with_appointments / props.stats.total_leads) * 100) : 0,
        icon: '📅',
        color: 'text-orange-600 bg-orange-50',
        description: 'Prospects avec rendez-vous'
    }
]);

// Fonctions
function confirmDelete(lead: Lead) {
    leadToDelete.value = lead;
    showDeleteModal.value = true;
}

function cancelDelete() {
    leadToDelete.value = null;
    showDeleteModal.value = false;
}

function deleteLead() {
    if (leadToDelete.value) {
        window.location.href = route('admin.leads.destroy', leadToDelete.value.id);
    }
    showDeleteModal.value = false;
}

// Formatage de la date
function formatDate(dateString: string | null): string {
    if (!dateString) return 'Non défini';

    const date = new Date(dateString);
    return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    }).format(date);
}

// Statut du consentement
function getConsentStatus(consentAt: string | null): { text: string; class: string } {
    if (!consentAt) {
        return { text: 'Non consent', class: 'bg-red-100 text-red-800' };
    }
    return { text: 'Consenti', class: 'bg-green-100 text-green-800' };
}
</script>

<template>
    <Head title="Gestion des prospects" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm">
            <!-- En-tête avec titre -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 border-b pb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Gestion des prospects</h2>
                    <p class="text-gray-500 mt-1">
                        Gérez les prospects et leurs activités
                    </p>
                </div>
            </div>

            <!-- Messages -->
            <div v-if="props.flash?.success" class="bg-green-50 border-l-4 border-green-500 p-4 rounded-md mb-4 flex items-start">
                <svg class="h-6 w-6 text-green-500 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-green-700">{{ props.flash.success }}</p>
                </div>
            </div>

            <div v-if="props.flash?.error" class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md mb-4 flex items-start">
                <svg class="h-6 w-6 text-red-500 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-red-700">{{ props.flash.error }}</p>
                </div>
            </div>

            <!-- Statistiques globales -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div
                    v-for="stat in globalStats"
                    :key="stat.title"
                    class="bg-white border border-gray-200 rounded-lg p-6 flex items-start gap-4"
                >
                    <div :class="['p-3 rounded-full', stat.color]">
                        <span class="text-xl">{{ stat.icon }}</span>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-600">{{ stat.title }}</p>
                        <div class="flex items-baseline gap-2">
                            <p class="text-2xl font-bold text-gray-900">{{ stat.value }}</p>
                            <p v-if="stat.percentage" class="text-sm font-medium" :class="stat.percentage >= 50 ? 'text-green-600' : 'text-yellow-600'">
                                {{ stat.percentage }}%
                            </p>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ stat.description }}</p>
                    </div>
                </div>
            </div>

            <!-- Tableau des leads -->
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-full bg-white divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Téléphone</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entreprise</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Formations</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">RDV</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date création</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="lead in props.leads" :key="lead.id" class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ lead.id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ lead.full_name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-600">{{ lead.email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-600">{{ lead.phone || 'Non renseigné' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-600">{{ lead.company || 'Non renseignée' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                                    {{ lead.formations_count }} formation(s)
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 bg-orange-100 text-orange-800 text-xs font-medium rounded-full">
                                    {{ lead.appointments_count }} RDV
                                </span>
                            </td>
                           
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ formatDate(lead.created_at) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                <div class="flex justify-center gap-2">
                                    <Link
                                        :href="route('admin.leads.show', lead.id)"
                                        class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 transition inline-flex items-center"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                        </svg>
                                        Détails
                                    </Link>
                                    <button
                                        @click="confirmDelete(lead)"
                                        class="px-3 py-1.5 bg-red-50 text-red-700 rounded-md hover:bg-red-100 transition inline-flex items-center"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        Supprimer
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="props.leads.length === 0">
                            <td colspan="10" class="px-6 py-10 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <p class="text-gray-500">Aucun prospect trouvé.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
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
                Êtes-vous sûr de vouloir supprimer le prospect "<span class="font-medium">{{ leadToDelete?.full_name }}</span>" ?
                <br>Toutes ses données associées seront également supprimées.
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
                    @click="deleteLead"
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition flex-1 max-w-xs"
                >
                    Supprimer
                </button>
            </div>
        </div>
    </div>
</template>