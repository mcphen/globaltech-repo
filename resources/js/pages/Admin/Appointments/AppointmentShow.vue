<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

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

interface Appel {
    id: number;
    user_id: number;
    user: User;
    called_at: string;
    type: 'entrant' | 'sortant';
    status: 'répondu' | 'non-répondu' | 'rappel-prévu';
    duration: number | null;
    notes: string;
    next_call_at: string | null;
    created_at: string;
    updated_at: string;
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
    appels: Appel[];
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

// États
const showConfirmModal = ref(false);
const showDeleteModal = ref(false);
const showAppelModal = ref(false);
const showAppelsList = ref(false);
const updating = ref(false);
const loadingAppels = ref(false);
const error = ref<string | null>(null);
const success = ref<string | null>(null);
const appels = ref<Appel[]>(props.appointment.appels || []);


// Formulaire d'appel
const appelForm = ref({
    type: 'sortant',
    status: 'répondu',
    duration: null as number | null,
    notes: '',
    next_call_at: '',
});

// Fonctions utilitaires
function formatDate(dateString: string): string {
    if (!dateString) return '';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    }).format(date);
}

function formatTime(timeString: string): string {
    return timeString.substring(0, 5);
}

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

function formatDuration(seconds: number | null) {
    if (!seconds) return 'N/A';
    const minutes = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${minutes}:${secs.toString().padStart(2, '0')}`;
}

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

function getAppelTypeLabel(type: string) {
    return type === 'sortant' ? 'Sortant' : 'Entrant';
}

function getAppelStatusLabel(status: string) {
    switch (status) {
        case 'répondu': return 'Répondu';
        case 'non-répondu': return 'Non répondu';
        case 'rappel-prévu': return 'Rappel prévu';
        default: return status;
    }
}

function getAppelTypeClasses(type: string) {
    return type === 'sortant' 
        ? 'bg-blue-100 text-blue-800' 
        : 'bg-purple-100 text-purple-800';
}

function getAppelStatusClasses(status: string) {
    switch (status) {
        case 'répondu': return 'bg-green-100 text-green-800';
        case 'non-répondu': return 'bg-red-100 text-red-800';
        case 'rappel-prévu': return 'bg-yellow-100 text-yellow-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

// Fonctions principales
async function confirmAppointment() {
    try {
        await router.post(route('admin.appointments.confirm', props.appointment.id));
        showConfirmModal.value = false;
        router.reload();
    } catch (error) {
        console.error('Erreur lors de la confirmation:', error);
    }
}

function deleteAppointment() {
    window.location.href = route('admin.appointments.destroy', props.appointment.id);
}

async function updateAppointmentStatus(status: string) {
    try {
        await router.put(route('admin.appointments.update', props.appointment.id), { status });
        router.reload();
    } catch (error) {
        console.error('Erreur lors de la mise à jour du statut:', error);
    }
}

async function submitAppel() {
    if (!appelForm.value.notes.trim()) {
        error.value = 'Les notes sont obligatoires';
        return;
    }

    try {
        const { data } = await axios.post(route('admin.appointments.appels.store', props.appointment.id), {
            ...appelForm.value,
            duration: appelForm.value.duration || null,
            next_call_at: appelForm.value.next_call_at || null,
        });

        // Ajouter le nouvel appel en tête de liste
        appels.value.unshift(data.appel);
        
        // Réinitialiser le formulaire
        appelForm.value = {
            type: 'sortant',
            status: 'répondu',
            duration: null,
            notes: '',
            next_call_at: '',
        };
        
        showAppelModal.value = false;
        success.value = 'Appel enregistré avec succès';
        setTimeout(() => { success.value = null; }, 3000);
    } catch (e: any) {
        error.value = e?.response?.data?.message || e?.message || 'Erreur lors de l\'enregistrement de l\'appel';
    }
}

async function deleteAppel(appelId: number) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cet appel ?')) return;

    try {
        await axios.delete(route('admin.appointments.appels.destroy', {
            appointment: props.appointment.id,
            appel: appelId
        }));
        
        // Supprimer de la liste
        appels.value = appels.value.filter(a => a.id !== appelId);
        success.value = 'Appel supprimé avec succès';
        setTimeout(() => { success.value = null; }, 3000);
    } catch (e: any) {
        error.value = e?.response?.data?.message || e?.message || 'Erreur lors de la suppression de l\'appel';
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

                <div class="flex flex-wrap gap-2">
                    <Link
                        :href="route('admin.appointments.index')"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Retour à la liste
                    </Link>
                    
                    <!-- Bouton Nouvel appel -->
                    <button
                        @click="showAppelModal = true"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                        </svg>
                        Nouvel appel
                    </button>
                    
                    <!-- Bouton Historique appels -->
                    <button
                        @click="showAppelsList = !showAppelsList"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                        </svg>
                        Historique appels ({{ appels.length }})
                    </button>
                    
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

            <!-- Messages d'erreur/succès -->
            <div v-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">
                {{ error }}
            </div>
            <div v-if="success" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md">
                {{ success }}
            </div>

            <!-- Message flash -->
            <!-- <div v-if="page.props.flash && page.props.flash.success" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md mb-4">
                {{ page.props.flash.success }}
            </div> -->

            <!-- Historique des appels -->
            <div v-if="showAppelsList" class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Historique des appels</h3>
                    <button
                        @click="showAppelsList = false"
                        class="text-gray-500 hover:text-gray-700"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
                
                <div v-if="appels.length === 0" class="text-center py-4 text-gray-500">
                    Aucun appel enregistré pour ce rendez-vous.
                </div>
                
                <div v-else class="space-y-3">
                    <div v-for="appel in appels" :key="appel.id" class="bg-white border border-gray-200 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center gap-2">
                                <span :class="['px-2 py-1 text-xs font-medium rounded-full', getAppelTypeClasses(appel.type)]">
                                    {{ getAppelTypeLabel(appel.type) }}
                                </span>
                                <span :class="['px-2 py-1 text-xs font-medium rounded-full', getAppelStatusClasses(appel.status)]">
                                    {{ getAppelStatusLabel(appel.status) }}
                                </span>
                                <span class="text-sm text-gray-500">
                                    {{ formatDateTime(appel.called_at) }}
                                </span>
                            </div>
                            <button
                                @click="deleteAppel(appel.id)"
                                class="text-red-500 hover:text-red-700"
                                title="Supprimer l'appel"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                        
                        <div class="text-sm text-gray-600 mb-2">
                            <div class="flex items-center gap-4">
                                <span>Par: {{ appel.user.name }}</span>
                                <span v-if="appel.duration">Durée: {{ formatDuration(appel.duration) }}</span>
                                <span v-if="appel.next_call_at" class="text-yellow-600">
                                    Rappel: {{ formatDate(appel.next_call_at) }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="text-sm text-gray-700 whitespace-pre-wrap bg-gray-50 p-3 rounded">
                            {{ appel.notes }}
                        </div>
                    </div>
                    
                </div>
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
                                    @click="showAppelModal = true"
                                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                    </svg>
                                    Nouvel appel
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

    <!-- Modal pour nouveau appel -->
    <div v-if="showAppelModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-semibold text-gray-900">Nouvel appel pour le rendez-vous</h3>
                    <button
                        @click="showAppelModal = false"
                        class="text-gray-400 hover:text-gray-500"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
                
                <div class="space-y-4">
                    <!-- Type d'appel -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type d'appel</label>
                        <div class="flex gap-4">
                            <label class="inline-flex items-center">
                                <input
                                    type="radio"
                                    v-model="appelForm.type"
                                    value="sortant"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                >
                                <span class="ml-2 text-sm text-gray-700">Sortant (nous appelons)</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input
                                    type="radio"
                                    v-model="appelForm.type"
                                    value="entrant"
                                    class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300"
                                >
                                <span class="ml-2 text-sm text-gray-700">Entrant (client appelle)</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Statut -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                        <select
                            v-model="appelForm.status"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="répondu">Répondu</option>
                            <option value="non-répondu">Non répondu</option>
                            <option value="rappel-prévu">Rappel prévu</option>
                        </select>
                    </div>
                    
                    <!-- Durée -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Durée (en secondes)
                            <span class="text-gray-400 text-sm font-normal"> - Optionnel</span>
                        </label>
                        <input
                            type="number"
                            v-model="appelForm.duration"
                            min="0"
                            placeholder="Ex: 300 pour 5 minutes"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        >
                    </div>
                    
                    <!-- Date de rappel -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Date de rappel
                            <span class="text-gray-400 text-sm font-normal"> - Optionnel</span>
                        </label>
                        <input
                            type="datetime-local"
                            v-model="appelForm.next_call_at"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        >
                    </div>
                    
                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Notes <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            v-model="appelForm.notes"
                            rows="6"
                            required
                            placeholder="Résumé de l'appel, points discutés, actions à prendre..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        ></textarea>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex justify-end gap-3 pt-4">
                        <button
                            @click="showAppelModal = false"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition"
                        >
                            Annuler
                        </button>
                        <button
                            @click="submitAppel"
                            :disabled="!appelForm.notes.trim()"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md transition disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Enregistrer l'appel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>