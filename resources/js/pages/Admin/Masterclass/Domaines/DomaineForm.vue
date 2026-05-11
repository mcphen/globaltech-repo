<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';
import { ref } from 'vue';

interface Domaine {
    id: number;
    icon: string;
    color: string;
    titre: string;
    description: string | null;
    niveau: string | null;
    duree: string | null;
    modules: string[] | null;
    sort_order: number;
    is_active: boolean;
}

const props = defineProps<{ domaine?: Domaine }>();
const isEdit = !!props.domaine;

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Domaines', href: route('admin.masterclass.domaines.index') },
    { title: isEdit ? 'Modifier' : 'Nouveau', href: '#' },
];

const form = useForm({
    icon:        props.domaine?.icon        ?? 'bi-star',
    color:       props.domaine?.color       ?? '#E8A020',
    titre:       props.domaine?.titre       ?? '',
    description: props.domaine?.description ?? '',
    niveau:      props.domaine?.niveau      ?? '',
    duree:       props.domaine?.duree       ?? '',
    modules:     props.domaine?.modules     ?? [] as string[],
    sort_order:  props.domaine?.sort_order  ?? 0,
    is_active:   props.domaine?.is_active   ?? true,
});

const newModule = ref('');

function addModule() {
    const val = newModule.value.trim();
    if (val) { form.modules.push(val); newModule.value = ''; }
}

function removeModule(i: number) {
    form.modules.splice(i, 1);
}

const isSubmitting = ref(false);

function submit() {
    isSubmitting.value = true;
    if (isEdit) {
        form.put(route('admin.masterclass.domaines.update', props.domaine!.id), {
            onFinish: () => { isSubmitting.value = false; },
        });
    } else {
        form.post(route('admin.masterclass.domaines.store'), {
            onFinish: () => { isSubmitting.value = false; },
        });
    }
}
</script>

<template>
    <Head :title="isEdit ? 'Modifier le domaine' : 'Nouveau domaine'" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm">
            <div class="border-b pb-6">
                <h2 class="text-2xl font-bold text-gray-800">{{ isEdit ? 'Modifier le domaine' : 'Nouveau domaine' }}</h2>
            </div>

            <form @submit.prevent="submit" class="space-y-6 max-w-2xl">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Icône Bootstrap Icons <span class="text-gray-400 text-xs">(ex: bi-cpu)</span></label>
                        <div class="mt-1 flex items-center gap-2">
                            <i :class="['bi', form.icon, 'text-xl']" :style="`color: ${form.color};`"></i>
                            <input v-model="form.icon" type="text" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono text-sm" />
                        </div>
                        <div v-if="form.errors.icon" class="mt-1 text-sm text-red-600">{{ form.errors.icon }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Couleur</label>
                        <div class="mt-1 flex items-center gap-2">
                            <input v-model="form.color" type="color" class="h-10 w-16 rounded-md border-gray-300 cursor-pointer" />
                            <input v-model="form.color" type="text" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono text-sm" />
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

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Public / Niveau</label>
                        <input v-model="form.niveau" type="text" placeholder="ex: C-Level" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Durée</label>
                        <input v-model="form.duree" type="text" placeholder="ex: 5 jours" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                </div>

                <!-- Modules -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Modules du programme</label>
                    <div class="space-y-2 mb-3">
                        <div v-for="(m, i) in form.modules" :key="i" class="flex items-center gap-2 p-2 bg-gray-50 rounded-lg">
                            <i class="bi bi-check2 text-green-500 flex-shrink-0"></i>
                            <span class="flex-1 text-sm text-gray-700">{{ m }}</span>
                            <button type="button" @click="removeModule(i)" class="text-red-400 hover:text-red-600 transition">
                                <i class="bi bi-x-lg text-sm"></i>
                            </button>
                        </div>
                        <p v-if="form.modules.length === 0" class="text-sm text-gray-400 italic">Aucun module ajouté.</p>
                    </div>
                    <div class="flex gap-2">
                        <input v-model="newModule" type="text" placeholder="Ajouter un module..." @keydown.enter.prevent="addModule"
                            class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" />
                        <button type="button" @click="addModule"
                            class="px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition text-sm">
                            Ajouter
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Ordre d'affichage</label>
                        <input v-model.number="form.sort_order" type="number" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <div class="flex items-end pb-1">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" v-model="form.is_active" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" />
                            <span class="text-sm font-medium text-gray-700">Actif (visible sur le site)</span>
                        </label>
                    </div>
                </div>

                <!-- Aperçu -->
                <div class="p-4 rounded-xl border border-dashed border-gray-300 bg-gray-50">
                    <p class="text-xs text-gray-400 mb-3 uppercase tracking-wider font-semibold">Aperçu carte</p>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center" :style="`background: ${form.color}18;`">
                            <i :class="['bi', form.icon, 'text-xl']" :style="`color: ${form.color};`"></i>
                        </div>
                        <div>
                            <span class="text-xs font-bold px-2 py-0.5 rounded-full text-white" :style="`background: ${form.color};`">{{ form.niveau || 'Niveau' }}</span>
                            <div class="text-base font-bold text-gray-800 mt-1">{{ form.titre || 'Titre du domaine' }}</div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-4">
                    <Link :href="route('admin.masterclass.domaines.index')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition">
                        Annuler
                    </Link>
                    <button type="submit" :disabled="isSubmitting" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition flex items-center gap-2">
                        <svg v-if="isSubmitting" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ isSubmitting ? 'Enregistrement...' : (isEdit ? 'Mettre à jour' : 'Créer') }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
