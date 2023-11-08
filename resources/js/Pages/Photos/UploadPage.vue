<template>
  <GenericLayout>
    <card class="mb-3">
      <DropZone @drop.prevent="drop" />
    </card>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <div class="grid gap-3">
        <div v-for="photo in photos.slice(0, photos.length / 4)" :key="photo.id">
          <img class="h-auto max-w-full rounded-lg" :src="photo.small_url" alt="unable to load Photo" />
        </div>
      </div>
      <div class="grid gap-3">
        <div v-for="photo in photos.slice(photos.length / 4, (photos.length / 4) * 2)" :key="photo.id">
          <img class="h-auto max-w-full rounded-lg" :src="photo.small_url" alt="" />
        </div>
      </div>
      <div class="grid gap-3">
        <div v-for="photo in photos.slice((photos.length / 4) * 2, (photos.length / 4) * 3)" :key="photo.id">
          <img class="h-auto max-w-full rounded-lg" :src="photo.small_url" alt="" />
        </div>
      </div>
      <div class="grid gap-3">
        <div v-for="photo in photos.slice((photos.length / 4) * 3, photos.length)" :key="photo.id">
          <img class="h-auto max-w-full rounded-lg" :src="photo.small_url" alt="" />
        </div>
      </div>
    </div>
    <div v-for="file in dropZoneFiles" :key="file.name">
      {{ file.name }}
    </div>
    <div v-for="url in uploadedUrls" :key="url">
      <!--      create img with url-->
      <div class="h-32" @click="clickMethod">
        <img class="h-auto max-w-full" :src="url" alt="An uploaded image" />
      </div>
    </div>
  </GenericLayout>
</template>

<script lang="ts" setup>
defineProps<{
  photos: Array<Photo>;
}>();
import axios from 'axios';
import DropZone from '@/Components/Photos/DropZone.vue';
import GenericLayout from '@/Layout/GenericLayout.vue';
import Card from '@/Components/CardComponent.vue';
import { ref } from 'vue';

let dropZoneFiles = ref('');
let uploadedUrls = ref([]);
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
          console.log(JSON.parse(response.data.photo));
          props.photos.unshift(JSON.parse(response.data.photo));
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

function clickMethod(event) {
  console.log(event);
}
</script>
