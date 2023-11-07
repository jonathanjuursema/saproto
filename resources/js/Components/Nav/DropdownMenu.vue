<script setup lang="ts">
import { onUnmounted, ref, Ref } from 'vue';
import NavItem from '@/Components/Nav/NavItem.vue';

withDefaults(
  defineProps<{
    noHover?: boolean;
    direction?: 'left' | 'right';
  }>(),
  {
    direction: 'left',
  }
);

const open = ref<boolean>(false);

const dropdown: Ref<HTMLElement> = ref(null);

const openDropdown = () => {
  open.value = true;
  window.addEventListener('click', clickAway);
};

const closeDropdown = () => {
  open.value = false;
  window.removeEventListener('click', clickAway);
};
const clickAway = (e: MouseEvent) => {
  if (!dropdown.value.contains(e.target) && dropdown.value !== e.target) {
    closeDropdown();
  }
};

onUnmounted(() => {
  window.removeEventListener('click', clickAway);
});
</script>

<template>
  <div ref="dropdown" class="relative">
    <NavItem>
      <button
        class="flex items-center space-x-1"
        :class="noHover ? '' : 'opacity-75 hover:opacity-100'"
        @click="open ? closeDropdown() : openDropdown()"
      >
        <div>
          <slot name="parent" />
        </div>
        <div :class="`fas fa-caret-${open ? 'up' : 'down'}`"></div>
      </button>
    </NavItem>
    <div
      v-if="open"
      class="absolute top-full mt-2 max-h-[80vh] w-fit overflow-y-auto bg-back text-front rounded-md shadow-lg min-w-[100px] py-2 ring-1 ring-front ring-opacity-20"
      :class="`${direction}-0`"
    >
      <slot name="children" />
    </div>
  </div>
</template>
<style scoped></style>
