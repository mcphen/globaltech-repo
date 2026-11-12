<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import HeroSlideView from '@/components/front/HeroSlideView.vue';
import { type BreadcrumbItemType } from '@/types';
import { computed, onBeforeUnmount, ref } from 'vue';

interface Slide {
    id: number;
    image_url: string | null;
    title: string | null;
    subtitle: string | null;
    text: string | null;
    button1_label: string | null;
    button1_url: string | null;
    button2_label: string | null;
    button2_url: string | null;
    text_position: 'left' | 'center' | 'right';
    overlay_opacity: number;
    sort_order: number;
    active: boolean;
}

const props = defineProps<{ slide: Slide | null }>();
const isEdit = computed(() => !!props.slide);

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: "Bannière d'accueil", href: route('admin.hero-slides.index') },
    { title: props.slide ? 'Modifier' : 'Ajouter', href: '#' },
];

const form = useForm({
    image: null as File | null,
    remove_image: false,
    title: props.slide?.title ?? '',
    subtitle: props.slide?.subtitle ?? '',
    text: props.slide?.text ?? '',
    button1_label: props.slide?.button1_label ?? '',
    button1_url: props.slide?.button1_url ?? '',
    button2_label: props.slide?.button2_label ?? '',
    button2_url: props.slide?.button2_url ?? '',
    text_position: props.slide?.text_position ?? 'left',
    overlay_opacity: props.slide?.overlay_opacity ?? 50,
    active: props.slide?.active ?? true,
});

// ── Image ────────────────────────────────────────────────────────────────────
const localPreview = ref<string | null>(null);
const imageUrl = computed(() => {
    if (localPreview.value) return localPreview.value;
    if (form.remove_image) return null;
    return props.slide?.image_url ?? null;
});

function onFile(e: Event) {
    const file = (e.target as HTMLInputElement).files?.[0] ?? null;
    form.image = file;
    form.remove_image = false;
    if (localPreview.value) URL.revokeObjectURL(localPreview.value);
    localPreview.value = file ? URL.createObjectURL(file) : null;
}

function removeImage() {
    form.image = null;
    if (localPreview.value) URL.revokeObjectURL(localPreview.value);
    localPreview.value = null;
    form.remove_image = !!props.slide?.image_url;
}

onBeforeUnmount(() => { if (localPreview.value) URL.revokeObjectURL(localPreview.value); });

const previewSlide = computed(() => ({ ...form.data(), image_url: imageUrl.value }));

const positions = [
    { value: 'left', label: 'Gauche', icon: 'bi-text-left' },
    { value: 'center', label: 'Centre', icon: 'bi-text-center' },
    { value: 'right', label: 'Droite', icon: 'bi-text-right' },
] as const;

function submit() {
    const opts = { preserveScroll: true, forceFormData: true };
    if (props.slide) {
        form.transform(d => ({ ...d, active: d.active ? 1 : 0, remove_image: d.remove_image ? 1 : 0, _method: 'PUT' }))
            .post(route('admin.hero-slides.update', props.slide.id), opts);
    } else {
        form.transform(d => ({ ...d, active: d.active ? 1 : 0, remove_image: 0 }))
            .post(route('admin.hero-slides.store'), opts);
    }
}

const input = 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm';
</script>

<template>
    <Head :title="isEdit ? 'Modifier un slide' : 'Ajouter un slide'" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm">

            <div class="flex justify-between items-center border-b pb-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">{{ isEdit ? 'Modifier le slide' : 'Nouveau slide' }}</h2>
                    <p class="text-sm text-gray-500 mt-1">Tous les champs sont facultatifs : n'affichez que ce dont vous avez besoin.</p>
                </div>
                <Link :href="route('admin.hero-slides.index')" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
                    <i class="bi bi-arrow-left"></i> Retour
                </Link>
            </div>

            <div class="grid xl:grid-cols-2 gap-8">
                <!-- ── Formulaire ───────────────────────────────────────── -->
                <form @submit.prevent="submit" class="space-y-6">

                    <!-- Image -->
                    <fieldset class="space-y-2">
                        <legend class="text-sm font-semibold text-gray-800 mb-2">Image de fond</legend>
                        <div class="flex items-center gap-4">
                            <div class="w-40 h-24 rounded-lg overflow-hidden border border-gray-200 flex-shrink-0"
                                style="background: linear-gradient(135deg,#060C22,#0E2060);">
                                <img v-if="imageUrl" :src="imageUrl" class="w-full h-full object-cover" alt="" />
                                <div v-else class="w-full h-full flex items-center justify-center text-white/40 text-xs">Aucune image</div>
                            </div>
                            <div class="space-y-2">
                                <label class="inline-flex items-center gap-2 cursor-pointer border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-semibold px-4 py-2 rounded-lg">
                                    <i class="bi bi-upload"></i> {{ imageUrl ? 'Remplacer' : 'Choisir une image' }}
                                    <input type="file" accept="image/jpeg,image/png,image/webp" class="hidden" @change="onFile" />
                                </label>
                                <button v-if="imageUrl" type="button" @click="removeImage" class="block text-xs text-red-600 hover:underline">
                                    Retirer l'image
                                </button>
                                <p class="text-xs text-gray-400">JPG, PNG ou WebP — 5 Mo max. Idéal : 1920 × 1080.</p>
                            </div>
                        </div>
                        <p v-if="form.errors.image" class="text-xs text-red-600">{{ form.errors.image }}</p>

                        <div v-if="imageUrl" class="pt-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Assombrissement de l'image : <span class="font-mono">{{ form.overlay_opacity }}%</span>
                            </label>
                            <input v-model.number="form.overlay_opacity" type="range" min="0" max="90" step="5" class="w-full accent-blue-600" />
                            <p class="text-xs text-gray-400">Augmentez si le texte est difficile à lire sur l'image.</p>
                        </div>
                    </fieldset>

                    <!-- Textes -->
                    <fieldset class="space-y-4">
                        <legend class="text-sm font-semibold text-gray-800 mb-2">Textes</legend>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sous-titre <span class="text-gray-400 font-normal">(petit texte au-dessus du titre)</span></label>
                            <input v-model="form.subtitle" type="text" :class="input" maxlength="255" />
                            <p v-if="form.errors.subtitle" class="mt-1 text-xs text-red-600">{{ form.errors.subtitle }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Titre</label>
                            <input v-model="form.title" type="text" :class="input" maxlength="255" />
                            <p v-if="form.errors.title" class="mt-1 text-xs text-red-600">{{ form.errors.title }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Texte</label>
                            <textarea v-model="form.text" rows="3" :class="input + ' resize-y'" maxlength="2000"></textarea>
                            <p v-if="form.errors.text" class="mt-1 text-xs text-red-600">{{ form.errors.text }}</p>
                        </div>
                    </fieldset>

                    <!-- Boutons -->
                    <fieldset class="space-y-4">
                        <legend class="text-sm font-semibold text-gray-800 mb-2">Boutons</legend>
                        <p class="text-xs text-gray-400 -mt-2">Un bouton s'affiche seulement si son libellé est rempli. Lien interne (ex. <code>/formations</code>) ou externe (<code>https://…</code>).</p>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Bouton 1 — libellé</label>
                                <input v-model="form.button1_label" type="text" :class="input" maxlength="100" placeholder="Voir les formations" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Bouton 1 — lien</label>
                                <input v-model="form.button1_url" type="text" :class="input" maxlength="500" placeholder="/formations" />
                                <p v-if="form.errors.button1_url" class="mt-1 text-xs text-red-600">{{ form.errors.button1_url }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Bouton 2 — libellé</label>
                                <input v-model="form.button2_label" type="text" :class="input" maxlength="100" placeholder="Nous contacter" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Bouton 2 — lien</label>
                                <input v-model="form.button2_url" type="text" :class="input" maxlength="500" placeholder="/contact" />
                                <p v-if="form.errors.button2_url" class="mt-1 text-xs text-red-600">{{ form.errors.button2_url }}</p>
                            </div>
                        </div>
                    </fieldset>

                    <!-- Mise en page -->
                    <fieldset class="space-y-4">
                        <legend class="text-sm font-semibold text-gray-800 mb-2">Mise en page</legend>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Position du texte et des boutons</label>
                            <div class="inline-flex rounded-lg border border-gray-300 overflow-hidden">
                                <button v-for="p in positions" :key="p.value" type="button" @click="form.text_position = p.value"
                                    class="px-4 py-2 text-sm font-medium flex items-center gap-2 transition border-r last:border-r-0 border-gray-300"
                                    :class="form.text_position === p.value ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'">
                                    <i :class="['bi', p.icon]"></i> {{ p.label }}
                                </button>
                            </div>
                        </div>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <span class="relative inline-block w-11 h-6">
                                <input v-model="form.active" type="checkbox" class="sr-only" />
                                <span :class="['absolute inset-0 rounded-full transition', form.active ? 'bg-blue-600' : 'bg-gray-300']"></span>
                                <span :class="['absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform', form.active ? 'translate-x-5' : '']"></span>
                            </span>
                            <span class="text-sm font-medium text-gray-700">Visible sur le site</span>
                        </label>
                    </fieldset>

                    <div class="flex gap-3 pt-2 border-t">
                        <button type="submit" :disabled="form.processing"
                            class="mt-4 bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-white font-semibold px-6 py-2.5 rounded-lg transition text-sm">
                            <i class="bi bi-check-lg mr-1"></i> Enregistrer
                        </button>
                        <Link :href="route('admin.hero-slides.index')"
                            class="mt-4 border border-gray-300 text-gray-700 hover:bg-gray-50 font-semibold px-6 py-2.5 rounded-lg transition text-sm">
                            Annuler
                        </Link>
                    </div>
                </form>

                <!-- ── Aperçu ───────────────────────────────────────────── -->
                <div class="xl:sticky xl:top-6 self-start">
                    <p class="text-xs text-gray-400 mb-2 uppercase tracking-wider font-semibold">Aperçu</p>
                    <div class="relative w-full aspect-video rounded-xl overflow-hidden shadow-lg border border-gray-200">
                        <HeroSlideView :slide="previewSlide" preview />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
