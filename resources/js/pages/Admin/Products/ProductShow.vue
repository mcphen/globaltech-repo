<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';

// Type pour un produit
interface Product {
    id: number;
    title: string;
    description: string | null;
    price: number;
    image_path: string | null;
    created_at: string;
    updated_at: string;
    image_url?: string;
}

// Props
interface Props {
    product: Product;
}

const props = defineProps<Props>();

// Définition des fil d'Ariane
const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Produits', href: route('admin.products.index') },
    { title: props.product.title, href: '#' }
];

// Formatage du prix
function formatPrice(price: number): string {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(price);
}

// Formatage de la date
function formatDate(dateString: string): string {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(date);
}

// Obtenir l'URL de l'image
function getImageUrl(): string {
    return props.product.image_url || `/storage/${props.product.image_path}` || '/images/placeholder-product.jpg';
}

// Extraire le texte brut du HTML et le tronquer
function stripAndTruncateHtml(html: string | null): string {
    if (!html) return '';

    // Créer un élément temporaire pour extraire le texte
    const tempDiv = document.createElement("div");
    tempDiv.innerHTML = html;
    const text = tempDiv.textContent || tempDiv.innerText || "";

    return text;
}
</script>

<template>
    <Head :title="`Détails - ${product.title}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm">
            <!-- En-tête avec actions -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 border-b pb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">{{ product.title }}</h2>
                    <p class="text-gray-500 mt-1">
                        Détails complets du produit
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <Link
                        :href="route('admin.products.index')"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition flex items-center gap-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                        Retour
                    </Link>
                    <Link
                        :href="route('admin.products.edit', product.id)"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition flex items-center gap-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                        Modifier
                    </Link>
                </div>
            </div>

            <!-- Contenu principal -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Section image -->
                <div class="space-y-6">
                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Image du produit</h3>
                        <div class="aspect-square max-w-md mx-auto">
                            <img
                                :src="getImageUrl()"
                                :alt="product.title"
                                class="w-full h-full object-cover rounded-lg border border-gray-200 shadow-sm"
                            />
                        </div>
                    </div>
                </div>

                <!-- Section détails -->
                <div class="space-y-6">
                    <!-- Informations générales -->
                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Informations générales</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <span class="font-medium text-gray-700">Nom du produit :</span>
                                <span class="text-gray-900">{{ product.title }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <span class="font-medium text-gray-700">Prix :</span>
                                <span class="text-2xl font-bold text-blue-600">{{ formatPrice(product.price) }}</span>
                            </div>
                            <div class="flex justify-between items-start py-2">
                                <span class="font-medium text-gray-700">Description :</span>
                                <div class="text-gray-900 text-right max-w-md">
                                    <p v-if="product.description" class="whitespace-pre-line"> {{ stripAndTruncateHtml(product.description) }}</p>
                                    <p v-else class="text-gray-400 italic">Aucune description</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Métadonnées -->
                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Métadonnées</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <span class="font-medium text-gray-700">ID du produit :</span>
                                <span class="text-gray-900 font-mono">#{{ product.id }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <span class="font-medium text-gray-700">Date de création :</span>
                                <span class="text-gray-900">{{ formatDate(product.created_at) }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="font-medium text-gray-700">Dernière modification :</span>
                                <span class="text-gray-900">{{ formatDate(product.updated_at) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Statistiques (à compléter avec des données réelles) -->
                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Statistiques</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center p-4 bg-white rounded-lg border border-gray-200">
                                <div class="text-2xl font-bold text-blue-600">0</div>
                                <div class="text-sm text-gray-600">Commandes</div>
                            </div>
                            <div class="text-center p-4 bg-white rounded-lg border border-gray-200">
                                <div class="text-2xl font-bold text-green-600">0</div>
                                <div class="text-sm text-gray-600">Revenus générés</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center pt-6 border-t border-gray-200">
                <Link
                    :href="route('admin.products.edit', product.id)"
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center justify-center gap-2"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                    </svg>
                    Modifier ce produit
                </Link>
                <Link
                    :href="route('admin.products.index')"
                    class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition flex items-center justify-center gap-2"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Retour à la liste
                </Link>
            </div>
        </div>
    </AppLayout>
</template>