<script setup lang="ts">
withDefaults(
  defineProps<{
    color?: string;
  }>(),
  {
    color: 'gray-500',
  }
);
</script>

<template>
  <p :class="`text-${color}`" class="text-xs italic">
    <slot />
  </p>
</template>

<style scoped></style>
