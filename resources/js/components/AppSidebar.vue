<script setup lang="ts">
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarGroupLabel,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    useSidebar,
} from '@/components/ui/sidebar';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, reactive, watch, type Component } from 'vue';
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
    Images,
    Award,
    Globe,
    BadgeCheck,
    Target,
    BarChart2,
    Mail,
    Briefcase,
    ShoppingCart,
    ShoppingBag,
    Package,
    Settings,
    ChevronRight,
    GalleryHorizontal,
    UserSearch,
    CalendarCheck,
    CalendarClock,
    AtSign,
    PanelTop,
    Palette,
    Share2,
    Send,
    FileText,
    Megaphone,
    Contact,
    SlidersHorizontal,
    ExternalLink,
} from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

interface MenuLink {
    title: string;
    href: string;
    icon: Component;
}

interface MenuGroup {
    id: string;
    title: string;
    icon: Component;
    items: MenuLink[];
}

interface MenuSection {
    label: string;
    groups: MenuGroup[];
}

// ── Structure du menu ───────────────────────────────────────────────────────
const dashboard: MenuLink = { title: 'Tableau de bord', href: route('dashboard'), icon: LayoutGrid };

const sections: MenuSection[] = [
    {
        label: 'Site web',
        groups: [
            {
                id: 'pages', title: 'Pages du site', icon: FileText, items: [
                    { title: "Page d'accueil",       href: route('admin.home-page.edit'),     icon: Monitor },
                    { title: "Slider d'accueil",     href: route('admin.hero-slides.index'),  icon: GalleryHorizontal },
                    { title: 'Page À propos',        href: route('admin.about.index'),        icon: Info },
                    { title: 'Bannières des pages',  href: route('admin.page-banners.index'), icon: PanelTop },
                ],
            },
            {
                id: 'formations', title: 'Formations', icon: GraduationCap, items: [
                    { title: 'Toutes les formations', href: route('admin.formations.index'),           icon: GraduationCap },
                    { title: 'Catégories',            href: route('admin.formation-categories.index'), icon: Layers },
                ],
            },
            {
                id: 'consulting', title: 'Consulting', icon: Briefcase, items: [
                    { title: 'Page Consulting',   href: route('admin.service-page.edit'), icon: Monitor },
                    { title: 'Services proposés', href: route('admin.services.index'),     icon: Briefcase },
                ],
            },
            {
                id: 'masterclass', title: 'Executive Masterclass', icon: Award, items: [
                    { title: 'Bannière & CTA',    href: route('admin.masterclass.page.edit'),           icon: PanelTop },
                    { title: 'Domaines',          href: route('admin.masterclass.domaines.index'),       icon: Globe },
                    { title: 'Formats',           href: route('admin.masterclass.formats.index'),        icon: Layers },
                    { title: 'Public cible',      href: route('admin.masterclass.public-cibles.index'),  icon: Target },
                    { title: 'Certifications',    href: route('admin.masterclass.certifications.index'), icon: BadgeCheck },
                    { title: 'Partenaires',       href: route('admin.masterclass.partenaires.index'),    icon: Handshake },
                    { title: 'Statistiques clés', href: route('admin.masterclass.stats.index'),          icon: BarChart2 },
                ],
            },
            {
                id: 'content', title: 'Contenu', icon: Megaphone, items: [
                    { title: 'Actualités',    href: route('admin.actualites.index'),   icon: Newspaper },
                    { title: 'Témoignages',   href: route('admin.testimonials.index'), icon: MessageSquare },
                    { title: 'Pourquoi nous', href: route('admin.why-us.index'),       icon: ThumbsUp },
                    { title: 'Équipe',        href: route('admin.team-members.index'), icon: Users },
                    { title: 'Partenaires',   href: route('admin.partners.index'),     icon: Handshake },
                ],
            },
            {
                id: 'media', title: 'Médias', icon: Images, items: [
                    { title: 'Albums photos',   href: route('admin.albums.index'), icon: Image },
                    { title: 'Bannière photos', href: route('admin.banner.index'), icon: Images },
                ],
            },
        ],
    },
    {
        label: 'Configuration',
        groups: [
            {
                id: 'settings', title: 'Paramètres', icon: Settings, items: [
                    { title: 'Paramètres du site', href: route('admin.site-settings'),    icon: SlidersHorizontal },
                    { title: 'Logo & favicon',     href: route('admin.branding.index'),   icon: Palette },
                    { title: 'Contact & réseaux',  href: route('admin.contact-settings'), icon: Share2 },
                    { title: 'E-mails (SMTP)',     href: route('admin.mail-settings'),    icon: Send },
                    { title: 'Utilisateurs',       href: route('admin.users.index'),      icon: Users },
                ],
            },
        ],
    },
];

// Section « Gestion » masquée à la demande du client (les pages restent
// accessibles par leur adresse). Pour la réafficher, l'ajouter à `sections`.
// eslint-disable-next-line @typescript-eslint/no-unused-vars
const hiddenSections: MenuSection[] = [
    {
        label: 'Gestion',
        groups: [
            {
                id: 'crm', title: 'Relation client', icon: Contact, items: [
                    { title: 'Messages reçus', href: route('admin.contacts.index'),     icon: Mail },
                    { title: 'Prospects',      href: route('admin.leads.index'),        icon: UserSearch },
                    { title: 'Rendez-vous',    href: route('admin.appointments.index'), icon: CalendarCheck },
                    { title: 'Créneaux',       href: route('admin.schedules.index'),    icon: CalendarClock },
                    { title: 'Newsletter',     href: route('admin.subscribes.index'),   icon: AtSign },
                ],
            },
            {
                id: 'shop', title: 'Boutique', icon: ShoppingBag, items: [
                    { title: 'Produits',  href: route('admin.products.index'), icon: Package },
                    { title: 'Commandes', href: route('admin.orders.index'),   icon: ShoppingCart },
                ],
            },
        ],
    },
];

// ── Page active : l'entrée dont l'adresse correspond le mieux à la page ─────
const page = usePage();
const pathOf = (href: string) => {
    try {
        return new URL(href, 'http://localhost').pathname.replace(/\/+$/, '') || '/';
    } catch {
        return href;
    }
};
const currentPath = computed(() => (page.url.split(/[?#]/)[0].replace(/\/+$/, '') || '/'));

const allLinks = [dashboard, ...sections.flatMap((s) => s.groups.flatMap((g) => g.items))];
void hiddenSections;
const activeHref = computed(() => {
    let best: string | null = null;
    let bestLen = -1;
    for (const link of allLinks) {
        const p = pathOf(link.href);
        if ((currentPath.value === p || currentPath.value.startsWith(p + '/')) && p.length > bestLen) {
            best = link.href;
            bestLen = p.length;
        }
    }
    return best;
});
const isActive = (item: MenuLink) => item.href === activeHref.value;
const groupHasActive = (g: MenuGroup) => g.items.some(isActive);

// ── Groupes ouverts (mémorisés dans le navigateur) ─────────────────────────
const STORAGE_KEY = 'admin_sidebar_open_groups';
const open = reactive<Record<string, boolean>>({});
try {
    if (typeof localStorage !== 'undefined') Object.assign(open, JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}'));
} catch {
    /* stockage indisponible : tout fermé */
}
const persist = () => {
    try {
        if (typeof localStorage !== 'undefined') localStorage.setItem(STORAGE_KEY, JSON.stringify(open));
    } catch {
        /* ignore */
    }
};

// Le groupe de la page courante est toujours ouvert
watch(
    activeHref,
    () => {
        for (const s of sections) for (const g of s.groups) if (groupHasActive(g)) open[g.id] = true;
        persist();
    },
    { immediate: true },
);

const { state, setOpen, isMobile } = useSidebar();
const toggle = (g: MenuGroup) => {
    // Menu réduit en icônes : on le déplie pour montrer le sous-menu
    if (state.value === 'collapsed' && !isMobile.value) {
        setOpen(true);
        open[g.id] = true;
    } else {
        open[g.id] = !open[g.id];
    }
    persist();
};
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

        <SidebarContent class="admin-nav-scroll gap-0 pb-4">
            <!-- Tableau de bord + lien vers le site -->
            <SidebarGroup class="pt-1 pb-0">
                <SidebarMenu class="gap-0.5">
                    <SidebarMenuItem>
                        <SidebarMenuButton as-child :tooltip="dashboard.title" class="admin-nav-link" :data-current="isActive(dashboard)">
                            <Link :href="dashboard.href">
                                <component :is="dashboard.icon" />
                                <span>{{ dashboard.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                    <SidebarMenuItem>
                        <SidebarMenuButton as-child tooltip="Voir le site" class="admin-nav-link">
                            <a href="/" target="_blank" rel="noopener">
                                <ExternalLink />
                                <span>Voir le site</span>
                            </a>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarGroup>

            <SidebarGroup v-for="section in sections" :key="section.label" class="pb-0">
                <SidebarGroupLabel class="admin-nav-section">{{ section.label }}</SidebarGroupLabel>
                <SidebarMenu class="gap-0.5">
                    <SidebarMenuItem v-for="g in section.groups" :key="g.id">
                        <!-- En-tête du groupe -->
                        <SidebarMenuButton
                            :tooltip="g.title"
                            class="admin-nav-group"
                            :data-has-current="groupHasActive(g)"
                            :aria-expanded="!!open[g.id]"
                            @click="toggle(g)"
                        >
                            <component :is="g.icon" />
                            <span>{{ g.title }}</span>
                            <ChevronRight class="admin-nav-chevron ml-auto" :class="{ 'rotate-90': open[g.id] }" />
                        </SidebarMenuButton>

                        <!-- Sous-menu -->
                        <div class="admin-nav-sub" :class="{ 'is-open': open[g.id] }">
                            <ul class="admin-nav-sub-inner">
                                <li v-for="item in g.items" :key="item.href">
                                    <Link :href="item.href" class="admin-nav-sublink" :data-current="isActive(item)"
                                        :aria-current="isActive(item) ? 'page' : undefined">
                                        <span>{{ item.title }}</span>
                                    </Link>
                                </li>
                            </ul>
                        </div>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarGroup>
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>

<style>
/* Barre de défilement discrète du menu */
.admin-nav-scroll {
    scrollbar-width: thin;
    scrollbar-color: hsl(228 30% 26%) transparent;
}
.admin-nav-scroll::-webkit-scrollbar { width: 6px; }
.admin-nav-scroll::-webkit-scrollbar-track { background: transparent; }
.admin-nav-scroll::-webkit-scrollbar-thumb { background: hsl(228 30% 26%); border-radius: 3px; }
.admin-nav-scroll::-webkit-scrollbar-thumb:hover { background: hsl(228 30% 34%); }

/* Libellés sur une seule ligne */
.admin-nav-link span,
.admin-nav-group span {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Libellés de section : SITE WEB, GESTION, CONFIGURATION */
.admin-nav-section {
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: hsl(220 15% 55%);
    margin-top: 0.5rem;
}

/* Liens de 1er niveau et en-têtes de groupe */
[data-sidebar] .admin-nav-link,
[data-sidebar] .admin-nav-group {
    color: hsl(220 15% 78%);
    font-weight: 500;
    transition: background-color 0.15s, color 0.15s;
}
[data-sidebar] .admin-nav-link:hover,
[data-sidebar] .admin-nav-group:hover {
    background: hsl(228 40% 18%);
    color: #fff;
}
[data-sidebar] .admin-nav-link svg,
[data-sidebar] .admin-nav-group svg {
    color: hsl(220 15% 60%);
}

/* Page active (1er niveau) */
[data-sidebar] .admin-nav-link[data-current='true'] {
    background: #E63946;
    color: #fff;
    font-weight: 600;
    box-shadow: 0 4px 14px -4px rgba(230, 57, 70, 0.55);
}
[data-sidebar] .admin-nav-link[data-current='true'] svg {
    color: #fff;
}

/* Groupe contenant la page active */
[data-sidebar] .admin-nav-group[data-has-current='true'] {
    color: #fff;
    font-weight: 600;
}
[data-sidebar] .admin-nav-group[data-has-current='true'] svg {
    color: #E63946;
}
[data-sidebar] .admin-nav-group[data-has-current='true'] .admin-nav-chevron {
    color: hsl(220 15% 70%);
}

.admin-nav-chevron {
    transition: transform 0.2s ease;
}

/* Sous-menu : ouverture animée */
.admin-nav-sub {
    display: grid;
    grid-template-rows: 0fr;
    transition: grid-template-rows 0.22s ease;
}
.admin-nav-sub.is-open {
    grid-template-rows: 1fr;
}
.admin-nav-sub-inner {
    overflow: hidden;
    margin-left: 1.05rem;
    padding-left: 0.6rem;
    border-left: 1px solid hsl(228 30% 22%);
}
.admin-nav-sub.is-open .admin-nav-sub-inner {
    padding-top: 0.15rem;
    padding-bottom: 0.35rem;
}
/* Masqué quand le menu est réduit en icônes */
[data-collapsible='icon'] .admin-nav-sub {
    display: none;
}

.admin-nav-sublink {
    position: relative;
    display: flex;
    align-items: center;
    min-height: 1.9rem;
    padding: 0.3rem 0.6rem;
    border-radius: 0.375rem;
    font-size: 0.8125rem;
    color: hsl(220 15% 68%);
    transition: background-color 0.15s, color 0.15s;
}
.admin-nav-sublink span {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.admin-nav-sublink:hover {
    background: hsl(228 40% 18%);
    color: #fff;
}
/* Page active dans un sous-menu : fond rouge léger + barre rouge sur la ligne */
.admin-nav-sublink[data-current='true'] {
    background: rgba(230, 57, 70, 0.16);
    color: #fff;
    font-weight: 600;
}
.admin-nav-sublink[data-current='true']::before {
    content: '';
    position: absolute;
    left: calc(-0.6rem - 1px);
    top: 0.3rem;
    bottom: 0.3rem;
    width: 2px;
    border-radius: 2px;
    background: #E63946;
}
</style>
