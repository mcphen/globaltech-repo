<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';
import { ref, computed } from 'vue';
import { useToast } from 'vue-toast-notification';

const $toast = useToast();


// Types
interface Formation {
    id: number;
    title: string;
    price: number;
    currency: string;
    duration_hours: number;
    pivot: {
        id: number;
        attentes: string | null;
        status: string;
        paid_at: string | null;
        created_at: string | null;
    };
}

interface OrderItem {
    id: number;
    title: string;
    price: number;
    quantity: number;
    line_total: number;
}

interface Order {
    id: number;
    reference: string;
    items_count: number;
    subtotal: number;
    total: number;
    currency: string;
    status: string;
    customer_name: string;
    customer_email: string;
    customer_phone: string;
    notes: string;
    created_at: string;
    items: OrderItem[];
}

interface Service {
    id: number;
    title: string;
    min_price: number;
}

interface Appointment {
    id: number;
    subject: string;
    description: string;
    status: string;
    schedule: {
        date: string;
        start_time: string;
        end_time: string;
    } | null;
    services: Service[];
    created_at: string;
}

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
    user: {
        id: number;
        name: string;
        email: string;
        email_verified_at: string | null;
        created_at: string;
    } | null;
    formations: Formation[];
    orders: Order[];
    appointments: Appointment[];
    stats: {
        total_spent: number;
        orders_count: number;
        pending_orders: number;
        formations_count: number;
        completed_formations: number;
        pending_formations: number;
        appointments_count: number;
        upcoming_appointments: number;
    };
}

// Props
interface Props {
    lead: Lead;
    flash?: { success?: string; error?: string };
}

const props = defineProps<Props>();

// Onglets
const activeTab = ref('profil');

// Définition des fil d'Ariane
const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Prospects', href: route('admin.leads.index') },
    { title: props.lead.full_name, href: route('admin.leads.show', props.lead.id) }
];

// Formatage de la date
function formatDate(dateString: string | null): string {
    if (!dateString) return 'Non défini';

    const date = new Date(dateString);
    return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(date);
}

// Formatage du prix
function formatPrice(price: number, currency: string = 'EUR'): string {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: currency,
    }).format(price);
}

// Statuts
function getConsentStatus(consentAt: string | null): { text: string; class: string } {
    if (!consentAt) {
        return { text: 'Non consent', class: 'bg-red-100 text-red-800' };
    }
    return { text: 'Consenti', class: 'bg-green-100 text-green-800' };
}

function getFormationStatus(status: string): { text: string; class: string } {
    const statusMap: { [key: string]: { text: string; class: string } } = {
        unpaid: { text: 'Non payé', class: 'bg-yellow-100 text-yellow-800' },
        paid: { text: 'Payé', class: 'bg-blue-100 text-blue-800' },
        cancelled: { text: 'Annulé', class: 'bg-red-100 text-red-800' },
        completed: { text: 'Terminé', class: 'bg-green-100 text-green-800' },
    };
    return statusMap[status] || { text: status, class: 'bg-gray-100 text-gray-800' };
}

function getOrderStatus(status: string): { text: string; class: string } {
    const statusMap: { [key: string]: { text: string; class: string } } = {
        pending: { text: 'En attente', class: 'bg-yellow-100 text-yellow-800' },
        confirmed: { text: 'Confirmée', class: 'bg-blue-100 text-blue-800' },
        completed: { text: 'Terminée', class: 'bg-green-100 text-green-800' },
        cancelled: { text: 'Annulée', class: 'bg-red-100 text-red-800' },
    };
    return statusMap[status] || { text: status, class: 'bg-gray-100 text-gray-800' };
}

function getAppointmentStatus(status: string): { text: string; class: string } {
    const statusMap: { [key: string]: { text: string; class: string } } = {
        pending: { text: 'En attente', class: 'bg-yellow-100 text-yellow-800' },
        confirmed: { text: 'Confirmé', class: 'bg-blue-100 text-blue-800' },
        completed: { text: 'Terminé', class: 'bg-green-100 text-green-800' },
        cancelled: { text: 'Annulé', class: 'bg-red-100 text-red-800' },
    };
    return statusMap[status] || { text: status, class: 'bg-gray-100 text-gray-800' };
}

// Mise à jour des statuts
async function updateFormationStatus(formationId: number, status: string) {
    try {
        await router.put(route('admin.leads.formations.update', {
            lead: props.lead.id,
            formation: formationId
        }), { status });
        $toast.success('Statut de formation mis à jour avec succès');
        
        // Recharger la page pour voir les changements
        router.reload();
    } catch (error) {
        console.error('Erreur lors de la mise à jour du statut:', error);
    }
}

async function updateOrderStatus(orderId: number, status: string) {
    try {
        await router.put(route('admin.leads.orders.update', {
            lead: props.lead.id,
            order: orderId
        }), { status });
        $toast.success('Statut de la commande mis à jour avec succès');
        
        router.reload();
    } catch (error) {
        console.error('Erreur lors de la mise à jour du statut:', error);
    }
}

async function updateAppointmentStatus(appointmentId: number, status: string) {
    try {
        await router.put(route('admin.leads.appointments.update', {
            lead: props.lead.id,
            appointment: appointmentId
        }), { status });
        $toast.success('Statut de rendez-vous mis à jour avec succès');
        
        router.reload();
    } catch (error) {
        console.error('Erreur lors de la mise à jour du statut:', error);
    }
}

// Appel téléphonique
function makePhoneCall(phone: string | null) {
    if (phone) {
        window.open(`tel:${phone}`, '_self');
    }
}

// Statistiques calculées
const stats = computed(() => [
    {
        title: 'Total Dépensé',
        value: formatPrice(props.lead.stats.total_spent),
        icon: '💰',
        color: 'text-green-600 bg-green-50'
    },
    {
        title: 'Commandes',
        value: props.lead.stats.orders_count,
        subvalue: `${props.lead.stats.pending_orders} en attente`,
        icon: '📦',
        color: 'text-blue-600 bg-blue-50'
    },
    {
        title: 'Formations',
        value: props.lead.stats.formations_count,
        subvalue: `${props.lead.stats.completed_formations} terminées`,
        icon: '🎓',
        color: 'text-purple-600 bg-purple-50'
    },
    {
        title: 'Rendez-vous',
        value: props.lead.stats.appointments_count,
        subvalue: `${props.lead.stats.upcoming_appointments} à venir`,
        icon: '📅',
        color: 'text-orange-600 bg-orange-50'
    }
]);
</script>

<template>
    <Head :title="`Prospect - ${lead.full_name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm">
            <!-- En-tête avec titre et boutons -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 border-b pb-6">
                <div class="flex items-center gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">{{ lead.full_name }}</h2>
                        <p class="text-gray-500 mt-1">
                            Prospect depuis {{ formatDate(lead.created_at) }}
                        </p>
                    </div>
                    <!-- Bouton d'appel -->
                    <button
                        v-if="lead.phone"
                        @click="makePhoneCall(lead.phone)"
                        class="p-3 bg-green-500 text-white rounded-full hover:bg-green-600 transition shadow-lg"
                        title="Appeler"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                    </button>
                </div>

                <div class="flex items-center gap-4">
                    <Link
                        :href="route('admin.leads.index')"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition flex items-center gap-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                        Retour
                    </Link>
                </div>
            </div>

            <!-- Messages -->
            <div v-if="props.flash?.success" class="bg-green-50 border-l-4 border-green-500 p-4 rounded-md flex items-start">
                <svg class="h-6 w-6 text-green-500 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-green-700">{{ props.flash.success }}</p>
                </div>
            </div>

            <div v-if="props.flash?.error" class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md flex items-start">
                <svg class="h-6 w-6 text-red-500 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-red-700">{{ props.flash.error }}</p>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div
                    v-for="stat in stats"
                    :key="stat.title"
                    class="bg-white border border-gray-200 rounded-lg p-4 flex items-center gap-4"
                >
                    <div :class="['p-3 rounded-full', stat.color]">
                        <span class="text-xl">{{ stat.icon }}</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">{{ stat.title }}</p>
                        <p class="text-xl font-bold text-gray-900">{{ stat.value }}</p>
                        <p v-if="stat.subvalue" class="text-xs text-gray-500">{{ stat.subvalue }}</p>
                    </div>
                </div>
            </div>

            <!-- Navigation par onglets -->
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <button
                        v-for="tab in [
                            { id: 'profil', name: 'Profil' },
                            { id: 'commandes', name: 'Commandes' },
                            { id: 'formations', name: 'Formations' },
                            { id: 'services', name: 'Services' }
                        ]"
                        :key="tab.id"
                        @click="activeTab = tab.id"
                        :class="[
                            activeTab === tab.id
                                ? 'border-blue-500 text-blue-600'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                            'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
                        ]"
                    >
                        {{ tab.name }}
                    </button>
                </nav>
            </div>

            <!-- Contenu des onglets -->
            <div class="flex-1">
                <!-- Onglet Profil -->
                <div v-if="activeTab === 'profil'" class="space-y-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Informations personnelles -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informations personnelles</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Nom complet:</span>
                                    <span class="font-medium">{{ lead.full_name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Email:</span>
                                    <span class="font-medium">{{ lead.email }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Téléphone:</span>
                                    <span class="font-medium">{{ lead.phone || 'Non renseigné' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Entreprise:</span>
                                    <span class="font-medium">{{ lead.company || 'Non renseignée' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Consentement RGPD:</span>
                                    <span :class="['px-2 py-1 text-xs font-medium rounded-full', getConsentStatus(lead.consent_at).class]">
                                        {{ getConsentStatus(lead.consent_at).text }}
                                    </span>
                                </div>
                                <div v-if="lead.consent_at" class="flex justify-between">
                                    <span class="text-gray-600">Date de consentement:</span>
                                    <span class="font-medium text-sm">{{ formatDate(lead.consent_at) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Informations du compte -->
                        <div class="bg-gray-50 rounded-lg p-6" v-if="lead.user">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Compte utilisateur</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Nom d'utilisateur:</span>
                                    <span class="font-medium">{{ lead.user.name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Email vérifié:</span>
                                    <span v-if="lead.user.email_verified_at" class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                        Oui
                                    </span>
                                    <span v-else class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">
                                        Non
                                    </span>
                                </div>
                                <div v-if="lead.user.email_verified_at" class="flex justify-between">
                                    <span class="text-gray-600">Vérifié le:</span>
                                    <span class="font-medium text-sm">{{ formatDate(lead.user.email_verified_at) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Compte créé le:</span>
                                    <span class="font-medium text-sm">{{ formatDate(lead.user.created_at) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Métadonnées -->
                        <div class="bg-gray-50 rounded-lg p-6 lg:col-span-2">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Métadonnées</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Date de création:</span>
                                    <span class="font-medium text-sm">{{ formatDate(lead.created_at) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Dernière modification:</span>
                                    <span class="font-medium text-sm">{{ formatDate(lead.updated_at) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Onglet Commandes -->
                <div v-if="activeTab === 'commandes'" class="space-y-6">
                    <div v-if="lead.orders.length === 0" class="text-center py-8">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <p class="text-gray-500">Aucune commande trouvée pour ce prospect.</p>
                    </div>

                    <div v-else class="space-y-4">
                        <div v-for="order in lead.orders" :key="order.id" class="border border-gray-200 rounded-lg p-6">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4">
                                <div>
                                    <h4 class="font-semibold text-gray-800">{{ order.reference }}</h4>
                                    <p class="text-sm text-gray-600">Créée le {{ formatDate(order.created_at) }}</p>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span :class="['px-3 py-1 text-sm font-medium rounded-full', getOrderStatus(order.status).class]">
                                        {{ getOrderStatus(order.status).text }}
                                    </span>
                                    <select
                                        :value="order.status"
                                        @change="updateOrderStatus(order.id, ($event.target as HTMLSelectElement).value)"
                                        class="text-sm border border-gray-300 rounded-md px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    >
                                        <option value="pending">En attente</option>
                                        <option value="confirmed">Confirmée</option>
                                        <option value="completed">Terminée</option>
                                        <option value="cancelled">Annulée</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm mb-4">
                                <div>
                                    <span class="text-gray-600">Articles:</span>
                                    <span class="font-medium ml-2">{{ order.items_count }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Sous-total:</span>
                                    <span class="font-medium ml-2">{{ formatPrice(order.subtotal, order.currency) }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Total:</span>
                                    <span class="font-medium ml-2">{{ formatPrice(order.total, order.currency) }}</span>
                                </div>
                            </div>

                            <div v-if="order.items.length > 0" class="border-t pt-4">
                                <h5 class="font-medium text-gray-700 mb-2">Articles commandés:</h5>
                                <div class="space-y-2">
                                    <div v-for="item in order.items" :key="item.id" class="flex justify-between text-sm">
                                        <span>{{ item.title }} (x{{ item.quantity }})</span>
                                        <span class="font-medium">{{ formatPrice(item.line_total, order.currency) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div v-if="order.notes" class="border-t pt-4 mt-4">
                                <h5 class="font-medium text-gray-700 mb-2">Notes:</h5>
                                <p class="text-sm text-gray-600">{{ order.notes }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Onglet Formations -->
                <div v-if="activeTab === 'formations'" class="space-y-6">
                    <div v-if="lead.formations.length === 0" class="text-center py-8">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v6l9-5-9-5-9 5 9 5z" />
                        </svg>
                        <p class="text-gray-500">Ce prospect n'est inscrit à aucune formation.</p>
                    </div>

                    <div v-else class="space-y-4">
                        <div v-for="formation in lead.formations" :key="formation.id" class="border border-gray-200 rounded-lg p-6">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-800">{{ formation.title }}</h4>
                                    <p class="text-sm text-gray-600">Inscrit le {{ formatDate(formation.pivot.created_at) }}</p>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span :class="['px-3 py-1 text-sm font-medium rounded-full', getFormationStatus(formation.pivot.status).class]">
                                        {{ getFormationStatus(formation.pivot.status).text }}
                                    </span>
                                    <select
                                        :value="formation.pivot.status"
                                        @change="updateFormationStatus(formation.id, ($event.target as HTMLSelectElement).value)"
                                        class="text-sm border border-gray-300 rounded-md px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    >
                                        <option value="unpaid">Non payé</option>
                                        <option value="paid">Payé</option>
                                        <option value="completed">Terminé</option>
                                        <option value="cancelled">Annulé</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">Prix:</span>
                                    <span class="font-medium ml-2">{{ formatPrice(formation.price, formation.currency) }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Durée:</span>
                                    <span class="font-medium ml-2">{{ formation.duration_hours }}h</span>
                                </div>
                                <div v-if="formation.pivot.paid_at">
                                    <span class="text-gray-600">Payé le:</span>
                                    <span class="font-medium ml-2">{{ formatDate(formation.pivot.paid_at) }}</span>
                                </div>
                            </div>

                            <div v-if="formation.pivot.attentes" class="mt-4">
                                <h5 class="font-medium text-gray-700 mb-2">Attentes du prospect:</h5>
                                <p class="text-sm text-gray-600">{{ formation.pivot.attentes }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Onglet Services -->
                <div v-if="activeTab === 'services'" class="space-y-6">
                    <div v-if="lead.appointments.length === 0" class="text-center py-8">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="text-gray-500">Aucun rendez-vous de service pour ce prospect.</p>
                    </div>

                    <div v-else class="space-y-4">
                        <div v-for="appointment in lead.appointments" :key="appointment.id" class="border border-gray-200 rounded-lg p-6">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-800">{{ appointment.subject }}</h4>
                                    <p class="text-sm text-gray-600">Créé le {{ formatDate(appointment.created_at) }}</p>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span :class="['px-3 py-1 text-sm font-medium rounded-full', getAppointmentStatus(appointment.status).class]">
                                        {{ getAppointmentStatus(appointment.status).text }}
                                    </span>
                                    <select
                                        :value="appointment.status"
                                        @change="updateAppointmentStatus(appointment.id, ($event.target as HTMLSelectElement).value)"
                                        class="text-sm border border-gray-300 rounded-md px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    >
                                        <option value="pending">En attente</option>
                                        <option value="confirmed">Confirmé</option>
                                        <option value="completed">Terminé</option>
                                        <option value="cancelled">Annulé</option>
                                    </select>
                                </div>
                            </div>

                            <div v-if="appointment.description" class="mb-4">
                                <h5 class="font-medium text-gray-700 mb-2">Description:</h5>
                                <p class="text-sm text-gray-600">{{ appointment.description }}</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div v-if="appointment.schedule">
                                    <span class="text-gray-600">Date:</span>
                                    <span class="font-medium ml-2">
                                        {{ formatDate(appointment.schedule.date) }} 
                                        de {{ appointment.schedule.start_time }} à {{ appointment.schedule.end_time }}
                                    </span>
                                </div>
                                <div v-if="appointment.services.length > 0">
                                    <span class="text-gray-600">Services:</span>
                                    <span class="font-medium ml-2">
                                        {{ appointment.services.map(s => s.title).join(', ') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>