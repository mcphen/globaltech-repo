<template>
  <Head>
    <title>Mon Profil - GlobalTECH EDUCATION Africa</title>
    <meta name="description" content="Consultez votre profil utilisateur." />
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


    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar -->
        <div class="lg:col-span-1">
          <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
            <div class="flex items-center space-x-4 mb-6">
              <div class="h-16 w-16 rounded-full bg-primary-bg-light flex items-center justify-center">
                <i class="bi bi-person text-3xl text-secondary"></i>
              </div>
              <div>
                <p class="text-lg font-semibold text-gray-900">{{ user?.name }}</p>
                <p class="text-sm text-gray-500">{{ user?.email }}</p>
              </div>
            </div>
            <div class="mt-6 space-y-2">
              <Link :href="route('front.profile')" class="block px-3 py-2 rounded-md bg-primary-bg-light text-primary hover:bg-primary-bg">Aperçu</Link>
              <Link :href="route('front.profile.edit')" class="block px-3 py-2 rounded-md hover:bg-primary-bg-light text-gray-700">Paramètres</Link>
            </div>
          </div>
        </div>

        <!-- Main -->
        <div class="lg:col-span-2">
          <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-center mb-6">
              <h2 class="text-2xl font-semibold text-gray-900">Informations du compte</h2>
              <Link
                :href="route('front.profile.edit')"
                class="inline-flex items-center px-4 py-2 bg-secondary text-white rounded-md hover:bg-secondary-dark"
              >
                <i class="bi bi-pencil mr-2"></i>
                Modifier le profil
              </Link>
            </div>

            <div class="space-y-6">
              <!-- Informations de base -->
              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Informations personnelles</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <p class="text-sm font-medium text-gray-500">Nom complet</p>
                    <p class="mt-1 text-lg text-gray-900">{{ user?.name }}</p>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-500">Adresse email</p>
                    <p class="mt-1 text-lg text-gray-900">{{ user?.email }}</p>
                  </div>
                  <!-- <div>
                    <p class="text-sm font-medium text-gray-500">Rôle</p>
                    <p class="mt-1">
                      <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        {{ userRole }}
                      </span>
                    </p>
                  </div> -->
                  <div>
                    <p class="text-sm font-medium text-gray-500">Membre depuis</p>
                    <p class="mt-1 text-lg text-gray-900">{{ formatDate(user?.created_at) }}</p>
                  </div>
                </div>
              </div>

              <!-- Sécurité -->
              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Sécurité du compte</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                  <div class="flex items-center justify-between">
                    <div>
                      <p class="font-medium text-gray-900">Mot de passe</p>
                      <p class="text-sm text-gray-500">Mettez à jour votre mot de passe régulièrement</p>
                    </div>
                    <Link
                      :href="route('front.profile.edit')"
                      class="text-secondary hover:text-secondary-dark font-medium"
                    >
                      Changer
                    </Link>
                  </div>
                </div>
              </div>


            </div>
          </div>
        </div>
      </div>
    </div>
  </LayoutFront>
</template>

<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';

interface User {
  id: number;
  name: string;
  email: string;
  role: string;
  created_at: string;
}

const page = usePage();
const auth = computed<any>(() => (page.props as any).auth);
const user = computed<User | null>(() => auth.value?.user ?? null);

// const userRole = computed(() => {
//   const role = user.value?.role;
//   const roles = {
//     'admin': 'Administrateur',
//     'user': 'Utilisateur',
//     'lead': 'Prospect'
//   };
//   return roles[role as keyof typeof roles] || role || 'Utilisateur';
// });

const formatDate = (dateString?: string) => {
  if (!dateString) return 'N/A';
  const date = new Date(dateString);
  return date.toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};
</script>
