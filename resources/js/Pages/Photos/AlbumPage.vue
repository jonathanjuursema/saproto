<template>
  <generic-layout>
    <Head title="Dashboard"/>
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
        <a
            v-for="photo in photos.data"
            :key="photo.id"
            :href="route('photo::view', { id: photo.id })"
            class="h-48 w-100 rounded-lg overflow-hidden"
            :style="{
            backgroundImage: 'url(' + photo.small_url + ')',
            backgroundSize: 'cover',
            backgroundPosition: 'center',
          }"
        >
          {{ photo.likes_count }}
        </a>
      </div>
      <pagination :links="photos.links" class="mt-3"/>
    </card>
  </generic-layout>
</template>

<script setup lang="ts">
import {Head, InertiaLinkProps} from '@inertiajs/vue3';
import GenericLayout from '@/Layout/GenericLayout.vue';
import Card from '@/Components/CardComponent.vue';
import pagination from '@/Components/Nav/PaginationWrapper.vue';
import NavPill from '@/Components/NavPill.vue';
import NavLink from '@/Components/Nav/NavLink.vue';

const props = defineProps<{
  photos: InertiaLinkProps;
  album: PhotoAlbum;
}>();
</script>
