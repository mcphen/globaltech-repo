<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import type { HeroSlideData } from './heroSlide';


const props = withDefaults(defineProps<{
    slide: HeroSlideData;
    /** Mode aperçu admin : tailles réduites, liens désactivés */
    preview?: boolean;
    /** Slide visible (déclenche l'animation du texte) */
    active?: boolean;
}>(), { preview: false, active: true });

const position = computed(() => props.slide.text_position || 'left');

const wrapperAlign = computed(() => ({
    left: 'mr-auto text-left items-start',
    center: 'mx-auto text-center items-center',
    right: 'ml-auto text-right items-end',
}[position.value] ?? 'mr-auto text-left items-start'));

const overlay = computed(() => {
    const o = Math.min(90, Math.max(0, props.slide.overlay_opacity ?? 50)) / 100;
    // Dégradé orienté vers le texte pour garder la lisibilité
    const dir = position.value === 'right' ? '270deg' : position.value === 'center' ? '0deg' : '90deg';
    if (position.value === 'center') return `rgba(6,12,34,${o})`;
    return `linear-gradient(${dir}, rgba(6,12,34,${Math.min(1, o + 0.15)}) 0%, rgba(6,12,34,${o}) 45%, rgba(6,12,34,${o * 0.4}) 100%)`;
});

const hasContent = computed(() =>
    !!(props.slide.title || props.slide.subtitle || props.slide.text || props.slide.button1_label || props.slide.button2_label)
);

const isExternal = (url?: string | null) => !!url && /^(https?:)?\/\//i.test(url);
</script>

<template>
    <div class="absolute inset-0 overflow-hidden">
        <!-- Fond -->
        <div v-if="slide.image_url"
            class="absolute inset-0 bg-cover bg-center transition-transform ease-out"
            :class="[active && !preview ? 'scale-105' : 'scale-100']"
            :style="`background-image: url('${slide.image_url}'); transition-duration: 7000ms;`"></div>
        <div v-else class="absolute inset-0"
            style="background: linear-gradient(rgba(255,255,255,0.025) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.025) 1px,transparent 1px),linear-gradient(135deg,#060C22 0%,#0B1437 42%,#0E2060 72%,#091830 100%); background-size:60px 60px,60px 60px,100% 100%;"></div>

        <!-- Voile -->
        <div v-if="slide.image_url" class="absolute inset-0" :style="`background: ${overlay};`"></div>

        <!-- Contenu -->
        <div v-if="hasContent" class="relative z-10 h-full flex items-center">
            <div :class="preview ? 'w-full px-6' : 'max-w-7xl w-full mx-auto px-6 lg:px-8'">
                <div class="flex flex-col max-w-2xl transition-all duration-700"
                    :class="[wrapperAlign, active ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6']">

                    <p v-if="slide.subtitle"
                        class="font-bold tracking-widest uppercase"
                        :class="preview ? 'text-[10px] mb-2' : 'text-xs sm:text-sm mb-4'"
                        style="color: #FF6B75;">
                        {{ slide.subtitle }}
                    </p>

                    <h1 v-if="slide.title"
                        class="font-black leading-tight tracking-tight text-white"
                        :class="preview ? 'text-xl mb-2' : 'text-4xl sm:text-5xl lg:text-6xl mb-6'"
                        style="font-family: 'Plus Jakarta Sans', sans-serif;">
                        {{ slide.title }}
                    </h1>

                    <p v-if="slide.text"
                        class="text-white/80 leading-relaxed whitespace-pre-line"
                        :class="preview ? 'text-xs mb-3' : 'text-base sm:text-lg mb-8'">
                        {{ slide.text }}
                    </p>

                    <div v-if="slide.button1_label || slide.button2_label"
                        class="flex flex-wrap gap-3"
                        :class="{ 'justify-center': position === 'center', 'justify-end': position === 'right' }">
                        <template v-if="slide.button1_label">
                            <span v-if="preview || !slide.button1_url" class="gt-btn-gold rounded-xl"
                                :class="preview ? '!px-3 !py-1.5 !text-[11px]' : 'text-base px-7 py-3.5'">{{ slide.button1_label }}</span>
                            <a v-else-if="isExternal(slide.button1_url)" :href="slide.button1_url!" target="_blank" rel="noopener"
                                class="gt-btn-gold text-base px-7 py-3.5 rounded-xl">{{ slide.button1_label }}</a>
                            <Link v-else :href="slide.button1_url!" class="gt-btn-gold text-base px-7 py-3.5 rounded-xl">{{ slide.button1_label }}</Link>
                        </template>
                        <template v-if="slide.button2_label">
                            <span v-if="preview || !slide.button2_url" class="gt-btn-outline rounded-xl"
                                :class="preview ? '!px-3 !py-1.5 !text-[11px]' : 'text-base px-7 py-3.5'">{{ slide.button2_label }}</span>
                            <a v-else-if="isExternal(slide.button2_url)" :href="slide.button2_url!" target="_blank" rel="noopener"
                                class="gt-btn-outline text-base px-7 py-3.5 rounded-xl">{{ slide.button2_label }}</a>
                            <Link v-else :href="slide.button2_url!" class="gt-btn-outline text-base px-7 py-3.5 rounded-xl">{{ slide.button2_label }}</Link>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
