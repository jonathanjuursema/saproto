<template>
  <AdminLayout :class="uploading ? 'cursor-wait' : ''">
    <template #left-bar>
      <card class="mb-3">
        <template #header>
          <div class="flex justify-between">
            <p class="text-center">Edit album</p>
            <SolidButton
              type="submit"
              variant="info"
              :disabled="form.processing"
              @click="router.get(route('photo::album::list', props.album.id))"
              >Preview
            </SolidButton>
          </div>
        </template>
        <form @submit.prevent="submitForm">
          <InputGroup>
            <template #input>
              Album name
              <InputField v-model="form.name" :value="album.name" :place-holder="album.name" />
              Album date
              <vue-tailwind-datepicker
                v-model="form.date_taken"
                :placeholder="moment(album.date_taken).format('yyyy-MM-DD HH:mm:ss')"
                as-single
                text-input
                class="text-dark mb-3"
              />

              <InputField v-model="form.private" :type="InputType.Checkbox"> Private album</InputField>
              <solid-button variant="primary" type="submit">Save</solid-button>
            </template>
            <template #error>{{ formerror }}</template>
          </InputGroup>
        </form>
      </card>

      <card class="mb-3">
        <DropZone v-if="!album.published" :album-id="props.album.id" @uploaded="addPhoto" @uploading="uploadSwitch" />
        <div v-else class="text-center">
          <div class="text-2xl">Album is published</div>
          <div class="text-sm">You can't upload photos to a published album</div>
        </div>
      </card>

      <card class="mb-3">
        <div class="grid grid-cols-2 md:grid-cols-4 justify-content-between gap-4">
          <solid-button :disabled="uploading" variant="danger" @click="action('remove')">Remove photos</solid-button>
          <solid-button :disabled="uploading" variant="info" @click="action('thumbnail')">Set thumbnail</solid-button>
          <solid-button :disabled="uploading" variant="warning" @click="action('private')">
            Toggle private
          </solid-button>
          <solid-button :disabled="uploading" variant="primary" @click="togglePublished">
            {{ album.published ? 'Unpublish album' : 'Publish album' }}
          </solid-button>
        </div>
      </card>
      <card class="mb-3">
        <template #header>
          <div class="text-center">Thumbnail</div>
        </template>
        <div class="grid grid-cols-1 justify-items-center">
          <div v-if="props.thumbnail" class="w-3/4">
            <PhotoCard class="cursor-default" :photo="props.thumbnail" @click.prevent />
          </div>
          <div v-else class="text-center">
            <div class="text-2xl">No thumbnail</div>
            <div class="text-sm">Set a thumbnail by clicking on the photo and then clicking on set thumbnail</div>
          </div>
        </div>
      </card>
    </template>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
      <PhotoCard
        v-for="photo in allPhotos"
        :key="photo.id"
        :photo="photo"
        :selected="selectedPhotos.includes(photo.id)"
        @click.prevent="clicked(photo.id)"
      />
    </div>
  </AdminLayout>
</template>

<script lang="ts" setup>
import Card from '@/Components/CardComponent.vue';
import DropZone from '@/Components/Photos/DropZone.vue';
import { computed, onUnmounted, Ref, ref } from 'vue';
import axios from 'axios';
import SolidButton from '@/Components/SolidButton.vue';
import AdminLayout from '@/Layout/AdminLayout.vue';
import { router, useForm } from '@inertiajs/vue3';
import PhotoCard from '@/Components/Photos/PhotoCard.vue';
import moment from 'moment';
import VueTailwindDatepicker from 'vue-tailwind-datepicker';
import InputField from '@/Components/Input/InputField.vue';
import InputGroup from '@/Components/Input/InputGroup.vue';
import InputType from '@/types/InputType.d.ts';
import NavLink from '@/Components/Nav/NavLink.vue';

const uploading = ref(false);
const selectedPhotos = ref([]);
const uploadedPhotos = ref([]);
const formerror: Ref<string> = ref('');
const props = defineProps<{
  photos: Photo[];
  album: PhotoAlbum;
  thumbnail?: Photo;
}>();

onUnmounted(
  router.on('before', (event) => {
    if (uploading.value) {
      return confirm('You are still uploading photos, are you sure you want to leave?');
    }
  })
);

const form = useForm({
  name: props.album.name,
  date_taken: moment(props.album.date_taken).format('YYYY/MM/DD'),
  private: props.album.private,
});

function submitForm() {
  axios
    .post(route('photo::admin::edit', { id: props.album.id }), form.data())
    .then((response) => {
      if (!uploading.value) {
        router.reload();
      }
    })
    .catch((error) => {
      formerror.value = error.message;
      console.log(error);
    });
}

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

function togglePublished() {
  if (props.album.published) {
    return axios.get(route('photo::admin::unpublish', { id: props.album.id })).then(() => {
      router.reload();
    });
  } else {
    axios.get(route('photo::admin::publish', { id: props.album.id })).then(() => {
      router.reload();
    });
  }
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
