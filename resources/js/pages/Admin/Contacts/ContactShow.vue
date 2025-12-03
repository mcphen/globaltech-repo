<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';
import { ref } from 'vue';
import axios from 'axios';

// Types
interface Client {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    phone: string | null;
    address: string | null;
    created_at: string;
    updated_at: string;
}

interface Contact {
    id: number;
    client_id: number;
    subject: string;
    description: string;
    status: string;
    notes: string | null;
    created_at: string;
    updated_at: string;
    client: Client;
}

// Props
interface Props {
    contact: Contact;
    flash?: { success?: string };
}

const props = defineProps<Props>();

// Définition des fil d'Ariane
const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Contacts', href: route('admin.contacts.index') },
    { title: `Message #${props.contact.id}`, href: route('admin.contacts.show', props.contact.id) }
];

// États
const showDeleteModal = ref(false);
const showNotesModal = ref(false);
const notesInput = ref(props.contact.notes || '');
const isUpdating = ref(false);

// Fonctions
function confirmDelete() {
    showDeleteModal.value = true;
}

function cancelDelete() {
    showDeleteModal.value = false;
}

function deleteContact() {
    window.location.href = route('admin.contacts.destroy', props.contact.id);
}

// Fonction pour mettre à jour le statut
async function updateStatus(status: string) {
    try {
        isUpdating.value = true;
        await axios.patch(route('admin.contacts.update-status', props.contact.id), {
            status: status,
            notes: status === 'processed' ? 'Traité via l\'interface' : props.contact.notes
        });
        
        window.location.reload();
    } catch (error) {
        console.error('Erreur lors de la mise à jour du statut:', error);
        alert('Erreur lors de la mise à jour du statut');
    } finally {
        isUpdating.value = false;
    }
}

// Fonction pour sauvegarder les notes
async function saveNotes() {
    try {
        isUpdating.value = true;
        await axios.patch(route('admin.contacts.update-status', props.contact.id), {
            status: props.contact.status,
            notes: notesInput.value
        });
        
        showNotesModal.value = false;
        window.location.reload();
    } catch (error) {
        console.error('Erreur lors de la sauvegarde des notes:', error);
        alert('Erreur lors de la sauvegarde des notes');
    } finally {
        isUpdating.value = false;
    }
}

// Fonction pour ouvrir le modal des notes
function openNotesModal() {
    notesInput.value = props.contact.notes || '';
    showNotesModal.value = true;
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

// Calculer le temps écoulé
function getTimeAgo(dateString: string): string {
    const now = new Date();
    const date = new Date(dateString);
    const diffInMs = now.getTime() - date.getTime();
    const diffInHours = Math.floor(diffInMs / (1000 * 60 * 60));
    const diffInDays = Math.floor(diffInMs / (1000 * 60 * 60 * 24));

    if (diffInHours < 1) {
        return 'Il y a quelques minutes';
    } else if (diffInHours < 24) {
        return `Il y a ${diffInHours} heure${diffInHours > 1 ? 's' : ''}`;
    } else {
        return `Il y a ${diffInDays} jour${diffInDays > 1 ? 's' : ''}`;
    }
}

// Obtenir les classes pour le badge de statut
function getStatusClasses(status: string) {
    switch(status) {
        case 'pending':
            return { bg: 'bg-yellow-100', text: 'text-yellow-800', label: 'En attente' };
        case 'processed':
            return { bg: 'bg-green-100', text: 'text-green-800', label: 'Traité' };
        case 'archived':
            return { bg: 'bg-gray-100', text: 'text-gray-800', label: 'Archivé' };
        default:
            return { bg: 'bg-gray-100', text: 'text-gray-800', label: 'Inconnu' };
    }
}

// Obtenir l'icône pour le statut
function getStatusIcon(status: string) {
    switch(status) {
        case 'pending':
            return `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />`;
        case 'processed':
            return `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />`;
        case 'archived':
            return `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />`;
        default:
            return `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />`;
    }
}
</script>

<template>
    <Head :title="`Message #${contact.id}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm">
            <!-- Message flash -->
            <div v-if="props.flash?.success" class="bg-green-50 border-l-4 border-green-500 p-4 rounded-md flex items-start">
                <svg class="h-6 w-6 text-green-500 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-green-700">{{ props.flash.success }}</p>
                </div>
            </div>

            <!-- En-tête avec titre et actions -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 border-b pb-6">
                <div>
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 h-12 w-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="text-blue-600 font-medium text-lg">
                                {{ contact.client.first_name.charAt(0) }}{{ contact.client.last_name.charAt(0) }}
                            </span>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">Message #{{ contact.id }}</h2>
                            <p class="text-gray-500 mt-1 flex items-center gap-2">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ getTimeAgo(contact.created_at) }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <Link
                        :href="route('admin.contacts.index')"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                        Retour
                    </Link>
                    <button
                        @click="confirmDelete"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        Supprimer
                    </button>
                </div>
            </div>

            <!-- Informations principales -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Informations du client -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                        <div class="px-4 py-3 bg-gray-50 border-b">
                            <h3 class="text-lg font-medium text-gray-900 flex items-center gap-2">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Informations client
                            </h3>
                        </div>
                        <div class="p-4">
                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Nom complet</p>
                                    <p class="mt-1 text-base text-gray-900">{{ contact.client.first_name }} {{ contact.client.last_name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Email</p>
                                    <p class="mt-1 text-base text-gray-900 flex items-center gap-2">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        {{ contact.client.email }}
                                    </p>
                                </div>
                                <div v-if="contact.client.phone">
                                    <p class="text-sm font-medium text-gray-500">Téléphone</p>
                                    <p class="mt-1 text-base text-gray-900 flex items-center gap-2">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                        {{ contact.client.phone }}
                                    </p>
                                </div>
                                <div v-if="contact.client.address">
                                    <p class="text-sm font-medium text-gray-500">Adresse</p>
                                    <p class="mt-1 text-base text-gray-900">{{ contact.client.address }}</p>
                                </div>
                                <div class="pt-4 border-t">
                                    <p class="text-sm font-medium text-gray-500">Client depuis</p>
                                    <p class="mt-1 text-base text-gray-900">{{ formatDate(contact.client.created_at) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Métadonnées du message -->
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                        <div class="px-4 py-3 bg-gray-50 border-b">
                            <h3 class="text-lg font-medium text-gray-900 flex items-center gap-2">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Métadonnées
                            </h3>
                        </div>
                        <div class="p-4">
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">ID du message</span>
                                    <span class="text-sm font-medium text-gray-900">#{{ contact.id }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Envoyé le</span>
                                    <span class="text-sm font-medium text-gray-900">{{ formatDateTime(contact.created_at) }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Dernière mise à jour</span>
                                    <span class="text-sm font-medium text-gray-900">{{ formatDateTime(contact.updated_at) }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Statut</span>
                                    <span :class="[
                                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                        getStatusClasses(contact.status).bg,
                                        getStatusClasses(contact.status).text
                                    ]">
                                        <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" v-html="getStatusIcon(contact.status)" />
                                        </svg>
                                        {{ getStatusClasses(contact.status).label }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                        <div class="px-4 py-3 bg-gray-50 border-b flex justify-between items-center">
                            <h3 class="text-lg font-medium text-gray-900 flex items-center gap-2">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Notes internes
                            </h3>
                            <button
                                @click="openNotesModal"
                                class="text-sm text-blue-600 hover:text-blue-800 font-medium"
                            >
                                {{ contact.notes ? 'Modifier' : 'Ajouter' }}
                            </button>
                        </div>
                        <div class="p-4">
                            <div v-if="contact.notes" class="bg-gray-50 rounded-lg p-4">
                                <p class="text-gray-700 whitespace-pre-wrap">{{ contact.notes }}</p>
                            </div>
                            <div v-else class="text-center py-6">
                                <svg class="h-10 w-10 text-gray-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <p class="text-gray-500 text-sm">Aucune note pour le moment</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Message principal -->
                <div class="lg:col-span-2">
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden h-full">
                        <div class="px-4 py-3 bg-gray-50 border-b flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Message</h3>
                                <p class="text-sm text-gray-500 mt-1">
                                    Sujet : <span class="font-medium text-gray-700">{{ contact.subject }}</span>
                                </p>
                            </div>
                            <span :class="[
                                'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium',
                                getStatusClasses(contact.status).bg,
                                getStatusClasses(contact.status).text
                            ]">
                                <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" v-html="getStatusIcon(contact.status)" />
                                </svg>
                                {{ getStatusClasses(contact.status).label }}
                            </span>
                        </div>
                        <div class="p-6">
                            <div class="prose prose-blue max-w-none">
                                <div class="whitespace-pre-wrap text-gray-700 leading-relaxed">
                                    {{ contact.description }}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Actions rapides -->
                        <div class="px-6 py-4 bg-gray-50 border-t">
                            <div class="flex flex-wrap gap-3">
                                <!-- <button
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition disabled:opacity-50"
                                    :disabled="isUpdating"
                                >
                                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    Répondre
                                </button> -->
                                
                                <button
                                    v-if="contact.status !== 'processed'"
                                    @click="updateStatus('processed')"
                                    :disabled="isUpdating"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition disabled:opacity-50"
                                >
                                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Marquer comme traité
                                </button>

                                <button
                                    v-if="contact.status !== 'archived'"
                                    @click="updateStatus('archived')"
                                    :disabled="isUpdating"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition disabled:opacity-50"
                                >
                                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                    </svg>
                                    Archiver
                                </button>

                                <button
                                    v-if="contact.status !== 'pending'"
                                    @click="updateStatus('pending')"
                                    :disabled="isUpdating"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition disabled:opacity-50"
                                >
                                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Remettre en attente
                                </button>

                                <button
                                    @click="openNotesModal"
                                    :disabled="isUpdating"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition disabled:opacity-50"
                                >
                                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    {{ contact.notes ? 'Modifier note' : 'Ajouter note' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>

    <!-- Modal de suppression -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
            <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 text-red-500 mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </div>
            <h3 class="text-xl font-semibold mb-2 text-center">Confirmer la suppression</h3>
            <p class="mb-6 text-gray-600 text-center">
                Êtes-vous sûr de vouloir supprimer le message "<span class="font-medium">{{ contact.subject }}</span>" ?
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
                    @click="deleteContact"
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition flex-1 max-w-xs"
                >
                    Supprimer
                </button>
            </div>
        </div>
    </div>

    <!-- Modal des notes -->
    <div v-if="showNotesModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-2xl w-full mx-4">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-900">
                    {{ contact.notes ? 'Modifier les notes' : 'Ajouter des notes' }}
                </h3>
                <button
                    @click="showNotesModal = false"
                    class="text-gray-400 hover:text-gray-500"
                >
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                    Notes internes
                </label>
                <textarea
                    id="notes"
                    v-model="notesInput"
                    rows="8"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Ajoutez des notes internes pour ce contact..."
                ></textarea>
                <p class="mt-2 text-sm text-gray-500">
                    Ces notes sont visibles uniquement par l'équipe administrative.
                </p>
            </div>
            
            <div class="flex gap-3 justify-end">
                <button
                    @click="showNotesModal = false"
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition"
                >
                    Annuler
                </button>
                <button
                    @click="saveNotes"
                    :disabled="isUpdating || notesInput === contact.notes"
                    :class="[
                        'px-4 py-2 text-white rounded-md transition',
                        isUpdating || notesInput === contact.notes
                            ? 'bg-blue-400 cursor-not-allowed'
                            : 'bg-blue-600 hover:bg-blue-700'
                    ]"
                >
                    <span v-if="isUpdating" class="flex items-center">
                        <svg class="animate-spin h-4 w-4 mr-2 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Enregistrement...
                    </span>
                    <span v-else>
                        Enregistrer
                    </span>
                </button>
            </div>
        </div>
    </div>
</template>