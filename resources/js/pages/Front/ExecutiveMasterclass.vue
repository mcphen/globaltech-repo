<script setup lang="ts">
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import { Head } from '@inertiajs/vue3';

interface PageData {
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

interface Stat { valeur: string; label: string; }
interface Domaine { id: number; icon: string; color: string; titre: string; description: string | null; niveau: string | null; duree: string | null; modules: string[] | null; }
interface Format { id: number; icon: string; titre: string; couleur: string; description: string | null; tags: string[] | null; }
interface Partenaire { id: number; nom: string; logo_icon: string; pays: string | null; description: string | null; }
interface Certification { id: number; titre: string; description: string | null; badge_icon: string; couleur: string; }
interface PublicCible { id: number; icon: string; titre: string; description: string | null; }
interface SessionData {
    badge_text: string;
    cta_titre: string;
    cta_description: string | null;
    date_session: string;
    places_total: number;
    places_restantes: number;
    prix_a_partir: string;
    financement: string;
}

const props = defineProps<{
    contactSettings?: any;
    page?: PageData;
    stats?: Stat[];
    domaines?: Domaine[];
    formats?: Format[];
    partenaires?: Partenaire[];
    certifications?: Certification[];
    publicCibles?: PublicCible[];
    session?: SessionData;
}>();

// Fallbacks pour affichage si la table n'est pas encore migrée/seedée
const page = props.page ?? {
    hero_badge: "Programme d'Excellence — Edition 2025",
    hero_title: 'Executive Masterclass',
    hero_subtitle: 'Africa Leadership Series',
    hero_description: "Des programmes d'élite conçus pour les cadres et dirigeants africains.",
    section_titre: "Un parcours d'excellence taillé pour l'Afrique",
    section_description: null,
    approche: '70% pratique / 30% théorie',
    langues: 'Français & Anglais',
    cohortes: '4 sessions par an',
    duree_info: '3 à 10 jours selon programme',
    financement_info: 'CPF, FDFP, Entreprises',
};
const stats = props.stats ?? [];
const domaines = props.domaines ?? [];
const formats = props.formats ?? [];
const partenaires = props.partenaires ?? [];
const certifications = props.certifications ?? [];
const publicCibles = props.publicCibles ?? [];
const session = props.session ?? null;
</script>

<template>
    <Head :title="`${page.hero_title} — GlobalTECH EDUCATION Africa`" />
    <LayoutFront>

        <!-- ══ BANNIÈRE PRINCIPALE ══ -->
        <section class="relative overflow-hidden" style="background: linear-gradient(135deg, #060C22 0%, #0B1437 50%, #0D1D3E 100%); min-height: 88vh;">
            <div class="absolute inset-0 opacity-5" style="background-image: repeating-linear-gradient(45deg, #E8A020 0, #E8A020 1px, transparent 0, transparent 50%); background-size: 40px 40px;"></div>
            <div class="absolute -top-32 -right-32 w-[500px] h-[500px] rounded-full opacity-10" style="background: radial-gradient(circle, #E8A020 0%, transparent 70%);"></div>
            <div class="absolute -bottom-20 -left-20 w-[350px] h-[350px] rounded-full opacity-8" style="background: radial-gradient(circle, #2563EB 0%, transparent 70%);"></div>

            <div class="relative max-w-7xl mx-auto px-6 lg:px-8 flex flex-col justify-center" style="min-height: 88vh; padding-top: 80px; padding-bottom: 80px;">
                <div class="max-w-3xl">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-bold uppercase tracking-widest mb-8 border"
                        style="background: rgba(232,160,32,0.12); border-color: rgba(232,160,32,0.3); color: #E8A020;">
                        <i class="bi bi-stars"></i>
                        {{ page.hero_badge }}
                    </div>

                    <h1 class="text-4xl md:text-6xl font-black text-white leading-tight mb-6">
                        {{ page.hero_title }}<br/>
                        <span style="color: #E8A020;" v-if="page.hero_subtitle">{{ page.hero_subtitle }}</span>
                    </h1>

                    <p v-if="page.hero_description" class="text-lg md:text-xl leading-relaxed mb-10" style="color: rgba(255,255,255,0.75); max-width: 600px;">
                        {{ page.hero_description }}
                    </p>

                    <!-- Stats -->
                    <div v-if="stats.length" class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-10">
                        <div v-for="stat in stats" :key="stat.label" class="text-center">
                            <div class="text-3xl font-black mb-1" style="color: #E8A020;">{{ stat.valeur }}</div>
                            <div class="text-xs font-medium uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">{{ stat.label }}</div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-4">
                        <a href="#domaines"
                            class="inline-flex items-center gap-2 px-8 py-4 rounded-xl font-bold text-white text-sm transition-all duration-300 hover:shadow-xl hover:scale-105"
                            style="background: linear-gradient(135deg, #E8A020, #C68400); box-shadow: 0 8px 32px rgba(232,160,32,0.35);">
                            <i class="bi bi-compass"></i>
                            Découvrir les programmes
                        </a>
                        <a href="#contact-cta"
                            class="inline-flex items-center gap-2 px-8 py-4 rounded-xl font-bold text-sm transition-all duration-300 hover:bg-white/10"
                            style="border: 2px solid rgba(255,255,255,0.3); color: white;">
                            <i class="bi bi-telephone"></i>
                            Parler à un conseiller
                        </a>
                    </div>
                </div>
            </div>

            <div class="absolute bottom-0 left-0 right-0">
                <svg viewBox="0 0 1440 60" xmlns="http://www.w3.org/2000/svg" style="display:block;">
                    <path d="M0,40 C360,0 1080,60 1440,20 L1440,60 L0,60 Z" fill="#F8FAFC"/>
                </svg>
            </div>
        </section>

        <!-- ══ PRÉSENTATION DU PROGRAMME ══ -->
        <section class="py-24 bg-gray-50">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-16 items-center">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-widest px-3 py-1.5 rounded-full inline-block mb-6" style="background: rgba(232,160,32,0.12); color: #C68400;">Le Programme</span>
                        <h2 class="text-4xl font-black mb-6" style="color: #0B1437;">{{ page.section_titre }}</h2>
                        <p v-if="page.section_description" class="text-gray-600 text-lg leading-relaxed mb-6">
                            {{ page.section_description }}
                        </p>
                        <div class="space-y-4">
                            <div v-for="point in [
                                'Formateurs certifiés et experts praticiens internationaux',
                                'Certifications reconnues par les employeurs africains et mondiaux',
                                'Réseau alumni de +1 200 cadres à travers 18 pays africains',
                                'Accès à la plateforme e-learning pendant 12 mois post-formation',
                            ]" :key="point" class="flex items-start gap-3">
                                <i class="bi bi-check-circle-fill text-lg mt-0.5 flex-shrink-0" style="color: #E8A020;"></i>
                                <span class="text-gray-700 font-medium">{{ point }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="rounded-3xl overflow-hidden shadow-2xl" style="background: linear-gradient(145deg, #0B1437, #1E3A8A);">
                            <div class="p-10">
                                <div class="text-center mb-8">
                                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl mb-4" style="background: rgba(232,160,32,0.2);">
                                        <i class="bi bi-trophy-fill text-3xl" style="color: #E8A020;"></i>
                                    </div>
                                    <h3 class="text-2xl font-black text-white mb-2">Notre Philosophie</h3>
                                    <p class="text-sm" style="color: rgba(255,255,255,0.6);">Learn. Lead. Transform Africa.</p>
                                </div>
                                <div class="space-y-4">
                                    <div v-for="item in [
                                        { label: 'Approche',   valeur: page.approche },
                                        { label: 'Langues',    valeur: page.langues },
                                        { label: 'Cohortes',   valeur: page.cohortes },
                                        { label: 'Durée',      valeur: page.duree_info },
                                        { label: 'Financement', valeur: page.financement_info },
                                    ]" :key="item.label" class="flex items-center justify-between py-3" style="border-bottom: 1px solid rgba(255,255,255,0.08);">
                                        <span class="text-sm font-medium" style="color: rgba(255,255,255,0.6);">{{ item.label }}</span>
                                        <span class="text-sm font-bold text-white">{{ item.valeur }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="absolute -bottom-6 -right-6 w-24 h-24 rounded-2xl rotate-12" style="background: linear-gradient(135deg, #E8A020, #C68400); opacity: 0.3; z-index: -1;"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══ PUBLIC CIBLE ══ -->
        <section v-if="publicCibles.length" class="py-24 bg-white">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="text-center mb-16">
                    <span class="text-xs font-bold uppercase tracking-widest px-3 py-1.5 rounded-full inline-block mb-6" style="background: rgba(232,160,32,0.1); color: #C68400;">À qui s'adresse ce programme ?</span>
                    <h2 class="text-4xl font-black mb-4" style="color: #0B1437;">Public Cible</h2>
                    <p class="text-gray-500 text-lg max-w-2xl mx-auto">Des programmes conçus pour les profils à fort potentiel qui souhaitent accélérer leur impact organisationnel.</p>
                </div>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="cible in publicCibles" :key="cible.id"
                        class="group p-8 rounded-2xl border-2 transition-all duration-300 hover:shadow-xl hover:-translate-y-1"
                        style="border-color: #F1F5F9; background: #FAFAFA;">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-5 transition-transform group-hover:scale-110"
                            style="background: linear-gradient(135deg, #0B1437, #1E3A8A);">
                            <i :class="[cible.icon, 'text-xl text-white']"></i>
                        </div>
                        <h3 class="text-lg font-bold mb-3" style="color: #0B1437;">{{ cible.titre }}</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">{{ cible.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══ DOMAINES DE MASTERCLASS ══ -->
        <section v-if="domaines.length" id="domaines" class="py-24" style="background: #F0F4FF;">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="text-center mb-16">
                    <span class="text-xs font-bold uppercase tracking-widest px-3 py-1.5 rounded-full inline-block mb-6" style="background: rgba(232,160,32,0.12); color: #C68400;">Catalogue 2025</span>
                    <h2 class="text-4xl font-black mb-4" style="color: #0B1437;">Domaines de Masterclass</h2>
                    <p class="text-gray-500 text-lg max-w-2xl mx-auto">{{ domaines.length }} domaines d'expertise couvrant les enjeux stratégiques des entreprises africaines contemporaines.</p>
                </div>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div v-for="domaine in domaines" :key="domaine.id"
                        class="group bg-white rounded-3xl p-8 shadow-sm hover:shadow-2xl transition-all duration-400 hover:-translate-y-2 border border-gray-100">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-6 transition-transform group-hover:scale-110"
                            :style="`background: ${domaine.color}18;`">
                            <i :class="[domaine.icon, 'text-2xl']" :style="`color: ${domaine.color};`"></i>
                        </div>
                        <div class="flex items-center gap-2 mb-4">
                            <span class="text-xs font-bold px-2.5 py-1 rounded-full text-white" :style="`background: ${domaine.color};`">{{ domaine.niveau }}</span>
                            <span class="text-xs font-medium text-gray-400 flex items-center gap-1"><i class="bi bi-clock"></i> {{ domaine.duree }}</span>
                        </div>
                        <h3 class="text-xl font-black mb-3" style="color: #0B1437;">{{ domaine.titre }}</h3>
                        <p class="text-gray-500 text-sm leading-relaxed mb-5">{{ domaine.description }}</p>
                        <ul v-if="domaine.modules?.length" class="space-y-2">
                            <li v-for="module in domaine.modules" :key="module" class="flex items-center gap-2 text-xs text-gray-600">
                                <i class="bi bi-check2 text-sm flex-shrink-0" :style="`color: ${domaine.color};`"></i>
                                {{ module }}
                            </li>
                        </ul>
                        <div class="mt-6 pt-5" style="border-top: 1px solid #F1F5F9;">
                            <a href="#contact-cta" class="inline-flex items-center gap-2 text-sm font-bold transition-colors" :style="`color: ${domaine.color};`">
                                En savoir plus <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══ FORMAT DES FORMATIONS ══ -->
        <section v-if="formats.length" class="py-24 bg-white">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="text-center mb-16">
                    <span class="text-xs font-bold uppercase tracking-widest px-3 py-1.5 rounded-full inline-block mb-6" style="background: rgba(232,160,32,0.1); color: #C68400;">Flexibilité & Accessibilité</span>
                    <h2 class="text-4xl font-black mb-4" style="color: #0B1437;">Format des Formations</h2>
                    <p class="text-gray-500 text-lg max-w-2xl mx-auto">Apprenez selon vos contraintes professionnelles. Nous adaptons nos formats à vos disponibilités.</p>
                </div>
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div v-for="format in formats" :key="format.id"
                        class="group relative rounded-3xl p-7 text-white overflow-hidden transition-all duration-300 hover:shadow-2xl hover:-translate-y-1"
                        :style="`background: linear-gradient(145deg, ${format.couleur}, ${format.couleur}CC);`">
                        <div class="absolute -top-8 -right-8 w-28 h-28 rounded-full opacity-10" style="background: white;"></div>
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-5" style="background: rgba(255,255,255,0.15);">
                            <i :class="[format.icon, 'text-xl text-white']"></i>
                        </div>
                        <h3 class="text-lg font-black mb-3">{{ format.titre }}</h3>
                        <p class="text-sm leading-relaxed mb-5 opacity-85">{{ format.description }}</p>
                        <div class="flex flex-wrap gap-2">
                            <span v-for="tag in format.tags" :key="tag" class="text-xs px-2 py-1 rounded-lg font-medium" style="background: rgba(255,255,255,0.15);">{{ tag }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══ PARTENAIRES STRATÉGIQUES ══ -->
        <section v-if="partenaires.length" class="py-24" style="background: #F8FAFC;">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="text-center mb-16">
                    <span class="text-xs font-bold uppercase tracking-widest px-3 py-1.5 rounded-full inline-block mb-6" style="background: rgba(232,160,32,0.1); color: #C68400;">Écosystème de Partenaires</span>
                    <h2 class="text-4xl font-black mb-4" style="color: #0B1437;">Partenaires Stratégiques</h2>
                    <p class="text-gray-500 text-lg max-w-2xl mx-auto">Nos partenariats garantissent la reconnaissance internationale et l'ancrage africain de nos certifications.</p>
                </div>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="partenaire in partenaires" :key="partenaire.id"
                        class="bg-white rounded-2xl p-7 shadow-sm hover:shadow-lg transition-all duration-300 border border-gray-100 hover:-translate-y-0.5">
                        <div class="flex items-start gap-5">
                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #0B1437, #1E3A8A);">
                                <i :class="[partenaire.logo_icon, 'text-xl text-white']"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold mb-1" style="color: #0B1437;">{{ partenaire.nom }}</h3>
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full mb-2 inline-block" style="background: #FEF7E8; color: #C68400;">{{ partenaire.pays }}</span>
                                <p class="text-sm text-gray-500 leading-relaxed">{{ partenaire.description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══ CERTIFICATIONS & RECONNAISSANCE ══ -->
        <section v-if="certifications.length" class="py-24" style="background: linear-gradient(135deg, #060C22 0%, #0B1437 100%);">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="text-center mb-16">
                    <span class="text-xs font-bold uppercase tracking-widest px-3 py-1.5 rounded-full inline-block mb-6 border" style="background: rgba(232,160,32,0.12); border-color: rgba(232,160,32,0.3); color: #E8A020;">Valeur Reconnue</span>
                    <h2 class="text-4xl font-black mb-4 text-white">Certifications &<br/>Reconnaissance</h2>
                    <p class="text-lg max-w-2xl mx-auto" style="color: rgba(255,255,255,0.65);">À l'issue de votre parcours, obtenez des certifications qui valident votre expertise.</p>
                </div>
                <div class="grid md:grid-cols-2 gap-6">
                    <div v-for="cert in certifications" :key="cert.id"
                        class="rounded-2xl p-8 border transition-all duration-300"
                        style="background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.1);">
                        <div class="flex items-start gap-5">
                            <div class="w-16 h-16 rounded-2xl flex items-center justify-center flex-shrink-0"
                                :style="`background: ${cert.couleur}20; border: 2px solid ${cert.couleur}40;`">
                                <i :class="[cert.badge_icon, 'text-2xl']" :style="`color: ${cert.couleur};`"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white mb-2">{{ cert.titre }}</h3>
                                <p class="text-sm leading-relaxed" style="color: rgba(255,255,255,0.6);">{{ cert.description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══ CALL TO ACTION ══ -->
        <section v-if="session" id="contact-cta" class="py-24 bg-white">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="rounded-3xl overflow-hidden relative" style="background: linear-gradient(135deg, #E8A020 0%, #C68400 40%, #A05A00 100%);">
                    <div class="absolute top-0 right-0 w-64 h-64 rounded-full opacity-15" style="background: white; transform: translate(30%, -30%);"></div>
                    <div class="absolute bottom-0 left-0 w-48 h-48 rounded-full opacity-10" style="background: white; transform: translate(-30%, 30%);"></div>

                    <div class="relative px-8 md:px-16 py-16 grid md:grid-cols-2 gap-10 items-center">
                        <div>
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-6" style="background: rgba(255,255,255,0.2); color: white;">
                                <i class="bi bi-fire"></i>
                                {{ session.badge_text }}
                            </div>
                            <h2 class="text-4xl md:text-5xl font-black text-white mb-5 leading-tight">
                                {{ session.cta_titre }}
                            </h2>
                            <p v-if="session.cta_description" class="text-lg leading-relaxed mb-8" style="color: rgba(255,255,255,0.85);">
                                {{ session.cta_description }}
                            </p>
                            <div class="flex flex-wrap gap-4">
                                <a :href="`mailto:${contactSettings?.contact_email || 'masterclass@globaltecheducation.com'}`"
                                    class="inline-flex items-center gap-2 px-8 py-4 rounded-xl font-bold text-sm transition-all duration-300 hover:shadow-xl hover:scale-105"
                                    style="background: #0B1437; color: white;">
                                    <i class="bi bi-envelope-fill"></i>
                                    Candidater maintenant
                                </a>
                                <a :href="`tel:${contactSettings?.contact_phone || ''}`"
                                    class="inline-flex items-center gap-2 px-8 py-4 rounded-xl font-bold text-sm border-2 border-white text-white transition-all duration-300 hover:bg-white hover:text-yellow-700">
                                    <i class="bi bi-telephone-fill"></i>
                                    Appeler un conseiller
                                </a>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div v-for="info in [
                                { icon: 'bi-calendar-check', titre: 'Prochaine session',   detail: session.date_session },
                                { icon: 'bi-people',         titre: 'Places disponibles',  detail: `${session.places_restantes} places restantes sur ${session.places_total}` },
                                { icon: 'bi-cash-coin',      titre: 'Investissement',       detail: `À partir de ${session.prix_a_partir} / programme` },
                                { icon: 'bi-shield-check',   titre: 'Financement',          detail: session.financement },
                            ]" :key="info.titre"
                                class="flex items-center gap-4 p-4 rounded-2xl"
                                style="background: rgba(255,255,255,0.15); backdrop-filter: blur(8px);">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background: rgba(255,255,255,0.2);">
                                    <i :class="[info.icon, 'text-white text-lg']"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wider text-white/70">{{ info.titre }}</p>
                                    <p class="text-sm font-bold text-white">{{ info.detail }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </LayoutFront>
</template>
