<script setup lang="ts">
import { Head, usePage, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';

// Types for shared props we expect
interface AuthUser {
  id: number;
  name?: string | null;
  email?: string | null;
  phone?: string | null;
}

interface LeadInfo {
  id?: number;
  company?: string | null;
  consent_at?: string | null;
}

const page = usePage();
const auth = computed(() => (page.props as any).auth as { user?: AuthUser } | undefined);
const lead = computed(() => ((page.props as any).lead as LeadInfo | undefined));

const user = computed<AuthUser | undefined>(() => auth.value?.user);

const metaTitle = computed(() => 'Profil prospect | TONGOLO TECH');
const metaDescription = computed(
  () => "Consultez les informations de votre profil prospect au sein de TONGOLO TECH."
);
</script>

<template>
  <LayoutFront>
    <Head>
      <title>{{ metaTitle }}</title>
      <meta name="description" :content="metaDescription" />
    </Head>

    <!-- Bandeau titre -->
    <section class="bg-primary-bg-light py-10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl md:text-4xl font-serif font-bold text-primary">Mon profil prospect</h1>
        <p class="text-gray-700 mt-2">Retrouvez ici vos informations de compte liées à vos prises de contact et rendez-vous.</p>
      </div>
    </section>

    <section class="py-10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Carte résumé -->
        <div class="lg:col-span-1 bg-white rounded-xl shadow p-6 border border-gray-100">
          <div class="flex items-center gap-4">
            <div class="h-14 w-14 rounded-full bg-primary/10 flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-primary" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 2a5 5 0 00-3.536 8.536A7 7 0 003 17a1 1 0 001 1h12a1 1 0 001-1 7 7 0 00-3.464-6.036A5 5 0 0010 2zm-3 5a3 3 0 116 0 3 3 0 01-6 0zM5.055 16a5 5 0 019.89 0H5.055z" clip-rule="evenodd" />
              </svg>
            </div>
            <div>
              <h2 class="text-xl font-semibold text-gray-900">{{ user?.name || 'Utilisateur' }}</h2>
              <p class="text-gray-600 text-sm">Prospect TONGOLO TECH</p>
            </div>
          </div>

          <div class="mt-6 space-y-3 text-sm">
            <div class="flex items-center gap-3">
              <i class="bi bi-envelope text-primary"></i>
              <span class="text-gray-700">{{ user?.email || '—' }}</span>
            </div>
            <div class="flex items-center gap-3">
              <i class="bi bi-telephone text-primary"></i>
              <span class="text-gray-700">{{ user?.phone || '—' }}</span>
            </div>
            <div class="flex items-center gap-3">
              <i class="bi bi-building text-primary"></i>
              <span class="text-gray-700">{{ lead?.company || 'Société non renseignée' }}</span>
            </div>
            <div class="flex items-center gap-3">
              <i class="bi bi-shield-check text-primary"></i>
              <span class="text-gray-700">
                Consentement:
                <span v-if="lead?.consent_at" class="text-green-600">donné le {{ new Date(lead!.consent_at!).toLocaleString() }}</span>
                <span v-else class="text-gray-500">non renseigné</span>
              </span>
            </div>
          </div>

          <div class="mt-6 flex gap-3">
            <Link :href="route('prospect.dashboard')" class="px-4 py-2 rounded-md bg-primary text-white hover:bg-primary-dark">Tableau de bord</Link>
            <Link v-if="route().has('front.profile.edit')" :href="route('front.profile.edit')" class="px-4 py-2 rounded-md border border-primary text-primary hover:bg-primary/5">Modifier</Link>
          </div>
        </div>

        <!-- Détails -->
        <div class="lg:col-span-2 space-y-8">
          <div class="bg-white rounded-xl shadow p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations personnelles</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-xs uppercase text-gray-500">Nom complet</label>
                <p class="mt-1 text-gray-900">{{ user?.name || '—' }}</p>
              </div>
              <div>
                <label class="block text-xs uppercase text-gray-500">Email</label>
                <p class="mt-1 text-gray-900">{{ user?.email || '—' }}</p>
              </div>
              <div>
                <label class="block text-xs uppercase text-gray-500">Téléphone</label>
                <p class="mt-1 text-gray-900">{{ user?.phone || '—' }}</p>
              </div>
              <div>
                <label class="block text-xs uppercase text-gray-500">Société</label>
                <p class="mt-1 text-gray-900">{{ lead?.company || '—' }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl shadow p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Confidentialité & consentement</h3>
            <p class="text-gray-700">
              Votre consentement nous permet de communiquer avec vous dans le cadre de nos services. Vous pouvez à tout moment nous contacter pour mettre à jour vos préférences.
            </p>
            <div class="mt-4 flex items-center gap-2">
              <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium" :class="lead?.consent_at ? 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20' : 'bg-gray-50 text-gray-700 ring-1 ring-inset ring-gray-600/20'">
                <i class="bi" :class="lead?.consent_at ? 'bi-check-circle me-1' : 'bi-dash-circle me-1'"></i>
                {{ lead?.consent_at ? 'Consentement enregistré' : 'Consentement non renseigné' }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </section>
  </LayoutFront>
</template>

<style scoped>
/* Simple helpers for consistent spacing and look */
</style>
