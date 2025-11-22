<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';

interface User {
  id: number;
  name: string;
  email: string;
  created_at?: string;
}

const page = usePage();
const auth = computed<any>(() => (page.props as any).auth);
const user = computed<User | null>(() => auth.value?.user ?? null);

const breadcrumbItems = [
  { name: 'Accueil', href: route('home'), current: false },
  { name: 'Mon profil', href: route('front.profile'), current: true }
];
</script>

<template>
  <Head>
    <title>Mon profil </title>
    <meta name="description" content="Gérez les informations de votre profil NG Consulting." />
  </Head>

  <LayoutFront>
    <!-- Header / Breadcrumb -->
    <div class="relative bg-gray-900">
      <div class="absolute inset-0 overflow-hidden">
        <img src="/images/breadcrumb-bg.jpg" alt="Bannière Profil" class="w-full h-full object-cover object-center opacity-40">
        <div class="absolute inset-0 bg-gradient-to-r from-primary/50 to-primary/30"></div>
      </div>
      <div class="relative max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8 flex flex-col items-center justify-center">
        <h1 class="text-4xl md:text-5xl font-serif font-bold text-white text-center mb-4">Mon profil</h1>
        <nav class="flex" aria-label="Breadcrumb">
          <ol class="flex items-center space-x-2">
            <li v-for="(item, index) in breadcrumbItems" :key="item.name">
              <div class="flex items-center">
                <Link
                  :href="item.href"
                  :class="[
                    'text-sm font-medium',
                    item.current ? 'text-white' : 'text-gray-200 hover:text-white'
                  ]"
                  :aria-current="item.current ? 'page' : undefined"
                >
                  {{ item.name }}
                </Link>
                <svg v-if="index < breadcrumbItems.length - 1" class="mx-2 h-4 w-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
              </div>
            </li>
          </ol>
        </nav>
      </div>
    </div>

    <!-- Profile content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar card -->
        <div class="lg:col-span-1">
          <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
            <div class="flex items-center space-x-4">
              <div class="h-14 w-14 rounded-full bg-primary-bg-light flex items-center justify-center">
                <i class="bi bi-person text-2xl text-secondary"></i>
              </div>
              <div>
                <p class="text-lg font-semibold text-gray-900">{{ user?.name }}</p>
                <p class="text-sm text-gray-500">{{ user?.email }}</p>
              </div>
            </div>
            <div class="mt-6 space-y-2">
              <Link :href="route('front.profile')" class="block px-3 py-2 rounded-md bg-primary-bg-light text-primary hover:bg-primary-bg">Aperçu</Link>
              <!-- Option to go to settings profile edit (Front) -->
              <Link :href="route('front.profile.edit')" class="block px-3 py-2 rounded-md hover:bg-primary-bg-light text-gray-700">Paramètres du compte</Link>
            </div>
          </div>
        </div>

        <!-- Main card -->
        <div class="lg:col-span-2">
          <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Informations du compte</h2>
            <div v-if="user" class="space-y-4">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <p class="text-sm text-gray-500">Nom</p>
                  <p class="text-base text-gray-900">{{ user.name }}</p>
                </div>
                <div>
                  <p class="text-sm text-gray-500">Email</p>
                  <p class="text-base text-gray-900">{{ user.email }}</p>
                </div>
              </div>
              <div class="pt-4">
                <Link :href="route('front.profile.edit')" class="inline-flex items-center px-4 py-2 bg-secondary text-white rounded-md hover:bg-secondary-dark">
                  <i class="bi bi-gear mr-2"></i>
                  Gérer mon profil
                </Link>
              </div>
            </div>
            <div v-else class="text-gray-600">Vous devez être connecté pour voir cette page.</div>
          </div>
        </div>
      </div>
    </div>
  </LayoutFront>
</template>
