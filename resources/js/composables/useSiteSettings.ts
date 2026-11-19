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
    page_banners: Partial<Record<PageBannerKey, string | null>>;
    logo_url: string;
    favicon_url: string;
}

export type PageBannerKey = 'home' | 'about' | 'formations' | 'consulting' | 'masterclass' | 'portfolio' | 'blog' | 'contact';

// Dégradé bleu d'origine des bannières (utilisé quand aucune image n'est définie)
const DEFAULT_BANNER_BG =
    'background: linear-gradient(rgba(255,255,255,0.025) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.025) 1px,transparent 1px),linear-gradient(135deg,#060C22 0%,#0B1437 42%,#0E2060 72%,#091830 100%); background-size:60px 60px,60px 60px,100% 100%;';

// Site-wide front settings managed in Admin > Paramètres du site
export function useSiteSettings() {
    const page = usePage();
    const site = computed<SiteSettings>(() => ({
        contact_enabled: true,
        blog_enabled: true,
        theme_mode: 'user',
        presence_countries: [],
        nav_menu: [],
        page_banners: {},
        logo_url: '/images/logo_globaltech.png',
        favicon_url: '/images/favicon_globaltech.png',
        ...((page.props as any).siteSettings ?? {}),
    }));

    return {
        site,
        contactEnabled: computed(() => site.value.contact_enabled),
        blogEnabled: computed(() => site.value.blog_enabled),
        logoUrl: computed(() => site.value.logo_url),
        faviconUrl: computed(() => site.value.favicon_url),
    };
}

/**
 * Style de fond de la bannière d'une page : image choisie dans
 * Admin > Bannières des pages (avec un voile sombre pour garder le texte
 * lisible), sinon le dégradé bleu par défaut.
 */
export function usePageBanner(key: PageBannerKey, fallback: string = DEFAULT_BANNER_BG) {
    const { site } = useSiteSettings();
    return computed(() => {
        const path = site.value.page_banners?.[key];
        if (!path) return fallback;
        const url = `/storage/${path}`.replace(/"/g, '%22');
        return `background: linear-gradient(135deg, rgba(6,12,34,0.88) 0%, rgba(11,20,55,0.72) 50%, rgba(9,24,48,0.55) 100%), url("${url}") center / cover no-repeat;`;
    });
}
