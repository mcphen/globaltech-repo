<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';
import { ref } from 'vue';

interface Certification { id: number; titre: string; badge_icon: string; couleur: string; description: string | null; sort_order: number; is_active: boolean; }

const props = defineProps<{ certifications: Certification[]; flash?: { success?: string; error?: string } }>();

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Masterclass', href: route('admin.masterclass.page.edit') },
    { title: 'Certifications', href: route('admin.masterclass.certifications.index') },
];

const toDelete = ref<Certification | null>(null);
function doDelete() {
    if (toDelete.value) router.delete(route('admin.masterclass.certifications.destroy', toDelete.value.id));
    toDelete.value = null;
}
</script>

<template>
    <Head title="Masterclass — Certifications" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 border-b pb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Certifications & Reconnaissance</h2>
                    <p class="text-gray-500 mt-1">Certifications délivrées aux participants de la Masterclass</p>
                </div>
                <Link :href="route('admin.masterclass.certifications.create')"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Nouvelle certification
                </Link>
            </div>
            <div v-if="props.flash?.success" class="bg-green-50 border-l-4 border-green-500 p-4 rounded-md"><p class="text-green-700">{{ props.flash.success }}</p></div>
            <div class="grid md:grid-cols-2 gap-4">
                <div v-for="c in props.certifications" :key="c.id"
                    class="group p-5 rounded-2xl border-2 border-gray-100 hover:border-blue-100 transition-all">
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center flex-shrink-0"
                            :style="`background: ${c.couleur}18; border: 2px solid ${c.couleur}30;`">
                            <i :class="['bi', c.badge_icon, 'text-xl']" :style="`color: ${c.couleur};`"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-bold text-gray-800 mb-1">{{ c.titre }}</div>
                            <p v-if="c.description" class="text-xs text-gray-500 leading-relaxed">{{ c.description }}</p>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        <span :class="c.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'" class="px-2 py-0.5 rounded-full text-xs font-semibold">{{ c.is_active ? 'Actif' : 'Inactif' }}</span>
                        <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <Link :href="route('admin.masterclass.certifications.edit', c.id)" class="text-xs px-3 py-1 bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100">Modifier</Link>
                            <button @click="toDelete = c" class="text-xs px-3 py-1 bg-red-50 text-red-700 rounded-md hover:bg-red-100">Supprimer</button>
                        </div>
                    </div>
                </div>
                <div v-if="props.certifications.length === 0" class="col-span-2 py-10 text-center text-gray-400">Aucune certification.</div>
            </div>
        </div>
    </AppLayout>
    <div v-if="toDelete" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm w-full">
            <h3 class="text-lg font-semibold mb-4 text-center">Supprimer cette certification ?</h3>
            <p class="text-center text-gray-600 mb-6"><strong>{{ toDelete.titre }}</strong></p>
            <div class="flex gap-3">
                <button @click="toDelete = null" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">Annuler</button>
                <button @click="doDelete" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Supprimer</button>
            </div>
        </div>
    </div>
</template>
