<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';
import { computed, reactive, ref, watch } from 'vue';

interface BannerText {
    badge: string;
    title: string;
    highlight: string;
    subtitle: string;
}

interface BannerPage {
    key: string;
    label: string;
    image_path: string | null;
    meta: { overlay: number } & Partial<BannerText>;
    has_text: boolean;
    text_defaults: BannerText | null;
    text_editor: { label: string; url: string } | null;
}

type BannerForm = { overlay: number } & BannerText;

const props = defineProps<{ pages: BannerPage[]; maxMb: number; flash?: { success?: string | null } }>();

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Bannières des pages', href: route('admin.page-banners.index') },
];

const flashSuccess = computed(() => props.flash?.success ?? null);

// ── Réglages (assombrissement + texte) : un formulaire par page ─────────────
const forms = reactive<Record<string, BannerForm>>({});
// Ne recharge que les pages dont les réglages enregistrés ont changé, pour ne
// pas perdre ce qui est en cours de saisie sur les autres pages.
const lastMeta: Record<string, string> = {};
const syncForms = () => {
    for (const p of props.pages) {
        const snapshot = JSON.stringify(p.meta);
        if (forms[p.key] && lastMeta[p.key] === snapshot) continue;
        lastMeta[p.key] = snapshot;
        forms[p.key] = {
            overlay: p.meta.overlay,
            badge: p.meta.badge ?? '',
            title: p.meta.title ?? '',
            highlight: p.meta.highlight ?? '',
            subtitle: p.meta.subtitle ?? '',
        };
    }
};
syncForms();
watch(() => props.pages, syncForms);

const saving = ref<string | null>(null);
const saveErrors = ref<Record<string, Record<string, string>>>({});

const isDirty = (p: BannerPage) => {
    const f = forms[p.key];
    if (!f) return false;
    if (f.overlay !== p.meta.overlay) return true;
    if (!p.has_text) return false;
    return (['badge', 'title', 'highlight', 'subtitle'] as const).some((k) => f[k] !== (p.meta[k] ?? ''));
};

const save = (p: BannerPage) => {
    const f = forms[p.key];
    const payload: Record<string, unknown> = { overlay: f.overlay };
    if (p.has_text) Object.assign(payload, { badge: f.badge, title: f.title, highlight: f.highlight, subtitle: f.subtitle });

    saving.value = p.key;
    saveErrors.value[p.key] = {};
    router.post(route('admin.page-banners.settings', p.key), payload, {
        preserveScroll: true,
        preserveState: true,
        onError: (errs) => { saveErrors.value[p.key] = errs as Record<string, string>; },
        onFinish: () => { saving.value = null; },
    });
};

const resetText = (p: BannerPage) => {
    if (!p.text_defaults) return;
    Object.assign(forms[p.key], p.text_defaults);
};

// ── Image ───────────────────────────────────────────────────────────────────
const uploading = ref<string | null>(null);
const errors = ref<Record<string, string>>({});
const inputs = ref<Record<string, HTMLInputElement | null>>({});

const defaultBg = 'linear-gradient(135deg,#060C22 0%,#0B1437 42%,#0E2060 72%,#091830 100%)';

// Même calcul que usePageBanner() côté site
const previewStyle = (p: BannerPage) => {
    if (!p.image_path) return `background: ${defaultBg};`;
    const d = Math.max(0, Math.min(100, forms[p.key]?.overlay ?? p.meta.overlay)) / 100;
    const a = (x: number) => (d * x).toFixed(3);
    return `background: linear-gradient(135deg, rgba(6,12,34,${a(1)}) 0%, rgba(11,20,55,${a(0.82)}) 50%, rgba(9,24,48,${a(0.63)}) 100%), url("/storage/${p.image_path}") center / cover no-repeat;`;
};

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
        preserveState: true,
        onError: (errs) => { errors.value[key] = errs.image ?? "L'envoi a échoué."; },
        onFinish: () => { uploading.value = null; },
    });
};

const remove = (p: BannerPage) => {
    if (!confirm(`Retirer l'image de la bannière « ${p.label} » ? La page reprendra le fond bleu par défaut.`)) return;
    router.delete(route('admin.page-banners.destroy', p.key), { preserveScroll: true, preserveState: true });
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
                        La grande bande en haut de chaque page : image de fond, assombrissement et texte.
                        Sans image, la page garde le fond bleu par défaut.
                        Image conseillée : paysage, 1920 × 800 px environ, JPG/PNG/WebP, {{ maxMb }} Mo maximum.
                    </p>

                    <div v-if="flashSuccess" class="mb-6 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                        {{ flashSuccess }}
                    </div>

                    <div class="space-y-8">
                        <div v-for="p in pages" :key="p.key" class="border border-gray-200 rounded-lg overflow-hidden">
                            <!-- Aperçu -->
                            <div class="px-6 py-8 sm:px-8 sm:py-10" :style="previewStyle(p)">
                                <span class="block text-[10px] uppercase tracking-widest text-white/50 mb-3">Aperçu — {{ p.label }}</span>
                                <template v-if="p.has_text && forms[p.key]">
                                    <span v-if="forms[p.key].badge"
                                        class="inline-block px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest mb-3"
                                        style="background: rgba(230,57,70,0.15); color: #E63946; border: 1px solid rgba(230,57,70,0.3);">
                                        {{ forms[p.key].badge }}
                                    </span>
                                    <div class="text-2xl sm:text-3xl font-black text-white leading-tight mb-2">
                                        {{ forms[p.key].title }}<span v-if="forms[p.key].highlight" style="color: #E63946;"> {{ forms[p.key].highlight }}</span>
                                    </div>
                                    <p v-if="forms[p.key].subtitle" class="text-sm text-white/70 max-w-xl">{{ forms[p.key].subtitle }}</p>
                                </template>
                                <div v-else class="text-2xl font-black text-white">{{ p.label }}</div>
                            </div>

                            <div class="p-5 space-y-5">
                                <!-- Image -->
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-sm font-medium text-gray-700 mr-auto">
                                        Image :
                                        <span :class="p.image_path ? 'text-green-600' : 'text-gray-400'" class="font-normal">
                                            {{ p.image_path ? 'personnalisée' : 'aucune (fond bleu)' }}
                                        </span>
                                    </span>
                                    <input type="file" accept="image/jpeg,image/png,image/webp" class="hidden"
                                        :ref="(el) => (inputs[p.key] = el as HTMLInputElement | null)"
                                        @change="upload(p.key, $event)" />
                                    <button type="button" @click="pick(p.key)" :disabled="uploading !== null"
                                        class="px-3 py-1.5 text-sm border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50">
                                        {{ uploading === p.key ? 'Envoi…' : (p.image_path ? "Changer l'image" : 'Ajouter une image') }}
                                    </button>
                                    <button v-if="p.image_path" type="button" @click="remove(p)" :disabled="uploading !== null"
                                        class="px-3 py-1.5 text-sm border border-red-300 text-red-600 rounded-md hover:bg-red-50 disabled:opacity-50">
                                        Retirer
                                    </button>
                                    <p v-if="errors[p.key]" class="w-full text-xs text-red-600">{{ errors[p.key] }}</p>
                                </div>

                                <!-- Assombrissement -->
                                <div v-if="forms[p.key]">
                                    <label class="flex items-center justify-between text-sm font-medium text-gray-700 mb-1">
                                        Assombrissement de l'image
                                        <span class="font-normal text-gray-500">{{ forms[p.key].overlay }} %</span>
                                    </label>
                                    <input v-model.number="forms[p.key].overlay" type="range" min="0" max="100" step="1"
                                        class="w-full accent-[#E63946]" :disabled="!p.image_path" />
                                    <p class="text-xs text-gray-400">
                                        {{ p.image_path
                                            ? "0 % = image d'origine, 100 % = presque noire. Plus l'image est claire, plus il faut assombrir pour que le texte reste lisible."
                                            : "S'applique quand une image est ajoutée." }}
                                    </p>
                                </div>

                                <!-- Texte -->
                                <div v-if="p.has_text && forms[p.key]" class="grid sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Badge <span class="font-normal text-gray-400">(petit texte rouge, facultatif)</span></label>
                                        <input v-model="forms[p.key].badge" type="text" maxlength="120"
                                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm" />
                                    </div>
                                    <div></div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Titre</label>
                                        <input v-model="forms[p.key].title" type="text" maxlength="120"
                                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm" />
                                        <p v-if="saveErrors[p.key]?.title" class="text-xs text-red-600 mt-1">{{ saveErrors[p.key].title }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Suite du titre en couleur <span class="font-normal text-gray-400">(facultatif)</span></label>
                                        <input v-model="forms[p.key].highlight" type="text" maxlength="120"
                                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm" />
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Sous-titre <span class="font-normal text-gray-400">(facultatif)</span></label>
                                        <textarea v-model="forms[p.key].subtitle" rows="2" maxlength="400"
                                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm"></textarea>
                                        <p v-if="saveErrors[p.key]?.subtitle" class="text-xs text-red-600 mt-1">{{ saveErrors[p.key].subtitle }}</p>
                                    </div>
                                </div>
                                <p v-else-if="p.text_editor" class="text-sm text-gray-500">
                                    Le texte de cette bannière se modifie dans
                                    <Link :href="p.text_editor.url" class="font-medium underline" style="color: #E63946;">{{ p.text_editor.label }}</Link>.
                                </p>

                                <div class="flex flex-wrap items-center justify-end gap-3 pt-1">
                                    <span v-if="isDirty(p)" class="text-xs text-amber-600 mr-auto">Modifications non enregistrées</span>
                                    <button v-if="p.has_text" type="button" @click="resetText(p)"
                                        class="px-3 py-1.5 text-sm text-gray-600 hover:underline">
                                        Texte d'origine
                                    </button>
                                    <button type="button" @click="save(p)" :disabled="saving !== null || !isDirty(p)"
                                        class="px-4 py-2 text-sm bg-primary text-white rounded-md hover:bg-primary-dark disabled:opacity-50">
                                        {{ saving === p.key ? 'Enregistrement…' : 'Enregistrer' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
