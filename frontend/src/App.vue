<script setup>
import { computed } from 'vue'
import { RouterView, useRoute } from 'vue-router'
import AuthenticatedLayout from './components/layouts/AuthenticatedLayout.vue';
import GuestLayout from './components/layouts/GuestLayout.vue';
import BlankLayout from './components/layouts/BlankLayout.vue';

const route = useRoute()

const layout = computed(() => {
  if (route.meta.layout === 'GuestLayout') {
    return GuestLayout
  } else if(route.meta.layout === 'AuthenticatedLayout') {
    return AuthenticatedLayout
  } else if(route.meta.layout === 'BlankLayout') {
    return BlankLayout
  } else {
    return null
  }
})

</script>

<template>
  <component v-if="layout" :is="layout">
    <RouterView />
  </component>
  <div v-else class="w-full h-[100vh] flex justify-center items-center">
    <h1 class="text-primary-500 animate-pulse">Loading ...</h1>
  </div>
</template>

<style></style>