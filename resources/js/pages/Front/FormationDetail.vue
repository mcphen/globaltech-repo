<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import { computed, ref, onMounted } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

interface Formation {
  id: number;
  title: string;
  description: string;
  image_path?: string | null;
  attachment_path?: string | null;
  date_mode: 'single' | 'range';
  date?: string | null;
  start_time?: string | null;
  end_time?: string | null;
  start_date?: string | null;
  end_date?: string | null;
  duration_hours?: number | null;
  duration_per_day_hours?: number | null;
  price?: number | null;
  currency?: string | null;
  created_at: string;
  updated_at: string;
}

const props = defineProps<{ formation: Formation }>();

const metaTitle = computed(() => `${props.formation.title} | Formations | TONGOLO TECHs`);
const metaDescription = computed(() => {
  const clean = (props.formation.description || '').replace(/<[^>]*>?/gm, '');
  return clean.slice(0, 160);
});

const formatDate = (dateString?: string | null) => {
  if (!dateString) return '';
  const d = new Date(dateString);
  return d.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' });
};

const showForm = ref(false);
const isSubmitting = ref(false);
const participateForm = useForm({
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  attentes: ''
});

// Auth/participation status
const isAuthenticated = ref(false);
const alreadySelected = ref(false);
const currentStatus = ref<string | null>(null);

const disableIdentityFields = computed(() => isAuthenticated.value && !alreadySelected.value);

onMounted(async () => {
  try {
    const resp = await axios.get(route('formations.participation-status', props.formation.id));
    if (resp?.data) {
      isAuthenticated.value = !!resp.data.authenticated;
      if (resp.data.authenticated) {
        alreadySelected.value = !!resp.data.selected;
        currentStatus.value = resp.data.status ?? null;
        const u = resp.data.user || {};
        if (!alreadySelected.value) {
          // prefill and lock identity fields
          participateForm.first_name = u.first_name || '';
          participateForm.last_name = u.last_name || '';
          participateForm.email = u.email || '';
          participateForm.phone = u.phone || '';
        }
      }
    }
  } catch (e) {
    // 401/guests simply mean not authenticated
    console.debug('participation-status failed/guest', e);
  }
});

async function submitParticipation() {
  try {
    // Démarrer l'état de soumission
    isSubmitting.value = true;
    // Nettoyer les erreurs précédentes si l'API existe
    if (typeof (participateForm as any).clearErrors === 'function') {
      (participateForm as any).clearErrors();
    }

    const payload = {
      first_name: participateForm.first_name,
      last_name: participateForm.last_name,
      email: participateForm.email,
      phone: participateForm.phone,
      attentes: participateForm.attentes,
    };

    const resp = await axios.post(route('formations.participate', props.formation.id), payload);

    // If backend suggests a redirect (guest flow), follow it immediately
    if (resp?.data?.redirect) {
      window.location.href = resp.data.redirect as string;
      return;
    }

    // Authenticated flow: mark as selected and show status
    if (resp?.data?.success) {
      alreadySelected.value = true;
      if (resp.data.status) currentStatus.value = resp.data.status as string;
      await Swal.fire({
        icon: 'success',
        title: 'Participation enregistrée',
        text: 'Votre participation a été enregistrée avec succès.',
        confirmButtonText: 'OK',
      });
      showForm.value = false;
      // Keep attentes in memory (no need to reset identity fields)
      participateForm.attentes = '';
      return;
    }

    // Fallback success UI
    await Swal.fire({
      icon: 'success',
      title: 'Participation enregistrée',
      text: 'Votre participation a été enregistrée avec succès.',
      confirmButtonText: 'OK',
    });

    showForm.value = false;
    participateForm.reset();
  } catch (error: any) {
    if (error?.response?.status === 422 && error.response.data?.errors) {
      const errs = error.response.data.errors as Record<string, string[] | string>;
      for (const [field, messages] of Object.entries(errs)) {
        const message = Array.isArray(messages) ? messages[0] : messages;
        if (typeof (participateForm as any).setError === 'function') {
          (participateForm as any).setError(field, message);
        }
      }
    } else {
      console.error(error);
      await Swal.fire({
        icon: 'error',
        title: 'Une erreur est survenue',
        text: "Impossible d'enregistrer votre participation. Veuillez réessayer.",
        confirmButtonText: 'Fermer',
      });
    }
  } finally {
    isSubmitting.value = false;
  }
}

const publicUrl = computed<string>(() => route('formations.show', props.formation.id));

async function shareFormation() {
  try {
    const url = publicUrl.value;
    const title = props.formation?.title || 'Formation';
    const text = `Découvrez la formation "${title}"`;

    if (navigator.share) {
      await navigator.share({ title, text, url });
      return;
    }

    if (navigator.clipboard && navigator.clipboard.writeText) {
      await navigator.clipboard.writeText(url);
      alert('Lien copié dans le presse-papiers.');
      return;
    }

    const textarea = document.createElement('textarea');
    textarea.value = url;
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand('copy');
    document.body.removeChild(textarea);
    alert('Lien copié dans le presse-papiers.');
  } catch (e) {
    console.error(e);
    alert('Impossible de partager le lien. Veuillez réessayer.');
  }
}
</script>

<template>
  <LayoutFront>
    <Head>
      <title>{{ metaTitle }}</title>
      <meta name="description" :content="metaDescription" />
    </Head>

    <div class="relative bg-gray-900">
      <div class="absolute inset-0 overflow-hidden">
        <img src="/images/breadcrumb-bg.jpg" alt="Bannière Formation" class="w-full h-full object-cover object-center opacity-40">
        <div class="absolute inset-0 bg-gradient-to-r from-primary/50 to-primary/30"></div>
      </div>

      <div class="relative max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
        <nav class="mb-4 text-white/80 text-sm" aria-label="Breadcrumb">
          <Link href="/" class="hover:text-white">Accueil</Link>
          <span class="mx-2">/</span>
          <Link :href="route('formations')" class="hover:text-white">Formations</Link>
          <span class="mx-2">/</span>
          <span class="text-white">{{ props.formation.title }}</span>
        </nav>
        <h1 class="text-3xl md:text-4xl font-serif font-bold text-white">{{ props.formation.title }}</h1>
      </div>
    </div>

    <section class="bg-white py-12">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
          <img v-if="props.formation.image_path" :src="`${props.formation.image_path}`" :alt="props.formation.title" class="w-full h-80 object-cover rounded-md mb-6" />

          <div class="prose max-w-none" v-html="props.formation.description"></div>

          <div v-if="props.formation.attachment_path" class="mt-8">
            <a :href="`${props.formation.attachment_path}`" target="_blank" class="inline-flex items-center gap-2 text-primary hover:text-primary-dark font-medium">
              Télécharger la brochure (PDF)
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4" />
              </svg>
            </a>
          </div>
        </div>

        <aside class="lg:col-span-1">
          <div class="rounded-lg border p-5 bg-white shadow-sm">
            <h2 class="text-lg font-semibold mb-4">Informations</h2>
            <ul class="space-y-2 text-gray-700">
              <li>
                <span class="font-medium">Dates: </span>
                <template v-if="props.formation.date_mode === 'single'">
                  Le {{ formatDate(props.formation.date) }}
                  <template v-if="props.formation.start_time && props.formation.end_time">
                    de {{ props.formation.start_time }} à {{ props.formation.end_time }}
                  </template>
                </template>
                <template v-else>Du {{ formatDate(props.formation.start_date) }} au {{ formatDate(props.formation.end_date) }}</template>
              </li>
              <li v-if="props.formation.date_mode === 'single' && props.formation.duration_hours">
                <span class="font-medium">Durée: </span>{{ props.formation.duration_hours }} h
              </li>
              <li v-if="props.formation.date_mode === 'range' && props.formation.duration_per_day_hours">
                <span class="font-medium">Durée par jour: </span>{{ props.formation.duration_per_day_hours }} h/jour
              </li>
              <li v-if="props.formation.price">
                <span class="font-medium">Tarif: </span>
                {{ props.formation.price?.toLocaleString('fr-FR', { minimumFractionDigits: 0, maximumFractionDigits: 2 }) }}
                <span class="ml-1">{{ props.formation.currency || 'FCFA' }}</span>
              </li>
            </ul>

            <div class="mt-6 space-y-3">
              <template v-if="alreadySelected">
                <div class="p-4 border rounded bg-gray-50">
                  <p class="text-sm text-gray-700">Vous avez déjà choisi cette formation.</p>
                  <p class="mt-1 text-sm"><span class="font-medium">Statut:</span> <span class="uppercase">{{ currentStatus || '—' }}</span></p>
                </div>
              </template>
              <template v-else>
                <button @click="showForm = !showForm" class="inline-block w-full text-center px-4 py-2 bg-secondary text-white rounded-md hover:bg-secondary-dark">
                  {{ showForm ? 'Fermer' : 'Participer' }}
                </button>
              </template>
              <button type="button" @click="shareFormation" class="inline-block w-full text-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-md">
                Partager
              </button>
              <Link :href="route('contact')" class="inline-block w-full text-center px-4 py-2 border border-secondary text-secondary rounded-md hover:bg-secondary/10">Nous contacter</Link>
            </div>

            <form v-if="showForm && !alreadySelected" @submit.prevent="submitParticipation" class="mt-4 space-y-3">
              <div class="grid grid-cols-1 gap-3">
                <div>
                  <label class="block text-sm font-medium mb-1">Prénom</label>
                  <input v-model="participateForm.first_name" type="text" class="w-full px-3 py-2 border rounded" :disabled="disableIdentityFields" required />
                  <p v-if="participateForm.errors.first_name" class="text-red-600 text-sm mt-1">{{ participateForm.errors.first_name }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">Nom</label>
                  <input v-model="participateForm.last_name" type="text" class="w-full px-3 py-2 border rounded" :disabled="disableIdentityFields" required />
                  <p v-if="participateForm.errors.last_name" class="text-red-600 text-sm mt-1">{{ participateForm.errors.last_name }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">Email</label>
                  <input v-model="participateForm.email" type="email" class="w-full px-3 py-2 border rounded" :disabled="disableIdentityFields" required />
                  <p v-if="participateForm.errors.email" class="text-red-600 text-sm mt-1">{{ participateForm.errors.email }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">Téléphone</label>
                  <input v-model="participateForm.phone" type="text" class="w-full px-3 py-2 border rounded" :disabled="disableIdentityFields" />
                  <p v-if="participateForm.errors.phone" class="text-red-600 text-sm mt-1">{{ participateForm.errors.phone }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">Attentes par rapport à la formation</label>
                  <textarea v-model="participateForm.attentes" class="w-full px-3 py-2 border rounded" rows="4" placeholder="Vos attentes..."></textarea>
                  <p v-if="participateForm.errors.attentes" class="text-red-600 text-sm mt-1">{{ participateForm.errors.attentes }}</p>
                </div>
              </div>
              <button type="submit" :disabled="isSubmitting" class="px-4 py-2 bg-primary text-white rounded w-full">
                {{ isSubmitting ? 'Envoi...' : 'Envoyer' }}
              </button>
            </form>
          </div>
        </aside>
      </div>
    </section>
  </LayoutFront>
</template>

<style scoped>
.prose :where(img, video) { max-width: 100%; border-radius: 0.5rem; }
</style>
