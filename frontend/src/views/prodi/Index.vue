<script setup>
import { ref, reactive } from 'vue'
import { Icon } from '@iconify/vue'
import SubHeader from '@/components/SubHeader.vue'
import CustomTable from '@/components/CustomTable.vue'
import Modal from '@/components/Modal.vue'
import Loading from '@/components/Loading.vue'
import axios from 'axios'


const url = import.meta.env.VITE_URL_API

const thead = [
    "#",
    "Nama",
    "Fakultas",
    "Aksi",
]

const loading = ref(null)
const table = ref(null)

function delete_prodi(id) {
    Swal.fire({
        title: 'Apakah anda yakin?',
        text: "Jurusan / Prodi akan dihapus secara permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            loading.value.open()
            axios.delete(`${url}/prodi/${id}`)
                .then(res => {
                    loading.value.close()
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Jurusan / Prodi berhasil dihapus!',
                    })
                    table.value.getData()
                })
                .catch(err => {
                    loading.value.close()
                    if (err.response.status == 422) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: err.response.data.message,
                        })
                    } else {
                        console.log(err.response)
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Terjadi kesalahan, silahkan coba lagi!',
                        })
                    }
                })
        }
    })
}

</script>

<template>
    <Loading ref="loading" />
    <div class="flex flex-col bg-white rounded-lg">
        <div class="px-8 py-5 min-w-full inline-block align-middle">
            <div class="flex justify-between mb-6">
                <h3 class="text-primary-400">List Data Jurusan / Prodi</h3>
                <router-link :to="{ name: 'create_prodi' }" class="btn btn-primary">
                    <Icon class="text-lg" icon="fluent:add-12-filled" /> Tambah Jurusan / Prodi
                </router-link>
            </div>
            <CustomTable ref="table" :thead="thead" :url="`${url}/prodi`" v-slot="item">
                <td :class="[item.defaultClass]" class="w-2">{{ item.key }}</td>
                <td :class="[item.defaultClass]">{{ item.nama_prodi }} ({{ item.singkatan_prodi }})</td>
                <td :class="[item.defaultClass]">{{ item.nama_fakultas }} ({{ item.singkatan_fakultas }})</td>
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
                                <RouterLink :to="{ name: 'update_prodi', params: { id: item.id } }"
                                    class="text-primary-500 cursor-pointer flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm hover:bg-gray-100 focus:outline-none focus:bg-gray-100">
                                    <Icon class="text-lg" icon="cil:pencil"></Icon>
                                    Edit
                                </RouterLink>
                                <span @click="delete_prodi(item.id)"
                                    class="text-primary-500 cursor-pointer flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm hover:bg-gray-100 focus:outline-none focus:bg-gray-100">
                                    <Icon class="text-lg" icon="cil:trash"></Icon>
                                    Hapus
                                </span>
                            </div>
                        </div>
                    </div>
                </td>
            </CustomTable>
        </div>
    </div>
</template>

<style scoped></style>