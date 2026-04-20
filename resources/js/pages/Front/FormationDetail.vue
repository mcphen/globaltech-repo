<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import { useDarkMode } from '@/composables/useDarkMode';
import { computed, ref, onMounted } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

interface Formation {
    id: number; title: string; slug: string; description: string;
    image_path?: string | null; image_url?: string | null;
    attachment_path?: string | null; attachment_url?: string | null;
    date_mode: 'single' | 'range';
    date?: string | null; start_time?: string | null; end_time?: string | null;
    start_date?: string | null; end_date?: string | null;
    duration_hours?: number | null; duration_per_day_hours?: number | null;
    price?: number | null; currency?: string | null;
    created_at: string;
}

const props = defineProps<{ formation: Formation }>();
const { isDark } = useDarkMode();

const metaTitle = computed(() => `${props.formation.title} — GlobalTECH EDUCATION Africa`);
const metaDescription = computed(() => (props.formation.description || '').replace(/<[^>]*>?/gm, '').slice(0, 160));

const formatDate = (d?: string | null) => d
    ? new Date(d).toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' })
    : '';

const showForm = ref(false);
const isSubmitting = ref(false);
const enrollmentMode = ref<'b2c' | 'b2b'>('b2c');
const participateForm = useForm({ first_name: '', last_name: '', email: '', phone: '', attentes: '' });

interface B2BParticipant { first_name: string; last_name: string; email: string; }
const b2bForm = ref({
    company_name: '',
    company_sector: '',
    contact_first_name: '',
    contact_last_name: '',
    contact_function: '',
    contact_email: '',
    contact_phone: '',
    attentes: '',
    participants: [{ first_name: '', last_name: '', email: '' }] as B2BParticipant[],
});
const b2bErrors = ref<Record<string, string>>({});

function addParticipant() {
    b2bForm.value.participants.push({ first_name: '', last_name: '', email: '' });
}
function removeParticipant(index: number) {
    if (b2bForm.value.participants.length > 1) b2bForm.value.participants.splice(index, 1);
}

const isAuthenticated = ref(false);
const alreadySelected = ref(false);
const currentStatus = ref<string | null>(null);
const disableIdentityFields = computed(() => isAuthenticated.value && !alreadySelected.value);

onMounted(async () => {
    try {
        const resp = await axios.get(route('formations.participation-status', props.formation.id));
        if (resp?.data) {
            isAuthenticated.value = !!resp.data.authenticated;
            if (resp.data.authenticated) {
                alreadySelected.value = !!resp.data.selected;
                currentStatus.value = resp.data.status ?? null;
                const u = resp.data.user || {};
                if (!alreadySelected.value) {
                    participateForm.first_name = u.first_name || '';
                    participateForm.last_name = u.last_name || '';
                    participateForm.email = u.email || '';
                    participateForm.phone = u.phone || '';
                }
            }
        }
    } catch {}
});

async function submitParticipation() {
    if (enrollmentMode.value === 'b2b') { await submitB2B(); return; }
    try {
        isSubmitting.value = true;
        const resp = await axios.post(route('formations.participate', props.formation.id), {
            type: 'b2c',
            first_name: participateForm.first_name,
            last_name: participateForm.last_name,
            email: participateForm.email,
            phone: participateForm.phone,
            attentes: participateForm.attentes,
        });
        if (resp?.data?.redirect) { window.location.href = resp.data.redirect; return; }
        if (resp?.data?.success) {
            alreadySelected.value = true;
            if (resp.data.status) currentStatus.value = resp.data.status;
        }
        await Swal.fire({ icon: 'success', title: 'Inscription enregistrée !', text: 'Notre équipe vous contactera sous 48h pour confirmer votre participation.', confirmButtonText: 'Parfait !', confirmButtonColor: '#E8A020' });
        showForm.value = false;
        participateForm.attentes = '';
    } catch (error: any) {
        if (error?.response?.status === 422 && error.response.data?.errors) {
            const errs = error.response.data.errors as Record<string, string[]>;
            for (const [field, messages] of Object.entries(errs)) {
                (participateForm as any).setError?.(field, messages[0]);
            }
        } else {
            await Swal.fire({ icon: 'error', title: 'Erreur', text: "Impossible d'enregistrer votre inscription. Veuillez réessayer.", confirmButtonColor: '#0B1437' });
        }
    } finally {
        isSubmitting.value = false;
    }
}

async function submitB2B() {
    b2bErrors.value = {};
    const f = b2bForm.value;
    if (!f.company_name) { b2bErrors.value.company_name = 'Requis'; }
    if (!f.contact_first_name) { b2bErrors.value.contact_first_name = 'Requis'; }
    if (!f.contact_last_name) { b2bErrors.value.contact_last_name = 'Requis'; }
    if (!f.contact_email) { b2bErrors.value.contact_email = 'Requis'; }
    if (Object.keys(b2bErrors.value).length) return;
    try {
        isSubmitting.value = true;
        const resp = await axios.post(route('formations.participate', props.formation.id), {
            type: 'b2b',
            company_name: f.company_name,
            company_sector: f.company_sector,
            contact_first_name: f.contact_first_name,
            contact_last_name: f.contact_last_name,
            contact_function: f.contact_function,
            contact_email: f.contact_email,
            contact_phone: f.contact_phone,
            attentes: f.attentes,
            participants: f.participants.filter(p => p.email || p.first_name || p.last_name),
        });
        if (resp?.data?.redirect) { window.location.href = resp.data.redirect; return; }
        await Swal.fire({
            icon: 'success',
            title: 'Demande entreprise enregistrée !',
            html: `Notre équipe contactera <strong>${f.contact_first_name} ${f.contact_last_name}</strong> sous 48h pour finaliser l'inscription des participants.`,
            confirmButtonText: 'Parfait !',
            confirmButtonColor: '#E8A020'
        });
        showForm.value = false;
    } catch (error: any) {
        if (error?.response?.status === 422 && error.response.data?.errors) {
            b2bErrors.value = Object.fromEntries(
                Object.entries(error.response.data.errors as Record<string, string[]>).map(([k, v]) => [k, v[0]])
            );
        } else {
            await Swal.fire({ icon: 'error', title: 'Erreur', text: "Impossible d'enregistrer la demande. Veuillez réessayer.", confirmButtonColor: '#0B1437' });
        }
    } finally {
        isSubmitting.value = false;
    }
}

async function shareFormation() {
    const url = route('formations.show', props.formation.slug ?? props.formation.id);
    try {
        if (navigator.share) { await navigator.share({ title: props.formation.title, url }); return; }
        await navigator.clipboard.writeText(url);
        alert('Lien copié !');
    } catch { alert('Impossible de partager.'); }
}

const statusLabel = computed(() => {
    const map: Record<string, string> = { pending: 'En attente', confirmed: 'Confirmée', cancelled: 'Annulée' };
    return map[currentStatus.value ?? ''] ?? currentStatus.value ?? '—';
});
const statusColor = computed(() => {
    if (isDark.value) {
        const map: Record<string, string> = { pending: 'text-yellow-400 bg-yellow-400/10', confirmed: 'text-green-400 bg-green-400/10', cancelled: 'text-red-400 bg-red-400/10' };
        return map[currentStatus.value ?? ''] ?? 'text-gray-400 bg-gray-400/10';
    }
    const map: Record<string, string> = { pending: 'text-yellow-600 bg-yellow-50', confirmed: 'text-green-600 bg-green-50', cancelled: 'text-red-600 bg-red-50' };
    return map[currentStatus.value ?? ''] ?? 'text-gray-600 bg-gray-50';
});

// Dark-mode styles
const contentBg      = computed(() => isDark.value ? '#0D1526' : '#FFFFFF');
const ctaBg          = computed(() => isDark.value ? '#0B1437' : '#F8FAFC');
const ctaBorder      = computed(() => isDark.value ? 'rgba(255,255,255,0.06)' : '#F1F5F9');
const titleColor     = computed(() => isDark.value ? '#F1F5F9' : '#0B1437');
const descColor      = computed(() => isDark.value ? '#94A3B8' : '#637084');
const metaLabelColor = computed(() => isDark.value ? '#64748B' : '#637084');
const valueColor     = computed(() => isDark.value ? '#E2E8F0' : '#0B1437');

// Benefit cards
const benefits = computed(() => [
    { icon: 'bi-patch-check-fill', color: '#16A34A', bgLight: '#F0FDF4', bgDark: 'rgba(22,163,74,0.15)',  title: 'Certification officielle', desc: 'Reconnue internationalement' },
    { icon: 'bi-people-fill',      color: '#2563EB', bgLight: '#EFF6FF', bgDark: 'rgba(37,99,235,0.15)',  title: 'Formateurs experts',       desc: 'Professionnels actifs' },
    { icon: 'bi-globe',            color: '#D97706', bgLight: '#FFFBEB', bgDark: 'rgba(217,119,6,0.15)',  title: 'Niveau international',     desc: 'Standards PMI & CompTIA' },
]);

// Sidebar card
const sidebarBg      = computed(() => isDark.value ? '#131F36' : '#FFFFFF');
const sidebarBorder  = computed(() => isDark.value ? 'rgba(255,255,255,0.07)' : '#E5E7EB');
const sidebarBodyBg  = computed(() => isDark.value ? '#131F36' : '#FFFFFF');
const sidebarDivider = computed(() => isDark.value ? 'rgba(255,255,255,0.07)' : '#F1F5F9');
const iconBgGold     = computed(() => isDark.value ? 'rgba(232,160,32,0.15)'  : '#FEF7E8');
const iconBgBlue     = computed(() => isDark.value ? 'rgba(37,99,235,0.15)'   : '#EFF6FF');
const iconBgRed      = computed(() => isDark.value ? 'rgba(239,68,68,0.15)'   : '#EFF6FF');
const shareBtnStyle  = computed(() => isDark.value
    ? 'border-color: rgba(255,255,255,0.12); color: #94A3B8;'
    : 'border-color: #E2E8F0; color: #475569;');
const contactBtnStyle = computed(() => isDark.value
    ? 'border-color: rgba(255,255,255,0.2); color: #E2E8F0;'
    : 'border-color: #0B1437; color: #0B1437;');
const attachBg       = computed(() => isDark.value ? '#1A2844' : '#F9FAFB');
const attachBorder   = computed(() => isDark.value ? 'rgba(255,255,255,0.07)' : '#E5E7EB');
const attachIconBg   = computed(() => isDark.value ? 'rgba(239,68,68,0.15)' : '#EFF6FF');

// Enrollment form
const formBg         = computed(() => isDark.value ? '#1A2844' : '#FFFBEB');
const formBorder     = computed(() => isDark.value ? 'rgba(232,160,32,0.25)' : '#FDE68A');
const formHeaderBorder = computed(() => isDark.value ? 'rgba(232,160,32,0.2)' : '#FDE68A');
const formLabelColor = computed(() => isDark.value ? '#CBD5E1' : '#0B1437');
const formTitleColor = computed(() => isDark.value ? '#FCD34D' : '#92400E');
const inputBase      = computed(() => isDark.value
    ? 'background: #0F1E38; border-color: rgba(255,255,255,0.1); color: #F1F5F9;'
    : 'background: white; border-color: #E5E7EB; color: #0B1437;');
</script>

<template>
    <Head>
        <title>{{ metaTitle }}</title>
        <meta name="description" :content="metaDescription" />
    </Head>
    <LayoutFront>

        <!-- Hero -->
        <section class="py-20 relative overflow-hidden" style="background: linear-gradient(rgba(255,255,255,0.025) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.025) 1px,transparent 1px),linear-gradient(135deg,#060C22 0%,#0B1437 42%,#0E2060 72%,#091830 100%); background-size:60px 60px,60px 60px,100% 100%;">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
                <nav class="flex items-center gap-2 text-sm text-white/60 mb-6">
                    <Link href="/" class="hover:text-white transition-colors">Accueil</Link>
                    <i class="bi bi-chevron-right text-xs"></i>
                    <Link :href="route('formations')" class="hover:text-white transition-colors">Formations</Link>
                    <i class="bi bi-chevron-right text-xs"></i>
                    <span class="text-white/90 truncate max-w-xs">{{ formation.title }}</span>
                </nav>
                <div class="max-w-3xl">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold text-white" style="background: #E8A020;">
                            <i class="bi bi-patch-check-fill mr-1"></i>Formation certifiante
                        </span>
                        <span v-if="formation.price" class="px-3 py-1 rounded-full text-xs font-bold text-white" style="background: rgba(255,255,255,0.15);">
                            {{ new Intl.NumberFormat('fr-FR').format(formation.price) }} {{ formation.currency || 'XOF' }}
                        </span>
                    </div>
                    <h1 class="text-4xl lg:text-5xl font-black text-white mb-4 leading-tight" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                        {{ formation.title }}
                    </h1>
                    <div class="flex flex-wrap gap-4 text-sm text-white/70">
                        <span v-if="formation.duration_hours" class="flex items-center gap-1.5">
                            <i class="bi bi-clock" style="color: #E8A020;"></i>
                            {{ formation.duration_hours }}h de formation
                        </span>
                        <span v-if="formation.date || formation.start_date" class="flex items-center gap-1.5">
                            <i class="bi bi-calendar3" style="color: #E8A020;"></i>
                            <template v-if="formation.date_mode === 'single'">{{ formatDate(formation.date) }}</template>
                            <template v-else>{{ formatDate(formation.start_date) }} → {{ formatDate(formation.end_date) }}</template>
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Content -->
        <section class="py-16" :style="`background: ${contentBg};`">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="grid lg:grid-cols-3 gap-10">

                    <!-- Main Content -->
                    <div class="lg:col-span-2">
                        <div v-if="formation.image_url" class="rounded-2xl overflow-hidden mb-10 shadow-lg">
                            <img :src="formation.image_url" :alt="formation.title" class="w-full h-72 object-cover" />
                        </div>

                        <!-- Description -->
                        <div class="prose max-w-none leading-relaxed text-sm"
                            :class="isDark ? 'prose-invert' : ''"
                            :style="`color: ${descColor};`"
                            v-html="formation.description"></div>

                        <!-- Attachment -->
                        <div v-if="formation.attachment_url || formation.attachment_path"
                            class="mt-8 p-5 rounded-2xl flex items-center justify-between"
                            :style="`background: ${attachBg}; border: 1px solid ${attachBorder};`">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center" :style="`background: ${attachIconBg};`">
                                    <i class="bi bi-file-earmark-pdf text-lg text-red-500"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-sm" :style="`color: ${titleColor};`">Brochure de la formation</p>
                                    <p class="text-xs" :style="`color: ${descColor};`">Programme complet au format PDF</p>
                                </div>
                            </div>
                            <a :href="formation.attachment_url || formation.attachment_path!" target="_blank"
                                class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold text-white transition-colors"
                                style="background: #0B1437;">
                                <i class="bi bi-download"></i> Télécharger
                            </a>
                        </div>

                        <!-- Benefits -->
                        <div class="mt-10 grid md:grid-cols-3 gap-4">
                            <div v-for="b in benefits" :key="b.title"
                                class="p-4 rounded-2xl"
                                :style="`background: ${isDark ? b.bgDark : 'transparent'}; border: 1px solid ${isDark ? 'rgba(255,255,255,0.07)' : '#F1F5F9'};`">
                                <div class="w-9 h-9 rounded-lg flex items-center justify-center mb-3"
                                    :style="`background: ${isDark ? b.bgDark : b.bgLight};`">
                                    <i :class="['bi', b.icon, 'text-sm']" :style="`color: ${b.color};`"></i>
                                </div>
                                <h4 class="text-sm font-bold mb-1" :style="`color: ${titleColor};`">{{ b.title }}</h4>
                                <p class="text-xs" :style="`color: ${descColor};`">{{ b.desc }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <aside class="lg:col-span-1">
                        <div class="sticky top-24 space-y-4">

                            <!-- Info Card -->
                            <div class="rounded-2xl overflow-hidden shadow-sm"
                                :style="`background: ${sidebarBg}; border: 1px solid ${sidebarBorder};`">
                                <!-- Header -->
                                <div class="px-6 py-4 text-white" style="background: linear-gradient(135deg, #0B1437, #152050);">
                                    <p class="text-xs font-bold uppercase tracking-widest opacity-70 mb-1">Investissement</p>
                                    <div v-if="formation.price" class="text-3xl font-black">
                                        {{ new Intl.NumberFormat('fr-FR').format(formation.price) }}
                                        <span class="text-base font-normal opacity-70">{{ formation.currency || 'XOF' }}</span>
                                    </div>
                                    <div v-else class="text-lg font-bold text-yellow-400">Sur devis</div>
                                </div>

                                <div class="p-6 space-y-4" :style="`background: ${sidebarBodyBg};`">
                                    <!-- Dates -->
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5" :style="`background: ${iconBgGold};`">
                                            <i class="bi bi-calendar3 text-sm" style="color: #E8A020;"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-wide mb-0.5" :style="`color: ${metaLabelColor};`">Date(s)</p>
                                            <template v-if="formation.date_mode === 'single'">
                                                <p class="text-sm font-medium" :style="`color: ${valueColor};`">{{ formatDate(formation.date) }}</p>
                                                <p v-if="formation.start_time && formation.end_time" class="text-xs" :style="`color: ${metaLabelColor};`">
                                                    {{ formation.start_time }} — {{ formation.end_time }}
                                                </p>
                                            </template>
                                            <template v-else>
                                                <p class="text-sm font-medium" :style="`color: ${valueColor};`">
                                                    Du {{ formatDate(formation.start_date) }}<br>au {{ formatDate(formation.end_date) }}
                                                </p>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Duration -->
                                    <div v-if="formation.duration_hours || formation.duration_per_day_hours" class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" :style="`background: ${iconBgBlue};`">
                                            <i class="bi bi-clock text-sm" style="color: #2563EB;"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-wide mb-0.5" :style="`color: ${metaLabelColor};`">Durée</p>
                                            <p v-if="formation.duration_hours" class="text-sm font-medium" :style="`color: ${valueColor};`">{{ formation.duration_hours }} heures</p>
                                            <p v-if="formation.duration_per_day_hours" class="text-xs" :style="`color: ${metaLabelColor};`">{{ formation.duration_per_day_hours }}h / jour</p>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="space-y-2 pt-4" :style="`border-top: 1px solid ${sidebarDivider};`">
                                        <template v-if="alreadySelected">
                                            <div class="p-4 rounded-xl" :class="statusColor">
                                                <div class="flex items-center gap-2">
                                                    <i class="bi bi-check-circle-fill"></i>
                                                    <p class="text-sm font-bold">Vous êtes inscrit(e)</p>
                                                </div>
                                                <p class="text-xs mt-1">Statut : <strong>{{ statusLabel }}</strong></p>
                                            </div>
                                        </template>
                                        <template v-else>
                                            <button @click="showForm = !showForm"
                                                class="w-full py-3.5 rounded-xl text-sm font-black text-white transition-all duration-200 hover:shadow-lg"
                                                style="background: linear-gradient(135deg, #E8A020, #C68400);">
                                                <i class="bi bi-mortarboard-fill mr-1.5"></i>
                                                {{ showForm ? 'Annuler' : 'S\'inscrire à cette formation' }}
                                            </button>
                                        </template>

                                        <button @click="shareFormation"
                                            class="w-full py-3 rounded-xl text-sm font-semibold border-2 flex items-center justify-center gap-2 transition-colors"
                                            :class="isDark ? 'hover:bg-white/6' : 'hover:bg-gray-50'"
                                            :style="shareBtnStyle">
                                            <i class="bi bi-share"></i> Partager
                                        </button>

                                        <Link :href="route('contact')"
                                            class="w-full py-3 rounded-xl text-sm font-semibold border-2 flex items-center justify-center gap-2 transition-all hover:shadow-sm"
                                            :style="contactBtnStyle">
                                            <i class="bi bi-telephone"></i> Nous contacter
                                        </Link>
                                    </div>
                                </div>
                            </div>

                            <!-- Enrollment Form -->
                            <Transition name="slide-down">
                                <div v-if="showForm && !alreadySelected"
                                    class="rounded-2xl overflow-hidden shadow-sm"
                                    :style="`background: ${formBg}; border: 1px solid ${formBorder};`">

                                    <!-- Header + Mode Toggle -->
                                    <div class="px-5 py-4 space-y-3" :style="`border-bottom: 1px solid ${formHeaderBorder};`">
                                        <h3 class="font-bold text-sm" :style="`color: ${formTitleColor};`">
                                            <i class="bi bi-mortarboard-fill mr-1.5"></i>Formulaire d'inscription
                                        </h3>
                                        <!-- B2C / B2B Toggle -->
                                        <div class="grid grid-cols-2 gap-1 p-1 rounded-xl" :style="`background: ${isDark ? 'rgba(255,255,255,0.06)' : '#F1F5F9'};`">
                                            <button type="button"
                                                @click="enrollmentMode = 'b2c'"
                                                class="py-2 rounded-lg text-xs font-bold transition-all"
                                                :style="enrollmentMode === 'b2c'
                                                    ? 'background: #E8A020; color: white; box-shadow: 0 1px 4px rgba(232,160,32,0.4);'
                                                    : `background: transparent; color: ${isDark ? '#94A3B8' : '#637084'};`">
                                                <i class="bi bi-person-fill mr-1"></i>Particulier
                                            </button>
                                            <button type="button"
                                                @click="enrollmentMode = 'b2b'"
                                                class="py-2 rounded-lg text-xs font-bold transition-all"
                                                :style="enrollmentMode === 'b2b'
                                                    ? 'background: #0B1437; color: white; box-shadow: 0 1px 4px rgba(11,20,55,0.4);'
                                                    : `background: transparent; color: ${isDark ? '#94A3B8' : '#637084'};`">
                                                <i class="bi bi-building-fill mr-1"></i>Entreprise
                                            </button>
                                        </div>
                                        <!-- Mode hint -->
                                        <p class="text-xs" :style="`color: ${isDark ? '#64748B' : '#94A3B8'};`">
                                            <template v-if="enrollmentMode === 'b2c'">
                                                <i class="bi bi-info-circle mr-1"></i>Inscription individuelle — vous vous inscrivez pour vous-même.
                                            </template>
                                            <template v-else>
                                                <i class="bi bi-info-circle mr-1"></i>Votre entreprise inscrit un ou plusieurs employés à cette formation.
                                            </template>
                                        </p>
                                    </div>

                                    <!-- B2C Form -->
                                    <form v-if="enrollmentMode === 'b2c'" @submit.prevent="submitParticipation" class="p-5 space-y-3">
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-xs font-semibold mb-1.5" :style="`color: ${formLabelColor};`">Prénom *</label>
                                                <input v-model="participateForm.first_name" type="text" required :disabled="disableIdentityFields"
                                                    class="w-full px-3 py-2 rounded-lg border text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 disabled:opacity-60 transition-all"
                                                    :style="participateForm.errors.first_name ? 'border-color: #EF4444;' : inputBase" />
                                                <p v-if="participateForm.errors.first_name" class="text-xs text-red-400 mt-1">{{ participateForm.errors.first_name }}</p>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold mb-1.5" :style="`color: ${formLabelColor};`">Nom *</label>
                                                <input v-model="participateForm.last_name" type="text" required :disabled="disableIdentityFields"
                                                    class="w-full px-3 py-2 rounded-lg border text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 disabled:opacity-60 transition-all"
                                                    :style="participateForm.errors.last_name ? 'border-color: #EF4444;' : inputBase" />
                                                <p v-if="participateForm.errors.last_name" class="text-xs text-red-400 mt-1">{{ participateForm.errors.last_name }}</p>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold mb-1.5" :style="`color: ${formLabelColor};`">Email *</label>
                                            <input v-model="participateForm.email" type="email" required :disabled="disableIdentityFields"
                                                class="w-full px-3 py-2 rounded-lg border text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 disabled:opacity-60 transition-all"
                                                :style="participateForm.errors.email ? 'border-color: #EF4444;' : inputBase" />
                                            <p v-if="participateForm.errors.email" class="text-xs text-red-400 mt-1">{{ participateForm.errors.email }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold mb-1.5" :style="`color: ${formLabelColor};`">Téléphone</label>
                                            <input v-model="participateForm.phone" type="tel" :disabled="disableIdentityFields"
                                                class="w-full px-3 py-2 rounded-lg border text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 disabled:opacity-60 transition-all"
                                                :style="inputBase" />
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold mb-1.5" :style="`color: ${formLabelColor};`">Vos attentes</label>
                                            <textarea v-model="participateForm.attentes" rows="3" placeholder="Ce que vous souhaitez apprendre..."
                                                class="w-full px-3 py-2 rounded-lg border text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 resize-none transition-all"
                                                :style="inputBase"></textarea>
                                        </div>
                                        <button type="submit" :disabled="isSubmitting"
                                            class="w-full py-3 rounded-xl text-sm font-black text-white transition-all disabled:opacity-60"
                                            style="background: #E8A020;">
                                            {{ isSubmitting ? 'Envoi en cours...' : 'Confirmer mon inscription' }}
                                        </button>
                                    </form>

                                    <!-- B2B Form -->
                                    <form v-else @submit.prevent="submitParticipation" class="p-5 space-y-4">

                                        <!-- Infos entreprise -->
                                        <div>
                                            <p class="text-xs font-black uppercase tracking-widest mb-2" :style="`color: ${isDark ? '#E8A020' : '#0B1437'};`">
                                                <i class="bi bi-building mr-1"></i>Informations entreprise
                                            </p>
                                            <div class="space-y-3">
                                                <div>
                                                    <label class="block text-xs font-semibold mb-1.5" :style="`color: ${formLabelColor};`">Nom de l'entreprise *</label>
                                                    <input v-model="b2bForm.company_name" type="text" placeholder="Ex: Acme Côte d'Ivoire"
                                                        class="w-full px-3 py-2 rounded-lg border text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 transition-all"
                                                        :style="b2bErrors.company_name ? 'border-color: #EF4444;' : inputBase" />
                                                    <p v-if="b2bErrors.company_name" class="text-xs text-red-400 mt-1">{{ b2bErrors.company_name }}</p>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-semibold mb-1.5" :style="`color: ${formLabelColor};`">Secteur d'activité</label>
                                                    <input v-model="b2bForm.company_sector" type="text" placeholder="Ex: Télécoms, Banque, BTP..."
                                                        class="w-full px-3 py-2 rounded-lg border text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 transition-all"
                                                        :style="inputBase" />
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Contact responsable -->
                                        <div>
                                            <p class="text-xs font-black uppercase tracking-widest mb-2" :style="`color: ${isDark ? '#E8A020' : '#0B1437'};`">
                                                <i class="bi bi-person-badge mr-1"></i>Responsable / Contact RH
                                            </p>
                                            <div class="space-y-3">
                                                <div class="grid grid-cols-2 gap-3">
                                                    <div>
                                                        <label class="block text-xs font-semibold mb-1.5" :style="`color: ${formLabelColor};`">Prénom *</label>
                                                        <input v-model="b2bForm.contact_first_name" type="text"
                                                            class="w-full px-3 py-2 rounded-lg border text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 transition-all"
                                                            :style="b2bErrors.contact_first_name ? 'border-color: #EF4444;' : inputBase" />
                                                        <p v-if="b2bErrors.contact_first_name" class="text-xs text-red-400 mt-1">{{ b2bErrors.contact_first_name }}</p>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-semibold mb-1.5" :style="`color: ${formLabelColor};`">Nom *</label>
                                                        <input v-model="b2bForm.contact_last_name" type="text"
                                                            class="w-full px-3 py-2 rounded-lg border text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 transition-all"
                                                            :style="b2bErrors.contact_last_name ? 'border-color: #EF4444;' : inputBase" />
                                                        <p v-if="b2bErrors.contact_last_name" class="text-xs text-red-400 mt-1">{{ b2bErrors.contact_last_name }}</p>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-semibold mb-1.5" :style="`color: ${formLabelColor};`">Fonction / Poste</label>
                                                    <input v-model="b2bForm.contact_function" type="text" placeholder="Ex: DRH, Responsable Formation"
                                                        class="w-full px-3 py-2 rounded-lg border text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 transition-all"
                                                        :style="inputBase" />
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-semibold mb-1.5" :style="`color: ${formLabelColor};`">Email professionnel *</label>
                                                    <input v-model="b2bForm.contact_email" type="email"
                                                        class="w-full px-3 py-2 rounded-lg border text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 transition-all"
                                                        :style="b2bErrors.contact_email ? 'border-color: #EF4444;' : inputBase" />
                                                    <p v-if="b2bErrors.contact_email" class="text-xs text-red-400 mt-1">{{ b2bErrors.contact_email }}</p>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-semibold mb-1.5" :style="`color: ${formLabelColor};`">Téléphone</label>
                                                    <input v-model="b2bForm.contact_phone" type="tel"
                                                        class="w-full px-3 py-2 rounded-lg border text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 transition-all"
                                                        :style="inputBase" />
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Liste des participants -->
                                        <div>
                                            <div class="flex items-center justify-between mb-2">
                                                <p class="text-xs font-black uppercase tracking-widest" :style="`color: ${isDark ? '#E8A020' : '#0B1437'};`">
                                                    <i class="bi bi-people-fill mr-1"></i>Participants ({{ b2bForm.participants.length }})
                                                </p>
                                                <button type="button" @click="addParticipant"
                                                    class="flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold text-white transition-all"
                                                    style="background: #0B1437;">
                                                    <i class="bi bi-plus-lg"></i> Ajouter
                                                </button>
                                            </div>
                                            <div class="space-y-2 max-h-64 overflow-y-auto pr-1">
                                                <div v-for="(p, idx) in b2bForm.participants" :key="idx"
                                                    class="p-3 rounded-xl"
                                                    :style="`background: ${isDark ? 'rgba(255,255,255,0.04)' : '#F8FAFC'}; border: 1px solid ${isDark ? 'rgba(255,255,255,0.07)' : '#E5E7EB'};`">
                                                    <div class="flex items-center justify-between mb-2">
                                                        <span class="text-xs font-bold" :style="`color: ${isDark ? '#64748B' : '#94A3B8'};`">
                                                            Participant {{ idx + 1 }}
                                                        </span>
                                                        <button v-if="b2bForm.participants.length > 1" type="button" @click="removeParticipant(idx)"
                                                            class="text-red-400 hover:text-red-500 transition-colors">
                                                            <i class="bi bi-x-circle text-sm"></i>
                                                        </button>
                                                    </div>
                                                    <div class="grid grid-cols-2 gap-2 mb-2">
                                                        <input v-model="p.first_name" type="text" placeholder="Prénom"
                                                            class="w-full px-2.5 py-1.5 rounded-lg border text-xs focus:outline-none focus:ring-1 focus:ring-yellow-400 transition-all"
                                                            :style="inputBase" />
                                                        <input v-model="p.last_name" type="text" placeholder="Nom"
                                                            class="w-full px-2.5 py-1.5 rounded-lg border text-xs focus:outline-none focus:ring-1 focus:ring-yellow-400 transition-all"
                                                            :style="inputBase" />
                                                    </div>
                                                    <input v-model="p.email" type="email" placeholder="Email professionnel"
                                                        class="w-full px-2.5 py-1.5 rounded-lg border text-xs focus:outline-none focus:ring-1 focus:ring-yellow-400 transition-all"
                                                        :style="inputBase" />
                                                </div>
                                            </div>
                                            <p class="text-xs mt-1.5" :style="`color: ${isDark ? '#64748B' : '#94A3B8'};`">
                                                Vous pouvez aussi envoyer la liste complète après confirmation.
                                            </p>
                                        </div>

                                        <!-- Attentes / message -->
                                        <div>
                                            <label class="block text-xs font-semibold mb-1.5" :style="`color: ${formLabelColor};`">Objectifs / Attentes</label>
                                            <textarea v-model="b2bForm.attentes" rows="3" placeholder="Objectifs de montée en compétence pour vos équipes..."
                                                class="w-full px-3 py-2 rounded-lg border text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 resize-none transition-all"
                                                :style="inputBase"></textarea>
                                        </div>

                                        <button type="submit" :disabled="isSubmitting"
                                            class="w-full py-3 rounded-xl text-sm font-black text-white transition-all disabled:opacity-60"
                                            style="background: linear-gradient(135deg, #0B1437, #152050);">
                                            <i class="bi bi-building-fill mr-1.5"></i>
                                            {{ isSubmitting ? 'Envoi en cours...' : 'Soumettre la demande entreprise' }}
                                        </button>
                                    </form>
                                </div>
                            </Transition>
                        </div>
                    </aside>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="py-16" :style="`background: ${ctaBg}; border-top: 1px solid ${ctaBorder};`">
            <div class="max-w-4xl mx-auto px-6 lg:px-8 text-center">
                <h3 class="text-2xl font-black mb-3" :style="`color: ${titleColor};`">Vous hésitez encore ?</h3>
                <p class="mb-6" :style="`color: ${descColor};`">Nos conseillers pédagogiques sont disponibles pour vous aider à choisir la formation adaptée à votre profil.</p>
                <div class="flex flex-wrap gap-4 justify-center">
                    <Link :href="route('contact')" class="gt-btn-gold px-8 py-3.5 rounded-xl">
                        <i class="bi bi-telephone-fill"></i> Parler à un conseiller
                    </Link>
                    <Link :href="route('formations')" class="gt-btn-outline-navy px-8 py-3.5 rounded-xl">
                        <i class="bi bi-grid"></i> Voir toutes les formations
                    </Link>
                </div>
            </div>
        </section>

    </LayoutFront>
</template>

<style scoped>
.prose :where(img, video) { max-width: 100%; border-radius: 1rem; }
.slide-down-enter-active { transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
.slide-down-leave-active { transition: all 0.2s ease; }
.slide-down-enter-from, .slide-down-leave-to { opacity: 0; transform: translateY(-12px); }
</style>
