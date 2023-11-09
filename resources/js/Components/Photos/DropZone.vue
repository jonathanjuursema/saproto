<template>
  <div
    :class="{ 'active-dropzone': !isActive }"
    class="dropzone"
    @dragenter.prevent="toggleActive"
    @dragleave.prevent="toggleActive"
    @dragover.prevent
    @drop.prevent="drop"
  >
    <span>Drag or drop file</span>
    <div v-for="file in dropZoneFiles" :key="file.name">
      {{ file.name }}
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref } from 'vue';
import axios from 'axios';
import { InertiaLinkProps } from '@inertiajs/vue3';

const isActive = ref(false);
let dropZoneFiles = ref('');
let uploadedUrls = ref([]);
const toggleActive = () => {
  isActive.value = !isActive.value;
};

const props = defineProps<{
  albumId: number;
}>();

const drop = (event) => {
  toggleActive();
  dropZoneFiles.value = event.dataTransfer.files;
  console.log(dropZoneFiles.value);
  Array.from(event.dataTransfer.files).forEach((file, index) => {
    const image = new Image();
    image.src = URL.createObjectURL(file);
    image.onload = () => {
      const sizes = [50, 420, 750, 1080];
      let promises = Array.from(sizes, function (width) {
        let canvas = createCanvas(image, width);
        return new Promise(function (resolve) {
          canvas.toBlob((blob) => resolve({ size: width, blob: blob, index: index }), 'image/jpeg', 0.75);
        });
      });

      Promise.all(promises).then((values) => {
        let data = new FormData();
        data.append('original', file);
        values.forEach((value) => {
          data.append(value.size, value.blob);
        });
        axios.post(route('photo::admin::upload', { id: props.albumId }), data).then((response) => {
          // uploadedPhotos.value.push(JSON.parse(response.data.photo));
          console.log(response);
          uploadedUrls.value.push(response.data.url);
        });
      });
    };
  });
};

function createCanvas(image, maxWidth, maxHeight) {
  const [newWidth, newHeight] = calculateSize(image, maxWidth, maxHeight);
  const canvas = document.createElement('canvas');
  canvas.width = newWidth;
  canvas.height = newHeight;
  const ctx = canvas.getContext('2d');
  ctx.drawImage(image, 0, 0, newWidth, newHeight);
  return canvas;
}

function calculateSize(img, longestSide) {
  let width = img.width;
  let height = img.height;
  let ratio = width / height;

  // if img is wider than height
  if (ratio > 1 && width > longestSide) {
    width = longestSide;
    height = longestSide / ratio;
  } else if (height > longestSide) {
    height = longestSide;
    width = longestSide * ratio;
  }
  return [Math.round(width), Math.round(height)];
}
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
