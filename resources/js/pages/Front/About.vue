<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import { useDarkMode } from '@/composables/useDarkMode';
import axios from 'axios';
import { ref, computed, onMounted } from 'vue';

interface AboutData {
    content?: string;
    image_path?: string;
    image_url?: string;
    hero_badge?: string;
    hero_title?: string;
    hero_subtitle?: string;
    history_label?: string;
    history_title?: string;
    stats?: Array<{ value: string; label: string }>;
    values?: Array<{ icon: string; color: string; title: string; desc: string }>;
    timeline_label?: string;
    timeline_title?: string;
    milestones?: Array<{ year: string; event: string }>;
    offices_title?: string;
    offices_subtitle?: string;
    offices?: Array<{ country: string; city: string; flag: string; role: string; address: string }>;
    cta_title?: string;
    cta_subtitle?: string;
}
interface TeamMember { id: number; firstname: string; lastname: string; position: string; bio?: string; image_path?: string; image_url?: string; }

const props = defineProps<{ about: AboutData | null }>();
const teamMembers = ref<TeamMember[]>([]);
const { isDark } = useDarkMode();

// Values: merge DB values with bg colors (display-only, not stored in DB)
const bgByIndex: Array<{ bgLight: string; bgDark: string }> = [
    { bgLight: '#EFF6FF', bgDark: 'rgba(37,99,235,0.15)' },
    { bgLight: '#F0FDF4', bgDark: 'rgba(22,163,74,0.15)' },
    { bgLight: '#FFFBEB', bgDark: 'rgba(217,119,6,0.15)' },
    { bgLight: '#F5F3FF', bgDark: 'rgba(124,58,237,0.15)' },
];

const displayValues = computed(() =>
    (props.about?.values ?? []).map((v, i) => ({
        ...v,
        bgLight: bgByIndex[i]?.bgLight ?? '#F8FAFC',
        bgDark:  bgByIndex[i]?.bgDark  ?? 'rgba(0,0,0,0.1)',
    }))
);

// ── Dark-mode computed styles ──────────────────────────────────────────────
const sectionWhiteBg  = computed(() => isDark.value ? '#0D1526' : '#FFFFFF');
const sectionGrayBg   = computed(() => isDark.value ? '#0B1437' : '#F8FAFC');
const titleColor      = computed(() => isDark.value ? '#F1F5F9' : '#0B1437');
const bodyColor       = computed(() => isDark.value ? '#94A3B8' : '#6B7280');
const descColor       = computed(() => isDark.value ? '#64748B' : '#637084');
const cardBg          = computed(() => isDark.value ? '#131F36' : '#FFFFFF');
const cardBorder      = computed(() => isDark.value ? 'rgba(255,255,255,0.07)' : 'rgba(203,213,225,0.6)');
const dotBorder       = computed(() => isDark.value ? '#0B1437' : '#FFFFFF');
const timelineEvent   = computed(() => isDark.value ? '#CBD5E1' : '#0B1437');
const officeHeadBg    = computed(() => isDark.value ? 'rgba(232,160,32,0.15)' : '#FEF7E8');
const officeHeadColor = computed(() => isDark.value ? '#E8A020' : '#C68400');
const officeBlueBg    = computed(() => isDark.value ? 'rgba(37,99,235,0.15)' : '#EFF6FF');
const officeBlueColor = computed(() => isDark.value ? '#60A5FA' : '#2563EB');
const teamAvatarBg    = computed(() => isDark.value ? 'linear-gradient(135deg,#1E2D50,#152050)' : 'linear-gradient(135deg,#EFF6FF,#DBEAFE)');

onMounted(async () => {
    try {
        const res = await axios.get('/team-members/listes');
        if (res.data) teamMembers.value = res.data;
    } catch {}
});
</script>

<template>
    <Head title="À Propos — GlobalTECH EDUCATION Africa" />
    <LayoutFront>

        <!-- Hero -->
        <section v-if="about?.hero_title || about?.hero_badge || about?.hero_subtitle"
            class="py-28 relative overflow-hidden"
            style="background: linear-gradient(rgba(255,255,255,0.025) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.025) 1px,transparent 1px),linear-gradient(135deg,#060C22 0%,#0B1437 42%,#0E2060 72%,#091830 100%); background-size:60px 60px,60px 60px,100% 100%;">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 text-center relative z-10">
                <div v-if="about?.hero_badge"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-bold uppercase tracking-widest mb-6"
                    style="background: rgba(232, 160, 32, 0.15); color: #E8A020; border: 1px solid rgba(232, 160, 32, 0.3);">
                    <i class="bi bi-globe-africa"></i>
                    {{ about.hero_badge }}
                </div>
                <h1 v-if="about?.hero_title" class="text-5xl lg:text-6xl font-black text-white mb-6" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                    <span class="gt-text-gradient">{{ about.hero_title }}</span>
                </h1>
                <p v-if="about?.hero_subtitle" class="text-xl text-white/70 max-w-3xl mx-auto leading-relaxed">
                    {{ about.hero_subtitle }}
                </p>
            </div>
        </section>

        <!-- Mission & Vision -->
        <section v-if="about?.history_title || about?.content || about?.stats?.length || displayValues.length || about?.image_url"
            class="py-24" :style="`background: ${sectionWhiteBg};`">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-16 items-center">
                    <div>
                        <div v-if="about?.history_label" class="gt-section-label">{{ about.history_label }}</div>
                        <h2 v-if="about?.history_title" class="gt-section-title text-4xl lg:text-5xl mb-6">
                            {{ about.history_title }}
                        </h2>
                        <div v-if="about?.content" class="prose max-w-none leading-relaxed" :style="`color: ${bodyColor};`" v-html="about.content"></div>

                        <div v-if="about?.stats?.length" class="grid grid-cols-3 gap-6 mt-10">
                            <div v-for="s in about.stats" :key="s.label" class="text-center">
                                <div class="text-3xl font-black mb-1" style="color: #E8A020;">{{ s.value }}</div>
                                <div class="text-sm" :style="`color: ${descColor};`">{{ s.label }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <img v-if="about?.image_url" :src="about.image_url" alt="GlobalTECH EDUCATION Africa" class="w-full rounded-3xl shadow-2xl object-cover h-96" />
                        <div v-else-if="displayValues.length" class="grid grid-cols-2 gap-4">
                            <div v-for="val in displayValues" :key="val.title"
                                class="p-6 rounded-2xl transition-shadow hover:shadow-lg"
                                :style="`background: ${cardBg}; border: 1px solid ${cardBorder};`">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-4"
                                    :style="`background: ${isDark ? val.bgDark : val.bgLight};`">
                                    <i :class="['bi', val.icon, 'text-xl']" :style="`color: ${val.color};`"></i>
                                </div>
                                <h4 class="font-bold mb-1" :style="`color: ${titleColor};`">{{ val.title }}</h4>
                                <p class="text-xs leading-relaxed" :style="`color: ${descColor};`">{{ val.desc }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Timeline -->
        <section v-if="about?.milestones?.length" class="py-24" :style="`background: ${sectionGrayBg};`">
            <div class="max-w-5xl mx-auto px-6 lg:px-8">
                <div class="text-center mb-16">
                    <div v-if="about?.timeline_label" class="gt-section-label justify-center">{{ about.timeline_label }}</div>
                    <h2 v-if="about?.timeline_title" class="gt-section-title text-4xl mb-4">{{ about.timeline_title }}</h2>
                </div>

                <div class="relative">
                    <div class="absolute left-6 lg:left-1/2 top-0 bottom-0 w-0.5 -translate-x-1/2" style="background: linear-gradient(to bottom, #E8A020, #2563EB);"></div>
                    <div class="space-y-8">
                        <div v-for="(m, i) in about.milestones" :key="m.year"
                            class="relative flex items-start gap-6"
                            :class="i % 2 === 0 ? 'lg:flex-row' : 'lg:flex-row-reverse'">
                            <div class="absolute left-6 lg:left-1/2 -translate-x-1/2 w-4 h-4 rounded-full border-4 z-10 mt-1"
                                :style="`background: #E8A020; border-color: ${dotBorder};`"></div>
                            <div class="ml-14 lg:ml-0 lg:w-1/2" :class="i % 2 === 0 ? 'lg:pr-12' : 'lg:pl-12'">
                                <div class="gt-card p-6">
                                    <div class="text-2xl font-black mb-2" style="color: #E8A020;">{{ m.year }}</div>
                                    <p class="text-sm font-medium" :style="`color: ${timelineEvent};`">{{ m.event }}</p>
                                </div>
                            </div>
                            <div class="hidden lg:block lg:w-1/2"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- International Offices -->
        <section v-if="about?.offices?.length" class="py-24" :style="`background: ${sectionWhiteBg};`">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="text-center mb-16">
                    <div class="gt-section-label justify-center">Présence Internationale</div>
                    <h2 v-if="about?.offices_title" class="gt-section-title text-4xl lg:text-5xl mb-4">{{ about.offices_title }}</h2>
                    <p v-if="about?.offices_subtitle" class="text-lg max-w-2xl mx-auto" :style="`color: ${descColor};`">
                        {{ about.offices_subtitle }}
                    </p>
                </div>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="office in about.offices" :key="office.country" class="gt-card p-6 flex items-start gap-4">
                        <div class="text-3xl flex-shrink-0">{{ office.flag }}</div>
                        <div>
                            <h3 class="font-bold mb-1" :style="`color: ${titleColor};`">{{ office.city }}, {{ office.country }}</h3>
                            <span class="text-xs px-2 py-0.5 rounded-full font-semibold mb-2 inline-block"
                                :style="office.role === 'Siège Social'
                                    ? `background: ${officeHeadBg}; color: ${officeHeadColor};`
                                    : `background: ${officeBlueBg}; color: ${officeBlueColor};`">
                                {{ office.role }}
                            </span>
                            <p class="text-xs mt-1" :style="`color: ${descColor};`">{{ office.address }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Team -->
        <section v-if="teamMembers.length" class="py-24" :style="`background: ${sectionGrayBg};`">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="text-center mb-16">
                    <div class="gt-section-label justify-center">Notre Équipe</div>
                    <h2 class="gt-section-title text-4xl mb-4">Les experts qui vous forment</h2>
                </div>
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div v-for="member in teamMembers" :key="member.id" class="gt-card text-center p-6 group">
                        <div class="w-20 h-20 rounded-full mx-auto mb-4 overflow-hidden" :style="`background: ${teamAvatarBg};`">
                            <img v-if="member.image_url" :src="member.image_url" :alt="`${member.firstname} ${member.lastname}`" class="w-full h-full object-cover" />
                            <div v-else class="w-full h-full flex items-center justify-center text-2xl font-black text-blue-400">
                                {{ member.firstname?.charAt(0) }}
                            </div>
                        </div>
                        <h3 class="font-bold mb-1" :style="`color: ${titleColor};`">{{ member.firstname }} {{ member.lastname }}</h3>
                        <p class="text-xs font-semibold mb-3" style="color: #E8A020;">{{ member.position }}</p>
                        <p v-if="member.bio" class="text-xs leading-relaxed" :style="`color: ${descColor};`">{{ member.bio }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section v-if="about?.cta_title || about?.cta_subtitle"
            class="py-20 relative overflow-hidden"
            style="background: linear-gradient(rgba(255,255,255,0.025) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.025) 1px,transparent 1px),linear-gradient(135deg,#060C22 0%,#0B1437 50%,#091830 100%); background-size:60px 60px,60px 60px,100% 100%;">
            <div class="absolute inset-0 opacity-10" style="background: radial-gradient(circle at 80% 50%, #E8A020, transparent 60%);"></div>
            <div class="max-w-3xl mx-auto px-6 lg:px-8 text-center relative z-10">
                <h2 v-if="about?.cta_title" class="text-3xl lg:text-4xl font-black text-white mb-4" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                    {{ about.cta_title }}
                </h2>
                <p v-if="about?.cta_subtitle" class="text-white/70 mb-8">{{ about.cta_subtitle }}</p>
                <div class="flex flex-wrap gap-4 justify-center">
                    <Link href="/contact" class="gt-btn-gold px-8 py-3.5 rounded-xl">
                        <i class="bi bi-envelope-fill"></i> Nous contacter
                    </Link>
                    <Link :href="route('formations')" class="gt-btn-outline px-8 py-3.5 rounded-xl">
                        <i class="bi bi-mortarboard"></i> Nos formations
                    </Link>
                </div>
            </div>
        </section>

    </LayoutFront>
</template>
