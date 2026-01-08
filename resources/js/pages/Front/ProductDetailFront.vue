<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import LayoutFront from '@/layouts/Front/LayoutFront.vue';
import { ref, computed, onMounted } from 'vue';

// Define Product interface
interface Product {
    id: number;
    title: string;
    description: string;
    price: number;
    image_path: string | null;
    image_url?: string;
}

// Define props with proper typing
const props = defineProps<{
    product: Product;
    relatedProducts?: Product[];
    contactSettings?: any;
}>();

// Current URL for canonical and sharing
const currentUrl = ref('');

// Cart: quantity and add
const quantity = ref<number>(1);

const addToCart = () => {
    if (!props.product?.id) return;

    const q = Math.max(1, Number(quantity.value) || 1);
    router.post(route('cart.add', { id: props.product.id }), { quantity: q }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => router.reload({ only: ['cart'] })
    });
};

// Format price with currency
const formatPrice = (price: number): string => {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(price);
};


// Extraire le texte brut du HTML et le tronquer à 100 caractères
function stripAndTruncateHtml(html: string | null, limit = 100): string {
    if (!html) return '';

    const tempDiv = document.createElement("div");
    tempDiv.innerHTML = html;
    const text = tempDiv.textContent || tempDiv.innerText || "";

    // Tronquer
    if (text.length > limit) {
        return text.substring(0, limit) + "...";
    }

    return text;
}


// Computed properties for meta tags
const metaTitle = computed(() =>
    props.product ? `${props.product.title} | Sophie Wedding` : 'Produit | Sophie Wedding'
);

const metaDescription = computed(() => {
    if (!props.product) return 'Découvrez nos produits de qualité';

    return props.product.description
        ? props.product.description.substring(0, 160)
        : `Découvrez ${props.product.title} - Produit de qualité par TONGOLO TECHs`;
});

// Helper function to get full image URL
const getImageUrl = (imagePath: string | null): string => {
    if (!imagePath) return '';
    if (typeof window === 'undefined') return '';
    return `${window.location.origin}/storage/${imagePath}`;
};

// JSON-LD structured data for product
const productJsonLd = computed(() => {
    if (!props.product) return null;

    return {
        '@context': 'https://schema.org',
        '@type': 'Product',
        name: props.product.title,
        description: props.product.description,
        image: getImageUrl(props.product.image_path),
        offers: {
            '@type': 'Offer',
            price: props.product.price,
            priceCurrency: 'XOF',
            availability: 'https://schema.org/InStock'
        },
        brand: {
            '@type': 'Brand',
            name: 'TONGOLO TECHs'
        }
    };
});

onMounted(() => {
    // Set current URL safely
    if (typeof window !== 'undefined') {
        currentUrl.value = window.location.href;
    }

    // Add JSON-LD script tag programmatically
    if (productJsonLd.value) {
        const script = document.createElement('script');
        script.type = 'application/ld+json';
        script.textContent = JSON.stringify(productJsonLd.value);
        document.head.appendChild(script);
    }
});
</script>

<template>
    <LayoutFront>
        <Head v-if="product">
            <title>{{ metaTitle }}</title>
            <meta name="description" :content="metaDescription" />
            <link rel="canonical" :href="currentUrl" />

            <!-- Open Graph / Facebook -->
            <meta property="og:type" content="product" />
            <meta property="og:title" :content="metaTitle" />
            <meta property="og:description" :content="metaDescription" />
            <meta property="og:image" :content="getImageUrl(product.image_path)" />
            <meta property="og:url" :content="currentUrl" />

            <!-- Twitter -->
            <meta name="twitter:card" content="summary_large_image" />
            <meta name="twitter:title" :content="metaTitle" />
            <meta name="twitter:description" :content="metaDescription" />
            <meta name="twitter:image" :content="getImageUrl(product.image_path)" />
        </Head>

        <!-- Error state if no product -->
        <div v-if="!product" class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-2xl font-bold text-gray-900 mb-4">Produit introuvable</h1>
                <Link :href="route('products')" class="text-primary hover:underline">
                    Retour aux produits
                </Link>
            </div>
        </div>

        <!-- Product content -->
        <template v-else>
            <!-- Breadcrumb -->
            <div class="bg-gray-100 py-4">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center text-sm text-gray-600">
                        <Link :href="route('home')" class="hover:text-primary">Accueil</Link>
                        <span class="mx-2">/</span>
                        <Link :href="route('products')" class="hover:text-primary">Produits</Link>
                        <span class="mx-2">/</span>
                        <span class="text-primary">{{ product.title }}</span>
                    </div>
                </div>
            </div>

            <!-- Product Detail Section -->
            <div class="py-12">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                        <div class="md:flex">
                            <!-- Product Image -->
                            <div class="md:w-1/2">
                                <div class="h-96 md:h-full overflow-hidden">
                                    <img
                                        v-if="product.image_path"
                                        :src="'/storage/' + product.image_path"
                                        :alt="product.title"
                                        class="w-full h-full object-cover object-center"
                                    />
                                    <div v-else class="w-full h-full bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-400">Pas d'image</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Product Info -->
                            <div class="md:w-1/2 p-8">
                                <h1 class="text-3xl font-serif font-bold text-gray-900 mb-4">{{ product.title }}</h1>
                                <p class="text-2xl text-primary font-bold mb-6">{{ formatPrice(product.price) }}</p>

                                <div class="prose prose-lg max-w-none mb-8">
                                    <p v-html="product.description"></p>
                                </div>

                                <div class="flex flex-col gap-4">
                                    <div class="flex items-center gap-3">
                                        <label for="qty" class="text-sm text-gray-700">Quantité</label>
                                        <input id="qty" type="number" min="1" v-model.number="quantity" class="w-24 border rounded px-3 py-2" />
                                    </div>
                                    <div class="flex flex-col sm:flex-row gap-4">
                                        <button
                                            @click="addToCart"
                                            class="px-8 py-3 bg-primary text-white rounded-full hover:bg-primary-dark transition-colors font-medium text-center"
                                        >
                                            Ajouter au panier
                                        </button>
                                        <Link
                                            :href="route('cart.index')"
                                            class="px-8 py-3 bg-white text-primary border border-primary rounded-full hover:bg-gray-50 transition-colors font-medium text-center"
                                        >
                                            Aller au panier
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Products Section -->
            <div class="py-12 bg-gray-50">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <h2 class="text-2xl font-serif font-bold text-primary mb-8 text-center">Produits similaires</h2>

                    <div v-if="relatedProducts && relatedProducts.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div v-for="relatedProduct in relatedProducts" :key="relatedProduct.id" class="bg-white rounded-lg shadow-md overflow-hidden transition-transform duration-300 hover:-translate-y-1 hover:shadow-lg">
                            <!-- Product Image -->
                            <div class="h-48 overflow-hidden">
                                <img
                                    v-if="relatedProduct.image_url"
                                    :src="relatedProduct.image_url"
                                    :alt="relatedProduct.title"
                                    class="w-full h-full object-cover object-center"
                                />
                                <div v-else class="w-full h-full bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-400">Pas d'image</span>
                                </div>
                            </div>

                            <!-- Product Content -->
                            <div class="p-4">
                                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ relatedProduct.title }}</h3>
                                <p class="text-primary font-bold mb-2">{{ formatPrice(relatedProduct.price) }}</p>
                                <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ stripAndTruncateHtml(relatedProduct.description, 100) }}</p>
                                <Link
                                    :href="route('product.show', { id: relatedProduct.id })"
                                    class="block w-full text-center px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark transition-colors"
                                >
                                    Voir détails
                                </Link>
                            </div>
                        </div>
                    </div>

                    <div v-else class="text-center py-8">
                        <p class="text-gray-600">Aucun produit similaire disponible.</p>
                    </div>
                </div>
            </div>

            <!-- CTA Section -->
            <div class="bg-primary-bg-light py-16">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <h2 class="text-3xl md:text-4xl font-serif font-bold text-primary mb-6">Besoin d'aide pour choisir ?</h2>
                    <p class="text-lg text-gray-700 max-w-3xl mx-auto mb-8">
                        Contactez-nous pour obtenir des conseils personnalisés sur nos produits et services.
                    </p>
                    <div class="flex flex-col sm:flex-row justify-center gap-4">
                        <Link
                            :href="route('appointment.create')"
                            class="px-8 py-3 bg-primary text-white rounded-full hover:bg-primary-dark transition-colors font-medium"
                        >
                            Prendre rendez-vous
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
        </template>
    </LayoutFront>
</template>

<style scoped>
/* Animation subtile au survol des cartes de produit */
.product-card:hover {
    transform: translateY(-5px);
}

/* Limiter le nombre de lignes pour la description */
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
