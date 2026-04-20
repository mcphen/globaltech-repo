<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import { ref, computed, watch } from 'vue';
import { useDarkMode } from '@/composables/useDarkMode';

interface Formation {
    id: number; title: string; slug: string; description?: string | null;
    image_path?: string | null; image_url?: string | null;
    date_mode: 'single' | 'range'; date?: string | null;
    start_date?: string | null; end_date?: string | null;
    duration_hours?: number | null; price?: number | null; currency?: string | null;
    created_at: string;
}

interface PaginatedData<T> {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
    current_page: number;
    last_page: number;
    total: number;
    per_page: number;
}

const props = defineProps<{
    formations: PaginatedData<Formation>;
    filters: { search?: string | null; category?: string | null };
}>();

const { isDark } = useDarkMode();

const searchQuery = ref(props.filters.search || '');
const activeCategory = ref(props.filters.category || 'all');

const categories = [
    { key: 'all', label: 'Toutes', icon: 'bi-grid', color: '#0B1437' },
    { key: 'it', label: 'Informatique & IT', icon: 'bi-cpu', color: '#2563EB', badge: '⭐ Star' },
    { key: 'pmp', label: 'PMP & Projet', icon: 'bi-diagram-3', color: '#16A34A' },
    { key: 'management', label: 'Management', icon: 'bi-people', color: '#D97706' },
    { key: 'finance', label: 'Finance', icon: 'bi-graph-up', color: '#7C3AED' },
];

const doSearch = () => {
    router.get(route('formations'), {
        search: searchQuery.value || undefined,
        category: activeCategory.value !== 'all' ? activeCategory.value : undefined,
    }, { preserveState: true, replace: true });
};

let searchTimer: ReturnType<typeof setTimeout>;
watch(searchQuery, () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(doSearch, 400);
});
watch(activeCategory, doSearch);

const formatDate = (d?: string | null) =>
    d ? new Date(d).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' }) : '';

// Dark-mode computed styles
const filterBarBg    = computed(() => isDark.value ? '#0D1526' : '#FFFFFF');
const filterBarBorder = computed(() => isDark.value ? 'rgba(255,255,255,0.08)' : '#E5E7EB');
const gridBg         = computed(() => isDark.value ? '#0B1437' : '#F8FAFC');
const cardBg         = computed(() => isDark.value ? '#131F36' : '#FFFFFF');
const cardBorder     = computed(() => isDark.value ? 'rgba(255,255,255,0.07)' : '#F1F5F9');
const titleColor     = computed(() => isDark.value ? '#F1F5F9' : '#0B1437');
const descColor      = computed(() => isDark.value ? '#94A3B8' : '#637084');
const metaColor      = computed(() => isDark.value ? '#64748B' : '#637084');
const dividerColor   = computed(() => isDark.value ? 'rgba(255,255,255,0.07)' : '#F1F5F9');
const priceColor     = computed(() => isDark.value ? '#F1F5F9' : '#0B1437');
const pillInactive   = computed(() => isDark.value
    ? 'background: rgba(255,255,255,0.06); color: #94A3B8;'
    : 'background: #F8FAFC; color: #475569;');
const searchStyle    = computed(() => isDark.value
    ? 'background: #1E2D50; border-color: rgba(255,255,255,0.1); color: #F1F5F9;'
    : 'background: #F8FAFC; border-color: #E5E7EB; color: #0B1437;');
const ctaBg          = computed(() => isDark.value ? '#0D1526' : '#FFFFFF');
const ctaBorder      = computed(() => isDark.value ? 'rgba(255,255,255,0.06)' : '#F1F5F9');
const countColor     = computed(() => isDark.value ? '#94A3B8' : '#637084');
const countBold      = computed(() => isDark.value ? '#F1F5F9' : '#0B1437');
const thumbBg        = computed(() => isDark.value
    ? 'linear-gradient(135deg, #1E2D50, #152050)'
    : 'linear-gradient(135deg, #EFF6FF, #DBEAFE)');
const thumbIcon      = computed(() => isDark.value ? '#2D4A7A' : '#BFDBFE');
</script>

<template>
    <Head title="Formations Certifiantes — GlobalTECH EDUCATION Africa" />
    <LayoutFront>

        <!-- Hero -->
        <section class="py-24 relative overflow-hidden" style="background: linear-gradient(rgba(255,255,255,0.025) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.025) 1px,transparent 1px),linear-gradient(135deg,#060C22 0%,#0B1437 42%,#0E2060 72%,#091830 100%); background-size:60px 60px,60px 60px,100% 100%;">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
                <div class="max-w-3xl">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-5"
                        style="background: rgba(232, 160, 32, 0.15); color: #E8A020; border: 1px solid rgba(232, 160, 32, 0.3);">
                        <i class="bi bi-mortarboard-fill"></i>
                        Centre accrédité PMI — Pearson VUE
                    </div>
                    <h1 class="text-5xl lg:text-6xl font-black text-white mb-5 leading-tight" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                        Formations <span class="gt-text-gradient">certifiantes</span>
                    </h1>
                    <p class="text-xl text-white/70 max-w-2xl leading-relaxed">
                        Des programmes conçus par des experts pour vous mener à la certification internationale. IT, PMP, Management — choisissez votre filière.
                    </p>
                </div>
            </div>
        </section>

        <!-- Filters & Search -->
        <section class="sticky top-[72px] z-40 shadow-sm"
            :style="`background: ${filterBarBg}; border-bottom: 1px solid ${filterBarBorder};`">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 py-4">
                <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">
                    <!-- Category Pills -->
                    <div class="flex flex-wrap gap-2">
                        <button v-for="cat in categories" :key="cat.key"
                            @click="activeCategory = cat.key"
                            class="flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-semibold transition-all duration-200"
                            :style="activeCategory === cat.key
                                ? `background: ${cat.color}; color: white; box-shadow: 0 4px 12px ${cat.color}40;`
                                : pillInactive">
                            <i :class="['bi', cat.icon, 'text-xs']"></i>
                            {{ cat.label }}
                            <span v-if="cat.badge" class="text-xs opacity-80">{{ cat.badge }}</span>
                        </button>
                    </div>

                    <!-- Search -->
                    <div class="relative flex-shrink-0">
                        <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-sm" style="color: #94A3B8;"></i>
                        <input v-model="searchQuery" type="text" placeholder="Rechercher une formation..."
                            class="pl-9 pr-4 py-2.5 rounded-xl border text-sm w-72 focus:outline-none focus:ring-2 transition-all"
                            :style="searchStyle"
                            style="--tw-ring-color: #E8A020;" />
                    </div>
                </div>
            </div>
        </section>

        <!-- Formations Grid -->
        <section class="py-16" :style="`background: ${gridBg};`">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">

                <!-- Results count -->
                <div class="flex items-center justify-between mb-8">
                    <p class="text-sm" :style="`color: ${countColor};`">
                        <span class="font-bold" :style="`color: ${countBold};`">{{ formations.total }}</span>
                        formation{{ formations.total !== 1 ? 's' : '' }} disponible{{ formations.total !== 1 ? 's' : '' }}
                    </p>
                    <div class="flex items-center gap-2 text-xs" :style="`color: ${countColor};`">
                        <i class="bi bi-patch-check-fill" style="color: #E8A020;"></i>
                        Toutes certifiantes
                    </div>
                </div>

                <!-- Grid -->
                <div v-if="formations.data.length" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <Link v-for="f in formations.data" :key="f.id" :href="route('formations.show', { slug: f.slug })"
                        class="group flex flex-col overflow-hidden rounded-2xl transition-all duration-300 hover:-translate-y-1 hover:shadow-xl"
                        :style="`background: ${cardBg}; border: 1px solid ${cardBorder};`">

                        <!-- Thumbnail -->
                        <div class="relative h-52 overflow-hidden" :style="`background: ${thumbBg};`">
                            <img v-if="f.image_url" :src="f.image_url" :alt="f.title"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" />
                            <div v-else class="w-full h-full flex items-center justify-center">
                                <i class="bi bi-mortarboard text-5xl" :style="`color: ${thumbIcon};`"></i>
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>

                            <!-- Certification badge -->
                            <div class="absolute top-3 right-3 px-2.5 py-1 rounded-full text-xs font-bold text-white"
                                style="background: rgba(11, 20, 55, 0.75); backdrop-filter: blur(8px);">
                                <i class="bi bi-patch-check-fill mr-1" style="color: #E8A020;"></i>Certifiante
                            </div>
                        </div>

                        <div class="p-6 flex flex-col flex-1">
                            <h3 class="text-base font-bold mb-3 line-clamp-2 leading-snug transition-colors"
                                :style="`color: ${titleColor}; font-family: 'Plus Jakarta Sans', sans-serif;`"
                                :class="isDark ? 'group-hover:text-yellow-400' : 'group-hover:text-blue-600'">
                                {{ f.title }}
                            </h3>

                            <p v-if="f.description" class="text-sm mb-4 line-clamp-2 flex-1 leading-relaxed"
                                :style="`color: ${descColor};`">
                                {{ f.description }}
                            </p>

                            <!-- Meta -->
                            <div class="mt-auto space-y-3">
                                <!-- Dates -->
                                <div v-if="f.date || f.start_date" class="flex items-center gap-2 text-xs"
                                    :style="`color: ${metaColor};`">
                                    <i class="bi bi-calendar3" style="color: #E8A020;"></i>
                                    <span v-if="f.date_mode === 'single'">{{ formatDate(f.date) }}</span>
                                    <span v-else>{{ formatDate(f.start_date) }} → {{ formatDate(f.end_date) }}</span>
                                </div>

                                <!-- Duration -->
                                <div v-if="f.duration_hours" class="flex items-center gap-2 text-xs"
                                    :style="`color: ${metaColor};`">
                                    <i class="bi bi-clock" style="color: #E8A020;"></i>
                                    {{ f.duration_hours }} heures de formation
                                </div>

                                <!-- Price + CTA -->
                                <div class="flex items-center justify-between pt-3"
                                    :style="`border-top: 1px solid ${dividerColor};`">
                                    <div v-if="f.price" class="font-black text-base" :style="`color: ${priceColor};`">
                                        {{ new Intl.NumberFormat('fr-FR').format(f.price) }}
                                        <span class="text-xs font-normal" :style="`color: ${metaColor};`">{{ f.currency || 'XOF' }}</span>
                                    </div>
                                    <div v-else class="text-xs px-2.5 py-1 rounded-lg font-semibold"
                                        :style="isDark ? 'background: rgba(22,163,74,0.15); color: #4ADE80;' : 'background: #F0FDF4; color: #16A34A;'">
                                        Sur devis
                                    </div>
                                    <span class="text-xs font-bold flex items-center gap-1 group-hover:gap-2 transition-all"
                                        :style="isDark ? 'color: #60A5FA;' : 'color: #2563EB;'">
                                        Détails <i class="bi bi-arrow-right"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </Link>
                </div>

                <!-- Empty state -->
                <div v-else class="text-center py-24">
                    <i class="bi bi-search text-5xl mb-4 block" style="color: #CBD5E1;"></i>
                    <h3 class="text-xl font-bold mb-2" :style="`color: ${titleColor};`">Aucune formation trouvée</h3>
                    <p class="mb-6" :style="`color: ${descColor};`">Essayez d'autres mots-clés ou explorez toutes nos filières.</p>
                    <button @click="searchQuery = ''; activeCategory = 'all'" class="gt-btn-primary rounded-xl">
                        Réinitialiser les filtres
                    </button>
                </div>

                <!-- Pagination -->
                <div v-if="formations.last_page > 1" class="flex justify-center gap-2 mt-12">
                    <Link v-for="link in formations.links" :key="link.label"
                        :href="link.url ?? '#'"
                        class="px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200"
                        :class="!link.url ? 'opacity-40 pointer-events-none' : ''"
                        :style="link.active
                            ? 'background: #0B1437; color: white;'
                            : isDark ? 'color: #94A3B8;' : 'color: #374151;'"
                        v-html="link.label">
                    </Link>
                </div>
            </div>
        </section>

        <!-- CTA Banner -->
        <section class="py-16" :style="`background: ${ctaBg}; border-top: 1px solid ${ctaBorder};`">
            <div class="max-w-4xl mx-auto px-6 lg:px-8 text-center">
                <div class="p-10 rounded-3xl text-white relative overflow-hidden"
                    style="background: linear-gradient(135deg, #0B1437, #152050);">
                    <div class="absolute inset-0 gt-grid-pattern opacity-20"></div>
                    <div class="relative z-10">
                        <i class="bi bi-building text-3xl mb-4 block" style="color: #E8A020;"></i>
                        <h3 class="text-2xl font-black mb-3" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                            Formation pour votre entreprise ?
                        </h3>
                        <p class="text-white/70 mb-6 max-w-xl mx-auto text-sm">
                            Nous concevons des programmes sur mesure pour former vos équipes. Contactez nos experts pour un devis personnalisé.
                        </p>
                        <Link href="/contact" class="gt-btn-gold px-8 py-3.5 rounded-xl">
                            <i class="bi bi-telephone-fill"></i>
                            Demander un devis entreprise
                        </Link>
                    </div>
                </div>
            </div>
        </section>

    </LayoutFront>
</template>
