<script setup lang="ts">
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';
import { ref } from 'vue';

interface WhyUsItem {
    id: number;
    icon: string;
    color: string;
    title: string;
    desc: string;
    sort_order: number;
    active: boolean;
}

interface Pagination {
    data: WhyUsItem[];
    current_page: number;
    last_page: number;
    total: number;
    links: Array<{ url: string | null; label: string; active: boolean }>;
}

const props = defineProps<{
    items: Pagination;
    flash?: { success?: string };
}>();

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Pourquoi nous', href: route('admin.why-us.index') },
];

const itemToDelete = ref<WhyUsItem | null>(null);
const showDeleteModal = ref(false);
const deleteForm = useForm({});

function confirmDelete(item: WhyUsItem) {
    itemToDelete.value = item;
    showDeleteModal.value = true;
}

function cancelDelete() {
    itemToDelete.value = null;
    showDeleteModal.value = false;
}

function deleteItem() {
    if (!itemToDelete.value) return;
    deleteForm.delete(route('admin.why-us.destroy', itemToDelete.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            itemToDelete.value = null;
        },
    });
}

function toggleActive(item: WhyUsItem) {
    router.patch(route('admin.why-us.update', item.id), {
        icon: item.icon,
        color: item.color,
        title: item.title,
        desc: item.desc,
        sort_order: item.sort_order,
        active: !item.active,
    }, { preserveScroll: true });
}
</script>

<template>
    <Head title="Pourquoi nous" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm">

            <!-- Header -->
            <div class="flex justify-between items-center border-b pb-4">
                <h2 class="text-2xl font-bold text-gray-800">Pourquoi nous choisir</h2>
                <Link :href="route('admin.why-us.create')"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                    <i class="bi bi-plus-lg"></i> Ajouter un argument
                </Link>
            </div>

            <!-- Flash -->
            <div v-if="flash?.success" class="bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg px-4 py-3">
                {{ flash.success }}
            </div>

            <!-- Table -->
            <div class="overflow-x-auto rounded-xl border border-gray-100">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3 text-left">Ordre</th>
                            <th class="px-4 py-3 text-left">Icône / Couleur</th>
                            <th class="px-4 py-3 text-left">Titre</th>
                            <th class="px-4 py-3 text-left">Description</th>
                            <th class="px-4 py-3 text-center">Actif</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="item in items.data" :key="item.id" class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-500 font-mono">{{ item.sort_order }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex w-8 h-8 items-center justify-center rounded-lg text-white text-sm"
                                        :style="`background: ${item.color};`">
                                        <i :class="['bi', item.icon]"></i>
                                    </span>
                                    <span class="text-xs font-mono text-gray-400">{{ item.color }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 font-semibold text-gray-800">{{ item.title }}</td>
                            <td class="px-4 py-3 text-gray-500 max-w-xs truncate">{{ item.desc }}</td>
                            <td class="px-4 py-3 text-center">
                                <button @click="toggleActive(item)"
                                    :class="item.active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400'"
                                    class="text-xs font-semibold px-2 py-1 rounded-full transition">
                                    {{ item.active ? 'Actif' : 'Inactif' }}
                                </button>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <Link :href="route('admin.why-us.edit', item.id)"
                                        class="text-blue-600 hover:text-blue-800 text-xs font-medium px-2 py-1 rounded border border-blue-200 hover:bg-blue-50 transition">
                                        Modifier
                                    </Link>
                                    <button @click="confirmDelete(item)"
                                        class="text-red-600 hover:text-red-800 text-xs font-medium px-2 py-1 rounded border border-red-200 hover:bg-red-50 transition">
                                        Supprimer
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!items.data.length">
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400 text-sm">
                                Aucun argument ajouté.
                                <Link :href="route('admin.why-us.create')" class="text-blue-600 hover:underline ml-1">Ajouter le premier</Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Delete Modal -->
            <div v-if="showDeleteModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                <div class="bg-white rounded-2xl p-6 max-w-sm w-full mx-4 shadow-2xl">
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Confirmer la suppression</h3>
                    <p class="text-sm text-gray-600 mb-6">
                        Supprimer <strong>{{ itemToDelete?.title }}</strong> ? Cette action est irréversible.
                    </p>
                    <div class="flex justify-end gap-3">
                        <button @click="cancelDelete"
                            class="px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            Annuler
                        </button>
                        <button @click="deleteItem"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition"
                            :disabled="deleteForm.processing">
                            Supprimer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
