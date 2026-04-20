<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

// Types for settings and form
type Setting = { key: string; value: string | null | undefined };

type ContactSettingsForm = {
    contact_phone: string;
    contact_phone_fixed: string;
    contact_email: string;
    contact_address: string;
    social_facebook: string;
    social_twitter: string;
    social_instagram: string;
    social_youtube: string;
    social_linkedin: string;
    social_tiktok: string;
    opening_hours: string;
};

// Typed props
const props = defineProps<{ settings?: Setting[] }>();

// Convert settings array to an object for easier form handling
const settingsObject: Record<string, string | null | undefined> = {};
props.settings?.forEach((setting) => {
    if (setting && typeof setting.key === 'string') {
        settingsObject[setting.key] = setting.value ?? '';
    }
});

// Create form with default values
const form = useForm<ContactSettingsForm>({
    // Contact information
    contact_phone: String(settingsObject['contact_phone'] ?? ''),
    contact_phone_fixed: String(settingsObject['contact_phone_fixed'] ?? ''),
    contact_email: String(settingsObject['contact_email'] ?? ''),
    contact_address: String(settingsObject['contact_address'] ?? ''),

    // Social media
    social_facebook: String(settingsObject['social_facebook'] ?? ''),
    social_twitter: String(settingsObject['social_twitter'] ?? ''),
    social_instagram: String(settingsObject['social_instagram'] ?? ''),
    social_youtube: String(settingsObject['social_youtube'] ?? ''),
    social_linkedin: String(settingsObject['social_linkedin'] ?? ''),
    social_tiktok: String(settingsObject['social_tiktok'] ?? ''),

    // Opening hours
    opening_hours: String(settingsObject['opening_hours'] ?? ''),
});

const submit = () => {
    form.post(route('admin.contact-settings.update'));
};
</script>

<template>
    <AppLayout>
        <Head title="Contact Settings" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h1 class="text-2xl font-semibold mb-6">Contact Settings</h1>

                        <form @submit.prevent="submit">
                            <div class="mb-8">
                                <h2 class="text-lg font-medium mb-4 text-gray-700 border-b pb-2">Contact Information</h2>

                                <div class="mb-4">
                                    <label for="contact_phone" class="block text-sm font-medium text-gray-700">Mobile Phone</label>
                                    <input
                                        id="contact_phone"
                                        v-model="form.contact_phone"
                                        type="text"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                        placeholder="+(221) 77 100 00 00"
                                    >
                                </div>

                                <div class="mb-4">
                                    <label for="contact_phone_fixed" class="block text-sm font-medium text-gray-700">Fixed Phone</label>
                                    <input
                                        id="contact_phone_fixed"
                                        v-model="form.contact_phone_fixed"
                                        type="text"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                        placeholder="+(221) 77 100 00 00 "
                                    >
                                </div>

                                <div class="mb-4">
                                    <label for="contact_email" class="block text-sm font-medium text-gray-700">Email</label>
                                    <input
                                        id="contact_email"
                                        v-model="form.contact_email"
                                        type="email"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                        placeholder="contact@globaltech-edu.com"
                                    >
                                </div>

                                <div class="mb-4">
                                    <label for="contact_address" class="block text-sm font-medium text-gray-700">Address</label>
                                    <textarea
                                        id="contact_address"
                                        v-model="form.contact_address"
                                        rows="3"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                        placeholder="71 rue de fontenay, Dakar, Sénégal"
                                    ></textarea>
                                </div>
                            </div>

                            <div class="mb-8">
                                <h2 class="text-lg font-medium mb-4 text-gray-700 border-b pb-2">Réseaux sociaux</h2>

                                <div class="mb-4">
                                    <label for="social_facebook" class="block text-sm font-medium text-gray-700">Facebook URL</label>
                                    <input
                                        id="social_facebook"
                                        v-model="form.social_facebook"
                                        type="url"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                        placeholder="https://www.facebook.com/globaltecheducationafrica/"
                                    >
                                </div>

                                <div class="mb-4">
                                    <label for="social_twitter" class="block text-sm font-medium text-gray-700">Twitter URL</label>
                                    <input
                                        id="social_twitter"
                                        v-model="form.social_twitter"
                                        type="url"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                        placeholder="https://twitter.com/GlobalTECHEdu"
                                    >
                                </div>

                                <div class="mb-4">
                                    <label for="social_instagram" class="block text-sm font-medium text-gray-700">Instagram URL</label>
                                    <input
                                        id="social_instagram"
                                        v-model="form.social_instagram"
                                        type="url"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                        placeholder="https://www.instagram.com/globaltecheducation/"
                                    >
                                </div>

                                <div class="mb-4">
                                    <label for="social_linkedin" class="block text-sm font-medium text-gray-700">LinkedIn URL</label>
                                    <input
                                        id="social_linkedin"
                                        v-model="form.social_linkedin"
                                        type="url"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                        placeholder="https://www.linkedin.com/company/globaltech-education-africa/"
                                    >
                                </div>

                                <div class="mb-4">
                                    <label for="social_youtube" class="block text-sm font-medium text-gray-700">YouTube URL</label>
                                    <input
                                        id="social_youtube"
                                        v-model="form.social_youtube"
                                        type="url"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                        placeholder="https://www.youtube.com/@globaltecheducationafrica"
                                    >
                                </div>

                                <div class="mb-4">
                                    <label for="social_tiktok" class="block text-sm font-medium text-gray-700">TikTok URL</label>
                                    <input
                                        id="social_tiktok"
                                        v-model="form.social_tiktok"
                                        type="url"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                        placeholder="#"
                                    >
                                </div>
                            </div>

                            <div class="mb-8">
                                <h2 class="text-lg font-medium mb-4 text-gray-700 border-b pb-2">Opening Hours</h2>

                                <div class="mb-4">
                                    <label for="opening_hours" class="block text-sm font-medium text-gray-700">Opening Hours</label>
                                    <textarea
                                        id="opening_hours"
                                        v-model="form.opening_hours"
                                        rows="3"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                        placeholder="Monday - Friday: 9am - 6pm
Saturday: 10am - 4pm
Sunday: Closed"
                                    ></textarea>
                                </div>
                            </div>

                            <div class="flex items-center justify-end">
                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
                                    :disabled="form.processing"
                                >
                                    Save Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
