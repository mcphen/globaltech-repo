<template>
  <Head>
    <title>Paramètres du compte - TONGOLO TECH</title>
    <meta name="description" content="Modifiez vos informations de profil." />
  </Head>

  <LayoutFront>
    <!-- Header / Breadcrumb -->
    <div class="relative bg-gray-900">
      <div class="absolute inset-0 overflow-hidden">
        <img src="/images/nav-second.jpeg" alt="Bannière Paramètres" class="w-full h-full object-cover object-center opacity-40">
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
        <div class="lg:col-span-2 space-y-6">
          <!-- Informations personnelles -->
          <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Informations personnelles</h2>

            <form @submit.prevent="submitProfile" class="space-y-6">
              <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nom *</label>
                <input
                  id="name"
                  type="text"
                  v-model="profileForm.name"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-secondary focus:ring-secondary"
                  :disabled="profileForm.processing" required
                />
                <p v-if="profileForm.errors.name" class="mt-1 text-sm text-red-600">{{ profileForm.errors.name }}</p>
              </div>

              <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                <input
                  id="email"
                  type="email"
                  v-model="profileForm.email"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-secondary focus:ring-secondary"
                  :disabled="profileForm.processing" required
                />
                <p v-if="profileForm.errors.email" class="mt-1 text-sm text-red-600">{{ profileForm.errors.email }}</p>
              </div>

              <div class="flex items-center gap-3">
                <button
                  type="submit"
                  :disabled="profileForm.processing"
                  class="inline-flex items-center px-4 py-2 bg-secondary text-white rounded-md hover:bg-secondary-dark disabled:opacity-50"
                >
                  <svg v-if="profileForm.processing" class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                  </svg>
                  <span>{{ profileForm.processing ? 'Enregistrement...' : 'Enregistrer' }}</span>
                </button>
                <button
                  type="button"
                  @click="showPasswordForm = !showPasswordForm"
                  class="text-sm text-secondary hover:underline"
                >
                  {{ showPasswordForm ? 'Masquer le changement de mot de passe' : 'Modifier le mot de passe' }}
                </button>
              </div>
            </form>
          </div>

          <!-- Formulaire de changement de mot de passe -->
          <div v-if="showPasswordForm" class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Changer le mot de passe</h2>

            <form @submit.prevent="submitPassword" class="space-y-6">
              <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700">Mot de passe actuel</label>
                <input
                  id="current_password"
                  type="password"
                  v-model="passwordForm.current_password"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-secondary focus:ring-secondary"
                  :disabled="passwordForm.processing"
                />
                <p v-if="passwordForm.errors.current_password" class="mt-1 text-sm text-red-600">
                  {{ passwordForm.errors.current_password }}
                </p>
              </div>

              <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Nouveau mot de passe</label>
                <input
                  id="password"
                  type="password"
                  v-model="passwordForm.password"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-secondary focus:ring-secondary"
                  :disabled="passwordForm.processing"
                />
                <p v-if="passwordForm.errors.password" class="mt-1 text-sm text-red-600">
                  {{ passwordForm.errors.password }}
                </p>
              </div>

              <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmer le nouveau mot de passe</label>
                <input
                  id="password_confirmation"
                  type="password"
                  v-model="passwordForm.password_confirmation"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-secondary focus:ring-secondary"
                  :disabled="passwordForm.processing"
                />
              </div>

              <div class="flex items-center gap-3">
                <button
                  type="submit"
                  :disabled="passwordForm.processing"
                  class="inline-flex items-center px-4 py-2 bg-secondary text-white rounded-md hover:bg-secondary-dark disabled:opacity-50"
                >
                  <svg v-if="passwordForm.processing" class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                  </svg>
                  <span>{{ passwordForm.processing ? 'Enregistrement...' : 'Changer le mot de passe' }}</span>
                </button>
              </div>

              <!-- Messages d'erreur spécifiques -->
              <div v-if="passwordForm.errors.password || passwordForm.errors.current_password" class="mt-2 p-2 bg-red-100 text-red-800 rounded">
                <ul class="list-disc pl-4">
                  <li v-if="passwordForm.errors.current_password">{{ passwordForm.errors.current_password }}</li>
                  <li v-if="passwordForm.errors.password">{{ passwordForm.errors.password }}</li>
                </ul>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </LayoutFront>
</template>

<script setup lang="ts">
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import { useToast } from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-sugar.css';

interface User {
  id: number;
  name: string;
  email: string;
}

interface Flash {
  success?: string;
  password_success?: string;
  error?: string;
}

const page = usePage();
const auth = computed<any>(() => (page.props as any).auth);
const user = computed<User | null>(() => auth.value?.user ?? null);
const flash = computed<Flash>(() => (page.props as any).flash || {});
const $toast = useToast();

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

const passwordForm = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
});

const showPasswordForm = ref(false);

// Watch for flash messages and show toasts
watch(flash, (newFlash) => {
  if (newFlash.success) {
    $toast.success(newFlash.success, {
      position: 'top-right',
      duration: 4000,
      dismissible: true,
    });
  }

  if (newFlash.password_success) {
    $toast.success(newFlash.password_success, {
      position: 'top-right',
      duration: 4000,
      dismissible: true,
    });
  }

  if (newFlash.error) {
    $toast.error(newFlash.error, {
      position: 'top-right',
      duration: 5000,
      dismissible: true,
    });
  }
}, { immediate: true });

// Watch for form errors and show toasts
watch(() => profileForm.errors, (errors) => {
  if (Object.keys(errors).length > 0) {
    const errorMessage = Object.values(errors).join(', ');
    $toast.error(`Erreur: ${errorMessage}`, {
      position: 'top-right',
      duration: 5000,
      dismissible: true,
    });
  }
});

watch(() => passwordForm.errors, (errors) => {
  if (Object.keys(errors).length > 0) {
    const errorMessage = Object.values(errors).join(', ');
    $toast.error(`Erreur: ${errorMessage}`, {
      position: 'top-right',
      duration: 5000,
      dismissible: true,
    });
  }
});

const submitProfile = () => {
  profileForm.patch(route('front.profile.update'), {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      profileForm.reset('name', 'email');
      profileForm.name = user.value?.name ?? '';
      profileForm.email = user.value?.email ?? '';

        $toast.success('Informations mis à jour avec succès', {
        position: 'top-right',
        duration: 7000,
        dismissible: true,
      });
    },
    onError: () => {
      // Errors are handled by the watch above
    },
  });
};

const submitPassword = () => {
  passwordForm.patch(route('front.profile.password.update'), {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      passwordForm.reset();
      showPasswordForm.value = false;
      $toast.success('Mot de passe mis à jour avec succès', {
        position: 'top-right',
        duration: 7000,
        dismissible: true,
      });
    },
    onError: () => {
      // Errors are handled by the watch above
    },
  });
};

// Also handle Inertia events for success messages
router.on('success', (event) => {
  // You can also listen for specific success events here if needed
});
</script>
