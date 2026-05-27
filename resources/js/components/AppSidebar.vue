<script setup lang="ts">
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import {
    LayoutGrid,
    Monitor,
    Info,
    GraduationCap,
    Layers,
    MessageSquare,
    Handshake,
    Newspaper,
    ThumbsUp,
    Users,
    Image,
    Award,
    Globe,
    BadgeCheck,
    Target,
    BarChart2,
    Mail,
    Briefcase,
    ShoppingCart,
    Settings,
    ChevronDown,
    ChevronUp,
} from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const page = usePage();

const isItemActive = (href: string): boolean => {
    try {
        const itemPath = new URL(href).pathname;
        return page.url === itemPath || page.url.startsWith(itemPath + '/');
    } catch {
        return page.url === href || page.url.startsWith(href + '/');
    }
};

// ── 1. PRINCIPAL ────────────────────────────────────────────────────────────
const mainNavItems: NavItem[] = [
    { title: 'Dashboard', href: '/dashboard', icon: LayoutGrid },
];

// ── 2. PAGES DU SITE (toujours visible) ────────────────────────────────────
const pagesItems: NavItem[] = [
    { title: 'Page d\'accueil', href: route('admin.home-page.edit'),    icon: Monitor },
    { title: 'Page À propos',   href: route('admin.about.index'),        icon: Info },
    { title: 'Page Consulting', href: route('admin.service-page.edit'), icon: Briefcase },
];

// ── 3. FORMATIONS ───────────────────────────────────────────────────────────
const formationItems: NavItem[] = [
    { title: 'Formations',            href: route('admin.formations.index'),          icon: GraduationCap },
    { title: 'Catégories',            href: route('admin.formation-categories.index'), icon: Layers },
];

// ── 4. CONTENU ÉDITORIAL ───────────────────────────────────────────────────
const contentItems: NavItem[] = [
    { title: 'Témoignages',   href: route('admin.testimonials.index'), icon: MessageSquare },
    { title: 'Partenaires',   href: route('admin.partners.index'),     icon: Handshake },
    { title: 'Actualités',    href: route('admin.actualites.index'),   icon: Newspaper },
    { title: 'Pourquoi nous', href: route('admin.why-us.index'),       icon: ThumbsUp },
    { title: 'Équipe',        href: route('admin.team-members.index'), icon: Users },
    { title: 'Albums photos', href: route('admin.albums.index'),       icon: Image },
];

// ── 5. EXECUTIVE MASTERCLASS ────────────────────────────────────────────────
const masterclassItems: NavItem[] = [
    { title: 'Bannière & CTA',    href: route('admin.masterclass.page.edit'),            icon: Layers },
    { title: 'Domaines',          href: route('admin.masterclass.domaines.index'),        icon: Globe },
    { title: 'Formats',           href: route('admin.masterclass.formats.index'),         icon: Layers },
    { title: 'Partenaires MC',    href: route('admin.masterclass.partenaires.index'),     icon: Award },
    { title: 'Certifications',    href: route('admin.masterclass.certifications.index'),  icon: BadgeCheck },
    { title: 'Public cible',      href: route('admin.masterclass.public-cibles.index'),   icon: Target },
    { title: 'Statistiques clés', href: route('admin.masterclass.stats.index'),           icon: BarChart2 },
];

// ── 6. OPÉRATIONNEL ─────────────────────────────────────────────────────────
const operationalItems: NavItem[] = [
    { title: 'Contacts',  href: route('admin.contacts.index'), icon: Mail },
    { title: 'Services',  href: route('admin.services.index'), icon: Briefcase },
    { title: 'Produits',  href: route('admin.products.index'), icon: ShoppingCart },
    { title: 'Commandes', href: route('admin.orders.index'),   icon: ShoppingCart },
];

// ── 7. ADMINISTRATION ────────────────────────────────────────────────────────
const adminItems: NavItem[] = [
    { title: 'Utilisateurs',          href: route('admin.users.index'),        icon: Users },
    { title: 'Config Contact & Réseaux', href: route('admin.contact-settings'), icon: Settings },
    { title: 'Config SMTP',           href: route('admin.mail-settings'),      icon: Settings },
];

// ── Collapsed state ──────────────────────────────────────────────────────────
const _showFormations    = ref(false);
const _showContent       = ref(false);
const _showMasterclass   = ref(false);
const _showOperational   = ref(false);
const _showAdmin         = ref(false);

const showFormations  = computed(() => _showFormations.value  || formationItems.some(i => isItemActive(i.href)));
const showContent     = computed(() => _showContent.value     || contentItems.some(i => isItemActive(i.href)));
const showMasterclass = computed(() => _showMasterclass.value || masterclassItems.some(i => isItemActive(i.href)));
const showOperational = computed(() => _showOperational.value || operationalItems.some(i => isItemActive(i.href)));
const showAdmin       = computed(() => _showAdmin.value       || adminItems.some(i => isItemActive(i.href)));
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

            <!-- ① Dashboard -->
            <NavMain :items="mainNavItems" group-title="Principal" />

            <!-- ② Pages du site — toujours visible, pas de toggle -->
            <NavMain :items="pagesItems" group-title="Pages du site" />

            <!-- ③ Formations -->
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton @click="_showFormations = !showFormations">
                        <GraduationCap class="mr-2 h-4 w-4" />
                        <span>Formations</span>
                        <component :is="showFormations ? ChevronUp : ChevronDown" class="ml-auto h-4 w-4 opacity-70" />
                    </SidebarMenuButton>
                </SidebarMenuItem>
                <template v-if="showFormations">
                    <SidebarMenuItem v-for="item in formationItems" :key="item.title">
                        <SidebarMenuButton as-child :is-active="isItemActive(item.href)">
                            <Link :href="item.href">
                                <component :is="item.icon" class="mr-2 h-4 w-4" />
                                <span>{{ item.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </template>
            </SidebarMenu>

            <!-- ④ Contenu éditorial -->
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton @click="_showContent = !showContent">
                        <Newspaper class="mr-2 h-4 w-4" />
                        <span>Contenu</span>
                        <component :is="showContent ? ChevronUp : ChevronDown" class="ml-auto h-4 w-4 opacity-70" />
                    </SidebarMenuButton>
                </SidebarMenuItem>
                <template v-if="showContent">
                    <SidebarMenuItem v-for="item in contentItems" :key="item.title">
                        <SidebarMenuButton as-child :is-active="isItemActive(item.href)">
                            <Link :href="item.href">
                                <component :is="item.icon" class="mr-2 h-4 w-4" />
                                <span>{{ item.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </template>
            </SidebarMenu>

            <!-- ⑤ Executive Masterclass -->
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton @click="_showMasterclass = !showMasterclass">
                        <Award class="mr-2 h-4 w-4" />
                        <span>Executive Masterclass</span>
                        <component :is="showMasterclass ? ChevronUp : ChevronDown" class="ml-auto h-4 w-4 opacity-70" />
                    </SidebarMenuButton>
                </SidebarMenuItem>
                <template v-if="showMasterclass">
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

            <!-- ⑥ Opérationnel -->
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton @click="_showOperational = !showOperational">
                        <Briefcase class="mr-2 h-4 w-4" />
                        <span>Opérationnel</span>
                        <component :is="showOperational ? ChevronUp : ChevronDown" class="ml-auto h-4 w-4 opacity-70" />
                    </SidebarMenuButton>
                </SidebarMenuItem>
                <template v-if="showOperational">
                    <SidebarMenuItem v-for="item in operationalItems" :key="item.title">
                        <SidebarMenuButton as-child :is-active="isItemActive(item.href)">
                            <Link :href="item.href">
                                <component :is="item.icon" class="mr-2 h-4 w-4" />
                                <span>{{ item.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </template>
            </SidebarMenu>

            <!-- ⑦ Administration -->
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton @click="_showAdmin = !showAdmin">
                        <Settings class="mr-2 h-4 w-4" />
                        <span>Administration</span>
                        <component :is="showAdmin ? ChevronUp : ChevronDown" class="ml-auto h-4 w-4 opacity-70" />
                    </SidebarMenuButton>
                </SidebarMenuItem>
                <template v-if="showAdmin">
                    <SidebarMenuItem v-for="item in adminItems" :key="item.title">
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
