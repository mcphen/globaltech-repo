<script setup lang="ts">
import { useSiteSettings } from '@/composables/useSiteSettings';

const { faviconUrl } = useSiteSettings();

defineOptions({
  inheritAttrs: false,
});
</script>

<template>
  <img
    :src="faviconUrl"
    class="object-contain"
    v-bind="$attrs"
    alt="GlobalTECH EDUCATION Africa Logo"
  />
</template>
