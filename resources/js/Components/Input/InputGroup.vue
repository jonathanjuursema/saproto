<script setup lang="ts">
import InfoText from '@/Components/InfoText.vue';
import Label from '@/Components/Input/Shared/InputLabel.vue';
import { useSlots } from 'vue';

defineOptions({
  inheritAttrs: false,
});

defineProps<{
  name?: string;
}>();

const slots = useSlots();
</script>

<template>
  <div class="mb-4">
    <Label :name="name">
      <slot />
    </Label>
    <div>
      <slot name="input"> </slot>
    </div>
    <InfoText v-if="slots.error" color="red-500">
      <slot name="error" />
    </InfoText>
    <InfoText v-if="slots.info">
      <slot name="info" />
    </InfoText>
  </div>
</template>

<style scoped></style>
