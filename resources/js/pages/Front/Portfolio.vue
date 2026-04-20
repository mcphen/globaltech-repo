<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import { useDarkMode } from '@/composables/useDarkMode';
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

interface Photo { id: number; album_id: number; image_path: string; image_url?: string; caption?: string; is_banner?: boolean; }
interface Album { id: number; title: string; description?: string; created_at: string; photos: Photo[]; cover?: Photo; }

const props = defineProps<{ albums?: Album[] }>();
const albums = ref<Album[]>(props.albums ?? []);
const { isDark } = useDarkMode();

onMounted(async () => {
    if (!albums.value.length) {
        try {
            const res = await axios.get('/api/albums/latest');
            if (res.data) albums.value = res.data;
        } catch {}
    }
});

const activeFilter = ref('all');
const filters = [
    { key: 'all', label: 'Tous les projets' },
    { key: 'formation', label: 'Formation' },
    { key: 'consulting', label: 'Consulting' },
    { key: 'institutional', label: 'Institutionnel' },
    { key: 'technology', label: 'Technologie' },
];

const caseStudies = [
    {
        id: 1, country: '🇨🇮', category: 'formation', tag: 'Ministère',
        title: 'Transformation digitale — Ministère de l\'Éducation CI',
        desc: 'Déploiement d\'une solution de formation e-learning pour 2 000 fonctionnaires. Conception du curriculum, mise en œuvre de la plateforme LMS et formation des formateurs.',
        metrics: [{ v: '2 000', l: 'Apprenants formés' }, { v: '95%', l: 'Taux de certification' }, { v: '6 mois', l: 'Durée du projet' }],
        tags: ['E-Learning', 'LMS', 'Formation', 'Côte d\'Ivoire'],
        color: '#2563EB', bgLight: '#EFF6FF', bgDark: 'rgba(37,99,235,0.12)', bannerDark: 'rgba(37,99,235,0.08)', icon: 'bi-laptop',
    },
    {
        id: 2, country: '🇸🇳', category: 'consulting', tag: 'Corporate',
        title: 'Programme PMP® — Groupe Sonatel / Orange Afrique',
        desc: 'Formation et certification de 120 chefs de projet au standard PMP®. Programme intensif sur 3 mois avec examens blancs et coaching individualisé.',
        metrics: [{ v: '120', l: 'Certifiés PMP®' }, { v: '92%', l: 'Taux de réussite' }, { v: '3 mois', l: 'Programme' }],
        tags: ['PMP®', 'Corporate', 'Certification', 'Sénégal'],
        color: '#16A34A', bgLight: '#F0FDF4', bgDark: 'rgba(22,163,74,0.12)', bannerDark: 'rgba(22,163,74,0.08)', icon: 'bi-award',
    },
    {
        id: 3, country: '🌍', category: 'technology', tag: 'International',
        title: 'Plateforme e-Learning panafricaine — ONG Internationale',
        desc: 'Architecture et déploiement d\'une plateforme LMS multi-pays pour 15 000 bénéficiaires. Intégration multilingue (FR/EN/PT) et tableau de bord de suivi des performances.',
        metrics: [{ v: '15 000', l: 'Bénéficiaires' }, { v: '8 pays', l: 'Déploiement' }, { v: '3 langues', l: 'Multilingue' }],
        tags: ['LMS', 'Multilingue', 'Digital', 'Panafricain'],
        color: '#D97706', bgLight: '#FFFBEB', bgDark: 'rgba(217,119,6,0.12)', bannerDark: 'rgba(217,119,6,0.08)', icon: 'bi-globe',
    },
    {
        id: 4, country: '🇨🇲', category: 'institutional', tag: 'Institution',
        title: 'Formation des cadres — Banque de Développement du Cameroun',
        desc: 'Programme de renforcement des compétences pour 80 cadres bancaires : management de projet, analyse financière et leadership.',
        metrics: [{ v: '80', l: 'Cadres formés' }, { v: '100%', l: 'Satisfaction' }, { v: '4 mois', l: 'Programme' }],
        tags: ['Finance', 'Management', 'Banking', 'Cameroun'],
        color: '#7C3AED', bgLight: '#F5F3FF', bgDark: 'rgba(124,58,237,0.12)', bannerDark: 'rgba(124,58,237,0.08)', icon: 'bi-bank',
    },
    {
        id: 5, country: '🇧🇫', category: 'consulting', tag: 'PMO',
        title: 'PMO & Gouvernance IT — Telecoms Burkina',
        desc: 'Mise en place d\'un PMO (Project Management Office) et accompagnement à la maturité en gestion de projets IT selon le référentiel PMBOK.',
        metrics: [{ v: 'PMO', l: 'Opérationnel' }, { v: '-40%', l: 'Délais projets' }, { v: '12 mois', l: 'Mission' }],
        tags: ['PMO', 'IT Governance', 'PMBOK', 'Burkina Faso'],
        color: '#0891B2', bgLight: '#F0FDFA', bgDark: 'rgba(8,145,178,0.12)', bannerDark: 'rgba(8,145,178,0.08)', icon: 'bi-diagram-3',
    },
    {
        id: 6, country: '🇲🇱', category: 'formation', tag: 'Agence Gouvernementale',
        title: 'Renforcement des capacités — Agence Emploi Mali',
        desc: 'Formation de 300 agents en informatique bureautique, cybersécurité de base et outils de productivité numérique.',
        metrics: [{ v: '300', l: 'Agents formés' }, { v: '85%', l: 'Taux de réussite' }, { v: '2 mois', l: 'Déploiement' }],
        tags: ['Bureautique', 'Cybersécurité', 'Gouvernement', 'Mali'],
        color: '#DC2626', bgLight: '#FEF2F2', bgDark: 'rgba(220,38,38,0.12)', bannerDark: 'rgba(220,38,38,0.08)', icon: 'bi-shield-check',
    },
];

const filtered = computed(() =>
    activeFilter.value === 'all'
        ? caseStudies
        : caseStudies.filter(c => c.category === activeFilter.value)
);

const lightboxPhoto = ref<string | null>(null);

// Dark-mode styles
const sectionGrayBg  = computed(() => isDark.value ? '#0B1437' : '#F8FAFC');
const sectionWhiteBg = computed(() => isDark.value ? '#0D1526' : '#FFFFFF');
const titleColor     = computed(() => isDark.value ? '#F1F5F9' : '#0B1437');
const descColor      = computed(() => isDark.value ? '#94A3B8' : '#637084');
const pillInactive   = computed(() => isDark.value
    ? 'background: rgba(255,255,255,0.06); color: #94A3B8; border: 1px solid rgba(255,255,255,0.08);'
    : 'background: white; color: #475569; border: 1px solid #E2E8F0;');
const tagStyle       = computed(() => isDark.value
    ? 'background: rgba(255,255,255,0.06); color: #94A3B8; border-color: rgba(255,255,255,0.1);'
    : 'background: #F8FAFC; color: #475569; border-color: #E2E8F0;');
const albumEmptyBg   = computed(() => isDark.value ? '#1E2D50' : '#F1F5F9');
</script>

<template>
    <Head title="Nos Projets & Réalisations — GlobalTECH EDUCATION Africa" />
    <LayoutFront>

        <!-- Hero -->
        <section class="py-28 relative overflow-hidden" style="background: linear-gradient(rgba(255,255,255,0.025) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.025) 1px,transparent 1px),linear-gradient(135deg,#060C22 0%,#0B1437 42%,#0E2060 72%,#091830 100%); background-size:60px 60px,60px 60px,100% 100%;">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
                <div class="max-w-3xl">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-6"
                        style="background: rgba(232, 160, 32, 0.15); color: #E8A020; border: 1px solid rgba(232, 160, 32, 0.3);">
                        <i class="bi bi-folder2-open-fill"></i>
                        Portfolio & Réalisations
                    </div>
                    <h1 class="text-5xl lg:text-6xl font-black text-white mb-6 leading-tight" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                        Nos projets <span class="gt-text-gradient">à travers l'Afrique</span>
                    </h1>
                    <p class="text-xl text-white/70 leading-relaxed">
                        De Abidjan à Douala, en passant par Dakar et Bamako — découvrez les projets à impact que nous avons conduits pour nos clients.
                    </p>
                </div>
            </div>
        </section>

        <!-- Case Studies -->
        <section class="py-24" :style="`background: ${sectionGrayBg};`">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">

                <!-- Category Filter -->
                <div class="flex flex-wrap gap-2 mb-12 justify-center">
                    <button v-for="f in filters" :key="f.key" @click="activeFilter = f.key"
                        class="px-5 py-2.5 rounded-full text-sm font-semibold transition-all duration-200"
                        :style="activeFilter === f.key
                            ? 'background: #0B1437; color: white; box-shadow: 0 4px 12px rgba(11,20,55,0.4);'
                            : pillInactive">
                        {{ f.label }}
                    </button>
                </div>

                <!-- Project Cards -->
                <div class="grid lg:grid-cols-2 gap-8">
                    <div v-for="proj in filtered" :key="proj.id" class="gt-card overflow-hidden group">
                        <!-- Banner -->
                        <div class="h-56 relative flex items-center justify-center"
                            :style="`background: linear-gradient(135deg, ${isDark ? proj.bannerDark : proj.bgLight}, ${isDark ? '#131F36' : 'white'});`">
                            <i :class="['bi', proj.icon, 'text-7xl opacity-15']" :style="`color: ${proj.color};`"></i>
                            <div class="absolute inset-0 flex items-end p-5">
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl">{{ proj.country }}</span>
                                    <span class="text-xs font-bold px-2.5 py-1 rounded-full text-white" :style="`background: ${proj.color};`">{{ proj.tag }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="p-8">
                            <h3 class="text-xl font-black mb-3 leading-tight" :style="`color: ${titleColor}; font-family: 'Plus Jakarta Sans', sans-serif;`">
                                {{ proj.title }}
                            </h3>
                            <p class="text-sm leading-relaxed mb-6" :style="`color: ${descColor};`">{{ proj.desc }}</p>

                            <!-- Metrics -->
                            <div class="grid grid-cols-3 gap-4 p-4 rounded-2xl mb-5"
                                :style="`background: ${isDark ? proj.bgDark : proj.bgLight};`">
                                <div v-for="m in proj.metrics" :key="m.l" class="text-center">
                                    <div class="text-xl font-black" :style="`color: ${proj.color};`">{{ m.v }}</div>
                                    <div class="text-xs" :style="`color: ${descColor};`">{{ m.l }}</div>
                                </div>
                            </div>

                            <!-- Tags -->
                            <div class="flex flex-wrap gap-2">
                                <span v-for="tag in proj.tags" :key="tag"
                                    class="text-xs px-2.5 py-1 rounded-full border font-medium"
                                    :style="tagStyle">
                                    {{ tag }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Photo Gallery -->
        <section v-if="albums.length" class="py-24" :style="`background: ${sectionWhiteBg};`">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="text-center mb-12">
                    <div class="gt-section-label justify-center">Galerie</div>
                    <h2 class="gt-section-title text-4xl mb-4">Nos événements et formations</h2>
                </div>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="album in albums" :key="album.id" class="gt-card overflow-hidden group">
                        <div class="h-48 overflow-hidden relative">
                            <img v-if="album.cover?.image_url || album.photos?.[0]?.image_url"
                                :src="album.cover?.image_url ?? album.photos[0]?.image_url"
                                :alt="album.title"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105 cursor-pointer"
                                @click="lightboxPhoto = album.cover?.image_url ?? album.photos?.[0]?.image_url ?? null" />
                            <div v-else class="w-full h-full flex items-center justify-center" :style="`background: ${albumEmptyBg};`">
                                <i class="bi bi-images text-4xl" style="color: #CBD5E1;"></i>
                            </div>
                            <div v-if="album.photos?.length > 1"
                                class="absolute bottom-2 right-2 px-2 py-1 rounded-full text-xs font-bold text-white"
                                style="background: rgba(0,0,0,0.6);">
                                +{{ album.photos.length - 1 }} photos
                            </div>
                        </div>
                        <div class="p-5">
                            <h3 class="font-bold mb-1 text-sm" :style="`color: ${titleColor};`">{{ album.title }}</h3>
                            <p v-if="album.description" class="text-xs line-clamp-2" :style="`color: ${descColor};`">{{ album.description }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Lightbox -->
        <Transition name="fade">
            <div v-if="lightboxPhoto" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/90"
                @click="lightboxPhoto = null">
                <img :src="lightboxPhoto" class="max-w-5xl max-h-[90vh] object-contain rounded-2xl shadow-2xl" />
                <button class="absolute top-4 right-4 w-10 h-10 rounded-full bg-white/10 text-white flex items-center justify-center hover:bg-white/20 transition-colors">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </Transition>

        <!-- CTA -->
        <section class="py-20 relative overflow-hidden" style="background: linear-gradient(135deg, #0B1437, #0E2060);">
            <div class="absolute inset-0 gt-grid-pattern opacity-20"></div>
            <div class="max-w-3xl mx-auto px-6 text-center relative z-10">
                <h2 class="text-3xl lg:text-4xl font-black text-white mb-4" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                    Votre projet mérite les meilleurs experts
                </h2>
                <p class="text-white/70 mb-8">
                    Contactez-nous pour discuter de votre projet et recevoir une proposition adaptée à vos besoins.
                </p>
                <div class="flex flex-wrap gap-4 justify-center">
                    <Link href="/contact" class="gt-btn-gold px-8 py-4 rounded-xl font-black">
                        <i class="bi bi-envelope-fill"></i> Nous contacter
                    </Link>
                    <Link href="/services" class="gt-btn-outline px-8 py-4 rounded-xl">
                        <i class="bi bi-briefcase"></i> Nos expertises
                    </Link>
                </div>
            </div>
        </section>

    </LayoutFront>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
