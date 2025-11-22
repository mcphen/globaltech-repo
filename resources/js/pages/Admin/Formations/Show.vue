<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { computed } from 'vue';

interface LeadPivot {
  attentes?: string | null;
  status?: 'paid' | 'unpaid' | string | null;
  paid_at?: string | null;
}

interface LeadItem {
  id: number;
  first_name: string;
  last_name: string;
  email: string;
  phone?: string | null;
  pivot?: LeadPivot;
}

interface Formation {
  id: number;
  title: string;
  slug: string;
  description?: string | null;
  image_path?: string | null;
  attachment_path?: string | null;
  date_mode: 'single' | 'range';
  date?: string | null;
  start_time?: string | null;
  end_time?: string | null;
  start_date?: string | null;
  end_date?: string | null;
  duration_hours?: number | null;
  duration_per_day_hours?: number | null;
  price?: number | null;
  currency?: string | null;
  leads?: LeadItem[];
}

const page = usePage<{ formation: Formation, leadsCount: number }>();
const formation = computed(() => page.props.formation);
const leads = computed<LeadItem[]>(() => (page.props.formation.leads as LeadItem[]) || []);

const publicUrl = computed<string>(() => route('formations.show', formation.value?.slug || 0));

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Formations', href: route('admin.formations.index') },
  { title: formation.value?.title || 'Détails', href: route('admin.formations.show', formation.value?.slug || 0) }
];

function formatDate(date?: string | null) {
  if (!date) return '';
  const d = new Date(date);
  return d.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' });
}

async function shareFormation() {
  try {
    const url = publicUrl.value;
    const title = formation.value?.title || 'Formation';
    const text = `Découvrez la formation "${title}"`;

    if (navigator.share) {
      await navigator.share({ title, text, url });
      return;
    }

    if (navigator.clipboard && navigator.clipboard.writeText) {
      await navigator.clipboard.writeText(url);
      alert('Lien copié dans le presse-papiers.');
      return;
    }

    const textarea = document.createElement('textarea');
    textarea.value = url;
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand('copy');
    document.body.removeChild(textarea);
    alert('Lien copié dans le presse-papiers.');
  } catch (e) {
    console.error(e);
    alert('Impossible de partager le lien. Veuillez réessayer.');
  }
}
</script>

<template>
  <Head :title="`Formation — ${formation?.title || ''}`" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 bg-white">
      <div class="flex items-center justify-between">
        <h2 class="text-2xl font-semibold">{{ formation.title }}</h2>
        <div class="flex items-center gap-2">
          <button type="button" @click="shareFormation" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="M8.59 13.51l6.83 3.98"/><path d="M15.41 6.51l-6.82 3.98"/></svg>
            Partager
          </button>
          <Link :href="route('formations.show', formation.slug)" class="btn btn-primary px-4 py-2 bg-cyan-600 text-white rounded" target="_blank">Voir la page publique</Link>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-4">
          <img v-if="formation.image_path" :src="`${formation.image_path}`" alt="" class="w-full h-64 object-cover rounded" />
          <div class="prose max-w-none" v-html="formation.description"></div>
          <div v-if="formation.attachment_path">
            <a :href="`${formation.attachment_path}`" target="_blank" class="inline-flex items-center gap-2 text-primary hover:text-primary-dark font-medium">
              Télécharger la brochure
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4" />
              </svg>
            </a>
          </div>
        </div>
        <aside class="lg:col-span-1">
          <div class="rounded border p-4">
            <h3 class="font-semibold mb-3">Informations</h3>
            <ul class="text-sm space-y-2 text-gray-700">
              <li>
                <span class="font-medium">Dates:</span>
                <template v-if="formation.date_mode === 'single'">
                  Le {{ formatDate(formation.date) }}
                  <template v-if="formation.start_time && formation.end_time">
                    de {{ formation.start_time }} à {{ formation.end_time }}
                  </template>
                </template>
                <template v-else> Du {{ formatDate(formation.start_date) }} au {{ formatDate(formation.end_date) }}</template>
              </li>
              <li v-if="formation.date_mode === 'single' && formation.duration_hours">
                <span class="font-medium">Durée:</span> {{ formation.duration_hours }} h
              </li>
              <li v-if="formation.date_mode === 'range' && formation.duration_per_day_hours">
                <span class="font-medium">Durée/jour:</span> {{ formation.duration_per_day_hours }} h
              </li>
              <li v-if="formation.price">
                <span class="font-medium">Tarif:</span>
                {{ Number(formation.price).toLocaleString('fr-FR', { minimumFractionDigits: 0, maximumFractionDigits: 2 }) }} {{ formation.currency || 'FCFA' }}
              </li>
              <li>
                <span class="font-medium">Participants:</span> {{ leads.length }}
              </li>
            </ul>
          </div>
        </aside>
      </div>

      <div class="mt-6">
        <h3 class="text-xl font-semibold mb-3">Participants</h3>
        <div v-if="leads.length === 0" class="text-gray-600">Aucun participant pour le moment.</div>
        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Téléphone</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attentes</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payé le</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="lead in leads" :key="lead.id">
                <td class="px-4 py-2">{{ lead.first_name }} {{ lead.last_name }}</td>
                <td class="px-4 py-2 text-sm text-gray-700">{{ lead.email }}</td>
                <td class="px-4 py-2 text-sm text-gray-700">{{ lead.phone || '—' }}</td>
                <td class="px-4 py-2 text-sm text-gray-700 max-w-[300px] truncate" :title="lead.pivot?.attentes || ''">{{ lead.pivot?.attentes || '—' }}</td>
                <td class="px-4 py-2">
                  <span :class="['px-2 py-1 rounded text-xs', (lead.pivot?.status === 'paid') ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800']">
                    {{ (lead.pivot?.status === 'paid') ? 'Payé' : 'Non payé' }}
                  </span>
                </td>
                <td class="px-4 py-2 text-sm text-gray-700">{{ lead.pivot?.paid_at ? formatDate(lead.pivot?.paid_at) : '—' }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
.prose :where(img, video) { max-width: 100%; border-radius: 0.5rem; }
</style>
