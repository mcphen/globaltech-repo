<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

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

const submit = () => {
  form.post(route('prospect.register.store'));
};
</script>

<template>
  <LayoutFront>
    <Head title="Inscription prospect" />

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
          Inscription
        </h1>
        <p class="text-lg text-white/95 max-w-3xl mx-auto leading-relaxed drop-shadow-md">
          Créez votre compte pour être enregistré comme prospect.
        </p>
      </div>

      <!-- Élément décoratif (optionnel) -->
      <div class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-yellow-400/50 to-transparent"></div>
    </div>

    <section class="py-12">
      <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-20">

        <div class="bg-white border rounded-lg shadow-sm p-6 sm:p-8">
          <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6 md:grid-cols-2">
              <div class="grid gap-2">
                <Label for="first_name">Prénom</Label>
                <Input id="first_name" v-model="form.first_name" required autofocus autocomplete="given-name" placeholder="Jean" />
                <InputError :message="form.errors.first_name" />
              </div>

              <div class="grid gap-2">
                <Label for="last_name">Nom</Label>
                <Input id="last_name" v-model="form.last_name" required autocomplete="family-name" placeholder="Dupont" />
                <InputError :message="form.errors.last_name" />
              </div>

              <div class="grid gap-2 md:col-span-2">
                <Label for="email">Email</Label>
                <Input id="email" type="email" v-model="form.email" required autocomplete="email" placeholder="email@example.com" />
                <InputError :message="form.errors.email" />
              </div>

              <div class="grid gap-2">
                <Label for="phone">Téléphone</Label>
                <Input id="phone" type="tel" v-model="form.phone" autocomplete="tel" placeholder="06 12 34 56 78" />
                <InputError :message="form.errors.phone" />
              </div>

              <div class="grid gap-2">
                <Label for="company">Société</Label>
                <Input id="company" v-model="form.company" placeholder="Ma Société SAS" />
                <InputError :message="form.errors.company" />
              </div>

              <div class="grid gap-2">
                <Label for="password">Mot de passe</Label>
                <Input id="password" type="password" v-model="form.password" required autocomplete="new-password" placeholder="••••••••" />
                <InputError :message="form.errors.password" />
              </div>

              <div class="grid gap-2">
                <Label for="password_confirmation">Confirmer le mot de passe</Label>
                <Input id="password_confirmation" type="password" v-model="form.password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
              </div>
            </div>

            <div class="flex items-start gap-3">
              <Checkbox id="consent" v-model="form.consent" />
              <Label for="consent" class="text-sm leading-6">
                J'accepte que mes données soient utilisées pour être recontacté(e) dans le cadre de ma demande (RGPD)
              </Label>
            </div>
            <InputError :message="form.errors.consent" />

            <Button type="submit" class="mt-2 w-full md:w-auto" :disabled="form.processing">
              <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
              Créer mon compte
            </Button>
          </form>
        </div>

        <p class="mt-6 text-sm text-muted-foreground">
          Vous avez déjà un compte ?
          <Link :href="route('login')" class="underline underline-offset-4">Se connecter</Link>
        </p>
      </div>
    </section>
  </LayoutFront>
</template>
