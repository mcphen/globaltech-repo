<template>
    <section class="relative bg-white">
        <!-- Desktop version -->
        <div class="hidden md:block">
            <div class="grid grid-cols-2">
                <!-- Left side - Image -->
                <div class="h-[600px] bg-gray-100">
                    <img
                        src="https://nasad.net/wp-content/uploads/2022/09/Admin-ReseauxSysteme.jpeg"
                        alt="TONGOLO TECH Presentation"
                        class="w-full h-full object-cover"
                        style="object-position: top"

                        onerror="this.src='/images/banner/banner_maj.jpg'; this.onerror=null;"
                    />
                </div>

                <!-- Right side - Content -->
                <div class="flex items-center bg-primary-dark">
                    <div class="p-12 max-w-xl">
                        <h1 class="text-3xl font-serif font-bold text-white mb-6">
                            Présentation de TONGOLO TECH
                        </h1>
                        <div class="prose prose-lg text-white/90">
                            <p>
                                 Créée en 2015, TONGOLO Tech (l’Etoile de la technologie) est une société à responsabilité limitée 
                                (SARL) dont le siège social est à Dakar, Fass Paillote.
                            </p>
                            <p>
                                 Elle est spécialisée dans l’Ingénierie, les prestations de services, la fourniture d’équipements et la 
maintenance
                            </p>
                            
                        </div>
                        <div class="mt-8 flex flex-wrap gap-4">
                            <Link
                                :href="route('appointment.create')"
                                class="px-8 py-3 rounded-full bg-white text-primary-dark hover:bg-white/90 font-medium transition-colors"
                            >
                                Prendre rendez-vous
                            </Link>
                            <a
                                href="#services"
                                class="px-8 py-3 rounded-full border border-white text-white hover:bg-white/10 font-medium transition-colors"
                            >
                                Découvrir nos services
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
       

        <!-- Mobile version -->
        <div class="md:hidden">
            <!-- Image -->
            <div class="h-[300px] bg-gray-100">
                <img
                    src="https://nasad.net/wp-content/uploads/2022/09/Admin-ReseauxSysteme.jpeg"
                    alt="TONGOLO-TECH Team"
                    class="w-full h-full object-cover"
                    style="object-position: top"
                    onerror="this.src='/images/banner/banner_maj.jpg'; this.onerror=null;"
                />
            </div>

            <!-- Content -->
            <div class="bg-primary-dark p-6">
                <h1 class="text-2xl font-serif font-bold text-white mb-4">
                    Présentation de TONGOLO TECH
                </h1>
                <div class="prose prose-sm text-white/90">
                    <p>
                         Créée en 2015, TONGOLO Tech (l’Etoile de la technologie) est une société à responsabilité limitée 
                        (SARL) dont le siège social est à Dakar, Fass Paillote.
                    </p>
                    <p>
                         Elle est spécialisée dans l’Ingénierie, les prestations de services, la fourniture d’équipements et la 
maintenance
                    </p>
                </div>
                <div class="mt-6 flex flex-wrap gap-3">
                    <Link
                        :href="route('appointment.create')"
                        class="px-6 py-2 rounded-full bg-white text-primary-dark hover:bg-white/90 font-medium transition-colors text-sm"
                    >
                        Prendre rendez-vous
                    </Link>
                    <a
                        href="#services"
                        class="px-6 py-2 rounded-full border border-white text-white hover:bg-white/10 font-medium transition-colors text-sm"
                    >
                        Découvrir nos services
                    </a>
                </div>
            </div>
        </div>
        

        <!-- Why choose us section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center mb-12">
                <h2 class="text-2xl font-serif font-bold text-gray-900">Pourquoi choisir TONGOLO TECH ?</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto bg-primary/10 rounded-full flex items-center justify-center mb-4">
                        <span class="text-2xl text-primary">🔍</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Expertise pluridisciplinaire</h3>
                    <p class="text-gray-600 text-sm">Une équipe aux compétences variées pour répondre à tous vos besoins</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 mx-auto bg-primary/10 rounded-full flex items-center justify-center mb-4">
                        <span class="text-2xl text-primary">✨</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Approche personnalisée</h3>
                    <p class="text-gray-600 text-sm">Des solutions sur mesure adaptées à votre contexte spécifique</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 mx-auto bg-primary/10 rounded-full flex items-center justify-center mb-4">
                        <span class="text-2xl text-primary">🔒</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Confidentialité & intégrité</h3>
                    <p class="text-gray-600 text-sm">Protection absolue de vos données et informations sensibles</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 mx-auto bg-primary/10 rounded-full flex items-center justify-center mb-4">
                        <span class="text-2xl text-primary">📈</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Résultats mesurables</h3>
                    <p class="text-gray-600 text-sm">Des impacts concrets et quantifiables pour votre entreprise</p>
                </div>
            </div>
        </div>
        
    </section>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>

<style scoped>
.prose p {
    margin-bottom: 1rem;
}
</style>
