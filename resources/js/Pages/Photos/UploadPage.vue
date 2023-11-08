<template>
  <GenericLayout>
    <card class="mb-3">
      <DropZone @drop.prevent="drop" />
    </card>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <a
        v-for="photo in allPhotos"
        :key="photo.id"
        :href="route('photo::view', { id: photo.id })"
        class="max-h-64 w-100 rounded-lg overflow-hidden"
      >
        <img class="object-cover object-center h-full w-full" :src="photo.medium_url" :alt="photo.name" />
      </a>
    </div>
    <div v-for="file in dropZoneFiles" :key="file.name">
      {{ file.name }}
    </div>
  </GenericLayout>
</template>

<script lang="ts" setup>
const props = defineProps<{
  photos: Array<Photo>;
}>();
import axios from 'axios';
import DropZone from '@/Components/Photos/DropZone.vue';
import GenericLayout from '@/Layout/GenericLayout.vue';
import Card from '@/Components/CardComponent.vue';
import { computed, ref } from 'vue';

let dropZoneFiles = ref('');
let uploadedUrls = ref([]);
const uploadedPhotos = ref([]);
const allPhotos = computed(() => {
  return [...uploadedPhotos.value, ...props.photos];
});
const drop = (event) => {
  dropZoneFiles.value = event.dataTransfer.files;
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
        axios.post(route('photo::admin::upload', { id: 1 }), data).then((response) => {
          uploadedPhotos.value.push(JSON.parse(response.data.photo));
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
