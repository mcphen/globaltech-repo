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
        <img :src="'/images/logotongolo.jpg'" class="h-20 w-auto" alt="NG Consulting">

    </div>
</template>
