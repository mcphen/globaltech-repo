import { ref, computed, watch } from 'vue';

type Theme = 'system' | 'light' | 'dark';

// Singleton — shared across all component instances
const theme = ref<Theme>('system');
const isDark = ref(false);
// Theme imposed by the admin (site settings); overrides the visitor's choice
const forced = ref<'dark' | 'light' | null>(null);

function resolve() {
    const effective = forced.value ?? theme.value;
    if (effective === 'dark') isDark.value = true;
    else if (effective === 'light') isDark.value = false;
    else isDark.value = window.matchMedia('(prefers-color-scheme: dark)').matches;
    document.documentElement.classList.toggle('dark', isDark.value);
}

// Bootstrap once on import (client side)
if (typeof window !== 'undefined') {
    const saved = localStorage.getItem('gt-theme') as Theme | null;
    theme.value = saved ?? 'light';
    resolve();
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', resolve);
}

watch(theme, () => {
    localStorage.setItem('gt-theme', theme.value);
    resolve();
});

watch(forced, () => {
    if (typeof window !== 'undefined') resolve();
});

export function setForcedTheme(mode: 'dark' | 'light' | null) {
    forced.value = mode;
}

export function useDarkMode() {
    const cycle = () => {
        const order: Theme[] = ['system', 'light', 'dark'];
        theme.value = order[(order.indexOf(theme.value) + 1) % order.length];
    };

    const icon = computed(() => ({ system: 'bi-display', light: 'bi-sun-fill', dark: 'bi-moon-stars-fill' }[theme.value]));
    const label = computed(() => ({ system: 'Système', light: 'Clair', dark: 'Sombre' }[theme.value]));

    const isForced = computed(() => forced.value !== null);

    return { theme, isDark, isForced, cycle, icon, label };
}
