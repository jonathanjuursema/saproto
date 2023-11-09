<template>
  <GenericLayout>
    <card class="mb-3">
      <DropZone :album-id="props.album.id" />
    </card>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <a
        v-for="photo in photos.data"
        :key="photo.id"
        :href="route('photo::view', { id: photo.id })"
        class="h-48 w-100 rounded-lg overflow-hidden"
      >
        <img class="object-cover object-center h-full w-full" :src="photo.medium_url" :alt="photo.name" />
      </a>
    </div>
    <pagination :links="photos.links" class="mt-3" />
  </GenericLayout>
</template>

<script lang="ts" setup>
import pagination from '@/Components/Nav/PaginationWrapper.vue';
import GenericLayout from '@/Layout/GenericLayout.vue';
import Card from '@/Components/CardComponent.vue';
import DropZone from '@/Components/Photos/DropZone.vue';
import type { InertiaLinkProps } from '@inertiajs/vue3';

const props = defineProps<{
  photos: InertiaLinkProps;
  album: PhotoAlbum;
}>();
console.log(props.photos);
</script>
