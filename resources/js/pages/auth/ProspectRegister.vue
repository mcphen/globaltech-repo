<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
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
  <AuthBase title="Créer un compte" description="Inscrivez‑vous pour créer votre compte et être enregistré comme prospect">
    <Head title="Inscription prospect" />

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

      <div class="flex items-start space-x-3">
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

      <p class="text-sm text-muted-foreground">
        Vous avez déjà un compte ?
        <Link :href="route('login')" class="underline underline-offset-4">Se connecter</Link>
      </p>
    </form>
  </AuthBase>
</template>
