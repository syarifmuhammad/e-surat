<template>
    <nav aria-label="Page navigation" class="mt-2" v-if="max > 1">
      <ul class="pagination pagination-lg justify-content-center">
        <li class="page-item prev cursor-pointer">
          <span class="page-link" v-if="parseInt((current ?? 1)) > 1" @click="changePage(current - 1)">
            <i class="tf-icon bx bx-chevrons-left"></i>
          </span>
        </li>
        <template v-if="max < 10">
          <li v-for="n in max" class="page-item"
            :class="{ active: n == (current ?? 1), 'cursor-pointer': n !== (current ?? 1) }" @click="changePage(n)">
            <span class="page-link">{{ n }}</span>
          </li>
        </template>
        <template v-else-if="current <= 5">
          <li v-for="n in 7" class="page-item"
            :class="{ active: n == (current ?? 1), 'cursor-pointer': n !== (current ?? 1) }" @click="changePage(n)">
            <span class="page-link">{{ n }}</span>
          </li>
          <li class="page-item disabled">
            <span class="page-link">...</span>
          </li>
          <li v-for="n in 2" class="page-item"
            :class="{ active: max - 2 + n == (current ?? 1), 'cursor-pointer': max - 2 + n !== (current ?? 1) }"
            @click="changePage(max - 2 + n)">
            <span class="page-link">{{ max - 2 + n }}</span>
          </li>
        </template>
        <template v-else-if="current >= 6 && current < max - 4">
          <li class="page-item cursor-pointer" @click="changePage(1)">
            <span class="page-link">1</span>
          </li>
          <li class="page-item disabled">
            <span class="page-link">...</span>
          </li>
          <li v-for="n in 5" class="page-item"
            :class="{ active: current - 3 + n == (current ?? 1), 'cursor-pointer': current - 3 + n !== (current ?? 1) }"
            @click="changePage(current - 3 + n)">
            <span class="page-link">{{ current - 3 + n }}</span>
          </li>
          <li class="page-item disabled">
            <span class="page-link">...</span>
          </li>
          <li class="page-item cursor-pointer" @click="changePage(max)">
            <span class="page-link">{{ max }}</span>
          </li>
        </template>
        <template v-else-if="current >= max - 4">
          <li v-for="n in 2" class="page-item cursor-pointer" @click="changePage(n)">
            <span class="page-link">{{ n }}</span>
          </li>
          <li class="page-item disabled">
            <span class="page-link">...</span>
          </li>
          <li v-for="n in 7" class="page-item"
            :class="{ active: max - 7 + n == (current ?? 1), 'cursor-pointer': max - 7 + n !== (current ?? 1) }"
            @click="changePage(max - 7 + n)">
            <span class="page-link">{{ max - 7 + n }}</span>
          </li>
        </template>
        <li class="page-item next cursor-pointer">
          <span class="page-link" v-if="parseInt((current ?? 1)) < max" @click="changePage(current + 1)">
            <i class="tf-icon bx bx-chevrons-right"></i>
          </span>
        </li>
      </ul>
    </nav>
  </template>
  <script>
  export default {
    name: "Pagination",
    props: {
      current: Number,
      max: {
        type: Number,
        default: 1,
      },
    },
    methods: {
      changePage(number) {
        this.$emit('change-page', number);
      }
    }
  };
  </script>