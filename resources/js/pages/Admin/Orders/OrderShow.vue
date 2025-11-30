<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed } from 'vue';
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
  created_at: string;
  updated_at: string;
}

const props = defineProps<{ order: Order }>();
const order = ref<Order>({ ...props.order });

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Commandes', href: route('admin.orders.index') },
  { title: `Commande #${order.value.id}` },
];

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

const updating = ref(false);
const error = ref<string | null>(null);
const success = ref<string | null>(null);

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

const totals = computed(() => ({
  items: order.value.items?.length || 0,
  subtotal: order.value.subtotal,
  total: order.value.total,
  currency: order.value.currency,
}));
</script>

<template>
  <Head :title="`Commande #${order.id}`" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm">
      <div class="flex flex-col gap-4">
        <div class="flex items-start justify-between gap-4">
          <div>
            <h2 class="text-2xl font-bold text-gray-800">Commande #{{ order.id }}</h2>
            <div class="mt-2 flex items-center gap-3">
              <span :class="['px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full', getStatusPill(order.status).cls]">
                {{ getStatusPill(order.status).text }}
              </span>
              <span class="text-sm text-gray-500">Créée le {{ formatDateTime(order.created_at) }}</span>
              <span class="text-sm text-gray-400">Dernière MAJ: {{ formatDateTime(order.updated_at) }}</span>
            </div>
          </div>
          <!-- Action icons -->
          <div class="flex items-center gap-2">
            <button
              class="inline-flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium border border-gray-300 hover:bg-gray-50 disabled:opacity-50"
              :disabled="updating || order.status==='pending'"
              @click="updateStatus('pending')"
              title="Marquer en attente"
            >
              <span class="i-lucide-clock text-gray-600"></span>
              En attente
            </button>
            <button
              class="inline-flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium border border-green-300 text-green-700 hover:bg-green-50 disabled:opacity-50"
              :disabled="updating || order.status==='paid'"
              @click="updateStatus('paid')"
              title="Marquer comme payée"
            >
              <span class="i-lucide-badge-check text-green-600"></span>
              Payée
            </button>
            <button
              class="inline-flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium border border-red-300 text-red-700 hover:bg-red-50 disabled:opacity-50"
              :disabled="updating || order.status==='canceled'"
              @click="updateStatus('canceled')"
              title="Annuler la commande"
            >
              <span class="i-lucide-x-circle text-red-600"></span>
              Annuler
            </button>
          </div>
        </div>

        <div v-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">{{ error }}</div>
        <div v-if="success" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md">{{ success }}</div>
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
</template>
