<template>
  <div v-if="links.length > 3">
    <div class="flex flex-wrap align-self-end mb-1">
      <template v-for="(link, key) in correctLinks" :key="key">
        <div v-if="link.url === null" class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded">
          {{ link.label }}
        </div>
        <NavLink
          v-else
          :key="key"
          class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-white focus:border-indigo-500 focus:text-indigo-500"
          :class="{ 'bg-white': link.active }"
          :href="link.url"
          >{{ link.label }}
        </NavLink>
      </template>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue';
import NavLink from '@/Components/Nav/NavLink.vue';

const props = defineProps<{
  links: {
    type: Array;
    required: true;
    default: () => [];
  };
}>();

const correctLinks = computed(() => {
  return props.links.map((link, index) => {
    switch (index) {
      case 0:
        return {
          ...link,
          label: '<',
        };
      case props.links.length - 1:
        return {
          ...link,
          label: '>',
        };
      default:
        return link;
    }
  });
});
</script>
