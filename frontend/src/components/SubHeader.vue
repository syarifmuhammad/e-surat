<script setup>
//   import { Icon } from '@iconify/vue';
//   import MonthDropdown from './MonthDropdown.vue';
  defineProps({
    title: {
      type: String,
      default: 'Title',
    },
  });
</script>

<template>
  <div class="grid grid-cols-2 items-center mb-9">
      <h1 class="text-primary-400"> {{ title }}</h1>
      <slot />
    <!-- <MonthDropdown v-if="hasDropdownMonth" /> -->
  </div>
</template>