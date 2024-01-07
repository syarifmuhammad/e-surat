<script setup>
import { ref } from 'vue'
import { RouterLink } from 'vue-router'
import { Icon } from '@iconify/vue'
import axios from 'axios'
import CustomTable from '@/components/CustomTable.vue';

const url = import.meta.env.VITE_URL_API

const thead = [
    "#",
    "Name",
    "Jenis Surat",
    "",
]

const loading = ref(null)
const table = ref(null)

function download(id, name, letter_type) {
    loading.value.open()
    axios.get(`${url}/outcoming-letters/templates/${id}/download`, {
        responseType: 'blob',
    }).then(res => {
        const url = window.URL.createObjectURL(new Blob([res.data]))
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', `${id}-${name}-${letter_type}.docx`)
        document.body.appendChild(link)
        link.click()
    }).finally(() => {
        loading.value.close()
    })
}
</script>

<template>
    <Loading ref="loading"></Loading>
    <div class="flex flex-col bg-white rounded-lg">
        <div class="px-8 py-5 min-w-full inline-block align-middle">
            <div class="flex justify-between mb-6">
                <h3 class="text-primary-400">List Template Surat</h3>
                <RouterLink :to="{ name: 'create_letter_templates' }" type="button" class="btn btn-primary">
                    <Icon class="text-lg" icon="fluent:add-12-filled" /> Tambah Template Surat
                </RouterLink>
            </div>
            <CustomTable ref="table" :thead="thead" :url="`${url}/outcoming-letters/templates`" v-slot="item">
                <td :class="[item.defaultClass]">{{ item.id }}</td>
                <td :class="[item.defaultClass]">{{ item.name }}</td>
                <td :class="[item.defaultClass]">{{ item.letter_type }}</td>
                <td :class="[item.defaultClass]">
                    <div class="hs-dropdown relative inline-flex">
                        <button id="hs-dropdown-with-icons" type="button"
                            class="hs-dropdown-toggle py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none">
                            Aksi
                            <svg class="hs-dropdown-open:rotate-180 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </button>

                        <div class="z-10 hs-dropdown-menu transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 hidden min-w-[15rem] bg-white shadow-md rounded-lg p-2 mt-2 divide-y divide-gray-200"
                            aria-labelledby="hs-dropdown-with-icons">
                            <div class="py-2 first:pt-0 last:pb-0">
                                <span @click="download(item.id, item.name, item.letter_type)"
                                    class="text-primary-500 cursor-pointer flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm hover:bg-gray-100 focus:outline-none focus:bg-gray-100">
                                    <Icon class="text-lg" icon="gg:file-document"></Icon>
                                    Download Template Surat
                                </span>
                                <RouterLink :to="{ name: 'update_letter_templates', params: { id: item.id } }"
                                    class="text-primary-500 cursor-pointer flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm hover:bg-gray-100 focus:outline-none focus:bg-gray-100">
                                    <Icon class="text-lg" icon="cil:pencil"></Icon>
                                    Edit Template Surat
                                </RouterLink>
                            </div>
                        </div>
                    </div>
                </td>
            </CustomTable>
        </div>
    </div>
</template>