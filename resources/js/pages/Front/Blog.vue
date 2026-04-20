<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import { useDarkMode } from '@/composables/useDarkMode';
import { ref, computed, watch } from 'vue';

interface Actualite {
    id: number; title: string; description?: string;
    image_path?: string; image_url?: string;
    published_at?: string; created_at: string;
}

interface PaginatedData {
    data: Actualite[];
    links: { url: string | null; label: string; active: boolean }[];
    current_page: number;
    last_page: number;
    total: number;
    per_page: number;
}

interface Filters { search?: string | null; date?: string | null; sort?: string | null; }

const props = defineProps<{ actualites: PaginatedData; filters: Filters; }>();
const { isDark } = useDarkMode();

const searchQuery = ref(props.filters.search || '');
const sortBy = ref(props.filters.sort || 'newest');

const doSearch = () => {
    router.get(route('blog'), {
        search: searchQuery.value || undefined,
        sort: sortBy.value,
    }, { preserveState: true, replace: true });
};

let timer: ReturnType<typeof setTimeout>;
watch(searchQuery, () => { clearTimeout(timer); timer = setTimeout(doSearch, 400); });
watch(sortBy, doSearch);

const formatDate = (d: string) => new Date(d).toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' });
const excerpt = (html?: string) => (html || '').replace(/<[^>]*>?/gm, '').slice(0, 140) + '...';

// Dark-mode styles
const filterBarBg    = computed(() => isDark.value ? '#0D1526' : '#FFFFFF');
const filterBarBorder = computed(() => isDark.value ? 'rgba(255,255,255,0.08)' : '#E5E7EB');
const gridBg         = computed(() => isDark.value ? '#0B1437' : '#F8FAFC');
const ctaBg          = computed(() => isDark.value ? '#0D1526' : '#FFFFFF');
const ctaBorder      = computed(() => isDark.value ? 'rgba(255,255,255,0.06)' : '#F1F5F9');
const titleColor     = computed(() => isDark.value ? '#F1F5F9' : '#0B1437');
const descColor      = computed(() => isDark.value ? '#94A3B8' : '#637084');
const countBold      = computed(() => isDark.value ? '#F1F5F9' : '#0B1437');
const inputStyle     = computed(() => isDark.value
    ? 'background: #1E2D50; border-color: rgba(255,255,255,0.1); color: #F1F5F9;'
    : 'background: #F8FAFC; border-color: #E5E7EB; color: #0B1437;');
const selectStyle    = computed(() => isDark.value
    ? 'background: #1E2D50; border-color: rgba(255,255,255,0.1); color: #F1F5F9;'
    : 'background: #F8FAFC; border-color: #E5E7EB; color: #0B1437;');
const featuredThumbBg = computed(() => isDark.value
    ? 'linear-gradient(135deg, #1E2D50, #152050)'
    : 'linear-gradient(135deg, #EFF6FF, #DBEAFE)');
const cardThumbBg    = computed(() => isDark.value
    ? 'linear-gradient(135deg, #131F36, #1E2D50)'
    : 'linear-gradient(135deg, #F8FAFC, #EFF6FF)');
const featuredBadgeBg = computed(() => isDark.value ? 'rgba(37,99,235,0.2)' : '#EFF6FF');
const featuredBadgeColor = computed(() => isDark.value ? '#60A5FA' : '#2563EB');
const hoverTitle     = computed(() => isDark.value ? 'group-hover:text-yellow-400' : 'group-hover:text-blue-600');
const paginationInactive = computed(() => isDark.value ? 'color: #94A3B8;' : 'color: #374151;');
</script>

<template>
    <Head title="Actualités — GlobalTECH EDUCATION Africa" />
    <LayoutFront>

        <!-- Hero -->
        <section class="py-24 relative overflow-hidden" style="background: linear-gradient(rgba(255,255,255,0.025) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.025) 1px,transparent 1px),linear-gradient(135deg,#060C22 0%,#0B1437 42%,#0E2060 72%,#091830 100%); background-size:60px 60px,60px 60px,100% 100%;">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
                <div class="max-w-2xl">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-5"
                        style="background: rgba(232, 160, 32, 0.15); color: #E8A020; border: 1px solid rgba(232, 160, 32, 0.3);">
                        <i class="bi bi-newspaper"></i>
                        Actualités & Insights
                    </div>
                    <h1 class="text-5xl lg:text-6xl font-black text-white mb-5 leading-tight" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                        Le blog <span class="gt-text-gradient">GlobalTECH</span>
                    </h1>
                    <p class="text-xl text-white/70">
                        Restez informé des dernières nouvelles, tendances et événements du monde de la formation professionnelle en Afrique.
                    </p>
                </div>
            </div>
        </section>

        <!-- Filters -->
        <div class="sticky top-[72px] z-40 shadow-sm"
            :style="`background: ${filterBarBg}; border-bottom: 1px solid ${filterBarBorder};`">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 py-4 flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                <div class="relative">
                    <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-sm" style="color: #94A3B8;"></i>
                    <input v-model="searchQuery" type="text" placeholder="Rechercher un article..."
                        class="pl-9 pr-4 py-2.5 rounded-xl border text-sm w-72 focus:outline-none focus:ring-2 focus:ring-yellow-400 transition-all"
                        :style="inputStyle" />
                </div>
                <select v-model="sortBy"
                    class="px-4 py-2.5 rounded-xl border text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"
                    :style="selectStyle">
                    <option value="newest">Plus récents</option>
                    <option value="oldest">Plus anciens</option>
                </select>
            </div>
        </div>

        <!-- Articles -->
        <section class="py-16" :style="`background: ${gridBg};`">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <p class="text-sm mb-8" :style="`color: ${descColor};`">
                    <span class="font-bold" :style="`color: ${countBold};`">{{ actualites.total }}</span>
                    article{{ actualites.total !== 1 ? 's' : '' }}
                </p>

                <!-- Grid -->
                <div v-if="actualites.data.length" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Featured first post -->
                    <Link v-if="actualites.current_page === 1 && actualites.data[0]"
                        :href="`/${actualites.data[0].id}/blog`"
                        class="md:col-span-2 lg:col-span-3 gt-card overflow-hidden group flex flex-col lg:flex-row">
                        <div class="lg:w-1/2 h-64 lg:h-auto overflow-hidden" :style="`background: ${featuredThumbBg};`">
                            <img v-if="actualites.data[0].image_url" :src="actualites.data[0].image_url" :alt="actualites.data[0].title"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" />
                            <div v-else class="w-full h-full flex items-center justify-center">
                                <i class="bi bi-newspaper text-6xl opacity-20" style="color: #2563EB;"></i>
                            </div>
                        </div>
                        <div class="lg:w-1/2 p-8 flex flex-col justify-center">
                            <span class="text-xs font-bold px-2.5 py-1 rounded-full mb-3 inline-block"
                                :style="`background: ${featuredBadgeBg}; color: ${featuredBadgeColor};`">
                                À la une
                            </span>
                            <h2 class="text-2xl font-black mb-3 transition-colors line-clamp-3"
                                :class="hoverTitle"
                                :style="`color: ${titleColor}; font-family: 'Plus Jakarta Sans', sans-serif;`">
                                {{ actualites.data[0].title }}
                            </h2>
                            <p class="text-sm leading-relaxed mb-4 line-clamp-3" :style="`color: ${descColor};`">
                                {{ excerpt(actualites.data[0].description) }}
                            </p>
                            <div class="flex items-center justify-between">
                                <span class="text-xs" style="color: #94A3B8;">{{ formatDate(actualites.data[0].published_at ?? actualites.data[0].created_at) }}</span>
                                <span class="text-sm font-bold flex items-center gap-1 group-hover:gap-2 transition-all" style="color: #E8A020;">
                                    Lire l'article <i class="bi bi-arrow-right"></i>
                                </span>
                            </div>
                        </div>
                    </Link>

                    <!-- Rest of articles -->
                    <Link v-for="post in (actualites.current_page === 1 ? actualites.data.slice(1) : actualites.data)" :key="post.id"
                        :href="`/${post.id}/blog`"
                        class="gt-card overflow-hidden group flex flex-col">
                        <div class="h-48 overflow-hidden" :style="`background: ${cardThumbBg};`">
                            <img v-if="post.image_url" :src="post.image_url" :alt="post.title"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" />
                            <div v-else class="w-full h-full flex items-center justify-center">
                                <i class="bi bi-newspaper text-4xl opacity-20" style="color: #2563EB;"></i>
                            </div>
                        </div>
                        <div class="p-6 flex flex-col flex-1">
                            <p class="text-xs mb-2" style="color: #94A3B8;">{{ formatDate(post.published_at ?? post.created_at) }}</p>
                            <h3 class="text-base font-bold mb-2 line-clamp-2 transition-colors flex-1"
                                :class="hoverTitle"
                                :style="`color: ${titleColor}; font-family: 'Plus Jakarta Sans', sans-serif;`">
                                {{ post.title }}
                            </h3>
                            <p v-if="post.description" class="text-sm line-clamp-2 mb-4" :style="`color: ${descColor};`">
                                {{ excerpt(post.description) }}
                            </p>
                            <span class="text-xs font-bold flex items-center gap-1 group-hover:gap-2 transition-all mt-auto" style="color: #E8A020;">
                                Lire <i class="bi bi-arrow-right"></i>
                            </span>
                        </div>
                    </Link>
                </div>

                <!-- Empty -->
                <div v-else class="text-center py-24">
                    <i class="bi bi-newspaper text-5xl mb-4 block" style="color: #CBD5E1;"></i>
                    <h3 class="text-xl font-bold mb-2" :style="`color: ${titleColor};`">Aucun article trouvé</h3>
                    <p class="mb-4" :style="`color: ${descColor};`">Essayez d'autres mots-clés.</p>
                    <button @click="searchQuery = ''" class="gt-btn-primary rounded-xl">Effacer la recherche</button>
                </div>

                <!-- Pagination -->
                <div v-if="actualites.last_page > 1" class="flex justify-center gap-2 mt-12">
                    <Link v-for="link in actualites.links" :key="link.label"
                        :href="link.url ?? '#'"
                        class="px-4 py-2 rounded-lg text-sm font-semibold transition-all"
                        :class="!link.url ? 'opacity-40 pointer-events-none' : ''"
                        :style="link.active ? 'background: #0B1437; color: white;' : paginationInactive"
                        v-html="link.label">
                    </Link>
                </div>
            </div>
        </section>

        <!-- Newsletter CTA -->
        <section class="py-16" :style="`background: ${ctaBg}; border-top: 1px solid ${ctaBorder};`">
            <div class="max-w-2xl mx-auto px-6 text-center">
                <i class="bi bi-bell-fill text-3xl mb-4 block" style="color: #E8A020;"></i>
                <h3 class="text-2xl font-black mb-3" :style="`color: ${titleColor};`">Ne manquez aucune actualité</h3>
                <p class="mb-6" :style="`color: ${descColor};`">Recevez nos derniers articles, annonces de formations et insights directement dans votre boîte mail.</p>
                <Link href="/contact" class="gt-btn-gold px-8 py-3.5 rounded-xl font-black">
                    <i class="bi bi-envelope-fill"></i> S'abonner aux actualités
                </Link>
            </div>
        </section>

    </LayoutFront>
</template>
