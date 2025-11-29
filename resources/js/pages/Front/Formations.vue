<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import { ref, computed } from 'vue';

interface Formation {
  id: number;
  title: string;
  slug: string;
  description: string;
  image_path?: string | null;
  date_mode: 'single' | 'range';
  date?: string | null;
  start_date?: string | null;
  end_date?: string | null;
  duration_hours?: number | null;
  duration_per_day_hours?: number | null;
  price?: number | null;
  currency?: string | null;
  created_at: string;
  updated_at: string;
}

interface PaginatedData<T> {
  data: T[];
  links: { url: string | null; label: string; active: boolean }[];
  meta: {
    current_page: number;
    last_page: number;
    total: number;
  };
}

const props = defineProps<{
  formations: PaginatedData<Formation>;
  filters: { search?: string | null };
}>();

const searchQuery = ref(props.filters.search || '');
const isLoading = ref(false);

const metaTitle = computed(() => 'Formations | NG Consultings');
const metaDescription = computed(() => "Découvrez nos formations proposées par NG Consultings. Programme, dates et modalités d'inscription.");

const formatDate = (dateString?: string | null) => {
  if (!dateString) return '';
  const d = new Date(dateString);
  return d.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' });
};

const applyFilters = () => {
  isLoading.value = true;
  const params: Record<string, string> = {};
  if (searchQuery.value) params.search = searchQuery.value;

  router.get(route('formations'), params, {
    preserveState: true,
    preserveScroll: false,
    onFinish: () => (isLoading.value = false)
  });
};

const resetFilters = () => {
  searchQuery.value = '';
  applyFilters();
};

const breadcrumbItems = [
  { name: 'Accueil', href: '/', current: false },
  { name: 'Formations', href: '/formations', current: true }
];
</script>

<template>
  <LayoutFront>

    <Head>
      <title>{{ metaTitle }}</title>
      <meta name="description" :content="metaDescription" />
    </Head>
    <!-- En-tête de la page avec image de fond -->
    <div class="relative bg-primary-bg-light py-16 overflow-hidden">
      <!-- Image de fond avec overlay -->
      <div class="absolute inset-0 z-0">
        <img src="/images/nav-second.jpeg" alt="Technology Background"
          class="w-full h-full object-cover" />
        <!-- Overlay gradient pour améliorer la lisibilité -->
        <div class="absolute inset-0 bg-gradient-to-r from-blue-900/85 via-blue-800/75 to-purple-900/85"></div>
      </div>

      <!-- Contenu en avant-plan -->
      <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col items-center justify-center">
        <h1 class="text-4xl md:text-5xl font-serif font-bold text-white text-center mb-4 drop-shadow-lg">
          Formations
        </h1>

        <!-- Breadcrumb navigation -->
        <nav class="flex" aria-label="Breadcrumb">
          <ol class="flex items-center space-x-2">
            <li v-for="(item, index) in breadcrumbItems" :key="item.name">
              <div class="flex items-center">
                <Link :href="item.href" :class="[
                  item.current ? 'text-white font-medium' : 'text-white/80 hover:text-white',
                  'text-sm md:text-base transition-colors drop-shadow-md'
                ]">
                {{ item.name }}
                </Link>

                <!-- Séparateur, sauf pour le dernier élément -->
                <svg v-if="index !== breadcrumbItems.length - 1" class="h-5 w-5 text-white/70 mx-2 drop-shadow-md"
                  fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </div>
            </li>
          </ol>
        </nav>
      </div>

      <!-- Élément décoratif -->
      <div
        class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-yellow-400/50 to-transparent">
      </div>
    </div>

    <section class="border-b bg-white py-8">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="lg:flex lg:items-center lg:justify-between lg:space-x-4">
          <div class="mb-4 lg:mb-0 lg:max-w-md lg:flex-grow">
            <div class="relative">
              <input v-model="searchQuery" type="text" placeholder="Rechercher une formation..."
                class="focus:ring-primary w-full rounded-full border border-gray-300 px-4 py-2 focus:border-transparent focus:ring-2 focus:outline-none" />
              <button @click="applyFilters" class="hover:text-primary absolute top-1 right-1 p-1.5 text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </button>
            </div>
          </div>

          <button @click="resetFilters"
            class="hover:text-primary hover:border-primary rounded-full border border-gray-300 px-4 py-2 text-sm text-gray-600 transition-colors">
            Réinitialiser
          </button>
        </div>
      </div>
    </section>

    <div v-if="isLoading" class="flex items-center justify-center py-20">
      <div class="flex flex-col items-center">
        <div class="border-primary h-16 w-16 animate-spin rounded-full border-t-2 border-b-2"></div>
        <div class="text-primary mt-4 text-lg font-medium">Chargement des formations...</div>
      </div>
    </div>

    <section v-else class="bg-white py-12">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div v-if="formations.data.length === 0" class="py-16 text-center">
          <p class="text-xl text-gray-600">Aucune formation pour le moment.</p>
          <button @click="resetFilters"
            class="bg-primary hover:bg-primary-dark mt-4 rounded-full px-6 py-2 text-white transition-colors">Voir
            toutes les formations</button>
        </div>

        <div v-else class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
          <div v-for="f in formations.data" :key="f.slug"
            class="group flex flex-col overflow-hidden rounded-lg bg-white shadow-md transition-shadow duration-300 hover:shadow-xl">
            <div class="relative h-56 overflow-hidden">
              <img :src="f.image_path ? `${f.image_path}` : '/images/placeholder.jpg'" :alt="f.title"
                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                loading="lazy" />
              <div class="bg-primary absolute bottom-0 left-0 px-4 py-1 text-white">
                <template v-if="f.date_mode === 'single'">Le {{ formatDate(f.date) }}</template>
                <template v-else>Du {{ formatDate(f.start_date) }} au {{ formatDate(f.end_date) }}</template>
              </div>
            </div>
            <div class="flex flex-grow flex-col p-5">
              <h2
                class="group-hover:text-primary mb-1 font-serif text-xl font-semibold text-gray-800 transition-colors">
                {{ f.title
                }}</h2>
              <p v-if="f.price" class="mb-2 text-sm text-gray-700">
                <span class="font-medium">Tarif: </span>
                {{ Number(f.price).toLocaleString('fr-FR', { minimumFractionDigits: 0, maximumFractionDigits: 2 }) }} {{
                  f.currency
                || 'FCFA' }}
              </p>
              <p class="mb-4 flex-grow text-gray-600"
                v-html="(f.description || '').slice(0, 150) + (f.description && f.description.length > 150 ? '…' : '')">
              </p>
              <Link :href="route('formations.show', f.slug)"
                class="text-primary group-hover:text-primary-dark mt-auto inline-flex items-center self-start font-medium">
              Voir la formation
              <svg xmlns="http://www.w3.org/2000/svg"
                class="ml-1 h-5 w-5 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
              </svg>
              </Link>
            </div>
          </div>
        </div>

        <div v-if="formations.meta && formations.meta.last_page > 1" class="mt-12 flex justify-center">
          <nav class="flex items-center space-x-1">
            <Link v-for="link in formations.links" :key="link.label" :href="link.url || '#'"
              :class="['rounded-md border px-4 py-2 transition-colors', link.active ? 'bg-primary border-primary text-white' : link.url ? 'border-gray-300 text-gray-700 hover:bg-gray-50' : 'cursor-not-allowed border-gray-200 text-gray-400']">
            <span v-html="link.label"></span>
            </Link>
          </nav>
        </div>
      </div>
    </section>
  </LayoutFront>
</template>

<style scoped>
@keyframes spin {
  0% {
    transform: rotate(0deg);
  }

  100% {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>
