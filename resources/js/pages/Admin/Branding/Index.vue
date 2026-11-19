<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';
import { computed, ref } from 'vue';

type BrandType = 'logo' | 'favicon';

const props = defineProps<{
    branding: { logo_url: string; favicon_url: string; logo_custom: boolean; favicon_custom: boolean };
    maxMb: number;
    flash?: { success?: string | null };
}>();

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Logo & favicon', href: route('admin.branding.index') },
];

const flashSuccess = computed(() => props.flash?.success ?? null);

const items = computed(() => [
    {
        type: 'logo' as BrandType,
        title: 'Logo',
        help: "Affiché dans l'en-tête et le pied de page du site. Format conseillé : PNG ou SVG à fond transparent, en largeur (ex. 600 × 300 px).",
        accept: 'image/png,image/jpeg,image/webp,image/svg+xml',
        url: props.branding.logo_url,
        custom: props.branding.logo_custom,
    },
    {
        type: 'favicon' as BrandType,
        title: 'Favicon',
        help: "Petite icône de l'onglet du navigateur, aussi utilisée dans l'admin et sur la page de connexion. Format conseillé : image carrée PNG (512 × 512 px) ou ICO.",
        accept: 'image/png,image/x-icon,image/vnd.microsoft.icon,.ico,image/jpeg,image/webp,image/svg+xml',
        url: props.branding.favicon_url,
        custom: props.branding.favicon_custom,
    },
]);

const uploading = ref<BrandType | null>(null);
const errors = ref<Record<string, string>>({});
const inputs = ref<Record<string, HTMLInputElement | null>>({});

const pick = (type: BrandType) => inputs.value[type]?.click();

const upload = (type: BrandType, e: Event) => {
    const input = e.target as HTMLInputElement;
    const file = input.files?.[0];
    input.value = '';
    if (!file) return;

    errors.value[type] = '';
    if (file.size > props.maxMb * 1024 * 1024) {
        errors.value[type] = `Fichier trop lourd : ${props.maxMb} Mo maximum.`;
        return;
    }

    uploading.value = type;
    router.post(route('admin.branding.update', type), { image: file }, {
        forceFormData: true,
        preserveScroll: true,
        onError: (errs) => { errors.value[type] = errs.image ?? "L'envoi a échoué."; },
        onFinish: () => { uploading.value = null; },
    });
};

const reset = (type: BrandType, title: string) => {
    if (!confirm(`Rétablir le ${title.toLowerCase()} d'origine ?`)) return;
    router.delete(route('admin.branding.destroy', type), { preserveScroll: true });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Logo & favicon" />

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h1 class="text-2xl font-semibold mb-2">Logo & favicon</h1>
                    <p class="text-sm text-gray-500 mb-6">
                        Les changements s'appliquent immédiatement à tout le site. Pour le favicon, le navigateur peut garder
                        l'ancienne icône en mémoire quelques minutes : rechargez la page (Ctrl + F5) pour voir la nouvelle.
                    </p>

                    <div v-if="flashSuccess" class="mb-6 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                        {{ flashSuccess }}
                    </div>

                    <div class="grid sm:grid-cols-2 gap-6">
                        <div v-for="item in items" :key="item.type" class="border border-gray-200 rounded-lg overflow-hidden">
                            <div class="grid grid-cols-2">
                                <div class="h-40 flex items-center justify-center p-4 bg-white" title="Sur fond clair">
                                    <img :src="item.url" :alt="item.title"
                                        :class="item.type === 'favicon' ? 'h-16 w-16' : 'max-h-28 max-w-full'" class="object-contain" />
                                </div>
                                <div class="h-40 flex items-center justify-center p-4" style="background: #0B1437;" title="Sur fond sombre">
                                    <img :src="item.url" :alt="item.title"
                                        :class="item.type === 'favicon' ? 'h-16 w-16' : 'max-h-28 max-w-full'" class="object-contain" />
                                </div>
                            </div>
                            <div class="p-4 border-t border-gray-200">
                                <div class="flex items-center justify-between mb-1">
                                    <h2 class="font-semibold">{{ item.title }}</h2>
                                    <span class="text-xs" :class="item.custom ? 'text-green-600' : 'text-gray-400'">
                                        {{ item.custom ? 'Personnalisé' : "D'origine" }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mb-3">{{ item.help }} {{ maxMb }} Mo maximum.</p>
                                <input type="file" :accept="item.accept" class="hidden"
                                    :ref="(el) => (inputs[item.type] = el as HTMLInputElement | null)"
                                    @change="upload(item.type, $event)" />
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" @click="pick(item.type)" :disabled="uploading !== null"
                                        class="px-3 py-1.5 text-sm bg-primary text-white rounded-md hover:bg-primary-dark disabled:opacity-50">
                                        {{ uploading === item.type ? 'Envoi…' : 'Changer' }}
                                    </button>
                                    <button v-if="item.custom" type="button" @click="reset(item.type, item.title)" :disabled="uploading !== null"
                                        class="px-3 py-1.5 text-sm border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 disabled:opacity-50">
                                        Rétablir l'original
                                    </button>
                                </div>
                                <p v-if="errors[item.type]" class="mt-2 text-xs text-red-600">{{ errors[item.type] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
