<script setup>
import { RouterLink } from 'vue-router';

  import { Icon } from '@iconify/vue';
//   import MonthDropdown from './MonthDropdown.vue';
defineProps({
  title: {
    type: String,
    default: 'Title',
  },
  back_url: {
    type: Object,
    default: null,
  },
});
</script>

<template>
  <div class="flex items-center mb-9">
    <RouterLink v-if="back_url" :to="back_url" class="mr-4">
      <Icon icon="eva:arrow-back-outline" class="text-primary-400 text-2xl" />
    </RouterLink>
    <h1 class="text-primary-400 text-lg"> {{ title }}</h1>
    <slot />
    <!-- <MonthDropdown v-if="hasDropdownMonth" /> -->
  </div>
</template>