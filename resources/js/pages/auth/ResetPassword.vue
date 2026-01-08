<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { useToast } from 'vue-toast-notification';
import { ref, watch } from 'vue';

interface Props {
    token: string;
    email: string;
}

interface Flash {
    success?: string;
    error?: string;
}

const props = defineProps<Props>();
const page = usePage<{ flash?: Flash }>();
const $toast = useToast();
const showPassword = ref(false);
const showConfirmPassword = ref(false);

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

// Watch for flash messages
watch(() => page.props.flash, (flash) => {
    if (flash?.success) {
        $toast.success(flash.success, {
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
        Object.values(errors).forEach(error => {
            $toast.error(error, {
                position: 'top-right',
                duration: 6000,
                dismissible: true,
            });
        });
    }
});

const passwordRules = [
    '• Minimum 8 caractères',
    '• Au moins une lettre majuscule',
    '• Au moins une lettre minuscule',

];

const submit = () => {
    form.post(route('password.store'), {
        preserveScroll: true,
        preserveState: true,
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <AuthLayout 
        title="Réinitialiser le mot de passe" 
        description="Veuillez saisir votre nouveau mot de passe"
    >
        <Head title="Réinitialiser le mot de passe" />

        <div class="space-y-6">
            <form @submit.prevent="submit">
                <div class="grid gap-6">
                    <!-- Email (lecture seule) -->
                    <div class="grid gap-2">
                        <Label for="email">Adresse email</Label>
                        <Input 
                            id="email" 
                            type="email" 
                            name="email" 
                            autocomplete="email" 
                            v-model="form.email" 
                            class="mt-1 block w-full bg-gray-50" 
                            readonly 
                            :disabled="form.processing"
                        />
                        <p class="text-sm text-gray-500">
                            Votre adresse email associée au compte
                        </p>
                        <InputError :message="form.errors.email" class="mt-2" />
                    </div>

                    <!-- Nouveau mot de passe -->
                    <div class="grid gap-2">
                        <div class="flex justify-between items-center">
                            <Label for="password">Nouveau mot de passe *</Label>
                            <button 
                                type="button" 
                                @click="showPassword = !showPassword"
                                class="text-sm text-secondary hover:text-secondary-dark"
                            >
                                {{ showPassword ? 'Masquer' : 'Afficher' }}
                            </button>
                        </div>
                        <Input
                            id="password"
                            :type="showPassword ? 'text' : 'password'"
                            name="password"
                            autocomplete="new-password"
                            v-model="form.password"
                            class="mt-1 block w-full"
                            autofocus
                            placeholder="Saisissez votre nouveau mot de passe"
                            required
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

                    <!-- Confirmation du mot de passe -->
                    <div class="grid gap-2">
                        <div class="flex justify-between items-center">
                            <Label for="password_confirmation">Confirmer le mot de passe *</Label>
                            <button 
                                type="button" 
                                @click="showConfirmPassword = !showConfirmPassword"
                                class="text-sm text-secondary hover:text-secondary-dark"
                            >
                                {{ showConfirmPassword ? 'Masquer' : 'Afficher' }}
                            </button>
                        </div>
                        <Input
                            id="password_confirmation"
                            :type="showConfirmPassword ? 'text' : 'password'"
                            name="password_confirmation"
                            autocomplete="new-password"
                            v-model="form.password_confirmation"
                            class="mt-1 block w-full"
                            placeholder="Confirmez votre nouveau mot de passe"
                            required
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

                    <!-- Bouton de soumission -->
                    <Button 
                        type="submit" 
                        class="mt-2 w-full" 
                        :disabled="form.processing || !form.password || !form.password_confirmation"
                    >
                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
                        {{ form.processing ? 'Réinitialisation en cours...' : 'Réinitialiser le mot de passe' }}
                    </Button>
                </div>
            </form>

            <!-- Informations supplémentaires -->
            <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <strong>Conseils de sécurité :</strong> 
                            Après la réinitialisation, vous serez redirigé vers la page de connexion. 
                            Utilisez votre nouveau mot de passe pour vous connecter.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AuthLayout>
</template>