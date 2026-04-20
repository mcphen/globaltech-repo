<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import { useDarkMode } from '@/composables/useDarkMode';
import { useToast } from 'vue-toast-notification';
import { computed } from 'vue';

const page = usePage();
const $toast = useToast();
const { isDark } = useDarkMode();
const contactSettings = computed<any>(() => page.props.contactSettings);

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    subject: '',
    description: '',
    company: '',
    inquiry_type: 'general',
});

const submitForm = () => {
    form.post(route('contact.store'), {
        preserveScroll: true,
        onSuccess: () => {
            $toast.success('Message envoyé ! Nous vous répondrons sous 24h.', {
                position: 'top-right', duration: 7000, dismissible: true,
            });
            form.reset();
            form.clearErrors();
        },
        onError: () => {
            $toast.error('Veuillez corriger les erreurs avant de soumettre.', {
                position: 'top-right', duration: 5000, dismissible: true,
            });
        },
    });
};

const inquiryTypes = [
    { value: 'general',     label: 'Demande générale' },
    { value: 'formation',   label: 'Information sur une formation' },
    { value: 'corporate',   label: 'Formation entreprise' },
    { value: 'consulting',  label: 'Consulting / Projet' },
    { value: 'partnership', label: 'Partenariat' },
];

const offices = [
    { country: 'Côte d\'Ivoire', city: 'Abidjan', flag: '🇨🇮', role: 'Siège',          address: 'Plateau, Abidjan',  email: 'abidjan@globaltech-edu.com' },
    { country: 'Sénégal',        city: 'Dakar',   flag: '🇸🇳', role: 'Bureau régional', address: 'Plateau, Dakar',    email: 'dakar@globaltech-edu.com' },
    { country: 'Mali',           city: 'Bamako',  flag: '🇲🇱', role: 'Bureau régional', address: 'ACI 2000',          email: 'bamako@globaltech-edu.com' },
    { country: 'Cameroun',       city: 'Douala',  flag: '🇨🇲', role: 'Bureau régional', address: 'Bonanjo',           email: 'douala@globaltech-edu.com' },
];

// Dark-mode styles
const sectionBg      = computed(() => isDark.value ? '#0B1437' : '#F8FAFC');
const titleColor     = computed(() => isDark.value ? '#F1F5F9' : '#0B1437');
const descColor      = computed(() => isDark.value ? '#94A3B8' : '#637084');
const labelColor     = computed(() => isDark.value ? '#CBD5E1' : '#0B1437');
const contactValueColor = computed(() => isDark.value ? '#E2E8F0' : '#0B1437');
const sectionHeadColor = computed(() => isDark.value ? '#94A3B8' : '#0B1437');

const iconBgGold     = computed(() => isDark.value ? 'rgba(232,160,32,0.15)'  : '#FEF7E8');
const iconBgBlue     = computed(() => isDark.value ? 'rgba(37,99,235,0.15)'   : '#EFF6FF');
const iconBgGreen    = computed(() => isDark.value ? 'rgba(22,163,74,0.15)'   : '#F0FDF4');

const officeRoleBg   = computed(() => isDark.value ? 'rgba(37,99,235,0.15)' : '#EFF6FF');
const officeRoleColor = computed(() => isDark.value ? '#60A5FA' : '#2563EB');

// Form inputs
const inputBase      = computed(() => isDark.value
    ? 'background: #1A2844; border-color: rgba(255,255,255,0.1); color: #F1F5F9;'
    : 'background: white; border-color: #E5E7EB; color: #0B1437;');
const inputError     = computed(() => isDark.value
    ? 'background: rgba(239,68,68,0.1); border-color: #EF4444; color: #F1F5F9;'
    : 'background: #FEF2F2; border-color: #F87171; color: #0B1437;');
const pillActive     = 'background: #0B1437; color: white; border-color: #0B1437;';
const pillInactive   = computed(() => isDark.value
    ? 'background: rgba(255,255,255,0.06); color: #94A3B8; border-color: rgba(255,255,255,0.1);'
    : 'background: white; color: #475569; border-color: #E2E8F0;');
</script>

<template>
    <Head title="Contact — GlobalTECH EDUCATION Africa" />
    <LayoutFront>

        <!-- Hero -->
        <section class="py-24 relative overflow-hidden" style="background: linear-gradient(rgba(255,255,255,0.025) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.025) 1px,transparent 1px),linear-gradient(135deg,#060C22 0%,#0B1437 42%,#0E2060 72%,#091830 100%); background-size:60px 60px,60px 60px,100% 100%;">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
                <div class="max-w-2xl">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-5"
                        style="background: rgba(232, 160, 32, 0.15); color: #E8A020; border: 1px solid rgba(232, 160, 32, 0.3);">
                        <i class="bi bi-chat-dots-fill"></i>
                        Parlons de votre projet
                    </div>
                    <h1 class="text-5xl lg:text-6xl font-black text-white mb-5 leading-tight" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                        Contactez-nous
                    </h1>
                    <p class="text-xl text-white/70 leading-relaxed">
                        Notre équipe est disponible pour répondre à toutes vos questions sur nos formations, consulting et projets.
                    </p>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section class="py-20" :style="`background: ${sectionBg};`">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="grid lg:grid-cols-3 gap-12">

                    <!-- Left: Info + Offices -->
                    <div class="lg:col-span-1 space-y-8">

                        <!-- Quick Contact -->
                        <div class="gt-card p-6">
                            <h3 class="text-lg font-black mb-5" :style="`color: ${titleColor}; font-family: 'Plus Jakarta Sans', sans-serif;`">
                                Siège Social
                            </h3>
                            <div class="space-y-4">
                                <div class="flex items-start gap-3" v-if="contactSettings?.contact_address">
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" :style="`background: ${iconBgGold};`">
                                        <i class="bi bi-geo-alt-fill text-sm" style="color: #E8A020;"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold uppercase tracking-wide mb-0.5" style="color: #94A3B8;">Adresse</p>
                                        <p class="text-sm" :style="`color: ${contactValueColor};`">{{ contactSettings.contact_address }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3" v-if="contactSettings?.contact_phone">
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" :style="`background: ${iconBgBlue};`">
                                        <i class="bi bi-telephone-fill text-sm" style="color: #2563EB;"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold uppercase tracking-wide mb-0.5" style="color: #94A3B8;">Téléphone</p>
                                        <a :href="`tel:${contactSettings.contact_phone}`"
                                            class="text-sm font-medium hover:text-blue-400 transition-colors"
                                            :style="`color: ${contactValueColor};`">
                                            {{ contactSettings.contact_phone }}
                                        </a>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3" v-if="contactSettings?.contact_email">
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" :style="`background: ${iconBgGreen};`">
                                        <i class="bi bi-envelope-fill text-sm" style="color: #16A34A;"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold uppercase tracking-wide mb-0.5" style="color: #94A3B8;">Email</p>
                                        <a :href="`mailto:${contactSettings.contact_email}`"
                                            class="text-sm font-medium hover:text-green-400 transition-colors"
                                            :style="`color: ${contactValueColor};`">
                                            {{ contactSettings.contact_email }}
                                        </a>
                                    </div>
                                </div>
                                <div v-if="!contactSettings?.contact_address && !contactSettings?.contact_phone && !contactSettings?.contact_email">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" :style="`background: ${iconBgGold};`">
                                            <i class="bi bi-geo-alt-fill text-sm" style="color: #E8A020;"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold uppercase tracking-wide mb-0.5" style="color: #94A3B8;">Adresse</p>
                                            <p class="text-sm" :style="`color: ${contactValueColor};`">Plateau, Abidjan, Côte d'Ivoire</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Offices -->
                        <div>
                            <h3 class="text-sm font-black uppercase tracking-widest mb-4" :style="`color: ${sectionHeadColor};`">Nos Bureaux</h3>
                            <div class="space-y-3">
                                <div v-for="office in offices" :key="office.country" class="gt-card p-4 flex items-start gap-3">
                                    <span class="text-2xl flex-shrink-0">{{ office.flag }}</span>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2 mb-0.5">
                                            <span class="text-sm font-bold" :style="`color: ${titleColor};`">{{ office.city }}</span>
                                            <span class="text-xs px-1.5 py-0.5 rounded-full font-semibold"
                                                :style="`background: ${officeRoleBg}; color: ${officeRoleColor};`">
                                                {{ office.role }}
                                            </span>
                                        </div>
                                        <p class="text-xs" :style="`color: ${descColor};`">{{ office.address }}</p>
                                        <a :href="`mailto:${office.email}`"
                                            class="text-xs hover:text-blue-400 transition-colors"
                                            style="color: #94A3B8;">
                                            {{ office.email }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- RDV CTA -->
                        <div class="rounded-2xl p-5 text-white text-center" style="background: linear-gradient(135deg, #0B1437, #152050);">
                            <i class="bi bi-calendar-check text-2xl mb-2 block" style="color: #E8A020;"></i>
                            <h4 class="font-bold mb-2">Préférez un RDV ?</h4>
                            <p class="text-xs text-white/70 mb-4">Réservez un entretien gratuit de 30 min avec un conseiller.</p>
                            <Link :href="route('appointment.create')" class="gt-btn-gold w-full rounded-xl py-2.5 text-sm font-bold">
                                Réserver maintenant
                            </Link>
                        </div>
                    </div>

                    <!-- Right: Contact Form -->
                    <div class="lg:col-span-2">
                        <div class="gt-card p-8 lg:p-10">
                            <h2 class="text-2xl font-black mb-2" :style="`color: ${titleColor}; font-family: 'Plus Jakarta Sans', sans-serif;`">
                                Envoyez-nous un message
                            </h2>
                            <p class="text-sm mb-8" :style="`color: ${descColor};`">Nous vous répondrons dans les 24 heures ouvrables.</p>

                            <form @submit.prevent="submitForm" class="space-y-5">
                                <!-- Type of inquiry -->
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wide mb-2" :style="`color: ${labelColor};`">
                                        Type de demande
                                    </label>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                        <button v-for="type in inquiryTypes" :key="type.value" type="button"
                                            @click="form.inquiry_type = type.value"
                                            class="px-3 py-2.5 rounded-xl text-xs font-semibold border-2 transition-all text-center"
                                            :style="form.inquiry_type === type.value ? pillActive : pillInactive">
                                            {{ type.label }}
                                        </button>
                                    </div>
                                </div>

                                <!-- Name -->
                                <div class="grid sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wide mb-1.5" :style="`color: ${labelColor};`">Prénom *</label>
                                        <input v-model="form.first_name" type="text" required
                                            class="w-full px-4 py-3 rounded-xl border text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 transition-all"
                                            :style="form.errors.first_name ? inputError : inputBase"
                                            placeholder="Votre prénom" />
                                        <p v-if="form.errors.first_name" class="text-xs text-red-400 mt-1">{{ form.errors.first_name }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wide mb-1.5" :style="`color: ${labelColor};`">Nom *</label>
                                        <input v-model="form.last_name" type="text" required
                                            class="w-full px-4 py-3 rounded-xl border text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 transition-all"
                                            :style="form.errors.last_name ? inputError : inputBase"
                                            placeholder="Votre nom" />
                                        <p v-if="form.errors.last_name" class="text-xs text-red-400 mt-1">{{ form.errors.last_name }}</p>
                                    </div>
                                </div>

                                <!-- Email + Phone -->
                                <div class="grid sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wide mb-1.5" :style="`color: ${labelColor};`">Email *</label>
                                        <input v-model="form.email" type="email" required
                                            class="w-full px-4 py-3 rounded-xl border text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 transition-all"
                                            :style="form.errors.email ? inputError : inputBase"
                                            placeholder="votre@email.com" />
                                        <p v-if="form.errors.email" class="text-xs text-red-400 mt-1">{{ form.errors.email }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wide mb-1.5" :style="`color: ${labelColor};`">Téléphone</label>
                                        <input v-model="form.phone" type="tel"
                                            class="w-full px-4 py-3 rounded-xl border text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 transition-all"
                                            :style="inputBase"
                                            placeholder="+225 XX XX XX XX" />
                                    </div>
                                </div>

                                <!-- Company -->
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wide mb-1.5" :style="`color: ${labelColor};`">
                                        Entreprise / Organisation
                                        <span class="normal-case font-normal ml-1" style="color: #94A3B8;">(optionnel)</span>
                                    </label>
                                    <input v-model="form.company" type="text"
                                        class="w-full px-4 py-3 rounded-xl border text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 transition-all"
                                        :style="inputBase"
                                        placeholder="Nom de votre entreprise" />
                                </div>

                                <!-- Subject -->
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wide mb-1.5" :style="`color: ${labelColor};`">Objet *</label>
                                    <input v-model="form.subject" type="text" required
                                        class="w-full px-4 py-3 rounded-xl border text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 transition-all"
                                        :style="form.errors.subject ? inputError : inputBase"
                                        placeholder="Ex : Demande de devis formation PMP®" />
                                    <p v-if="form.errors.subject" class="text-xs text-red-400 mt-1">{{ form.errors.subject }}</p>
                                </div>

                                <!-- Message -->
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wide mb-1.5" :style="`color: ${labelColor};`">Message *</label>
                                    <textarea v-model="form.description" required rows="5"
                                        class="w-full px-4 py-3 rounded-xl border text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 transition-all resize-none"
                                        :style="form.errors.description ? inputError : inputBase"
                                        placeholder="Décrivez votre besoin en détail..."></textarea>
                                    <p v-if="form.errors.description" class="text-xs text-red-400 mt-1">{{ form.errors.description }}</p>
                                </div>

                                <!-- Submit -->
                                <button type="submit" :disabled="form.processing"
                                    class="w-full py-4 rounded-xl text-base font-black text-white flex items-center justify-center gap-2 transition-all duration-200 hover:shadow-xl disabled:opacity-60"
                                    style="background: linear-gradient(135deg, #E8A020, #C68400);">
                                    <svg v-if="form.processing" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                    </svg>
                                    <i v-else class="bi bi-send-fill"></i>
                                    {{ form.processing ? 'Envoi en cours...' : 'Envoyer le message' }}
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </section>

    </LayoutFront>
</template>
