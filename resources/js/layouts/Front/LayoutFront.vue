<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { useDarkMode } from '@/composables/useDarkMode';

interface ContactSettings {
    contact_phone: string;
    contact_phone_fixed: string;
    contact_email: string;
    social_facebook: string;
    social_twitter: string;
    social_youtube: string;
    social_linkedin: string;
    social_tiktok: string;
    social_instagram: string;
    contact_address: string;
    opening_hours: string;
}

interface FormationCategoryProp {
    id: number;
    name: string;
    slug: string;
    icon: string;
    color: string;
    is_featured: boolean;
}

const page = usePage();
const contactSettings = computed<ContactSettings>(() => page.props.contactSettings as ContactSettings);
const cart = computed<any>(() => (page.props as any).cart ?? { items: [], count: 0, subtotal: 0, total: 0 });
const auth = computed<any>(() => (page.props as any).auth);
const dbCategories = computed<FormationCategoryProp[]>(() => (page.props as any).formationCategories ?? []);

const { isDark, theme, cycle, icon, label } = useDarkMode();
type Theme = 'system' | 'light' | 'dark';
const setTheme = (t: Theme) => { theme.value = t; };

const isLoading = ref(false);
const isScrolled = ref(false);
const mobileMenuOpen = ref(false);
const profileMenuOpen = ref(false);
const cartMenuOpen = ref(false);
const formationsDropdownOpen = ref(false);

const formationCategories = computed(() => {
    const cats = dbCategories.value.map(cat => ({
        label: cat.name,
        icon: cat.icon,
        href: `/formations?category=${cat.slug}`,
        color: '',
        hexColor: cat.color,
        badge: cat.is_featured ? 'Star' : undefined,
    }));
    cats.push({ label: 'Toutes les formations', icon: 'bi-grid', href: '/formations', color: 'text-gray-600', hexColor: '', badge: undefined });
    return cats;
});

const handleScroll = () => { isScrolled.value = window.scrollY > 60; };

const removeFromCart = (id: number) => {
    router.post(route('cart.remove', { id }), {}, {
        preserveScroll: true, preserveState: true, only: ['cart'], replace: true,
    });
};

const clearCart = () => {
    router.post(route('cart.clear'), {}, {
        preserveScroll: true, preserveState: true, only: ['cart'], replace: true,
    });
};

const closeAll = () => {
    mobileMenuOpen.value = false;
    profileMenuOpen.value = false;
    cartMenuOpen.value = false;
    formationsDropdownOpen.value = false;
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll, { passive: true });
    router.on('start', (event: any) => {
        const visit = event?.detail?.visit;
        if (Array.isArray(visit?.only) && visit.only.length > 0) return;
        isLoading.value = true;
        closeAll();
    });
    router.on('finish', (event: any) => {
        const visit = event?.detail?.visit;
        if (Array.isArray(visit?.only) && visit.only.length > 0) return;
        setTimeout(() => { isLoading.value = false; }, 200);
    });
});

onUnmounted(() => { window.removeEventListener('scroll', handleScroll); });

// Computed styles for dark-mode-aware elements
const headerBg = computed(() =>
    isDark.value
        ? (isScrolled.value ? '#0D1526' : '#0B1437')
        : (isScrolled.value ? '#FFFFFF' : '#FFFFFF')
);
const headerBorder = computed(() =>
    isDark.value ? 'rgba(255,255,255,0.08)' : '#E5E7EB'
);
const headerShadow = computed(() =>
    isScrolled.value
        ? (isDark.value ? '0 4px 24px rgba(0,0,0,0.4)' : '0 4px 24px rgba(0,0,0,0.08)')
        : 'none'
);
const navLinkClass = computed(() =>
    isDark.value
        ? 'text-slate-300 hover:text-white hover:bg-white/8'
        : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50'
);
const navActiveClass = computed(() =>
    isDark.value ? 'text-yellow-400 bg-yellow-400/10' : 'text-yellow-600 bg-yellow-50'
);
const dropdownBg = computed(() => isDark.value ? '#131F36' : '#FFFFFF');
const dropdownBorder = computed(() => isDark.value ? 'rgba(255,255,255,0.08)' : '#F1F5F9');
const mobileBg = computed(() => isDark.value ? '#0D1526' : '#FFFFFF');
</script>

<template>
    <Head>
        <link rel="icon" type="image/jpeg" href="/images/logo_globaltech.jpeg" />
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="description" content="GlobalTECH EDUCATION Africa — Leader en formations certifiantes PMP, Informatique et Management en Afrique. Certifiez votre excellence professionnelle." />
        <meta name="keywords" content="formation certifiante, PMP, informatique, management, Côte d'Ivoire, Afrique, consulting, certification professionnelle" />
        <meta name="author" content="GlobalTECH EDUCATION Africa" />
        <meta name="robots" content="index, follow" />
        <meta property="og:type" content="website" />
        <meta property="og:title" content="GlobalTECH EDUCATION Africa" />
        <meta property="og:description" content="Leader en formations certifiantes PMP, Informatique et Management en Afrique." />
        <meta property="og:site_name" content="GlobalTECH EDUCATION Africa" />
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous" />
    </Head>

    <!-- Page Loader -->
    <Transition name="loader">
        <div v-if="isLoading" class="gt-loader">
            <div class="gt-loader-ring mb-4"></div>
            <span class="text-white/60 text-sm font-medium tracking-widest uppercase">Chargement</span>
        </div>
    </Transition>

    <div class="min-h-screen flex flex-col" :style="`background: ${isDark ? '#0D1526' : '#FFFFFF'}; font-family: 'Plus Jakarta Sans', Inter, sans-serif;`">

        <!-- ══ TOP BAR ══ -->
        <div class="hidden md:block" style="background: #060C22;">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 flex items-center justify-between h-10">
                <div class="flex items-center gap-6 text-white/70 text-xs">
                    <span v-if="contactSettings?.contact_email" class="flex items-center gap-1.5">
                        <i class="bi bi-envelope text-yellow-400"></i>
                        {{ contactSettings.contact_email }}
                    </span>
                    <span v-if="contactSettings?.contact_phone" class="flex items-center gap-1.5">
                        <i class="bi bi-telephone text-yellow-400"></i>
                        {{ contactSettings.contact_phone }}
                    </span>
                    <span class="flex items-center gap-1.5">
                        <i class="bi bi-geo-alt text-yellow-400"></i>
                        Abidjan, Côte d'Ivoire — Présence panafricaine
                    </span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-3">
                        <a v-if="contactSettings?.social_linkedin" :href="contactSettings.social_linkedin" target="_blank" class="text-white/50 hover:text-yellow-400 transition-colors">
                            <i class="bi bi-linkedin"></i>
                        </a>
                        <a v-if="contactSettings?.social_facebook" :href="contactSettings.social_facebook" target="_blank" class="text-white/50 hover:text-yellow-400 transition-colors">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a v-if="contactSettings?.social_twitter" :href="contactSettings.social_twitter" target="_blank" class="text-white/50 hover:text-yellow-400 transition-colors">
                            <i class="bi bi-twitter-x"></i>
                        </a>
                        <a v-if="contactSettings?.social_youtube" :href="contactSettings.social_youtube" target="_blank" class="text-white/50 hover:text-yellow-400 transition-colors">
                            <i class="bi bi-youtube"></i>
                        </a>
                    </div>
                    <div v-if="!auth?.user" class="flex items-center gap-2 border-l border-white/10 pl-4">
                        <Link :href="route('login')" class="text-white/70 hover:text-white text-xs font-medium transition-colors">
                            Connexion
                        </Link>
                        <Link :href="route('prospect.register')" class="px-3 py-1 text-xs font-semibold rounded" style="background: #E8A020; color: white;">
                            S'inscrire
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══ MAIN NAVIGATION ══ -->
        <header
            class="sticky top-0 z-50 transition-all duration-300"
            :style="`background: ${headerBg}; border-bottom: 1px solid ${headerBorder}; box-shadow: ${headerShadow};`"
        >
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="flex items-center justify-between h-16 md:h-24">

                    <!-- Logo -->
                    <Link :href="route('home')" class="flex items-center flex-shrink-0">
                        <img src="/images/logo_globaltech.jpeg" alt="GlobalTECH EDUCATION Africa" class="h-12 md:h-20 w-auto object-contain" />
                    </Link>

                    <!-- Desktop Nav -->
                    <nav class="hidden lg:flex items-center gap-1">
                        <Link :href="route('home')"
                            class="px-4 py-2 text-sm font-semibold rounded-lg transition-all duration-200"
                            :class="page.url === '/' ? navActiveClass : navLinkClass">
                            Accueil
                        </Link>

                        <Link href="/about"
                            class="px-4 py-2 text-sm font-semibold rounded-lg transition-all duration-200"
                            :class="page.url.startsWith('/about') ? navActiveClass : navLinkClass">
                            À Propos
                        </Link>

                        <!-- Formations Dropdown -->
                        <div class="relative"
                            @mouseenter="formationsDropdownOpen = true"
                            @mouseleave="formationsDropdownOpen = false">
                            <Link :href="route('formations')"
                                class="flex items-center gap-1 px-4 py-2 text-sm font-semibold rounded-lg transition-all duration-200"
                                :class="page.url.startsWith('/formations') ? navActiveClass : navLinkClass">
                                Formations
                                <i class="bi bi-chevron-down text-xs transition-transform duration-200" :class="formationsDropdownOpen ? 'rotate-180' : ''"></i>
                            </Link>

                            <Transition name="dropdown">
                                <div v-show="formationsDropdownOpen"
                                    class="absolute top-full left-0 mt-1 w-72 rounded-2xl shadow-xl p-2 z-50"
                                    :style="`background: ${dropdownBg}; border: 1px solid ${dropdownBorder};`">
                                    <div class="px-3 py-2 mb-1">
                                        <p class="text-xs font-bold uppercase tracking-widest" style="color: #E8A020;">Nos programmes</p>
                                    </div>
                                    <Link v-for="cat in formationCategories" :key="cat.href" :href="cat.href"
                                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors group"
                                        :class="isDark ? 'hover:bg-white/6' : 'hover:bg-gray-50'">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors"
                                            :class="isDark ? 'bg-white/8 group-hover:bg-white/12' : 'bg-gray-100 group-hover:bg-white'">
                                            <i :class="[cat.icon, 'text-sm']" :style="cat.hexColor ? `color: ${cat.hexColor}` : ''"></i>
                                        </div>
                                        <span class="text-sm font-medium transition-colors"
                                            :class="isDark ? 'text-slate-300 group-hover:text-white' : 'text-gray-700 group-hover:text-gray-900'">
                                            {{ cat.label }}
                                        </span>
                                        <span v-if="cat.badge" class="ml-auto text-xs font-bold px-1.5 py-0.5 rounded-md" style="background: #FEF7E8; color: #C68400;">{{ cat.badge }}</span>
                                    </Link>
                                </div>
                            </Transition>
                        </div>

                        <Link href="/services"
                            class="px-4 py-2 text-sm font-semibold rounded-lg transition-all duration-200"
                            :class="page.url.startsWith('/services') ? navActiveClass : navLinkClass">
                            Consulting
                        </Link>

                        <Link href="/executive-masterclass"
                            class="px-4 py-2 text-sm font-semibold rounded-lg transition-all duration-200"
                            :class="page.url.startsWith('/executive-masterclass') ? navActiveClass : navLinkClass">
                            Executive Masterclass
                        </Link>

                        <Link :href="route('blog')"
                            class="px-4 py-2 text-sm font-semibold rounded-lg transition-all duration-200"
                            :class="page.url.startsWith('/blog') ? navActiveClass : navLinkClass">
                            Actualités
                        </Link>

                        <Link :href="route('contact')"
                            class="px-4 py-2 text-sm font-semibold rounded-lg transition-all duration-200"
                            :class="page.url.startsWith('/contact') ? navActiveClass : navLinkClass">
                            Contact
                        </Link>
                    </nav>

                    <!-- Right Actions -->
                    <div class="hidden lg:flex items-center gap-2">

                        <!-- Dark mode toggle -->
                        <button @click="cycle"
                            class="relative p-2 rounded-lg transition-all group"
                            :class="isDark ? 'text-slate-300 hover:text-yellow-400 hover:bg-white/8' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100'"
                            :title="`Thème : ${label}`">
                            <i :class="['bi', icon, 'text-base']"></i>
                            <!-- Tooltip -->
                            <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 text-xs font-semibold rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"
                                style="background: #0B1437; color: white;">
                                {{ label }}
                            </span>
                        </button>

                        <!-- Cart -->
                        <div class="relative">
                            <button @click="cartMenuOpen = !cartMenuOpen; profileMenuOpen = false"
                                class="relative p-2 rounded-lg transition-all"
                                :class="isDark ? 'text-slate-300 hover:text-white hover:bg-white/8' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'">
                                <i class="bi bi-bag text-lg"></i>
                                <span v-if="cart.count > 0"
                                    class="absolute -top-1 -right-1 w-5 h-5 rounded-full text-white text-xs font-bold flex items-center justify-center"
                                    style="background: #E8A020;">
                                    {{ cart.count }}
                                </span>
                            </button>
                            <!-- Cart dropdown -->
                            <Transition name="dropdown">
                                <div v-show="cartMenuOpen"
                                    class="absolute right-0 top-12 w-80 rounded-2xl shadow-xl z-50 overflow-hidden"
                                    :style="`background: ${dropdownBg}; border: 1px solid ${dropdownBorder};`">
                                    <div class="px-4 py-3 flex items-center justify-between" :style="`border-bottom: 1px solid ${dropdownBorder};`">
                                        <span class="font-bold" :class="isDark ? 'text-white' : 'text-gray-900'">Panier</span>
                                        <button v-if="cart.items?.length" @click="clearCart" class="text-xs text-red-500 hover:underline">Vider</button>
                                    </div>
                                    <div v-if="!cart.items?.length" class="p-6 text-center text-sm" :class="isDark ? 'text-slate-400' : 'text-gray-500'">
                                        <i class="bi bi-bag text-3xl mb-2 block opacity-30"></i>
                                        Votre panier est vide
                                    </div>
                                    <ul v-else class="max-h-72 overflow-auto divide-y" :class="isDark ? 'divide-white/6' : 'divide-gray-50'">
                                        <li v-for="item in cart.items" :key="item.id" class="px-4 py-3 flex items-center gap-3">
                                            <img v-if="item.image_url" :src="item.image_url" :alt="item.title" class="w-12 h-12 object-cover rounded-lg" />
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium truncate" :class="isDark ? 'text-white' : 'text-gray-900'">{{ item.title }}</p>
                                                <p class="text-xs" :class="isDark ? 'text-slate-400' : 'text-gray-500'">x{{ item.quantity }} · {{ new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(item.price) }}</p>
                                            </div>
                                            <button @click="removeFromCart(item.id)" class="text-gray-400 hover:text-red-500 transition-colors">
                                                <i class="bi bi-x-lg text-sm"></i>
                                            </button>
                                        </li>
                                    </ul>
                                    <div v-if="cart.items?.length" class="p-4" :style="`border-top: 1px solid ${dropdownBorder};`">
                                        <div class="flex justify-between text-sm font-semibold mb-3" :class="isDark ? 'text-white' : ''">
                                            <span>Sous-total</span>
                                            <span>{{ new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(cart.subtotal) }}</span>
                                        </div>
                                        <div class="flex gap-2">
                                            <Link :href="route('cart.index')" @click="cartMenuOpen = false"
                                                class="flex-1 py-2 text-center text-sm font-semibold rounded-lg border-2 transition-colors"
                                                style="border-color: #0B1437; color: #0B1437;">
                                                Voir panier
                                            </Link>
                                            <button @click="router.visit(route('cart.checkout'))"
                                                class="flex-1 py-2 text-sm font-bold rounded-lg text-white transition-colors"
                                                style="background: #0B1437;">
                                                Commander
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </Transition>
                        </div>

                        <!-- Profile or Auth -->
                        <template v-if="auth?.user">
                            <div class="relative">
                                <button @click="profileMenuOpen = !profileMenuOpen; cartMenuOpen = false"
                                    class="flex items-center gap-2 px-3 py-2 rounded-lg transition-all"
                                    :class="isDark ? 'hover:bg-white/8' : 'hover:bg-gray-100'">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold"
                                        style="background: linear-gradient(135deg, #0B1437, #2563EB);">
                                        {{ auth.user.name?.charAt(0)?.toUpperCase() }}
                                    </div>
                                    <span class="text-sm font-semibold" :class="isDark ? 'text-slate-200' : 'text-gray-700'">{{ auth.user.name?.split(' ')[0] }}</span>
                                    <i class="bi bi-chevron-down text-xs" :class="isDark ? 'text-slate-400' : 'text-gray-500'"></i>
                                </button>
                                <Transition name="dropdown">
                                    <div v-show="profileMenuOpen"
                                        class="absolute right-0 top-12 w-52 rounded-2xl shadow-xl py-2 z-50"
                                        :style="`background: ${dropdownBg}; border: 1px solid ${dropdownBorder};`">
                                        <Link :href="route('prospect.profile')"
                                            class="flex items-center gap-3 px-4 py-2.5 text-sm rounded-lg mx-1 transition-colors"
                                            :class="isDark ? 'text-slate-300 hover:bg-white/6 hover:text-white' : 'text-gray-700 hover:bg-gray-50'">
                                            <i class="bi bi-person-circle text-gray-400"></i> Profil
                                        </Link>
                                        <Link :href="route('prospect.dashboard')"
                                            class="flex items-center gap-3 px-4 py-2.5 text-sm rounded-lg mx-1 transition-colors"
                                            :class="isDark ? 'text-slate-300 hover:bg-white/6 hover:text-white' : 'text-gray-700 hover:bg-gray-50'">
                                            <i class="bi bi-speedometer2 text-gray-400"></i> Tableau de bord
                                        </Link>
                                        <div class="border-t my-1" :class="isDark ? 'border-white/8' : 'border-gray-100'"></div>
                                        <Link method="post" :href="route('logout')" as="button"
                                            class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-500 hover:bg-red-500/10 rounded-lg mx-1 transition-colors">
                                            <i class="bi bi-box-arrow-right"></i> Déconnexion
                                        </Link>
                                    </div>
                                </Transition>
                            </div>
                        </template>
                        <template v-else>
                        </template>
                    </div>

                    <!-- Mobile right: dark toggle + hamburger -->
                    <div class="lg:hidden flex items-center gap-1">
                        <button @click="cycle"
                            class="p-2 rounded-lg transition-all"
                            :class="isDark ? 'text-slate-300 hover:text-yellow-400' : 'text-gray-500 hover:text-gray-900'"
                            :title="`Thème : ${label}`">
                            <i :class="['bi', icon, 'text-base']"></i>
                        </button>
                        <button @click="mobileMenuOpen = !mobileMenuOpen"
                            class="p-2 rounded-lg transition-colors"
                            :class="isDark ? 'text-slate-300 hover:bg-white/8' : 'text-gray-700 hover:bg-gray-100'">
                            <i :class="mobileMenuOpen ? 'bi bi-x-lg' : 'bi bi-list'" class="text-xl"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <Transition name="mobile-menu">
                <div v-show="mobileMenuOpen" class="lg:hidden" :style="`background: ${mobileBg}; border-top: 1px solid ${headerBorder};`">
                    <div class="max-w-7xl mx-auto px-6 py-4 space-y-1">
                        <Link :href="route('home')" @click="mobileMenuOpen = false"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-colors"
                            :class="page.url === '/' ? navActiveClass : (isDark ? 'text-slate-300 hover:bg-white/6' : 'text-gray-700 hover:bg-gray-50')">
                            <i class="bi bi-house w-5 text-center"></i> Accueil
                        </Link>
                        <Link href="/about" @click="mobileMenuOpen = false"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-colors"
                            :class="isDark ? 'text-slate-300 hover:bg-white/6' : 'text-gray-700 hover:bg-gray-50'">
                            <i class="bi bi-info-circle w-5 text-center"></i> À Propos
                        </Link>
                        <Link :href="route('formations')" @click="mobileMenuOpen = false"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-colors"
                            :class="isDark ? 'text-slate-300 hover:bg-white/6' : 'text-gray-700 hover:bg-gray-50'">
                            <i class="bi bi-mortarboard w-5 text-center"></i> Formations
                        </Link>
                        <Link href="/services" @click="mobileMenuOpen = false"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-colors"
                            :class="isDark ? 'text-slate-300 hover:bg-white/6' : 'text-gray-700 hover:bg-gray-50'">
                            <i class="bi bi-briefcase w-5 text-center"></i> Consulting
                        </Link>
                        <Link href="/executive-masterclass" @click="mobileMenuOpen = false"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-colors"
                            :class="isDark ? 'text-slate-300 hover:bg-white/6' : 'text-gray-700 hover:bg-gray-50'">
                            <i class="bi bi-award w-5 text-center"></i> Executive Masterclass
                        </Link>
                        <Link :href="route('blog')" @click="mobileMenuOpen = false"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-colors"
                            :class="isDark ? 'text-slate-300 hover:bg-white/6' : 'text-gray-700 hover:bg-gray-50'">
                            <i class="bi bi-newspaper w-5 text-center"></i> Actualités
                        </Link>
                        <Link :href="route('contact')" @click="mobileMenuOpen = false"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-colors"
                            :class="isDark ? 'text-slate-300 hover:bg-white/6' : 'text-gray-700 hover:bg-gray-50'">
                            <i class="bi bi-envelope w-5 text-center"></i> Contact
                        </Link>

                        <!-- Theme selector row -->
                        <div class="flex items-center gap-2 px-4 py-3" :style="`border-top: 1px solid ${headerBorder}; margin-top: 4px;`">
                            <span class="text-xs font-semibold flex-1" :class="isDark ? 'text-slate-400' : 'text-gray-500'">Apparence</span>
                            <div class="flex gap-1">
                                <button v-for="t in ['system', 'light', 'dark'] as const" :key="t" @click="setTheme(t); mobileMenuOpen = false"
                                    class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all"
                                    :class="theme === t
                                        ? 'text-white'
                                        : (isDark ? 'text-slate-400 hover:bg-white/8' : 'text-gray-500 hover:bg-gray-100')"
                                    :style="theme === t ? 'background: #E8A020;' : ''">
                                    <i :class="['bi', t === 'system' ? 'bi-display' : t === 'light' ? 'bi-sun-fill' : 'bi-moon-stars-fill']"></i>
                                    {{ t === 'system' ? 'Auto' : t === 'light' ? 'Clair' : 'Sombre' }}
                                </button>
                            </div>
                        </div>

                        <div class="pt-2 flex flex-col gap-2">
                            <template v-if="auth?.user">
                                <Link :href="route('prospect.dashboard')" @click="mobileMenuOpen = false"
                                    class="px-4 py-3 text-center rounded-xl text-sm font-bold text-white"
                                    style="background: #0B1437;">
                                    Mon Espace
                                </Link>
                                <Link method="post" :href="route('logout')" as="button" @click="mobileMenuOpen = false"
                                    class="px-4 py-3 text-center rounded-xl text-sm font-semibold text-red-500 border"
                                    :class="isDark ? 'border-red-500/30' : 'border-red-200'">
                                    Déconnexion
                                </Link>
                            </template>
                            <template v-else>
                                <Link :href="route('login')" @click="mobileMenuOpen = false"
                                    class="px-4 py-3 text-center rounded-xl text-sm font-semibold border-2 transition-colors"
                                    :style="`border-color: #0B1437; color: ${isDark ? '#E2E8F0' : '#0B1437'};`">
                                    Connexion
                                </Link>
                                <Link :href="route('prospect.register')" @click="mobileMenuOpen = false"
                                    class="px-4 py-3 text-center rounded-xl text-sm font-bold text-white"
                                    style="background: #E8A020;">
                                    S'inscrire
                                </Link>
                            </template>
                        </div>
                    </div>
                </div>
            </Transition>
        </header>

        <!-- ══ PAGE CONTENT ══ -->
        <main class="flex-1">
            <slot></slot>
        </main>

        <!-- ══ FOOTER ══ -->
        <footer style="background: #060C22; font-family: 'Plus Jakarta Sans', Inter, sans-serif;">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 py-16">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">

                    <!-- Brand Column -->
                    <div class="lg:col-span-1">
                        <Link :href="route('home')" class="flex items-center mb-5">
                            <img src="/images/logo_globaltech.jpeg" alt="GlobalTECH EDUCATION Africa" class="h-12 md:h-20 w-auto object-contain" />
                        </Link>
                        <p class="text-sm leading-relaxed mb-6" style="color: #94A3B8;">
                            Leader en formations certifiantes et consulting en Afrique. Nous certifions l'excellence professionnelle depuis Abidjan.
                        </p>
                        <div class="mb-5">
                            <p class="text-xs font-bold uppercase tracking-widest mb-3" style="color: #E8A020;">Notre présence</p>
                            <div class="flex flex-wrap gap-2">
                                <span v-for="c in ['🇨🇮 Côte d\'Ivoire', '🇸🇳 Sénégal', '🇲🇱 Mali', '🇨🇲 Cameroun', '🇧🇫 Burkina']" :key="c"
                                    class="text-xs px-2 py-1 rounded-lg" style="background: rgba(255,255,255,0.06); color: #CBD5E1;">{{ c }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <a v-if="contactSettings?.social_linkedin" :href="contactSettings.social_linkedin" target="_blank"
                                class="w-9 h-9 rounded-xl flex items-center justify-center transition-all duration-200 hover:scale-110"
                                style="background: rgba(255,255,255,0.08);">
                                <i class="bi bi-linkedin text-sm" style="color: #94A3B8;"></i>
                            </a>
                            <a v-if="contactSettings?.social_facebook" :href="contactSettings.social_facebook" target="_blank"
                                class="w-9 h-9 rounded-xl flex items-center justify-center transition-all duration-200 hover:scale-110"
                                style="background: rgba(255,255,255,0.08);">
                                <i class="bi bi-facebook text-sm" style="color: #94A3B8;"></i>
                            </a>
                            <a v-if="contactSettings?.social_twitter" :href="contactSettings.social_twitter" target="_blank"
                                class="w-9 h-9 rounded-xl flex items-center justify-center transition-all duration-200 hover:scale-110"
                                style="background: rgba(255,255,255,0.08);">
                                <i class="bi bi-twitter-x text-sm" style="color: #94A3B8;"></i>
                            </a>
                            <a v-if="contactSettings?.social_youtube" :href="contactSettings.social_youtube" target="_blank"
                                class="w-9 h-9 rounded-xl flex items-center justify-center transition-all duration-200 hover:scale-110"
                                style="background: rgba(255,255,255,0.08);">
                                <i class="bi bi-youtube text-sm" style="color: #94A3B8;"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Formations Column -->
                    <div>
                        <h4 class="text-sm font-bold text-white mb-5 uppercase tracking-widest">Formations</h4>
                        <ul class="space-y-3">
                            <li v-for="cat in formationCategories.slice(0, 4)" :key="cat.href">
                                <Link :href="cat.href" class="text-sm transition-colors flex items-center gap-2 group" style="color: #94A3B8;">
                                    <i :class="['bi', cat.icon, 'text-xs opacity-60 group-hover:opacity-100']" :style="cat.hexColor ? `color: ${cat.hexColor}` : ''"></i>
                                    <span class="group-hover:text-white transition-colors">{{ cat.label }}</span>
                                </Link>
                            </li>
                            <li>
                                <Link :href="route('formations')" class="text-sm transition-colors flex items-center gap-2 group" style="color: #E8A020;">
                                    <i class="bi bi-arrow-right text-xs"></i>
                                    <span class="group-hover:underline">Toutes les formations</span>
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <!-- Quick Links Column -->
                    <div>
                        <h4 class="text-sm font-bold text-white mb-5 uppercase tracking-widest">Navigation</h4>
                        <ul class="space-y-3">
                            <li v-for="link in [
                                { label: 'Accueil', href: '/' },
                                { label: 'À Propos', href: '/about' },
                                { label: 'Consulting', href: '/services' },
                                { label: 'Executive Masterclass', href: '/executive-masterclass' },
                                { label: 'Actualités', href: '/blog' },
                                { label: 'Contact', href: '/contact' },
                            ]" :key="link.href">
                                <Link :href="link.href" class="text-sm flex items-center gap-2 group" style="color: #94A3B8;">
                                    <i class="bi bi-chevron-right text-xs opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                    <span class="group-hover:text-white transition-colors -ml-3 group-hover:ml-0 transition-all duration-200">{{ link.label }}</span>
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <!-- Contact Column -->
                    <div>
                        <h4 class="text-sm font-bold text-white mb-5 uppercase tracking-widest">Contact</h4>
                        <ul class="space-y-4">
                            <li class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5" style="background: rgba(232, 160, 32, 0.15);">
                                    <i class="bi bi-geo-alt text-sm" style="color: #E8A020;"></i>
                                </div>
                                <span class="text-sm" style="color: #94A3B8;">{{ contactSettings?.contact_address || 'Abidjan, Côte d\'Ivoire' }}</span>
                            </li>
                            <li v-if="contactSettings?.contact_phone" class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background: rgba(232, 160, 32, 0.15);">
                                    <i class="bi bi-telephone text-sm" style="color: #E8A020;"></i>
                                </div>
                                <span class="text-sm" style="color: #94A3B8;">{{ contactSettings.contact_phone }}</span>
                            </li>
                            <li v-if="contactSettings?.contact_email" class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background: rgba(232, 160, 32, 0.15);">
                                    <i class="bi bi-envelope text-sm" style="color: #E8A020;"></i>
                                </div>
                                <span class="text-sm" style="color: #94A3B8;">{{ contactSettings.contact_email }}</span>
                            </li>
                        </ul>
                        <div class="mt-6">
                            <p class="text-xs font-semibold text-white mb-2">Restez informé</p>
                            <Link :href="route('contact')"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white transition-all duration-200 hover:shadow-lg hover:shadow-yellow-900/20"
                                style="background: linear-gradient(135deg, #E8A020, #C68400);">
                                <i class="bi bi-bell"></i>
                                Nous contacter
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div style="border-top: 1px solid rgba(255,255,255,0.07);">
                <div class="max-w-7xl mx-auto px-6 lg:px-8 py-5 flex flex-col md:flex-row items-center justify-between gap-3">
                    <p class="text-xs" style="color: #475569;">
                        &copy; {{ new Date().getFullYear() }} GlobalTECH EDUCATION Africa. Tous droits réservés.
                    </p>
                    <div class="flex items-center gap-4">
                        <span class="text-xs" style="color: #475569;">Abidjan · Dakar · Bamako · Douala</span>
                        <div class="flex items-center gap-1 text-xs" style="color: #475569;">
                            <i class="bi bi-shield-check" style="color: #E8A020;"></i>
                            Certifications Officielles
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
.loader-enter-active, .loader-leave-active { transition: opacity 0.3s ease; }
.loader-enter-from, .loader-leave-to { opacity: 0; }

.dropdown-enter-active { transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1); }
.dropdown-leave-active { transition: all 0.15s ease; }
.dropdown-enter-from { opacity: 0; transform: translateY(-8px) scale(0.97); }
.dropdown-leave-to { opacity: 0; transform: translateY(-4px) scale(0.98); }

.mobile-menu-enter-active { transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
.mobile-menu-leave-active { transition: all 0.2s ease; }
.mobile-menu-enter-from, .mobile-menu-leave-to { opacity: 0; transform: translateY(-12px); }
</style>
