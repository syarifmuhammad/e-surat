<script setup>
import { ref, reactive } from 'vue'
import { Icon } from '@iconify/vue'
import CustomTable from '@/components/CustomTable.vue';
import Modal from '@/components/Modal.vue';
import Loading from '@/components/Loading.vue';
import SearchInput from '@/components/SearchInput.vue';
import UploadFile from '@/components/UploadFile.vue';
import axios from 'axios'

const url = import.meta.env.VITE_URL_API
const loading = ref(null)
const table = ref(null)
const modal_form = ref(null)
const selected_category = ref(null)
const file_surat = ref(null)

const thead = [
    "#",
    "Kategori",
    "Nomor",
    "Pengirim",
    "Perihal",
    "Keterangan",
    "File",
    "Tanggal",
    "Aksi",
]

const form = reactive({
    id: "",
    reference_number: "",
    asal_surat: "",
    perihal: "",
    keterangan: "",
    file_surat: "",
    tanggal_surat: "",
})

const errors = reactive({
    reference_number: "",
    asal_surat: "",
    perihal: "",
    keterangan: "",
    file_surat: "",
    tanggal_surat: "",
})

function reset_form() {
    form.id = ""
    form.reference_number = ""
    form.asal_surat = ""
    form.perihal = ""
    form.keterangan = ""
    form.tanggal_surat = ""
    file_surat.value.files = null
    selected_category.value = null
}

function reset_errors() {
    Object.keys(errors).forEach(key => {
        errors[key] = ""
    })
}

function reset_category() {
    selected_category.value = null
}

function open_modal_form(form_args = null) {
    if (form_args) {
        form.id = form_args.id
        selected_category.value = form_args.category
        form.reference_number = form_args.reference_number
        form.asal_surat = form_args.asal_surat
        form.perihal = form_args.perihal
        form.keterangan = form_args.keterangan
        form.tanggal_surat = form_args.tanggal_surat
    }

    modal_form.value.open()
}

function close_modal_form() {
    modal_form.value.close()
    reset_form()
    reset_errors()
}

function save() {
    reset_errors()
    loading.value.open()
    if (form.id != "") {
        // update
        let payload = new FormData()
        payload.append('_method', 'PUT')
        payload.append('category', selected_category.value.id)
        payload.append('reference_number', form.reference_number)
        payload.append('asal_surat', form.asal_surat)
        payload.append('perihal', form.perihal)
        payload.append('keterangan', form.keterangan)
        payload.append('file_surat', file_surat.value.files)
        payload.append('tanggal_surat', form.tanggal_surat)

        axios.post(`${url}/incoming-letters/${form.id}`, payload)
            .then(res => {
                loading.value.close()
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Kategori berhasil diubah!',
                })
                table.value.getData()
                close_modal_form()
            })
            .catch(err => {
                loading.value.close()
                if (err.response.status == 422) {
                    modal_form.value.open()
                    errors.name = err.response.data.errors.name[0]
                } else {
                    console.log(err.response)
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Terjadi kesalahan, silahkan coba lagi!',
                    })
                    close_modal_form()
                }

            })
    } else {
        // create
        let payload = new FormData()
        payload.append('category', selected_category.value.id)
        payload.append('reference_number', form.reference_number)
        payload.append('asal_surat', form.asal_surat)
        payload.append('perihal', form.perihal)
        payload.append('keterangan', form.keterangan)
        payload.append('file_surat', file_surat.value.files)
        payload.append('tanggal_surat', form.tanggal_surat)

        axios.post(`${url}/incoming-letters`, payload)
            .then(res => {
                loading.value.close()
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Surat masuk berhasil ditambahkan!',
                })
                table.value.getData()
                close_modal_form()
            })
            .catch(err => {
                loading.value.close()
                if (err.response.status == 422) {
                    modal_form.value.open()
                    errors.name = err.response.data.errors.name[0]
                } else {
                    console.log(err.response)
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Terjadi kesalahan, silahkan coba lagi!',
                    })
                    close_modal_form()
                }

            })
    }
}

function delete_surat_masuk(id) {
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
                <h3 class="text-primary-400">List Surat Masuk</h3>
                <button @click="open_modal_form()" class="btn btn-primary">
                    <Icon class="text-lg" icon="fluent:add-12-filled" /> Tambah Surat Masuk
                </button>
            </div>
            <CustomTable ref="table" :thead="thead" :url="`${url}/incoming-letters`" v-slot="item">
                <td :class="[item.defaultClass, { disabled: item.show }]">{{ item.key }}</td>
                <td :class="[item.defaultClass, { disabled: item.show }]">{{ item.category.name }}</td>
                <td :class="[item.defaultClass, { disabled: item.show }]">{{ item.reference_number }}</td>
                <td :class="[item.defaultClass, { disabled: item.show }]">{{ item.asal_surat }}</td>
                <td :class="[item.defaultClass, { disabled: item.show }]">{{ item.perihal }}</td>
                <td :class="[item.defaultClass, { disabled: item.show }]">{{ item.keterangan }}</td>
                <td :class="[item.defaultClass, { disabled: item.show }]">
                    <RouterLink target="_blank" :to="{ name: 'file_archive_incoming_letter', params: { id: item.id } }" class="btn btn-primary w-[110px] text-center">Lihat Surat</RouterLink>
                </td>
                <td :class="[item.defaultClass, { disabled: item.show }]">{{ item.tanggal_surat_formated }}</td>
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
                                <span @click="open_modal_form(item)"
                                    class="text-primary-500 cursor-pointer flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm hover:bg-gray-100 focus:outline-none focus:bg-gray-100">
                                    <Icon class="text-lg" icon="cil:pencil"></Icon>
                                    Edit
                                </span>
                                <span @click="delete_surat_masuk(item.id)"
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
        <Modal ref="modal_form">
            <form @submit.prevent="save">
                <div class="p-4 sm:p-10 overflow-y-auto">
                    <div class="text-center mb-6">
                        <h3 class="mb-2 text-xl font-bold text-gray-800">
                            Form Tambah Surat Masuk
                        </h3>
                        <p class="text-gray-500">
                            Tambahkan surat masuk dengan mengisi form dibawah ini
                        </p>
                    </div>
                    <div class="mb-4">
                        <label for="input-label" class="block text-sm font-medium mb-2">Kategori</label>
                        <SearchInput v-if="!selected_category" v-model="selected_category"
                            :url="`${url}/incoming-letters/categories`" id="category" placeholder="Cari Kategori Surat ...">
                            <template v-slot="{ data }">
                                <p>{{ data.name }}</p>
                            </template>
                        </SearchInput>
                        <p v-if="errors.category" class="text-xs text-red-600 mt-2" id="category-error">
                            {{ errors.category }}
                        </p>
                        <div v-if="selected_category"
                            class="form-control bg-primary-200/20  mt-2 flex justify-between items-center gap-x-4">
                            <div class="w-full">
                                <p class="mb-0">{{ selected_category.name }}</p>
                            </div>
                            <span @click="reset_category"
                                class="p-3 hover:bg-red-200 rounded-full cursor-pointer transition ease-in-out duration-500">
                                <Icon icon="jam:trash" class="text-red-600 text-2xl" />
                            </span>
                        </div>
                    </div>
                    <div class="mb-4 grid grid-cols-2 gap-x-8">
                        <div>
                            <label for="input-label" class="block text-sm font-medium mb-2">Nomor Surat</label>
                            <input type="text" v-model="form.reference_number" class="form-control" required
                                placeholder="Nomor surat" />
                            <p v-if="errors.reference_number" class="text-xs text-red-600 mt-2" id="reference_number-error">
                                {{ errors.reference_number }}
                            </p>
                        </div>
                        <div>
                            <label for="input-label" class="block text-sm font-medium mb-2">Perihal</label>
                            <input type="text" v-model="form.perihal" class="form-control" required placeholder="Perihal" />
                            <p v-if="errors.perihal" class="text-xs text-red-600 mt-2" id="perihal-error">
                                {{ errors.perihal }}
                            </p>
                        </div>
                    </div>
                    <div class="mb-4 grid grid-cols-2 gap-x-8">
                        <div>
                            <label for="input-label" class="block text-sm font-medium mb-2">Pengirim</label>
                            <input type="text" v-model="form.asal_surat" class="form-control" required
                                placeholder="Pengirim" />
                            <p v-if="errors.asal_surat" class="text-xs text-red-600 mt-2" id="pengirim-error">
                                {{ errors.asal_surat }}
                            </p>
                        </div>
                        <div>
                            <label for="input-label" class="block text-sm font-medium mb-2">Tanggal Surat</label>
                            <input type="date" v-model="form.tanggal_surat" class="form-control" required
                                placeholder="Tanggal surat" />
                            <p v-if="errors.tanggal_surat" class="text-xs text-red-600 mt-2" id="tanggal_surat-error">
                                {{ errors.tanggal_surat }}
                            </p>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="input-label" class="block text-sm font-medium mb-2">Keterangan</label>
                        <textarea v-model="form.keterangan" class="form-control"
                            placeholder="Keterangan"></textarea>
                        <p v-if="errors.keterangan" class="text-xs text-red-600 mt-2" id="keterangan-error">
                            {{ errors.keterangan }}
                        </p>
                    </div>
                    <div class="mb-4">
                        <label for="input-label" class="block text-sm font-medium mb-2">File</label>
                        <UploadFile ref="file_surat" />
                        <p v-if="errors.file_surat" class="text-xs text-red-600 mt-2" id="file_surat-error">
                            {{ errors.file_surat }}
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