<template>
  <GenericLayout>
    <card>
      <DropZone @drop.prevent="drop" />
    </card>
    <div v-for="file in dropZoneFiles" :key="file.name">
      {{ file.name }}
    </div>
    <div id="canva"></div>
  </GenericLayout>
</template>

<script lang="ts" setup>
import axios from 'axios';
import DropZone from '@/Components/Photos/DropZone.vue';
import GenericLayout from '@/Layout/GenericLayout.vue';
import Card from '@/Components/CardComponent.vue';
import { ref } from 'vue';

let dropZoneFiles = ref('');
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
          //remove file from dropzone array
          console.log(response);
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
