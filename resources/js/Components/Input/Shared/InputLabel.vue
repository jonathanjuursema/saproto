<script setup lang="ts">
defineOptions({
  inheritAttrs: false,
});
defineProps<{
  name?: string;
}>();
</script>

<template>
  <label class="block text-sm font-bold mb-2" :for="name">
    <slot />
  </label>
</template>

<style scoped></style>
