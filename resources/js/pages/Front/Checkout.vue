<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import { ref } from 'vue';

type CartItem = {
  id: number;
  title: string;
  price: number;
  quantity: number;
  image_url?: string | null;
};

type Totals = {
  count: number;
  subtotal: number;
  total: number;
};

type OrderSummary = {
  id: number;
  status: string;
  items_count: number;
  invoice_number?: string | null;
  invoice_date?: string | null;
  total?: number;
  currency?: string;
} | null;

const { items, totals, order } = withDefaults(defineProps<{
  items: CartItem[];
  totals: Totals;
  contactSettings?: Record<string, unknown>;
  order?: OrderSummary;
}>(), {
  items: () => [],
  totals: () => ({ count: 0, subtotal: 0, total: 0 }),
});

const isSubmitting = ref(false);

const formatPrice = (price: number) => new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(price);

const finalize = () => {
  if (isSubmitting.value) return;
  isSubmitting.value = true;
  router.post(route('cart.checkout.submit'), {}, {
    preserveScroll: true,
    onFinish: () => { isSubmitting.value = false; },
  });
};
</script>

<template>
  <LayoutFront>
    <Head>
      <title>Validation de commande | TONGOLO TECHs</title>
    </Head>

    <div class="relative bg-primary-bg-light py-16 overflow-hidden">
      <div class="absolute inset-0 z-0">
        <img
          src="https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=1920&q=80"
          alt="Technology Background"
          class="w-full h-full object-cover"
        />
        <div class="absolute inset-0 bg-gradient-to-r from-blue-900/85 via-blue-800/75 to-purple-900/85"></div>
      </div>

      <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-serif font-bold text-white mb-4 drop-shadow-lg">
          Valider la commande
        </h1>
        <p class="text-lg text-white/95 max-w-3xl mx-auto leading-relaxed drop-shadow-md">
          Veuillez vérifier les détails de votre commande avant de finaliser.
        </p>
      </div>

      <div class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-yellow-400/50 to-transparent"></div>
    </div>

    <div class="py-12">
      <div v-if="order" class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow p-8 text-center">
          <div class="text-green-600 mx-auto mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-12 h-12">
              <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-2.89a.75.75 0 1 0-1.22-.86l-4.35 6.17L8.1 12.6a.75.75 0 1 0-1.2.9l2.25 3a.75.75 0 0 0 1.2 0l5.26-7.39Z" clip-rule="evenodd" />
            </svg>
          </div>
          <h2 class="text-2xl font-semibold mb-2">Commande confirmée</h2>
          <p class="text-gray-600 mb-6">Merci pour votre commande. Vous recevrez un email de confirmation sous peu.</p>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-left">
            <div class="bg-gray-50 rounded p-4">
              <div class="text-sm text-gray-500">Numéro de facture</div>
              <div class="font-medium">{{ (order as any)?.invoice_number || '-' }}</div>
            </div>
            <div class="bg-gray-50 rounded p-4">
              <div class="text-sm text-gray-500">Date de facture</div>
              <div class="font-medium">{{ (order as any)?.invoice_date || '-' }}</div>
            </div>
            <div class="bg-gray-50 rounded p-4">
              <div class="text-sm text-gray-500">Total</div>
              <div class="font-medium">{{ formatPrice(((order as any)?.total) || 0) }}</div>
            </div>
            <div class="bg-gray-50 rounded p-4">
              <div class="text-sm text-gray-500">Statut</div>
              <div class="font-medium capitalize">{{ (order as any)?.status }}</div>
            </div>
          </div>
          <div class="mt-8 flex flex-wrap justify-center gap-3">
            <Link :href="route('products')" class="px-6 py-3 bg-primary text-white rounded-md hover:bg-primary-dark">Continuer vos achats</Link>
            <Link :href="route('prospect.product-orders')" class="px-6 py-3 bg-white border rounded-md hover:bg-gray-50">Mes commandes</Link>
          </div>
        </div>
      </div>
      <div v-else class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Récapitulatif des articles -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow">
          <div class="divide-y">
            <div v-for="item in items" :key="(item as any).id" class="p-4 flex items-center gap-4">
              <img v-if="(item as any).image_url" :src="(item as any).image_url" :alt="(item as any).title" class="w-20 h-20 object-cover rounded" />
              <div class="flex-1">
                <h3 class="font-medium text-gray-900">{{ (item as any).title }}</h3>
                <p class="text-sm text-gray-500">Quantité: {{ (item as any).quantity }}</p>
              </div>
              <div class="text-primary font-semibold">
                {{ formatPrice((item as any).price * (item as any).quantity) }}
              </div>
            </div>
          </div>
          <div class="p-4 flex flex-wrap items-center justify-between gap-3">
            <Link :href="route('cart.index')" class="inline-flex items-center px-4 py-2 rounded-md bg-white border text-gray-700 hover:bg-gray-50">
              Retour au panier
            </Link>
          </div>
        </div>

        <!-- Résumé -->
        <div class="bg-white rounded-lg shadow p-6 h-fit">
          <h2 class="text-lg font-semibold mb-4">Résumé</h2>
          <div class="flex justify-between py-2 border-b">
            <span>Sous-total</span>
            <span>{{ formatPrice((totals as any).subtotal) }}</span>
          </div>
          <div class="flex justify-between py-2 font-bold text-primary">
            <span>Total</span>
            <span>{{ formatPrice((totals as any).total) }}</span>
          </div>
          <button @click="finalize" class="w-full mt-6 px-6 py-3 bg-primary text-white rounded-md hover:bg-primary-dark">
            Finaliser la commande
          </button>
        </div>
      </div>
    </div>
  </LayoutFront>
</template>

<style scoped>
</style>
