<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';
import { computed, ref } from 'vue';

interface BannerPage {
    key: string;
    label: string;
    image_path: string | null;
}

const props = defineProps<{ pages: BannerPage[]; maxMb: number; flash?: { success?: string | null } }>();

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Bannières des pages', href: route('admin.page-banners.index') },
];

const flashSuccess = computed(() => props.flash?.success ?? null);

const uploading = ref<string | null>(null);
const errors = ref<Record<string, string>>({});
const inputs = ref<Record<string, HTMLInputElement | null>>({});

const defaultBg =
    'linear-gradient(135deg,#060C22 0%,#0B1437 42%,#0E2060 72%,#091830 100%)';

const previewStyle = (p: BannerPage) =>
    p.image_path
        ? `background: linear-gradient(135deg, rgba(6,12,34,0.88) 0%, rgba(11,20,55,0.72) 50%, rgba(9,24,48,0.55) 100%), url("/storage/${p.image_path}") center / cover no-repeat;`
        : `background: ${defaultBg};`;

const pick = (key: string) => inputs.value[key]?.click();

const upload = (key: string, e: Event) => {
    const input = e.target as HTMLInputElement;
    const file = input.files?.[0];
    input.value = '';
    if (!file) return;

    errors.value[key] = '';
    if (file.size > props.maxMb * 1024 * 1024) {
        errors.value[key] = `Fichier trop lourd : ${props.maxMb} Mo maximum.`;
        return;
    }

    uploading.value = key;
    router.post(route('admin.page-banners.update', key), { image: file }, {
        forceFormData: true,
        preserveScroll: true,
        onError: (errs) => { errors.value[key] = errs.image ?? "L'envoi a échoué."; },
        onFinish: () => { uploading.value = null; },
    });
};

const remove = (p: BannerPage) => {
    if (!confirm(`Retirer l'image de la bannière « ${p.label} » ? La page reprendra le fond bleu par défaut.`)) return;
    router.delete(route('admin.page-banners.destroy', p.key), { preserveScroll: true });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Bannières des pages" />

        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h1 class="text-2xl font-semibold mb-2">Bannières des pages</h1>
                    <p class="text-sm text-gray-500 mb-6">
                        Image de fond de la grande bande en haut de chaque page. Un voile sombre est ajouté automatiquement
                        pour que le texte reste lisible. Sans image, la page garde le fond bleu par défaut.
                        Format conseillé : paysage, 1920 × 800 px environ, JPG/PNG/WebP, {{ maxMb }} Mo maximum.
                    </p>

                    <div v-if="flashSuccess" class="mb-6 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                        {{ flashSuccess }}
                    </div>

                    <div class="grid sm:grid-cols-2 gap-6">
                        <div v-for="p in pages" :key="p.key" class="border border-gray-200 rounded-lg overflow-hidden">
                            <div class="h-36 flex items-end p-4" :style="previewStyle(p)">
                                <span class="text-white font-bold text-lg drop-shadow">{{ p.label }}</span>
                            </div>
                            <div class="p-4 flex flex-wrap items-center gap-2">
                                <span class="text-xs mr-auto" :class="p.image_path ? 'text-green-600' : 'text-gray-400'">
                                    {{ p.image_path ? 'Image personnalisée' : 'Fond bleu par défaut' }}
                                </span>
                                <input type="file" accept="image/jpeg,image/png,image/webp" class="hidden"
                                    :ref="(el) => (inputs[p.key] = el as HTMLInputElement | null)"
                                    @change="upload(p.key, $event)" />
                                <button type="button" @click="pick(p.key)" :disabled="uploading !== null"
                                    class="px-3 py-1.5 text-sm bg-primary text-white rounded-md hover:bg-primary-dark disabled:opacity-50">
                                    {{ uploading === p.key ? 'Envoi…' : (p.image_path ? "Changer l'image" : 'Ajouter une image') }}
                                </button>
                                <button v-if="p.image_path" type="button" @click="remove(p)" :disabled="uploading !== null"
                                    class="px-3 py-1.5 text-sm border border-red-300 text-red-600 rounded-md hover:bg-red-50 disabled:opacity-50">
                                    Retirer
                                </button>
                                <p v-if="errors[p.key]" class="w-full text-xs text-red-600">{{ errors[p.key] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
