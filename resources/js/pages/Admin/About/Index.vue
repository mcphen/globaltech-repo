<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { computed } from 'vue';

/**
 * Props
 */
const props = defineProps<{
    about: {
        content: string;
        image_path?: string;
        updated_at?: string;
    };
}>();

/**
 * Breadcrumbs
 */
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Accueil', href: '/' },
    { title: 'À propos', href: '/about' }
];


const imageUrl = computed(() => {
    if (!props.about?.image_path) return null;

    const cleanPath = props.about.image_path.replace(/^\/?storage\/+/g, '');

    return `/storage/${cleanPath}`;
});
</script>

<template>
    <Head title="À propos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4 bg-white">
            <!-- Header -->
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-semibold">À propos</h1>
                <Link
                    :href="route('admin.about.edit')"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
                >
                    Modifier
                </Link>
            </div>

            <!-- Contenu -->
            <div
                v-if="about?.content"
                class="prose max-w-none"
                v-html="about.content"
            ></div>

            <!-- Image -->
            <div v-if="imageUrl" class="mt-4">
                <img
                    :src="imageUrl"
                    alt="Image À propos"
                    class="w-full h-auto object-cover rounded-lg border"
                >
            </div>

            <!-- Date -->
            <div
                v-if="about?.updated_at"
                class="mt-4 text-sm text-gray-500"
            >
                Dernière mise à jour :
                {{ new Date(about.updated_at).toLocaleDateString('fr-FR') }}
            </div>
        </div>
    </AppLayout>
</template>
