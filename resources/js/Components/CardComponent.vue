<script setup lang="ts">
import { useSlots } from 'vue';

withDefaults(
  defineProps<{
    background?: string;
    headerBackground?: string;
  }>(),
  {
    background: 'bg-back',
    headerBackground: 'bg-back-dark',
  }
);

const slots = useSlots();
</script>

<template>
  <div :class="background" class="overflow-clip rounded-md shadow-lg ring-1 ring-front ring-opacity-5">
    <div v-if="slots.header" :class="headerBackground" class="text-front-dark px-4 py-2">
      <slot name="header" />
    </div>
    <div class="p-4">
      <slot />
    </div>
    <div v-if="slots.footer" :class="headerBackground" class="text-front-dark px-4 py-2">
      <slot name="footer" />
    </div>
  </div>
</template>

<style scoped></style>
