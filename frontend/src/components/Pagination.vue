<script setup>

const props = defineProps({
  current: Number,
  max: {
    type: Number,
    default: 1,
  }
})

const emit = defineEmits(['change-page'])

function changePage(number) {
  emit('change-page', number);
}
</script>
<template>
  <nav class="flex items-center gap-x-1 mt-2 justify-center">
    <button type="button"
      class="min-h-[38px] min-w-[38px] py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm rounded-lg text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none"
      :disabled="parseInt((current ?? 1)) < 2" @click="changePage(current - 1)">
      <svg class="flex-shrink-0 w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="m15 18-6-6 6-6" />
      </svg>
      <span>Sebelumnya</span>
    </button>
    <div class="flex items-center gap-x-1">
      <template v-if="max < 10">
        <button v-for="n in max" type="button" @click="changePage(n)"
          class="min-h-[38px] min-w-[38px] flex justify-center items-center text-gray-800 py-2 px-3 text-sm rounded-lg focus:outline-none disabled:opacity-50 disabled:pointer-events-none"
          :class="{ 'bg-gray-200 focus:bg-gray-300': n == (current ?? 1), 'hover:bg-gray-100 focus:bg-gray-100': n != (current ?? 1) }"
          aria-current="page">{{ n }}</button>
      </template>
      <template v-else-if="current <= 5">
        <button v-for="n in 7" type="button" @click="changePage(n)"
          class="min-h-[38px] min-w-[38px] flex justify-center items-center text-gray-800 py-2 px-3 text-sm rounded-lg focus:outline-none disabled:opacity-50 disabled:pointer-events-none"
          :class="{ 'bg-gray-200 focus:bg-gray-300': n == (current ?? 1), 'hover:bg-gray-100 focus:bg-gray-100': n != (current ?? 1) }"
          aria-current="page">{{ n }}</button>
        <li v-for="n in 7" class="page-item"
          :class="{ active: n == (current ?? 1), 'cursor-pointer': n !== (current ?? 1) }">
          <span class="page-link">{{ n }}</span>
        </li>
        <div class="hs-tooltip inline-block">
          <button type="button"
            class="hs-tooltip-toggle group min-h-[38px] min-w-[38px] flex justify-center items-center text-gray-400 hover:text-blue-600 p-2 text-sm rounded-lg focus:outline-none focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none">
            <span class="group-hover:hidden text-xs">•••</span>
            <svg class="group-hover:block hidden flex-shrink-0 w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24"
              height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
              stroke-linejoin="round">
              <path d="m6 17 5-5-5-5" />
              <path d="m13 17 5-5-5-5" />
            </svg>
            <span
              class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 transition-opacity inline-block absolute invisible z-10 py-1 px-2 bg-gray-900 text-xs font-medium text-white rounded shadow-sm"
              role="tooltip">
              Berikutnya halaman 8
            </span>
          </button>
        </div>
        <button v-for="n in 2" type="button" @click="changePage(n)"
          class="min-h-[38px] min-w-[38px] flex justify-center items-center text-gray-800 py-2 px-3 text-sm rounded-lg focus:outline-none disabled:opacity-50 disabled:pointer-events-none"
          :class="{ 'bg-gray-200 focus:bg-gray-300': max - 2 + n == (current ?? 1), 'hover:bg-gray-100 focus:bg-gray-100': max - 2 + n != (current ?? 1) }"
          aria-current="page">{{ max - 2 + n }}</button>
      </template>
      <template v-else-if="current >= 6 && current < max - 4">
        <button type="button" @click="changePage(1)"
          class="min-h-[38px] min-w-[38px] flex justify-center items-center text-gray-800 py-2 px-3 text-sm rounded-lg focus:outline-none disabled:opacity-50 disabled:pointer-events-none"
          :class="{ 'bg-gray-200 focus:bg-gray-300': 1 == (current ?? 1), 'hover:bg-gray-100 focus:bg-gray-100': 1 != (current ?? 1) }"
          aria-current="page">1</button>
        <div class="hs-tooltip inline-block">
          <button type="button"
            class="hs-tooltip-toggle group min-h-[38px] min-w-[38px] flex justify-center items-center text-gray-400 hover:text-blue-600 p-2 text-sm rounded-lg focus:outline-none focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none">
            <span class="group-hover:hidden text-xs">•••</span>
            <svg class="group-hover:block hidden flex-shrink-0 w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24"
              height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
              stroke-linejoin="round">
              <path d="m6 17 5-5-5-5" />
              <path d="m13 17 5-5-5-5" />
            </svg>
            <span
              class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 transition-opacity inline-block absolute invisible z-10 py-1 px-2 bg-gray-900 text-xs font-medium text-white rounded shadow-sm"
              role="tooltip">
              Berikutnya halaman 2
            </span>
          </button>
        </div>
        <button v-for="n in 5" type="button" @click="changePage(current - 3 + n)"
          class="min-h-[38px] min-w-[38px] flex justify-center items-center text-gray-800 py-2 px-3 text-sm rounded-lg focus:outline-none disabled:opacity-50 disabled:pointer-events-none"
          :class="{ 'bg-gray-200 focus:bg-gray-300': current - 3 + n == (current ?? 1), 'hover:bg-gray-100 focus:bg-gray-100': current - 3 + n != (current ?? 1) }"
          aria-current="page">{{ current - 3 + n }}</button>
        <div class="hs-tooltip inline-block">
          <button type="button"
            class="hs-tooltip-toggle group min-h-[38px] min-w-[38px] flex justify-center items-center text-gray-400 hover:text-blue-600 p-2 text-sm rounded-lg focus:outline-none focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none">
            <span class="group-hover:hidden text-xs">•••</span>
            <svg class="group-hover:block hidden flex-shrink-0 w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24"
              height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
              stroke-linejoin="round">
              <path d="m6 17 5-5-5-5" />
              <path d="m13 17 5-5-5-5" />
            </svg>
            <span
              class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 transition-opacity inline-block absolute invisible z-10 py-1 px-2 bg-gray-900 text-xs font-medium text-white rounded shadow-sm"
              role="tooltip">
              Berikutnya halaman {{ current + n }}
            </span>
          </button>
        </div>
        <button type="button" @click="changePage(max)"
          class="min-h-[38px] min-w-[38px] flex justify-center items-center text-gray-800 py-2 px-3 text-sm rounded-lg focus:outline-none disabled:opacity-50 disabled:pointer-events-none"
          :class="{ 'bg-gray-200 focus:bg-gray-300': max == (current ?? 1), 'hover:bg-gray-100 focus:bg-gray-100': max != (current ?? 1) }"
          aria-current="page">{{ max }}</button>
      </template>
      <template v-else-if="current >= max - 4">
        <button v-for="n in 2" type="button" @click="changePage(n)"
          class="min-h-[38px] min-w-[38px] flex justify-center items-center text-gray-800 py-2 px-3 text-sm rounded-lg focus:outline-none disabled:opacity-50 disabled:pointer-events-none"
          :class="{ 'bg-gray-200 focus:bg-gray-300': n == (current ?? 1), 'hover:bg-gray-100 focus:bg-gray-100': n != (current ?? 1) }"
          aria-current="page">{{ n }}</button>
        <div class="hs-tooltip inline-block">
          <button type="button"
            class="hs-tooltip-toggle group min-h-[38px] min-w-[38px] flex justify-center items-center text-gray-400 hover:text-blue-600 p-2 text-sm rounded-lg focus:outline-none focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none">
            <span class="group-hover:hidden text-xs">•••</span>
            <svg class="group-hover:block hidden flex-shrink-0 w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24"
              height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
              stroke-linejoin="round">
              <path d="m6 17 5-5-5-5" />
              <path d="m13 17 5-5-5-5" />
            </svg>
            <span
              class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 transition-opacity inline-block absolute invisible z-10 py-1 px-2 bg-gray-900 text-xs font-medium text-white rounded shadow-sm"
              role="tooltip">
              Berikutnya halaman 3
            </span>
          </button>
        </div>
        <button v-for="n in 7" type="button" @click="changePage(max - 7 + n)"
          class="min-h-[38px] min-w-[38px] flex justify-center items-center text-gray-800 py-2 px-3 text-sm rounded-lg focus:outline-none disabled:opacity-50 disabled:pointer-events-none"
          :class="{ 'bg-gray-200 focus:bg-gray-300': max - 7 + n == (current ?? 1), 'hover:bg-gray-100 focus:bg-gray-100': max - 7 + n != (current ?? 1) }"
          aria-current="page">{{ max - 7 + n }}</button>
      </template>
    </div>
    <button type="button" :disabled="parseInt((current ?? 1)) == max" @click="changePage(current + 1)"
      class="min-h-[38px] min-w-[38px] py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm rounded-lg text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none">
      <span>Berikutnya</span>
      <svg class="flex-shrink-0 w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="m9 18 6-6-6-6" />
      </svg>
    </button>
  </nav>
</template>