<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import { useDarkMode } from '@/composables/useDarkMode';
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

interface Service {
    id: number; title: string; description?: string; icon?: string;
    image_path?: string; image_url?: string; subtitle?: string;
    items?: { id: number; title: string; description?: string }[];
}

const props = defineProps<{ services?: Service[] }>();
const services = ref<Service[]>(props.services ?? []);
const { isDark } = useDarkMode();

onMounted(async () => {
    if (!services.value.length) {
        try {
            const res = await axios.get('/api/services/all-with-items');
            if (res.data) services.value = res.data;
        } catch {}
    }
});

const consultingOffers = [
    {
        icon: 'bi-lightbulb-fill', color: '#2563EB', bgLight: '#EFF6FF', bgDark: 'rgba(37,99,235,0.15)',
        title: 'Conseil Stratégique IT',
        desc: 'Accompagnement stratégique en transformation digitale, architecture IT et gouvernance des systèmes d\'information.',
        deliverables: ['Audit SI', 'Feuille de route digitale', 'Gouvernance IT', 'KPIs & tableaux de bord'],
    },
    {
        icon: 'bi-diagram-3-fill', color: '#16A34A', bgLight: '#F0FDF4', bgDark: 'rgba(22,163,74,0.15)', featured: true,
        title: 'Management de Projet',
        desc: 'Pilotage de projets complexes selon les standards PMI, PRINCE2 et méthodes agiles. De la planification à la livraison.',
        deliverables: ['PMO externalisé', 'Plan de projet', 'Gestion des risques', 'Reporting exécutif'],
    },
    {
        icon: 'bi-people-fill', color: '#D97706', bgLight: '#FFFBEB', bgDark: 'rgba(217,119,6,0.15)',
        title: 'Capital Humain & RH',
        desc: 'Stratégie RH, gestion des talents, plans de développement des compétences et transformation organisationnelle.',
        deliverables: ['Audit RH', 'Plans de formation', 'Évaluation des compétences', 'Change management'],
    },
    {
        icon: 'bi-shield-check', color: '#7C3AED', bgLight: '#F5F3FF', bgDark: 'rgba(124,58,237,0.15)',
        title: 'Cybersécurité & Conformité',
        desc: 'Évaluation des risques cyber, mise en conformité réglementaire et implémentation des bonnes pratiques de sécurité.',
        deliverables: ['Audit sécurité', 'RSSI externalisé', 'ISO 27001', 'Politique de sécurité'],
    },
    {
        icon: 'bi-globe-africa', color: '#0891B2', bgLight: '#F0FDFA', bgDark: 'rgba(8,145,178,0.15)',
        title: 'Projets Institutionnels',
        desc: 'Réponse aux appels d\'offres, implémentation de projets pour ministères, organisations internationales et ONG.',
        deliverables: ['Ingénierie de projet', 'Coordination d\'équipes', 'Reporting bailleurs', 'Évaluation d\'impact'],
    },
    {
        icon: 'bi-graph-up-arrow', color: '#DC2626', bgLight: '#FEF2F2', bgDark: 'rgba(220,38,38,0.15)',
        title: 'Audit & Performance',
        desc: 'Diagnostic organisationnel, audit des processus, identification des leviers de performance et plan d\'optimisation.',
        deliverables: ['Audit opérationnel', 'Benchmarking', 'Plan d\'amélioration', 'Suivi des actions'],
    },
];

const process = [
    { step: '01', icon: 'bi-telephone', title: 'Diagnostic', desc: 'Échange initial pour comprendre vos enjeux et définir vos objectifs.' },
    { step: '02', icon: 'bi-file-earmark-text', title: 'Proposition', desc: 'Élaboration d\'une offre sur mesure avec planning et budget détaillés.' },
    { step: '03', icon: 'bi-people', title: 'Intervention', desc: 'Déploiement de notre équipe d\'experts sur le terrain ou à distance.' },
    { step: '04', icon: 'bi-bar-chart', title: 'Résultats', desc: 'Livraison des livrables, formation des équipes et transfert de compétences.' },
];

// Dark-mode styles
const sectionWhiteBg  = computed(() => isDark.value ? '#0D1526' : '#FFFFFF');
const sectionGrayBg   = computed(() => isDark.value ? '#0B1437' : '#F8FAFC');
const titleColor      = computed(() => isDark.value ? '#F1F5F9' : '#0B1437');
const descColor       = computed(() => isDark.value ? '#94A3B8' : '#637084');
const labelColor      = computed(() => isDark.value ? '#64748B' : '#637084');
const serviceIconBg   = computed(() => isDark.value ? 'rgba(37,99,235,0.15)' : '#EFF6FF');
const ringOffset      = computed(() => isDark.value ? 'ring-offset-[#0B1437]' : 'ring-offset-white');
</script>

<template>
    <Head title="Consulting & Conseil — GlobalTECH EDUCATION Africa" />
    <LayoutFront>

        <!-- Hero -->
        <section class="py-28 relative overflow-hidden" style="background: linear-gradient(rgba(255,255,255,0.025) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.025) 1px,transparent 1px),linear-gradient(135deg,#060C22 0%,#0B1437 42%,#0E2060 72%,#091830 100%); background-size:60px 60px,60px 60px,100% 100%;">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-6"
                            style="background: rgba(232, 160, 32, 0.15); color: #E8A020; border: 1px solid rgba(232, 160, 32, 0.3);">
                            <i class="bi bi-briefcase-fill"></i>
                            Conseil & Expertise
                        </div>
                        <h1 class="text-5xl lg:text-6xl font-black text-white mb-6 leading-tight" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                            Consulting <span class="gt-text-gradient">à impact</span>
                        </h1>
                        <p class="text-xl text-white/70 leading-relaxed mb-8">
                            Nos experts vous accompagnent dans vos projets de transformation, de renforcement des capacités et d'implémentation de systèmes en Afrique.
                        </p>
                        <div class="flex flex-wrap gap-4">
                            <Link href="/contact" class="gt-btn-gold rounded-xl px-8 py-3.5">
                                <i class="bi bi-telephone-fill"></i> Demander un devis
                            </Link>
                            <Link :href="route('portfolio')" class="gt-btn-outline rounded-xl px-8 py-3.5">
                                <i class="bi bi-folder2-open"></i> Voir nos projets
                            </Link>
                        </div>
                    </div>
                    <div class="hidden lg:grid grid-cols-2 gap-4">
                        <div v-for="item in [
                            { n: '200+', l: 'Missions réalisées', icon: 'bi-briefcase' },
                            { n: '12', l: 'Pays d\'intervention', icon: 'bi-globe-africa' },
                            { n: '50+', l: 'Experts consultants', icon: 'bi-people' },
                            { n: '98%', l: 'Satisfaction clients', icon: 'bi-star' },
                        ]" :key="item.l"
                            class="p-5 rounded-2xl border text-center"
                            style="background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.1);">
                            <i :class="['bi', item.icon, 'text-2xl mb-2 block']" style="color: #E8A020;"></i>
                            <div class="text-2xl font-black text-white">{{ item.n }}</div>
                            <div class="text-xs text-white/60">{{ item.l }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Dynamic services from DB -->
        <section v-if="services.length" class="py-24" :style="`background: ${sectionWhiteBg};`">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="text-center mb-16">
                    <div class="gt-section-label justify-center">Nos Services</div>
                    <h2 class="gt-section-title text-4xl mb-4">Ce que nous offrons</h2>
                </div>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="service in services" :key="service.id" class="gt-card p-8 group">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-6 transition-transform duration-300 group-hover:scale-110"
                            :style="`background: ${serviceIconBg};`">
                            <i v-if="service.icon" :class="['bi', service.icon, 'text-2xl']" style="color: #2563EB;"></i>
                            <i v-else class="bi bi-briefcase text-2xl" style="color: #2563EB;"></i>
                        </div>
                        <h3 class="text-lg font-bold mb-2" :style="`color: ${titleColor};`">{{ service.title }}</h3>
                        <p v-if="service.subtitle" class="text-xs font-semibold mb-2" style="color: #E8A020;">{{ service.subtitle }}</p>
                        <p v-if="service.description" class="text-sm leading-relaxed mb-4" :style="`color: ${descColor};`" v-html="service.description"></p>
                        <ul v-if="service.items?.length" class="space-y-1.5">
                            <li v-for="item in service.items" :key="item.id" class="flex items-center gap-2 text-xs" :style="`color: ${descColor};`">
                                <i class="bi bi-check-lg" style="color: #16A34A;"></i>
                                {{ item.title }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Consulting Offers -->
        <section class="py-24" :style="`background: ${sectionGrayBg};`">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="text-center mb-16">
                    <div class="gt-section-label justify-center">Nos Expertises</div>
                    <h2 class="gt-section-title text-4xl lg:text-5xl mb-4">Nos offres de conseil</h2>
                    <p class="text-lg max-w-2xl mx-auto" :style="`color: ${descColor};`">
                        Des solutions sur mesure pour entreprises, institutions et organisations internationales.
                    </p>
                </div>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="offer in consultingOffers" :key="offer.title"
                        class="gt-card p-8 group"
                        :class="offer.featured ? `ring-2 ring-offset-2 ring-green-400 ${ringOffset}` : ''">
                        <div v-if="offer.featured" class="text-xs font-bold px-2.5 py-1 rounded-full text-white mb-4 inline-block"
                            :style="`background: ${offer.color};`">
                            <i class="bi bi-star-fill mr-1"></i> Le plus demandé
                        </div>
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-6 transition-transform duration-300 group-hover:scale-110"
                            :style="`background: ${isDark ? offer.bgDark : offer.bgLight};`">
                            <i :class="['bi', offer.icon, 'text-2xl']" :style="`color: ${offer.color};`"></i>
                        </div>
                        <h3 class="text-lg font-bold mb-3" :style="`color: ${titleColor};`">{{ offer.title }}</h3>
                        <p class="text-sm leading-relaxed mb-5" :style="`color: ${descColor};`">{{ offer.desc }}</p>
                        <div class="space-y-1.5">
                            <p class="text-xs font-bold uppercase tracking-wide mb-2" :style="`color: ${labelColor};`">Livrables</p>
                            <div v-for="d in offer.deliverables" :key="d" class="flex items-center gap-2 text-xs" :style="`color: ${descColor};`">
                                <i class="bi bi-check-circle-fill text-xs" :style="`color: ${offer.color};`"></i>
                                {{ d }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Process -->
        <section class="py-24" :style="`background: ${sectionWhiteBg};`">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="text-center mb-16">
                    <div class="gt-section-label justify-center">Notre Approche</div>
                    <h2 class="gt-section-title text-4xl mb-4">Comment nous travaillons</h2>
                </div>
                <div class="grid md:grid-cols-4 gap-6 relative">
                    <div class="hidden md:block absolute top-10 left-[12.5%] right-[12.5%] h-0.5" style="background: linear-gradient(90deg, #E8A020, #2563EB);"></div>
                    <div v-for="(step, i) in process" :key="step.step" class="relative text-center">
                        <div class="w-20 h-20 rounded-2xl mx-auto mb-4 flex flex-col items-center justify-center relative z-10 shadow-lg"
                            :style="i % 2 === 0 ? 'background: #0B1437;' : 'background: #E8A020;'">
                            <i :class="['bi', step.icon, 'text-xl text-white mb-1']"></i>
                            <span class="text-xs text-white/70">{{ step.step }}</span>
                        </div>
                        <h3 class="font-bold mb-2" :style="`color: ${titleColor};`">{{ step.title }}</h3>
                        <p class="text-sm" :style="`color: ${descColor};`">{{ step.desc }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="py-20 relative overflow-hidden" style="background: linear-gradient(135deg, #0B1437, #0E2060);">
            <div class="absolute inset-0 gt-grid-pattern opacity-20"></div>
            <div class="max-w-3xl mx-auto px-6 text-center relative z-10">
                <h2 class="text-3xl lg:text-4xl font-black text-white mb-4" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                    Un projet en Afrique ?
                </h2>
                <p class="text-white/70 mb-8 text-lg">
                    Nos équipes sont prêtes à intervenir dans 12 pays. Décrivez-nous votre besoin et recevez une proposition sous 48h.
                </p>
                <div class="flex flex-wrap gap-4 justify-center">
                    <Link href="/contact" class="gt-btn-gold px-8 py-4 rounded-xl text-base font-black">
                        <i class="bi bi-send-fill"></i> Demander un devis
                    </Link>
                </div>
            </div>
        </section>

    </LayoutFront>
</template>
