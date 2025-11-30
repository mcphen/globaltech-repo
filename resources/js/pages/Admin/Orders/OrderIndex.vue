<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed, onMounted, watch } from 'vue';
import axios from 'axios';

interface BreadcrumbItemType {
  title: string;
  href?: string;
}

interface Order {
  id: number;
  user_id: number | null;
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

interface Pagination<T> {
  current_page: number;
  data: T[];
  from: number | null;
  last_page: number;
  links: Array<{ url: string | null; label: string; active: boolean }>;
  per_page: number;
  to: number | null;
  total: number;
}

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Commandes', href: route('admin.orders.index') },
];

// State
const loading = ref(false);
const error = ref<string | null>(null);
const statusFilter = ref<'all' | 'pending' | 'paid' | 'canceled'>('all');
const perPage = ref(10);
const pagination = ref<Pagination<Order> | null>(null);
const orders = computed(() => pagination.value?.data ?? []);

async function fetchOrders(page = 1) {
  loading.value = true;
  error.value = null;
  try {
    const { data } = await axios.get(route('admin.orders.list'), {
      params: { status: statusFilter.value, page, per_page: perPage.value },
    });
    pagination.value = data as Pagination<Order>;
  } catch (e: any) {
    error.value = e?.message ?? 'Erreur lors du chargement des commandes';
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  fetchOrders();
});

watch([statusFilter, perPage], () => fetchOrders(1));

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

const stats = computed(() => {
  const all = orders.value;
  return {
    total: pagination.value?.total ?? 0,
    pending: all.filter(o => o.status === 'pending').length,
    paid: all.filter(o => o.status === 'paid').length,
    canceled: all.filter(o => o.status === 'canceled').length,
  };
});

function onPageClick(link: { url: string | null; label: string; active: boolean }) {
  if (!link.url) return;
  // Extract page from url query param
  const url = new URL(link.url, window.location.origin);
  const page = Number(url.searchParams.get('page') || '1');
  fetchOrders(page);
}
</script>

<template>
  <Head title="Gestion des commandes" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-2xl font-bold text-gray-800">Gestion des commandes</h2>
          <p class="text-gray-500 mt-1">Consultez et gérez les commandes passées sur le site</p>
        </div>
        <div class="flex items-center gap-3">
          <div class="flex bg-gray-100 rounded-md p-1">
            <button @click="statusFilter = 'all'" :class="['px-3 py-1.5 rounded-md text-sm', statusFilter==='all' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-600 hover:bg-gray-200']">Tous</button>
            <button @click="statusFilter = 'pending'" :class="['px-3 py-1.5 rounded-md text-sm', statusFilter==='pending' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-600 hover:bg-gray-200']">En attente</button>
            <button @click="statusFilter = 'paid'" :class="['px-3 py-1.5 rounded-md text-sm', statusFilter==='paid' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-600 hover:bg-gray-200']">Payées</button>
            <button @click="statusFilter = 'canceled'" :class="['px-3 py-1.5 rounded-md text-sm', statusFilter==='canceled' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-600 hover:bg-gray-200']">Annulées</button>
          </div>
          <select v-model.number="perPage" class="border rounded-md px-2 py-1 text-sm text-gray-700">
            <option :value="10">10</option>
            <option :value="20">20</option>
            <option :value="50">50</option>
          </select>
        </div>
      </div>

      <!-- Statistiques rapides -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4" v-if="pagination">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
          <p class="text-sm font-medium text-blue-600">Total commandes</p>
          <p class="text-2xl font-bold text-blue-900">{{ stats.total }}</p>
        </div>
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
          <p class="text-sm font-medium text-purple-600">En attente</p>
          <p class="text-2xl font-bold text-purple-900">{{ stats.pending }}</p>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
          <p class="text-sm font-medium text-green-600">Payées</p>
          <p class="text-2xl font-bold text-green-900">{{ stats.paid }}</p>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
          <p class="text-sm font-medium text-red-600">Annulées</p>
          <p class="text-2xl font-bold text-red-900">{{ stats.canceled }}</p>
        </div>
      </div>

      <div v-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">{{ error }}</div>

      <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Articles</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
              <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-if="loading">
              <td colspan="6" class="px-6 py-4 text-center text-gray-500">Chargement...</td>
            </tr>
            <tr v-else-if="orders.length === 0">
              <td colspan="6" class="px-6 py-6 text-center text-gray-500">Aucune commande trouvée.</td>
            </tr>
            <tr v-for="order in orders" :key="order.id" class="hover:bg-gray-50">
              <td class="px-6 py-4">
                <div class="text-sm font-medium text-gray-900">{{ order.customer_name || '—' }}</div>
                <div class="text-sm text-gray-500">{{ order.customer_email || '—' }}</div>
                <div class="text-xs text-gray-400" v-if="order.customer_phone">{{ order.customer_phone }}</div>
              </td>
              <td class="px-6 py-4 text-sm text-gray-700">{{ formatDateTime(order.created_at) }}</td>
              <td class="px-6 py-4 text-sm text-gray-700">{{ order.items_count }}</td>
              <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ formatCurrency(order.total, order.currency) }}</td>
              <td class="px-6 py-4">
                <span :class="[
                  'px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full',
                  order.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                  order.status === 'paid' ? 'bg-green-100 text-green-800' :
                  order.status === 'canceled' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'
                ]">{{ order.status }}</span>
              </td>
              <td class="px-6 py-4 text-center">
                <Link :href="route('admin.orders.show', order.id)" class="text-indigo-600 hover:text-indigo-900">Détails</Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="pagination?.links && pagination.links.length > 3" class="mt-4 flex justify-center">
        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
          <button
            v-for="(link, i) in pagination.links"
            :key="i"
            @click="onPageClick(link)"
            class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"
            :disabled="!link.url"
            :class="{
              'z-10 bg-indigo-50 border-indigo-500 text-indigo-600': link.active,
              'bg-gray-100 text-gray-500 cursor-not-allowed': !link.url,
            }"
          >
            <span v-html="link.label"></span>
          </button>
        </nav>
      </div>
    </div>
  </AppLayout>
</template>
