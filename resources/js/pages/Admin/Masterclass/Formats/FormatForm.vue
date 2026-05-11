<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';
import { ref } from 'vue';

interface Format { id: number; icon: string; titre: string; couleur: string; description: string | null; tags: string[] | null; sort_order: number; is_active: boolean; }

const props = defineProps<{ format?: Format }>();
const isEdit = !!props.format;

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Formats', href: route('admin.masterclass.formats.index') },
    { title: isEdit ? 'Modifier' : 'Nouveau', href: '#' },
];

const form = useForm({
    icon:        props.format?.icon        ?? 'bi-people-fill',
    titre:       props.format?.titre       ?? '',
    couleur:     props.format?.couleur     ?? '#0B1437',
    description: props.format?.description ?? '',
    tags:        props.format?.tags        ?? [] as string[],
    sort_order:  props.format?.sort_order  ?? 0,
    is_active:   props.format?.is_active   ?? true,
});

const newTag = ref('');
function addTag() {
    const val = newTag.value.trim();
    if (val) { form.tags.push(val); newTag.value = ''; }
}
function removeTag(i: number) { form.tags.splice(i, 1); }

const isSubmitting = ref(false);
function submit() {
    isSubmitting.value = true;
    if (isEdit) {
        form.put(route('admin.masterclass.formats.update', props.format!.id), { onFinish: () => { isSubmitting.value = false; } });
    } else {
        form.post(route('admin.masterclass.formats.store'), { onFinish: () => { isSubmitting.value = false; } });
    }
}
</script>

<template>
    <Head :title="isEdit ? 'Modifier le format' : 'Nouveau format'" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm">
            <div class="border-b pb-6">
                <h2 class="text-2xl font-bold text-gray-800">{{ isEdit ? 'Modifier le format' : 'Nouveau format' }}</h2>
            </div>
            <form @submit.prevent="submit" class="space-y-6 max-w-2xl">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Icône <span class="text-gray-400 text-xs">(Bootstrap Icons)</span></label>
                        <div class="mt-1 flex items-center gap-2">
                            <i :class="['bi', form.icon, 'text-xl']" :style="`color: ${form.couleur};`"></i>
                            <input v-model="form.icon" type="text" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono text-sm" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Couleur de fond</label>
                        <div class="mt-1 flex items-center gap-2">
                            <input v-model="form.couleur" type="color" class="h-10 w-16 rounded-md border-gray-300 cursor-pointer" />
                            <input v-model="form.couleur" type="text" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono text-sm" />
                        </div>
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
                <!-- Tags -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tags / Caractéristiques</label>
                    <div class="flex flex-wrap gap-2 mb-3">
                        <span v-for="(tag, i) in form.tags" :key="i"
                            class="flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium text-white"
                            :style="`background: ${form.couleur};`">
                            {{ tag }}
                            <button type="button" @click="removeTag(i)" class="ml-1 opacity-70 hover:opacity-100">
                                <i class="bi bi-x text-xs"></i>
                            </button>
                        </span>
                        <span v-if="form.tags.length === 0" class="text-sm text-gray-400 italic">Aucun tag.</span>
                    </div>
                    <div class="flex gap-2">
                        <input v-model="newTag" type="text" placeholder="Ajouter un tag..." @keydown.enter.prevent="addTag"
                            class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" />
                        <button type="button" @click="addTag" class="px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">Ajouter</button>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Ordre d'affichage</label>
                        <input v-model.number="form.sort_order" type="number" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div class="flex items-end pb-1">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" v-model="form.is_active" class="h-4 w-4 text-blue-600 border-gray-300 rounded" />
                            <span class="text-sm font-medium text-gray-700">Actif</span>
                        </label>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-4 pt-4">
                    <Link :href="route('admin.masterclass.formats.index')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">Annuler</Link>
                    <button type="submit" :disabled="isSubmitting" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 flex items-center gap-2">
                        {{ isSubmitting ? 'Enregistrement...' : (isEdit ? 'Mettre à jour' : 'Créer') }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
