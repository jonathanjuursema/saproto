<script setup>
defineProps({
  active: Boolean,
});
</script>

<template>
  <button
    class="block w-full rounded whitespace-nowrap px-4 py-2 transition"
    :class="active ? 'bg-primary text-white' : 'bg-back-light hover:bg-primary hover:text-white'"
  >
    <slot />
  </button>
</template>

<style scoped></style>
