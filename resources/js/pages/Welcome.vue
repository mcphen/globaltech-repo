<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import { useDarkMode } from '@/composables/useDarkMode';
import axios from 'axios';

const { isDark } = useDarkMode();
const sectionBgAlt = computed(() => isDark.value ? '#111F38' : '#F8FAFC');
const sectionBgWhite = computed(() => isDark.value ? '#0D1526' : '#FFFFFF');
const cardBg = computed(() => isDark.value ? '#131F36' : '#FFFFFF');
const textBody = computed(() => isDark.value ? '#94A3B8' : '#637084');
const textHeading = computed(() => isDark.value ? '#F1F5F9' : '#0B1437');

const page = usePage();
const contactSettings = computed<any>(() => page.props.contactSettings);

interface FormationCategory {
    name: string; slug: string; icon?: string; color?: string; background?: string;
}

interface Formation {
    id: number; uuid: string; slug: string; title: string;
    description?: string; image_path?: string; image_url?: string;
    date?: string; start_date?: string; end_date?: string; date_mode?: string;
    duration_hours?: number; price?: number; currency?: string;
    category?: FormationCategory | null;
}

interface Testimonial {
    id: number; name: string; position?: string; company?: string;
    content: string; rating?: number; image_path?: string; image_url?: string;
}

interface Partner {
    id: number; name: string; logo_path?: string; logo_url?: string; website?: string;
}

interface Actualite {
    id: number; title: string; excerpt?: string; image_path?: string; image_url?: string;
    created_at: string; slug?: string;
}

interface Pillar {
    icon?: string; category: string; title: string;
    color: string; bg: string; description: string;
    certifications: string[]; href: string; featured: boolean;
}

const formations = ref<Formation[]>([]);
const testimonials = ref<Testimonial[]>([]);
const partners = ref<Partner[]>([]);
const latestPosts = ref<Actualite[]>([]);
const pillars = ref<Pillar[]>([]);

const statsAnimated = ref(false);
const statsRef = ref<HTMLElement | null>(null);

const stats = [
    { value: 500, suffix: '+', label: 'Entreprises formées', icon: 'bi-building' },
    { value: 5000, suffix: '+', label: 'Professionnels certifiés', icon: 'bi-people' },
    { value: 12, suffix: '', label: 'Pays en Afrique', icon: 'bi-globe-africa' },
    { value: 15, suffix: '+', label: "Années d'expertise", icon: 'bi-award' },
];

const displayStats = ref(stats.map(s => ({ ...s, current: 0 })));

const animateCounter = (index: number, target: number) => {
    const duration = 2000;
    const steps = 60;
    const increment = target / steps;
    let current = 0;
    const timer = setInterval(() => {
        current = Math.min(current + increment, target);
        displayStats.value[index].current = Math.floor(current);
        if (current >= target) clearInterval(timer);
    }, duration / steps);
};


const whyUs = [
    { icon: 'bi-patch-check-fill', color: '#2563EB', title: 'Certifications reconnues', desc: 'Toutes nos formations débouchent sur des certifications mondialement reconnues.' },
    { icon: 'bi-people-fill', color: '#16A34A', title: 'Experts praticiens', desc: 'Nos formateurs sont des professionnels actifs avec une expérience terrain de 10+ ans.' },
    { icon: 'bi-building-fill', color: '#D97706', title: 'Formation corporate', desc: 'Solutions B2B sur-mesure pour former vos équipes directement en entreprise ou à distance.' },
    { icon: 'bi-globe-africa', color: '#7C3AED', title: 'Présence panafricaine', desc: 'Bureaux dans 12 pays africains avec une qualité de formation uniforme et internationale.' },
    { icon: 'bi-graph-up-arrow', color: '#0891B2', title: 'ROI mesurable', desc: 'Nos clients constatent en moyenne +35% de productivité après certification de leurs équipes.' },
    //{ icon: 'bi-shield-fill-check', color: '#DC2626', title: 'Accrédité PMI', desc: 'Centre de formation accrédité par PMI, Pearson VUE et les grandes instances internationales.' },
];

onMounted(async () => {
    try {
        const [f, t, p, a, cats] = await Promise.all([
            axios.get('/api/formations/featured').catch(() => ({ data: [] })),
            axios.get('/api/testimonials/latest').catch(() => ({ data: [] })),
            axios.get('/api/partners/list').catch(() => ({ data: [] })),
            axios.get('/api/actualites/latest').catch(() => ({ data: [] })),
            axios.get('/api/formation-categories').catch(() => ({ data: [] })),
        ]);
        formations.value = f.data?.slice?.(0, 6) ?? [];
        testimonials.value = t.data?.slice?.(0, 3) ?? [];
        partners.value = p.data?.slice?.(0, 8) ?? [];
        latestPosts.value = a.data?.slice?.(0, 3) ?? [];
        pillars.value = cats.data ?? [];
    } catch {}

    // Intersection Observer for stats animation
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !statsAnimated.value) {
                statsAnimated.value = true;
                stats.forEach((s, i) => animateCounter(i, s.value));
            }
        });
    }, { threshold: 0.3 });

    if (statsRef.value) observer.observe(statsRef.value);
});

const formatDate = (d: string) => d ? new Date(d).toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' }) : '';
</script>

<template>
    <Head title="GlobalTECH EDUCATION Africa — Formations Certifiantes en Afrique" />
    <LayoutFront>

        <!-- ══════════════════════════════════════
             HERO SECTION
        ══════════════════════════════════════ -->
        <section class="relative min-h-screen flex items-center overflow-hidden"
            style="background: linear-gradient(rgba(255,255,255,0.025) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.025) 1px,transparent 1px),linear-gradient(135deg,#060C22 0%,#0B1437 42%,#0E2060 72%,#091830 100%); background-size:60px 60px,60px 60px,100% 100%;">
            <!-- Floating orbs -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute w-96 h-96 rounded-full blur-3xl opacity-20 animate-float-slow"
                    style="background: radial-gradient(circle, #2563EB, transparent); top: -10%; right: 10%;"></div>
                <div class="absolute w-64 h-64 rounded-full blur-3xl opacity-15 animate-float-medium"
                    style="background: radial-gradient(circle, #E8A020, transparent); bottom: 20%; left: 5%;"></div>
                <div class="absolute w-48 h-48 rounded-full blur-3xl opacity-10 animate-float-slow"
                    style="background: radial-gradient(circle, #7C3AED, transparent); top: 50%; right: 30%; animation-delay: -2s;"></div>
            </div>

            <div class="max-w-7xl mx-auto px-6 lg:px-8 py-24 relative z-10">
                <div class="grid lg:grid-cols-2 gap-16 items-center">

                    <!-- Left: Text -->
                    <div>
                        <!-- Eyebrow -->
                        <div class="flex items-center gap-3 mb-6">
                            <div class="flex items-center gap-2 px-3 py-1.5 rounded-full border text-xs font-bold tracking-widest uppercase"
                                style="background: rgba(232, 160, 32, 0.1); border-color: rgba(232, 160, 32, 0.3); color: #F5BC4A;">
                                <span class="w-2 h-2 rounded-full animate-pulse" style="background: #E8A020;"></span>
                                Leader dans les formations certifiantes & consulting en Afrique
                            </div>
                        </div>

                        <h1 class="text-5xl lg:text-6xl xl:text-7xl font-black leading-tight tracking-tight mb-6"
                            style="font-family: 'Plus Jakarta Sans', sans-serif; color: white;">
                            Certifiez
                            <span class="block gt-text-gradient">l'Excellence</span>
                            <span class="text-white/80">de vos équipes.</span>
                        </h1>

                        <p class="text-lg text-white/70 leading-relaxed mb-8 max-w-xl">
                            GLOBALTECH EDUCATION Africa est le leader panafricain des formations certifiantes en Technologie Numérique, en Management et en Leadership.
                            <strong class="text-white/90">+5 000 professionnels certifiés</strong> dans 12 pays.
                        </p>

                        <div class="flex flex-wrap gap-4 mb-10">
                            <Link :href="route('formations')" class="gt-btn-gold text-base px-7 py-3.5 rounded-xl">
                                <i class="bi bi-mortarboard-fill"></i>
                                Voir les formations
                            </Link>
                            <Link href="/contact" class="gt-btn-outline text-base px-7 py-3.5 rounded-xl">
                                <i class="bi bi-building"></i>
                                Solution entreprise
                            </Link>
                        </div>

                        <!-- Trust badges-->
                        <div class="flex flex-wrap items-center gap-4">
                            <div v-for="badge in ['CISCO', 'EC-COUNCIL', 'ISC²']" :key="badge"
                                class="flex items-center gap-2 text-xs font-semibold text-white/60">
                                <i class="bi bi-patch-check-fill text-yellow-400"></i>
                                {{ badge }}
                            </div>
                        </div> 
                    </div>

                    <!-- Right: Feature Cards -->
                    <div class="hidden lg:grid grid-cols-2 gap-4">
                        <div v-for="(p, i) in pillars" :key="p.title"
                            class="p-5 rounded-2xl backdrop-blur-sm border transition-all duration-300 hover:scale-105 cursor-pointer"
                                style="background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.1);"
                            @click="$inertia?.visit(p.href)">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3"
                                :style="`background: ${p.color}25;`">
                                <i :class="['bi', p.icon, 'text-lg']" :style="`color: ${p.color};`"></i>
                            </div>
                            
                            <h3 class="text-sm font-bold text-white mb-2">{{ p.title }}</h3>
                            
                        </div>

                        <!-- Stats mini card -->
                        <div class="col-span-2 p-5 rounded-2xl border flex items-center justify-between"
                            style="background: rgba(232, 160, 32, 0.08); border-color: rgba(232, 160, 32, 0.2);">
                            <div v-for="s in [{ v: '12', l: 'Pays' }, { v: '500+', l: 'Entreprises' }, { v: '5K+', l: 'Certifiés' }]" :key="s.l" class="text-center">
                                <div class="text-2xl font-black" style="color: #E8A020;">{{ s.v }}</div>
                                <div class="text-xs text-white/60">{{ s.l }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scroll indicator -->
            <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 animate-bounce">
                <div class="w-5 h-8 rounded-full border-2 border-white/30 flex justify-center pt-1.5">
                    <div class="w-1 h-2 rounded-full bg-white/60"></div>
                </div>
            </div>
        </section>

        <!-- ══════════════════════════════════════
             STATS BAR
        ══════════════════════════════════════ -->
        <section ref="statsRef" class="py-16 border-b" :style="`background: ${sectionBgWhite}; border-color: ${isDark ? 'rgba(255,255,255,0.07)' : '#F1F5F9'};`">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
                    <div v-for="(stat, i) in displayStats" :key="i" class="text-center group">
                        <div class="w-14 h-14 rounded-2xl mx-auto mb-4 flex items-center justify-center transition-all duration-300 group-hover:scale-110"
                            style="background: linear-gradient(135deg, #EFF6FF, #DBEAFE);">
                            <i :class="['bi', stat.icon, 'text-xl']" style="color: #2563EB;"></i>
                        </div>
                        <div class="text-4xl font-black mb-1" :style="`color: ${textHeading}; font-family: 'Plus Jakarta Sans', sans-serif;`">
                            {{ stat.current.toLocaleString() }}<span style="color: #E8A020;">{{ stat.suffix }}</span>
                        </div>
                        <p class="text-sm font-medium" :style="`color: ${textBody};`">{{ stat.label }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══════════════════════════════════════
             PILLARS — 3 CORE DOMAINS
        ══════════════════════════════════════ -->
        <section class="py-24" :style="`background: ${sectionBgAlt};`">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="text-center mb-16">
                    <div class="gt-section-label justify-center">Nos Programmes</div>
                    <h2 class="gt-section-title text-4xl lg:text-5xl mb-4">
                        Trois piliers d'excellence
                    </h2>
                    <p class="text-lg max-w-2xl mx-auto" :style="`color: ${textBody};`">
                        Des programmes certifiants soigneusement conçus pour répondre aux besoins du marché africain et international.
                    </p>
                </div>

                <div class="grid sm:grid-cols-2 gap-8">
                    <div v-for="pillar in pillars" :key="pillar.title"
                        class="relative rounded-3xl overflow-hidden group hover:shadow-2xl transition-all duration-500 hover:-translate-y-2"
                        :class="pillar.featured ? 'ring-2 ring-offset-2' : ''"
                        :style="`background: ${cardBg}; border: 1px solid ${isDark ? 'rgba(255,255,255,0.08)' : '#F1F5F9'};`">

                        <!-- Featured banner -->
                        <div v-if="pillar.featured" class="absolute top-4 right-4 px-3 py-1 rounded-full text-xs font-bold text-white z-10"
                            :style="`background: ${pillar.color};`">
                            <i class="bi bi-star-fill mr-1"></i> Populaire
                        </div>

                        <!-- Top color bar -->
                        <div class="h-2" :style="`background: linear-gradient(90deg, ${pillar.color}, ${pillar.color}80);`"></div>

                        <div class="p-8">
                            <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-6 transition-transform duration-300 group-hover:scale-110"
                                :style="`background: ${pillar.bg};`">
                                <i :class="['bi', pillar.icon, 'text-2xl']" :style="`color: ${pillar.color};`"></i>
                            </div>

                            <div class="text-xs font-bold uppercase tracking-widest mb-2" :style="`color: ${pillar.color};`">
                                {{ pillar.category }}
                            </div>
                            <h3 class="text-2xl font-black mb-3" :style="`color: ${textHeading}; font-family: 'Plus Jakarta Sans', sans-serif;`">
                                {{ pillar.title }}
                            </h3>
                            <p class="text-sm leading-relaxed mb-6" :style="`color: ${textBody};`">{{ pillar.description }}</p>

                            <!-- Certifications -->
                            <div class="flex flex-wrap gap-2 mb-8">
                                <span v-for="cert in pillar.certifications" :key="cert"
                                    class="text-xs px-2.5 py-1 rounded-lg font-semibold"
                                    :style="`background: ${pillar.bg}; color: ${pillar.color};`">
                                    {{ cert }}
                                </span>
                            </div>

                            <Link :href="pillar.href"
                                class="w-full flex items-center justify-center gap-2 py-3 rounded-xl text-sm font-bold text-white transition-all duration-200 hover:shadow-lg group-hover:shadow-md"
                                :style="`background: ${pillar.color};`">
                                Explorer les formations
                                <i class="bi bi-arrow-right group-hover:translate-x-1 transition-transform duration-200"></i>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══════════════════════════════════════
             FEATURED FORMATIONS
        ══════════════════════════════════════ -->
        <section class="py-24" :style="`background: ${sectionBgWhite};`">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between mb-12 gap-4">
                    <div>
                        <div class="gt-section-label">Nos Formations</div>
                        <h2 class="gt-section-title text-4xl lg:text-5xl">
                            Formations à la une
                        </h2>
                    </div>
                    <Link :href="route('formations')" class="gt-btn-outline-navy self-start lg:self-auto">
                        Voir tout le catalogue
                        <i class="bi bi-arrow-right"></i>
                    </Link>
                </div>

                <!-- Real formation cards -->
                <div v-if="formations.length" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <Link v-for="f in formations" :key="f.id"
                        :href="route('formations.show', { slug: f.slug })"
                        class="group flex flex-col overflow-hidden rounded-2xl transition-all duration-300 hover:-translate-y-1 hover:shadow-xl"
                        :style="`background: ${cardBg}; border: 1px solid ${isDark ? 'rgba(255,255,255,0.08)' : '#F1F5F9'};`">

                        <!-- Image / Thumbnail -->
                        <div class="relative h-52 overflow-hidden flex items-center justify-center"
                            :style="`background: linear-gradient(135deg, ${f.category?.background || '#EFF6FF'}, ${isDark ? '#0D1526' : 'white'});`">
                            <img v-if="f.image_url" :src="f.image_url" :alt="f.title"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105 absolute inset-0" />
                            <i v-else :class="['bi', f.category?.icon || 'bi-mortarboard', 'text-6xl opacity-20 transition-transform duration-300 group-hover:scale-110']"
                                :style="`color: ${f.category?.color || '#2563EB'};`"></i>

                            <!-- Top badges -->
                            <div class="absolute top-3 left-3 flex items-center gap-2">
                                <span v-if="f.category" class="text-xs font-bold px-2.5 py-1 rounded-full text-white shadow-sm"
                                    :style="`background: ${f.category.color};`">
                                    <i v-if="f.category.icon" :class="['bi', f.category.icon, 'mr-1 text-[10px]']"></i>
                                    {{ f.category.name }}
                                </span>
                                <span v-else class="text-xs font-bold px-2.5 py-1 rounded-full text-white shadow-sm" style="background: #0B1437;">
                                    <i class="bi bi-mortarboard-fill mr-1 text-[10px]"></i>Formation
                                </span>
                            </div>

                            <!-- Price badge top-right 
                            <div class="absolute top-3 right-3">
                                <span v-if="f.price" class="text-xs font-black px-2.5 py-1 rounded-full text-white shadow-sm" style="background: rgba(11,20,55,0.85);">
                                    {{ new Intl.NumberFormat('fr-FR').format(Number(f.price)) }} {{ f.currency || 'XOF' }}
                                </span>
                                <span v-else class="text-xs font-bold px-2.5 py-1 rounded-full shadow-sm" style="background: rgba(22,163,74,0.9); color: white;">
                                    Sur devis
                                </span>
                            </div>-->

                            <!-- Gradient overlay -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/10 to-transparent pointer-events-none"></div>
                        </div>

                        <!-- Card body -->
                        <div class="p-5 flex flex-col flex-1">
                            <h3 class="text-base font-bold mb-2 line-clamp-2 transition-colors duration-200"
                                :class="isDark ? 'text-slate-100 group-hover:text-yellow-400' : 'text-[#0B1437] group-hover:text-[#E8A020]'"
                                style="font-family: 'Plus Jakarta Sans', sans-serif;">
                                {{ f.title }}
                            </h3>
                            <p v-if="f.description" class="text-xs leading-relaxed mb-4 line-clamp-2 flex-1"
                                :style="`color: ${textBody};`">
                                {{ f.description }}
                            </p>

                            <!-- Footer meta -->
                            <div class="flex items-center justify-between mt-auto pt-3"
                                :style="`border-top: 1px solid ${isDark ? 'rgba(255,255,255,0.07)' : '#F1F5F9'};`">
                                <div class="flex items-center gap-3">
                                    <span v-if="f.duration_hours" class="flex items-center gap-1 text-xs font-medium"
                                        :style="`color: ${textBody};`">
                                        <i class="bi bi-clock text-[10px]"></i>{{ f.duration_hours }}h
                                    </span>
                                    <span v-if="f.date || f.start_date" class="flex items-center gap-1 text-xs font-medium"
                                        :style="`color: ${textBody};`">
                                        <i class="bi bi-calendar3 text-[10px]"></i>
                                        {{ formatDate(f.date || f.start_date || '') }}
                                    </span>
                                </div>
                                <span class="text-xs font-bold flex items-center gap-1 transition-colors duration-200"
                                    :style="`color: ${f.category?.color || '#E8A020'};`">
                                    En savoir plus <i class="bi bi-arrow-right text-[10px] group-hover:translate-x-0.5 transition-transform"></i>
                                </span>
                            </div>
                        </div>
                    </Link>
                </div>

                <!-- Sample cards (empty state) -->
                <div v-else class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <Link v-for="sample in [
                        { title: 'Certification PMP® — Project Management Professional', duration: '35h', tag: 'PMP', color: '#16A34A', bg: '#F0FDF4', icon: 'bi-diagram-3', href: route('formations') },
                        { title: 'Cybersécurité — CompTIA Security+', duration: '40h', tag: 'IT Star', color: '#2563EB', bg: '#EFF6FF', icon: 'bi-shield-lock', href: route('formations') },
                        { title: 'Management & Leadership — MBA Executive', duration: '60h', tag: 'Management', color: '#D97706', bg: '#FFFBEB', icon: 'bi-people', href: route('formations') },
                        { title: 'AWS Solutions Architect Associate', duration: '45h', tag: 'IT Star', color: '#2563EB', bg: '#EFF6FF', icon: 'bi-cloud', href: route('formations') },
                        { title: 'CAPM® — Certified Associate PM', duration: '23h', tag: 'PMP', color: '#16A34A', bg: '#F0FDF4', icon: 'bi-graph-up', href: route('formations') },
                        { title: 'Lean Six Sigma Green Belt', duration: '40h', tag: 'Management', color: '#D97706', bg: '#FFFBEB', icon: 'bi-gear', href: route('formations') },
                    ]" :key="sample.title" :href="sample.href"
                        class="group flex flex-col overflow-hidden rounded-2xl transition-all duration-300 hover:-translate-y-1 hover:shadow-xl"
                        :style="`background: ${cardBg}; border: 1px solid ${isDark ? 'rgba(255,255,255,0.08)' : '#F1F5F9'};`">

                        <!-- Thumbnail -->
                        <div class="relative h-52 flex items-center justify-center overflow-hidden"
                            :style="`background: linear-gradient(135deg, ${sample.bg}, ${isDark ? '#0D1526' : 'white'});`">
                            <i :class="['bi', sample.icon, 'text-6xl opacity-20 transition-transform duration-300 group-hover:scale-110']"
                                :style="`color: ${sample.color};`"></i>

                            <!-- Category badge -->
                            <div class="absolute top-3 left-3">
                                <span class="text-xs font-bold px-2.5 py-1 rounded-full text-white shadow-sm"
                                    :style="`background: ${sample.color};`">
                                    {{ sample.tag }}
                                </span>
                            </div>

                            <!-- "Sur devis" badge -->
                            <div class="absolute top-3 right-3">
                                <span class="text-xs font-bold px-2.5 py-1 rounded-full text-white shadow-sm" style="background: rgba(22,163,74,0.9);">
                                    Sur devis
                                </span>
                            </div>

                            <div class="absolute inset-0 bg-gradient-to-t from-black/10 to-transparent pointer-events-none"></div>
                        </div>

                        <!-- Body -->
                        <div class="p-5 flex flex-col flex-1">
                            <!-- Certification badge -->
                            <div class="flex items-center gap-1.5 mb-2">
                                <i class="bi bi-patch-check-fill text-xs" :style="`color: ${sample.color};`"></i>
                                <span class="text-xs font-semibold" :style="`color: ${sample.color};`">Formation certifiante</span>
                            </div>

                            <h3 class="text-base font-bold mb-3 line-clamp-2 transition-colors duration-200"
                                :class="isDark ? 'text-slate-100 group-hover:text-yellow-400' : 'text-[#0B1437] group-hover:text-[#E8A020]'"
                                style="font-family: 'Plus Jakarta Sans', sans-serif;">
                                {{ sample.title }}
                            </h3>

                            <div class="flex items-center justify-between mt-auto pt-3"
                                :style="`border-top: 1px solid ${isDark ? 'rgba(255,255,255,0.07)' : '#F1F5F9'};`">
                                <span class="flex items-center gap-1 text-xs font-medium" :style="`color: ${textBody};`">
                                    <i class="bi bi-clock text-[10px]"></i>{{ sample.duration }}
                                </span>
                                <span class="text-xs font-bold flex items-center gap-1" :style="`color: ${sample.color};`">
                                    En savoir plus <i class="bi bi-arrow-right text-[10px] group-hover:translate-x-0.5 transition-transform"></i>
                                </span>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
        </section>

        <!-- ══════════════════════════════════════
             B2B CORPORATE SECTION
        ══════════════════════════════════════ -->
        <section class="py-24 relative overflow-hidden" style="background: linear-gradient(135deg, #0B1437 0%, #0E2060 50%, #091830 100%);">
            <!-- Background pattern -->
            <div class="absolute inset-0 gt-grid-pattern opacity-30"></div>
            <div class="absolute right-0 top-0 w-1/2 h-full pointer-events-none hidden lg:block">
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="w-96 h-96 rounded-full opacity-10" style="background: radial-gradient(circle, #E8A020, transparent);"></div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
                <div class="grid lg:grid-cols-2 gap-16 items-center">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-6"
                            style="background: rgba(232, 160, 32, 0.15); color: #E8A020; border: 1px solid rgba(232, 160, 32, 0.3);">
                            <i class="bi bi-building"></i>
                            Solution Entreprise
                        </div>

                        <h2 class="text-4xl lg:text-5xl font-black text-white mb-6 leading-tight" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                            Boostez la performance
                            <span class="gt-text-gradient block">de vos équipes.</span>
                        </h2>

                        <p class="text-lg text-white/70 leading-relaxed mb-8">
                            Vous êtes une entreprise, un groupe ou une institution ? Nous concevons des parcours de formation certifiante sur mesure pour vos collaborateurs, avec des solutions flexibles en présentiel, à distance ou en blended learning.
                        </p>

                        <ul class="space-y-4 mb-10">
                            <li v-for="item in [
                                'Programmes 100% personnalisés selon vos besoins métiers',
                                //'Formation en intra-entreprise sur votre site',
                               // 'Suivi individuel et reporting détaillé',
                               // 'Financement et prise en charge disponibles',
                                'Certifications reconnues à l\'international',
                            ]" :key="item" class="flex items-center gap-3 text-white/80 text-sm">
                                <div class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0"
                                    style="background: rgba(232, 160, 32, 0.2);">
                                    <i class="bi bi-check text-xs" style="color: #E8A020;"></i>
                                </div>
                                {{ item }}
                            </li>
                        </ul>

                        <div class="flex flex-wrap gap-4">
                            <Link href="/contact" class="gt-btn-gold rounded-xl px-8 py-3.5">
                                <i class="bi bi-telephone-fill"></i>
                                Demander un devis
                            </Link>
                            <Link href="/about" class="gt-btn-outline rounded-xl px-8 py-3.5">
                                En savoir plus
                            </Link>
                        </div>
                    </div>

                    <!-- Right: Corporate benefits grid -->
                    <div class="grid grid-cols-2 gap-4">
                        <div v-for="item in [
                            { icon: 'bi-calendar-check', title: 'Planning flexible', desc: 'Sessions adaptées à vos contraintes' },
                            { icon: 'bi-translate', title: 'Multilingue', desc: 'Français, Anglais' },
                            { icon: 'bi-laptop', title: 'E-Learning', desc: 'Plateforme LMS intégrée' },
                            { icon: 'bi-bar-chart', title: 'ROI Garanti', desc: 'Suivi KPI et tableaux de bord' },
                        ]" :key="item.title"
                            class="p-5 rounded-2xl border backdrop-blur-sm"
                            style="background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.1);">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3"
                                style="background: rgba(232, 160, 32, 0.15);">
                                <i :class="['bi', item.icon, 'text-lg']" style="color: #E8A020;"></i>
                            </div>
                            <h4 class="text-sm font-bold text-white mb-1">{{ item.title }}</h4>
                            <p class="text-xs text-white/60">{{ item.desc }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══════════════════════════════════════
             WHY CHOOSE US
        ══════════════════════════════════════ -->
        <section class="py-24" :style="`background: ${sectionBgAlt};`">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="text-center mb-16">
                    <div class="gt-section-label justify-center">Pourquoi Nous</div>
                    <h2 class="gt-section-title text-4xl lg:text-5xl mb-4">L'excellence à chaque étape</h2>
                    <p class="text-lg max-w-2xl mx-auto" :style="`color: ${textBody};`">
                        Rejoignez les milliers de professionnels et d'entreprises qui font confiance à GLOBALTECH EDUCATION Africa.
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="item in whyUs" :key="item.title"
                        class="gt-card p-8 flex flex-col items-start group">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-6 transition-transform duration-300 group-hover:scale-110"
                            :style="`background: ${item.color}15;`">
                            <i :class="['bi', item.icon, 'text-2xl']" :style="`color: ${item.color};`"></i>
                        </div>
                        <h3 class="text-lg font-bold mb-2" :style="`color: ${textHeading};`">{{ item.title }}</h3>
                        <p class="text-sm leading-relaxed" :style="`color: ${textBody};`">{{ item.desc }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══════════════════════════════════════
             PORTFOLIO PROJECTS
        ══════════════════════════════════════ -->
        <section class="py-24" :style="`background: ${sectionBgWhite};`">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between mb-12 gap-4">
                    <div>
                        <div class="gt-section-label">Nos Réalisations</div>
                        <h2 class="gt-section-title text-4xl lg:text-5xl">
                            Des projets à impact
                        </h2>
                    </div>
                    <Link :href="route('portfolio')" class="gt-btn-outline-navy self-start lg:self-auto">
                        Voir tous les projets
                        <i class="bi bi-arrow-right"></i>
                    </Link>
                </div>

                <div class="grid lg:grid-cols-3 gap-6">
                    <div v-for="proj in [
                        { country: '🇨🇮', tag: 'Ministère', title: '', desc: '', icon: 'bi-laptop', color: '#2563EB', bg: '#EFF6FF' },
                        { country: '🇸🇳', tag: 'Corporate', title: '', desc: '', icon: 'bi-award', color: '#16A34A', bg: '#F0FDF4' },
                        { country: '🌍', tag: 'International', title: '', desc: '', icon: 'bi-globe', color: '#D97706', bg: '#FFFBEB' },
                    ]" :key="proj.title"
                        class="gt-card overflow-hidden group">
                        <div class="h-48 relative flex items-center justify-center" :style="`background: linear-gradient(135deg, ${proj.bg}, white);`">
                            <i :class="['bi', proj.icon, 'text-6xl opacity-20']" :style="`color: ${proj.color};`"></i>
                            <div class="absolute top-4 left-4 flex items-center gap-2">
                                <span class="text-lg">{{ proj.country }}</span>
                                <span class="text-xs font-bold px-2 py-1 rounded-full text-white" :style="`background: ${proj.color};`">{{ proj.tag }}</span>
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-base font-bold mb-2 line-clamp-2" :style="`color: ${textHeading};`">{{ proj.title }}</h3>
                            <p class="text-sm leading-relaxed" :style="`color: ${textBody};`">{{ proj.desc }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══════════════════════════════════════
             TESTIMONIALS
        ══════════════════════════════════════ -->
        <section class="py-24" :style="`background: ${sectionBgAlt};`">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="text-center mb-16">
                    <div class="gt-section-label justify-center">Témoignages</div>
                    <h2 class="gt-section-title text-4xl lg:text-5xl mb-4">Ils nous font confiance</h2>
                </div>

                <div v-if="testimonials.length" class="grid md:grid-cols-3 gap-6">
                    <div v-for="t in testimonials" :key="t.id" class="gt-card p-8">
                        <div class="flex mb-4 gap-0.5">
                            <i v-for="n in 5" :key="n" class="bi bi-star-fill text-sm" style="color: #E8A020;"></i>
                        </div>
                        <p class="text-sm leading-relaxed mb-6 italic" :style="`color: ${textBody};`">"{{ t.content }}"</p>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full overflow-hidden" style="background: #EFF6FF;">
                                <img v-if="t.image_url" :src="t.image_url" :alt="t.name" class="w-full h-full object-cover" />
                                <div v-else class="w-full h-full flex items-center justify-center font-bold text-blue-600">
                                    {{ t.name.charAt(0) }}
                                </div>
                            </div>
                            <div>
                                <p class="text-sm font-bold" :style="`color: ${textHeading};`">{{ t.name }}</p>
                                <p class="text-xs" :style="`color: ${textBody};`">{{ t.position || 'Professionnel certifié' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="grid md:grid-cols-3 gap-6">
                    <div v-for="t in [
                        { name: '', role: '', quote: '' },
                        { name: '', role: '', quote: '' },
                        { name: '', role: '', quote: '' },
                    ]" :key="t.name" class="gt-card p-8">
                        <div class="flex mb-4 gap-0.5">
                            <i v-for="n in 5" :key="n" class="bi bi-star-fill text-sm" style="color: #E8A020;"></i>
                        </div>
                        <p class="text-sm leading-relaxed mb-6 italic" :style="`color: ${textBody};`">"{{ t.quote }}"</p>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-white text-sm"
                                style="background: linear-gradient(135deg, #0B1437, #2563EB);">
                                {{ t.name.charAt(0) }}
                            </div>
                            <div>
                                <p class="text-sm font-bold" :style="`color: ${textHeading};`">{{ t.name }}</p>
                                <p class="text-xs" :style="`color: ${textBody};`">{{ t.role }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══════════════════════════════════════
             PARTNERS / CERTIFICATIONS
        ══════════════════════════════════════ -->
        <section class="py-16" :style="`background: ${sectionBgWhite}; border-top: 1px solid ${isDark ? 'rgba(255,255,255,0.07)' : '#F1F5F9'}; border-bottom: 1px solid ${isDark ? 'rgba(255,255,255,0.07)' : '#F1F5F9'};`">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <p class="text-center text-xs font-bold uppercase tracking-widest mb-10" style="color: #94A3B8;">
                    Partenaires officiels
                </p>
                <div class="flex flex-wrap items-center justify-center gap-8">
                    <div v-for="partner in [ 'Cisco', 'Microsoft',  'EC-COUNCIL']" :key="partner"
                        class="px-5 py-3 rounded-2xl text-sm font-bold transition-all duration-200 cursor-default"
                        :style="`border: 1px solid ${isDark ? 'rgba(255,255,255,0.12)' : '#E5E7EB'}; color: ${isDark ? '#94A3B8' : '#6B7280'};`">
                        {{ partner }}
                    </div>
                </div>
            </div>
        </section>

        <!-- ══════════════════════════════════════
             LATEST NEWS
        ══════════════════════════════════════ -->
        <section class="py-24" :style="`background: ${sectionBgAlt};`">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between mb-12 gap-4">
                    <div>
                        <div class="gt-section-label">Actualités</div>
                        <h2 class="gt-section-title text-4xl lg:text-5xl">Dernières nouvelles</h2>
                    </div>
                    <Link :href="route('blog')" class="gt-btn-outline-navy self-start lg:self-auto">
                        Toutes les actualités
                        <i class="bi bi-arrow-right"></i>
                    </Link>
                </div>

                <div v-if="latestPosts.length" class="grid md:grid-cols-3 gap-6">
                    <Link v-for="post in latestPosts" :key="post.id" :href="`/${post.id}/blog`" class="gt-card overflow-hidden group">
                        <div class="h-48 overflow-hidden" style="background: #F1F5F9;">
                            <img v-if="post.image_url" :src="post.image_url" :alt="post.title"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" />
                            <div v-else class="w-full h-full flex items-center justify-center">
                                <i class="bi bi-newspaper text-4xl" style="color: #CBD5E1;"></i>
                            </div>
                        </div>
                        <div class="p-6">
                            <p class="text-xs font-medium mb-2" style="color: #94A3B8;">
                                {{ formatDate(post.created_at) }}
                            </p>
                            <h3 class="text-base font-bold line-clamp-2 group-hover:text-blue-600 transition-colors" :style="`color: ${textHeading};`">{{ post.title }}</h3>
                        </div>
                    </Link>
                </div>

                <div v-else class="grid md:grid-cols-3 gap-6">
                    <div v-for="post in [
                        { date: 'Avril 2026', title: 'GLOBALTECH ouvre un nouveau centre de formation à Douala', tag: 'Expansion' },
                        { date: 'Mars 2026', title: '200 chefs de projet certifiés PMP® lors de la session Q1 2026', tag: 'Succès' },
                        { date: 'Mars 2026', title: 'Nouveau partenariat avec Microsoft pour les certifications Azure', tag: 'Partenariat' },
                    ]" :key="post.title" class="gt-card overflow-hidden group">
                        <div class="h-48 flex items-center justify-center" style="background: linear-gradient(135deg, #EFF6FF, #DBEAFE);">
                            <i class="bi bi-newspaper text-5xl opacity-20" style="color: #2563EB;"></i>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-xs px-2 py-0.5 rounded-full font-semibold" style="background: #EFF6FF; color: #2563EB;">{{ post.tag }}</span>
                                <span class="text-xs" style="color: #94A3B8;">{{ post.date }}</span>
                            </div>
                            <h3 class="text-base font-bold group-hover:text-blue-600 transition-colors" :style="`color: ${textHeading};`">{{ post.title }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══════════════════════════════════════
             FINAL CTA
        ══════════════════════════════════════ -->
        <section class="relative py-24 overflow-hidden" style="background: linear-gradient(135deg, #0B1437, #0E2060);">
            <div class="absolute inset-0 gt-grid-pattern opacity-20"></div>
            <div class="absolute -top-20 -right-20 w-80 h-80 rounded-full blur-3xl opacity-10" style="background: #E8A020;"></div>

            <div class="max-w-4xl mx-auto px-6 lg:px-8 text-center relative z-10">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-bold uppercase tracking-widest mb-6"
                    style="background: rgba(232, 160, 32, 0.15); color: #E8A020; border: 1px solid rgba(232, 160, 32, 0.3);">
                    <i class="bi bi-rocket-takeoff-fill"></i>
                    Démarrez aujourd'hui
                </div>

                <h2 class="text-4xl lg:text-5xl font-black text-white mb-6 leading-tight" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                    Prêt à certifier votre <span class="gt-text-gradient">excellence</span> ?
                </h2>

                <p class="text-lg text-white/70 mb-10 max-w-2xl mx-auto">
                    Rejoignez plus de 5 000 professionnels certifiés à travers l'Afrique. Nos conseillers sont disponibles pour vous guider dans votre parcours.
                </p>

                <div class="flex flex-wrap gap-4 justify-center">
                    <Link :href="route('formations')" class="gt-btn-gold px-8 py-4 text-base rounded-xl font-black">
                        <i class="bi bi-mortarboard-fill"></i>
                        Voir les formations
                    </Link>
                </div>
            </div>
        </section>

    </LayoutFront>
</template>
