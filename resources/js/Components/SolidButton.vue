<script setup lang="ts">
import type { Component } from 'vue';

withDefaults(
  defineProps<{
    variant: string;
    as?: string | Component;
    href?: string;
  }>(),
  {
    as: 'button',
    href: '#',
  }
);
</script>

<template>
  <component
    :is="as"
    :href="href"
    class="rounded-md px-3 py-2 text-sm font-semibold text-white shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2"
    :class="`bg-${variant} hover:bg-${variant}-dark focus-visible:outline-${variant}-dark`"
    ><slot
  /></component>
</template>

<style scoped></style>
