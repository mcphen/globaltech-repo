<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';
import { computed, ref, watch } from 'vue';

interface Slide {
    id: number;
    image_url: string | null;
    title: string | null;
    subtitle: string | null;
    text: string | null;
    button1_label: string | null;
    button2_label: string | null;
    text_position: 'left' | 'center' | 'right';
    sort_order: number;
    active: boolean;
}

const props = defineProps<{ slides: Slide[] }>();

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: "Bannière d'accueil", href: route('admin.hero-slides.index') },
];

const list = ref<Slide[]>([...props.slides]);
watch(() => props.slides, (v) => { list.value = [...v]; });

const positionLabel: Record<string, string> = { left: 'Gauche', center: 'Centre', right: 'Droite' };
const activeCount = computed(() => list.value.filter(s => s.active).length);

function move(index: number, dir: -1 | 1) {
    const target = index + dir;
    if (target < 0 || target >= list.value.length) return;
    const copy = [...list.value];
    [copy[index], copy[target]] = [copy[target], copy[index]];
    list.value = copy;
    router.post(route('admin.hero-slides.reorder'), { ids: copy.map(s => s.id) }, { preserveScroll: true });
}

function toggle(slide: Slide) {
    router.patch(route('admin.hero-slides.toggle', slide.id), {}, { preserveScroll: true });
}

const toDelete = ref<Slide | null>(null);
function destroy() {
    if (!toDelete.value) return;
    router.delete(route('admin.hero-slides.destroy', toDelete.value.id), {
        preserveScroll: true,
        onFinish: () => { toDelete.value = null; },
    });
}
</script>

<template>
    <Head title="Bannière d'accueil" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm">

            <div class="flex flex-wrap justify-between items-center gap-3 border-b pb-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Bannière d'accueil</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Dès qu'au moins un slide est actif, le carrousel remplace la bannière par défaut de la page d'accueil.
                    </p>
                </div>
                <Link :href="route('admin.hero-slides.create')"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                    <i class="bi bi-plus-lg"></i> Ajouter un slide
                </Link>
            </div>

            <div class="flex items-center gap-2 text-sm px-4 py-3 rounded-lg"
                :class="activeCount ? 'bg-green-50 text-green-700' : 'bg-amber-50 text-amber-700'">
                <i :class="['bi', activeCount ? 'bi-check-circle-fill' : 'bi-info-circle-fill']"></i>
                <span v-if="activeCount">{{ activeCount }} slide(s) actif(s) — le carrousel est affiché sur l'accueil.</span>
                <span v-else>Aucun slide actif — la bannière par défaut est affichée sur l'accueil.</span>
            </div>

            <div v-if="!list.length" class="text-center py-16 text-gray-400">
                <i class="bi bi-images text-5xl"></i>
                <p class="mt-3">Aucun slide pour le moment.</p>
            </div>

            <div v-else class="space-y-3">
                <div v-for="(slide, i) in list" :key="slide.id"
                    class="flex items-center gap-4 p-3 border border-gray-200 rounded-xl hover:bg-gray-50 transition"
                    :class="{ 'opacity-60': !slide.active }">

                    <div class="flex flex-col">
                        <button type="button" class="p-1 text-gray-400 hover:text-gray-700 disabled:opacity-30"
                            :disabled="i === 0" @click="move(i, -1)" title="Monter">
                            <i class="bi bi-chevron-up"></i>
                        </button>
                        <button type="button" class="p-1 text-gray-400 hover:text-gray-700 disabled:opacity-30"
                            :disabled="i === list.length - 1" @click="move(i, 1)" title="Descendre">
                            <i class="bi bi-chevron-down"></i>
                        </button>
                    </div>

                    <div class="w-40 h-20 rounded-lg overflow-hidden flex-shrink-0" style="background: linear-gradient(135deg,#060C22,#0E2060);">
                        <img v-if="slide.image_url" :src="slide.image_url" class="w-full h-full object-cover" alt="" />
                        <div v-else class="w-full h-full flex items-center justify-center text-white/40 text-xs">Sans image</div>
                    </div>

                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-800 truncate">{{ slide.title || slide.subtitle || '(sans titre)' }}</p>
                        <p v-if="slide.title && slide.subtitle" class="text-sm text-gray-500 truncate">{{ slide.subtitle }}</p>
                        <div class="flex flex-wrap gap-2 mt-1 text-xs text-gray-500">
                            <span class="px-2 py-0.5 rounded bg-gray-100">Texte : {{ positionLabel[slide.text_position] }}</span>
                            <span v-if="slide.button1_label" class="px-2 py-0.5 rounded bg-gray-100">{{ slide.button1_label }}</span>
                            <span v-if="slide.button2_label" class="px-2 py-0.5 rounded bg-gray-100">{{ slide.button2_label }}</span>
                        </div>
                    </div>

                    <button type="button" @click="toggle(slide)" :title="slide.active ? 'Désactiver' : 'Activer'">
                        <span class="relative inline-block w-11 h-6 align-middle">
                            <span :class="['absolute inset-0 rounded-full transition', slide.active ? 'bg-blue-600' : 'bg-gray-300']"></span>
                            <span :class="['absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform', slide.active ? 'translate-x-5' : '']"></span>
                        </span>
                    </button>

                    <Link :href="route('admin.hero-slides.edit', slide.id)"
                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg" title="Modifier">
                        <i class="bi bi-pencil-square"></i>
                    </Link>
                    <button type="button" @click="toDelete = slide"
                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg" title="Supprimer">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal suppression -->
        <div v-if="toDelete" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-2">Supprimer ce slide ?</h3>
                <p class="text-sm text-gray-500 mb-6">Cette action est irréversible. L'image associée sera également supprimée.</p>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="toDelete = null"
                        class="border border-gray-300 text-gray-700 hover:bg-gray-50 font-semibold px-4 py-2 rounded-lg text-sm">Annuler</button>
                    <button type="button" @click="destroy"
                        class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-lg text-sm">Supprimer</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
