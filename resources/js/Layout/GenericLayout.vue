<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import NavBar from '@/Layout/NavBar.vue';
import WebsiteFooter from '@/Layout/WebsiteFooter.vue';

const page = usePage();

const theme = computed(() => page.props.auth.user.theme);
const themes = computed(() => page.props.themes);
</script>

<template>
  <div :data-theme="themes[theme ?? 0]" class="flex flex-col bg-back-light text-front min-h-screen">
    <NavBar />
    <main role="main" class="w-full p-4 mx-auto mb-auto lg:w-10/12 xl:w-8/12">
      <slot />
    </main>
    <WebsiteFooter />
  </div>
</template>
