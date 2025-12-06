<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
  <LayoutFront>
    <Head title="Connexion" />

    <!-- En-tête identique à ProductsFront.vue -->
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
      <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-serif font-bold text-white mb-4 drop-shadow-lg">
          Connexion
        </h1>
        <p class="text-lg text-white/95 max-w-3xl mx-auto leading-relaxed drop-shadow-md">
          Accédez à votre espace en saisissant vos identifiants.
        </p>
      </div>

      <!-- Élément décoratif (optionnel) -->
      <div class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-yellow-400/50 to-transparent"></div>
    </div>

    <section class="py-12">
      <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-20">

        <div class="bg-white border rounded-lg shadow-sm p-6 sm:p-8">
          <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
            {{ status }}
          </div>

          <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">
              <div class="grid gap-2">
                <Label for="email">Email</Label>
                <Input
                  id="email"
                  type="email"
                  required
                  autofocus
                  :tabindex="1"
                  autocomplete="email"
                  v-model="form.email"
                  placeholder="email@example.com"
                />
                <InputError :message="form.errors.email" />
              </div>

              <div class="grid gap-2">
                <div class="flex items-center justify-between">
                  <Label for="password">Mot de passe</Label>
                  <Link v-if="canResetPassword" :href="route('password.request')" class="text-sm text-primary hover:text-secondary">
                    Mot de passe oublié ?
                  </Link>
                </div>
                <Input
                  id="password"
                  type="password"
                  required
                  :tabindex="2"
                  autocomplete="current-password"
                  v-model="form.password"
                  placeholder="••••••••"
                />
                <InputError :message="form.errors.password" />
              </div>

              <div class="flex items-center justify-between">
                <Label for="remember" class="flex items-center gap-3">
                  <Checkbox id="remember" v-model="form.remember" :tabindex="3" />
                  <span>Se souvenir</span>
                </Label>
              </div>

              <Button type="submit" class="mt-2 w-full md:w-auto" :tabindex="4" :disabled="form.processing">
                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                Connexion
              </Button>
            </div>
          </form>
        </div>

        <p class="mt-6 text-sm text-muted-foreground text-center">
          Vous n'avez pas encore de compte ?  <Link :href="route('prospect.register')" class="">Inscrivez-vous</Link>
        </p>
      </div>
    </section>
  </LayoutFront>
</template>
