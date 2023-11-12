<script setup lang="ts">
const props = withDefaults(
  defineProps<{
    photo: Photo;
    selected?: boolean;
    height?: number;
  }>(),
  {
    selected: false,
    height: 48,
  }
);
</script>
<template>
  <!--    align the last item to the bottom of the div-->
  <a
    :href="route('photo::view', { id: photo.id })"
    class="h-80 w-full rounded-lg overflow-hidden flex relative"
    :class="selected ? 'border-4 border-opacity-1 border-primary' : ''"
    :style="{
      backgroundImage: 'url(' + photo.small_url + ')',
      backgroundSize: 'cover',
      backgroundPosition: 'center',
    }"
  >
    <!--        todo: replace this with an actual tooltip     -->

    <i v-if="photo.likes_count !== undefined" class="fas fa-heart text-red-600 mt-2 ms-2">
      {{ ' ' + photo.likes_count }}</i
    >
    <i
      v-if="photo.private"
      class="fas fa-eye-slash ms-2 mb-2 text-info absolute bottom-0 start-0"
      data-bs-toggle="tooltip"
      data-bs-placement="bottom"
      title="This photo is only visible to members."
    ></i>
  </a>
</template>
