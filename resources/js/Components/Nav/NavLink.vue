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
    class="my-auto opacity-75 hover:opacity-100 cursor-pointer whitespace-nowrap"
    :href="href"
    ><slot
  /></component>
</template>

<style scoped></style>
