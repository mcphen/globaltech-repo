<script setup lang="ts">
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { ref, computed } from 'vue';
import { useToast } from 'vue-toast-notification';


const $toast = useToast();

interface LeadPivot {
    attentes?: string | null;
    status?: 'paid' | 'unpaid' | string | null;
    paid_at?: string | null;
}

interface LeadItem {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    phone?: string | null;
    pivot?: LeadPivot;
}

interface Formation {
    id: number;
    title: string;
    slug: string;
    description?: string | null;
    image_path?: string | null;
    attachment_path?: string | null;
    date_mode: 'single' | 'range';
    date?: string | null;
    start_time?: string | null;
    end_time?: string | null;
    start_date?: string | null;
    end_date?: string | null;
    duration_hours?: number | null;
    duration_per_day_hours?: number | null;
    price?: number | null;
    currency?: string | null;
    leads?: LeadItem[];
    created_at: string;
    updated_at: string;
}

interface Props {
    formation: Formation;
    leadsCount: number;
}

const props = defineProps<Props>();
const page = usePage();

// Définition des fil d'Ariane
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Formations', href: route('admin.formations.index') },
    { title: props.formation.title, href: route('admin.formations.show', props.formation.id) }
];

// Accéder aux leads
const leads = computed<LeadItem[]>(() => props.formation.leads || []);

// URL publique
const publicUrl = computed(() => route('formations.show', props.formation.slug));

// Filtre des participants
const statusFilter = ref<'all' | 'paid' | 'unpaid'>('all');

// Formatage de la date
function formatDate(dateString: string): string {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: 'long',
        year: 'numeric'
    }).format(date);
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

// Formatage du prix
function formatPrice(price: number, currency: string | null | undefined = 'FCFA'): string {
    const currencyCode = currency === 'FCFA' || !currency ? 'XOF' : currency;
    return new Intl.NumberFormat('fr-FR', { 
        style: 'currency', 
        currency: currencyCode 
    }).format(price);
}

// Filtrer les participants
const filteredLeads = computed(() => {
    if (statusFilter.value === 'all') {
        return leads.value;
    }
    return leads.value.filter(lead => lead.pivot?.status === statusFilter.value);
});

// Statistiques des participants
const leadStats = computed(() => {
    const total = leads.value.length;
    const paid = leads.value.filter(lead => lead.pivot?.status === 'paid').length;
    const unpaid = total - paid;
    
    return {
        total,
        paid,
        unpaid,
        paidPercentage: total > 0 ? Math.round((paid / total) * 100) : 0
    };
});

// Partager la formation
async function shareFormation() {
    try {
        const url = publicUrl.value;
        const title = props.formation.title;
        const text = `Découvrez la formation "${title}"`;

        if (navigator.share) {
            await navigator.share({ title, text, url });
            return;
        }

        if (navigator.clipboard && navigator.clipboard.writeText) {
            await navigator.clipboard.writeText(url);
            alert('Lien copié dans le presse-papiers.');
            return;
        }

        // Fallback pour les anciens navigateurs
        const textarea = document.createElement('textarea');
        textarea.value = url;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        alert('Lien copié dans le presse-papiers.');
    } catch (e) {
        console.error(e);
        alert('Impossible de partager le lien. Veuillez réessayer.');
    }
}

// Mettre à jour le statut d'un participant
async function updateLeadStatus(leadId: number, status: string) {
    try {
        // Utiliser la route pour les formations avec props.formation
        await router.put(route('admin.formations.leads.update-status', {
            formation: props.formation.id,
            lead: leadId
        }), { status });

        $toast.success('Statut du participant mis à jour avec succès.');
        
        // Recharger les données
        router.reload({ only: ['formation'] });
    } catch (error) {
        console.error('Erreur lors de la mise à jour du statut:', error);
    }
}
</script>

<template>
    <Head :title="`${formation.title} - Détails`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm">
            <!-- En-tête avec titre et actions -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 border-b pb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">{{ formation.title }}</h2>
                    <p class="text-gray-500 mt-1">
                        Créé le {{ formatDateTime(formation.created_at) }}
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <button
                        @click="shareFormation"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z" />
                        </svg>
                        Partager
                    </button>
                    <Link
                        :href="route('admin.formations.edit', formation.id)"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                        Modifier
                    </Link>
                    <Link
                        :href="publicUrl"
                        target="_blank"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM6.75 9.25a.75.75 0 000 1.5h6.5a.75.75 0 000-1.5h-6.5z" clip-rule="evenodd" />
                        </svg>
                        Voir public
                    </Link>
                </div>
            </div>

            <!-- Message flash -->
            <!-- <div v-if="page.props.flash && page.props.flash.success" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md mb-4">
                {{ page.props.flash.success }}
            </div> -->

            <div v-if="page.props.flash && (page.props.flash as any).success" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md mb-4">
                {{ (page.props.flash as any).success }}
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
                                    v-if="formation.image_path"
                                    :src="formation.image_path"
                                    :alt="formation.title"
                                    class="w-full h-64 object-cover rounded-lg"
                                />
                                <div v-else class="w-full h-64 bg-gray-100 flex items-center justify-center rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                            </div>
                            
                            <div v-if="formation.description" class="prose prose-lg max-w-none">
                                <div v-html="formation.description"></div>
                            </div>
                            <div v-else class="text-gray-500 italic">
                                Aucune description fournie pour cette formation.
                            </div>

                            <!-- Brochure -->
                            <div v-if="formation.attachment_path" class="mt-6 pt-6 border-t">
                                <a
                                    :href="formation.attachment_path"
                                    target="_blank"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                    Télécharger la brochure
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Colonne de droite - Informations et statistiques -->
                <div class="space-y-6">
                    <!-- Informations de la formation -->
                    <div class="bg-white border rounded-lg shadow-sm overflow-hidden">
                        <div class="px-4 py-3 bg-gray-50 border-b">
                            <h3 class="text-lg font-medium text-gray-900">Informations</h3>
                        </div>
                        <div class="p-4 space-y-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Type de date</h4>
                                <p class="mt-1 text-sm text-gray-900 capitalize">{{ formation.date_mode }}</p>
                            </div>
                            
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Dates</h4>
                                <p class="mt-1 text-sm text-gray-900">
                                    <template v-if="formation.date_mode === 'single'">
                                        Le {{ formatDate(formation.date!) }}
                                        <template v-if="formation.start_time && formation.end_time">
                                            <br>de {{ formation.start_time }} à {{ formation.end_time }}
                                        </template>
                                    </template>
                                    <template v-else>
                                        Du {{ formatDate(formation.start_date!) }}<br>au {{ formatDate(formation.end_date!) }}
                                    </template>
                                </p>
                            </div>

                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Durée</h4>
                                <p class="mt-1 text-sm text-gray-900">
                                    <template v-if="formation.date_mode === 'single'">
                                        {{ formation.duration_hours }} heure{{ formation.duration_hours !== 1 ? 's' : '' }}
                                    </template>
                                    <template v-else>
                                        {{ formation.duration_per_day_hours }} heure{{ formation.duration_per_day_hours !== 1 ? 's' : '' }} par jour
                                    </template>
                                </p>
                            </div>

                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Tarif</h4>
                               <p class="mt-1 text-sm text-gray-900">
                                    <template v-if="formation.price">
                                        {{ formatPrice(formation.price, formation.currency || 'FCFA') }}
                                    </template>
                                    <template v-else>
                                        <span class="text-green-600 font-medium">Gratuit</span>
                                    </template>
                                </p>
                            </div>

                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Dernière modification</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ formatDateTime(formation.updated_at) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Statistiques des participants -->
                    <div class="bg-white border rounded-lg shadow-sm overflow-hidden">
                        <div class="px-4 py-3 bg-gray-50 border-b">
                            <h3 class="text-lg font-medium text-gray-900">Statistiques</h3>
                        </div>
                        <div class="p-4 space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="text-center p-3 bg-blue-50 rounded-lg">
                                    <div class="text-2xl font-bold text-blue-600">{{ leadStats.total }}</div>
                                    <div class="text-sm text-blue-600">Total</div>
                                </div>
                                <div class="text-center p-3 bg-green-50 rounded-lg">
                                    <div class="text-2xl font-bold text-green-600">{{ leadStats.paid }}</div>
                                    <div class="text-sm text-green-600">Payés</div>
                                </div>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div 
                                    class="bg-green-600 h-2 rounded-full" 
                                    :style="{ width: `${leadStats.paidPercentage}%` }"
                                ></div>
                            </div>
                            <p class="text-xs text-gray-500 text-center">
                                {{ leadStats.paidPercentage }}% des participants ont payé
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section Participants -->
            <div class="bg-white border rounded-lg shadow-sm overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <h3 class="text-lg font-medium text-gray-900">Participants ({{ leadStats.total }})</h3>
                    
                    <!-- Filtres -->
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
                            Tous ({{ leadStats.total }})
                        </button>
                        <button
                            @click="statusFilter = 'paid'"
                            :class="[
                                'px-3 py-1.5 rounded-md flex items-center gap-2 transition text-sm',
                                statusFilter === 'paid'
                                    ? 'bg-white shadow-sm text-blue-600'
                                    : 'text-gray-600 hover:bg-gray-200'
                            ]"
                        >
                            Payés ({{ leadStats.paid }})
                        </button>
                        <button
                            @click="statusFilter = 'unpaid'"
                            :class="[
                                'px-3 py-1.5 rounded-md flex items-center gap-2 transition text-sm',
                                statusFilter === 'unpaid'
                                    ? 'bg-white shadow-sm text-blue-600'
                                    : 'text-gray-600 hover:bg-gray-200'
                            ]"
                        >
                            Non payés ({{ leadStats.unpaid }})
                        </button>
                    </div>
                </div>

                <div class="p-4">
                    <div v-if="filteredLeads.length === 0" class="text-center py-8 text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <p>Aucun participant trouvé</p>
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Participant</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attentes</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut paiement</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date paiement</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="lead in filteredLeads" :key="lead.id" class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ lead.first_name }} {{ lead.last_name }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ lead.email }}</div>
                                        <div v-if="lead.phone" class="text-sm text-gray-500">{{ lead.phone }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div v-if="lead.pivot?.attentes" class="text-sm text-gray-600 max-w-xs truncate" :title="lead.pivot.attentes">
                                            {{ lead.pivot.attentes }}
                                        </div>
                                        <div v-else class="text-sm text-gray-400 italic">—</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <select
                                            :value="lead.pivot?.status || 'unpaid'"
                                            @change="updateLeadStatus(lead.id, ($event.target as HTMLSelectElement).value)"
                                            class="text-sm border border-gray-300 rounded-md px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        >
                                            <option value="unpaid">Non payé</option>
                                            <option value="paid">Payé</option>
                                        </select>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ lead.pivot?.paid_at ? formatDate(lead.pivot.paid_at) : '—' }}
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