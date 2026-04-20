<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';

defineProps({
  total: { type: Number, default: 0 },
  contactSettings: Object,
});

const formatPrice = (price: number) => new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(price);
</script>

<template>
  <LayoutFront>
    <Head>
      <title>Confirmation de commande | GlobalTECH EDUCATION Africa</title>
    </Head>

    <div class="bg-primary-bg-light py-16">
      <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="bg-white p-10 rounded-lg shadow">
          <h1 class="text-3xl font-serif font-bold text-primary mb-4">Merci pour votre commande</h1>
          <p class="text-gray-700 mb-6">Votre commande a été prise en compte. Montant total: <span class="font-semibold">{{ formatPrice(total) }}</span>.</p>
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <Link :href="route('products')" class="px-6 py-3 bg-primary text-white rounded-full hover:bg-primary-dark">Continuer vos achats</Link>
            <Link :href="route('home')" class="px-6 py-3 bg-white text-primary border border-primary rounded-full hover:bg-gray-50">Retour à l'accueil</Link>
          </div>
        </div>
      </div>
    </div>
  </LayoutFront>
</template>

<style scoped>
</style>
