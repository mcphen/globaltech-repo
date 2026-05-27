<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { computed } from 'vue';

const props = defineProps<{
    about: {
        content?: string;
        image_path?: string;
        updated_at?: string;
        hero_badge?: string;
        hero_title?: string;
        hero_subtitle?: string;
        history_label?: string;
        history_title?: string;
        stats?: Array<{ value: string; label: string }>;
        milestones?: Array<{ year: string; event: string }>;
        offices?: Array<{ country: string; city: string; flag: string; role: string; address: string }>;
        cta_title?: string;
        timeline_title?: string;
        offices_title?: string;
    } | null;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'À propos', href: route('admin.about.index') },
];

const imageUrl = computed(() => {
    if (!props.about?.image_path) return null;
    const cleanPath = props.about.image_path.replace(/^\/?storage\/+/g, '');
    return `/storage/${cleanPath}`;
});

const sections = computed(() => [
    { label: 'Hero badge',      value: props.about?.hero_badge    || '—' },
    { label: 'Titre hero',      value: props.about?.hero_title    || '—' },
    { label: 'Label histoire',  value: props.about?.history_label || '—' },
    { label: 'Titre histoire',  value: props.about?.history_title || '—' },
    { label: 'Titre timeline',  value: props.about?.timeline_title || '—' },
    { label: 'Titre bureaux',   value: props.about?.offices_title || '—' },
    { label: 'Titre CTA',       value: props.about?.cta_title     || '—' },
]);
</script>

<template>
    <Head title="À propos — Admin" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4 md:p-6 bg-white">

            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-semibold text-gray-800">Page « À propos »</h1>
                <div class="flex gap-3">
                    <a :href="route('about')" target="_blank"
                        class="px-4 py-2 border border-gray-300 text-gray-600 rounded hover:bg-gray-50 transition text-sm">
                        Voir en ligne ↗
                    </a>
                    <Link :href="route('admin.about.edit')"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition text-sm">
                        Modifier
                    </Link>
                </div>
            </div>

            <!-- Résumé des sections -->
            <div class="grid md:grid-cols-2 gap-4">
                <div v-for="s in sections" :key="s.label"
                    class="flex items-start gap-3 p-4 rounded-lg border border-gray-100 bg-gray-50">
                    <div class="text-xs font-semibold text-gray-500 w-32 flex-shrink-0 pt-0.5">{{ s.label }}</div>
                    <div class="text-sm text-gray-800 truncate">{{ s.value }}</div>
                </div>
            </div>

            <!-- Stats -->
            <div v-if="about?.stats?.length" class="mt-2">
                <h3 class="text-sm font-semibold text-gray-600 mb-2">Chiffres clés</h3>
                <div class="flex gap-4">
                    <div v-for="s in about.stats" :key="s.label"
                        class="px-4 py-3 rounded-lg bg-amber-50 border border-amber-100 text-center">
                        <div class="text-xl font-black text-amber-600">{{ s.value }}</div>
                        <div class="text-xs text-gray-500">{{ s.label }}</div>
                    </div>
                </div>
            </div>

            <!-- Jalons -->
            <div v-if="about?.milestones?.length" class="mt-2">
                <h3 class="text-sm font-semibold text-gray-600 mb-2">Jalons ({{ about.milestones.length }})</h3>
                <div class="flex flex-wrap gap-2">
                    <span v-for="m in about.milestones" :key="m.year"
                        class="px-3 py-1 rounded-full text-xs bg-blue-50 text-blue-700 border border-blue-100">
                        {{ m.year }} — {{ m.event }}
                    </span>
                </div>
            </div>

            <!-- Bureaux -->
            <div v-if="about?.offices?.length" class="mt-2">
                <h3 class="text-sm font-semibold text-gray-600 mb-2">Bureaux ({{ about.offices.length }})</h3>
                <div class="flex flex-wrap gap-2">
                    <span v-for="o in about.offices" :key="o.country"
                        class="px-3 py-1 rounded-full text-xs bg-gray-100 text-gray-700">
                        {{ o.flag }} {{ o.city }}
                    </span>
                </div>
            </div>

            <!-- Image -->
            <div v-if="imageUrl" class="mt-2">
                <h3 class="text-sm font-semibold text-gray-600 mb-2">Image illustrative</h3>
                <img :src="imageUrl" alt="Image À propos" class="max-w-sm h-auto object-cover rounded-lg border" />
            </div>

            <!-- Date -->
            <div v-if="about?.updated_at" class="text-xs text-gray-400">
                Dernière mise à jour : {{ new Date(about.updated_at).toLocaleDateString('fr-FR') }}
            </div>
        </div>
    </AppLayout>
</template>
