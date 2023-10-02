<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import NavBar from '@/Layout/NavBar.vue';
import Footer from '@/Layout/WebsiteFooter.vue';

const page = usePage();

const theme = computed(() => page.props.user.theme);
const themes = computed(() => page.props.themes);
</script>

<template>
  <div :data-theme="themes[theme ?? 0]">
    <NavBar />
    <main role="main" class="w-full p-4 mx-auto lg:w-10/12 xl:w-8/12">
      <slot />
    </main>
    <Footer />
  </div>
  <!--  Dirty Fix-->
  <div
    class="bg-primary bg-info bg-warning bg-danger text-primary text-info text-warning text-danger border-primary border-info border-warning border-danger"
  ></div>
  <div
    class="hover:bg-primary-dark hover:bg-info-dark hover:bg-warning-dark hover:bg-danger-dark hover:text-primary-dark hover:text-info-dark hover:text-warning-dark hover:text-danger-dark hover:border-primary-dark hover:border-info-dark hover:border-warning-dark hover:border-danger-dark"
  ></div>
</template>
