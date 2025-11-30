<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { ref, reactive, computed } from 'vue';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import Temoignages from '@/components/front/Temoignages.vue';
import Partners from '@/components/front/Partners.vue';
import BlogPosts from '@/components/front/BlogPosts.vue';
import MockServiceSection from '@/components/front/MockServiceSection.vue';
import CompanyValues from '@/components/front/CompanyValues.vue';
import CompanyPresentation from '@/components/front/CompanyPresentation.vue';
import axios from 'axios';

// No props needed for mock data version

// Define ContactSettings interface
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

// Contact form data
const contactForm = reactive({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    subject: '',
    description: ''
});

// Form validation state
const errors = reactive({
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

// Handle form submission
const submitForm = async () => {
    // Reset errors and set submitting state
    isSubmitting.value = true;
    Object.keys(errors).forEach(key => {
        errors[key] = '';
    });

    try {
        // Send form data to server
        await axios.post('/contact', contactForm);

        // Handle success
        isSuccess.value = true;

        // Reset form ONLY after successful submission
        Object.keys(contactForm).forEach(key => {
            contactForm[key] = '';
        });

        // Reset success message after 5 seconds
        setTimeout(() => {
            isSuccess.value = false;
        }, 5000);
    } catch (error) {
        // Handle validation errors
        if (error.response && error.response.status === 422) {
            const validationErrors = error.response.data.errors;

            // Set error messages
            Object.keys(validationErrors).forEach(key => {
                errors[key] = validationErrors[key][0];
            });
        }
    } finally {
        isSubmitting.value = false;
    }
};



</script>

<template>
    <Head title="TONGOLO TECH - Cabinet Conseil"  />


    <LayoutFront>
        <!-- Company Presentation Section -->
        <CompanyPresentation />

        <!-- Services Preview -->
        <div id="services">
            <MockServiceSection />
        </div>

        <!-- Company Values Section -->
        <CompanyValues />

        <!-- Portfolio Section -->

        <Temoignages
            :bg-color="'bg-white'"
            :class-names-title="'text-3xl font-serif font-bold text-gray-900'"
        />

        <!-- Partners Section -->

        <Partners :bg-color="'bg-gray-50'"
                  :class-names="'text-3xl font-serif font-bold text-gray-900'" />

        <!-- Blog Preview -->

        <BlogPosts />
        <!-- Contact Section -->
        <section class="py-20 bg-primary-bg-light">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    <div>
                        <h2 class="text-3xl font-serif font-bold text-gray-900">Contactez-nous</h2>
                        <p class="mt-4 text-lg text-gray-600">
Vous avez des questions ou souhaitez discuter de vos projets en ingénierie, télécommunications ou réseaux ? Contactez notre équipe TONGOLO TECH dès maintenant                        </p>

                        <div class="mt-8 space-y-6">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div class="ml-3 text-gray-600">
                                    <p>{{ contactSettings.contact_address }}</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </div>
                                <div class="ml-3 text-gray-600">
                                    <p>{{ contactSettings.contact_phone }}</p>
                                    <p>{{ contactSettings.contact_phone_fixed }}</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="ml-3 text-gray-600">
                                    <p>{{ contactSettings.contact_email }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8">
                            <h3 class="text-lg font-semibold text-gray-900">Heures d'ouverture</h3>
                            <div class="mt-3 space-y-2 text-gray-600">
                                <p>{{ contactSettings.opening_hours }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100">
                        <h3 class="text-xl font-semibold text-gray-900 mb-6">Envoyez-nous un message</h3>

                        <!-- Success message -->
                        <div v-if="isSuccess" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">
                            <p class="font-medium">Merci pour votre message !</p>
                            <p>Nous vous contacterons dans les plus brefs délais.</p>
                        </div>

                        <form @submit.prevent="submitForm">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">Prénom *</label>
                                    <input
                                        type="text"
                                        id="first_name"
                                        v-model="contactForm.first_name"
                                        class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                        :class="{'border-red-500': errors.first_name}"
                                        required
                                    />
                                    <p v-if="errors.first_name" class="mt-1 text-sm text-red-600">{{ errors.first_name }}</p>
                                </div>
                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                                    <input
                                        type="text"
                                        id="last_name"
                                        v-model="contactForm.last_name"
                                        class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                        :class="{'border-red-500': errors.last_name}"
                                        required
                                    />
                                    <p v-if="errors.last_name" class="mt-1 text-sm text-red-600">{{ errors.last_name }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                    <input
                                        type="email"
                                        id="email"
                                        v-model="contactForm.email"
                                        class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                        :class="{'border-red-500': errors.email}"
                                        required
                                    />
                                    <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
                                </div>
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                                    <input
                                        type="tel"
                                        id="phone"
                                        v-model="contactForm.phone"
                                        class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                        :class="{'border-red-500': errors.phone}"
                                    />
                                    <p v-if="errors.phone" class="mt-1 text-sm text-red-600">{{ errors.phone }}</p>
                                </div>
                            </div>

                            <div class="mb-6">
                                <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Sujet *</label>
                                <input
                                    type="text"
                                    id="subject"
                                    v-model="contactForm.subject"
                                    class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                    :class="{'border-red-500': errors.subject}"
                                    required
                                />
                                <p v-if="errors.subject" class="mt-1 text-sm text-red-600">{{ errors.subject }}</p>
                            </div>

                            <div class="mb-6">
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Message *</label>
                                <textarea
                                    id="description"
                                    v-model="contactForm.description"
                                    rows="4"
                                    class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                    :class="{'border-red-500': errors.description}"
                                    required
                                ></textarea>
                                <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description }}</p>
                            </div>

                            <button
                                type="submit"
                                class="w-full px-6 py-3 bg-primary text-white font-medium rounded-md hover:bg-primary-dark transition-colors"
                                :disabled="isSubmitting"
                            >
                                <span v-if="isSubmitting">Envoi en cours...</span>
                                <span v-else>Envoyer le message</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
        <!-- CTA Section -->


    </LayoutFront>

</template>
