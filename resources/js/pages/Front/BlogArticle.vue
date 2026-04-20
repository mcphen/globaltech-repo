<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import { computed } from 'vue';

interface Actualite {
    id: number; title: string; description: string;
    image_path?: string; image_url?: string;
    published_at?: string; created_at: string;
}

const props = defineProps<{ actualite: Actualite; }>();

const formatDate = (d: string) => new Date(d).toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' });
const metaDescription = computed(() => (props.actualite.description || '').replace(/<[^>]*>?/gm, '').slice(0, 160));
</script>

<template>
    <Head>
        <title>{{ actualite.title }} — GlobalTECH EDUCATION Africa</title>
        <meta name="description" :content="metaDescription" />
    </Head>
    <LayoutFront>

        <!-- Hero -->
        <section class="py-20 relative overflow-hidden" style="background: linear-gradient(rgba(255,255,255,0.025) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.025) 1px,transparent 1px),linear-gradient(135deg,#060C22 0%,#0B1437 42%,#0E2060 72%,#091830 100%); background-size:60px 60px,60px 60px,100% 100%;">
            <div class="max-w-4xl mx-auto px-6 lg:px-8 relative z-10">
                <nav class="flex items-center gap-2 text-sm text-white/60 mb-6">
                    <Link href="/" class="hover:text-white transition-colors">Accueil</Link>
                    <i class="bi bi-chevron-right text-xs"></i>
                    <Link :href="route('blog')" class="hover:text-white transition-colors">Actualités</Link>
                    <i class="bi bi-chevron-right text-xs"></i>
                    <span class="text-white/90 truncate max-w-xs">{{ actualite.title }}</span>
                </nav>
                <h1 class="text-4xl lg:text-5xl font-black text-white mb-5 leading-tight" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                    {{ actualite.title }}
                </h1>
                <p class="text-white/60 text-sm">
                    <i class="bi bi-calendar3 mr-1.5" style="color: #E8A020;"></i>
                    Publié le {{ formatDate(actualite.published_at ?? actualite.created_at) }}
                </p>
            </div>
        </section>

        <!-- Content -->
        <article class="py-16 bg-white">
            <div class="max-w-4xl mx-auto px-6 lg:px-8">

                <!-- Cover Image -->
                <div v-if="actualite.image_url" class="rounded-3xl overflow-hidden mb-12 shadow-xl">
                    <img :src="actualite.image_url" :alt="actualite.title" class="w-full h-96 object-cover" />
                </div>

                <!-- Article Body -->
                <div class="prose prose-lg max-w-none
                    prose-headings:font-black prose-headings:text-gray-900
                    prose-p:text-gray-600 prose-p:leading-relaxed
                    prose-a:text-blue-600 prose-a:no-underline hover:prose-a:underline
                    prose-img:rounded-2xl prose-img:shadow-lg
                    prose-blockquote:border-l-4 prose-blockquote:pl-6
                    prose-strong:text-gray-900"
                    style="--tw-prose-quote-borders: #E8A020;"
                    v-html="actualite.description">
                </div>

                <!-- Share -->
                <div class="mt-12 pt-8 border-t border-gray-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest mb-2" style="color: #0B1437;">Partager cet article</p>
                        <div class="flex items-center gap-3">
                            <a :href="`https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent($page.url)}`" target="_blank"
                                class="w-9 h-9 rounded-xl flex items-center justify-center border border-gray-200 hover:bg-blue-50 transition-colors">
                                <i class="bi bi-linkedin text-sm text-blue-700"></i>
                            </a>
                            <a :href="`https://twitter.com/intent/tweet?text=${encodeURIComponent(actualite.title)}&url=${encodeURIComponent($page.url)}`" target="_blank"
                                class="w-9 h-9 rounded-xl flex items-center justify-center border border-gray-200 hover:bg-gray-50 transition-colors">
                                <i class="bi bi-twitter-x text-sm text-gray-800"></i>
                            </a>
                        </div>
                    </div>
                    <Link :href="route('blog')" class="gt-btn-outline-navy px-6 py-2.5 rounded-xl text-sm">
                        <i class="bi bi-arrow-left"></i> Toutes les actualités
                    </Link>
                </div>
            </div>
        </article>

        <!-- Related CTA -->
        <section class="py-16 border-t border-gray-100" style="background: #F8FAFC;">
            <div class="max-w-3xl mx-auto px-6 text-center">
                <h3 class="text-2xl font-black mb-3" style="color: #0B1437;">Intéressé par nos formations ?</h3>
                <p class="mb-6" style="color: #637084;">Découvrez nos programmes certifiants et faites progresser votre carrière.</p>
                <Link :href="route('formations')" class="gt-btn-gold px-8 py-3.5 rounded-xl font-black">
                    <i class="bi bi-mortarboard-fill"></i> Voir les formations
                </Link>
            </div>
        </section>

    </LayoutFront>
</template>
