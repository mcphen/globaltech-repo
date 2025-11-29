<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItemType } from '@/types';
import { ref, computed, onMounted } from 'vue';

// Type pour un produit
interface Product {
    id: number;
    title: string;
    description: string | null;
    price: number;
    image_path: string | null;
    created_at: string;
    updated_at: string;
}

// Type pour pagination
interface Pagination {
    current_page: number;
    data: Product[];
    from: number;
    last_page: number;
    links: Array<{ url: string | null; label: string; active: boolean }>;
    per_page: number;
    to: number;
    total: number;
}

// Props
interface Props {
    products: Pagination;
    flash?: { success?: string };
}

const props = defineProps<Props>();

// Accéder aux produits
const productsList = computed(() => props.products.data);

// Définition des fil d'Ariane
const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Produits', href: route('admin.products.index') }
];

// État de confirmation de suppression
const productToDelete = ref<Product | null>(null);
const showDeleteModal = ref(false);

// Mode d'affichage (grille ou liste) - Liste par défaut
const viewMode = ref<'grid' | 'list'>('list');

// Statistiques
const stats = ref({
    totalProducts: 0,
   // totalOrders: 0,
    averagePrice: 0,
    topProduct: null as string | null
});

// Fonctions
function confirmDelete(product: Product) {
    productToDelete.value = product;
    showDeleteModal.value = true;
}

function cancelDelete() {
    productToDelete.value = null;
    showDeleteModal.value = false;
}

function deleteProduct() {
    if (productToDelete.value) {
        window.location.href = route('admin.products.destroy', productToDelete.value.id);
    }
    showDeleteModal.value = false;
}

// Formatage du prix
function formatPrice(price: number): string {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(price);
}

// Formatage de la date
function formatDate(dateString: string): string {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    }).format(date);
}

// Extraire le texte brut du HTML et le tronquer
// function stripAndTruncateHtml(html: string | null, maxLength: number = 100): string {
//     if (!html) return '';

//     // Créer un élément temporaire pour extraire le texte
//     const tempDiv = document.createElement("div");
//     tempDiv.innerHTML = html;
//     const text = tempDiv.textContent || tempDiv.innerText || "";

//     // Tronquer si nécessaire
//     if (text.length > maxLength) {
//         return text.substring(0, maxLength) + '...';
//     }

//     return text;
// }

// Calculer les statistiques
function calculateStats() {
    const totalProducts = props.products.total;
    const totalPrice = productsList.value.reduce((sum, product) => sum + Number(product.price), 0);
    const averagePrice = totalProducts > 0 ? totalPrice / totalProducts : 0;
    
    // Pour l'exemple, on simule des données de commandes
   
    const topProduct = productsList.value.length > 0 ? productsList.value[0].title : null;

    stats.value = {
        totalProducts,
        //totalOrders,
        averagePrice,
        topProduct
    };
}

onMounted(() => {
    calculateStats();
});
</script>

<template>
    <Head title="Produits" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 bg-white shadow-sm">
            <!-- En-tête avec titre et bouton d'ajout -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 border-b pb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Gestion des produits</h2>
                    <p class="text-gray-500 mt-1">
                        Gérez votre catalogue de produits et consultez les statistiques
                    </p>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Sélecteur de vue -->
                    <div class="flex bg-gray-100 rounded-md p-1">
                        <button
                            @click="viewMode = 'grid'"
                            :class="[
                                'px-3 py-1.5 rounded-md flex items-center gap-2 transition',
                                viewMode === 'grid'
                                    ? 'bg-white shadow-sm text-blue-600'
                                    : 'text-gray-600 hover:bg-gray-200'
                            ]"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            Grille
                        </button>
                        <button
                            @click="viewMode = 'list'"
                            :class="[
                                'px-3 py-1.5 rounded-md flex items-center gap-2 transition',
                                viewMode === 'list'
                                    ? 'bg-white shadow-sm text-blue-600'
                                    : 'text-gray-600 hover:bg-gray-200'
                            ]"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                            </svg>
                            Liste
                        </button>
                    </div>

                    <Link
                        :href="route('admin.products.create')"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition flex items-center gap-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Nouveau produit
                    </Link>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-blue-600">Total produits</p>
                            <p class="text-2xl font-bold text-blue-900">{{ stats.totalProducts }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-green-600">Commandes totales</p>
                            <p class="text-2xl font-bold text-green-900"></p>
                        </div>
                    </div>
                </div>

                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-purple-600">Prix moyen</p>
                            <p class="text-2xl font-bold text-purple-900">{{ formatPrice(stats.averagePrice) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-orange-100 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-orange-600">Produit populaire</p>
                            <p class="text-lg font-bold text-orange-900 truncate" :title="stats.topProduct || 'Aucun'">
                                {{ stats.topProduct || 'Aucun' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Message de succès -->
            <div v-if="props.flash?.success" class="bg-green-50 border-l-4 border-green-500 p-4 rounded-md mb-4 flex items-start">
                <svg class="h-6 w-6 text-green-500 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-green-700">{{ props.flash.success }}</p>
                </div>
            </div>

            <!-- Affichage en grille (cards) -->
            <div v-if="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <!-- Votre code d'affichage grille existant -->
            </div>

            <!-- Affichage en liste (table) - PAR DÉFAUT -->
            <div v-if="viewMode === 'list'" class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date de création</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="product in productsList" :key="product.id" class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <img
                                        v-if="product.image_path"
                                        :src="`/storage/${product.image_path}`"
                                        :alt="product.title"
                                        class="w-16 h-16 object-cover rounded-md border border-gray-200"
                                    >
                                    <div v-else class="w-16 h-16 bg-gray-100 flex items-center justify-center rounded-md border border-gray-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ product.title }}</div>
                            </td>
                       
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                                    {{ formatPrice(product.price) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ formatDate(product.created_at) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                <div class="flex justify-center gap-2">
                                    <Link
                                        :href="route('admin.products.edit', product.id)"
                                        class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 transition inline-flex items-center text-sm"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                        Modifier
                                    </Link>
                                    <Link
                                        :href="route('admin.products.show', product.id)"
                                        class="px-3 py-1.5 bg-green-50 text-green-700 rounded-md hover:bg-green-100 transition inline-flex items-center text-sm"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                        </svg>
                                        Détails
                                    </Link>
                                    <button
                                        @click="confirmDelete(product)"
                                        class="px-3 py-1.5 bg-red-50 text-red-700 rounded-md hover:bg-red-100 transition inline-flex items-center text-sm"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        Supprimer
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="productsList.length === 0">
                            <td colspan="6" class="px-6 py-10 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                <p class="text-gray-500">Aucun produit trouvé. Commencez par en ajouter un !</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex items-center justify-between border-t border-gray-200 px-4 py-3 sm:px-6">
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Affichage de <span class="font-medium">{{ props.products.from }}</span> à
                            <span class="font-medium">{{ props.products.to }}</span> sur
                            <span class="font-medium">{{ props.products.total }}</span> produits
                        </p>
                    </div>
                    <div>
                        <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                            <Link
                                v-for="(link, i) in props.products.links"
                                :key="i"
                                :href="link.url || '#'"
                                v-html="link.label"
                                :class="[
                                    'relative inline-flex items-center px-4 py-2 text-sm',
                                    link.url === null
                                        ? 'text-gray-300 cursor-not-allowed'
                                        : 'text-gray-500 hover:bg-gray-50',
                                    link.active ? 'bg-blue-50 text-blue-600 font-medium border-blue-500 z-10' : 'border-gray-300',
                                    i === 0 ? 'rounded-l-md' : '',
                                    i === props.products.links.length - 1 ? 'rounded-r-md' : '',
                                    'border'
                                ]"
                            />
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>

    <!-- Modal de confirmation de suppression -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
            <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 text-red-500 mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </div>
            <h3 class="text-xl font-semibold mb-2 text-center">Confirmer la suppression</h3>
            <p class="mb-6 text-gray-600 text-center">
                Êtes-vous sûr de vouloir supprimer le produit "<span class="font-medium">{{ productToDelete?.title }}</span>" ?
                <br>Cette action est irréversible.
            </p>
            <div class="flex gap-3 justify-center">
                <button
                    @click="cancelDelete"
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition flex-1 max-w-xs"
                >
                    Annuler
                </button>
                <button
                    @click="deleteProduct"
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition flex-1 max-w-xs"
                >
                    Supprimer
                </button>
            </div>
        </div>
    </div>
</template>