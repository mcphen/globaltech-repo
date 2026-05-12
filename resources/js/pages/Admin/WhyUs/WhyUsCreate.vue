<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Pourquoi nous', href: route('admin.why-us.index') },
    { title: 'Ajouter', href: route('admin.why-us.create') },
];

const form = useForm({
    icon: '',
    color: '#2563EB',
    title: '',
    desc: '',
    sort_order: 0,
    active: true,
});

// Bootstrap Icons suggestions for quick pick
const iconSuggestions = [
    'bi-patch-check-fill', 'bi-people-fill', 'bi-building-fill',
    'bi-globe-africa', 'bi-graph-up-arrow', 'bi-shield-fill-check',
    'bi-award-fill', 'bi-star-fill', 'bi-lightning-fill',
    'bi-briefcase-fill', 'bi-mortarboard-fill', 'bi-gear-fill',
    'bi-check-circle-fill', 'bi-clock-fill', 'bi-laptop',
];

function submit() {
    form.post(route('admin.why-us.store'), { preserveScroll: true });
}
</script>

<template>
    <Head title="Ajouter — Pourquoi nous" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm max-w-2xl">

            <div class="flex justify-between items-center border-b pb-4">
                <h2 class="text-2xl font-bold text-gray-800">Ajouter un argument</h2>
                <Link :href="route('admin.why-us.index')"
                    class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
                    <i class="bi bi-arrow-left"></i> Retour
                </Link>
            </div>

            <form @submit.prevent="submit" class="space-y-5">

                <!-- Titre -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Titre <span class="text-red-500">*</span></label>
                    <input v-model="form.title" type="text"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        placeholder="ex : Certifications reconnues" required />
                    <p v-if="form.errors.title" class="mt-1 text-xs text-red-600">{{ form.errors.title }}</p>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
                    <textarea v-model="form.desc" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none resize-none"
                        placeholder="Description courte affichée sur la page d'accueil" required></textarea>
                    <p v-if="form.errors.desc" class="mt-1 text-xs text-red-600">{{ form.errors.desc }}</p>
                </div>

                <!-- Icône + Couleur -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Icône Bootstrap <span class="text-red-500">*</span></label>
                        <input v-model="form.icon" type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            placeholder="bi-patch-check-fill" required />
                        <p v-if="form.errors.icon" class="mt-1 text-xs text-red-600">{{ form.errors.icon }}</p>
                        <!-- Quick pick -->
                        <div class="flex flex-wrap gap-1 mt-2">
                            <button v-for="ic in iconSuggestions" :key="ic" type="button"
                                @click="form.icon = ic"
                                :class="['p-1.5 rounded border text-sm transition', form.icon === ic ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-200 hover:bg-gray-50']"
                                :title="ic">
                                <i :class="['bi', ic]"></i>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Couleur <span class="text-red-500">*</span></label>
                        <div class="flex items-center gap-3">
                            <input v-model="form.color" type="color"
                                class="w-12 h-10 rounded border border-gray-300 cursor-pointer p-0.5" />
                            <input v-model="form.color" type="text"
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none font-mono text-sm"
                                placeholder="#2563EB" />
                        </div>
                        <p v-if="form.errors.color" class="mt-1 text-xs text-red-600">{{ form.errors.color }}</p>
                    </div>
                </div>

                <!-- Aperçu -->
                <div v-if="form.icon && form.title" class="p-5 rounded-xl border border-dashed border-gray-200 bg-gray-50">
                    <p class="text-xs text-gray-400 mb-3 uppercase tracking-wider font-semibold">Aperçu</p>
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center flex-shrink-0"
                            :style="`background: ${form.color}20;`">
                            <i :class="['bi', form.icon, 'text-2xl']" :style="`color: ${form.color};`"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800">{{ form.title }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ form.desc }}</p>
                        </div>
                    </div>
                </div>

                <!-- Ordre + Actif -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ordre d'affichage</label>
                        <input v-model.number="form.sort_order" type="number" min="0"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                    </div>
                    <div class="flex items-end">
                        <label class="flex items-center gap-3 cursor-pointer pb-2">
                            <div class="relative">
                                <input v-model="form.active" type="checkbox" class="sr-only" />
                                <div :class="['w-11 h-6 rounded-full transition', form.active ? 'bg-blue-600' : 'bg-gray-300']"></div>
                                <div :class="['absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform', form.active ? 'translate-x-5' : '']"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Visible sur le site</span>
                        </label>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2.5 rounded-lg transition text-sm"
                        :disabled="form.processing">
                        <i class="bi bi-check-lg mr-1"></i> Enregistrer
                    </button>
                    <Link :href="route('admin.why-us.index')"
                        class="border border-gray-300 text-gray-700 hover:bg-gray-50 font-semibold px-6 py-2.5 rounded-lg transition text-sm">
                        Annuler
                    </Link>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
