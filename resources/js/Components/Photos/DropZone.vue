<template>
  <div
    :class="{
      'border-gray-400': !isActive && !uploading,
      'border-gray-200': isActive && !uploading,
      'border-warning cursor-wait': uploading,
    }"
    class="dropzone border-2 border-dashed rounded-lg p-4 text-center"
    @dragenter.prevent="toggleActive"
    @dragleave.prevent="toggleActive"
    @dragover.prevent
    @drop.prevent="drop"
  >
    <span v-if="!uploading">Drag or drop file</span>
    <span v-else>Uploading...</span>
    <div v-for="file in dropZoneFiles" :key="file.name" class="text-grey">
      {{ file.name }}
    </div>
  </div>

  <div v-if="errors.length > 0" class="border-error border-2">
    <div v-for="error in errors" :key="error" class="text-red-500">
      {{ error }}
    </div>
  </div>
</template>

<script lang="ts" setup>
import { computed, ref } from 'vue';
import axios from 'axios';

const isActive = ref(false);
let dropZoneFiles = ref([]);
let errors = ref([]);
const toggleActive = () => {
  isActive.value = !isActive.value;
};

const uploading = computed(() => {
  return dropZoneFiles.value.length > 0;
});

const props = defineProps<{
  albumId: number;
}>();

const emit = defineEmits<{
  uploaded: (photo: Photo) => void;
  uploading: (uploading: boolean) => void;
}>();

const drop = (event) => {
  toggleActive();
  emit('uploading', true);
  Array.from(event.dataTransfer.files).forEach((file, index) => {
    dropZoneFiles.value.push({ index: index, name: file.name });
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
          if (value.size === 1080) {
            let image = document.createElement('image');
            //set the src of the image to the blob url
            image.src = URL.createObjectURL(value.blob);
          }
        });
        axios
          .post(route('photo::admin::upload', { id: props.albumId }), data)
          .then((response) => {
            emit('uploaded', JSON.parse(response.data.photo));
            dropZoneFiles.value = dropZoneFiles.value.filter((file) => file.index !== values[0].index);
            if (!uploading.value) {
              emit('uploading', false);
            }
          })
          .catch(function (error) {
            dropZoneFiles.value = dropZoneFiles.value.filter((file) => file.index !== values[0].index);
            errors.value.push(image.name + ' - ' + error.message);
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
