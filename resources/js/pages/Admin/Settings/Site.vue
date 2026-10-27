<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

type Country = { flag: string; name: string };
type ThemeMode = 'user' | 'dark' | 'light';

type SiteSettingsForm = {
    contact_enabled: boolean;
    blog_enabled: boolean;
    theme_mode: ThemeMode;
    presence_countries: Country[];
};

const props = defineProps<{
    site: {
        contact_enabled: boolean;
        blog_enabled: boolean;
        theme_mode: ThemeMode;
        presence_countries: Country[];
    };
}>();

const form = useForm<SiteSettingsForm>({
    contact_enabled: props.site.contact_enabled,
    blog_enabled: props.site.blog_enabled,
    theme_mode: props.site.theme_mode,
    presence_countries: props.site.presence_countries.map((c) => ({ flag: c.flag ?? '', name: c.name })),
});

const themeOptions: { value: ThemeMode; label: string; desc: string }[] = [
    { value: 'dark', label: 'Sombre uniquement', desc: 'Le site est toujours affiché en mode sombre.' },
    { value: 'light', label: 'Clair uniquement', desc: 'Le site est toujours affiché en mode clair.' },
    { value: 'user', label: 'Au choix du visiteur', desc: 'Le visiteur peut basculer entre clair, sombre et automatique.' },
];

const addCountry = () => form.presence_countries.push({ flag: '', name: '' });
const removeCountry = (i: number) => form.presence_countries.splice(i, 1);
const moveCountry = (i: number, dir: -1 | 1) => {
    const j = i + dir;
    if (j < 0 || j >= form.presence_countries.length) return;
    const list = form.presence_countries;
    [list[i], list[j]] = [list[j], list[i]];
};

const submit = () => {
    form.post(route('admin.site-settings.update'), { preserveScroll: true });
};
</script>

<template>
    <AppLayout>
        <Head title="Paramètres du site" />

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h1 class="text-2xl font-semibold mb-6">Paramètres du site</h1>

                        <form @submit.prevent="submit">
                            <!-- Pages -->
                            <div class="mb-8">
                                <h2 class="text-lg font-medium mb-4 text-gray-700 border-b pb-2">Pages du site</h2>
                                <p class="text-sm text-gray-500 mb-4">
                                    Une page désactivée disparaît du menu, du pied de page et des boutons du site. Son adresse redirige vers l'accueil.
                                </p>

                                <label class="flex items-center justify-between py-3 cursor-pointer">
                                    <span>
                                        <span class="block text-sm font-medium text-gray-700">Page Contact</span>
                                        <span class="block text-xs text-gray-500">/contact — formulaire et boutons « Nous contacter »</span>
                                    </span>
                                    <input v-model="form.contact_enabled" type="checkbox" class="h-5 w-5 rounded border-gray-300 text-primary focus:ring-primary" />
                                </label>

                                <label class="flex items-center justify-between py-3 cursor-pointer border-t">
                                    <span>
                                        <span class="block text-sm font-medium text-gray-700">Page Actualités</span>
                                        <span class="block text-xs text-gray-500">/blog — articles et section actualités de l'accueil</span>
                                    </span>
                                    <input v-model="form.blog_enabled" type="checkbox" class="h-5 w-5 rounded border-gray-300 text-primary focus:ring-primary" />
                                </label>
                            </div>

                            <!-- Theme -->
                            <div class="mb-8">
                                <h2 class="text-lg font-medium mb-4 text-gray-700 border-b pb-2">Apparence du site</h2>
                                <div class="space-y-2">
                                    <label v-for="opt in themeOptions" :key="opt.value"
                                        class="flex items-start gap-3 p-3 rounded-md border cursor-pointer"
                                        :class="form.theme_mode === opt.value ? 'border-primary bg-gray-50' : 'border-gray-200'">
                                        <input v-model="form.theme_mode" type="radio" :value="opt.value" class="mt-1 text-primary focus:ring-primary" />
                                        <span>
                                            <span class="block text-sm font-medium text-gray-700">{{ opt.label }}</span>
                                            <span class="block text-xs text-gray-500">{{ opt.desc }}</span>
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <!-- Presence -->
                            <div class="mb-8">
                                <h2 class="text-lg font-medium mb-4 text-gray-700 border-b pb-2">Notre présence (pied de page)</h2>
                                <p class="text-sm text-gray-500 mb-4">Pays affichés sous « Notre présence ». Le drapeau est un emoji (ex. 🇨🇮), facultatif.</p>

                                <div v-for="(country, i) in form.presence_countries" :key="i" class="flex items-center gap-2 mb-2">
                                    <input v-model="country.flag" type="text" placeholder="🇨🇮"
                                        class="w-16 text-center border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" />
                                    <input v-model="country.name" type="text" placeholder="Nom du pays" required
                                        class="flex-1 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" />
                                    <button type="button" @click="moveCountry(i, -1)" :disabled="i === 0"
                                        class="px-2 py-2 text-gray-500 hover:text-gray-800 disabled:opacity-30" title="Monter">↑</button>
                                    <button type="button" @click="moveCountry(i, 1)" :disabled="i === form.presence_countries.length - 1"
                                        class="px-2 py-2 text-gray-500 hover:text-gray-800 disabled:opacity-30" title="Descendre">↓</button>
                                    <button type="button" @click="removeCountry(i)"
                                        class="px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-md">Supprimer</button>
                                </div>
                                <p v-if="!form.presence_countries.length" class="text-sm text-gray-400 italic mb-2">
                                    Aucun pays : la section « Notre présence » sera masquée.
                                </p>
                                <p v-if="form.errors.presence_countries" class="text-sm text-red-600">{{ form.errors.presence_countries }}</p>

                                <button type="button" @click="addCountry"
                                    class="mt-2 px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                                    + Ajouter un pays
                                </button>
                            </div>

                            <div class="flex items-center justify-end gap-3">
                                <span v-if="form.recentlySuccessful" class="text-sm text-green-600">Enregistré.</span>
                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
                                    :disabled="form.processing"
                                >
                                    Enregistrer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
