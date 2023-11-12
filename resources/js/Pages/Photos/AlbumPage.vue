<template>
  <generic-layout>
    <Head title="Dashboard" />
    <card>
      <template #header>
        <div class="flex justify-content-start space-x-8">
          <div class="flex flex-row gap-3">
            <nav-pill>
              <nav-link no-inertia :href="route('photo::albums')">Album overview</nav-link>
            </nav-pill>
            <nav-pill>
              <nav-link :href="route('photo::admin::edit', album.id)">Edit album</nav-link>
            </nav-pill>
          </div>
        </div>
      </template>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <PhotoCard v-for="photo in photos.data" :key="photo.id" :photo="photo" />
      </div>
      <pagination :links="photos.links" class="mt-3" />
    </card>
  </generic-layout>
</template>

<script setup lang="ts">
import { Head, InertiaLinkProps } from '@inertiajs/vue3';
import GenericLayout from '@/Layout/GenericLayout.vue';
import Card from '@/Components/CardComponent.vue';
import pagination from '@/Components/Nav/PaginationWrapper.vue';
import NavPill from '@/Components/NavPill.vue';
import NavLink from '@/Components/Nav/NavLink.vue';
import PhotoCard from '@/Components/Photos/PhotoCard.vue';

const props = defineProps<{
  photos: InertiaLinkProps;
  album: PhotoAlbum;
}>();
console.log(props.photos.data[0]);
</script>
