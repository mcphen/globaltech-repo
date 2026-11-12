<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref } from 'vue';
import HeroSlideView from './HeroSlideView.vue';
import type { HeroSlideData } from './heroSlide';

const props = withDefaults(defineProps<{
    slides: HeroSlideData[];
    interval?: number;
}>(), { interval: 6000 });

const current = ref(0);
const paused = ref(false);
let timer: ReturnType<typeof setInterval> | null = null;

function go(index: number) {
    const n = props.slides.length;
    current.value = ((index % n) + n) % n;
    restart();
}
const next = () => go(current.value + 1);
const prev = () => go(current.value - 1);

function start() {
    stop();
    if (props.slides.length < 2) return;
    timer = setInterval(() => { if (!paused.value) current.value = (current.value + 1) % props.slides.length; }, props.interval);
}
function stop() { if (timer) { clearInterval(timer); timer = null; } }
function restart() { start(); }

// Swipe mobile
let touchX = 0;
const onTouchStart = (e: TouchEvent) => { touchX = e.touches[0].clientX; };
const onTouchEnd = (e: TouchEvent) => {
    const dx = e.changedTouches[0].clientX - touchX;
    if (Math.abs(dx) > 50) (dx < 0 ? next : prev)();
};

onMounted(start);
onBeforeUnmount(stop);
</script>

<template>
    <section class="relative w-full overflow-hidden h-[80vh] min-h-[520px] lg:h-[calc(100vh-6rem)] lg:min-h-[600px]"
        style="background: #060C22;"
        @mouseenter="paused = true" @mouseleave="paused = false"
        @touchstart.passive="onTouchStart" @touchend.passive="onTouchEnd">

        <div v-for="(slide, i) in slides" :key="slide.id ?? i"
            class="absolute inset-0 transition-opacity duration-1000"
            :class="i === current ? 'opacity-100 z-10' : 'opacity-0 z-0 pointer-events-none'"
            :aria-hidden="i !== current">
            <HeroSlideView :slide="slide" :active="i === current" />
        </div>

        <template v-if="slides.length > 1">
            <button type="button" @click="prev" aria-label="Slide précédent"
                class="hidden sm:flex absolute left-4 top-1/2 -translate-y-1/2 z-20 w-12 h-12 items-center justify-center rounded-full border border-white/20 bg-black/20 text-white backdrop-blur-sm hover:bg-white/20 transition">
                <i class="bi bi-chevron-left text-xl"></i>
            </button>
            <button type="button" @click="next" aria-label="Slide suivant"
                class="hidden sm:flex absolute right-4 top-1/2 -translate-y-1/2 z-20 w-12 h-12 items-center justify-center rounded-full border border-white/20 bg-black/20 text-white backdrop-blur-sm hover:bg-white/20 transition">
                <i class="bi bi-chevron-right text-xl"></i>
            </button>

            <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-20 flex items-center gap-2">
                <button v-for="(s, i) in slides" :key="'dot-' + (s.id ?? i)" type="button" @click="go(i)"
                    :aria-label="`Aller au slide ${i + 1}`"
                    class="h-2 rounded-full transition-all duration-300"
                    :class="i === current ? 'w-8 bg-[#E63946]' : 'w-2 bg-white/50 hover:bg-white/80'"></button>
            </div>
        </template>
    </section>
</template>
