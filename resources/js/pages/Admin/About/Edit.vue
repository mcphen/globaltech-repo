<script setup lang="ts">
import { Head, usePage, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { ref, onMounted, reactive } from 'vue';
import Quill from 'quill';
import 'quill/dist/quill.snow.css';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'À propos', href: route('admin.about.index') },
    { title: 'Modifier', href: route('admin.about.edit') },
];

const page = usePage<{
    about: any;
    flash: { success?: string };
}>();

const about = page.props.about || {};

// ── Quill ──────────────────────────────────────────────────────────────────
const editorRef = ref<HTMLDivElement | null>(null);
let quill: Quill;

// ── Image ──────────────────────────────────────────────────────────────────
const imageFile = ref<File | null>(null);
const imagePreview = ref<string | null>(
    about.image_path ? `/storage/${about.image_path.replace(/^\/?storage\/+/g, '')}` : null
);

function handleImageChange(event: Event) {
    const input = event.target as HTMLInputElement;
    if (input.files?.[0]) {
        imageFile.value = input.files[0];
        const reader = new FileReader();
        reader.onload = (e) => { imagePreview.value = e.target?.result as string; };
        reader.readAsDataURL(input.files[0]);
    }
}

// ── Form state ─────────────────────────────────────────────────────────────
const form = reactive({
    // Hero
    hero_badge:    about.hero_badge    || 'Présent dans 12 pays africains',
    hero_title:    about.hero_title    || 'À propos de GlobalTECH',
    hero_subtitle: about.hero_subtitle || 'Depuis 2010, nous formons et certifions les talents d\'Afrique aux standards internationaux les plus exigeants.',
    // Histoire
    history_label: about.history_label || 'Notre Histoire',
    history_title: about.history_title || 'Former l\'Afrique de demain',
    stats: about.stats || [
        { value: '500+', label: 'Entreprises' },
        { value: '5K+',  label: 'Certifiés' },
        { value: '12',   label: 'Pays' },
    ],
    values: about.values || [
        { icon: 'bi-award-fill',   color: '#2563EB', title: 'Excellence',       desc: 'Standards internationaux dans chaque formation' },
        { icon: 'bi-globe-africa', color: '#16A34A', title: 'Afrique d\'abord', desc: 'Des contenus adaptés au contexte africain' },
        { icon: 'bi-people-fill',  color: '#D97706', title: 'Impact humain',    desc: 'Le développement des talents au cœur de tout' },
        { icon: 'bi-shield-check', color: '#7C3AED', title: 'Intégrité',        desc: 'Certifications officielles et transparentes' },
    ],
    // Timeline
    timeline_label: about.timeline_label || 'Notre Parcours',
    timeline_title: about.timeline_title || '15 ans d\'excellence',
    milestones: about.milestones || [
        { year: '2010', event: 'Création de GlobalTECH EDUCATION Africa à Abidjan' },
        { year: '2013', event: 'Accréditation PMI — premier centre agréé en Afrique de l\'Ouest' },
        { year: '2016', event: 'Ouverture des bureaux de Dakar et Bamako' },
        { year: '2018', event: 'Partenariat stratégique Microsoft et Cisco' },
        { year: '2020', event: 'Lancement de la plateforme e-Learning panafricaine' },
        { year: '2022', event: '5 000 professionnels certifiés — cap historique' },
        { year: '2024', event: 'Expansion au Cameroun, Burkina Faso et Togo' },
    ],
    // Bureaux
    offices_title:    about.offices_title    || 'Nos bureaux en Afrique',
    offices_subtitle: about.offices_subtitle || 'Une présence locale forte pour mieux servir nos clients partout sur le continent.',
    offices: about.offices || [
        { country: 'Côte d\'Ivoire', city: 'Abidjan',      flag: '🇨🇮', role: 'Siège Social',    address: 'Plateau, Abidjan' },
        { country: 'Sénégal',        city: 'Dakar',         flag: '🇸🇳', role: 'Bureau Régional', address: 'Plateau, Dakar' },
        { country: 'Mali',           city: 'Bamako',        flag: '🇲🇱', role: 'Bureau Régional', address: 'ACI 2000, Bamako' },
        { country: 'Cameroun',       city: 'Douala',        flag: '🇨🇲', role: 'Bureau Régional', address: 'Bonanjo, Douala' },
        { country: 'Burkina Faso',   city: 'Ouagadougou',   flag: '🇧🇫', role: 'Bureau Régional', address: 'Ouaga 2000' },
        { country: 'Togo',           city: 'Lomé',          flag: '🇹🇬', role: 'Représentation',  address: 'Centre-ville, Lomé' },
    ],
    // CTA
    cta_title:    about.cta_title    || 'Faisons connaissance',
    cta_subtitle: about.cta_subtitle || 'Contactez nos conseillers pour discuter de vos besoins en formation et certification.',
});

// ── Helpers pour les listes ────────────────────────────────────────────────
function addStat()      { form.stats.push({ value: '', label: '' }); }
function removeStat(i: number) { form.stats.splice(i, 1); }

function addValue()     { form.values.push({ icon: '', color: '#000000', title: '', desc: '' }); }
function removeValue(i: number) { form.values.splice(i, 1); }

function addMilestone() { form.milestones.push({ year: '', event: '' }); }
function removeMilestone(i: number) { form.milestones.splice(i, 1); }

function addOffice()    { form.offices.push({ country: '', city: '', flag: '', role: 'Bureau Régional', address: '' }); }
function removeOffice(i: number) { form.offices.splice(i, 1); }

// ── Init Quill ─────────────────────────────────────────────────────────────
onMounted(() => {
    if (editorRef.value) {
        quill = new Quill(editorRef.value, { theme: 'snow' });
        quill.root.innerHTML = about.content || '';
    }
});

// ── Submission ─────────────────────────────────────────────────────────────
const processing = ref(false);
const errors = ref<Record<string, string>>({});

function submit() {
    processing.value = true;
    errors.value = {};

    const payload = new FormData();
    payload.append('content', quill?.root.innerHTML || '');
    if (imageFile.value) payload.append('image', imageFile.value);

    const appendScalar = (key: string, val: any) => payload.append(key, val ?? '');

    appendScalar('hero_badge',       form.hero_badge);
    appendScalar('hero_title',       form.hero_title);
    appendScalar('hero_subtitle',    form.hero_subtitle);
    appendScalar('history_label',    form.history_label);
    appendScalar('history_title',    form.history_title);
    appendScalar('timeline_label',   form.timeline_label);
    appendScalar('timeline_title',   form.timeline_title);
    appendScalar('offices_title',    form.offices_title);
    appendScalar('offices_subtitle', form.offices_subtitle);
    appendScalar('cta_title',        form.cta_title);
    appendScalar('cta_subtitle',     form.cta_subtitle);

    // Arrays as JSON strings (Laravel receives them as arrays via JSON decode in controller)
    payload.append('stats',      JSON.stringify(form.stats));
    payload.append('values',     JSON.stringify(form.values));
    payload.append('milestones', JSON.stringify(form.milestones));
    payload.append('offices',    JSON.stringify(form.offices));

    router.post(route('admin.about.update'), payload, {
        forceFormData: true,
        onError: (e) => { errors.value = e; },
        onFinish: () => { processing.value = false; },
    });
}

// ── Tabs ───────────────────────────────────────────────────────────────────
const activeTab = ref('hero');
const tabs = [
    { id: 'hero',      label: 'Hero' },
    { id: 'histoire',  label: 'Notre Histoire' },
    { id: 'timeline',  label: 'Timeline' },
    { id: 'bureaux',   label: 'Bureaux' },
    { id: 'cta',       label: 'CTA' },
];
</script>

<template>
    <Head title="Modifier — À propos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4 md:p-6 bg-white">

            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-800">Modifier la page « À propos »</h1>
            </div>

            <!-- Flash -->
            <div v-if="page.props.flash?.success" class="p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
                {{ page.props.flash.success }}
            </div>

            <form @submit.prevent="submit" class="space-y-6">

                <!-- Tabs -->
                <div class="border-b border-gray-200 flex gap-1 flex-wrap">
                    <button
                        v-for="tab in tabs"
                        :key="tab.id"
                        type="button"
                        @click="activeTab = tab.id"
                        class="px-4 py-2 text-sm font-medium rounded-t-lg transition"
                        :class="activeTab === tab.id
                            ? 'bg-blue-600 text-white'
                            : 'text-gray-600 hover:bg-gray-100'"
                    >
                        {{ tab.label }}
                    </button>
                </div>

                <!-- ── HERO ─────────────────────────────────────────────── -->
                <div v-show="activeTab === 'hero'" class="space-y-4">
                    <h2 class="text-lg font-semibold text-gray-700">Section Hero (bannière du haut)</h2>

                    <div>
                        <label class="gt-label">Badge (petite étiquette dorée)</label>
                        <input v-model="form.hero_badge" type="text" class="gt-input" placeholder="Présent dans 12 pays africains" />
                    </div>
                    <div>
                        <label class="gt-label">Titre principal</label>
                        <input v-model="form.hero_title" type="text" class="gt-input" placeholder="À propos de GlobalTECH" />
                    </div>
                    <div>
                        <label class="gt-label">Sous-titre</label>
                        <textarea v-model="form.hero_subtitle" rows="3" class="gt-input" placeholder="Depuis 2010..."></textarea>
                    </div>
                </div>

                <!-- ── NOTRE HISTOIRE ───────────────────────────────────── -->
                <div v-show="activeTab === 'histoire'" class="space-y-6">
                    <h2 class="text-lg font-semibold text-gray-700">Section Notre Histoire</h2>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="gt-label">Label de section</label>
                            <input v-model="form.history_label" type="text" class="gt-input" placeholder="Notre Histoire" />
                        </div>
                        <div>
                            <label class="gt-label">Titre de section</label>
                            <input v-model="form.history_title" type="text" class="gt-input" placeholder="Former l'Afrique de demain" />
                        </div>
                    </div>

                    <!-- Contenu riche -->
                    <div>
                        <label class="gt-label">Contenu (texte principal)</label>
                        <div ref="editorRef" class="min-h-[160px] border rounded"></div>
                    </div>

                    <!-- Image -->
                    <div>
                        <label class="gt-label">Image illustrative</label>
                        <div class="flex flex-col md:flex-row gap-4">
                            <div v-if="imagePreview" class="w-full md:w-1/3">
                                <img :src="imagePreview" alt="Aperçu" class="w-full h-auto object-cover rounded border" />
                            </div>
                            <div class="flex-1">
                                <input type="file" @change="handleImageChange" accept="image/*" class="gt-input" />
                                <p class="text-xs text-gray-400 mt-1">JPG, PNG, GIF — max 2 Mo. Si vide, les 4 cartes de valeurs s'affichent.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <label class="gt-label mb-0">Chiffres clés (3 statistiques)</label>
                            <button type="button" @click="addStat" class="gt-btn-sm">+ Ajouter</button>
                        </div>
                        <div class="space-y-2">
                            <div v-for="(s, i) in form.stats" :key="i" class="flex gap-3 items-center">
                                <input v-model="s.value" type="text" class="gt-input w-24" placeholder="500+" />
                                <input v-model="s.label" type="text" class="gt-input flex-1" placeholder="Entreprises" />
                                <button type="button" @click="removeStat(i)" class="gt-btn-danger">×</button>
                            </div>
                        </div>
                    </div>

                    <!-- Values -->
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <label class="gt-label mb-0">Cartes de valeurs (affichées si pas d'image)</label>
                            <button type="button" @click="addValue" class="gt-btn-sm">+ Ajouter</button>
                        </div>
                        <div class="space-y-3">
                            <div v-for="(v, i) in form.values" :key="i" class="p-3 border rounded-lg bg-gray-50 grid md:grid-cols-4 gap-3 items-center">
                                <div>
                                    <label class="text-xs text-gray-500">Icône Bootstrap</label>
                                    <input v-model="v.icon" type="text" class="gt-input" placeholder="bi-award-fill" />
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500">Couleur</label>
                                    <div class="flex gap-2 items-center">
                                        <input v-model="v.color" type="color" class="h-9 w-12 rounded border cursor-pointer" />
                                        <input v-model="v.color" type="text" class="gt-input flex-1" placeholder="#2563EB" />
                                    </div>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500">Titre</label>
                                    <input v-model="v.title" type="text" class="gt-input" placeholder="Excellence" />
                                </div>
                                <div class="flex gap-2 items-start">
                                    <div class="flex-1">
                                        <label class="text-xs text-gray-500">Description</label>
                                        <input v-model="v.desc" type="text" class="gt-input" placeholder="Description..." />
                                    </div>
                                    <button type="button" @click="removeValue(i)" class="gt-btn-danger mt-5">×</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── TIMELINE ─────────────────────────────────────────── -->
                <div v-show="activeTab === 'timeline'" class="space-y-4">
                    <h2 class="text-lg font-semibold text-gray-700">Section Timeline (jalons historiques)</h2>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="gt-label">Label de section</label>
                            <input v-model="form.timeline_label" type="text" class="gt-input" placeholder="Notre Parcours" />
                        </div>
                        <div>
                            <label class="gt-label">Titre de section</label>
                            <input v-model="form.timeline_title" type="text" class="gt-input" placeholder="15 ans d'excellence" />
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <label class="gt-label mb-0">Jalons</label>
                            <button type="button" @click="addMilestone" class="gt-btn-sm">+ Ajouter</button>
                        </div>
                        <div class="space-y-2">
                            <div v-for="(m, i) in form.milestones" :key="i" class="flex gap-3 items-center">
                                <input v-model="m.year" type="text" class="gt-input w-20" placeholder="2024" />
                                <input v-model="m.event" type="text" class="gt-input flex-1" placeholder="Événement..." />
                                <button type="button" @click="removeMilestone(i)" class="gt-btn-danger">×</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── BUREAUX ──────────────────────────────────────────── -->
                <div v-show="activeTab === 'bureaux'" class="space-y-4">
                    <h2 class="text-lg font-semibold text-gray-700">Section Bureaux / Présence internationale</h2>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="gt-label">Titre de section</label>
                            <input v-model="form.offices_title" type="text" class="gt-input" placeholder="Nos bureaux en Afrique" />
                        </div>
                        <div>
                            <label class="gt-label">Sous-titre</label>
                            <input v-model="form.offices_subtitle" type="text" class="gt-input" placeholder="Une présence locale forte..." />
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <label class="gt-label mb-0">Bureaux</label>
                            <button type="button" @click="addOffice" class="gt-btn-sm">+ Ajouter</button>
                        </div>
                        <div class="space-y-3">
                            <div v-for="(o, i) in form.offices" :key="i" class="p-3 border rounded-lg bg-gray-50 grid md:grid-cols-5 gap-3 items-end">
                                <div>
                                    <label class="text-xs text-gray-500">Drapeau (emoji)</label>
                                    <input v-model="o.flag" type="text" class="gt-input" placeholder="🇨🇮" />
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500">Pays</label>
                                    <input v-model="o.country" type="text" class="gt-input" placeholder="Côte d'Ivoire" />
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500">Ville</label>
                                    <input v-model="o.city" type="text" class="gt-input" placeholder="Abidjan" />
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500">Rôle</label>
                                    <select v-model="o.role" class="gt-input">
                                        <option>Siège Social</option>
                                        <option>Bureau Régional</option>
                                        <option>Représentation</option>
                                    </select>
                                </div>
                                <div class="flex gap-2 items-end">
                                    <div class="flex-1">
                                        <label class="text-xs text-gray-500">Adresse</label>
                                        <input v-model="o.address" type="text" class="gt-input" placeholder="Plateau, Abidjan" />
                                    </div>
                                    <button type="button" @click="removeOffice(i)" class="gt-btn-danger">×</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── CTA ─────────────────────────────────────────────── -->
                <div v-show="activeTab === 'cta'" class="space-y-4">
                    <h2 class="text-lg font-semibold text-gray-700">Section CTA (appel à l'action du bas)</h2>

                    <div>
                        <label class="gt-label">Titre</label>
                        <input v-model="form.cta_title" type="text" class="gt-input" placeholder="Faisons connaissance" />
                    </div>
                    <div>
                        <label class="gt-label">Sous-titre</label>
                        <textarea v-model="form.cta_subtitle" rows="3" class="gt-input" placeholder="Contactez nos conseillers..."></textarea>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex items-center gap-4 pt-4 border-t">
                    <button
                        type="submit"
                        :disabled="processing"
                        class="px-6 py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 disabled:opacity-60 flex items-center gap-2"
                    >
                        <svg v-if="processing" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                        </svg>
                        {{ processing ? 'Enregistrement...' : 'Sauvegarder toutes les modifications' }}
                    </button>
                    <a :href="route('about')" target="_blank" class="text-sm text-blue-600 hover:underline">
                        Voir la page publique ↗
                    </a>
                </div>

            </form>
        </div>
    </AppLayout>
</template>

<style scoped>
.gt-label  { @apply block text-sm font-medium text-gray-700 mb-1; }
.gt-input  { @apply w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500; }
.gt-btn-sm { @apply px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700; }
.gt-btn-danger { @apply px-2 py-1 text-sm bg-red-100 text-red-600 rounded hover:bg-red-200; }
</style>
