<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';
import { ref } from 'vue';

interface Format { id: number; icon: string; titre: string; couleur: string; description: string | null; tags: string[] | null; sort_order: number; is_active: boolean; }

const props = defineProps<{ formats: Format[]; flash?: { success?: string; error?: string } }>();

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Masterclass', href: route('admin.masterclass.page.edit') },
    { title: 'Formats', href: route('admin.masterclass.formats.index') },
];

const toDelete = ref<Format | null>(null);
function doDelete() {
    if (toDelete.value) router.delete(route('admin.masterclass.formats.destroy', toDelete.value.id));
    toDelete.value = null;
}
</script>

<template>
    <Head title="Masterclass — Formats" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 border-b pb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Formats de Formation</h2>
                    <p class="text-gray-500 mt-1">Modalités pédagogiques disponibles pour la Masterclass</p>
                </div>
                <Link :href="route('admin.masterclass.formats.create')"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Nouveau format
                </Link>
            </div>
            <div v-if="props.flash?.success" class="bg-green-50 border-l-4 border-green-500 p-4 rounded-md"><p class="text-green-700">{{ props.flash.success }}</p></div>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div v-for="f in props.formats" :key="f.id"
                    class="group relative rounded-2xl p-6 text-white overflow-hidden"
                    :style="`background: ${f.couleur};`">
                    <div class="absolute -top-6 -right-6 w-20 h-20 rounded-full opacity-10" style="background: white;"></div>
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-4" style="background: rgba(255,255,255,0.2);">
                        <i :class="['bi', f.icon, 'text-lg text-white']"></i>
                    </div>
                    <div class="font-bold mb-2">{{ f.titre }}</div>
                    <div class="flex flex-wrap gap-1 mb-4">
                        <span v-for="tag in f.tags" :key="tag" class="text-xs px-2 py-0.5 rounded-lg" style="background: rgba(255,255,255,0.2);">{{ tag }}</span>
                    </div>
                    <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <Link :href="route('admin.masterclass.formats.edit', f.id)" class="text-xs px-2 py-1 rounded-md font-semibold" style="background: rgba(255,255,255,0.2);">Modifier</Link>
                        <button @click="toDelete = f" class="text-xs px-2 py-1 rounded-md font-semibold bg-red-500/70 text-white">Supprimer</button>
                    </div>
                </div>
                <div v-if="props.formats.length === 0" class="col-span-4 py-10 text-center text-gray-400">Aucun format.</div>
            </div>
        </div>
    </AppLayout>
    <div v-if="toDelete" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm w-full">
            <h3 class="text-lg font-semibold mb-4 text-center">Supprimer ce format ?</h3>
            <p class="text-center text-gray-600 mb-6"><strong>{{ toDelete.titre }}</strong></p>
            <div class="flex gap-3">
                <button @click="toDelete = null" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-md">Annuler</button>
                <button @click="doDelete" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Supprimer</button>
            </div>
        </div>
    </div>
</template>
