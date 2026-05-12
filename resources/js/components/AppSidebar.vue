<script setup lang="ts">
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import {
    LayoutGrid,
    Briefcase,
    ShoppingCart,
    Users,
    MessageSquare,
    Newspaper,
    Image,
    Calendar,
    CalendarClock,
    Mail,
    Settings,
    GraduationCap,
    Award,
    BarChart2,
    Globe,
    Layers,
    BadgeCheck,
    Target,
    ChevronDown,
    ChevronUp,
    ThumbsUp
} from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

// Groupe principal - Tableau de bord
const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    }
];

// Groupe contenu et médias (items restants en dehors de "Gestion du site web")
const contentMediaItems: NavItem[] = [
];

// Groupe interactions et contacts (items restants en dehors de "Gestion du site web")
const interactionItems: NavItem[] = [
    {
        title: 'Contacts',
        href: route('admin.contacts.index'),
        icon: Mail,
    },
    /*{
        title: 'Prospects',
        href: route('admin.leads.index'),
        icon: Users,
    },
    {
        title: 'Abonnements',
        href: route('admin.subscribes.index'),
        icon: Users,
    },*/
];

// Nouveau groupe: Gestion du site web (replié par défaut)
const siteManagementItems: NavItem[] = [
    {
        title: 'Actualités',
        href: route('admin.actualites.index'),
        icon: Newspaper,
    },
    {
        title: 'Albums photos',
        href: route('admin.albums.index'),
        icon: Image,
    },
    {
        title: 'Témoignages',
        href: route('admin.testimonials.index'),
        icon: MessageSquare,
    },
    {
        title: 'À propos',
        href: route('admin.about.index'),
        icon: Newspaper,
    },
    {
        title: 'Partner',
        href: route('admin.partners.index'),
        icon: Users,
    },
    {
        title: 'Équipes',
        href: route('admin.team-members.index'),
        icon: Users,
    },
    {
        title: 'Pourquoi nous',
        href: route('admin.why-us.index'),
        icon: ThumbsUp,
    },
];

const page = usePage();

const isItemActive = (href: string): boolean => {
    try {
        const itemPath = new URL(href).pathname;
        return page.url === itemPath || page.url.startsWith(itemPath + '/');
    } catch {
        return page.url === href || page.url.startsWith(href + '/');
    }
};

const _showSiteManagement = ref(false);
const showSiteManagement = computed(() => _showSiteManagement.value || siteManagementItems.some(item => isItemActive(item.href)));

const _showConfiguration = ref(false);
const showConfiguration = computed(() => _showConfiguration.value || configurationItems.some(item => isItemActive(item.href)));

const _showServiceManagement = ref(false);
const showServiceManagement = computed(() => _showServiceManagement.value || serviceManagementItems.some(item => isItemActive(item.href)));

const _showProductManagement = ref(false);
const showProductManagement = computed(() => _showProductManagement.value || productManagementItems.some(item => isItemActive(item.href)));

const _showTrainingManagement = ref(false);
const showTrainingManagement = computed(() => _showTrainingManagement.value || trainingManagementItems.some(item => isItemActive(item.href)));

const _showMasterclassManagement = ref(false);
const showMasterclassManagement = computed(() => _showMasterclassManagement.value || masterclassItems.some(item => isItemActive(item.href)));

// Groupe Executive Masterclass
const masterclassItems: NavItem[] = [
    { title: 'Bannière & CTA',     href: route('admin.masterclass.page.edit'),               icon: Layers },
    { title: 'Domaines',           href: route('admin.masterclass.domaines.index'),           icon: Globe },
    { title: 'Formats',            href: route('admin.masterclass.formats.index'),            icon: Layers },
    { title: 'Partenaires',        href: route('admin.masterclass.partenaires.index'),        icon: Award },
    { title: 'Certifications',     href: route('admin.masterclass.certifications.index'),     icon: BadgeCheck },
    { title: 'Public cible',       href: route('admin.masterclass.public-cibles.index'),      icon: Target },
    { title: 'Statistiques clés',  href: route('admin.masterclass.stats.index'),              icon: BarChart2 },
];

// Groupe gestion des produits (replié par défaut)
const productManagementItems: NavItem[] = [
    {
        title: 'Produits',
        href: route('admin.products.index'),
        icon: ShoppingCart,
    },
    {
        title: 'Commandes',
        href: route('admin.orders.index'),
        icon: ShoppingCart,
    },
];

// Groupe gestion des services (replié par défaut)
const serviceManagementItems: NavItem[] = [
    {
        title: 'Services',
        href: route('admin.services.index'),
        icon: Briefcase,
    },
  
];

// Groupe gestion des formations (replié par défaut)
const trainingManagementItems: NavItem[] = [
    {
        title: 'Formations',
        href: route('admin.formations.index'),
        icon: GraduationCap,
    },
    {
        title: 'Catégories de formations',
        href: route('admin.formation-categories.index'),
        icon: Briefcase,
    },
];

// Groupe équipe et utilisateurs (hors "Équipes")
const teamItems: NavItem[] = [
    {
        title: 'Utilisateurs',
        href: route('admin.users.index'),
        icon: Users,
    },
];

// Groupe configuration
const configurationItems: NavItem[] = [
    {
        title: 'Configuration • Contact & Réseaux',
        href: route('admin.contact-settings'),
        icon: Settings,
    },
    {
        title: 'Configuration • SMTP',
        href: route('admin.mail-settings'),
        icon: Settings,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <!-- Tableau de bord -->
            <NavMain
                :items="mainNavItems"
                group-title="Principal"
            />

            <!-- Contenu et médias -->


            

            <!-- Gestion du site web (replié par défaut) -->
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton @click="_showSiteManagement = !showSiteManagement">
                        <span>Gestion du site web</span>
                        <component :is="showSiteManagement ? ChevronUp : ChevronDown" class="ml-auto h-4 w-4 opacity-70" />
                    </SidebarMenuButton>
                </SidebarMenuItem>
                <template v-if="showSiteManagement">
                    <SidebarMenuItem v-for="item in siteManagementItems" :key="item.title">
                        <SidebarMenuButton as-child :is-active="isItemActive(item.href)">
                            <Link :href="item.href">
                                <component :is="item.icon" class="mr-2 h-4 w-4" />
                                <span>{{ item.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </template>
            </SidebarMenu>

            <!-- Gestion des services (replié par défaut) -->
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton @click="_showServiceManagement = !showServiceManagement">
                        <span>Gestion des services</span>
                        <component :is="showServiceManagement ? ChevronUp : ChevronDown" class="ml-auto h-4 w-4 opacity-70" />
                    </SidebarMenuButton>
                </SidebarMenuItem>
                <template v-if="showServiceManagement">
                    <SidebarMenuItem v-for="item in serviceManagementItems" :key="item.title">
                        <SidebarMenuButton as-child :is-active="isItemActive(item.href)">
                            <Link :href="item.href">
                                <component :is="item.icon" class="mr-2 h-4 w-4" />
                                <span>{{ item.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </template>
            </SidebarMenu>

            <!-- Gestion des formations (replié par défaut) -->
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton @click="_showTrainingManagement = !showTrainingManagement">
                        <span>Gestion des formations</span>
                        <component :is="showTrainingManagement ? ChevronUp : ChevronDown" class="ml-auto h-4 w-4 opacity-70" />
                    </SidebarMenuButton>
                </SidebarMenuItem>
                <template v-if="showTrainingManagement">
                    <SidebarMenuItem v-for="item in trainingManagementItems" :key="item.title">
                        <SidebarMenuButton as-child :is-active="isItemActive(item.href)">
                            <Link :href="item.href">
                                <component :is="item.icon" class="mr-2 h-4 w-4" />
                                <span>{{ item.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </template>
            </SidebarMenu>

            <!-- Executive Masterclass (replié par défaut) -->
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton @click="_showMasterclassManagement = !showMasterclassManagement">
                        <Award class="mr-2 h-4 w-4" />
                        <span>Executive Masterclass</span>
                        <component :is="showMasterclassManagement ? ChevronUp : ChevronDown" class="ml-auto h-4 w-4 opacity-70" />
                    </SidebarMenuButton>
                </SidebarMenuItem>
                <template v-if="showMasterclassManagement">
                    <SidebarMenuItem v-for="item in masterclassItems" :key="item.title">
                        <SidebarMenuButton as-child :is-active="isItemActive(item.href)">
                            <Link :href="item.href">
                                <component :is="item.icon" class="mr-2 h-4 w-4" />
                                <span>{{ item.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </template>
            </SidebarMenu>

            <!-- Interactions -->
            <NavMain
                :items="interactionItems"
                group-title="Interactions"
            />

            <!-- Équipe -->
            <NavMain
                :items="teamItems"
                group-title="Équipe & Utilisateurs"
            />

            <!-- Configuration (replié par défaut) -->
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton @click="_showConfiguration = !showConfiguration">
                        <span>Configuration</span>
                        <component :is="showConfiguration ? ChevronUp : ChevronDown" class="ml-auto h-4 w-4 opacity-70" />
                    </SidebarMenuButton>
                </SidebarMenuItem>
                <template v-if="showConfiguration">
                    <SidebarMenuItem v-for="item in configurationItems" :key="item.title">
                        <SidebarMenuButton as-child :is-active="isItemActive(item.href)">
                            <Link :href="item.href">
                                <component :is="item.icon" class="mr-2 h-4 w-4" />
                                <span>{{ item.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </template>
            </SidebarMenu>
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
