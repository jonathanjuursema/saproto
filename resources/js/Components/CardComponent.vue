<script setup lang="ts">
import { useSlots } from 'vue';

const slots = useSlots();
</script>

<template>
  <div class="bg-white overflow-clip text-black rounded-md shadow-lg ring-1 ring-black ring-opacity-5">
    <div v-if="slots.header" class="bg-dark text-white px-4 py-2">
      <slot name="header" />
    </div>
    <div class="p-4">
      <slot />
    </div>
    <div v-if="slots.footer" class="bg-dark text-white px-4 py-2">
      <slot name="footer" />
    </div>
  </div>
</template>

<style scoped></style>
