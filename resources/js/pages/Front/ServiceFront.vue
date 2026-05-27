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

interface ConsultingOffer {
    icon: string; color: string; title: string; desc: string;
    deliverables: string[]; featured?: boolean;
}

interface ProcessStep {
    step: string; icon: string; title: string; desc: string;
}

interface ServicePageData {
    hero_badge?: string;
    hero_title?: string;
    hero_gradient_word?: string;
    hero_subtitle?: string;
    hero_stats?: Array<{ n: string; l: string; icon: string }>;
    services_label?: string;
    services_title?: string;
    consulting_label?: string;
    consulting_title?: string;
    consulting_subtitle?: string;
    consulting_offers?: ConsultingOffer[];
    process_label?: string;
    process_title?: string;
    process_steps?: ProcessStep[];
    cta_title?: string;
    cta_subtitle?: string;
}

const props = defineProps<{ services?: Service[]; servicePage?: ServicePageData | null }>();
const services = ref<Service[]>(props.services ?? []);
const sp = computed(() => props.servicePage || {});

const { isDark } = useDarkMode();

// bg colors indexed by offer position (display-only, not stored)
const offerBgByColor: Record<string, { light: string; dark: string }> = {
    '#2563EB': { light: '#EFF6FF', dark: 'rgba(37,99,235,0.15)' },
    '#16A34A': { light: '#F0FDF4', dark: 'rgba(22,163,74,0.15)' },
    '#D97706': { light: '#FFFBEB', dark: 'rgba(217,119,6,0.15)' },
    '#7C3AED': { light: '#F5F3FF', dark: 'rgba(124,58,237,0.15)' },
    '#0891B2': { light: '#F0FDFA', dark: 'rgba(8,145,178,0.15)' },
    '#DC2626': { light: '#FEF2F2', dark: 'rgba(220,38,38,0.15)' },
};

function offerBg(color: string): string {
    const match = offerBgByColor[color.toUpperCase()] || offerBgByColor[color];
    if (match) return isDark.value ? match.dark : match.light;
    return isDark.value ? `${color}22` : `${color}18`;
}

onMounted(async () => {
    if (!services.value.length) {
        try {
            const res = await axios.get('/api/services/all-with-items');
            if (res.data) services.value = res.data;
        } catch {}
    }
});

// Dark-mode styles
const sectionWhiteBg = computed(() => isDark.value ? '#0D1526' : '#FFFFFF');
const sectionGrayBg  = computed(() => isDark.value ? '#0B1437' : '#F8FAFC');
const titleColor     = computed(() => isDark.value ? '#F1F5F9' : '#0B1437');
const descColor      = computed(() => isDark.value ? '#94A3B8' : '#637084');
const labelColor     = computed(() => isDark.value ? '#64748B' : '#637084');
const serviceIconBg  = computed(() => isDark.value ? 'rgba(37,99,235,0.15)' : '#EFF6FF');
const ringOffset     = computed(() => isDark.value ? 'ring-offset-[#0B1437]' : 'ring-offset-white');
</script>

<template>
    <Head title="Consulting & Conseil — GlobalTECH EDUCATION Africa" />
    <LayoutFront>

        <!-- Hero -->
        <section v-if="sp.hero_title || sp.hero_badge"
            class="py-28 relative overflow-hidden"
            style="background: linear-gradient(rgba(255,255,255,0.025) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.025) 1px,transparent 1px),linear-gradient(135deg,#060C22 0%,#0B1437 42%,#0E2060 72%,#091830 100%); background-size:60px 60px,60px 60px,100% 100%;">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <div v-if="sp.hero_badge"
                            class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-6"
                            style="background: rgba(232,160,32,0.15); color: #E8A020; border: 1px solid rgba(232,160,32,0.3);">
                            <i class="bi bi-briefcase-fill"></i>
                            {{ sp.hero_badge }}
                        </div>
                        <h1 class="text-5xl lg:text-6xl font-black text-white mb-6 leading-tight" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                            {{ sp.hero_title }}
                            <span v-if="sp.hero_gradient_word" class="gt-text-gradient"> {{ sp.hero_gradient_word }}</span>
                        </h1>
                        <p v-if="sp.hero_subtitle" class="text-xl text-white/70 leading-relaxed mb-8">{{ sp.hero_subtitle }}</p>
                        <div class="flex flex-wrap gap-4">
                            <Link href="/contact" class="gt-btn-gold rounded-xl px-8 py-3.5">
                                <i class="bi bi-telephone-fill"></i> Demander un devis
                            </Link>
                            <Link :href="route('portfolio')" class="gt-btn-outline rounded-xl px-8 py-3.5">
                                <i class="bi bi-folder2-open"></i> Voir nos projets
                            </Link>
                        </div>
                    </div>

                    <div v-if="sp.hero_stats?.length" class="hidden lg:grid grid-cols-2 gap-4">
                        <div v-for="item in sp.hero_stats" :key="item.l"
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

        <!-- Services depuis la DB -->
        <section v-if="services.length" class="py-24" :style="`background: ${sectionWhiteBg};`">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="text-center mb-16">
                    <div v-if="sp.services_label" class="gt-section-label justify-center">{{ sp.services_label }}</div>
                    <h2 v-if="sp.services_title" class="gt-section-title text-4xl mb-4">{{ sp.services_title }}</h2>
                </div>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="service in services" :key="service.id" class="gt-card p-8 group">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-6 transition-transform duration-300 group-hover:scale-110"
                            :style="`background: ${serviceIconBg};`">
                            <i :class="['bi', service.icon || 'bi-briefcase', 'text-2xl']" style="color: #2563EB;"></i>
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

        <!-- Offres de conseil -->
        <section v-if="sp.consulting_offers?.length" class="py-24" :style="`background: ${sectionGrayBg};`">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="text-center mb-16">
                    <div v-if="sp.consulting_label" class="gt-section-label justify-center">{{ sp.consulting_label }}</div>
                    <h2 v-if="sp.consulting_title" class="gt-section-title text-4xl lg:text-5xl mb-4">{{ sp.consulting_title }}</h2>
                    <p v-if="sp.consulting_subtitle" class="text-lg max-w-2xl mx-auto" :style="`color: ${descColor};`">{{ sp.consulting_subtitle }}</p>
                </div>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="offer in sp.consulting_offers" :key="offer.title"
                        class="gt-card p-8 group"
                        :class="offer.featured ? `ring-2 ring-offset-2 ring-green-400 ${ringOffset}` : ''">
                        <div v-if="offer.featured" class="text-xs font-bold px-2.5 py-1 rounded-full text-white mb-4 inline-block"
                            :style="`background: ${offer.color};`">
                            <i class="bi bi-star-fill mr-1"></i> Le plus demandé
                        </div>
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-6 transition-transform duration-300 group-hover:scale-110"
                            :style="`background: ${offerBg(offer.color)};`">
                            <i :class="['bi', offer.icon, 'text-2xl']" :style="`color: ${offer.color};`"></i>
                        </div>
                        <h3 class="text-lg font-bold mb-3" :style="`color: ${titleColor};`">{{ offer.title }}</h3>
                        <p class="text-sm leading-relaxed mb-5" :style="`color: ${descColor};`">{{ offer.desc }}</p>
                        <div v-if="offer.deliverables?.length" class="space-y-1.5">
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
        <section v-if="sp.process_steps?.length" class="py-24" :style="`background: ${sectionWhiteBg};`">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="text-center mb-16">
                    <div v-if="sp.process_label" class="gt-section-label justify-center">{{ sp.process_label }}</div>
                    <h2 v-if="sp.process_title" class="gt-section-title text-4xl mb-4">{{ sp.process_title }}</h2>
                </div>
                <div class="grid md:grid-cols-4 gap-6 relative">
                    <div class="hidden md:block absolute top-10 left-[12.5%] right-[12.5%] h-0.5"
                        style="background: linear-gradient(90deg, #E8A020, #2563EB);"></div>
                    <div v-for="(step, i) in sp.process_steps" :key="step.step" class="relative text-center">
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
        <section v-if="sp.cta_title"
            class="py-20 relative overflow-hidden"
            style="background: linear-gradient(135deg, #0B1437, #0E2060);">
            <div class="absolute inset-0 gt-grid-pattern opacity-20"></div>
            <div class="max-w-3xl mx-auto px-6 text-center relative z-10">
                <h2 class="text-3xl lg:text-4xl font-black text-white mb-4" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                    {{ sp.cta_title }}
                </h2>
                <p v-if="sp.cta_subtitle" class="text-white/70 mb-8 text-lg">{{ sp.cta_subtitle }}</p>
                <div class="flex flex-wrap gap-4 justify-center">
                    <Link href="/contact" class="gt-btn-gold px-8 py-4 rounded-xl text-base font-black">
                        <i class="bi bi-send-fill"></i> Demander un devis
                    </Link>
                </div>
            </div>
        </section>

    </LayoutFront>
</template>
