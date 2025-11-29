<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';
import { ref, computed, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

// Type pour un rendez-vous
interface AppointmentService {
    id: number;
    title: string;
    description: string;
    price: number;
    pivot: {
        appointment_id: number;
        service_id: number;
    };
}

interface Client {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    phone: string;
}

interface Schedule {
    id: number;
    date: string;
    start_time: string;
    end_time: string;
}

interface Appointment {
    id: number;
    client_id: number;
    schedule_id: number;
    subject: string;
    description: string | null;
    status: 'pending' | 'confirmed' | 'cancelled' | 'completed';
    confirmed_at: string | null;
    confirmed_by: number | null;
    created_at: string;
    updated_at: string;
    client: Client;
    schedule: Schedule;
    services: AppointmentService[];
}

// Type pour pagination
interface Pagination {
    current_page: number;
    data: Appointment[];
    from: number;
    last_page: number;
    links: Array<{ url: string | null; label: string; active: boolean }>;
    per_page: number;
    to: number;
    total: number;
}

// Props
interface Props {
    appointments: Pagination;
    flash?: { success?: string };
}

const props = defineProps<Props>();
const page = usePage();

// Accéder aux rendez-vous
const appointmentsList = computed(() => props.appointments.data);

// Définition des fil d'Ariane
const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Rendez-vous', href: route('admin.appointments.index') }
];

// État de confirmation de suppression
const appointmentToDelete = ref<Appointment | null>(null);
const showDeleteModal = ref(false);

// État pour la modal de confirmation
const showConfirmModal = ref(false);
const currentAppointment = ref<Appointment | null>(null);

// Mode d'affichage (grille ou liste)
const viewMode = ref<'grid' | 'list'>('list');

// Filtres
const statusFilter = ref<'all' | 'pending' | 'confirmed' | 'completed' | 'cancelled'>('all');

// Statistiques
const stats = ref({
    totalAppointments: 0,
    pendingAppointments: 0,
    confirmedAppointments: 0,
    completedAppointments: 0,
    cancelledAppointments: 0,
    upcomingAppointments: 0,
    todayAppointments: 0
});

// Fonctions
function confirmDelete(appointment: Appointment) {
    appointmentToDelete.value = appointment;
    showDeleteModal.value = true;
}

function cancelDelete() {
    appointmentToDelete.value = null;
    showDeleteModal.value = false;
}

function deleteAppointment() {
    if (appointmentToDelete.value) {
        window.location.href = route('admin.appointments.destroy', appointmentToDelete.value.id);
    }
    showDeleteModal.value = false;
}

// Ouvrir la modal de confirmation
function openConfirmModal(appointment: Appointment) {
    currentAppointment.value = appointment;
    showConfirmModal.value = true;
}

// Confirmer un rendez-vous
// Confirmer un rendez-vous
async function confirmAppointment() {
    if (currentAppointment.value) {
        try {
            await router.post(route('admin.appointments.confirm', currentAppointment.value.id));
            showConfirmModal.value = false;
            // Recharger les données
            router.reload({ only: ['appointments'] });
        } catch (error) {
            console.error('Erreur lors de la confirmation:', error);
        }
    }
}
// Mettre à jour le statut d'un rendez-vous
async function updateAppointmentStatus(appointmentId: number, status: string) {
    try {
        await router.put(route('admin.appointments.update', appointmentId), { status });
        // Optionnel: ajouter un toast de succès
        // $toast.success('Statut du rendez-vous mis à jour avec succès');
        
        // Recharger les données
        router.reload({ only: ['appointments'] });
    } catch (error) {
        console.error('Erreur lors de la mise à jour du statut:', error);
    }
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

// Formatage de l'heure
function formatTime(timeString: string): string {
    return timeString.substring(0, 5);
}

// Vérifier si une date est aujourd'hui
function isToday(dateString: string): boolean {
    const today = new Date();
    const date = new Date(dateString);
    return date.toDateString() === today.toDateString();
}

// Vérifier si une date est future
function isUpcoming(dateString: string): boolean {
    const today = new Date();
    const date = new Date(dateString);
    return date >= today;
}

// Filtrer les rendez-vous par statut
const filteredAppointments = computed(() => {
    if (statusFilter.value === 'all') {
        return appointmentsList.value;
    }
    return appointmentsList.value.filter(appointment => appointment.status === statusFilter.value);
});

// Obtenir la classe de couleur en fonction du statut
function getStatusClass(status: string): { class: string, text: string } {
    switch (status) {
        case 'pending':
            return { class: 'bg-yellow-100 text-yellow-800', text: 'En attente' };
        case 'confirmed':
            return { class: 'bg-blue-100 text-blue-800', text: 'Confirmé' };
        case 'completed':
            return { class: 'bg-green-100 text-green-800', text: 'Terminé' };
        case 'cancelled':
            return { class: 'bg-red-100 text-red-800', text: 'Annulé' };
        default:
            return { class: 'bg-gray-100 text-gray-800', text: status };
    }
}

// Calculer les statistiques
function calculateStats() {
    const totalAppointments = props.appointments.total;
    const pendingAppointments = appointmentsList.value.filter(a => a.status === 'pending').length;
    const confirmedAppointments = appointmentsList.value.filter(a => a.status === 'confirmed').length;
    const completedAppointments = appointmentsList.value.filter(a => a.status === 'completed').length;
    const cancelledAppointments = appointmentsList.value.filter(a => a.status === 'cancelled').length;
    
    const todayAppointments = appointmentsList.value.filter(a => isToday(a.schedule.date)).length;
    const upcomingAppointments = appointmentsList.value.filter(a => isUpcoming(a.schedule.date) && a.status !== 'cancelled').length;

    stats.value = {
        totalAppointments,
        pendingAppointments,
        confirmedAppointments,
        completedAppointments,
        cancelledAppointments,
        todayAppointments,
        upcomingAppointments
    };
}

onMounted(() => {
    calculateStats();
});
</script>

<template>
    <Head title="Gestion des rendez-vous" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm">
            <!-- En-tête avec titre et filtres -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 border-b pb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Gestion des rendez-vous</h2>
                    <p class="text-gray-500 mt-1">
                        Consultez et gérez les demandes de rendez-vous
                    </p>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Filtres de statut -->
                    <div class="flex bg-gray-100 rounded-md p-1">
                        <button
                            @click="statusFilter = 'all'"
                            :class="[
                                'px-3 py-1.5 rounded-md flex items-center gap-2 transition text-sm',
                                statusFilter === 'all'
                                    ? 'bg-white shadow-sm text-blue-600'
                                    : 'text-gray-600 hover:bg-gray-200'
                            ]"
                        >
                            Tous
                        </button>
                        <button
                            @click="statusFilter = 'pending'"
                            :class="[
                                'px-3 py-1.5 rounded-md flex items-center gap-2 transition text-sm',
                                statusFilter === 'pending'
                                    ? 'bg-white shadow-sm text-blue-600'
                                    : 'text-gray-600 hover:bg-gray-200'
                            ]"
                        >
                            En attente
                        </button>
                        <button
                            @click="statusFilter = 'confirmed'"
                            :class="[
                                'px-3 py-1.5 rounded-md flex items-center gap-2 transition text-sm',
                                statusFilter === 'confirmed'
                                    ? 'bg-white shadow-sm text-blue-600'
                                    : 'text-gray-600 hover:bg-gray-200'
                            ]"
                        >
                            Confirmés
                        </button>
                        <button
                            @click="statusFilter = 'completed'"
                            :class="[
                                'px-3 py-1.5 rounded-md flex items-center gap-2 transition text-sm',
                                statusFilter === 'completed'
                                    ? 'bg-white shadow-sm text-blue-600'
                                    : 'text-gray-600 hover:bg-gray-200'
                            ]"
                        >
                            Terminés
                        </button>
                        <button
                            @click="statusFilter = 'cancelled'"
                            :class="[
                                'px-3 py-1.5 rounded-md flex items-center gap-2 transition text-sm',
                                statusFilter === 'cancelled'
                                    ? 'bg-white shadow-sm text-blue-600'
                                    : 'text-gray-600 hover:bg-gray-200'
                            ]"
                        >
                            Annulés
                        </button>
                    </div>

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
                </div>
            </div>

            <!-- Statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-blue-600">Total rendez-vous</p>
                            <p class="text-2xl font-bold text-blue-900">{{ stats.totalAppointments }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-green-600">À venir</p>
                            <p class="text-2xl font-bold text-green-900">{{ stats.upcomingAppointments }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-orange-100 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-orange-600">Aujourd'hui</p>
                            <p class="text-2xl font-bold text-orange-900">{{ stats.todayAppointments }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-purple-600">En attente</p>
                            <p class="text-2xl font-bold text-purple-900">{{ stats.pendingAppointments }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Message flash -->
            <div v-if="page.props.flash && page.props.flash.success" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md mb-4">
                {{ page.props.flash.success }}
            </div>

            <!-- Vue en liste -->
            <div v-if="viewMode === 'list'" class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Client
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date & Heure
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Sujet
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Services
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Statut
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="appointment in filteredAppointments" :key="appointment.id" class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ appointment.client.first_name }} {{ appointment.client.last_name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ appointment.client.email }}
                                        </div>
                                        <div v-if="appointment.client.phone" class="text-sm text-gray-500">
                                            {{ appointment.client.phone }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ formatDate(appointment.schedule.date) }}</div>
                                <div class="text-sm text-gray-500">
                                    {{ formatTime(appointment.schedule.start_time) }} - {{ formatTime(appointment.schedule.end_time) }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ appointment.subject }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    <span
                                        v-for="service in appointment.services"
                                        :key="service.id"
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                                    >
                                        {{ service.title }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <span :class="['px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full', getStatusClass(appointment.status).class]">
                                        {{ getStatusClass(appointment.status).text }}
                                    </span>
                                   <select
    :value="appointment.status"
    @change="updateAppointmentStatus(appointment.id, ($event.target as HTMLSelectElement).value)"
    class="text-xs border border-gray-300 rounded-md px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500"
>
    <option value="pending">En attente</option>
    <option value="confirmed">Confirmé</option>
    <option value="completed">Terminé</option>
    <option value="cancelled">Annulé</option>
</select>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                <div class="flex justify-center gap-2">
                                    <Link
                                        :href="route('admin.appointments.show', appointment.id)"
                                        class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 p-2 rounded transition"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                        </svg>
                                    </Link>
                                    <button
                                        v-if="appointment.status === 'pending'"
                                        @click="openConfirmModal(appointment)"
                                        class="text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 p-2 rounded transition"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <button
                                        @click="confirmDelete(appointment)"
                                        class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-2 rounded transition"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Vue en grille -->
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="appointment in filteredAppointments" :key="appointment.id" class="bg-white border rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                    <div class="p-4 border-b">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ appointment.subject }}</h3>
                                <p class="text-sm text-gray-600">{{ formatDate(appointment.schedule.date) }} | {{ formatTime(appointment.schedule.start_time) }} - {{ formatTime(appointment.schedule.end_time) }}</p>
                            </div>
                            <span :class="['px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full', getStatusClass(appointment.status).class]">
                                {{ getStatusClass(appointment.status).text }}
                            </span>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="mb-3">
                            <h4 class="text-sm font-medium text-gray-500">Client</h4>
                            <p class="text-sm">{{ appointment.client.first_name }} {{ appointment.client.last_name }}</p>
                            <p class="text-sm text-gray-600">{{ appointment.client.email }}</p>
                            <p v-if="appointment.client.phone" class="text-sm text-gray-600">{{ appointment.client.phone }}</p>
                        </div>
                        <div class="mb-3">
                            <h4 class="text-sm font-medium text-gray-500">Services demandés</h4>
                            <div class="flex flex-wrap gap-1 mt-1">
                                <span
                                    v-for="service in appointment.services"
                                    :key="service.id"
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                                >
                                    {{ service.title }}
                                </span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <h4 class="text-sm font-medium text-gray-500">Statut</h4>
                            <select
                                :value="appointment.status"
                                @change="updateAppointmentStatus(appointment.id, ($event.target as HTMLSelectElement).value)"
                                class="text-sm border border-gray-300 rounded-md px-2 py-1 w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                <option value="pending">En attente</option>
                                <option value="confirmed">Confirmé</option>
                                <option value="completed">Terminé</option>
                                <option value="cancelled">Annulé</option>
                            </select>
                        </div>
                    </div>
                    <div class="px-4 py-3 bg-gray-50 border-t flex justify-end gap-2">
                        <Link
                            :href="route('admin.appointments.show', appointment.id)"
                            class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Détails
                        </Link>
                        <button
                            v-if="appointment.status === 'pending'"
                            @click="openConfirmModal(appointment)"
                            class="inline-flex items-center px-3 py-1.5 border border-transparent shadow-sm text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                        >
                            Confirmer
                        </button>
                        <button
                            @click="confirmDelete(appointment)"
                            class="inline-flex items-center px-3 py-1.5 border border-transparent shadow-sm text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                        >
                            Supprimer
                        </button>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="props.appointments.links && props.appointments.links.length > 3" class="mt-4 flex justify-center">
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    <Link
                        v-for="(link, i) in props.appointments.links"
                        :key="i"
                        :href="link.url"
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"
                        :class="{
                            'z-10 bg-indigo-50 border-indigo-500 text-indigo-600': link.active,
                            'bg-gray-100 text-gray-500 cursor-not-allowed': !link.url,
                        }"
                    >
                        <span v-html="link.label"></span>
                    </Link>
                </nav>
            </div>

            <!-- Message si aucun rendez-vous -->
            <div v-if="filteredAppointments.length === 0" class="text-center py-12">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun rendez-vous</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Aucun rendez-vous ne correspond à vos critères de recherche.
                </p>
            </div>

            <!-- Modal de confirmation de suppression -->
            <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                    <h3 class="text-lg font-medium text-gray-900">Confirmer la suppression</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Êtes-vous sûr de vouloir supprimer ce rendez-vous ? Cette action est irréversible.
                    </p>
                    <div class="mt-4 flex justify-end gap-3">
                        <button
                            @click="cancelDelete"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Annuler
                        </button>
                        <button
                            @click="deleteAppointment"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                        >
                            Supprimer
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal de confirmation de rendez-vous -->
            <div v-if="showConfirmModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                    <h3 class="text-lg font-medium text-gray-900">Confirmer le rendez-vous</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Êtes-vous sûr de vouloir confirmer ce rendez-vous ? Un email de confirmation sera envoyé au client.
                    </p>
                    <div class="mt-4 flex justify-end gap-3">
                        <button
                            @click="showConfirmModal = false"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Annuler
                        </button>
                        <button
                            @click="confirmAppointment"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                        >
                            Confirmer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>