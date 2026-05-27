<script setup lang="ts">
import { Head, usePage, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { ref, reactive } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Page Consulting', href: route('admin.service-page.edit') },
];

const page = usePage<{ servicePage: any; flash: { success?: string } }>();
const sp = page.props.servicePage || {};

const form = reactive({
    // Hero
    hero_badge:         sp.hero_badge         || '',
    hero_title:         sp.hero_title         || '',
    hero_gradient_word: sp.hero_gradient_word || '',
    hero_subtitle:      sp.hero_subtitle      || '',
    hero_stats: sp.hero_stats || [
        { n: '200+', l: 'Missions réalisées',  icon: 'bi-briefcase' },
        { n: '12',   l: "Pays d'intervention", icon: 'bi-globe-africa' },
        { n: '50+',  l: 'Experts consultants', icon: 'bi-people' },
        { n: '98%',  l: 'Satisfaction clients', icon: 'bi-star' },
    ],
    // Services DB
    services_label: sp.services_label || '',
    services_title: sp.services_title || '',
    // Consulting offers
    consulting_label:    sp.consulting_label    || '',
    consulting_title:    sp.consulting_title    || '',
    consulting_subtitle: sp.consulting_subtitle || '',
    consulting_offers: sp.consulting_offers || [
        { icon: 'bi-lightbulb-fill',  color: '#2563EB', title: 'Conseil Stratégique IT',      desc: "Accompagnement stratégique en transformation digitale, architecture IT et gouvernance des systèmes d'information.", deliverables: ['Audit SI', 'Feuille de route digitale', 'Gouvernance IT', 'KPIs & tableaux de bord'], featured: false },
        { icon: 'bi-diagram-3-fill',  color: '#16A34A', title: 'Management de Projet',         desc: 'Pilotage de projets complexes selon les standards PMI, PRINCE2 et méthodes agiles. De la planification à la livraison.', deliverables: ['PMO externalisé', 'Plan de projet', 'Gestion des risques', 'Reporting exécutif'], featured: true },
        { icon: 'bi-people-fill',     color: '#D97706', title: 'Capital Humain & RH',          desc: "Stratégie RH, gestion des talents, plans de développement des compétences et transformation organisationnelle.", deliverables: ['Audit RH', 'Plans de formation', 'Évaluation des compétences', 'Change management'], featured: false },
        { icon: 'bi-shield-check',    color: '#7C3AED', title: 'Cybersécurité & Conformité',   desc: "Évaluation des risques cyber, mise en conformité réglementaire et implémentation des bonnes pratiques de sécurité.", deliverables: ['Audit sécurité', 'RSSI externalisé', 'ISO 27001', 'Politique de sécurité'], featured: false },
        { icon: 'bi-globe-africa',    color: '#0891B2', title: 'Projets Institutionnels',       desc: "Réponse aux appels d'offres, implémentation de projets pour ministères, organisations internationales et ONG.", deliverables: ["Ingénierie de projet", "Coordination d'équipes", 'Reporting bailleurs', "Évaluation d'impact"], featured: false },
        { icon: 'bi-graph-up-arrow',  color: '#DC2626', title: 'Audit & Performance',           desc: "Diagnostic organisationnel, audit des processus, identification des leviers de performance et plan d'optimisation.", deliverables: ['Audit opérationnel', 'Benchmarking', "Plan d'amélioration", 'Suivi des actions'], featured: false },
    ],
    // Process
    process_label: sp.process_label || '',
    process_title: sp.process_title || '',
    process_steps: sp.process_steps || [
        { step: '01', icon: 'bi-telephone',          title: 'Diagnostic',   desc: 'Échange initial pour comprendre vos enjeux et définir vos objectifs.' },
        { step: '02', icon: 'bi-file-earmark-text',  title: 'Proposition',  desc: "Élaboration d'une offre sur mesure avec planning et budget détaillés." },
        { step: '03', icon: 'bi-people',             title: 'Intervention', desc: "Déploiement de notre équipe d'experts sur le terrain ou à distance." },
        { step: '04', icon: 'bi-bar-chart',          title: 'Résultats',    desc: 'Livraison des livrables, formation des équipes et transfert de compétences.' },
    ],
    // CTA
    cta_title:    sp.cta_title    || '',
    cta_subtitle: sp.cta_subtitle || '',
});

// ── Helpers listes ─────────────────────────────────────────────────────────
function addStat()    { form.hero_stats.push({ n: '', l: '', icon: '' }); }
function removeStat(i: number) { form.hero_stats.splice(i, 1); }

function addOffer()   { form.consulting_offers.push({ icon: '', color: '#2563EB', title: '', desc: '', deliverables: [], featured: false }); }
function removeOffer(i: number) { form.consulting_offers.splice(i, 1); }
function addDeliverable(i: number) { form.consulting_offers[i].deliverables.push(''); }
function removeDeliverable(offerIdx: number, delIdx: number) { form.consulting_offers[offerIdx].deliverables.splice(delIdx, 1); }

function addStep()    { form.process_steps.push({ step: String(form.process_steps.length + 1).padStart(2, '0'), icon: '', title: '', desc: '' }); }
function removeStep(i: number) { form.process_steps.splice(i, 1); }

// ── Submit ─────────────────────────────────────────────────────────────────
const processing = ref(false);
const errors = ref<Record<string, string>>({});

function submit() {
    processing.value = true;
    errors.value = {};
    const payload = new FormData();
    const append = (k: string, v: any) => payload.append(k, v ?? '');

    append('hero_badge',         form.hero_badge);
    append('hero_title',         form.hero_title);
    append('hero_gradient_word', form.hero_gradient_word);
    append('hero_subtitle',      form.hero_subtitle);
    append('services_label',     form.services_label);
    append('services_title',     form.services_title);
    append('consulting_label',   form.consulting_label);
    append('consulting_title',   form.consulting_title);
    append('consulting_subtitle',form.consulting_subtitle);
    append('process_label',      form.process_label);
    append('process_title',      form.process_title);
    append('cta_title',          form.cta_title);
    append('cta_subtitle',       form.cta_subtitle);

    payload.append('hero_stats',        JSON.stringify(form.hero_stats));
    payload.append('consulting_offers', JSON.stringify(form.consulting_offers));
    payload.append('process_steps',     JSON.stringify(form.process_steps));

    router.post(route('admin.service-page.update'), payload, {
        forceFormData: true,
        onError: (e) => { errors.value = e; },
        onFinish: () => { processing.value = false; },
    });
}

// ── Tabs ───────────────────────────────────────────────────────────────────
const activeTab = ref('hero');
const tabs = [
    { id: 'hero',       label: 'Hero' },
    { id: 'services',   label: 'Services DB' },
    { id: 'consulting', label: 'Offres de conseil' },
    { id: 'process',    label: 'Process' },
    { id: 'cta',        label: 'CTA' },
];
</script>

<template>
    <Head title="Page Consulting — Admin" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4 md:p-6 bg-white">

            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-800">Modifier la page « Consulting »</h1>
                <a href="/services" target="_blank" class="text-sm text-blue-600 hover:underline">Voir en ligne ↗</a>
            </div>

            <div v-if="page.props.flash?.success" class="p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
                {{ page.props.flash.success }}
            </div>

            <form @submit.prevent="submit" class="space-y-6">

                <!-- Tabs nav -->
                <div class="border-b border-gray-200 flex gap-1 flex-wrap">
                    <button v-for="tab in tabs" :key="tab.id" type="button" @click="activeTab = tab.id"
                        class="px-4 py-2 text-sm font-medium rounded-t-lg transition"
                        :class="activeTab === tab.id ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100'">
                        {{ tab.label }}
                    </button>
                </div>

                <!-- ── HERO ──────────────────────────────────────────────── -->
                <div v-show="activeTab === 'hero'" class="space-y-5">
                    <h2 class="text-lg font-semibold text-gray-700">Section Hero</h2>

                    <div class="grid md:grid-cols-3 gap-4">
                        <div>
                            <label class="gt-label">Badge</label>
                            <input v-model="form.hero_badge" type="text" class="gt-input" placeholder="Conseil & Expertise" />
                        </div>
                        <div>
                            <label class="gt-label">Titre principal</label>
                            <input v-model="form.hero_title" type="text" class="gt-input" placeholder="Consulting" />
                        </div>
                        <div>
                            <label class="gt-label">Mot en dégradé doré</label>
                            <input v-model="form.hero_gradient_word" type="text" class="gt-input" placeholder="à impact" />
                        </div>
                    </div>

                    <div>
                        <label class="gt-label">Sous-titre</label>
                        <textarea v-model="form.hero_subtitle" rows="3" class="gt-input" placeholder="Nos experts vous accompagnent..."></textarea>
                    </div>

                    <!-- Mini stats -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="gt-label mb-0">Chiffres clés (4 cartes en haut à droite)</label>
                            <button type="button" @click="addStat" class="gt-btn-sm">+ Ajouter</button>
                        </div>
                        <div class="space-y-2">
                            <div v-for="(s, i) in form.hero_stats" :key="i" class="flex gap-3 items-center">
                                <input v-model="s.n"    type="text" class="gt-input w-20"  placeholder="200+" />
                                <input v-model="s.l"    type="text" class="gt-input flex-1" placeholder="Missions réalisées" />
                                <input v-model="s.icon" type="text" class="gt-input w-36"  placeholder="bi-briefcase" />
                                <button type="button" @click="removeStat(i)" class="gt-btn-danger">×</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── SERVICES DB ───────────────────────────────────────── -->
                <div v-show="activeTab === 'services'" class="space-y-4">
                    <h2 class="text-lg font-semibold text-gray-700">Section Services (depuis la base de données)</h2>
                    <p class="text-sm text-gray-500">Le contenu des cartes est géré dans <strong>Opérationnel → Services</strong>. Ici vous gérez uniquement les textes d'en-tête.</p>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="gt-label">Label de section</label>
                            <input v-model="form.services_label" type="text" class="gt-input" placeholder="Nos Services" />
                        </div>
                        <div>
                            <label class="gt-label">Titre</label>
                            <input v-model="form.services_title" type="text" class="gt-input" placeholder="Ce que nous offrons" />
                        </div>
                    </div>
                </div>

                <!-- ── OFFRES DE CONSEIL ──────────────────────────────────── -->
                <div v-show="activeTab === 'consulting'" class="space-y-5">
                    <h2 class="text-lg font-semibold text-gray-700">Section Offres de conseil</h2>

                    <div class="grid md:grid-cols-3 gap-4">
                        <div>
                            <label class="gt-label">Label de section</label>
                            <input v-model="form.consulting_label" type="text" class="gt-input" placeholder="Nos Expertises" />
                        </div>
                        <div>
                            <label class="gt-label">Titre</label>
                            <input v-model="form.consulting_title" type="text" class="gt-input" placeholder="Nos offres de conseil" />
                        </div>
                        <div>
                            <label class="gt-label">Sous-titre</label>
                            <input v-model="form.consulting_subtitle" type="text" class="gt-input" placeholder="Des solutions sur mesure..." />
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <label class="gt-label mb-0">Cartes d'offres</label>
                            <button type="button" @click="addOffer" class="gt-btn-sm">+ Ajouter une offre</button>
                        </div>
                        <div class="space-y-4">
                            <div v-for="(offer, i) in form.consulting_offers" :key="i"
                                class="border rounded-xl p-4 bg-gray-50 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-semibold text-gray-600">Offre {{ i + 1 }}</span>
                                    <div class="flex items-center gap-3">
                                        <label class="flex items-center gap-1.5 text-xs text-gray-600 cursor-pointer">
                                            <input v-model="offer.featured" type="checkbox" class="rounded" />
                                            Mis en avant (badge "Le plus demandé")
                                        </label>
                                        <button type="button" @click="removeOffer(i)" class="gt-btn-danger">× Supprimer</button>
                                    </div>
                                </div>
                                <div class="grid md:grid-cols-3 gap-3">
                                    <div>
                                        <label class="text-xs text-gray-500">Icône Bootstrap</label>
                                        <input v-model="offer.icon" type="text" class="gt-input" placeholder="bi-lightbulb-fill" />
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-500">Couleur</label>
                                        <div class="flex gap-2 items-center">
                                            <input v-model="offer.color" type="color" class="h-9 w-12 rounded border cursor-pointer" />
                                            <input v-model="offer.color" type="text" class="gt-input flex-1" placeholder="#2563EB" />
                                        </div>
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-500">Titre</label>
                                        <input v-model="offer.title" type="text" class="gt-input" placeholder="Conseil Stratégique IT" />
                                    </div>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500">Description</label>
                                    <textarea v-model="offer.desc" rows="2" class="gt-input" placeholder="Accompagnement stratégique..."></textarea>
                                </div>
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="text-xs text-gray-500">Livrables</label>
                                        <button type="button" @click="addDeliverable(i)" class="text-xs text-blue-600 hover:underline">+ Ajouter</button>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        <div v-for="(d, di) in offer.deliverables" :key="di" class="flex items-center gap-1">
                                            <input v-model="offer.deliverables[di]" type="text" class="gt-input w-36" placeholder="Audit SI" />
                                            <button type="button" @click="removeDeliverable(i, di)" class="gt-btn-danger text-xs">×</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── PROCESS ────────────────────────────────────────────── -->
                <div v-show="activeTab === 'process'" class="space-y-4">
                    <h2 class="text-lg font-semibold text-gray-700">Section Process / Comment nous travaillons</h2>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="gt-label">Label de section</label>
                            <input v-model="form.process_label" type="text" class="gt-input" placeholder="Notre Approche" />
                        </div>
                        <div>
                            <label class="gt-label">Titre</label>
                            <input v-model="form.process_title" type="text" class="gt-input" placeholder="Comment nous travaillons" />
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <label class="gt-label mb-0">Étapes</label>
                            <button type="button" @click="addStep" class="gt-btn-sm">+ Ajouter</button>
                        </div>
                        <div class="space-y-2">
                            <div v-for="(step, i) in form.process_steps" :key="i"
                                class="grid md:grid-cols-4 gap-3 p-3 border rounded-lg bg-gray-50 items-end">
                                <div>
                                    <label class="text-xs text-gray-500">N° étape</label>
                                    <input v-model="step.step" type="text" class="gt-input" placeholder="01" />
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500">Icône Bootstrap</label>
                                    <input v-model="step.icon" type="text" class="gt-input" placeholder="bi-telephone" />
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500">Titre</label>
                                    <input v-model="step.title" type="text" class="gt-input" placeholder="Diagnostic" />
                                </div>
                                <div class="flex gap-2 items-end">
                                    <div class="flex-1">
                                        <label class="text-xs text-gray-500">Description</label>
                                        <input v-model="step.desc" type="text" class="gt-input" placeholder="Échange initial..." />
                                    </div>
                                    <button type="button" @click="removeStep(i)" class="gt-btn-danger">×</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── CTA ────────────────────────────────────────────────── -->
                <div v-show="activeTab === 'cta'" class="space-y-4">
                    <h2 class="text-lg font-semibold text-gray-700">Section CTA (fond marine en bas)</h2>
                    <div>
                        <label class="gt-label">Titre</label>
                        <input v-model="form.cta_title" type="text" class="gt-input" placeholder="Un projet en Afrique ?" />
                    </div>
                    <div>
                        <label class="gt-label">Sous-titre</label>
                        <textarea v-model="form.cta_subtitle" rows="3" class="gt-input" placeholder="Nos équipes sont prêtes à intervenir..."></textarea>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex items-center gap-4 pt-4 border-t">
                    <button type="submit" :disabled="processing"
                        class="px-6 py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 disabled:opacity-60 flex items-center gap-2">
                        <svg v-if="processing" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                        </svg>
                        {{ processing ? 'Enregistrement...' : 'Sauvegarder' }}
                    </button>
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
