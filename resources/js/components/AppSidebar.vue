<script setup lang="ts">
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
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
    GraduationCap
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

// Groupe contenu et médias
const contentMediaItems: NavItem[] = [
    {
        title: 'Services',
        href: route('admin.services.index'),
        icon: Briefcase,
    },
    {
        title: 'Produits',
        href: route('admin.products.index'),
        icon: ShoppingCart,
    },
    {
        title: 'Actualités',
        href: route('admin.actualites.index'),
        icon: Newspaper,
    },
    {
        title: 'Formations',
        href: route('admin.formations.index'),
        icon: GraduationCap,
    },
    {
        title: 'Albums photos',
        href: route('admin.albums.index'),
        icon: Image,
    },
];

// Groupe interactions et contacts
const interactionItems: NavItem[] = [
    {
        title: 'Témoignages',
        href: route('admin.testimonials.index'),
        icon: MessageSquare,
    },
    {
        title: 'Contacts',
        href: route('admin.contacts.index'),
        icon: Mail,
    },
    {
        title: 'Prospects',
        href: route('admin.leads.index'),
        icon: Users,
    },

    {
        title: 'Partner',
        href: route('admin.partners.index'),
        icon: Users,
    },
];

// Groupe rendez-vous et planning
const schedulingItems: NavItem[] = [
    {
        title: 'Créneaux',
        href: route('admin.schedules.index'),
        icon: Calendar,
    },
    {
        title: 'Rendez-vous',
        href: route('admin.appointments.index'),
        icon: CalendarClock,
    },
];

// Groupe équipe et utilisateurs
const teamItems: NavItem[] = [
    {
        title: 'Équipe',
        href: route('admin.team-members.index'),
        icon: Users,
    },
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
            <NavMain 
                :items="contentMediaItems" 
                group-title="Contenu & Médias" 
            />
            
            <!-- Interactions -->
            <NavMain 
                :items="interactionItems" 
                group-title="Interactions" 
            />
            
            <!-- Planning -->
            <NavMain 
                :items="schedulingItems" 
                group-title="Planning & RDV" 
            />
            
            <!-- Équipe -->
            <NavMain 
                :items="teamItems" 
                group-title="Équipe & Utilisateurs" 
            />
            
            <!-- Configuration -->
            <NavMain 
                :items="configurationItems" 
                group-title="Configuration" 
            />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>