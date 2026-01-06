<script setup lang="ts">
import { Head, Link, router  } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

// Types
interface Client {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    phone: string | null;
    address: string | null;
}

interface Contact {
    id: number;
    client_id: number;
    subject: string;
    description: string;
    status: 'pending' | 'processed' | 'archived';
    notes: string | null;
    created_at: string;
    updated_at: string;
    client: Client;
}

interface Pagination {
    current_page: number;
    data: Contact[];
    from: number;
    last_page: number;
    links: Array<{ url: string | null; label: string; active: boolean }>;
    per_page: number;
    to: number;
    total: number;
}

interface Filters {
    status?: 'pending' | 'processed' | 'archived';
}

// Props
interface Props {
    contacts: Pagination;
    flash?: { success?: string };
    filters?: Filters;
}

const props = defineProps<Props>();

// Accéder aux contacts
const contactsList = computed(() => props.contacts.data);

// Définition des fil d'Ariane
const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Contacts', href: route('admin.contacts.index') }
];

// État de confirmation de suppression
const contactToDelete = ref<Contact | null>(null);
const showDeleteModal = ref(false);

// Mode d'affichage (grille ou liste) - Liste par défaut
const viewMode = ref<'grid' | 'list'>('list');

// Filtres
const activeFilter = ref(props.filters?.status || 'all');

// Statistiques
const stats = ref({
    totalContacts: 0,
    contactsToday: 0,
    pendingContacts: 0,
    processedContacts: 0,
    archivedContacts: 0,
    uniqueClients: 0
});

// Fonctions
function confirmDelete(contact: Contact) {
    contactToDelete.value = contact;
    showDeleteModal.value = true;
}

function cancelDelete() {
    contactToDelete.value = null;
    showDeleteModal.value = false;
}

async function deleteContact() {
    if (contactToDelete.value) {
        try {
            await axios.delete(route('admin.contacts.destroy', contactToDelete.value.id));
            router.reload({ preserveState: false });
        } catch (error) {
            console.error('Erreur lors de la suppression:', error);
            alert('Erreur lors de la suppression du contact.');
        }
    }
    showDeleteModal.value = false;
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

// Tronquer le texte
function truncateText(text: string, maxLength: number = 100): string {
    if (!text) return '';
    if (text.length <= maxLength) return text;
    return text.substring(0, maxLength) + '...';
}

// Obtenir les classes pour le badge de statut
function getStatusClasses(status: string) {
    switch(status) {
        case 'pending':
            return { bg: 'bg-yellow-100', text: 'text-yellow-800', label: 'En attente', icon: 'pending' };
        case 'processed':
            return { bg: 'bg-green-100', text: 'text-green-800', label: 'Traité', icon: 'processed' };
        case 'archived':
            return { bg: 'bg-gray-100', text: 'text-gray-800', label: 'Archivé', icon: 'archived' };
        default:
            return { bg: 'bg-gray-100', text: 'text-gray-800', label: 'Inconnu', icon: 'unknown' };
    }
}

// Obtenir l'icône pour le statut
function getStatusIcon(iconType: string) {
    switch(iconType) {
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

// Calculer les statistiques
function calculateStats() {
    const totalContacts = props.contacts.total;
    const today = new Date().toISOString().split('T')[0];
    
    // Filtrer les contacts de la page actuelle pour les statistiques
    const contactsToday = contactsList.value.filter(contact => {
        const contactDate = new Date(contact.created_at).toISOString().split('T')[0];
        return contactDate === today;
    }).length;

    const pendingContacts = contactsList.value.filter(contact => contact.status === 'pending').length;
    const processedContacts = contactsList.value.filter(contact => contact.status === 'processed').length;
    const archivedContacts = contactsList.value.filter(contact => contact.status === 'archived').length;

    const uniqueClients = new Set(contactsList.value.map(contact => contact.client_id)).size;

    stats.value = {
        totalContacts,
        contactsToday,
        pendingContacts,
        processedContacts,
        archivedContacts,
        uniqueClients
    };
}

// Fonction pour changer le filtre
function applyFilter(status: string) {
    activeFilter.value = status;
    
    // Utiliser Inertia pour recharger la page avec les paramètres de filtre
    router.get(route('admin.contacts.index'), {
        status: status === 'all' ? undefined : status
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true
    });
}

// Fonction pour vérifier si un filtre est actif
function isFilterActive(status: string) {
    return activeFilter.value === status;
}

onMounted(() => {
    calculateStats();
});
</script>

<template>
    <Head title="Messages de contact" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm">
            <!-- En-tête avec titre -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 border-b pb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Gestion des contacts</h2>
                    <p class="text-gray-500 mt-1">
                        Gérez les messages de contact et les demandes de vos clients
                    </p>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Sélecteur de vue -->
                    <div class="flex bg-gray-100 rounded-md p-1">
                        <button
                            @click="viewMode = 'grid'"
                            :class="[
                                'px-3 py-1.5 rounded-md flex items-center gap-2 transition',
                                viewMode === 'grid'
                                    ? 'bg-white shadow-sm text-blue-600'
                                    : 'text-gray-600 hover:bg-gray-200'
                            ]"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            Grille
                        </button>
                        <button
                            @click="viewMode = 'list'"
                            :class="[
                                'px-3 py-1.5 rounded-md flex items-center gap-2 transition',
                                viewMode === 'list'
                                    ? 'bg-white shadow-sm text-blue-600'
                                    : 'text-gray-600 hover:bg-gray-200'
                            ]"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                            </svg>
                            Liste
                        </button>
                    </div>
                </div>
            </div>

            <!-- Filtres rapides -->
          <!-- Filtres rapides -->
<div class="flex flex-wrap gap-2 mb-4">
    <button
        @click="applyFilter('all')"
        :class="[
            'px-4 py-2 rounded-md text-sm font-medium transition',
            isFilterActive('all')
                ? 'bg-blue-600 text-white'
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
        ]"
    >
        Tous ({{ stats.totalContacts }})
    </button>
    <button
        @click="applyFilter('pending')"
        :class="[
            'px-4 py-2 rounded-md text-sm font-medium transition',
            isFilterActive('pending')
                ? 'bg-yellow-600 text-white'
                : 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200'
        ]"
    >
        En attente ({{ stats.pendingContacts }})
    </button>
    <button
        @click="applyFilter('processed')"
        :class="[
            'px-4 py-2 rounded-md text-sm font-medium transition',
            isFilterActive('processed')
                ? 'bg-green-600 text-white'
                : 'bg-green-100 text-green-800 hover:bg-green-200'
        ]"
    >
        Traités ({{ stats.processedContacts }})
    </button>
    <button
        @click="applyFilter('archived')"
        :class="[
            'px-4 py-2 rounded-md text-sm font-medium transition',
            isFilterActive('archived')
                ? 'bg-gray-600 text-white'
                : 'bg-gray-100 text-gray-800 hover:bg-gray-200'
        ]"
    >
        Archivés ({{ stats.archivedContacts }})
    </button>
</div>

            <!-- Statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-blue-600">Total messages</p>
                            <p class="text-2xl font-bold text-blue-900">{{ stats.totalContacts }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-yellow-600">En attente</p>
                            <p class="text-2xl font-bold text-yellow-900">{{ stats.pendingContacts }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-green-600">Traités</p>
                            <p class="text-2xl font-bold text-green-900">{{ stats.processedContacts }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-purple-600">Clients uniques</p>
                            <p class="text-2xl font-bold text-purple-900">{{ stats.uniqueClients }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Message de succès -->
            <div v-if="props.flash?.success" class="bg-green-50 border-l-4 border-green-500 p-4 rounded-md mb-4 flex items-start">
                <svg class="h-6 w-6 text-green-500 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-green-700">{{ props.flash.success }}</p>
                </div>
            </div>

            <!-- Affichage en grille (cards) -->
            <div v-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="contact in contactsList" :key="contact.id" class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition">
                    <div class="p-4">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ contact.subject }}</h3>
                                <p class="text-sm text-gray-500">
                                    De {{ contact.client.first_name }} {{ contact.client.last_name }}
                                </p>
                            </div>
                            <span :class="[
                                'px-2 py-1 text-xs font-medium rounded-full',
                                getStatusClasses(contact.status).bg,
                                getStatusClasses(contact.status).text
                            ]">
                                {{ getStatusClasses(contact.status).label }}
                            </span>
                        </div>
                        
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                            {{ truncateText(contact.description, 150) }}
                        </p>
                        
                        <div class="flex items-center justify-between border-t pt-3">
                            <div class="text-sm text-gray-500">
                                <div class="flex items-center gap-2">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    {{ contact.client.email }}
                                </div>
                                <div class="text-xs text-gray-400 mt-1">
                                    {{ formatDate(contact.created_at) }}
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <Link
                                    :href="route('admin.contacts.show', contact.id)"
                                    class="px-3 py-1.5 text-sm bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 transition"
                                >
                                    Voir
                                </Link>
                                <button
                                    @click="confirmDelete(contact)"
                                    class="px-3 py-1.5 text-sm bg-red-50 text-red-700 rounded-md hover:bg-red-100 transition"
                                >
                                    Supprimer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Affichage en liste (table) - PAR DÉFAUT -->
            <div v-if="viewMode === 'list'" class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sujet</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="contact in contactsList" :key="contact.id" class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600 font-medium">
                                            {{ contact.client.first_name.charAt(0) }}{{ contact.client.last_name.charAt(0) }}
                                        </span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ contact.client.first_name }} {{ contact.client.last_name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ contact.client.phone || 'N/A' }}
                                        </div>
                                         <div class="text-sm text-gray-500">
                                            {{ contact.client.email || 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                           
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ contact.subject }}</div>
                                <div class="text-sm text-gray-500 line-clamp-2 max-w-xs">
                                    {{ truncateText(contact.description, 80) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ formatDateTime(contact.created_at) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="[
                                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                    getStatusClasses(contact.status).bg,
                                    getStatusClasses(contact.status).text
                                ]">
                                 
                                    {{ getStatusClasses(contact.status).label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                <div class="flex justify-center gap-2">
                                    <Link
                                        :href="route('admin.contacts.show', contact.id)"
                                        class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 transition inline-flex items-center text-sm"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                        </svg>
                                        
                                    </Link>
                                    <button
                                        @click="confirmDelete(contact)"
                                        class="px-3 py-1.5 bg-red-50 text-red-700 rounded-md hover:bg-red-100 transition inline-flex items-center text-sm"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="contactsList.length === 0">
                            <td colspan="6" class="px-6 py-10 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <p class="text-gray-500">Aucun message de contact trouvé.</p>
                                <p v-if="activeFilter !== 'all'" class="text-sm text-gray-400 mt-1">
                                    Essayez de changer le filtre ou de réinitialiser les filtres.
                                </p>
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
                            Affichage de <span class="font-medium">{{ props.contacts.from }}</span> à
                            <span class="font-medium">{{ props.contacts.to }}</span> sur
                            <span class="font-medium">{{ props.contacts.total }}</span> messages
                        </p>
                    </div>
                    <div>
                        <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                            <Link
                                v-for="(link, i) in props.contacts.links"
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
                                    i === props.contacts.links.length - 1 ? 'rounded-r-md' : '',
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
                Êtes-vous sûr de vouloir supprimer le message "<span class="font-medium">{{ contactToDelete?.subject }}</span>" ?
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
</template>