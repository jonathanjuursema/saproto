<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

withDefaults(
  defineProps<{
    href?: string;
    noInertia?: boolean;
  }>(),
  {
    href: '#',
  }
);
</script>

<template>
  <component
    :is="noInertia ? 'a' : Link"
    :href="href"
    class="flex items-center w-full hover:bg-back-light px-5 py-1 cursor-pointer whitespace-nowrap"
  >
    <slot />
  </component>
</template>

<style scoped></style>
