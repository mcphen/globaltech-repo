<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';
import { ref } from 'vue';

interface Stat { id: number; valeur: string; label: string; sort_order: number; }

const props = defineProps<{ stat?: Stat }>();
const isEdit = !!props.stat;

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Stats', href: route('admin.masterclass.stats.index') },
    { title: isEdit ? 'Modifier' : 'Nouvelle', href: '#' },
];

const form = useForm({
    valeur:     props.stat?.valeur     ?? '',
    label:      props.stat?.label      ?? '',
    sort_order: props.stat?.sort_order ?? 0,
});

const isSubmitting = ref(false);
function submit() {
    isSubmitting.value = true;
    if (isEdit) {
        form.put(route('admin.masterclass.stats.update', props.stat!.id), { onFinish: () => { isSubmitting.value = false; } });
    } else {
        form.post(route('admin.masterclass.stats.store'), { onFinish: () => { isSubmitting.value = false; } });
    }
}
</script>

<template>
    <Head :title="isEdit ? 'Modifier la stat' : 'Nouvelle stat'" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm">
            <div class="border-b pb-6">
                <h2 class="text-2xl font-bold text-gray-800">{{ isEdit ? 'Modifier la statistique' : 'Nouvelle statistique' }}</h2>
            </div>
            <form @submit.prevent="submit" class="space-y-6 max-w-lg">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Valeur <span class="text-red-500">*</span> <span class="text-gray-400 text-xs">(ex: 1 200+, 96%)</span></label>
                    <input v-model="form.valeur" type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    <div v-if="form.errors.valeur" class="mt-1 text-sm text-red-600">{{ form.errors.valeur }}</div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Label <span class="text-red-500">*</span> <span class="text-gray-400 text-xs">(ex: Cadres formés)</span></label>
                    <input v-model="form.label" type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    <div v-if="form.errors.label" class="mt-1 text-sm text-red-600">{{ form.errors.label }}</div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Ordre d'affichage</label>
                    <input v-model.number="form.sort_order" type="number" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                </div>
                <!-- Aperçu -->
                <div class="p-4 rounded-xl border border-dashed border-gray-300 bg-gray-50 text-center">
                    <div class="text-3xl font-black" style="color: #E8A020;">{{ form.valeur || '—' }}</div>
                    <div class="text-xs uppercase tracking-wider text-gray-500 mt-1">{{ form.label || 'Label' }}</div>
                </div>
                <div class="flex items-center justify-end gap-4 pt-4">
                    <Link :href="route('admin.masterclass.stats.index')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">Annuler</Link>
                    <button type="submit" :disabled="isSubmitting" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 flex items-center gap-2">
                        {{ isSubmitting ? 'Enregistrement...' : (isEdit ? 'Mettre à jour' : 'Créer') }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
