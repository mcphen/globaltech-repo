<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import 'bootstrap-icons/font/bootstrap-icons.css';
import AppLogoIcon from '@/components/AppLogoIcon.vue';

// Define Service interface
interface Service {
    id: number;
    title: string;
    description?: string;
    image_path?: string;
    image_url?: string;
    min_price?: number;
}

const mobileMenuOpen = ref(false);
const dropdownStates = ref({
    about: false,
    services: false
});
const isLoading = ref(true);

// Mock data for services
const services = ref<Service[]>([
    { id: 1, title: "CONSEIL" },
    { id: 2, title: "ASSISTANCE COMPTABLE" },
    { id: 3, title: "AUDIT COMPTABLE ET FINANCIER" },
    { id: 4, title: "GOUVERNANCE & RISQUES" }
]);

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

// Use shared contact settings from backend via Inertia
const page = usePage();
const contactSettings = computed<ContactSettings>(() => page.props.contactSettings as ContactSettings);

// Shared cart from Inertia props
const cart = computed<any>(() => (page.props as any).cart ?? { items: [], count: 0, subtotal: 0, total: 0 });

// Authenticated user (from shared Inertia props)
const auth = computed<any>(() => (page.props as any).auth);

// Profile dropdown state
const profileMenuOpen = ref(false);

// Cart dropdown state
const cartMenuOpen = ref(false);
const toggleCartMenu = () => {
    cartMenuOpen.value = !cartMenuOpen.value;
};
const closeCartMenu = () => {
    cartMenuOpen.value = false;
};

// Cart actions
const removeFromCart = (id: number) => {
    router.post(route('cart.remove', { id }), {}, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => router.reload({ only: ['cart'] })
    });
};
const clearCart = () => {
    router.post(route('cart.clear'), {}, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => router.reload({ only: ['cart'] })
    });
};
const checkoutCart = () => {
    router.post(route('cart.checkout'));
};

const toggleDropdown = (menu: keyof typeof dropdownStates.value) => {
    dropdownStates.value[menu] = !dropdownStates.value[menu];
};

const closeMobileMenu = () => {
    mobileMenuOpen.value = false;
};

// Configuration du système de chargement
onMounted(() => {
    // Initialiser le loader comme visible
    isLoading.value = false;

    // Ajouter les écouteurs d'événements pour les transitions de page
    router.on('start', () => {
        isLoading.value = true;
    });

    router.on('finish', () => {
        // Utilisation d'un petit délai pour assurer que le DOM est mis à jour
        setTimeout(() => {
            isLoading.value = false;
        }, 200);
    });
});

</script>

<template>
    <Head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="description" content="NG Consulting - Cabinet conseil spécialisé dans l'accompagnement stratégique, comptable et opérationnel des entreprises. Solutions personnalisées pour renforcer votre performance." />
        <meta name="keywords" content="conseil stratégique, assistance comptable, audit financier, gouvernance, gestion des risques, consulting, entreprise, performance" />
        <meta name="author" content="NG Consulting" />
        <meta name="robots" content="index, follow" />


        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website" />
        <meta property="og:title" content="NG Consulting - Cabinet Conseil" />
        <meta property="og:description" content="NG Consulting - Cabinet conseil spécialisé dans l'accompagnement stratégique, comptable et opérationnel des entreprises. Solutions personnalisées pour renforcer votre performance." />
        <meta property="og:image" content="/images/logo.jpg" />
        <meta property="og:site_name" content="NG Consulting" />

        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="NG Consulting - Cabinet Conseil" />
        <meta name="twitter:description" content="NG Consulting - Cabinet conseil spécialisé dans l'accompagnement stratégique, comptable et opérationnel des entreprises. Solutions personnalisées pour renforcer votre performance." />
        <meta name="twitter:image" content="/images/logo.jpg" />
    </Head>

    <!-- Loader -->
    <div v-if="isLoading" class="fixed inset-0 z-[9999] flex items-center justify-center bg-white bg-opacity-80 transition-opacity duration-500">
        <div class="flex flex-col items-center">
            <div class="h-24 w-24 animate-spin rounded-full border-b-2 border-t-2 border-secondary"></div>
            <div class="mt-4 text-xl font-medium text-secondary">Chargement...</div>
        </div>
    </div>



    <div class="min-h-screen bg-white text-[#1b1b18]">
        <!-- Top Bar -->
        <div class="bg-primary text-white py-2 hidden md:block">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <div class="flex items-center space-x-6">
                    <!-- <div class="flex items-center">
                        <i class="bi bi-telephone mr-1 text-current"></i>
                        <span class="text-sm">{{ contactSettings.contact_phone }}</span>
                    </div> -->
                    <!-- <div class="flex items-center" v-if="contactSettings.contact_phone_fixed">
                        <i class="bi bi-phone-fill mr-1 text-current"></i>
                        <span class="text-sm">{{ contactSettings.contact_phone_fixed }}</span>
                    </div> -->
                    <div class="flex items-center">
                        <i class="bi bi-envelope mr-1 text-current"></i>
                        <span class="text-sm">{{ contactSettings.contact_email }}</span>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <a :href="contactSettings.social_linkedin" target="_blank" class="hover:text-white/80 transition-colors">
                        <i class="bi bi-linkedin text-current text-lg"></i>
                    </a>
                    <a :href="contactSettings.social_facebook" target="_blank" class="hover:text-white/80 transition-colors">
                        <i class="bi bi-facebook text-current text-lg"></i>
                    </a>
                    <a :href="contactSettings.social_twitter" target="_blank" class="hover:text-white/80 transition-colors">
                        <i class="bi bi-twitter text-current text-lg"></i>
                    </a>
                    <a :href="contactSettings.social_youtube" target="_blank" class="hover:text-white/80 transition-colors">
                        <i class="bi bi-youtube text-current text-lg"></i>
                    </a>
                    <a :href="contactSettings.social_instagram" target="_blank" class="hover:text-white/80 transition-colors">
                        <i class="bi bi-instagram text-current text-lg"></i>
                    </a>

                    <template v-if="!auth || !auth.user">
                        <span class="mx-3 h-4 w-px bg-white/40"></span>
                        <Link :href="route('login')" class="text-sm text-white hover:text-white/80 font-medium">Se connecter</Link>
                        <Link :href="route('prospect.register')" class="ml-2 px-3 py-1.5 bg-secondary text-white rounded-md hover:bg-secondary-dark text-sm font-medium transition-colors">S’inscrire</Link>
                    </template>
                </div>

            </div>
        </div>


        <!-- Navigation -->
        <header class="w-full bg-white shadow-md sticky top-0 z-50">
            <div class="max-w-10xl mx-auto px-4 sm:px-6 lg:px-10">
                <div class="flex justify-between h-20">
                    <div class="flex items-center">
                        <Link :href="route('home')" class="flex-shrink-0 flex items-center">
                            <AppLogoIcon />
                        </Link>
                    </div>

                    <!-- Desktop Menu -->
                    <nav class="hidden md:flex items-center space-x-2">
                        <Link
                            :href="route('home')"
                            class="px-3 py-2 text-gray-800 hover:text-secondary font-medium transition-colors"
                        >
                            Accueil
                        </Link>
                        <Link
                            href="/about"
                            class="px-3 py-2 text-gray-800 hover:text-secondary font-medium transition-colors"
                        >
                            À propos
                        </Link>

                        <Link
                            href="/services"
                            class="px-3 py-2 text-gray-800 hover:text-secondary font-medium transition-colors"
                        >
                            Nos Activités
                        </Link>

                        <Link
                            :href="route('products')"
                            class="px-3 py-2 text-gray-800 hover:text-secondary font-medium transition-colors"
                        >
                            Produits
                        </Link>

                        <Link
                            :href="route('blog')"
                            class="px-3 py-2 text-gray-800 hover:text-secondary font-medium transition-colors"
                        >
                            Actualités
                        </Link>
                        <Link
                            :href="route('formations')"
                            class="px-3 py-2 text-gray-800 hover:text-secondary font-medium transition-colors"
                        >
                            Formations
                        </Link>
                        <Link
                            :href="route('contact')"
                            class="px-3 py-2 text-gray-800 hover:text-secondary font-medium transition-colors"
                        >
                            Contact
                        </Link>
                        <Link v-if="!auth || !auth.user"
                            :href="route('appointment.create')"
                            class="ml-3 px-6 py-2 bg-secondary text-white rounded-md hover:bg-secondary-dark transition-colors"
                        >
                            Prendre rendez-vous
                        </Link>
                    </nav>

                    <!-- Right side: Cart + Profile (desktop) -->
                    <div class="hidden md:flex items-center ml-4 space-x-4 relative">
                        <!-- Cart Icon and dropdown -->
                        <div class="relative">
                            <button @click="toggleCartMenu" class="relative px-3 py-2 rounded-md hover:bg-primary-bg-light flex items-center">
                                <i class="bi bi-cart3 text-xl text-gray-800"></i>
                                <span v-if="cart.count > 0" class="ml-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-semibold leading-4 rounded-full bg-secondary text-white">
                                    {{ cart.count }}
                                </span>
                            </button>
                            <div v-show="cartMenuOpen" class="absolute right-0 top-12 w-80 bg-white border border-gray-200 rounded-md shadow-lg z-50">
                                <div class="p-3 border-b flex items-center justify-between">
                                    <span class="font-semibold">Panier</span>
                                    <button v-if="cart.items.length" @click="clearCart" class="text-xs text-gray-600 hover:underline">Vider</button>
                                </div>
                                <div v-if="!cart.items.length" class="p-4 text-sm text-gray-600">Votre panier est vide.</div>
                                <ul v-else class="max-h-80 overflow-auto divide-y">
                                    <li v-for="item in cart.items" :key="item.id" class="p-3 flex items-center gap-3">
                                        <img v-if="item.image_url" :src="item.image_url" :alt="item.title" class="w-12 h-12 object-cover rounded" />
                                        <div class="flex-1">
                                            <div class="text-sm font-medium text-gray-900 line-clamp-1">{{ item.title }}</div>
                                            <div class="text-xs text-gray-600">x{{ item.quantity }} — {{ new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(item.price) }}</div>
                                        </div>
                                        <button @click="removeFromCart(item.id)" class="text-red-600 hover:text-red-700" title="Retirer">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </li>
                                </ul>
                                <div class="p-3 border-t">
                                    <div class="flex justify-between text-sm mb-3">
                                        <span>Sous-total</span>
                                        <span>{{ new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(cart.subtotal) }}</span>
                                    </div>
                                    <div class="flex gap-2">
                                        <Link :href="route('cart.index')" class="flex-1 px-3 py-2 text-center border border-primary text-primary rounded-md hover:bg-primary-bg-light" @click="closeCartMenu">Voir panier</Link>
                                        <button @click="checkoutCart" class="flex-1 px-3 py-2 bg-primary text-white rounded-md hover:bg-primary-dark">Valider</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Profile menu (desktop) -->
                        <div class="relative" v-if="auth && auth.user">
                            <button @click="profileMenuOpen = !profileMenuOpen" class="flex items-center space-x-2 px-3 py-2 rounded-md hover:bg-primary-bg-light">
                                <i class="bi bi-person-circle text-xl text-gray-800"></i>
                                <span class="text-gray-800 font-medium">{{ auth.user.name }}</span>
                                <i class="bi bi-caret-down-fill text-xs text-gray-600"></i>
                            </button>
                            <div v-show="profileMenuOpen" class="absolute right-0 top-12 w-48 bg-white border border-gray-200 rounded-md shadow-lg z-50">
                                <Link :href="route('prospect.profile')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil</Link>
                                <Link method="post" :href="route('logout')" as="button" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Se déconnecter</Link>
                            </div>
                        </div>
                    </div>


                    <!-- Mobile menu button -->
                    <div class="flex md:hidden items-center">
                        <button
                            @click="mobileMenuOpen = !mobileMenuOpen"
                            class="inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-secondary hover:bg-primary-bg-light focus:outline-none focus:ring-2 focus:ring-inset focus:ring-secondary"
                        >
                            <span class="sr-only">Ouvrir le menu</span>
                            <svg
                                :class="{'hidden': mobileMenuOpen, 'block': !mobileMenuOpen}"
                                class="h-6 w-6"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"
                                />
                            </svg>
                            <svg
                                :class="{'block': mobileMenuOpen, 'hidden': !mobileMenuOpen}"
                                class="h-6 w-6"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <div :class="{'block': mobileMenuOpen, 'hidden': !mobileMenuOpen}" class="md:hidden">
                <div class="pt-2 pb-4 space-y-1">
                    <Link
                        :href="route('home')"
                        class="block px-4 py-2 text-base font-medium text-primary border-l-4 border-secondary bg-primary-bg-light"
                        @click="closeMobileMenu"
                    >
                        Accueil
                    </Link>

                    <!-- Mobile À Propos dropdown -->
                    <Link
                        href="/about"
                        class="block px-4 py-2 text-base font-medium text-primary border-l-4 border-secondary bg-primary-bg-light"
                        @click="closeMobileMenu"
                    >
                        À Propos
                    </Link>

                    <!-- Mobile Services dropdown -->
                    <div>
                        <button
                            @click="toggleDropdown('services')"
                            class="w-full flex justify-between items-center px-4 py-2 text-base font-medium text-gray-700 hover:text-secondary hover:bg-primary-bg-light"
                        >
                            <span>Nos Activités</span>
                            <svg
                                :class="{'transform rotate-180': dropdownStates.services}"
                                class="h-5 w-5 transition-transform duration-200"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </button>
                        <div v-show="dropdownStates.services" class="pl-4 pr-2 py-2 space-y-1 bg-gray-50">
                            <Link
                                href="/services"
                                class="block px-3 py-2 text-base font-medium text-gray-600 hover:text-secondary hover:bg-primary-bg-light rounded-md"
                                @click="closeMobileMenu"
                            >
                                Voir toutes nos activités
                            </Link>
                        </div>
                    </div>

                    <Link
                        :href="route('products')"
                        class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-secondary hover:bg-primary-bg-light"
                        @click="closeMobileMenu"
                    >
                        Produits
                    </Link>

                    <Link
                        :href="route('portfolio')"
                        class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-secondary hover:bg-primary-bg-light"
                        @click="closeMobileMenu"
                    >
                        Réalisations
                    </Link>
                    <Link
                        :href="route('blog')"
                        class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-secondary hover:bg-primary-bg-light"
                        @click="closeMobileMenu"
                    >
                        Actualités
                    </Link>
                    <Link
                        :href="route('formations')"
                        class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-secondary hover:bg-primary-bg-light"
                        @click="closeMobileMenu"
                    >
                        Formations
                    </Link>
                    <Link
                        :href="route('contact')"
                        class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-secondary hover:bg-primary-bg-light"
                        @click="closeMobileMenu"
                    >
                        Contact
                    </Link>
                    <Link
                        :href="route('appointment.create')"
                        class="block mx-4 mt-3 px-4 py-2 bg-secondary text-center text-white rounded-md hover:bg-secondary-dark transition-colors"
                        @click="closeMobileMenu"
                    >
                        Prendre rendez-vous
                    </Link>

                    <!-- Mobile: Profile and Logout when authenticated -->
                    <div v-if="auth && auth.user" class="mt-4 border-t border-gray-200 pt-2">
                        <div class="flex items-center px-4 py-2">
                            <i class="bi bi-person-circle text-xl text-gray-800 mr-2"></i>
                            <span class="text-gray-800 font-medium">{{ auth.user.name }}</span>
                        </div>
                        <Link
                            :href="route('front.profile.edit')"
                            class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-secondary hover:bg-primary-bg-light"
                            @click="closeMobileMenu"
                        >
                            Profil
                        </Link>
                        <Link
                            method="post"
                            :href="route('logout')"
                            as="button"
                            class="block w-full text-left px-4 py-2 text-base font-medium text-gray-700 hover:text-secondary hover:bg-primary-bg-light"
                            @click="closeMobileMenu"
                        >
                            Se déconnecter
                        </Link>
                    </div>

                    <!-- Mobile: Auth links for guests -->
                    <div v-else class="mt-4 border-t border-gray-200 pt-2">
                        <Link
                            :href="route('login')"
                            class="block px-4 py-2 text-base font-medium text-gray-700 hover:text-secondary hover:bg-primary-bg-light"
                            @click="closeMobileMenu"
                        >
                            Se connecter
                        </Link>
                        <Link
                            :href="route('prospect.register')"
                            class="block mx-4 mt-2 px-4 py-2 bg-secondary text-center text-white rounded-md hover:bg-secondary-dark transition-colors"
                            @click="closeMobileMenu"
                        >
                            S’inscrire
                        </Link>
                    </div>
                </div>
            </div>
        </header>

        <!-- Contenu principal (slot pour les pages) -->
        <main>
            <slot></slot>
        </main>

        <!-- Footer -->
        <footer class="bg-primary-dark text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div>
                        <h3 class="text-lg font-semibold mb-4">NG Consulting</h3>
                        <p class="text-gray-300 mb-4">
                            NG CONSULTING, fondée en 2024, est une société de conseil spécialisée dans l'accompagnement stratégique, comptable et opérationnel des entreprises.
                        </p>
                        <p class="text-gray-300 mb-4">
                            Notre mission est de fournir des solutions personnalisées et efficaces pour renforcer la performance, la gouvernance et la croissance durable de nos clients.
                        </p>
                        <div class="flex space-x-4">
                            <a :href="contactSettings.social_linkedin" target="_blank" class="text-gray-300 hover:text-white transition-colors">
                                <i class="bi bi-linkedin text-xl"></i>
                            </a>
                            <a :href="contactSettings.social_facebook" target="_blank" class="text-gray-300 hover:text-white transition-colors">
                                <i class="bi bi-facebook text-xl"></i>
                            </a>
                            <a :href="contactSettings.social_twitter" target="_blank" class="text-gray-300 hover:text-white transition-colors">
                                <i class="bi bi-twitter text-xl"></i>
                            </a>
                            <a :href="contactSettings.social_youtube" target="_blank" class="text-gray-300 hover:text-white transition-colors">
                                <i class="bi bi-youtube text-xl"></i>
                            </a>
                            <a :href="contactSettings.social_instagram" target="_blank" class="text-gray-300 hover:text-white transition-colors">
                                <i class="bi bi-instagram text-xl"></i>
                            </a>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold mb-4">Liens Rapides</h3>
                        <ul class="space-y-2">
                            <li>
                                <Link :href="route('home')" class="text-gray-300 hover:text-white transition-colors">
                                    Accueil
                                </Link>
                            </li>
                            <li>
                                <Link href="/about" class="text-gray-300 hover:text-white transition-colors">
                                    À Propos
                                </Link>
                            </li>
                            <li>
                                <Link href="/services" class="text-gray-300 hover:text-white transition-colors">
                                    Nos Activités
                                </Link>
                            </li>
                            <li>
                                <Link :href="route('portfolio')" class="text-gray-300 hover:text-white transition-colors">
                                    Réalisations
                                </Link>
                            </li>
                            <li>
                                <Link :href="route('blog')" class="text-gray-300 hover:text-white transition-colors">
                                    Actualités
                                </Link>
                            </li>
                            <li>
                                <Link :href="route('contact')" class="text-gray-300 hover:text-white transition-colors">
                                    Contact
                                </Link>
                            </li>
                            <li>
                                <Link :href="route('appointment.create')" class="text-gray-300 hover:text-white transition-colors">
                                    Prendre rendez-vous
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold mb-4">Nos Activités</h3>
                        <ul class="space-y-2">
                            <li v-for="service in services" :key="service.id">
                                <Link href="/services" class="text-gray-300 hover:text-white transition-colors">
                                    {{ service.title }}
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold mb-4">Contact</h3>
                        <ul class="space-y-2 text-gray-300">
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-white mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>{{contactSettings.contact_address}}</span>
                            </li>
                            <li class="flex items-center">
                                <svg class="h-5 w-5 text-white mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span>{{contactSettings.contact_phone}}</span>
                            </li>
                            <li class="flex items-center">
                                <svg class="h-5 w-5 text-white mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span>{{contactSettings.contact_email}}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mt-12 pt-8 border-t border-gray-700">
                    <p class="text-gray-400 text-center">
                        &copy; 2024 NG Consulting. Tous droits réservés.
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>
<style scoped>
/* Animation pour le loader */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style>
