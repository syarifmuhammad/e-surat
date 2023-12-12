<script setup>
import { ref, reactive } from 'vue'
import { Icon } from '@iconify/vue'
import SubHeader from '@/components/SubHeader.vue'
import CustomTable from '@/components/CustomTable.vue';
import Modal from '@/components/Modal.vue';
import Loading from '@/components/Loading.vue';
import axios from 'axios'

const url = import.meta.env.VITE_URL_API
const loading = ref(null)
const table = ref(null)
const modal_form_category = ref(null)

const thead = [
    "#",
    "Nama",
    "Jumlah Surat",
    "Aksi",
]

const form_category = reactive({
    id: "",
    name: "",
})

const errors = reactive({
    name: '',
})

function reset_form_category() {
    form_category.id = ""
    form_category.name = ""
}

function reset_errors() {
    errors.name = ""
}

function open_modal_form_category(form_category_args = null) {
    if (form_category_args) {
        form_category.id = form_category_args.id
        form_category.name = form_category_args.name
    }

    modal_form_category.value.open()
}

function close_modal_form_category() {
    modal_form_category.value.close()
    reset_form_category()
    reset_errors()
}

function save_category() {
    reset_errors()
    loading.value.open()
    if (form_category.id != "") {
        // update
        let payload = {
            name: form_category.name,
        }

        axios.put(`${url}/incoming-letters/categories/${form_category.id}`, payload)
            .then(res => {
                loading.value.close()
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Kategori berhasil diubah!',
                })
                table.value.getData()
                close_modal_form_category()
            })
            .catch(err => {
                loading.value.close()
                if (err.response.status == 422) {
                    modal_form_category.value.open()
                    errors.name = err.response.data.errors.name[0]
                } else {
                    console.log(err.response)
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Terjadi kesalahan, silahkan coba lagi!',
                    })
                    close_modal_form_category()
                }

            })
    } else {
        // create
        let payload = {
            name: form_category.name,
        }

        axios.post(`${url}/incoming-letters/categories`, payload)
            .then(res => {
                loading.value.close()
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Kategori berhasil ditambahkan!',
                })
                table.value.getData()
                close_modal_form_category()
            })
            .catch(err => {
                loading.value.close()
                if (err.response.status == 422) {
                    modal_form_category.value.open()
                    errors.name = err.response.data.errors.name[0]
                } else {
                    console.log(err.response)
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Terjadi kesalahan, silahkan coba lagi!',
                    })
                    close_modal_form_category()
                }

            })
    }
}

function delete_category(id) {
    Swal.fire({
        title: 'Apakah anda yakin?',
        text: "Kategori akan dihapus secara permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            loading.value.open()
            axios.delete(`${url}/incoming-letters/categories/${id}`)
                .then(res => {
                    loading.value.close()
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Kategori berhasil dihapus!',
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
    <!-- <SubHeader :title="``"></SubHeader> -->
    <div class="flex flex-col bg-white rounded-lg">
        <div class="px-8 py-5 min-w-full inline-block align-middle">
            <div class="flex justify-between mb-6">
                <h3 class="text-primary-400">List Kategori Surat Masuk</h3>
                <button @click="open_modal_form_category()" class="btn btn-primary">
                    <Icon class="text-lg" icon="fluent:add-12-filled" /> Tambah Kategori
                </button>
            </div>
            <CustomTable ref="table" :thead="thead" :url="`${url}/incoming-letters/categories`" v-slot="item">
                <td :class="[item.defaultClass, { disabled: item.show }]">{{ item.key }}</td>
                <td :class="[item.defaultClass, { disabled: item.show }]">{{ item.name }}</td>
                <td :class="[item.defaultClass, { disabled: item.show }]">{{ item.count }}</td>
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
                                <span
                                    @click="open_modal_form_category({ id: item.id, name: item.name })"
                                    class="text-primary-500 cursor-pointer flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm hover:bg-gray-100 focus:outline-none focus:bg-gray-100">
                                    <Icon class="text-lg" icon="cil:pencil"></Icon>
                                    Edit
                                </span>
                                <span v-if="item.count < 1" @click="delete_category(item.id)"
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
        <Modal ref="modal_form_category">
            <form @submit.prevent="save_category">
                <div class="p-4 sm:p-10 overflow-y-auto">
                    <div class="text-center mb-6">
                        <h3 class="mb-2 text-xl font-bold text-gray-800">
                            Form Tambah Kategori
                        </h3>
                        <p class="text-gray-500">
                            Tambahkan kategori surat dengan mengisi form dibawah ini
                        </p>
                    </div>
                    <div class="mb-4">
                        <label for="input-label" class="block text-sm font-medium mb-2">Kategori</label>
                        <input v-model="form_category.name" type="text" id="input-label" class="form-control"
                            placeholder="Cth: Surat Undangan">
                        <p v-if="errors.name" class="text-xs text-red-600 mt-2" id="name-error">
                            {{ errors.name }}
                        </p>
                    </div>
                </div>
                <div class="border-t p-4 sm:px-10 flex justify-end">
                    <button class="btn btn-primary px-14 py-3">Simpan</button>
                </div>
            </form>
        </Modal>
    </div>
</template>

<style scoped></style>