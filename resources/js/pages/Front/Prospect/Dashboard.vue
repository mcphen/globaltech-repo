<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import { computed, ref, onMounted } from 'vue';
import axios from 'axios';

const page = usePage();
const auth = computed<any>(() => (page.props as any).auth);

const title = computed(() => 'Mon Tableau de bord');
const description = computed(() => "Vue d'ensemble de votre compte et de vos actions récentes.");

// Tabs state
const activeTab = ref<'rendezvous' | 'produits' | 'formations'>('rendezvous');

// Data coming from Inertia page props (fallback to empty arrays)
const appointments = computed<any[]>(() => ((page.props as any).appointments ?? []));
const productOrders = ref<any[]>([]);
const loadingProductOrders = ref(false);
const trainingOrders = ref<any[]>([]);
const loadingTrainingOrders = ref(false);

const loadProductOrders = async () => {
  try {
    loadingProductOrders.value = true;
    const { data } = await axios.get(route('prospect.product-orders'));
    productOrders.value = Array.isArray(data) ? data : [];
  } catch (e) {
    console.error('Failed to load product orders', e);
    productOrders.value = [];
  } finally {
    loadingProductOrders.value = false;
  }
};

const loadTrainingOrders = async () => {
  try {
    loadingTrainingOrders.value = true;
    const { data } = await axios.get(route('prospect.training-orders'));
    trainingOrders.value = Array.isArray(data) ? data : [];
  } catch (e) {
    console.error('Failed to load training orders', e);
    trainingOrders.value = [];
  } finally {
    loadingTrainingOrders.value = false;
  }
};

onMounted(() => {
  loadProductOrders();
  loadTrainingOrders();
});
</script>

<template>
  <LayoutFront>
    <Head>
      <title>{{ title }}</title>
      <meta name="description" :content="description" />
    </Head>

    <section class="bg-primary-bg-light py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
          <h1 class="text-3xl md:text-4xl font-serif font-bold text-primary">Bonjour{{ auth?.user ? `, ${auth.user.name}` : '' }} 👋</h1>
          <p class="text-gray-700 mt-2">Bienvenue sur votre espace TONGOLO TECH.</p>
        </div>

        <div class="flex flex-col md:flex-row gap-6">
          <!-- Left: Nav tabs -->
          <aside class="md:w-64 w-full">
            <nav class="bg-white rounded-lg shadow p-4">
              <ul class="space-y-2">
                <li>
                  <button
                    class="w-full flex items-center gap-3 px-3 py-2 rounded-md text-left"
                    :class="activeTab === 'rendezvous' ? 'bg-primary text-white' : 'hover:bg-primary-bg-light text-gray-800'"
                    @click="activeTab = 'rendezvous'"
                  >
                    <i class="bi bi-calendar-event"></i>
                    <span>Rendez-vous</span>
                  </button>
                </li>
                <li>
                  <button
                    class="w-full flex items-center gap-3 px-3 py-2 rounded-md text-left"
                    :class="activeTab === 'produits' ? 'bg-primary text-white' : 'hover:bg-primary-bg-light text-gray-800'"
                    @click="activeTab = 'produits'"
                  >
                    <i class="bi bi-bag"></i>
                    <span>Commandes produits</span>
                  </button>
                </li>
                <li>
                  <button
                    class="w-full flex items-center gap-3 px-3 py-2 rounded-md text-left"
                    :class="activeTab === 'formations' ? 'bg-primary text-white' : 'hover:bg-primary-bg-light text-gray-800'"
                    @click="activeTab = 'formations'"
                  >
                    <i class="bi bi-mortarboard"></i>
                    <span>Commandes formations</span>
                  </button>
                </li>
              </ul>
            </nav>

            <!-- Quick actions -->
            <div class="mt-6 bg-white rounded-lg shadow p-4">
              <h3 class="text-sm font-semibold text-gray-900 mb-3">Actions rapides</h3>
              <div class="flex flex-wrap gap-2">
                <Link :href="route('appointment.create')" class="px-3 py-1.5 border border-primary text-primary rounded-full hover:bg-primary hover:text-white text-sm">Prendre rendez-vous</Link>
                <Link :href="route('formations')" class="px-3 py-1.5 border border-primary text-primary rounded-full hover:bg-primary hover:text-white text-sm">Voir les formations</Link>
                <Link :href="route('products')" class="px-3 py-1.5 border border-primary text-primary rounded-full hover:bg-primary hover:text-white text-sm">Produits</Link>
              </div>
            </div>
          </aside>

          <!-- Right: Tab content -->
          <div class="flex-1">
            <div class="bg-white rounded-lg shadow p-6">
              <!-- Rendez-vous -->
              <div v-if="activeTab === 'rendezvous'">
                <div class="flex items-center justify-between mb-4">
                  <h2 class="text-xl font-semibold text-gray-900">Vos rendez-vous</h2>
                  <Link :href="route('appointment.create')" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark">Nouveau rendez-vous</Link>
                </div>
                <div v-if="appointments && appointments.length" class="divide-y">
                  <div v-for="(rdv, idx) in appointments" :key="rdv.id ?? idx" class="py-4 flex items-start justify-between">
                    <div>
                      <p class="font-medium text-gray-900">{{ rdv.title ?? 'Rendez-vous' }}</p>
                      <p class="text-sm text-gray-600 mt-1">
                        {{ rdv.date ?? rdv.scheduled_at ?? rdv.created_at }}
                        <span v-if="rdv.time || rdv.hour"> • {{ rdv.time ?? rdv.hour }}</span>
                      </p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full"
                          :class="rdv.status === 'confirmed' ? 'bg-green-100 text-green-700' : rdv.status === 'canceled' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700'">
                      {{ rdv.status ?? 'en attente' }}
                    </span>
                  </div>
                </div>
                <div v-else class="text-center py-10">
                  <i class="bi bi-calendar-x text-4xl text-gray-300"></i>
                  <p class="mt-3 text-gray-600">Aucun rendez-vous pour le moment.</p>
                  <Link :href="route('appointment.create')" class="inline-block mt-4 px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark">Prendre rendez-vous</Link>
                </div>
              </div>

              <!-- Commandes produits -->
              <div v-else-if="activeTab === 'produits'">
                <div class="flex items-center justify-between mb-4">
                  <h2 class="text-xl font-semibold text-gray-900">Vos commandes produits</h2>
                  <Link :href="route('products')" class="px-4 py-2 border border-primary text-primary rounded-md hover:bg-primary hover:text-white">Découvrir nos produits</Link>
                </div>
                <div v-if="productOrders && productOrders.length" class="divide-y">
                  <div v-for="(order, idx) in productOrders" :key="order.id ?? idx" class="py-4 flex items-start justify-between">
                    <div>
                      <p class="font-medium text-gray-900">Commande #{{ order.reference ?? order.code ?? order.id }}</p>
                      <p class="text-sm text-gray-600 mt-1">Passée le {{ order.created_at }}</p>
                    </div>
                    <div class="text-right">
                      <p class="text-sm text-gray-700">Total: {{ order.total ?? order.amount }} {{ order.currency ?? 'FCFA' }}</p>
                      <span class="text-xs px-2 py-1 rounded-full"
                            :class="order.status === 'paid' ? 'bg-green-100 text-green-700' : order.status === 'canceled' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700'">
                        {{ order.status ?? 'en attente' }}
                      </span>
                    </div>
                  </div>
                </div>
                <div v-else class="text-center py-10">
                  <i class="bi bi-bag-x text-4xl text-gray-300"></i>
                  <p class="mt-3 text-gray-600">Aucune commande produit trouvée.</p>
                  <Link :href="route('products')" class="inline-block mt-4 px-4 py-2 border border-primary text-primary rounded-md hover:bg-primary hover:text-white">Parcourir les produits</Link>
                </div>
              </div>

              <!-- Commandes formations -->
              <div v-else>
                <div class="flex items-center justify-between mb-4">
                  <h2 class="text-xl font-semibold text-gray-900">Vos commandes formations</h2>
                  <Link :href="route('formations')" class="px-4 py-2 border border-primary text-primary rounded-md hover:bg-primary hover:text-white">Voir les formations</Link>
                </div>
                <div v-if="trainingOrders && trainingOrders.length" class="divide-y">
                  <div v-for="(order, idx) in trainingOrders" :key="order.id ?? idx" class="py-4 flex items-start justify-between">
                    <div>
                      <p class="font-medium text-gray-900">Commande formation #{{ order.reference ?? order.code ?? order.id }}</p>
                      <p class="text-sm text-gray-600 mt-1">Inscrit le {{ order.created_at }}</p>
                    </div>
                    <div class="text-right">
                      <p class="text-sm text-gray-700">Total: {{ order.total ?? order.amount }} {{ order.currency ?? 'FCFA' }}</p>
                      <span class="text-xs px-2 py-1 rounded-full"
                            :class="order.status === 'paid' ? 'bg-green-100 text-green-700' : order.status === 'canceled' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700'">
                        {{ order.status ?? 'en attente' }}
                      </span>
                    </div>
                  </div>
                </div>
                <div v-else class="text-center py-10">
                  <i class="bi bi-mortarboard text-4xl text-gray-300"></i>
                  <p class="mt-3 text-gray-600">Aucune commande de formation trouvée.</p>
                  <Link :href="route('formations')" class="inline-block mt-4 px-4 py-2 border border-primary text-primary rounded-md hover:bg-primary hover:text-white">Voir les formations</Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </LayoutFront>
</template>

<style scoped>
</style>