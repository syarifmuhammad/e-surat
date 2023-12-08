<script setup>
import { ref, reactive } from 'vue'
import Modal from "@/components/Modal.vue"
import Loading from "@/components/Loading.vue"
import axios from "axios"

const props = defineProps({
    employee: {
        type: Object,
        required: true
    }
})

const emit = defineEmits(["inserted"])

const url = import.meta.env.VITE_URL_API
const loading = ref(null)
const modal = ref(null)

const form_rekening = reactive({
    employee_id: props.employee.id,
    nama_bank: '',
    atas_nama: '',
    nomor_rekening: ''
})

const errors = reactive({
    nama_bank: '',
    atas_nama: '',
    nomor_rekening: ''
})

function save() {
    loading.value.open()
    axios.post(`${url}/employees/${form_rekening.employee_id}/rekening`, form_rekening).then(res => {
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
    errors.nama_bank = ''
    errors.atas_nama = ''
    errors.nomor_rekening = ''
}

function reset_form() {
    form_rekening.nama_bank = ''
    form_rekening.atas_nama = ''
    form_rekening.nomor_rekening = ''
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
                        Form Tambah Data Rekening
                    </h3>
                </div>
                <div class="overflow-auto max-h-[400px]">
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Nama Pegawai <span
                                class="text-red-400">*</span></label>
                        <input disabled class="form-control" required :value="props.employee.name">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Nama Bank <span class="text-red-400">*</span></label>
                        <input class="form-control" required v-model="form_rekening.nama_bank" placeholder="Nama Bank">
                        <p v-if="errors.nama_bank" class="text-xs text-red-600 mt-2">
                            {{ errors.nama_bank }}
                        </p>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Atas Nama <span class="text-red-400">*</span></label>
                        <input class="form-control" required v-model="form_rekening.atas_nama" placeholder="Atas Nama">
                        <p v-if="errors.atas_nama" class="text-xs text-red-600 mt-2">
                            {{ errors.atas_nama }}
                        </p>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Nomor Rekening <span
                                class="text-red-400">*</span></label>
                        <input class="form-control" required v-model="form_rekening.nomor_rekening"
                            placeholder="Nomor Rekening">
                        <p v-if="errors.nomor_rekening" class="text-xs text-red-600 mt-2">
                            {{ errors.nomor_rekening }}
                        </p>
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