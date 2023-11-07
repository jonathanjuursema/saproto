<template>
  <div
    :class="{ 'active-dropzone': !isActive }"
    class="dropzone"
    @dragenter.prevent="toggleActive"
    @dragleave.prevent="toggleActive"
    @dragover.prevent
    @drop.prevent="toggleActive"
  >
    <span>Drag or drop file</span>
  </div>
</template>

<script lang="ts" setup>
import { ref } from 'vue';

const isActive = ref(false);

const toggleActive = () => {
  isActive.value = !isActive.value;
};
</script>
<style scoped>
.dropzone {
  border: 2px dashed #ccc;
  border-radius: 5px;
  padding: 25px;
  text-align: center;
  transition: all 0.3s ease;
}

.active-dropzone {
  border: 2px dashed #000;
}
</style>
