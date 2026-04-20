<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';

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

const { items = [], totals = { count: 0, subtotal: 0, total: 0 } } = defineProps<{
  items?: CartItem[];
  totals?: Totals;
  contactSettings?: Record<string, unknown>;
}>();

const formatPrice = (price: number) => new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(price);

const updateQty = (id: number, quantity: number) => {
  router.post(route('cart.update', { id }), { quantity }, { preserveScroll: true });
};

const removeItem = (id: number) => {
  router.post(route('cart.remove', { id }), {}, { preserveScroll: true });
};

const clearCart = () => {
  router.post(route('cart.clear'));
};

const checkout = () => {
  router.visit(route('cart.checkout'));
};
</script>

<template>
  <LayoutFront>
    <Head>
      <title>Panier | GlobalTECH EDUCATION Africa</title>
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
          Votre panier
        </h1>
        <p class="text-lg text-white/95 max-w-3xl mx-auto leading-relaxed drop-shadow-md">
          Retrouvez ici les produits ajoutés à votre commande.
        </p>
      </div>

      <div class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-yellow-400/50 to-transparent"></div>
    </div>

    <div class=" py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div v-if="items.length === 0" class="bg-white p-8 rounded-lg shadow text-center">
          <p class="text-gray-600 mb-4">Votre panier est vide.</p>
          <Link :href="route('products')" class="inline-block px-6 py-3 bg-primary text-white rounded-full hover:bg-primary-dark">Continuer vos achats</Link>
        </div>

        <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Items -->
          <div class="lg:col-span-2 bg-white rounded-lg shadow">
            <div class="divide-y">
              <div v-for="item in items" :key="item.id" class="p-4 flex items-center gap-4">
                <img v-if="item.image_url" :src="item.image_url" :alt="item.title" class="w-20 h-20 object-cover rounded" />
                <div class="flex-1">
                  <h3 class="font-medium text-gray-900">{{ item.title }}</h3>
                  <p class="text-primary font-semibold">{{ formatPrice(item.price) }}</p>
                </div>
                <div class="flex items-center gap-2">
                  <div class="inline-flex items-center border rounded">
                    <button
                      @click="updateQty(item.id, item.quantity - 1)"
                      :disabled="item.quantity <= 1"
                      class="px-2 py-1 text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
                      aria-label="Diminuer la quantité"
                    >
                      -
                    </button>
                    <span class="px-3 py-1 min-w-8 text-center select-none">{{ item.quantity }}</span>
                    <button
                      @click="updateQty(item.id, item.quantity + 1)"
                      class="px-2 py-1 text-gray-700"
                      aria-label="Augmenter la quantité"
                    >
                      +
                    </button>
                  </div>
                  <button @click="removeItem(item.id)" class="text-red-600 hover:text-red-700 p-2" aria-label="Retirer">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-5 h-5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>
            <div class="p-4 flex flex-wrap items-center justify-between gap-3">
              <button
                @click="clearCart"
                class="inline-flex items-center px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500/50"
              >
                Vider le panier
              </button>
              <Link
                :href="route('products')"
                class="inline-flex items-center px-4 py-2 rounded-md bg-primary text-white hover:bg-primary-dark focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50"
              >
                Continuer vos achats
              </Link>
            </div>
          </div>

          <!-- Summary -->
          <div class="bg-white rounded-lg shadow p-6 h-fit">
            <h2 class="text-lg font-semibold mb-4">Résumé</h2>
            <div class="flex justify-between py-2 border-b">
              <span>Sous-total</span>
              <span>{{ formatPrice(totals.subtotal) }}</span>
            </div>
            <div class="flex justify-between py-2 font-bold text-primary">
              <span>Total</span>
              <span>{{ formatPrice(totals.total) }}</span>
            </div>
            <button @click="checkout" class="w-full mt-6 px-6 py-3 bg-primary text-white rounded-md hover:bg-primary-dark">Valider la commande</button>
          </div>
        </div>
      </div>
    </div>
  </LayoutFront>
</template>

<style scoped>
</style>
