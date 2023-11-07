<script setup lang="ts">
withDefaults(
  defineProps<{
    textColor?: string;
    textSize?: string;
  }>(),
  {
    textColor: 'text-front-light',
    textSize: 'text-xs',
  }
);
</script>

<template>
  <p :class="[textColor, textSize]" class="italic">
    <slot />
  </p>
</template>

<style scoped></style>
