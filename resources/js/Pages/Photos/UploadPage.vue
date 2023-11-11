<template>
  <AdminLayout>
    <template #left-bar>
      <card class="mb-3">
        <DropZone v-if="!album.published" :album-id="props.album.id" @uploaded="addPhoto" @uploading="uploadSwitch" />
        <div v-else class="text-center">
          <div class="text-2xl">Album is published</div>
          <div class="text-sm">You can't upload photos to a published album</div>
        </div>
      </card>

      <card class="mb-3">
        <div class="grid grid-cols-1 md:grid-cols-3 justify-content-between gap-4">
          <solid-button :disabled="uploading" variant="danger" @click="action('remove')"> Remove</solid-button>
          <solid-button :disabled="uploading" variant="info" @click="action('thumbnail')"> Set thumbnail</solid-button>
          <solid-button :disabled="uploading" variant="warning" @click="action('private')">
            Toggle private
          </solid-button>
        </div>
      </card>
      <card class="mb-3">
        <template #header>
          <div class="text-center">Thumbnail</div>
        </template>
        <div class="grid grid-cols-1 justify-items-center">
          <img
            v-if="props.thumbnailUrl"
            class="inline rounded-lg h-20 object-cover object-center border-2 border-white"
            :src="props.thumbnailUrl"
            alt="thumbnail"
          />
          <div v-else class="text-center">
            <div class="text-2xl">No thumbnail</div>
            <div class="text-sm">Set a thumbnail by clicking on the photo and then clicking on set thumbnail</div>
          </div>
        </div>
      </card>
    </template>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <a
        v-for="photo in allPhotos"
        :key="photo.id"
        :href="route('photo::view', { id: photo.id })"
        class="h-48 w-100 rounded-lg overflow-hidden"
        :class="selectedPhotos.includes(photo.id) ? 'border-4 border-opacity-1 border-primary' : ''"
        :style="{
          backgroundImage: 'url(' + photo.small_url + ')',
          backgroundSize: 'cover',
          backgroundPosition: 'center',
        }"
        @click.prevent
        @click="clicked(photo.id)"
      >
      </a>
    </div>
  </AdminLayout>
</template>

<script lang="ts" setup>
import Card from '@/Components/CardComponent.vue';
import DropZone from '@/Components/Photos/DropZone.vue';
import { computed, ref } from 'vue';
import axios from 'axios';
import SolidButton from '@/Components/SolidButton.vue';
import AdminLayout from '@/Layout/AdminLayout.vue';
import { router } from '@inertiajs/vue3';

const uploading = ref(false);
const selectedPhotos = ref([]);
const uploadedPhotos = ref([]);
const props = defineProps<{
  photos: Photo[];
  album: PhotoAlbum;
  thumbnailUrl?: string;
}>();

const allPhotos = computed(() => {
  return props.photos.concat(uploadedPhotos.value);
});

function action(action: string) {
  axios
    .post(route('photo::admin::action', { id: props.album.id }), {
      photos: selectedPhotos.value,
      action: action,
    })
    .then(() => {
      router.reload();
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
  if (event.shiftKey) {
    //todo: still need to not be able to let the id be multiple times in the array
    if (event.key === 'ArrowRight') {
      event.preventDefault();
      const highestIndex = props.photos.findIndex(
        (photo: Photo) => photo.id === selectedPhotos.value[selectedPhotos.value.length - 1]
      );
      //get the index of the photo in the props that has an index higher than the highest index and is not already selected
      // const nextPhotoIndex = props.photos.findIndex(
      //   (photo: Photo) => photo.index > highestIndex && !selectedPhotos.value.includes(photo.id)
      // );
      console.log(nextPhotoIndex);
      selectedPhotos.value.push(props.photos[highestIndex + 1].id);
    }

    if (event.key === 'ArrowLeft') {
      event.preventDefault();
      const lastSelectedPhotoIndex = props.photos.findIndex(
        (photo: Photo) => photo.id === selectedPhotos.value[selectedPhotos.value.length - 1]
      );
      console.log(lastSelectedPhotoIndex);
      console.log(selectedPhotos.value);
      selectedPhotos.value.push(props.photos[lastSelectedPhotoIndex - 1].id);
    }
  }
});

function clicked(id: number) {
  selectedPhotos.value.includes(id)
    ? selectedPhotos.value.splice(selectedPhotos.value.indexOf(id), 1)
    : selectedPhotos.value.push(id);
}

function uploadSwitch(newUploading: boolean) {
  uploading.value = newUploading;
}

function addPhoto(photo: Photo) {
  uploadedPhotos.value.push(photo);
}
</script>
