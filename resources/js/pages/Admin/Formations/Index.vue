<script setup lang="ts">
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Formations', href: route('admin.formations.index') }
];

const page = usePage<{ formations: { data: any[], links: any[] } }>();
const formations = page.props.formations?.data || [];
const links = page.props.formations?.links || [];

function formatDate(date?: string | null) {
  if (!date) return '';
  const d = new Date(date);
  return d.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' });
}

function destroy(id: number) {
  if (confirm('Supprimer cette formation ?')) {
    router.delete(route('admin.formations.destroy', id));
  }
}
</script>

<template>
  <Head title="Formations" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 bg-white">
      <div class="flex items-center justify-between">
        <h2 class="text-2xl font-semibold">Formations</h2>
        <Link :href="route('admin.formations.create')" class="btn btn-primary px-4 py-2 bg-blue-600 text-white rounded">
          Créer une formation
        </Link>
      </div>

      <div v-if="formations.length === 0" class="text-gray-600">Aucune formation pour le moment.</div>

      <div v-else class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titre</th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durée</th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarif</th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prospects</th>
              <th class="px-4 py-2"></th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="f in formations" :key="f.id">
              <td class="px-4 py-2">
                <div class="flex items-center gap-3">
                  <img v-if="f.image_path" :src="`${f.image_path}`" alt="" class="w-10 h-10 object-cover rounded"/>
                  <div>
                    <div class="font-medium">{{ f.title }}</div>
                    <div class="text-sm text-gray-500 line-clamp-1" v-html="f.description"></div>
                  </div>
                </div>
              </td>
              <td class="px-4 py-2 text-sm text-gray-700">
                <span v-if="f.date_mode === 'single'">Le {{ formatDate(f.date) }}</span>
                <span v-else>Du {{ formatDate(f.start_date) }} au {{ formatDate(f.end_date) }}</span>
              </td>
              <td class="px-4 py-2 text-sm text-gray-700">
                <span v-if="f.date_mode === 'single'">{{ f.duration_hours }} h</span>
                <span v-else>{{ f.duration_per_day_hours }} h/jour</span>
              </td>
              <td class="px-4 py-2 text-sm text-gray-700">
                <template v-if="f.price">{{ Number(f.price).toLocaleString('fr-FR', {minimumFractionDigits: 0, maximumFractionDigits: 2}) }} {{ f.currency || 'FCFA' }}</template>
                <template v-else>—</template>
              </td>
              <td class="px-4 py-2 text-sm text-gray-700">
                {{ f.leads_count || 0 }}
              </td>
              <td class="px-4 py-2 text-right flex items-center justify-end gap-2 flex-wrap">
                <Link :href="route('admin.formations.show', f.id)" class="btn btn-sm btn-primary px-4 py-2 bg-blue-600 text-white rounded">Détails</Link>
                <Link :href="route('admin.formations.edit', f.id)" class="btn btn-sm px-4 py-2 bg-yellow-500 text-white rounded">Modifier</Link>
                <Link :href="route('formations.show', f.slug)" class="btn btn-sm btn-outline-info px-4 py-2 bg-cyan-500 text-white rounded" target="_blank">Voir public</Link>
                <button @click="destroy(f.id)" class="btn btn-sm btn-danger px-4 py-2 bg-red-600 text-white rounded m-1">Supprimer</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination (basic) -->
      <div v-if="links.length" class="mt-4 flex gap-2 flex-wrap">
        <Link v-for="(l, idx) in links" :key="idx" :href="l.url || ''" :class="['btn px-3 py-1 rounded border', l.active ? 'bg-blue-600 text-white' : 'bg-white']" preserve-scroll>
          <span v-html="l.label"></span>
        </Link>
      </div>
    </div>
  </AppLayout>
</template>
