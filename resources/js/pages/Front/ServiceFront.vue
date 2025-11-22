<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import { ref, onMounted, computed } from 'vue';

// Interface for service data
interface ServiceItem {
  title: string;
  description: string;
}

interface Service {
  id: number;
  icon: string;
  title: string;
  subtitle: string;
  items: ServiceItem[];
}

// Services data from API
const services = ref<Service[]>([]);

// Fetch services data from API
const fetchServices = async () => {
  try {
    const response = await fetch('/api/services/all-with-items');
    const data = await response.json();
    services.value = data;
  } catch (error) {
    console.error('Error fetching services:', error);
  }
};

const currentUrl = ref('');

// Computed properties for meta tags
const metaTitle = computed(() => "Nos Activités | NG Consulting");
const metaDescription = computed(() => "Découvrez les services professionnels de conseil stratégique, d'assistance comptable, d'audit financier et de gouvernance proposés par NG Consulting. Renforcez la performance de votre entreprise.");

// JSON-LD structured data for services
const servicesJsonLd = computed(() => {
  return {
    '@context': 'https://schema.org',
    '@type': 'ItemList',
    itemListElement: services.value.map((service, index) => ({
      '@type': 'ListItem',
      position: index + 1,
      item: {
        '@type': 'Service',
        name: service.title,
        description: service.subtitle,
        provider: {
          '@type': 'Organization',
          name: 'NG Consulting',
          image: '/images/logo.jpg',
          address: {
            '@type': 'PostalAddress',
            addressLocality: 'Paris',
            addressRegion: 'Île-de-France',
            addressCountry: 'FR'
          }
        }
      }
    }))
  };
});

onMounted(() => {
  currentUrl.value = window.location.href;
  fetchServices();
});
</script>

<template>
  <LayoutFront>
    <Head>
      <title>{{ metaTitle }}</title>
      <meta name="description" :content="metaDescription" />
      <link rel="canonical" :href="currentUrl" />

      <!-- Open Graph / Facebook -->
      <meta property="og:type" content="website" />
      <meta property="og:title" :content="metaTitle" />
      <meta property="og:description" :content="metaDescription" />
      <meta property="og:url" :content="currentUrl" />

      <!-- Twitter -->
      <meta name="twitter:card" content="summary_large_image" />
      <meta name="twitter:title" :content="metaTitle" />
      <meta name="twitter:description" :content="metaDescription" />

      <!-- Add JSON-LD as a string in a meta tag instead of script -->
      <meta name="structured-data" :content="JSON.stringify(servicesJsonLd)" />
    </Head>

    <!-- En-tête de la page -->
    <div class="bg-primary-bg-light py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-serif font-bold text-primary mb-4">Nos Activités</h1>
        <p class="text-lg text-gray-700 max-w-3xl mx-auto">
          NG Consulting vous propose une gamme de services professionnels pour renforcer la performance, la gouvernance et la croissance durable de votre entreprise.
        </p>
      </div>
    </div>

    <!-- Liste des services -->
    <div class="py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Message si aucun service n'est trouvé -->
        <div v-if="services.length === 0" class="text-center py-20">
          <p class="text-lg text-gray-600">Aucun service disponible pour le moment.</p>
        </div>

        <!-- Boucle sur chaque service -->
        <div v-else v-for="(service, index) in services" :key="service.id" class="mb-24 last:mb-0">
          <div class="flex flex-col md:flex-row" :class="{ 'md:flex-row-reverse': index % 2 !== 0 }">
            <!-- Icône et titre du service -->
            <div class="w-full md:w-1/2 mb-8 md:mb-0 bg-primary-bg-light rounded-lg p-8">
              <div class="flex items-center mb-6">
                <div class="w-16 h-16 flex items-center justify-center bg-primary/10 rounded-full mr-4">
                  <span class="text-3xl text-primary">{{ service.icon }}</span>
                </div>
                <h2 class="text-3xl font-serif font-bold text-primary">{{ service.title }}</h2>
              </div>
              <p class="text-gray-700 text-lg mb-8 leading-relaxed">{{ service.subtitle }}</p>
              <Link
                :href="route('appointment.create')"
                class="inline-block px-8 py-3 bg-primary text-white rounded-full hover:bg-primary-dark transition-colors font-medium"
              >
                Prendre rendez-vous
              </Link>
            </div>

            <!-- Contenu détaillé du service -->
            <div class="w-full md:w-1/2 flex items-center" :class="[
              index % 2 === 0 ? 'md:pl-12' : 'md:pr-12'
            ]">
              <div class="space-y-6">
                <div v-for="(item, itemIndex) in service.items" :key="itemIndex" class="flex">
                  <div class="flex-shrink-0 mr-3">
                    <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </div>
                  <div>
                    <h3 class="font-medium text-gray-900 text-lg">{{ item.title }}</h3>
                    <p class="text-gray-700 mt-1">{{ item.description }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Section CTA -->
    <div class="bg-primary-bg-light py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-serif font-bold text-primary mb-6">Prêt à transformer votre entreprise ?</h2>
        <p class="text-lg text-gray-700 max-w-3xl mx-auto mb-8">
          Contactez-nous dès aujourd'hui pour une consultation personnalisée et découvrez comment nos solutions peuvent renforcer votre performance.
        </p>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
          <Link
            :href="route('appointment.create')"
            class="px-8 py-3 bg-primary text-white rounded-full hover:bg-primary-dark transition-colors font-medium"
          >
            prendre rendez-vous
          </Link>
          <Link
            :href="route('contact')"
            class="px-8 py-3 bg-white text-primary border border-primary rounded-full hover:bg-gray-50 transition-colors font-medium"
          >
            Nous contacter
          </Link>
        </div>
      </div>
    </div>
  </LayoutFront>
</template>

<style scoped>
/* Ajout d'une transition douce pour les éléments interactifs */
a {
  transition: all 0.3s ease;
}

/* Animation subtile au survol des cartes de service */
.service-card:hover {
  transform: translateY(-5px);
}
</style>
