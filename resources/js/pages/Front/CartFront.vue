<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';

defineProps({
  items: { type: Array, default: () => [] },
  totals: { type: Object, default: () => ({ count: 0, subtotal: 0, total: 0 }) },
  contactSettings: Object,
});

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
  router.post(route('cart.checkout'));
};
</script>

<template>
  <LayoutFront>
    <Head>
      <title>Panier | TONGOLO TECHs</title>
    </Head>

    <div class="bg-primary-bg-light py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-serif font-bold text-primary mb-6">Votre panier</h1>

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
                  <input type="number" min="1" :value="item.quantity" @change="(e:any)=>updateQty(item.id, parseInt(e.target.value)||1)" class="w-20 border rounded px-2 py-1" />
                  <button @click="removeItem(item.id)" class="text-red-600 hover:underline">Retirer</button>
                </div>
              </div>
            </div>
            <div class="p-4 flex justify-between">
              <button @click="clearCart" class="text-gray-600 hover:underline">Vider le panier</button>
              <Link :href="route('products')" class="text-primary hover:underline">Continuer vos achats</Link>
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
