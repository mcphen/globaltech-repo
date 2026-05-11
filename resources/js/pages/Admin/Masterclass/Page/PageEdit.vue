<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';
import { ref } from 'vue';

interface PageData {
    id: number;
    hero_badge: string;
    hero_title: string;
    hero_subtitle: string;
    hero_description: string | null;
    section_titre: string;
    section_description: string | null;
    approche: string;
    langues: string;
    cohortes: string;
    duree_info: string;
    financement_info: string;
}

interface SessionData {
    id: number;
    badge_text: string;
    cta_titre: string;
    cta_description: string | null;
    date_session: string;
    places_total: number;
    places_restantes: number;
    prix_a_partir: string;
    financement: string;
    is_active: boolean;
}

const props = defineProps<{
    page: PageData;
    session: SessionData;
    flash?: { success?: string; error?: string };
}>();

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Executive Masterclass', href: route('admin.masterclass.page.edit') },
];

const sections = [
    { label: 'Domaines', icon: 'bi-grid-3x3-gap', route: 'admin.masterclass.domaines.index', count: 'domaines de masterclass' },
    { label: 'Formats', icon: 'bi-layout-wtf', route: 'admin.masterclass.formats.index', count: 'formats de formation' },
    { label: 'Partenaires', icon: 'bi-building-check', route: 'admin.masterclass.partenaires.index', count: 'partenaires stratégiques' },
    { label: 'Certifications', icon: 'bi-patch-check', route: 'admin.masterclass.certifications.index', count: 'certifications' },
    { label: 'Public Cible', icon: 'bi-people', route: 'admin.masterclass.public-cibles.index', count: 'profils cibles' },
    { label: 'Statistiques', icon: 'bi-bar-chart', route: 'admin.masterclass.stats.index', count: 'chiffres clés' },
];

const pageForm = useForm({
    hero_badge:          props.page.hero_badge,
    hero_title:          props.page.hero_title,
    hero_subtitle:       props.page.hero_subtitle,
    hero_description:    props.page.hero_description ?? '',
    section_titre:       props.page.section_titre,
    section_description: props.page.section_description ?? '',
    approche:            props.page.approche,
    langues:             props.page.langues,
    cohortes:            props.page.cohortes,
    duree_info:          props.page.duree_info,
    financement_info:    props.page.financement_info,
});

const sessionForm = useForm({
    badge_text:       props.session.badge_text,
    cta_titre:        props.session.cta_titre,
    cta_description:  props.session.cta_description ?? '',
    date_session:     props.session.date_session,
    places_total:     props.session.places_total,
    places_restantes: props.session.places_restantes,
    prix_a_partir:    props.session.prix_a_partir,
    financement:      props.session.financement,
    is_active:        props.session.is_active,
});

const submittingPage = ref(false);
const submittingSession = ref(false);

function submitPage() {
    submittingPage.value = true;
    pageForm.post(route('admin.masterclass.page.update-page'), { onFinish: () => { submittingPage.value = false; } });
}

function submitSession() {
    submittingSession.value = true;
    sessionForm.post(route('admin.masterclass.page.update-session'), { onFinish: () => { submittingSession.value = false; } });
}
</script>

<template>
    <Head title="Executive Masterclass — Paramètres" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-8 p-6">

            <!-- Flash -->
            <div v-if="props.flash?.success" class="bg-green-50 border-l-4 border-green-500 p-4 rounded-md">
                <p class="text-green-700">{{ props.flash.success }}</p>
            </div>
            <div v-if="props.flash?.error" class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
                <p class="text-red-700">{{ props.flash.error }}</p>
            </div>

            <!-- Header -->
            <div class="rounded-2xl p-6 text-white" style="background: linear-gradient(135deg, #0B1437, #1E3A8A);">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center flex-shrink-0" style="background: rgba(232,160,32,0.2);">
                        <i class="bi bi-award text-3xl" style="color: #E8A020;"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-black text-white">Executive Masterclass</h1>
                        <p class="text-sm mt-1" style="color: rgba(255,255,255,0.6);">Gérez tout le contenu de la page Executive Masterclass</p>
                    </div>
                    <a href="/executive-masterclass" target="_blank"
                        class="ml-auto flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-all"
                        style="background: rgba(232,160,32,0.15); color: #E8A020; border: 1px solid rgba(232,160,32,0.3);">
                        <i class="bi bi-eye"></i> Voir la page
                    </a>
                </div>
            </div>

            <!-- Navigation rapide vers les sections -->
            <div class="rounded-xl bg-white shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Sections du contenu</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                    <Link v-for="s in sections" :key="s.route" :href="route(s.route)"
                        class="flex flex-col items-center gap-2 p-4 rounded-xl border-2 border-gray-100 hover:border-blue-200 hover:bg-blue-50 transition-all group text-center">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center group-hover:bg-blue-100 transition-colors" style="background: #F0F4FF;">
                            <i :class="['bi', s.icon, 'text-lg text-blue-600']"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-700 group-hover:text-blue-700">{{ s.label }}</span>
                    </Link>
                </div>
            </div>

            <div class="grid lg:grid-cols-2 gap-6">

                <!-- Paramètres bannière -->
                <div class="rounded-xl bg-white shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-6 pb-4 border-b">Bannière principale & Présentation</h2>
                    <form @submit.prevent="submitPage" class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Badge (texte sous le logo)</label>
                            <input v-model="pageForm.hero_badge" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" />
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Titre principal <span class="text-red-500">*</span></label>
                                <input v-model="pageForm.hero_title" type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Sous-titre</label>
                                <input v-model="pageForm.hero_subtitle" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Description de la bannière</label>
                            <textarea v-model="pageForm.hero_description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></textarea>
                        </div>
                        <hr class="border-gray-100" />
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Titre de la section "Présentation"</label>
                            <input v-model="pageForm.section_titre" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Description de la présentation</label>
                            <textarea v-model="pageForm.section_description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600">Approche</label>
                                <input v-model="pageForm.approche" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600">Langues</label>
                                <input v-model="pageForm.langues" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600">Nombre de cohortes</label>
                                <input v-model="pageForm.cohortes" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600">Durée info</label>
                                <input v-model="pageForm.duree_info" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600">Info financement</label>
                            <input v-model="pageForm.financement_info" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" />
                        </div>
                        <div class="flex justify-end pt-2">
                            <button type="submit" :disabled="submittingPage"
                                class="px-5 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition flex items-center gap-2 text-sm font-semibold">
                                <svg v-if="submittingPage" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ submittingPage ? 'Enregistrement...' : 'Sauvegarder la bannière' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Section CTA / Prochaine session -->
                <div class="rounded-xl bg-white shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-6 pb-4 border-b">Section Call-to-Action (Prochaine session)</h2>
                    <form @submit.prevent="submitSession" class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Badge CTA <span class="text-gray-400 text-xs">(ex: Prochaine session — Juillet 2025)</span></label>
                            <input v-model="sessionForm.badge_text" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Titre CTA <span class="text-red-500">*</span></label>
                            <input v-model="sessionForm.cta_titre" type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Description CTA</label>
                            <textarea v-model="sessionForm.cta_description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date & Lieu de la session</label>
                            <input v-model="sessionForm.date_session" type="text" placeholder="ex: 14 Juillet 2025 — Abidjan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" />
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600">Places totales</label>
                                <input v-model.number="sessionForm.places_total" type="number" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600">Places restantes</label>
                                <input v-model.number="sessionForm.places_restantes" type="number" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Prix à partir de</label>
                            <input v-model="sessionForm.prix_a_partir" type="text" placeholder="ex: 450 000 XOF" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Info financement</label>
                            <input v-model="sessionForm.financement" type="text" placeholder="ex: Prise en charge FDFP & entreprises" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" />
                        </div>
                        <div class="flex items-center gap-3">
                            <input type="checkbox" v-model="sessionForm.is_active" id="session_active" class="h-4 w-4 text-blue-600 border-gray-300 rounded" />
                            <label for="session_active" class="text-sm font-medium text-gray-700 cursor-pointer">Section CTA active (visible sur le site)</label>
                        </div>
                        <div class="flex justify-end pt-2">
                            <button type="submit" :disabled="submittingSession"
                                class="px-5 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 transition flex items-center gap-2 text-sm font-semibold">
                                <svg v-if="submittingSession" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ submittingSession ? 'Enregistrement...' : 'Sauvegarder le CTA' }}
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
