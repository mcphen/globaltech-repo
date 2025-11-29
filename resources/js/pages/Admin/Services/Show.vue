<script setup lang="ts">
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';
import { ref, computed } from 'vue';

// Types
interface ServiceItem {
    id: number;
    title: string;
    description: string | null;
    order: number;
}

interface Appointment {
    id: number;
    subject: string;
    description: string | null;
    status: string;
    created_at: string;
    schedule: {
        date: string;
        start_time: string;
        end_time: string;
    };
    client: {
        first_name: string;
        last_name: string;
        email: string;
        phone: string | null;
    };
}

interface Service {
    id: number;
    title: string;
    icon: string | null;
    subtitle: string | null;
    description_old: string | null;
    image_path: string | null;
    min_price: number | null;
    created_at: string;
    updated_at: string;
    items: ServiceItem[];
    appointments: Appointment[];
    appointments_count: number;
}

interface Props {
    service: Service;
}

const props = defineProps<Props>();
const page = usePage();

// Définition des fil d'Ariane
const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Services', href: route('admin.services.index') },
    { title: props.service.title, href: route('admin.services.show', props.service.id) }
];

// Filtre des rendez-vous
const appointmentFilter = ref<'all' | 'pending' | 'confirmed' | 'cancelled'>('all');

// Formatage du prix
function formatPrice(price: number | null): string {
    if (price === null || price === 0) return 'Gratuit';
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(price);
}

// Formatage de la date
function formatDate(dateString: string): string {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: 'long',
        year: 'numeric'
    }).format(date);
}

// Formatage de l'heure
function formatTime(timeString: string): string {
    return timeString.substring(0, 5);
}

// Formatage de la date et heure
function formatDateTime(dateString: string): string {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(date);
}

// Obtenir l'URL complète de l'image
function getImageUrl(imagePath: string | null): string {
    if (!imagePath) return '/images/placeholder-service.jpg';
    return imagePath.startsWith('http') ? imagePath : `/storage/${imagePath}`;
}

// Obtenir la classe de couleur en fonction du statut
function getAppointmentStatusClass(status: string): { class: string, text: string } {
    switch (status) {
        case 'pending':
            return { class: 'bg-yellow-100 text-yellow-800', text: 'En attente' };
        case 'confirmed':
            return { class: 'bg-green-100 text-green-800', text: 'Confirmé' };
        case 'cancelled':
            return { class: 'bg-red-100 text-red-800', text: 'Annulé' };
        case 'completed':
            return { class: 'bg-blue-100 text-blue-800', text: 'Terminé' };
        default:
            return { class: 'bg-gray-100 text-gray-800', text: status };
    }
}

// Filtrer les rendez-vous
const filteredAppointments = computed(() => {
    if (appointmentFilter.value === 'all') {
        return props.service.appointments;
    }
    return props.service.appointments.filter(appointment => appointment.status === appointmentFilter.value);
});

// Statistiques des rendez-vous
const appointmentStats = computed(() => {
    const total = props.service.appointments_count;
    const pending = props.service.appointments.filter(a => a.status === 'pending').length;
    const confirmed = props.service.appointments.filter(a => a.status === 'confirmed').length;
    const cancelled = props.service.appointments.filter(a => a.status === 'cancelled').length;
    const completed = props.service.appointments.filter(a => a.status === 'completed').length;
    
    return {
        total,
        pending,
        confirmed,
        cancelled,
        completed
    };
});
</script>

<template>
    <Head :title="`${service.title} - Détails`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm">
            <!-- En-tête avec titre et actions -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 border-b pb-6">
                <div>
                    <div class="flex items-center gap-3">
                        <span v-if="service.icon" class="text-3xl">{{ service.icon }}</span>
                        <h2 class="text-2xl font-bold text-gray-800">{{ service.title }}</h2>
                    </div>
                    <p class="text-gray-500 mt-1">
                        Créé le {{ formatDateTime(service.created_at) }}
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <Link
                        :href="route('admin.services.index')"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Retour à la liste
                    </Link>
                    <Link
                        :href="route('admin.services.edit', service.id)"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Modifier
                    </Link>
                </div>
            </div>

            <!-- Message flash -->
            <div v-if="page.props.flash?.success" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md mb-4">
                {{ page.props.flash.success }}
            </div>

            <!-- Contenu principal -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Colonne de gauche - Contenu principal -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Image et description -->
                    <div class="bg-white border rounded-lg shadow-sm overflow-hidden">
                        <div class="p-6">
                            <div class="aspect-w-16 aspect-h-9 mb-6">
                                <img
                                    v-if="service.image_path"
                                    :src="getImageUrl(service.image_path)"
                                    :alt="service.title"
                                    class="w-full h-64 object-cover rounded-lg"
                                />
                                <div v-else class="w-full h-64 bg-gray-100 flex items-center justify-center rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                </div>
                            </div>
                            
                            <div v-if="service.subtitle" class="text-lg text-gray-700 mb-4 font-medium">
                                {{ service.subtitle }}
                            </div>
                            
                            <div v-if="service.description_old" class="prose prose-lg max-w-none">
                                <div v-html="service.description_old"></div>
                            </div>
                            <div v-else class="text-gray-500 italic">
                                Aucune description fournie pour ce service.
                            </div>
                        </div>
                    </div>

                    <!-- Éléments du service -->
                    <div v-if="service.items && service.items.length > 0" class="bg-white border rounded-lg shadow-sm overflow-hidden">
                        <div class="px-4 py-3 bg-gray-50 border-b">
                            <h3 class="text-lg font-medium text-gray-900">Éléments inclus</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div
                                    v-for="item in service.items"
                                    :key="item.id"
                                    class="border rounded-lg p-4 hover:bg-gray-50 transition"
                                >
                                    <h4 class="font-medium text-gray-900 mb-2">{{ item.title }}</h4>
                                    <p v-if="item.description" class="text-sm text-gray-600">
                                        {{ item.description }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Colonne de droite - Informations et statistiques -->
                <div class="space-y-6">
                    <!-- Informations du service -->
                    <div class="bg-white border rounded-lg shadow-sm overflow-hidden">
                        <div class="px-4 py-3 bg-gray-50 border-b">
                            <h3 class="text-lg font-medium text-gray-900">Informations</h3>
                        </div>
                        <div class="p-4 space-y-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Prix minimum</h4>
                                <p class="mt-1 text-lg font-semibold text-blue-600">
                                    {{ formatPrice(service.min_price) }}
                                </p>
                            </div>
                            
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Icône</h4>
                                <p class="mt-1 text-2xl">
                                    {{ service.icon || '—' }}
                                </p>
                            </div>

                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Éléments inclus</h4>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ service.items.length }} élément{{ service.items.length !== 1 ? 's' : '' }}
                                </p>
                            </div>

                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Dernière modification</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ formatDateTime(service.updated_at) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Statistiques des rendez-vous -->
                    <div class="bg-white border rounded-lg shadow-sm overflow-hidden">
                        <div class="px-4 py-3 bg-gray-50 border-b">
                            <h3 class="text-lg font-medium text-gray-900">Statistiques RDV</h3>
                        </div>
                        <div class="p-4 space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Total</span>
                                <span class="text-lg font-bold text-gray-900">{{ appointmentStats.total }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-yellow-600">En attente</span>
                                <span class="text-lg font-bold text-yellow-600">{{ appointmentStats.pending }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-green-600">Confirmés</span>
                                <span class="text-lg font-bold text-green-600">{{ appointmentStats.confirmed }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-blue-600">Terminés</span>
                                <span class="text-lg font-bold text-blue-600">{{ appointmentStats.completed }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-red-600">Annulés</span>
                                <span class="text-lg font-bold text-red-600">{{ appointmentStats.cancelled }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section Rendez-vous associés -->
            <div class="bg-white border rounded-lg shadow-sm overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <h3 class="text-lg font-medium text-gray-900">
                        Rendez-vous associés ({{ appointmentStats.total }})
                    </h3>
                    
                    <!-- Filtres -->
                    <div class="flex bg-gray-100 rounded-md p-1">
                        <button
                            @click="appointmentFilter = 'all'"
                            :class="[
                                'px-3 py-1.5 rounded-md flex items-center gap-2 transition text-sm',
                                appointmentFilter === 'all'
                                    ? 'bg-white shadow-sm text-blue-600'
                                    : 'text-gray-600 hover:bg-gray-200'
                            ]"
                        >
                            Tous ({{ appointmentStats.total }})
                        </button>
                        <button
                            @click="appointmentFilter = 'pending'"
                            :class="[
                                'px-3 py-1.5 rounded-md flex items-center gap-2 transition text-sm',
                                appointmentFilter === 'pending'
                                    ? 'bg-white shadow-sm text-blue-600'
                                    : 'text-gray-600 hover:bg-gray-200'
                            ]"
                        >
                            En attente ({{ appointmentStats.pending }})
                        </button>
                        <button
                            @click="appointmentFilter = 'confirmed'"
                            :class="[
                                'px-3 py-1.5 rounded-md flex items-center gap-2 transition text-sm',
                                appointmentFilter === 'confirmed'
                                    ? 'bg-white shadow-sm text-blue-600'
                                    : 'text-gray-600 hover:bg-gray-200'
                            ]"
                        >
                            Confirmés ({{ appointmentStats.confirmed }})
                        </button>
                    </div>
                </div>

                <div class="p-4">
                    <div v-if="filteredAppointments.length === 0" class="text-center py-8 text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p>Aucun rendez-vous trouvé</p>
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sujet</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Heure</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date création</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="appointment in filteredAppointments" :key="appointment.id" class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ appointment.client.first_name }} {{ appointment.client.last_name }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ appointment.client.email }}</div>
                                        <div v-if="appointment.client.phone" class="text-sm text-gray-500">{{ appointment.client.phone }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm text-gray-900">{{ appointment.subject }}</div>
                                        <div v-if="appointment.description" class="text-sm text-gray-500 max-w-xs truncate">
                                            {{ appointment.description }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ formatDate(appointment.schedule.date) }}</div>
                                        <div class="text-sm text-gray-500">
                                            {{ formatTime(appointment.schedule.start_time) }} - {{ formatTime(appointment.schedule.end_time) }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span :class="['px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full', getAppointmentStatusClass(appointment.status).class]">
                                            {{ getAppointmentStatusClass(appointment.status).text }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ formatDate(appointment.created_at) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.prose :deep(img) {
    max-width: 100%;
    height: auto;
    border-radius: 0.5rem;
}
</style>