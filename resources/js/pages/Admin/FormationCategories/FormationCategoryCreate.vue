<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';
import { ref } from 'vue';

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Catégories de formations', href: route('admin.formation-categories.index') },
    { title: 'Nouvelle catégorie', href: route('admin.formation-categories.create') },
];

const form = useForm({
    name: '',
    label: '',
    slug: '',
    icon: '',
    color: '#2563EB',
    background: '#EFF6FF',
    description: '',
    is_featured: false,
    sort_order: 0,
});

const isSubmitting = ref(false);

function submit() {
    isSubmitting.value = true;
    form.post(route('admin.formation-categories.store'), {
        onFinish: () => { isSubmitting.value = false; },
    });
}
</script>

<template>
    <Head title="Nouvelle catégorie de formation" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm">
            <div class="border-b pb-6">
                <h2 class="text-2xl font-bold text-gray-800">Nouvelle catégorie de formation</h2>
                <p class="text-gray-500 mt-1">Créez un nouveau domaine de formation</p>
            </div>

            <form @submit.prevent="submit" class="space-y-6 max-w-2xl">

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nom <span class="text-red-500">*</span></label>
                    <input id="name" v-model="form.name" type="text" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</div>
                </div>

                <div>
                    <label for="label" class="block text-sm font-medium text-gray-700">
                        Label court <span class="text-gray-400 text-xs">(affiché au-dessus du titre sur la homepage)</span>
                    </label>
                    <input id="label" v-model="form.label" type="text" placeholder="ex: Programme Star"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    <div v-if="form.errors.label" class="mt-1 text-sm text-red-600">{{ form.errors.label }}</div>
                </div>

                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700">
                        Slug <span class="text-gray-400 text-xs">(généré automatiquement si vide)</span>
                    </label>
                    <input id="slug" v-model="form.slug" type="text" placeholder="ex: it"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono" />
                    <div v-if="form.errors.slug" class="mt-1 text-sm text-red-600">{{ form.errors.slug }}</div>
                </div>

                <div>
                    <label for="icon" class="block text-sm font-medium text-gray-700">
                        Icône Bootstrap Icons <span class="text-gray-400 text-xs">(ex: bi-cpu-fill)</span>
                    </label>
                    <input id="icon" v-model="form.icon" type="text" placeholder="bi-cpu-fill"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono" />
                    <div v-if="form.errors.icon" class="mt-1 text-sm text-red-600">{{ form.errors.icon }}</div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="color" class="block text-sm font-medium text-gray-700">Couleur principale</label>
                        <div class="mt-1 flex items-center gap-2">
                            <input id="color" v-model="form.color" type="color"
                                class="h-10 w-16 rounded-md border-gray-300 cursor-pointer" />
                            <input v-model="form.color" type="text" placeholder="#2563EB"
                                class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono text-sm" />
                        </div>
                        <div v-if="form.errors.color" class="mt-1 text-sm text-red-600">{{ form.errors.color }}</div>
                    </div>
                    <div>
                        <label for="background" class="block text-sm font-medium text-gray-700">Couleur de fond</label>
                        <div class="mt-1 flex items-center gap-2">
                            <input id="background" v-model="form.background" type="color"
                                class="h-10 w-16 rounded-md border-gray-300 cursor-pointer" />
                            <input v-model="form.background" type="text" placeholder="#EFF6FF"
                                class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono text-sm" />
                        </div>
                        <div v-if="form.errors.background" class="mt-1 text-sm text-red-600">{{ form.errors.background }}</div>
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea id="description" v-model="form.description" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    <div v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-700">Ordre d'affichage</label>
                        <input id="sort_order" v-model.number="form.sort_order" type="number" min="0"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                        <div v-if="form.errors.sort_order" class="mt-1 text-sm text-red-600">{{ form.errors.sort_order }}</div>
                    </div>
                    <div class="flex items-end pb-1">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" v-model="form.is_featured"
                                class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" />
                            <span class="text-sm font-medium text-gray-700">Afficher en vedette (homepage)</span>
                        </label>
                    </div>
                </div>

                <!-- Aperçu -->
                <div v-if="form.name" class="p-4 rounded-xl border border-dashed border-gray-300 bg-gray-50">
                    <p class="text-xs text-gray-400 mb-3 uppercase tracking-wider font-semibold">Aperçu carte homepage</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                            :style="`background: ${form.background};`">
                            <i v-if="form.icon" :class="['bi', form.icon, 'text-lg']" :style="`color: ${form.color};`"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold uppercase tracking-widest" :style="`color: ${form.color};`">
                                {{ form.label || form.name }}
                            </div>
                            <div class="text-sm font-bold text-gray-800">{{ form.name }}</div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-4">
                    <Link :href="route('admin.formation-categories.index')"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition">
                        Annuler
                    </Link>
                    <button type="submit" :disabled="isSubmitting"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition flex items-center gap-2">
                        <svg v-if="isSubmitting" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ isSubmitting ? 'Création...' : 'Créer la catégorie' }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
