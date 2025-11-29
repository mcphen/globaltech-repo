<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';

// Types
interface Lead {
    id: number;
    first_name: string;
    last_name: string;
    phone: string | null;
    company: string | null;
    consent_at: string | null;
    user: {
        id: number;
        name: string;
        email: string;
    } | null;
}

// Props
interface Props {
    lead: Lead;
    flash?: { success?: string; error?: string };
}

const props = defineProps<Props>();

// Définition des fil d'Ariane
const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Prospects', href: route('admin.leads.index') },
    { title: 'Modifier ' + props.lead.first_name + ' ' + props.lead.last_name, href: route('admin.leads.edit', props.lead.id) }
];

// Form
const form = useForm({
    first_name: props.lead.first_name,
    last_name: props.lead.last_name,
    phone: props.lead.phone || '',
    company: props.lead.company || '',
});

function submit() {
    form.put(route('admin.leads.update', props.lead.id));
}
</script>

<template>
    <Head :title="`Modifier prospect - ${lead.first_name} ${lead.last_name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm">
            <!-- En-tête avec titre -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 border-b pb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Modifier le prospect</h2>
                    <p class="text-gray-500 mt-1">
                        Modifiez les informations du prospect {{ lead.first_name }} {{ lead.last_name }}
                    </p>
                </div>

                <div class="flex items-center gap-4">
                    <Link
                        :href="route('admin.leads.show', lead.id)"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition flex items-center gap-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                        Retour
                    </Link>
                </div>
            </div>

            <!-- Messages -->
            <div v-if="props.flash?.success" class="bg-green-50 border-l-4 border-green-500 p-4 rounded-md flex items-start">
                <svg class="h-6 w-6 text-green-500 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-green-700">{{ props.flash.success }}</p>
                </div>
            </div>

            <div v-if="props.flash?.error" class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md flex items-start">
                <svg class="h-6 w-6 text-red-500 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-red-700">{{ props.flash.error }}</p>
                </div>
            </div>

            <!-- Formulaire -->
            <form @submit.prevent="submit" class="max-w-2xl space-y-6">
                <!-- Informations personnelles -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Informations personnelles</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Prénom -->
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">
                                Prénom *
                            </label>
                            <input
                                id="first_name"
                                v-model="form.first_name"
                                type="text"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                :class="{ 'border-red-300': form.errors.first_name }"
                            />
                            <p v-if="form.errors.first_name" class="mt-1 text-sm text-red-600">
                                {{ form.errors.first_name }}
                            </p>
                        </div>

                        <!-- Nom -->
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">
                                Nom *
                            </label>
                            <input
                                id="last_name"
                                v-model="form.last_name"
                                type="text"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                :class="{ 'border-red-300': form.errors.last_name }"
                            />
                            <p v-if="form.errors.last_name" class="mt-1 text-sm text-red-600">
                                {{ form.errors.last_name }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <!-- Téléphone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                                Téléphone
                            </label>
                            <input
                                id="phone"
                                v-model="form.phone"
                                type="tel"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                :class="{ 'border-red-300': form.errors.phone }"
                            />
                            <p v-if="form.errors.phone" class="mt-1 text-sm text-red-600">
                                {{ form.errors.phone }}
                            </p>
                        </div>

                        <!-- Entreprise -->
                        <div>
                            <label for="company" class="block text-sm font-medium text-gray-700 mb-1">
                                Entreprise
                            </label>
                            <input
                                id="company"
                                v-model="form.company"
                                type="text"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                :class="{ 'border-red-300': form.errors.company }"
                            />
                            <p v-if="form.errors.company" class="mt-1 text-sm text-red-600">
                                {{ form.errors.company }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Informations du compte (lecture seule) -->
                <div class="bg-blue-50 rounded-lg p-6" v-if="lead.user">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Compte utilisateur associé</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Nom d'utilisateur:</span>
                            <span class="font-medium">{{ lead.user.name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Email:</span>
                            <span class="font-medium">{{ lead.user.email }}</span>
                        </div>
                    </div>
                    <p class="text-xs text-blue-600 mt-3">
                        ⓘ Les informations du compte utilisateur ne peuvent pas être modifiées depuis cette page.
                    </p>
                </div>

                <!-- Boutons d'action -->
                <div class="flex gap-4 pt-6 border-t">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                    >
                        <svg v-if="form.processing" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        {{ form.processing ? 'Mise à jour...' : 'Mettre à jour' }}
                    </button>

                    <Link
                        :href="route('admin.leads.show', lead.id)"
                        class="px-6 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition flex items-center gap-2"
                    >
                        Annuler
                    </Link>
                </div>
            </form>
        </div>
    </AppLayout>
</template>