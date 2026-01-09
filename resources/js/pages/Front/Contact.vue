<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import NewsletterSubscribe from '../Front/NewsletterSubscribe.vue';
import { useToast } from 'vue-toast-notification';
import { computed } from 'vue';

const page = usePage();
const $toast = useToast();

// Create form using Inertia's useForm
const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    subject: '',
    description: ''
});

// Fonction pour gérer la soumission du formulaire
const submitForm = () => {
    form.post(route('contact.store'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            // Succès
            $toast.success('Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.', {
                position: 'top-right',
                duration: 9000,
                dismissible: true,
            });

            // Réinitialiser le formulaire
            form.reset();
            form.clearErrors();
        },
        onError: (errors) => {
            // Gérer les erreurs de champ spécifiques
            Object.entries(errors).forEach(([field, message]) => {
                if (field !== 'error') {
                    $toast.error(`${field}: ${message}`, {
                        position: 'top-right',
                        duration: 10000,
                        dismissible: true,
                    });
                }
            });

            // Gérer les erreurs générales
            if (errors.error) {
                $toast.error(errors.error, {
                    position: 'top-right',
                    duration: 10000,
                    dismissible: true,
                });
            }
        },
        onFinish: () => {
            // Nettoyer après traitement
            form.clearErrors();
        }
    });
};

// Breadcrumb data
const breadcrumbItems = [
    { name: 'Accueil', href: route('home'), current: false },
    { name: 'Contact', href: route('contact'), current: true }
];

// Classes pour les champs avec erreurs
const inputClass = (field: string) => {
    const baseClass = 'w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-primary focus:border-transparent disabled:bg-gray-100 transition-colors duration-200';

    // Type assertion pour accéder aux erreurs de manière sécurisée
    const errors = form.errors as Record<string, string>;
    if (errors[field]) {
        return `${baseClass} border-red-300 focus:ring-red-500`;
    }

    return `${baseClass} border-gray-300 focus:border-primary`;
};

const textareaClass = (field: string) => {
    const baseClass = 'w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-primary focus:border-transparent disabled:bg-gray-100 transition-colors duration-200';

    // Type assertion pour accéder aux erreurs de manière sécurisée
    const errors = form.errors as Record<string, string>;
    if (errors[field]) {
        return `${baseClass} border-red-300 focus:ring-red-500`;
    }

    return `${baseClass} border-gray-300 focus:border-primary`;
};

// Helper pour accéder aux erreurs
const getError = (field: string): string | undefined => {
    const errors = form.errors as Record<string, string>;
    return errors[field];
};

// Accès sécurisé aux props
const contactSettings = computed(() => {
    const props = page.props as any;
    return props.contactSettings || {
        contact_phone: '',
        contact_phone_fixed: '',
        contact_email: '',
        social_facebook: '',
        social_twitter: '',
        social_youtube: '',
        social_linkedin: '',
        social_tiktok: '',
        social_instagram: '',
        contact_address: '',
        opening_hours: ''
    };
});
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
                        <form @submit.prevent="submitForm" class="p-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Prénom -->
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">Prénom *</label>
                                    <input
                                        type="text"
                                        id="first_name"
                                        v-model="form.first_name"
                                        :class="inputClass('first_name')"
                                        :disabled="form.processing"
                                        required
                                    >
                                    <p v-if="getError('first_name')" class="mt-1 text-sm text-red-600 animate-fade-in">
                                        {{ getError('first_name') }}
                                    </p>
                                </div>

                                <!-- Nom -->
                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                                    <input
                                        type="text"
                                        id="last_name"
                                        v-model="form.last_name"
                                        :class="inputClass('last_name')"
                                        :disabled="form.processing"
                                        required
                                    >
                                    <p v-if="getError('last_name')" class="mt-1 text-sm text-red-600 animate-fade-in">
                                        {{ getError('last_name') }}
                                    </p>
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                    <input
                                        type="email"
                                        id="email"
                                        v-model="form.email"
                                        :class="inputClass('email')"
                                        :disabled="form.processing"
                                        required
                                    >
                                    <p v-if="getError('email')" class="mt-1 text-sm text-red-600 animate-fade-in">
                                        {{ getError('email') }}
                                    </p>
                                </div>

                                <!-- Téléphone -->
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Téléphone (optionnel)</label>
                                    <input
                                        type="tel"
                                        id="phone"
                                        v-model="form.phone"
                                        :class="inputClass('phone')"
                                        :disabled="form.processing"
                                        placeholder="Ex: +221 77 123 45 67"
                                    >
                                    <p v-if="getError('phone')" class="mt-1 text-sm text-red-600 animate-fade-in">
                                        {{ getError('phone') }}
                                    </p>
                                    <p v-else class="mt-1 text-xs text-gray-500">
                                        Format accepté: +221 77 123 45 67
                                    </p>
                                </div>

                                <!-- Objet -->
                                <div class="md:col-span-2">
                                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Objet *</label>
                                    <input
                                        type="text"
                                        id="subject"
                                        v-model="form.subject"
                                        :class="inputClass('subject')"
                                        :disabled="form.processing"
                                        required
                                    >
                                    <p v-if="getError('subject')" class="mt-1 text-sm text-red-600 animate-fade-in">
                                        {{ getError('subject') }}
                                    </p>
                                </div>

                                <!-- Message -->
                                <div class="md:col-span-2">
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Message *</label>
                                    <textarea
                                        id="description"
                                        v-model="form.description"
                                        rows="6"
                                        :class="textareaClass('description')"
                                        :disabled="form.processing"
                                        required
                                        placeholder="Décrivez votre projet ou votre question en détail..."
                                    ></textarea>
                                    <div class="flex justify-between items-center mt-1">
                                        <p v-if="getError('description')" class="text-sm text-red-600 animate-fade-in">
                                            {{ getError('description') }}
                                        </p>
                                        <p v-else class="text-xs text-gray-500">
                                            {{ form.description.length }}/1000 caractères
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 flex justify-center">
                                <button
                                    type="submit"
                                    class="px-8 py-3 bg-primary text-white rounded-full hover:bg-primary-dark transition-colors focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                                    :disabled="form.processing"
                                >
                                    <span v-if="form.processing" class="flex items-center">
                                        <svg class="animate-spin h-5 w-5 text-white mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Envoi en cours...
                                    </span>
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
                        <p class="text-gray-600" v-if="contactSettings.contact_phone_fixed">
                            Fixe: {{ contactSettings.contact_phone_fixed }}
                        </p>
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
                        <p class="text-gray-600">{{ contactSettings.contact_address }}</p>
                    </div>
                </section>
            </div>
        </div>

        <NewsletterSubscribe
            :compact="true"
            title="Abonnez-vous à notre newsletter"
            description="Recevez les dernières nouvelles et mises à jour directement dans votre boîte de réception."
        />
    </LayoutFront>
</template>

<style scoped>
/* Animation pour le spinner */
.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

/* Animation pour les messages d'erreur */
.animate-fade-in {
  animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-5px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Transition pour le bouton */
.transition-colors {
  transition-property: background-color, border-color, color, fill, stroke;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}
</style>
