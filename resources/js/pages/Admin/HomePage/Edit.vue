<script setup lang="ts">
import { Head, usePage, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { ref, reactive } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Page d\'accueil', href: route('admin.home-page.edit') },
];

const page = usePage<{ homePage: any; flash: { success?: string } }>();
const hp = page.props.homePage || {};

// ── Form state ─────────────────────────────────────────────────────────────
const form = reactive({
    // Hero
    hero_badge:    hp.hero_badge    || '',
    hero_title_1:  hp.hero_title_1  || '',
    hero_title_2:  hp.hero_title_2  || '',
    hero_title_3:  hp.hero_title_3  || '',
    hero_subtitle: hp.hero_subtitle || '',
    hero_trust_badges: hp.hero_trust_badges || ['CISCO', 'EC-COUNCIL', 'ISC²'],
    hero_mini_stats:   hp.hero_mini_stats   || [
        { v: '12',   l: 'Pays' },
        { v: '500+', l: 'Entreprises' },
        { v: '5K+',  l: 'Certifiés' },
    ],
    // Stats bar
    stats: hp.stats || [
        { value: 500,  suffix: '+', label: 'Entreprises formées',    icon: 'bi-building' },
        { value: 5000, suffix: '+', label: 'Professionnels certifiés', icon: 'bi-people' },
        { value: 12,   suffix: '',  label: 'Pays en Afrique',        icon: 'bi-globe-africa' },
        { value: 15,   suffix: '+', label: "Années d'expertise",     icon: 'bi-award' },
    ],
    // Pillars
    pillars_label:    hp.pillars_label    || '',
    pillars_title:    hp.pillars_title    || '',
    pillars_subtitle: hp.pillars_subtitle || '',
    // B2B
    b2b_badge:     hp.b2b_badge    || '',
    b2b_title_1:   hp.b2b_title_1  || '',
    b2b_title_2:   hp.b2b_title_2  || '',
    b2b_subtitle:  hp.b2b_subtitle || '',
    b2b_list_items: hp.b2b_list_items || [
        "Programmes 100% personnalisés selon vos besoins métiers",
        "Certifications reconnues à l'international",
    ],
    b2b_benefits: hp.b2b_benefits || [
        { icon: 'bi-calendar-check', title: 'Planning flexible', desc: 'Sessions adaptées à vos contraintes' },
        { icon: 'bi-translate',      title: 'Multilingue',       desc: 'Français, Anglais' },
        { icon: 'bi-laptop',         title: 'E-Learning',        desc: 'Plateforme LMS intégrée' },
        { icon: 'bi-bar-chart',      title: 'ROI Garanti',       desc: 'Suivi KPI et tableaux de bord' },
    ],
    // Why Us
    why_label:    hp.why_label    || '',
    why_title:    hp.why_title    || '',
    why_subtitle: hp.why_subtitle || '',
    // Portfolio
    portfolio_label: hp.portfolio_label || '',
    portfolio_title: hp.portfolio_title || '',
    // Testimonials
    testimonials_label: hp.testimonials_label || '',
    testimonials_title: hp.testimonials_title || '',
    // News
    news_label: hp.news_label || '',
    news_title: hp.news_title || '',
    // CTA
    cta_badge:          hp.cta_badge          || '',
    cta_title:          hp.cta_title          || '',
    cta_gradient_word:  hp.cta_gradient_word  || '',
    cta_subtitle:       hp.cta_subtitle       || '',
});

// ── Array helpers ──────────────────────────────────────────────────────────
function addBadge()       { form.hero_trust_badges.push(''); }
function removeBadge(i: number) { form.hero_trust_badges.splice(i, 1); }

function addMiniStat()    { form.hero_mini_stats.push({ v: '', l: '' }); }
function removeMiniStat(i: number) { form.hero_mini_stats.splice(i, 1); }

function addStat()        { form.stats.push({ value: 0, suffix: '', label: '', icon: '' }); }
function removeStat(i: number) { form.stats.splice(i, 1); }

function addListItem()    { form.b2b_list_items.push(''); }
function removeListItem(i: number) { form.b2b_list_items.splice(i, 1); }

function addBenefit()     { form.b2b_benefits.push({ icon: '', title: '', desc: '' }); }
function removeBenefit(i: number) { form.b2b_benefits.splice(i, 1); }

// ── Submit ─────────────────────────────────────────────────────────────────
const processing = ref(false);
const errors = ref<Record<string, string>>({});

function submit() {
    processing.value = true;
    errors.value = {};

    const payload = new FormData();
    const append = (k: string, v: any) => payload.append(k, v ?? '');

    append('hero_badge',    form.hero_badge);
    append('hero_title_1',  form.hero_title_1);
    append('hero_title_2',  form.hero_title_2);
    append('hero_title_3',  form.hero_title_3);
    append('hero_subtitle', form.hero_subtitle);
    append('pillars_label',    form.pillars_label);
    append('pillars_title',    form.pillars_title);
    append('pillars_subtitle', form.pillars_subtitle);
    append('b2b_badge',    form.b2b_badge);
    append('b2b_title_1',  form.b2b_title_1);
    append('b2b_title_2',  form.b2b_title_2);
    append('b2b_subtitle', form.b2b_subtitle);
    append('why_label',    form.why_label);
    append('why_title',    form.why_title);
    append('why_subtitle', form.why_subtitle);
    append('portfolio_label', form.portfolio_label);
    append('portfolio_title', form.portfolio_title);
    append('testimonials_label', form.testimonials_label);
    append('testimonials_title', form.testimonials_title);
    append('news_label', form.news_label);
    append('news_title', form.news_title);
    append('cta_badge',         form.cta_badge);
    append('cta_title',         form.cta_title);
    append('cta_gradient_word', form.cta_gradient_word);
    append('cta_subtitle',      form.cta_subtitle);

    payload.append('hero_trust_badges', JSON.stringify(form.hero_trust_badges));
    payload.append('hero_mini_stats',   JSON.stringify(form.hero_mini_stats));
    payload.append('stats',             JSON.stringify(form.stats));
    payload.append('b2b_list_items',    JSON.stringify(form.b2b_list_items));
    payload.append('b2b_benefits',      JSON.stringify(form.b2b_benefits));

    router.post(route('admin.home-page.update'), payload, {
        forceFormData: true,
        onError: (e) => { errors.value = e; },
        onFinish: () => { processing.value = false; },
    });
}

// ── Tabs ───────────────────────────────────────────────────────────────────
const activeTab = ref('hero');
const tabs = [
    { id: 'hero',         label: 'Hero' },
    { id: 'stats',        label: 'Stats' },
    { id: 'pillars',      label: 'Piliers' },
    { id: 'b2b',          label: 'B2B' },
    { id: 'sections',     label: 'Sections textes' },
    { id: 'cta',          label: 'CTA final' },
];
</script>

<template>
    <Head title="Page d'accueil — Admin" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4 md:p-6 bg-white">

            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-800">Modifier la page d'accueil</h1>
                <a href="/" target="_blank" class="text-sm text-blue-600 hover:underline">Voir en ligne ↗</a>
            </div>

            <!-- Flash -->
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
                    <h2 class="text-lg font-semibold text-gray-700">Section Hero (bannière principale)</h2>

                    <div>
                        <label class="gt-label">Badge eyebrow (texte doré en haut)</label>
                        <input v-model="form.hero_badge" type="text" class="gt-input" placeholder="Leader dans les formations certifiantes & consulting en Afrique" />
                    </div>

                    <div class="grid md:grid-cols-3 gap-4">
                        <div>
                            <label class="gt-label">Titre ligne 1</label>
                            <input v-model="form.hero_title_1" type="text" class="gt-input" placeholder="Certifiez" />
                        </div>
                        <div>
                            <label class="gt-label">Titre ligne 2 <span class="text-amber-500">(dégradé doré)</span></label>
                            <input v-model="form.hero_title_2" type="text" class="gt-input" placeholder="l'Excellence" />
                        </div>
                        <div>
                            <label class="gt-label">Titre ligne 3</label>
                            <input v-model="form.hero_title_3" type="text" class="gt-input" placeholder="de vos équipes." />
                        </div>
                    </div>

                    <div>
                        <label class="gt-label">Sous-titre</label>
                        <textarea v-model="form.hero_subtitle" rows="3" class="gt-input" placeholder="GLOBALTECH EDUCATION Africa est le leader..."></textarea>
                    </div>

                    <!-- Trust badges -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="gt-label mb-0">Badges de certification (ligne du bas)</label>
                            <button type="button" @click="addBadge" class="gt-btn-sm">+ Ajouter</button>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <div v-for="(b, i) in form.hero_trust_badges" :key="i" class="flex items-center gap-1">
                                <input v-model="form.hero_trust_badges[i]" type="text" class="gt-input w-32" placeholder="CISCO" />
                                <button type="button" @click="removeBadge(i)" class="gt-btn-danger">×</button>
                            </div>
                        </div>
                    </div>

                    <!-- Mini stats (carte dorée en bas à droite) -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="gt-label mb-0">Mini-stats (carte dorée dans le hero)</label>
                            <button type="button" @click="addMiniStat" class="gt-btn-sm">+ Ajouter</button>
                        </div>
                        <div class="space-y-2">
                            <div v-for="(s, i) in form.hero_mini_stats" :key="i" class="flex gap-3 items-center">
                                <input v-model="s.v" type="text" class="gt-input w-20" placeholder="12" />
                                <input v-model="s.l" type="text" class="gt-input flex-1" placeholder="Pays" />
                                <button type="button" @click="removeMiniStat(i)" class="gt-btn-danger">×</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── STATS BAR ──────────────────────────────────────────── -->
                <div v-show="activeTab === 'stats'" class="space-y-4">
                    <h2 class="text-lg font-semibold text-gray-700">Barre de statistiques (sous le hero)</h2>
                    <p class="text-sm text-gray-500">Les chiffres s'animent au scroll. Le champ "Valeur" doit être un entier (le suffixe est affiché en or à côté).</p>

                    <div class="flex justify-end">
                        <button type="button" @click="addStat" class="gt-btn-sm">+ Ajouter une stat</button>
                    </div>

                    <div class="space-y-3">
                        <div v-for="(s, i) in form.stats" :key="i"
                            class="p-4 border rounded-lg bg-gray-50 grid md:grid-cols-4 gap-3 items-end">
                            <div>
                                <label class="text-xs text-gray-500">Valeur (entier)</label>
                                <input v-model.number="s.value" type="number" class="gt-input" placeholder="500" />
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Suffixe (ex: +, %, k)</label>
                                <input v-model="s.suffix" type="text" class="gt-input" placeholder="+" />
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Label</label>
                                <input v-model="s.label" type="text" class="gt-input" placeholder="Entreprises formées" />
                            </div>
                            <div class="flex gap-2 items-end">
                                <div class="flex-1">
                                    <label class="text-xs text-gray-500">Icône Bootstrap</label>
                                    <input v-model="s.icon" type="text" class="gt-input" placeholder="bi-building" />
                                </div>
                                <button type="button" @click="removeStat(i)" class="gt-btn-danger">×</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── PILIERS ─────────────────────────────────────────────── -->
                <div v-show="activeTab === 'pillars'" class="space-y-4">
                    <h2 class="text-lg font-semibold text-gray-700">Section Piliers / Programmes</h2>
                    <p class="text-sm text-gray-500">Les cartes de piliers elles-mêmes viennent des Catégories de formation (admin &gt; Catégories).</p>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="gt-label">Label de section</label>
                            <input v-model="form.pillars_label" type="text" class="gt-input" placeholder="Nos Programmes" />
                        </div>
                        <div>
                            <label class="gt-label">Titre</label>
                            <input v-model="form.pillars_title" type="text" class="gt-input" placeholder="Trois piliers d'excellence" />
                        </div>
                    </div>
                    <div>
                        <label class="gt-label">Sous-titre</label>
                        <textarea v-model="form.pillars_subtitle" rows="2" class="gt-input" placeholder="Des programmes certifiants soigneusement conçus..."></textarea>
                    </div>
                </div>

                <!-- ── B2B ─────────────────────────────────────────────────── -->
                <div v-show="activeTab === 'b2b'" class="space-y-5">
                    <h2 class="text-lg font-semibold text-gray-700">Section B2B / Solution Entreprise</h2>

                    <div class="grid md:grid-cols-3 gap-4">
                        <div>
                            <label class="gt-label">Badge</label>
                            <input v-model="form.b2b_badge" type="text" class="gt-input" placeholder="Solution Entreprise" />
                        </div>
                        <div>
                            <label class="gt-label">Titre ligne 1</label>
                            <input v-model="form.b2b_title_1" type="text" class="gt-input" placeholder="Boostez la performance" />
                        </div>
                        <div>
                            <label class="gt-label">Titre ligne 2 <span class="text-amber-500">(dégradé)</span></label>
                            <input v-model="form.b2b_title_2" type="text" class="gt-input" placeholder="de vos équipes." />
                        </div>
                    </div>

                    <div>
                        <label class="gt-label">Sous-titre</label>
                        <textarea v-model="form.b2b_subtitle" rows="3" class="gt-input" placeholder="Vous êtes une entreprise..."></textarea>
                    </div>

                    <!-- Liste à puces -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="gt-label mb-0">Points clés (liste à puces)</label>
                            <button type="button" @click="addListItem" class="gt-btn-sm">+ Ajouter</button>
                        </div>
                        <div class="space-y-2">
                            <div v-for="(item, i) in form.b2b_list_items" :key="i" class="flex gap-2 items-center">
                                <input v-model="form.b2b_list_items[i]" type="text" class="gt-input flex-1" placeholder="Programmes 100% personnalisés..." />
                                <button type="button" @click="removeListItem(i)" class="gt-btn-danger">×</button>
                            </div>
                        </div>
                    </div>

                    <!-- Grille bénéfices (droite) -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="gt-label mb-0">Grille bénéfices (4 cartes à droite)</label>
                            <button type="button" @click="addBenefit" class="gt-btn-sm">+ Ajouter</button>
                        </div>
                        <div class="space-y-2">
                            <div v-for="(b, i) in form.b2b_benefits" :key="i"
                                class="grid md:grid-cols-3 gap-3 p-3 border rounded-lg bg-gray-50 items-end">
                                <div>
                                    <label class="text-xs text-gray-500">Icône Bootstrap</label>
                                    <input v-model="b.icon" type="text" class="gt-input" placeholder="bi-calendar-check" />
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500">Titre</label>
                                    <input v-model="b.title" type="text" class="gt-input" placeholder="Planning flexible" />
                                </div>
                                <div class="flex gap-2 items-end">
                                    <div class="flex-1">
                                        <label class="text-xs text-gray-500">Description</label>
                                        <input v-model="b.desc" type="text" class="gt-input" placeholder="Sessions adaptées..." />
                                    </div>
                                    <button type="button" @click="removeBenefit(i)" class="gt-btn-danger">×</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── SECTIONS TEXTES ─────────────────────────────────────── -->
                <div v-show="activeTab === 'sections'" class="space-y-6">
                    <h2 class="text-lg font-semibold text-gray-700">Labels et titres des autres sections</h2>
                    <p class="text-sm text-gray-500">Le contenu de ces sections (formations, témoignages, actualités, pourquoi nous) est géré dans leurs propres modules admin.</p>

                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Why Us -->
                        <div class="p-4 border rounded-lg space-y-3">
                            <h3 class="font-semibold text-gray-600 text-sm">Pourquoi Nous</h3>
                            <div>
                                <label class="gt-label">Label</label>
                                <input v-model="form.why_label" type="text" class="gt-input" placeholder="Pourquoi Nous" />
                            </div>
                            <div>
                                <label class="gt-label">Titre</label>
                                <input v-model="form.why_title" type="text" class="gt-input" placeholder="L'excellence à chaque étape" />
                            </div>
                            <div>
                                <label class="gt-label">Sous-titre</label>
                                <input v-model="form.why_subtitle" type="text" class="gt-input" placeholder="Rejoignez les milliers de professionnels..." />
                            </div>
                        </div>

                        <!-- Portfolio -->
                        <div class="p-4 border rounded-lg space-y-3">
                            <h3 class="font-semibold text-gray-600 text-sm">Réalisations / Portfolio</h3>
                            <div>
                                <label class="gt-label">Label</label>
                                <input v-model="form.portfolio_label" type="text" class="gt-input" placeholder="Nos Réalisations" />
                            </div>
                            <div>
                                <label class="gt-label">Titre</label>
                                <input v-model="form.portfolio_title" type="text" class="gt-input" placeholder="Des projets à impact" />
                            </div>
                        </div>

                        <!-- Testimonials -->
                        <div class="p-4 border rounded-lg space-y-3">
                            <h3 class="font-semibold text-gray-600 text-sm">Témoignages</h3>
                            <div>
                                <label class="gt-label">Label</label>
                                <input v-model="form.testimonials_label" type="text" class="gt-input" placeholder="Témoignages" />
                            </div>
                            <div>
                                <label class="gt-label">Titre</label>
                                <input v-model="form.testimonials_title" type="text" class="gt-input" placeholder="Ils nous font confiance" />
                            </div>
                        </div>

                        <!-- News -->
                        <div class="p-4 border rounded-lg space-y-3">
                            <h3 class="font-semibold text-gray-600 text-sm">Actualités</h3>
                            <div>
                                <label class="gt-label">Label</label>
                                <input v-model="form.news_label" type="text" class="gt-input" placeholder="Actualités" />
                            </div>
                            <div>
                                <label class="gt-label">Titre</label>
                                <input v-model="form.news_title" type="text" class="gt-input" placeholder="Dernières nouvelles" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── CTA FINAL ───────────────────────────────────────────── -->
                <div v-show="activeTab === 'cta'" class="space-y-4">
                    <h2 class="text-lg font-semibold text-gray-700">Section CTA finale (fond marine)</h2>

                    <div>
                        <label class="gt-label">Badge</label>
                        <input v-model="form.cta_badge" type="text" class="gt-input" placeholder="Démarrez aujourd'hui" />
                    </div>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="gt-label">Titre (partie normale)</label>
                            <input v-model="form.cta_title" type="text" class="gt-input" placeholder="Prêt à certifier votre" />
                        </div>
                        <div>
                            <label class="gt-label">Mot en dégradé doré</label>
                            <input v-model="form.cta_gradient_word" type="text" class="gt-input" placeholder="excellence" />
                        </div>
                    </div>
                    <div>
                        <label class="gt-label">Sous-titre</label>
                        <textarea v-model="form.cta_subtitle" rows="3" class="gt-input" placeholder="Rejoignez plus de 5 000 professionnels..."></textarea>
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
                        {{ processing ? 'Enregistrement...' : 'Sauvegarder toutes les modifications' }}
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
