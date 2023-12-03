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
  <div class="flex items-center mb-9">
      <h1 class="text-primary-400 text-lg"> {{ title }}</h1>
      <slot />
    <!-- <MonthDropdown v-if="hasDropdownMonth" /> -->
  </div>
</template>