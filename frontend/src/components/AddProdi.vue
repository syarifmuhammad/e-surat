<script setup>
import { ref, reactive } from 'vue'
import Modal from "@/components/Modal.vue"
import Loading from "@/components/Loading.vue"
import axios from "axios"

const emit = defineEmits(["inserted"])

const url = import.meta.env.VITE_URL_API
const loading = ref(null)
const modal = ref(null)

const form_prodi = reactive({
    nama_fakultas: '',
    singkatan_fakultas: '',
    nama_prodi: '',
    singkatan_prodi: '',
})

const errors = reactive({
    nama_fakultas: '',
    singkatan_fakultas: '',
    nama_prodi: '',
    singkatan_prodi: '',
})

function save() {
    loading.value.open()
    axios.post(`${url}/prodi`, form_prodi).then(res => {
        if (res.status == 201) {
            Swal.fire({
                icon: "success",
                title: "Berhasil",
                text: res.data.message,
            });
            loading.value.close()
            modal.value.close()
            emit('inserted', res.data.data)
        }
    }).catch(e => {
        if (e.response.status == 422) {
            Swal.fire({
                icon: "error",
                title: "Gagal",
                text: e.response.data.message,
            });
            loading.value.close()
            modal.value.open()
        } else {
            Swal.fire({
                icon: "error",
                title: "Gagal",
                text: "Kegagal pada server, silahkan coba lagi nanti",
            });
            loading.value.close()
            modal.value.close()
        }
    })

}

function reset_errors() {
    errors.nama_fakultas = ''
    errors.singkatan_fakultas = ''
    errors.nama_prodi = ''
    errors.singkatan_prodi = ''
}

function reset_form() {
    form_prodi.nama_fakultas = ''
    form_prodi.singkatan_fakultas = ''
    form_prodi.nama_prodi = ''
    form_prodi.singkatan_prodi = ''
    reset_errors()
}

function open() {
    modal.value.open()
}
function close() {
    modal.value.close()
}

function closeAndReset() {
    reset_form()
    close()
}

defineExpose({ open, close })
</script>
<template>
    <Loading ref="loading" />
    <Modal ref="modal" class="[--overlay-backdrop:static]" customClass="mt-40 hs-overlay-open:mt-52"
        data-hs-overlay-keyboard="false">
        <form @submit.prevent="save">
            <div class="p-4 pb-0 sm:p-10 sm:pb-0 h-full flex flex-col">
                <div class="text-center mb-6">
                    <h3 class="mb-2 text-xl font-bold text-gray-800">
                        Form Tambah Data Prodi
                    </h3>
                </div>
                <div class="overflow-auto max-h-[400px]">
                    <div class="grid grid-cols-2 gap-x-4 mb-4">
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Nama Fakultas <span
                                    class="text-red-400">*</span></label>
                            <input class="form-control" required v-model="form_prodi.nama_fakultas" placeholder="cth: Fakultas Teknologi Informasi Dan Bisnis">
                            <p v-if="errors.nama_fakultas" class="text-xs text-red-600 mt-2">
                                {{ errors.nama_fakultas }}
                            </p>
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">Nama Singkatan Fakultas <span class="text-red-400">*</span></label>
                                <input class="form-control" required v-model="form_prodi.singkatan_fakultas" placeholder="cth: FTIB">
                                <p v-if="errors.singkatan_fakultas" class="text-xs text-red-600 mt-2">
                                    {{ errors.singkatan_fakultas }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-x-4 mb-4">
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Nama Prodi <span
                                    class="text-red-400">*</span></label>
                            <input class="form-control" required v-model="form_prodi.nama_prodi" placeholder="cth: Rekayasa Perangkat Lunak">
                            <p v-if="errors.nama_prodi" class="text-xs text-red-600 mt-2">
                                {{ errors.nama_prodi }}
                            </p>
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">Nama Singkatan Prodi <span class="text-red-400">*</span></label>
                                <input class="form-control" required v-model="form_prodi.singkatan_prodi" placeholder="cth: RPL">
                                <p v-if="errors.singkatan_prodi" class="text-xs text-red-600 mt-2">
                                    {{ errors.singkatan_prodi }}
                                </p>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
            <div class="border-t p-4 sm:px-10 flex justify-end">
                <button type="button" class="btn btn-outline-primary px-14 py-3 mr-6" @click="closeAndReset">Batal</button>
                <button class="btn btn-primary px-14 py-3">Simpan</button>
            </div>
        </form>
    </Modal>
</template>