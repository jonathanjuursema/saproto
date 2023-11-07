<script setup lang="ts">
import { computed, useSlots } from 'vue';
import InputType from '@/types/InputType.d.ts';

const slots = useSlots();

const variantStyles = {
  primary: 'bg-primary border-primary',
  secondary: 'bg-secondary border-secondary',
  success: 'bg-success border-success',
  info: 'bg-info border-info',
  warning: 'bg-warning border-warning',
  danger: 'bg-danger border-danger',
  dark: 'bg-dark border-dark',
  light: 'bg-light border-light',
};

const hoverStyles = {
  primary: 'hover:bg-primary-dark hover:border-primary-dark',
  secondary: 'hover:bg-secondary-dark hover:border-secondary-dark',
  success: 'hover:bg-success-dark hover:border-success-dark',
  info: 'hover:bg-info-dark hover:border-info-dark',
  warning: 'hover:bg-warning-dark hover:border-warning-dark',
  danger: 'hover:bg-danger-dark hover:border-danger-dark',
  dark: 'hover:bg-dark-dark hover:border-dark-dark',
  light: 'hover:bg-light-dark hover:border-light-dark',
};

const inputStyles = () => {
  let styles =
    'focus:ring-0 focus:outline-none focus:border-blue-600 leading-tight w-full text-front px-3 py-2 bg-back disabled:bg-back-light';
  styles += slots.before ? ' border-s-0' : ' rounded-s';
  styles += slots.after ? ' border-e-0' : ' rounded-e';
  return styles;
};

defineOptions({
  inheritAttrs: false,
});

const props = withDefaults(
  defineProps<{
    name?: string;
    id?: string;
    type?: InputType;
    disabled?: boolean;
    required?: boolean;
    value?: string | number | boolean | Array<boolean>;
    modelValue?: string | number | boolean | Array<boolean>;
    placeHolder?: string;
    afterVariant?: string;
    beforeVariant?: string;
    beforeHover?: boolean;
    afterHover?: boolean;
  }>(),
  {
    name: null,
    id: null,
    type: InputType.Text,
    value: null,
    modelValue: null,
    placeHolder: null,
    afterVariant: 'info',
    beforeVariant: 'info',
  }
);

const emit = defineEmits(['update:modelValue']);

const model = computed({
  get() {
    return props.modelValue ?? props.value;
  },
  set(val) {
    emit('update:modelValue', val);
  },
});
</script>

<template>
  <div v-if="type === InputType.Checkbox" class="flex items-center">
    <input
      :id="id ?? name"
      v-model="model"
      class="rounded bg-back border-none ring-1 ring-front ring-opacity-20 checked:bg-primary hover:bg-back-light checked:hover:bg-primary-dark"
      :value="value"
      type="checkbox"
      :name="name"
      :disabled="disabled"
      :required="required"
    />
    <label class="ml-1" :for="id ?? name">
      <slot />
    </label>
  </div>
  <input
    v-else-if="type === InputType.File"
    type="file"
    class="box-border w-full pr-2 rounded file:ring-primary hover:file:ring-primary-dark file:ring-1 file:cursor-pointer file:bg-primary hover:file:bg-primary-dark file:border-primary hover:file:border-primary-dark file:border-solid file:text-white file:rounded-s file:px-3 file:py-2 file:text-sm file:font-semibold border border-gray-500"
  />
  <div v-else class="shadow rounded flex items-center m-0 p-0 ring-blue-600 focus-within:ring-1">
    <div class="order-2 flex-grow peer">
      <select
        v-if="type === InputType.Select"
        :id="id ?? name"
        v-model="model"
        :name="name"
        :value="value"
        :disabled="disabled"
        :required="required"
        :class="inputStyles()"
      >
        <slot />
      </select>
      <input
        v-else
        :id="id ?? name"
        v-model="model"
        :name="name"
        type="text"
        :placeholder="placeHolder"
        :disabled="disabled"
        :required="required"
        :class="inputStyles()"
      />
    </div>
    <div
      v-if="slots.before"
      class="order-1 self-stretch text-white flex items-center rounded-s border-t border-l border-b peer-focus-within:border-blue-600"
      :class="variantStyles[beforeVariant] + ' ' + (beforeHover ? hoverStyles[beforeVariant] : '')"
    >
      <slot name="before" />
    </div>
    <div
      v-if="slots.after"
      class="order-3 self-stretch text-white flex items-center rounded-e border-t border-r border-b peer-focus-within:border-blue-600"
      :class="variantStyles[afterVariant] + ' ' + (afterHover ? hoverStyles[afterVariant] : '')"
    >
      <slot name="after" />
    </div>
  </div>
</template>

<style scoped></style>
