<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import { ref, computed } from 'vue';

// Use the same contact settings structure as LayoutFront
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

const page = usePage();
const contactSettings = computed<ContactSettings>(() => page.props.contactSettings as ContactSettings);

// Form validation state
const errors = ref({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    subject: '',
    description: ''
});

// Form submission state
const isSubmitting = ref(false);
const isSuccess = ref(false);

// Create form using Inertia's useForm
const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    subject: '',
    description: ''
});

// Handle form submission
const submitForm = () => {
    isSubmitting.value = true;
    errors.value = {
        first_name: '',
        last_name: '',
        email: '',
        phone: '',
        subject: '',
        description: ''
    };

    form.post(route('contact.store'), {
        onSuccess: () => {
            isSuccess.value = true;
            form.reset();
            isSubmitting.value = false;

            // Reset success message after 5 seconds
            setTimeout(() => {
                isSuccess.value = false;
            }, 5000);
        },
        onError: (formErrors) => {
            errors.value = formErrors;
            isSubmitting.value = false;
        }
    });
};

// Breadcrumb data
const breadcrumbItems = [
    { name: 'Accueil', href: route('home'), current: false },
    { name: 'Contact', href: route('contact'), current: true }
];
</script>

<template>
    <Head>
        <title>Contact - TONGOLO TECH</title>
        <meta name="description" content="Contactez TONGOLO TECH pour planifier votre mariage de rêve ou pour toute question concernant nos services." />
    </Head>

    <LayoutFront>
        <!-- Bannière du breadcrumb avec image de fond -->
        <div class="relative bg-primary-bg-light py-16 overflow-hidden">
        <!-- Image de fond avec overlay -->
        <div class="absolute inset-0 z-0">
            <img
            src="https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=1920&q=80"
            alt="Technology Background"
            class="w-full h-full object-cover"
            />
            <!-- Overlay gradient pour améliorer la lisibilité -->
            <div class="absolute inset-0 bg-gradient-to-r from-blue-900/85 via-blue-800/75 to-purple-900/85"></div>
        </div>

        <!-- Contenu en avant-plan -->
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col items-center justify-center">
            <h1 class="text-4xl md:text-5xl font-serif font-bold text-white text-center mb-4 drop-shadow-lg">
            Contact
            </h1>

            <!-- Breadcrumb navigation -->
            <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2">
                <li v-for="(item, index) in breadcrumbItems" :key="item.name">
                <div class="flex items-center">
                    <Link
                    :href="item.href"
                    :class="[
                        item.current ? 'text-white font-medium' : 'text-white/80 hover:text-white',
                        'text-sm md:text-base transition-colors drop-shadow-md'
                    ]"
                    >
                    {{ item.name }}
                    </Link>

                    <!-- Séparateur, sauf pour le dernier élément -->
                    <svg
                    v-if="index !== breadcrumbItems.length - 1"
                    class="h-5 w-5 text-white/70 mx-2 drop-shadow-md"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
                </li>
            </ol>
            </nav>
        </div>

        <!-- Élément décoratif -->
        <div class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-yellow-400/50 to-transparent"></div>
        </div>

        <div class="py-12 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Formulaire de Contact -->
                <section class="mb-16">
                    <h2 class="text-3xl font-serif font-bold text-center text-primary mb-3">Contactez-nous</h2>
                    <div class="w-24 h-1 bg-primary mx-auto mb-8"></div>
                   <p class="text-center text-gray-600 max-w-3xl mx-auto mb-12">
                        Vous avez des questions ou souhaitez discuter de vos projets en ingénierie, télécommunications ou réseaux ? Contactez notre équipe TONGOLO TECH dès maintenant, soit en remplissant le formulaire ci-dessous, soit en nous appelant directement. Nous vous répondrons rapidement.
                    </p>


                    <div class="bg-white shadow-lg rounded-lg overflow-hidden max-w-4xl mx-auto">
                        <!-- Success message -->
                        <div v-if="isSuccess" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">
                            <p class="font-medium">Merci pour votre message !</p>
                            <p>Nous vous contacterons dans les plus brefs délais.</p>
                        </div>

                        <form @submit.prevent="submitForm" class="p-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Prénom -->
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">Prénom *</label>
                                    <input
                                        type="text"
                                        id="first_name"
                                        v-model="form.first_name"
                                        class="w-full px-4 py-2 border rounded-md focus:ring-primary focus:border-primary"
                                        :class="{'border-red-500': errors.first_name}"
                                        required
                                    >
                                    <p v-if="errors.first_name" class="mt-1 text-sm text-red-600">{{ errors.first_name }}</p>
                                </div>

                                <!-- Nom -->
                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                                    <input
                                        type="text"
                                        id="last_name"
                                        v-model="form.last_name"
                                        class="w-full px-4 py-2 border rounded-md focus:ring-primary focus:border-primary"
                                        :class="{'border-red-500': errors.last_name}"
                                        required
                                    >
                                    <p v-if="errors.last_name" class="mt-1 text-sm text-red-600">{{ errors.last_name }}</p>
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                    <input
                                        type="email"
                                        id="email"
                                        v-model="form.email"
                                        class="w-full px-4 py-2 border rounded-md focus:ring-primary focus:border-primary"
                                        :class="{'border-red-500': errors.email}"
                                        required
                                    >
                                    <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
                                </div>

                                <!-- Téléphone -->
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                                    <input
                                        type="tel"
                                        id="phone"
                                        v-model="form.phone"
                                        class="w-full px-4 py-2 border rounded-md focus:ring-primary focus:border-primary"
                                        :class="{'border-red-500': errors.phone}"
                                    >
                                    <p v-if="errors.phone" class="mt-1 text-sm text-red-600">{{ errors.phone }}</p>
                                </div>

                                <!-- Objet -->
                                <div class="md:col-span-2">
                                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Objet *</label>
                                    <input
                                        type="text"
                                        id="subject"
                                        v-model="form.subject"
                                        class="w-full px-4 py-2 border rounded-md focus:ring-primary focus:border-primary"
                                        :class="{'border-red-500': errors.subject}"
                                        required
                                    >
                                    <p v-if="errors.subject" class="mt-1 text-sm text-red-600">{{ errors.subject }}</p>
                                </div>

                                <!-- Message -->
                                <div class="md:col-span-2">
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Message *</label>
                                    <textarea
                                        id="description"
                                        v-model="form.description"
                                        rows="6"
                                        class="w-full px-4 py-2 border rounded-md focus:ring-primary focus:border-primary"
                                        :class="{'border-red-500': errors.description}"
                                        required
                                    ></textarea>
                                    <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description }}</p>
                                </div>
                            </div>

                            <div class="mt-8 flex justify-center">
                                <button
                                    type="submit"
                                    class="px-8 py-3 bg-primary text-white rounded-full hover:bg-primary-dark transition-colors focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                                    :disabled="isSubmitting"
                                >
                                    <span v-if="isSubmitting">Envoi en cours...</span>
                                    <span v-else>Envoyer le message</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </section>

                <!-- Section Informations de Contact -->
                <section class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
                    <div class="bg-white p-6 rounded-lg shadow-md flex flex-col items-center text-center">
                        <div class="w-16 h-16 bg-primary-bg-light rounded-full flex items-center justify-center mb-4">
                            <svg class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">Téléphone</h3>
                        <p class="text-gray-600 mb-1">Mobile: {{ contactSettings.contact_phone }}</p>
                        <p class="text-gray-600" v-if="contactSettings.contact_phone_fixed">Fixe: {{ contactSettings.contact_phone_fixed }}</p>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-md flex flex-col items-center text-center">
                        <div class="w-16 h-16 bg-primary-bg-light rounded-full flex items-center justify-center mb-4">
                            <svg class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">Email</h3>
                        <p class="text-gray-600">{{ contactSettings.contact_email }}</p>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-md flex flex-col items-center text-center">
                        <div class="w-16 h-16 bg-primary-bg-light rounded-full flex items-center justify-center mb-4">
                            <svg class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">Adresse</h3>
                        <p class="text-gray-600">{{contactSettings.contact_address}}</p>
                    </div>
                </section>

                <!-- Section Carte Google Maps -->
                <section class="mb-16">
                    <div class="w-full h-96 rounded-lg overflow-hidden shadow-lg">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2628.190826009983!2d2.2725177!3d48.7973365!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e6709ffe38026b%3A0x13f519149dc17db4!2s71%20Rue%20de%20Fontenay%2C%2092140%20Clamart%2C%20France!5e0!3m2!1sen!2ssn!4v1760344298268!5m2!1sen!2ssn" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </section>
            </div>
        </div>
    </LayoutFront>
</template>
