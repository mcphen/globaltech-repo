import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export interface PresenceCountry {
    flag: string;
    name: string;
}

export type NavKey = 'home' | 'about' | 'formations' | 'consulting' | 'masterclass' | 'blog' | 'contact';

export interface NavMenuItem {
    key: NavKey;
    label: string;
    visible: boolean;
}

export interface SiteSettings {
    contact_enabled: boolean;
    blog_enabled: boolean;
    theme_mode: 'user' | 'dark' | 'light';
    presence_countries: PresenceCountry[];
    nav_menu: NavMenuItem[];
}

// Site-wide front settings managed in Admin > Paramètres du site
export function useSiteSettings() {
    const page = usePage();
    const site = computed<SiteSettings>(() => ({
        contact_enabled: true,
        blog_enabled: true,
        theme_mode: 'user',
        presence_countries: [],
        nav_menu: [],
        ...((page.props as any).siteSettings ?? {}),
    }));

    return {
        site,
        contactEnabled: computed(() => site.value.contact_enabled),
        blogEnabled: computed(() => site.value.blog_enabled),
    };
}
