<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import { computed } from 'vue';

const page = usePage();
const auth = computed<any>(() => (page.props as any).auth);

const title = computed(() => 'Mon Tableau de bord');
const description = computed(() => "Vue d'ensemble de votre compte et de vos actions récentes.");
</script>

<template>
  <LayoutFront>
    <Head>
      <title>{{ title }}</title>
      <meta name="description" :content="description" />
    </Head>

    <section class="bg-primary-bg-light py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
          <div>
            <h1 class="text-3xl md:text-4xl font-serif font-bold text-primary">Bonjour{{ auth?.user ? `, ${auth.user.name}` : '' }} 👋</h1>
            <p class="text-gray-700 mt-2">Bienvenue sur votre espace NG Consulting.</p>
          </div>
        </div>

        <!-- Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <!-- Rendez-vous -->
          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
              <h2 class="text-lg font-semibold text-gray-900">Prendre rendez-vous</h2>
              <span class="text-primary text-xl bi bi-calendar-event"></span>
            </div>
            <p class="text-gray-600 mt-2">Planifiez une rencontre avec nos consultants.</p>
            <Link :href="route('appointment.create')" class="inline-block mt-4 px-4 py-2 bg-primary text-white rounded-full hover:bg-primary-dark">Nouveau rendez-vous</Link>
          </div>

          <!-- Formations -->
          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
              <h2 class="text-lg font-semibold text-gray-900">Nos formations</h2>
              <span class="text-primary text-xl bi bi-mortarboard"></span>
            </div>
            <p class="text-gray-600 mt-2">Découvrez et participez à nos formations.</p>
            <Link :href="route('formations')" class="inline-block mt-4 px-4 py-2 bg-primary text-white rounded-full hover:bg-primary-dark">Voir les formations</Link>
          </div>

          <!-- Nous contacter -->
          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
              <h2 class="text-lg font-semibold text-gray-900">Besoin d'aide ?</h2>
              <span class="text-primary text-xl bi bi-chat-dots"></span>
            </div>
            <p class="text-gray-600 mt-2">Contactez notre équipe pour toute question.</p>
            <Link :href="route('contact')" class="inline-block mt-4 px-4 py-2 bg-primary text-white rounded-full hover:bg-primary-dark">Contact</Link>
          </div>
        </div>

        <!-- Actions rapides -->
        <div class="mt-10 bg-white rounded-lg shadow p-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">Actions rapides</h2>
          <div class="flex flex-wrap gap-3">
            <Link :href="route('appointment.create')" class="px-4 py-2 border border-primary text-primary rounded-full hover:bg-primary hover:text-white">Prendre rendez-vous</Link>
            <Link :href="route('formations')" class="px-4 py-2 border border-primary text-primary rounded-full hover:bg-primary hover:text-white">Voir les formations</Link>
            <Link :href="route('products')" class="px-4 py-2 border border-primary text-primary rounded-full hover:bg-primary hover:text-white">Découvrir nos produits</Link>
            <Link :href="route('services')" class="px-4 py-2 border border-primary text-primary rounded-full hover:bg-primary hover:text-white">Nos services</Link>
          </div>
        </div>
      </div>
    </section>
  </LayoutFront>
</template>

<style scoped>
</style>
