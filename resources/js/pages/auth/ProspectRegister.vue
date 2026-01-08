<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import { Head, useForm, Link, usePage } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { useToast } from 'vue-toast-notification';
import { watch } from 'vue';
import { ref } from 'vue';

interface Flash {
    success?: string;
    error?: string;
    status?: string;
}

const page = usePage<{ flash?: Flash }>();
const $toast = useToast();

const showPassword = ref(false);
const showConfirmPassword = ref(false);

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    company: '',
    password: '',
    password_confirmation: '',
    consent: false,
});

// Watch for flash messages
watch(() => page.props.flash, (flash: Flash | undefined) => {
    if (flash?.success || flash?.status) {
        const message = flash.success || flash.status;
        $toast.success(message, {
            position: 'top-right',
            duration: 5000,
            dismissible: true,
        });
    }
    
    if (flash?.error) {
        $toast.error(flash.error, {
            position: 'top-right',
            duration: 6000,
            dismissible: true,
        });
    }
}, { immediate: true });

// Watch for form errors
watch(() => form.errors, (errors) => {
    if (Object.keys(errors).length > 0) {
        Object.values(errors).forEach((error: string) => {
            $toast.error(error, {
                position: 'top-right',
                duration: 6000,
                dismissible: true,
            });
        });
    }
});

// Règles du mot de passe
const passwordRules = [
    'Minimum 8 caractères',
    'Au moins une lettre majuscule',
    'Au moins une lettre minuscule',
    'Au moins un chiffre',
];

const submit = () => {
    form.post(route('prospect.register.store'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            form.reset();
        },
        onError: () => {
            // Errors are handled by the watch above
        },
    });
};
</script>

<template>
    <LayoutFront>
        <Head title="Inscription prospect" />

        <!-- En-tête -->
        <div class="relative bg-primary-bg-light py-16 overflow-hidden">
            <div class="absolute inset-0 z-0">
                <img
                    src="https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=1920&q=80"
                    alt="Technology Background"
                    class="w-full h-full object-cover"
                />
                <div class="absolute inset-0 bg-gradient-to-r from-blue-900/85 via-blue-800/75 to-purple-900/85"></div>
            </div>

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-4xl md:text-5xl font-serif font-bold text-white mb-4 drop-shadow-lg">
                    Inscription Prospect
                </h1>
                <p class="text-lg text-white/95 max-w-3xl mx-auto leading-relaxed drop-shadow-md">
                    Créez votre compte pour accéder à nos services et être accompagné dans votre projet.
                </p>
            </div>

            <div class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-yellow-400/50 to-transparent"></div>
        </div>

        <!-- Formulaire -->
        <section class="py-12">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-20">
                <div class="bg-white border rounded-lg shadow-sm p-6 sm:p-8">
                    <form @submit.prevent="submit" class="flex flex-col gap-6">
                        <!-- Informations personnelles -->
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">Informations personnelles</h2>
                            <div class="grid gap-6 md:grid-cols-2">
                                <div class="grid gap-2">
                                    <Label for="first_name" class="required">Prénom</Label>
                                    <Input 
                                        id="first_name" 
                                        v-model="form.first_name" 
                                        required 
                                        autofocus 
                                        autocomplete="given-name" 
                                        placeholder="Jean" 
                                        :disabled="form.processing"
                                    />
                                    <InputError :message="form.errors.first_name" />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="last_name" class="required">Nom</Label>
                                    <Input 
                                        id="last_name" 
                                        v-model="form.last_name" 
                                        required 
                                        autocomplete="family-name" 
                                        placeholder="Dupont" 
                                        :disabled="form.processing"
                                    />
                                    <InputError :message="form.errors.last_name" />
                                </div>

                                <div class="grid gap-2 md:col-span-2">
                                    <Label for="email" class="required">Email</Label>
                                    <Input 
                                        id="email" 
                                        type="email" 
                                        v-model="form.email" 
                                        required 
                                        autocomplete="email" 
                                        placeholder="email@exemple.com" 
                                        :disabled="form.processing"
                                    />
                                    <InputError :message="form.errors.email" />
                                    <p class="text-sm text-gray-500 mt-1">
                                        Utilisé pour votre connexion et nos communications
                                    </p>
                                </div>

                                <div class="grid gap-2">
                                    <Label for="phone">Téléphone</Label>
                                    <Input 
                                        id="phone" 
                                        type="tel" 
                                        v-model="form.phone" 
                                        autocomplete="tel" 
                                        placeholder="06 12 34 56 78" 
                                        :disabled="form.processing"
                                    />
                                    <InputError :message="form.errors.phone" />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="company">Société</Label>
                                    <Input 
                                        id="company" 
                                        v-model="form.company" 
                                        placeholder="Ma Société SAS" 
                                        :disabled="form.processing"
                                    />
                                    <InputError :message="form.errors.company" />
                                </div>
                            </div>
                        </div>

                        <!-- Mot de passe -->
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">Sécurité du compte</h2>
                            <div class="grid gap-6 md:grid-cols-2">
                                <div class="grid gap-2">
                                    <div class="flex justify-between items-center">
                                        <Label for="password" class="required">Mot de passe</Label>
                                        <button 
                                            type="button" 
                                            @click="showPassword = !showPassword"
                                            class="text-sm text-secondary hover:text-secondary-dark"
                                            :disabled="form.processing"
                                        >
                                            {{ showPassword ? 'Masquer' : 'Afficher' }}
                                        </button>
                                    </div>
                                    <Input 
                                        id="password" 
                                        :type="showPassword ? 'text' : 'password'" 
                                        v-model="form.password" 
                                        required 
                                        autocomplete="new-password" 
                                        placeholder="••••••••" 
                                        :disabled="form.processing"
                                    />
                                    <InputError :message="form.errors.password" />
                                    
                                    <!-- Règles du mot de passe -->
                                    <div class="mt-2 p-3 bg-gray-50 rounded-md">
                                        <p class="text-sm font-medium text-gray-700 mb-2">Le mot de passe doit contenir :</p>
                                        <ul class="text-sm text-gray-600 space-y-1">
                                            <li v-for="rule in passwordRules" :key="rule" class="flex items-start">
                                                <svg class="h-4 w-4 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                                {{ rule }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="grid gap-2">
                                    <div class="flex justify-between items-center">
                                        <Label for="password_confirmation" class="required">Confirmer le mot de passe</Label>
                                        <button 
                                            type="button" 
                                            @click="showConfirmPassword = !showConfirmPassword"
                                            class="text-sm text-secondary hover:text-secondary-dark"
                                            :disabled="form.processing"
                                        >
                                            {{ showConfirmPassword ? 'Masquer' : 'Afficher' }}
                                        </button>
                                    </div>
                                    <Input 
                                        id="password_confirmation" 
                                        :type="showConfirmPassword ? 'text' : 'password'" 
                                        v-model="form.password_confirmation" 
                                        required 
                                        autocomplete="new-password" 
                                        placeholder="••••••••" 
                                        :disabled="form.processing"
                                    />
                                    <InputError :message="form.errors.password_confirmation" />
                                    
                                    <!-- Indicateur de correspondance -->
                                    <div v-if="form.password && form.password_confirmation" class="mt-2">
                                        <div v-if="form.password === form.password_confirmation" class="flex items-center text-green-600 text-sm">
                                            <svg class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            Les mots de passe correspondent
                                        </div>
                                        <div v-else class="flex items-center text-red-600 text-sm">
                                            <svg class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                            Les mots de passe ne correspondent pas
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Consentement -->
                        <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <div class="flex items-start gap-3">
                                <Checkbox 
                                    id="consent" 
                                    v-model="form.consent" 
                                    :disabled="form.processing"
                                />
                                <div>
                                    <Label for="consent" class="text-sm leading-6 font-medium text-gray-900">
                                        J'accepte les conditions d'utilisation et la politique de confidentialité
                                    </Label>
                                    <p class="text-sm text-gray-600 mt-1">
                                        En cochant cette case, vous acceptez que vos données soient traitées dans le cadre de votre demande et conformément au RGPD. Vous pourrez vous désinscrire à tout moment.
                                    </p>
                                </div>
                            </div>
                            <InputError :message="form.errors.consent" class="mt-2" />
                        </div>

                        <!-- Bouton de soumission -->
                        <Button 
                            type="submit" 
                            class="mt-2 w-full md:w-auto md:self-start px-8" 
                            :disabled="form.processing || !form.consent"
                            size="lg"
                        >
                            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
                            {{ form.processing ? 'Création en cours...' : 'Créer mon compte' }}
                        </Button>

                        <!-- Informations supplémentaires -->
                        <div class="mt-4 p-4 bg-green-50 rounded-lg border border-green-200">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-green-700">
                                        <strong>Après inscription :</strong> Vous serez redirigé vers la page de connexion. 
                                        Utilisez vos identifiants pour accéder à votre espace prospect.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Lien vers connexion -->
                <p class="mt-6 text-sm text-muted-foreground text-center">
                    Vous avez déjà un compte ?
                    <Link :href="route('login')" class="text-secondary hover:text-secondary-dark font-medium underline underline-offset-4 ml-1">
                        Se connecter
                    </Link>
                </p>
            </div>
        </section>
    </LayoutFront>
</template>

<style scoped>
.required::after {
    content: ' *';
    color: #ef4444;
}
</style>