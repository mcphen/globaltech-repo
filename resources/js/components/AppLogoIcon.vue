<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <div class="flex items-center">
        <img :src="'/images/logo.jpg'" class="h-10 w-auto" alt="NG Consulting">
        <span class="ml-2 text-xl font-semibold text-primary-color">NG Consulting</span>
    </div>
</template>
