<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { useToast } from 'vue-toast-notification';
import { watch } from 'vue';

interface Props {
    status?: string;
}

interface Flash {
    success?: string;
    error?: string;
}

const props = defineProps<Props>();
const page = usePage<{ flash?: Flash }>();
const $toast = useToast();

const form = useForm({
    email: '',
});

// Watch for status messages from props
watch(() => props.status, (status: string | undefined) => {
    if (status) {
        $toast.success(status, {
            position: 'top-right',
            duration: 5000,
            dismissible: true,
        });
    }
});

// Watch for flash messages
watch(() => page.props.flash, (flash: Flash | undefined) => {
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
        Object.values(errors).forEach((error: string) => {
            $toast.error(error, {
                position: 'top-right',
                duration: 6000,
                dismissible: true,
            });
        });
    }
});

const submit = () => {
    form.post(route('password.email'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            form.reset('email');
        },
        onError: () => {
            // Errors are handled by the watch above
        },
    });
};
</script>

<template>
    <AuthLayout 
        title="Mot de passe oublié" 
        description="Entrez votre adresse email pour recevoir un lien de réinitialisation"
    >
        <Head title="Mot de passe oublié" />

        <div class="space-y-6">
            <form @submit.prevent="submit">
                <div class="grid gap-2">
                    <Label for="email">Adresse email *</Label>
                    <Input 
                        id="email" 
                        type="email" 
                        name="email" 
                        autocomplete="email" 
                        v-model="form.email" 
                        autofocus 
                        placeholder="email@exemple.com" 
                        required
                        :disabled="form.processing"
                    />
                    <InputError :message="form.errors.email" />
                    <p class="text-sm text-gray-500 mt-1">
                        Nous vous enverrons un lien sécurisé pour réinitialiser votre mot de passe.
                    </p>
                </div>

                <div class="my-6 flex items-center justify-start">
                    <Button class="w-full" :disabled="form.processing">
                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
                        {{ form.processing ? 'Envoi en cours...' : 'Envoyer le lien de réinitialisation' }}
                    </Button>
                </div>
            </form>

            <div class="space-x-1 text-center text-sm text-muted-foreground">
                <span>Vous vous souvenez de votre mot de passe ?</span>
                <TextLink :href="route('login')">Retour à la connexion</TextLink>
            </div>
            
            <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <strong>Important :</strong> Le lien de réinitialisation est valable 60 minutes. 
                            Vérifiez votre dossier spam si vous ne recevez pas l'email.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AuthLayout>
</template>