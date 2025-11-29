<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';
import { ref } from 'vue';
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
    address: string;
}

interface Schedule {
    id: number;
    date: string;
    start_time: string;
    end_time: string;
    description: string | null;
}

interface User {
    id: number;
    name: string;
    email: string;
}

interface Appointment {
    id: number;
    client_id: number;
    schedule_id: number;
    subject: string;
    description: string | null;
    status: 'pending' | 'confirmed' | 'completed' | 'cancelled';
    confirmed_at: string | null;
    confirmed_by: number | null;
    created_at: string;
    updated_at: string;
    client: Client;
    schedule: Schedule;
    services: AppointmentService[];
    confirmedBy?: User;
}

// Props
interface Props {
    appointment: Appointment;
}

const props = defineProps<Props>();
const page = usePage();

// Définition des fil d'Ariane
const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Rendez-vous', href: route('admin.appointments.index') },
    { title: `Rendez-vous #${props.appointment.id}`, href: route('admin.appointments.show', props.appointment.id) }
];

// État pour les modals
const showConfirmModal = ref(false);
const showDeleteModal = ref(false);

// Fonctions
// Confirmer un rendez-vous
async function confirmAppointment() {
    try {
        await router.post(route('admin.appointments.confirm', props.appointment.id));
        showConfirmModal.value = false;
        // Recharger les données
        router.reload();
    } catch (error) {
        console.error('Erreur lors de la confirmation:', error);
    }
}

function deleteAppointment() {
    window.location.href = route('admin.appointments.destroy', props.appointment.id);
}

// Mettre à jour le statut
async function updateAppointmentStatus(status: string) {
    try {
        await router.put(route('admin.appointments.update', props.appointment.id), { status });
        // Optionnel: ajouter un toast de succès
        // $toast.success('Statut du rendez-vous mis à jour avec succès');
        
        // Recharger les données
        router.reload();
    } catch (error) {
        console.error('Erreur lors de la mise à jour du statut:', error);
    }
}
// Formatage de la date
function formatDate(dateString: string): string {
    if (!dateString) return '';
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

// Formatage de la date et heure
function formatDateTime(dateString: string): string {
    if (!dateString) return '';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(date);
}

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
</script>

<template>
    <Head :title="`Rendez-vous #${appointment.id}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm">
            <!-- En-tête avec titre et actions -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 border-b pb-6">
                <div>
                    <div class="flex items-center gap-3">
                        <h2 class="text-2xl font-bold text-gray-800">Rendez-vous #{{ appointment.id }}</h2>
                        <div class="flex items-center gap-2">
                            <span :class="['px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full', getStatusClass(appointment.status).class]">
                                {{ getStatusClass(appointment.status).text }}
                            </span>
                           <select
    :value="appointment.status"
    @change="updateAppointmentStatus(($event.target as HTMLSelectElement).value)"
    class="text-sm border border-gray-300 rounded-md px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500"
>
    <option value="pending">En attente</option>
    <option value="confirmed">Confirmé</option>
    <option value="completed">Terminé</option>
    <option value="cancelled">Annulé</option>
</select>
                        </div>
                    </div>
                    <p class="text-gray-500 mt-1">
                        Créé le {{ formatDateTime(appointment.created_at) }}
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <Link
                        :href="route('admin.appointments.index')"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Retour à la liste
                    </Link>
                    <button
                        v-if="appointment.status === 'pending'"
                        @click="showConfirmModal = true"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                    >
                        Confirmer
                    </button>
                    <button
                        @click="showDeleteModal = true"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                    >
                        Supprimer
                    </button>
                </div>
            </div>

            <!-- Message flash -->
            <div v-if="page.props.flash && page.props.flash.success" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md mb-4">
                {{ page.props.flash.success }}
            </div>

            <!-- Informations principales -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Colonne de gauche -->
                <div class="space-y-6">
                    <!-- Informations du client -->
                    <div class="bg-white border rounded-lg shadow-sm overflow-hidden">
                        <div class="px-4 py-3 bg-gray-50 border-b">
                            <h3 class="text-lg font-medium text-gray-900">Informations du client</h3>
                        </div>
                        <div class="p-4 space-y-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Nom complet</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ appointment.client.first_name }} {{ appointment.client.last_name }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Email</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ appointment.client.email }}</p>
                            </div>
                            <div v-if="appointment.client.phone">
                                <h4 class="text-sm font-medium text-gray-500">Téléphone</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ appointment.client.phone }}</p>
                            </div>
                            <div v-if="appointment.client.address">
                                <h4 class="text-sm font-medium text-gray-500">Adresse</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ appointment.client.address }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Détails du rendez-vous -->
                    <div class="bg-white border rounded-lg shadow-sm overflow-hidden">
                        <div class="px-4 py-3 bg-gray-50 border-b">
                            <h3 class="text-lg font-medium text-gray-900">Détails du rendez-vous</h3>
                        </div>
                        <div class="p-4 space-y-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Sujet</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ appointment.subject }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Date</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ formatDate(appointment.schedule.date) }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Heure</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ formatTime(appointment.schedule.start_time) }} - {{ formatTime(appointment.schedule.end_time) }}</p>
                            </div>
                            <div v-if="appointment.schedule.description">
                                <h4 class="text-sm font-medium text-gray-500">Description du créneau</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ appointment.schedule.description }}</p>
                            </div>
                            <div v-if="appointment.description">
                                <h4 class="text-sm font-medium text-gray-500">Notes supplémentaires</h4>
                                <div class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-md">
                                    <div v-html="appointment.description"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Colonne de droite -->
                <div class="space-y-6">
                    <!-- Statut et confirmation -->
                    <div class="bg-white border rounded-lg shadow-sm overflow-hidden">
                        <div class="px-4 py-3 bg-gray-50 border-b">
                            <h3 class="text-lg font-medium text-gray-900">Statut et confirmation</h3>
                        </div>
                        <div class="p-4 space-y-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Statut actuel</h4>
                                <p class="mt-1">
                                    <span :class="['px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full', getStatusClass(appointment.status).class]">
                                        {{ getStatusClass(appointment.status).text }}
                                    </span>
                                </p>
                            </div>
                            <div v-if="appointment.status === 'confirmed'">
                                <h4 class="text-sm font-medium text-gray-500">Date de confirmation</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ formatDateTime(appointment.confirmed_at!) }}</p>
                            </div>
                            <div v-if="appointment.status === 'confirmed' && appointment.confirmedBy">
                                <h4 class="text-sm font-medium text-gray-500">Confirmé par</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ appointment.confirmedBy.name }}</p>
                                <p class="text-sm text-gray-500">{{ appointment.confirmedBy.email }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Dernière mise à jour</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ formatDateTime(appointment.updated_at) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Services demandés -->
                    <div class="bg-white border rounded-lg shadow-sm overflow-hidden">
                        <div class="px-4 py-3 bg-gray-50 border-b">
                            <h3 class="text-lg font-medium text-gray-900">Services demandés</h3>
                        </div>
                        <div class="p-4">
                            <div v-if="appointment.services.length > 0" class="space-y-3">
                                <div v-for="service in appointment.services" :key="service.id" class="border rounded-md p-3 bg-gray-50">
                                    <h4 class="font-medium text-gray-900">{{ service.title }}</h4>
                                    <p v-if="service.description" class="text-sm text-gray-600 mt-1">{{ service.description }}</p>
                                    <p v-if="service.price" class="text-sm font-medium text-blue-600 mt-2">
                                        {{ new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(service.price) }}
                                    </p>
                                </div>
                            </div>
                            <div v-else class="text-center py-6">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                </svg>
                                <p class="text-gray-500 mt-2">Aucun service sélectionné</p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions rapides -->
                    <div class="bg-white border rounded-lg shadow-sm overflow-hidden">
                        <div class="px-4 py-3 bg-gray-50 border-b">
                            <h3 class="text-lg font-medium text-gray-900">Actions rapides</h3>
                        </div>
                        <div class="p-4">
                            <div class="grid grid-cols-1 gap-2">
                                <button
                                    v-if="appointment.status === 'pending'"
                                    @click="showConfirmModal = true"
                                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    Confirmer le rendez-vous
                                </button>
                                <button
                                    @click="showDeleteModal = true"
                                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    Supprimer le rendez-vous
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal de confirmation -->
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

            <!-- Modal de suppression -->
            <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                    <h3 class="text-lg font-medium text-gray-900">Confirmer la suppression</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Êtes-vous sûr de vouloir supprimer ce rendez-vous ? Cette action est irréversible.
                    </p>
                    <div class="mt-4 flex justify-end gap-3">
                        <button
                            @click="showDeleteModal = false"
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
        </div>
    </AppLayout>
</template>