<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

interface BreadcrumbItemType {
  title: string;
  href?: string;
}

interface OrderItem {
  id: number;
  order_id: number;
  product_id: number | null;
  title: string;
  price: string | number;
  quantity: number;
  line_total: string | number;
  data: Record<string, any> | null;
}

interface UserSnapshot {
  id: number;
  name: string;
  email: string;
}

interface Appel {
  id: number;
  user_id: number;
  user: UserSnapshot;
  called_at: string;
  type: 'entrant' | 'sortant';
  status: 'répondu' | 'non-répondu' | 'rappel-prévu';
  duration: number | null;
  notes: string;
  next_call_at: string | null;
  created_at: string;
  updated_at: string;
}

interface Order {
  id: number;
  user_id: number | null;
  user?: UserSnapshot | null;
  items: OrderItem[];
  items_count: number;
  subtotal: string | number;
  total: string | number;
  currency: string;
  status: 'pending' | 'paid' | 'canceled' | string;
  customer_name: string | null;
  customer_email: string | null;
  customer_phone: string | null;
  notes: string | null;
  invoice_number: string | null;
  invoice_date: string | null;
  invoice_path: string | null;
  created_at: string;
  updated_at: string;
  appels: Appel[];
}

const props = defineProps<{ order: Order }>();
const order = ref<Order>({ ...props.order });

// États
const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Commandes', href: route('admin.orders.index') },
  { title: `Commande #${order.value.id}` },
];

const updating = ref(false);
const error = ref<string | null>(null);
const success = ref<string | null>(null);
const loadingInvoice = ref(false);
const loadingAppels = ref(false);
const showAppelModal = ref(false);
const showAppelsList = ref(false);
const appels = ref<Appel[]>(order.value.appels || []);
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

// Helper functions
function formatCurrency(value: string | number, currency = 'EUR') {
  const num = typeof value === 'string' ? parseFloat(value) : value;
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency }).format(num || 0);
}

function formatDateTime(iso: string) {
  const d = new Date(iso);
  return new Intl.DateTimeFormat('fr-FR', {
    year: 'numeric', month: '2-digit', day: '2-digit',
    hour: '2-digit', minute: '2-digit'
  }).format(d);
}

function formatDate(iso: string) {
  const d = new Date(iso);
  return new Intl.DateTimeFormat('fr-FR', {
    year: 'numeric', month: '2-digit', day: '2-digit'
  }).format(d);
}

function getStatusPill(status: string) {
  switch (status) {
    case 'pending':
      return { text: 'En attente', cls: 'bg-yellow-100 text-yellow-800' };
    case 'paid':
      return { text: 'Payée', cls: 'bg-green-100 text-green-800' };
    case 'canceled':
      return { text: 'Annulée', cls: 'bg-red-100 text-red-800' };
    default:
      return { text: status, cls: 'bg-gray-100 text-gray-800' };
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

function formatDuration(seconds: number | null) {
  if (!seconds) return 'N/A';
  const minutes = Math.floor(seconds / 60);
  const secs = seconds % 60;
  return `${minutes}:${secs.toString().padStart(2, '0')}`;
}

// Computed
const totals = computed(() => ({
  items: order.value.items?.length || 0,
  subtotal: order.value.subtotal,
  total: order.value.total,
  currency: order.value.currency,
}));

const hasInvoice = computed(() => {
  return order.value.invoice_number && order.value.invoice_path;
});

// Functions
async function updateStatus(status: 'pending' | 'paid' | 'canceled') {
  if (updating.value) return;
  updating.value = true;
  error.value = null;
  success.value = null;
  try {
    const { data } = await axios.patch(route('admin.orders.update-status', order.value.id), { status });
    order.value.status = data.order.status;
    success.value = 'Statut mis à jour.';
  } catch (e: any) {
    error.value = e?.response?.data?.message || e?.message || 'Erreur lors de la mise à jour du statut';
  } finally {
    updating.value = false;
    setTimeout(() => { success.value = null; }, 2000);
  }
}

async function generateInvoice() {
  if (loadingInvoice.value) return;
  loadingInvoice.value = true;
  error.value = null;
  try {
    const { data } = await axios.post(route('admin.orders.generate-invoice', order.value.id));
    order.value.invoice_number = data.order.invoice_number;
    order.value.invoice_date = data.order.invoice_date;
    order.value.invoice_path = data.order.invoice_path;
    success.value = 'Facture générée avec succès';
  } catch (e: any) {
    error.value = e?.response?.data?.message || e?.message || 'Erreur lors de la génération de la facture';
  } finally {
    loadingInvoice.value = false;
    setTimeout(() => { success.value = null; }, 3000);
  }
}

function downloadInvoice() {
  if (!order.value.invoice_path) return;
  window.open(route('admin.orders.download-invoice', order.value.id), '_blank');
}

async function loadAppels() {
  if (loadingAppels.value) return;
  loadingAppels.value = true;
  try {
    const { data } = await axios.get(route('admin.orders.appels.index', order.value.id), {
      params: { page: appelsPage.value }
    });
    
    if (data.data.length > 0) {
      appels.value = [...appels.value, ...data.data];
      appelsPage.value++;
      appelsHasMore.value = data.next_page_url !== null;
    }
  } catch (e: any) {
    console.error('Erreur lors du chargement des appels:', e);
  } finally {
    loadingAppels.value = false;
  }
}

async function submitAppel() {
  if (!appelForm.value.notes.trim()) {
    error.value = 'Les notes sont obligatoires';
    return;
  }

  try {
    const { data } = await axios.post(route('admin.orders.appels.store', order.value.id), {
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
    await axios.delete(route('admin.orders.appels.destroy', {
      order: order.value.id,
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

// Initial load
onMounted(() => {
  if (order.value.appels && order.value.appels.length > 0) {
    appels.value = order.value.appels;
  }
});
</script>

<template>
  <Head :title="`Commande #${order.id}`" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm">
      <!-- Header avec actions -->
      <div class="flex flex-col gap-4">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
          <div>
            <h2 class="text-2xl font-bold text-gray-800">Commande #{{ order.id }}</h2>
            <div class="mt-2 flex flex-wrap items-center gap-3">
              <span :class="['px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full', getStatusPill(order.status).cls]">
                {{ getStatusPill(order.status).text }}
              </span>
              <span class="text-sm text-gray-500">Créée le {{ formatDateTime(order.created_at) }}</span>
              <span v-if="order.invoice_number" class="text-sm text-blue-600 font-medium">
                Facture: {{ order.invoice_number }}
              </span>
            </div>
          </div>
          
          <!-- Action buttons -->
          <div class="flex flex-wrap gap-2">
            <!-- Statut buttons -->
            <button
              class="inline-flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium border border-gray-300 hover:bg-gray-50 disabled:opacity-50 transition"
              :disabled="updating || order.status==='pending'"
              @click="updateStatus('pending')"
              title="Marquer en attente"
            >
              <span class="i-lucide-clock text-gray-600"></span>
              En attente
            </button>
            <button
              class="inline-flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium border border-green-300 text-green-700 hover:bg-green-50 disabled:opacity-50 transition"
              :disabled="updating || order.status==='paid'"
              @click="updateStatus('paid')"
              title="Marquer comme payée"
            >
              <span class="i-lucide-badge-check text-green-600"></span>
              Payée
            </button>
            <button
              class="inline-flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium border border-red-300 text-red-700 hover:bg-red-50 disabled:opacity-50 transition"
              :disabled="updating || order.status==='canceled'"
              @click="updateStatus('canceled')"
              title="Annuler la commande"
            >
              <span class="i-lucide-x-circle text-red-600"></span>
              Annuler
            </button>
            
            <!-- Facture button -->
            <button
              v-if="!hasInvoice"
              @click="generateInvoice"
              :disabled="loadingInvoice"
              class="inline-flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 transition"
            >
              <span v-if="loadingInvoice" class="i-lucide-loader animate-spin"></span>
              <span v-else class="i-lucide-file-text"></span>
              {{ loadingInvoice ? 'Génération...' : 'Générer facture' }}
            </button>
            
            <!-- Télécharger facture -->
            <button
              v-if="hasInvoice"
              @click="downloadInvoice"
              class="inline-flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium bg-green-600 text-white hover:bg-green-700 transition"
            >
              <span class="i-lucide-download"></span>
              Télécharger facture
            </button>
            
            <!-- Nouvel appel -->
            <button
              @click="showAppelModal = true"
              class="inline-flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium bg-purple-600 text-white hover:bg-purple-700 transition"
            >
              <span class="i-lucide-phone"></span>
              Nouvel appel
            </button>
            
            <!-- Voir les appels -->
            <button
              @click="showAppelsList = !showAppelsList"
              class="inline-flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium border border-gray-300 hover:bg-gray-50 transition"
            >
              <span class="i-lucide-history"></span>
              Historique appels ({{ appels.length }})
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
      </div>

      <!-- Historique des appels -->
      <div v-if="showAppelsList" class="bg-gray-50 border border-gray-200 rounded-lg p-4">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold text-gray-800">Historique des appels</h3>
          <button
            @click="showAppelsList = false"
            class="text-gray-500 hover:text-gray-700"
          >
            <span class="i-lucide-x"></span>
          </button>
        </div>
        
        <div v-if="appels.length === 0" class="text-center py-4 text-gray-500">
          Aucun appel enregistré pour cette commande.
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
                <span class="i-lucide-trash-2 h-4 w-4"></span>
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
          
          <button
            v-if="appelsHasMore"
            @click="loadAppels"
            :disabled="loadingAppels"
            class="w-full py-2 text-sm text-blue-600 hover:text-blue-800 disabled:opacity-50"
          >
            {{ loadingAppels ? 'Chargement...' : 'Charger plus' }}
          </button>
        </div>
      </div>

      <!-- Customer and summary -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 bg-gray-50 border border-gray-200 rounded-lg p-4">
          <h3 class="text-sm font-semibold text-gray-700 mb-3">Client</h3>
          <div class="text-sm text-gray-700">
            <div><span class="text-gray-500">Nom:</span> {{ order.customer_name || order.user?.name || '—' }}</div>
            <div><span class="text-gray-500">Email:</span> {{ order.customer_email || order.user?.email || '—' }}</div>
            <div v-if="order.customer_phone"><span class="text-gray-500">Téléphone:</span> {{ order.customer_phone }}</div>
            <div v-if="order.notes" class="mt-2">
              <span class="text-gray-500">Notes:</span>
              <p class="mt-1 whitespace-pre-wrap">{{ order.notes }}</p>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
          <h3 class="text-sm font-semibold text-gray-700 mb-3">Récapitulatif</h3>
          <div class="flex flex-col gap-2 text-sm text-gray-700">
            <div class="flex justify-between">
              <span>Articles</span>
              <span>{{ totals.items }}</span>
            </div>
            <div class="flex justify-between">
              <span>Sous-total</span>
              <span class="font-medium">{{ formatCurrency(totals.subtotal, totals.currency) }}</span>
            </div>
            <div class="flex justify-between border-t pt-2 mt-2">
              <span>Total</span>
              <span class="font-bold text-gray-900">{{ formatCurrency(totals.total, totals.currency) }}</span>
            </div>
            <div v-if="order.invoice_date" class="flex justify-between text-xs text-gray-500">
              <span>Facture du:</span>
              <span>{{ formatDate(order.invoice_date) }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Items table -->
      <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Article</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qté</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total ligne</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-if="!order.items || order.items.length === 0">
              <td colspan="4" class="px-6 py-6 text-center text-gray-500">Aucun article dans cette commande.</td>
            </tr>
            <tr v-for="it in order.items" :key="it.id" class="hover:bg-gray-50">
              <td class="px-6 py-4">
                <div class="text-sm font-medium text-gray-900">{{ it.title }}</div>
                <div class="text-xs text-gray-400" v-if="it.product_id">Produit #{{ it.product_id }}</div>
              </td>
              <td class="px-6 py-4 text-sm text-gray-700">{{ formatCurrency(it.price, order.currency) }}</td>
              <td class="px-6 py-4 text-sm text-gray-700">{{ it.quantity }}</td>
              <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ formatCurrency(it.line_total, order.currency) }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="flex justify-between">
        <Link :href="route('admin.orders.index')" class="text-sm text-gray-600 hover:text-gray-900">← Retour à la liste</Link>
      </div>
    </div>
  </AppLayout>

  <!-- Modal pour nouveau appel -->
  <div v-if="showAppelModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
      <div class="p-6">
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-xl font-semibold text-gray-900">Nouvel appel</h3>
          <button
            @click="showAppelModal = false"
            class="text-gray-400 hover:text-gray-500"
          >
            <span class="i-lucide-x h-6 w-6"></span>
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