<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';

interface User {
  id: number;
  name: string;
  email: string;
}

const page = usePage();
const auth = computed<any>(() => (page.props as any).auth);
const user = computed<User | null>(() => auth.value?.user ?? null);

const breadcrumbItems = [
  { name: 'Accueil', href: route('home'), current: false },
  { name: 'Mon profil', href: route('front.profile'), current: false },
  { name: 'Paramètres', href: route('front.profile.edit'), current: true }
];

// Forms
const profileForm = useForm({
  name: user.value?.name ?? '',
  email: user.value?.email ?? '',
});

const isSaving = ref(false);

const submitProfile = () => {
  isSaving.value = true;
  profileForm.patch(route('profile.update'), {
    preserveScroll: true,
    onFinish: () => (isSaving.value = false),
  });
};
</script>

<template>
  <Head>
    <title>Paramètres du compte - NG Consulting</title>
    <meta name="description" content="Modifiez vos informations de profil." />
  </Head>

  <LayoutFront>
    <!-- Header / Breadcrumb -->
    <div class="relative bg-gray-900">
      <div class="absolute inset-0 overflow-hidden">
        <img src="/images/breadcrumb-bg.jpg" alt="Bannière Paramètres" class="w-full h-full object-cover object-center opacity-40">
        <div class="absolute inset-0 bg-gradient-to-r from-primary/50 to-primary/30"></div>
      </div>
      <div class="relative max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8 flex flex-col items-center justify-center">
        <h1 class="text-4xl md:text-5xl font-serif font-bold text-white text-center mb-4">Paramètres du compte</h1>
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
              <Link :href="route('front.profile')" class="block px-3 py-2 rounded-md hover:bg-primary-bg-light text-gray-700">Aperçu</Link>
              <Link :href="route('front.profile.edit')" class="block px-3 py-2 rounded-md bg-primary-bg-light text-primary hover:bg-primary-bg">Paramètres</Link>
            </div>
          </div>
        </div>

        <!-- Main -->
        <div class="lg:col-span-2">
          <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Informations personnelles</h2>

            <form @submit.prevent="submitProfile" class="space-y-6">
              <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nom</label>
                <input id="name" type="text" v-model="profileForm.name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-secondary focus:ring-secondary" />
                <p v-if="profileForm.errors.name" class="mt-1 text-sm text-red-600">{{ profileForm.errors.name }}</p>
              </div>

              <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input id="email" type="email" v-model="profileForm.email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-secondary focus:ring-secondary" />
                <p v-if="profileForm.errors.email" class="mt-1 text-sm text-red-600">{{ profileForm.errors.email }}</p>
              </div>

              <div class="flex items-center gap-3">
                <button type="submit" :disabled="isSaving" class="inline-flex items-center px-4 py-2 bg-secondary text-white rounded-md hover:bg-secondary-dark disabled:opacity-50">
                  <span v-if="!isSaving">Enregistrer</span>
                  <span v-else>Sauvegarde...</span>
                </button>
                <Link :href="route('password.edit')" class="text-sm text-secondary hover:underline">Modifier le mot de passe</Link>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </LayoutFront>
</template>
