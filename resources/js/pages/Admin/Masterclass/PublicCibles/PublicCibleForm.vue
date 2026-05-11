<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';
import { ref } from 'vue';

interface Cible { id: number; icon: string; titre: string; description: string | null; sort_order: number; is_active: boolean; }

const props = defineProps<{ cible?: Cible }>();
const isEdit = !!props.cible;

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Public Cible', href: route('admin.masterclass.public-cibles.index') },
    { title: isEdit ? 'Modifier' : 'Nouveau', href: '#' },
];

const form = useForm({
    icon:        props.cible?.icon        ?? 'bi-person-badge',
    titre:       props.cible?.titre       ?? '',
    description: props.cible?.description ?? '',
    sort_order:  props.cible?.sort_order  ?? 0,
    is_active:   props.cible?.is_active   ?? true,
});

const isSubmitting = ref(false);
function submit() {
    isSubmitting.value = true;
    if (isEdit) {
        form.put(route('admin.masterclass.public-cibles.update', props.cible!.id), { onFinish: () => { isSubmitting.value = false; } });
    } else {
        form.post(route('admin.masterclass.public-cibles.store'), { onFinish: () => { isSubmitting.value = false; } });
    }
}
</script>

<template>
    <Head :title="isEdit ? 'Modifier le profil' : 'Nouveau profil'" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm">
            <div class="border-b pb-6">
                <h2 class="text-2xl font-bold text-gray-800">{{ isEdit ? 'Modifier le profil cible' : 'Nouveau profil cible' }}</h2>
            </div>
            <form @submit.prevent="submit" class="space-y-6 max-w-2xl">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Icône Bootstrap Icons <span class="text-gray-400 text-xs">(ex: bi-person-badge)</span></label>
                    <div class="mt-1 flex items-center gap-2">
                        <i :class="['bi', form.icon, 'text-xl text-gray-600']"></i>
                        <input v-model="form.icon" type="text" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono text-sm" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Titre <span class="text-red-500">*</span></label>
                    <input v-model="form.titre" type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    <div v-if="form.errors.titre" class="mt-1 text-sm text-red-600">{{ form.errors.titre }}</div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea v-model="form.description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Ordre d'affichage</label>
                        <input v-model.number="form.sort_order" type="number" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div class="flex items-end pb-1">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" v-model="form.is_active" class="h-4 w-4 text-blue-600 border-gray-300 rounded" />
                            <span class="text-sm font-medium text-gray-700">Actif (visible sur le site)</span>
                        </label>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-4 pt-4">
                    <Link :href="route('admin.masterclass.public-cibles.index')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">Annuler</Link>
                    <button type="submit" :disabled="isSubmitting" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 flex items-center gap-2">
                        {{ isSubmitting ? 'Enregistrement...' : (isEdit ? 'Mettre à jour' : 'Créer') }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
