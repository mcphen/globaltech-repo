<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';
import { ref, computed, onMounted } from 'vue';
import { useToast } from 'vue-toast-notification';
import axios from 'axios';

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

interface Invoice {
    invoice_number: string;
    invoice_date: string;
    invoice_path: string;
    order_id: number;
    order_reference: string;
    total: number;
    currency: string;
    status: string;
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
    invoice_number: string | null;
    invoice_date: string | null;
    invoice_path: string | null;
    created_at: string;
    items: OrderItem[];
}

interface Appel {
    id: number;
    user_id: number;
    user: {
        id: number;
        name: string;
        email: string;
    };
    callable_type: string;
    callable_id: number;
    called_at: string;
    type: 'entrant' | 'sortant';
    status: 'répondu' | 'non-répondu' | 'rappel-prévu';
    duration: number | null;
    notes: string;
    next_call_at: string | null;
    created_at: string;
    updated_at: string;
    callable?: {
        id: number;
        subject?: string;
        reference?: string;
    };
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

// États
const activeTab = ref('profil');
const loadingInvoices = ref(false);
const loadingAppels = ref(false);
const showAppelModal = ref(false);
const showInvoices = ref(false);
const showAppelsList = ref(false);
const invoices = ref<Invoice[]>([]);
const appels = ref<Appel[]>([]);
const appelsPage = ref(1);
const appelsHasMore = ref(true);

// Formulaire d'appel
const appelForm = ref({
    type: 'sortant',
    status: 'répondu',
    duration: null as number | null,
    notes: '',
    next_call_at: '',
});

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
    const currencyMap: Record<string, string> = {
        'FCFA': 'XOF',
        'CFA': 'XOF',
        'XAF': 'XAF',
        'XOF': 'XOF',
        'EUR': 'EUR',
        'USD': 'USD',
        'GBP': 'GBP',
    };
    
    const validCurrency = currencyMap[currency.toUpperCase()] || 'EUR';
    
    try {
        return new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: validCurrency,
        }).format(price);
    } catch (error) {
        return new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'EUR',
        }).format(price);
    }
}

// Formatage de la durée d'appel
function formatDuration(seconds: number | null) {
    if (!seconds) return 'N/A';
    const minutes = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${minutes}:${secs.toString().padStart(2, '0')}`;
}

// Fonctions pour les appels
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
        paid: { text: 'Payée', class: 'bg-green-100 text-green-800' },
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

// Télécharger une facture
async function downloadInvoice(orderId: number, invoiceNumber: string) {
    try {
        window.open(route('admin.orders.download-invoice', orderId), '_blank');
    } catch (error) {
        console.error('Erreur lors du téléchargement:', error);
        $toast.error('Erreur lors du téléchargement de la facture');
    }
}

// Charger les factures
async function loadInvoices() {
    if (loadingInvoices.value) return;
    
    loadingInvoices.value = true;
    try {
        // Récupérer toutes les commandes avec factures
        const ordersWithInvoices = props.lead.orders.filter(order => 
            order.invoice_number && order.invoice_path
        );
        
        // Transformer en format facture
        invoices.value = ordersWithInvoices.map(order => ({
            invoice_number: order.invoice_number!,
            invoice_date: order.invoice_date!,
            invoice_path: order.invoice_path!,
            order_id: order.id,
            order_reference: order.reference,
            total: order.total,
            currency: order.currency,
            status: order.status
        }));
        
        showInvoices.value = true;
    } catch (error) {
        console.error('Erreur lors du chargement des factures:', error);
        $toast.error('Erreur lors du chargement des factures');
    } finally {
        loadingInvoices.value = false;
    }
}

// Charger les appels
async function loadAppels() {
    if (loadingAppels.value || !appelsHasMore.value) return;
    
    loadingAppels.value = true;
    try {
        // Charger les appels liés à ce lead (via ses commandes et rendez-vous)
        const userId = props.lead.user?.id;
        if (!userId) {
            appelsHasMore.value = false;
            return;
        }

        // Appeler l'API pour récupérer les appels liés à ce lead
        const { data } = await axios.get(route('admin.leads.appels.index', props.lead.id), {
            params: { page: appelsPage.value }
        });
        
        if (data.data && data.data.length > 0) {
            // Éviter les doublons
            const existingIds = new Set(appels.value.map(a => a.id));
            const newAppels = data.data.filter((appel: Appel) => !existingIds.has(appel.id));
            
            if (newAppels.length > 0) {
                appels.value = [...appels.value, ...newAppels];
            }
        }
        
        appelsPage.value++;
        appelsHasMore.value = data.current_page < data.last_page;
        showAppelsList.value = true;
        
    } catch (error) {
        console.error('Erreur lors du chargement des appels:', error);
        $toast.error('Erreur lors du chargement des appels');
    } finally {
        loadingAppels.value = false;
    }
}

// Créer un nouvel appel
async function createAppel() {
    if (!appelForm.value.notes.trim()) {
        $toast.error('Les notes sont obligatoires');
        return;
    }

    try {
        // Créer un appel directement lié au lead
        const { data } = await axios.post(route('admin.leads.appels.store', props.lead.id), {
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
        $toast.success('Appel enregistré avec succès');
        
    } catch (error) {
        console.error('Erreur lors de l\'enregistrement de l\'appel:', error);
        $toast.error('Erreur lors de l\'enregistrement de l\'appel');
    }
}

// Supprimer un appel
async function deleteAppel(appelId: number) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cet appel ?')) return;

    try {
        await axios.delete(route('admin.leads.appels.destroy', {
            lead: props.lead.id,
            appel: appelId
        }));
        
        // Supprimer de la liste
        appels.value = appels.value.filter(a => a.id !== appelId);
        $toast.success('Appel supprimé avec succès');
        
    } catch (error) {
        console.error('Erreur lors de la suppression de l\'appel:', error);
        $toast.error('Erreur lors de la suppression de l\'appel');
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

// Initialiser
onMounted(() => {
    // Pré-charger les factures si des commandes en ont
    const hasInvoices = props.lead.orders.some(order => order.invoice_number);
    if (hasInvoices) {
        loadInvoices();
    }

    // Pré-charger les appels dès le chargement de la page
    loadAppels();
});
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
                    <!-- Bouton Nouvel Appel -->
                    <button
                        @click="showAppelModal = true"
                        class="p-3 bg-purple-500 text-white rounded-full hover:bg-purple-600 transition shadow-lg"
                        title="Nouvel appel"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                        </svg>
                    </button>
                </div>

                <div class="flex flex-wrap gap-2">
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
                <nav class="-mb-px flex space-x-4 overflow-x-auto">
                    <button
                        v-for="tab in [
                            { id: 'profil', name: 'Profil' },
                            { id: 'commandes', name: 'Commandes' },
                            { id: 'formations', name: 'Formations' },
                            { id: 'services', name: 'Services' },
                            { id: 'factures', name: 'Factures' },
                            { id: 'appels', name: 'Appels' }
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
                        <span v-if="tab.id === 'factures' && invoices.length > 0" class="ml-2 bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded-full">
                            {{ invoices.length }}
                        </span>
                        <span v-if="tab.id === 'appels' && appels.length > 0" class="ml-2 bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded-full">
                            {{ appels.length }}
                        </span>
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
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-800">{{ order.reference }}</h4>
                                    <p class="text-sm text-gray-600">Créée le {{ formatDate(order.created_at) }}</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span :class="['px-3 py-1 text-sm font-medium rounded-full', getOrderStatus(order.status).class]">
                                            {{ getOrderStatus(order.status).text }}
                                        </span>
                                        <span v-if="order.invoice_number" class="px-3 py-1 text-sm font-medium rounded-full bg-blue-100 text-blue-800">
                                            Facture: {{ order.invoice_number }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex flex-col sm:flex-row gap-2">
                                    <select
                                        :value="order.status"
                                        @change="updateOrderStatus(order.id, ($event.target as HTMLSelectElement).value)"
                                        class="text-sm border border-gray-300 rounded-md px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    >
                                        <option value="pending">En attente</option>
                                        <option value="paid">Payée</option>
                                        <option value="cancelled">Annulée</option>
                                    </select>
                                    <button
                                        v-if="order.invoice_path"
                                        @click="downloadInvoice(order.id, order.invoice_number!)"
                                        class="px-3 py-1 text-sm bg-green-600 text-white rounded-md hover:bg-green-700 transition"
                                    >
                                        Télécharger facture
                                    </button>
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

                <!-- Onglet Factures -->
                <div v-if="activeTab === 'factures'" class="space-y-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Factures</h3>
                        <span class="text-sm text-gray-500">{{ invoices.length }} facture(s) trouvée(s)</span>
                    </div>

                    <div v-if="loadingInvoices" class="text-center py-8">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
                        <p class="text-gray-500 mt-2">Chargement des factures...</p>
                    </div>

                    <div v-else-if="invoices.length === 0" class="text-center py-8">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-gray-500">Aucune facture disponible pour ce prospect.</p>
                        <p class="text-sm text-gray-400 mt-2">Les factures seront disponibles après paiement des commandes.</p>
                    </div>

                    <div v-else class="space-y-4">
                        <div v-for="invoice in invoices" :key="invoice.invoice_number" class="border border-gray-200 rounded-lg p-6">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-800">Facture {{ invoice.invoice_number }}</h4>
                                    <p class="text-sm text-gray-600">Émise le {{ formatDate(invoice.invoice_date) }}</p>
                                    <p class="text-sm text-gray-600">Commande: {{ invoice.order_reference }}</p>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span :class="['px-3 py-1 text-sm font-medium rounded-full', getOrderStatus(invoice.status).class]">
                                        {{ getOrderStatus(invoice.status).text }}
                                    </span>
                                    <span class="text-lg font-bold text-gray-800">
                                        {{ formatPrice(invoice.total, invoice.currency) }}
                                    </span>
                                    <button
                                        @click="downloadInvoice(invoice.order_id, invoice.invoice_number)"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition flex items-center gap-2"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                        Télécharger
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Onglet Appels -->
                <div v-if="activeTab === 'appels'" class="space-y-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Historique des appels</h3>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-500">{{ appels.length }} appel(s) enregistré(s)</span>
                            <button
                                @click="showAppelModal = true"
                                class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition flex items-center gap-2"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                </svg>
                                Nouvel appel
                            </button>
                        </div>
                    </div>

                    <div v-if="loadingAppels && appels.length === 0" class="text-center py-8">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
                        <p class="text-gray-500 mt-2">Chargement des appels...</p>
                    </div>

                    <div v-else-if="appels.length === 0" class="text-center py-8">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <p class="text-gray-500">Aucun appel enregistré pour ce prospect.</p>
                        <p class="text-sm text-gray-400 mt-2">Cliquez sur "Nouvel appel" pour commencer à enregistrer.</p>
                    </div>

                    <div v-else class="space-y-4">
                        <div v-for="appel in appels" :key="appel.id" class="border border-gray-200 rounded-lg p-6">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex items-center gap-2">
                                    <span :class="['px-2 py-1 text-xs font-medium rounded-full', getAppelTypeClasses(appel.type)]">
                                        {{ getAppelTypeLabel(appel.type) }}
                                    </span>
                                    <span :class="['px-2 py-1 text-xs font-medium rounded-full', getAppelStatusClasses(appel.status)]">
                                        {{ getAppelStatusLabel(appel.status) }}
                                    </span>
                                    <span class="text-sm text-gray-500">
                                        {{ formatDate(appel.called_at) }}
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
                            
                            <div class="text-sm text-gray-600 mb-3">
                                <div class="flex flex-wrap items-center gap-4">
                                    <span class="flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                        </svg>
                                        Par: {{ appel.user.name }}
                                    </span>
                                    <span v-if="appel.duration" class="flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                        </svg>
                                        Durée: {{ formatDuration(appel.duration) }}
                                    </span>
                                    <span v-if="appel.next_call_at" class="flex items-center gap-1 text-yellow-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                        </svg>
                                        Rappel: {{ formatDate(appel.next_call_at) }}
                                    </span>
                                    <span v-if="appel.callable" class="flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                                        </svg>
                                        {{ appel.callable_type === 'App\\Models\\Order' ? 'Commande' : 'Rendez-vous' }}: 
                                        {{ appel.callable.subject || appel.callable.reference || `#${appel.callable.id}` }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="text-sm text-gray-700 whitespace-pre-wrap bg-gray-50 p-4 rounded-md border border-gray-200">
                                {{ appel.notes }}
                            </div>
                        </div>

                        <button
                            v-if="appelsHasMore"
                            @click="loadAppels"
                            :disabled="loadingAppels"
                            class="w-full py-3 text-sm font-medium text-blue-600 hover:text-blue-800 disabled:opacity-50 flex items-center justify-center gap-2"
                        >
                            <span v-if="loadingAppels" class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-500"></span>
                            {{ loadingAppels ? 'Chargement...' : 'Charger plus d\'appels' }}
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
                    <h3 class="text-xl font-semibold text-gray-900">Nouvel appel pour {{ lead.full_name }}</h3>
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
                            @click="createAppel"
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