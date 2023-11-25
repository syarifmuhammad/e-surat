<script setup>
import { ref, watch, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router';
import axios from "axios";
import Pagination from "./Pagination.vue";

const props = defineProps({
  thead: {
    type: Array,
    default: [],
  },
  url: {
    type: String,
    required: true,
  },
})

const route = useRoute()
const router = useRouter()

const loading = ref(true)

const current = ref(parseInt(route.query.page ?? 1))

const data = ref([])
const last_page = ref(null)
const per_page = ref(null)
const timeout = ref(null)
const search = ref(route.query.search ?? "")
const beforeSearch = ref(route.query.search ?? "")
const route_name = route.name

async function getData() {
  loading.value = true

  const {
    data: { data: data_from_api, meta: { last_page: last_page_from_api, per_page: per_page_from_api } },
  } = await axios.get(
    `${props.url}?page=${current.value}&search=${search.value}`
  );

  if (Array.isArray(data_from_api)) {
    data.value = data_from_api;
  } else {
    console.log("Data tidak mengembalikan sebuah array!");
  }
  last_page.value = last_page_from_api;
  per_page.value = per_page_from_api;
  loading.value = false;
}

function changePage(cur) {
  router.push({
    name: route.name,
    query: {
      page: cur,
      search: route.query.search,
    },
  });
}

function modifySearch({ target: { value } }) {
  if (timeout.value != null) {
    clearTimeout(timeout.value);
  }
  let query = {};
  if (value != "") {
    query.search = value;
  }
  search.value = value;
  timeout.value = setTimeout(() => {
    if (beforeSearch.value != value) {
      getData();
      router.replace({ name: route.name, query: query });
      beforeSearch.value = value;
    }
    timeout.value = null;
  }, 500);
}

// function refresh() {
//   getData();
// }

watch(
  () => route.query,
  async () => {
    if (route_name === route.name) {
      current.value = parseInt(route.query.page ?? 1);
      await getData();
    }
  }
);

onMounted(async () => {
  await getData();
})

defineExpose({ getData })

</script>
<template>
  <div class="p-1.5 min-w-full max-w-full inline-block align-middle">
    <div class="w-full border rounded-lg divide-y divide-gray-200 overflow-hidden">
      <div class="py-3 px-4">
        <div class="relative max-w-xs">
          <label class="sr-only">Search</label>
          <input :value="search" @input="modifySearch" type="text" name="hs-table-with-pagination-search"
            id="hs-table-with-pagination-search"
            class="py-2 px-3 ps-9 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:z-10 focus:border-primary-400 focus:ring-primary-400 disabled:opacity-50 disabled:pointer-events-none"
            placeholder="Search for items">
          <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3">
            <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
              viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
              stroke-linejoin="round">
              <circle cx="11" cy="11" r="8" />
              <path d="m21 21-4.3-4.3" />
            </svg>
          </div>
        </div>
      </div>
      <div class="overflow-auto min-w-full">
        <table v-if="!loading" class="divide-y divide-gray-200 w-full">
          <thead class="bg-gray-50">
            <tr>
              <th v-for="d in thead" scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">
                {{ d }}</th>
            </tr>
          </thead>
          <tbody v-if="data.length > 0" class="divide-y divide-gray-200">
            <tr v-for="(d, key) in data">
              <slot
                v-bind="{ ...d, key: current * per_page - per_page + key + 1, defaultClass: 'px-6 py-4 text-sm text-gray-800' }">
              </slot>
            </tr>
          </tbody>
          <tbody v-else class="divide-y divide-gray-200">
            <tr class="text-center text-danger">
              <td :colspan="thead.length">Data tidak ditemukan !</td>
            </tr>
          </tbody>
        </table>
        <span v-else class="block text-center p-4 w-full">Loading ...</span>
      </div>
      <div class="py-1 px-4" v-if="last_page > 1">
        <pagination :max="last_page" :current="current" @change-page="changePage"></pagination>
      </div>
    </div>
  </div>
</template>