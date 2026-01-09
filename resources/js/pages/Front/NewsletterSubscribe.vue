<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { useToast } from 'vue-toast-notification';
import { computed } from 'vue';

interface Props {
  title?: string;
  description?: string;
  compact?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  title: 'Restez informé de nos actualités',
  description: 'Abonnez-vous à notre newsletter pour suivre nos actualités, nos solutions technologiques et nos formations dédiées aux professionnels.',
  compact: false,
});

const $toast = useToast();

// Formulaire d'abonnement
const subscribeForm = useForm({
  email: '',
});

// Fonction pour gérer l'abonnement
const handleSubscribe = () => {
  subscribeForm.post(route('subscribe.store'), {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      // Succès
      $toast.success('Abonnement réussi ! Merci de vous être abonné à notre newsletter.', {
        position: 'top-right',
        duration: 4000,
        dismissible: true,
      });

      // Réinitialiser le formulaire
      subscribeForm.reset();
      subscribeForm.clearErrors();
    },
    onError: (errors) => {
      // Gérer les erreurs spécifiques
      if (errors.email) {
        $toast.error(errors.email, {
          position: 'top-right',
          duration: 8000,
          dismissible: true,
        });
      }

      // Gérer les erreurs générales
      if (errors.error) {
        $toast.error(errors.error, {
          position: 'top-right',
          duration: 8000,
          dismissible: true,
        });
      }
    },
    onFinish: () => {
      // Nettoyer après traitement
      subscribeForm.clearErrors();
    }
  });
};

// Classe calculée pour le layout
const containerClass = computed(() => {
  return props.compact
    ? 'py-12'
    : 'py-16 bg-primary-bg-light';
});

const contentClass = computed(() => {
  return props.compact
    ? 'max-w-2xl mx-auto px-4 sm:px-6 lg:px-8'
    : 'max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center';
});

// Classe pour le champ d'input
const inputClass = computed(() => {
  const baseClass = 'flex-grow px-4 py-2 rounded-l-full border-y border-l focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent disabled:bg-gray-100';

  if (subscribeForm.errors.email) {
    return `${baseClass} border-red-300 focus:ring-red-500`;
  }

  return `${baseClass} border-gray-300`;
});
</script>

<template>
  <section :class="containerClass">
    <div :class="contentClass">
      <h2 v-if="title" class="text-2xl font-serif font-bold text-primary mb-4">
        {{ title }}
      </h2>
      <p v-if="description" class="text-lg text-gray-700 mb-8">
        {{ description }}
      </p>

      <div class="max-w-md mx-auto">
        <!-- Formulaire d'abonnement -->
        <form @submit.prevent="handleSubscribe" class="space-y-3">
          <div class="flex">
            <input
              v-model="subscribeForm.email"
              type="email"
              placeholder="Votre adresse email"
              required
              :disabled="subscribeForm.processing"
              :class="inputClass"
            />
            <button
              type="submit"
              :disabled="subscribeForm.processing"
              class="px-6 py-2 bg-primary text-white rounded-r-full hover:bg-primary-dark transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="subscribeForm.processing">
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
              </span>
              <span v-else>S'abonner</span>
            </button>
          </div>

          <!-- Message d'erreur inline -->
          <p v-if="subscribeForm.errors.email" class="text-sm text-red-600 text-left animate-fade-in">
            {{ subscribeForm.errors.email }}
          </p>

          <p class="text-sm text-gray-500 mt-2">
            Nous respectons votre vie privée. Désabonnez-vous à tout moment.
          </p>
        </form>
      </div>
    </div>
  </section>
</template>

<style scoped>
/* Animation pour le spinner */
.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

/* Animation pour les messages d'erreur */
.animate-fade-in {
  animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-5px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
