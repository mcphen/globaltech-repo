<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';
import { ref } from 'vue';

interface Partenaire { id: number; nom: string; logo_icon: string; pays: string | null; description: string | null; sort_order: number; is_active: boolean; }

const props = defineProps<{ partenaires: Partenaire[]; flash?: { success?: string; error?: string } }>();

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Masterclass', href: route('admin.masterclass.page.edit') },
    { title: 'Partenaires', href: route('admin.masterclass.partenaires.index') },
];

const toDelete = ref<Partenaire | null>(null);
function doDelete() {
    if (toDelete.value) router.delete(route('admin.masterclass.partenaires.destroy', toDelete.value.id));
    toDelete.value = null;
}
</script>

<template>
    <Head title="Masterclass — Partenaires" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 border-b pb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Partenaires Stratégiques</h2>
                    <p class="text-gray-500 mt-1">Gérez les partenaires affichés sur la page Masterclass</p>
                </div>
                <Link :href="route('admin.masterclass.partenaires.create')"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Nouveau partenaire
                </Link>
            </div>
            <div v-if="props.flash?.success" class="bg-green-50 border-l-4 border-green-500 p-4 rounded-md"><p class="text-green-700">{{ props.flash.success }}</p></div>
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-full bg-white divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ordre</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Partenaire</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pays</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actif</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="p in props.partenaires" :key="p.id" class="hover:bg-gray-50">
                            <td class="px-4 py-4 text-sm text-gray-500">{{ p.sort_order }}</td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #0B1437, #1E3A8A);">
                                        <i :class="['bi', p.logo_icon, 'text-white text-base']"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ p.nom }}</div>
                                        <div v-if="p.description" class="text-xs text-gray-400 truncate max-w-xs">{{ p.description }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500">{{ p.pays ?? '—' }}</td>
                            <td class="px-4 py-4 text-center">
                                <span :class="p.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'" class="px-2 py-0.5 rounded-full text-xs font-semibold">{{ p.is_active ? 'Oui' : 'Non' }}</span>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <Link :href="route('admin.masterclass.partenaires.edit', p.id)" class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 text-sm">Modifier</Link>
                                    <button @click="toDelete = p" class="px-3 py-1.5 bg-red-50 text-red-700 rounded-md hover:bg-red-100 text-sm">Supprimer</button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="props.partenaires.length === 0">
                            <td colspan="5" class="px-4 py-10 text-center text-gray-500">Aucun partenaire.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
    <div v-if="toDelete" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm w-full">
            <h3 class="text-lg font-semibold mb-4 text-center">Supprimer ce partenaire ?</h3>
            <p class="text-center text-gray-600 mb-6"><strong>{{ toDelete.nom }}</strong></p>
            <div class="flex gap-3">
                <button @click="toDelete = null" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">Annuler</button>
                <button @click="doDelete" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Supprimer</button>
            </div>
        </div>
    </div>
</template>
