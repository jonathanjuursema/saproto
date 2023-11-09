<template>
  <GenericLayout class="w-100">
    <div class="flex flex-column justify-content-around gap-3">
      <card class="mb-3 w-100">
        <DropZone :album-id="props.album.id" />
      </card>
      <card v-if="props.thumbnail_url" class="mb-3 align-content-center">
        <img class="h-20 object-cover object-center" :src="props.thumbnail_url" alt="thumbnail" />
        Thumbnail
      </card>
    </div>

    <card class="mb-3">
      <solid-button variant="danger" @click="action('remove')"> Remove</solid-button>
      <solid-button variant="info" @click="action('thumbnail')"> Set thumbnail</solid-button>
      <solid-button variant="warning" @click="action('private')"> Toggle private</solid-button>
    </card>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <a
        v-for="photo in photos"
        :key="photo.id"
        :href="route('photo::view', { id: photo.id })"
        class="h-48 w-100 rounded-lg overflow-hidden"
        :class="selectedPhotos.includes(photo.id) ? 'border-4 border-opacity-0.5 border-primary' : ''"
        :style="{
          backgroundImage: 'url(' + photo.small_url + ')',
          backgroundSize: 'cover',
          backgroundPosition: 'center',
        }"
        @click.prevent
        @click="clicked(photo.id)"
      >
        <!--        <img class="object-cover object-center h-full w-full" :src="photo.medium_url" :alt="photo.name" />-->
      </a>
    </div>
  </GenericLayout>
</template>

<script lang="ts" setup>
import GenericLayout from '@/Layout/GenericLayout.vue';
import Card from '@/Components/CardComponent.vue';
import DropZone from '@/Components/Photos/DropZone.vue';
import type { InertiaLinkProps } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';
import SolidButton from '@/Components/SolidButton.vue';
import { router } from '@inertiajs/vue3';

const selectedPhotos = ref([]);
const props = defineProps<{
  photos: InertiaLinkProps;
  album: PhotoAlbum;
  thumbnail_url: string;
}>();

console.log(props.thumbnail_url);

function action(action: string) {
  axios
    .post(route('photo::admin::action', { id: props.album.id }), {
      photos: selectedPhotos.value,
      action: action,
    })
    .then(() => {
      router.get(route('photo::admin::edit', { id: props.album.id }));
    });
}

document.addEventListener('keydown', function (event) {
  if (event.ctrlKey) {
    if (event.key === 'a') {
      event.preventDefault();
      selectedPhotos.value = props.photos.map((photo: Photo) => photo.id);
    }
    if (event.key === 'z') {
      event.preventDefault();
      selectedPhotos.value = [];
    }
  }
  // if (event.shiftKey) {
  //   //todo: still need to not be able to let the id be multiple times in the array
  //   if (event.key === 'ArrowRight') {
  //     event.preventDefault();
  //     const lastSelectedPhotoIndex = props.photos.findIndex(
  //       (photo: Photo) => photo.id === selectedPhotos.value[selectedPhotos.value.length - 1]
  //     );
  //     selectedPhotos.value.push(props.photos[lastSelectedPhotoIndex + 1].id);
  //   }
  //
  //   if (event.key === 'ArrowRight') {
  //     event.preventDefault();
  //     const lastSelectedPhotoIndex = props.photos.findIndex(
  //       (photo: Photo) => photo.id === selectedPhotos.value[selectedPhotos.value.length - 1]
  //     );
  //     selectedPhotos.value.push(props.photos[lastSelectedPhotoIndex - 1].id);
  //   }
  //   console.log(selectedPhotos.value);
  // }
});

function clicked(id: number) {
  selectedPhotos.value.includes(id)
    ? selectedPhotos.value.splice(selectedPhotos.value.indexOf(id), 1)
    : selectedPhotos.value.push(id);
}
</script>
