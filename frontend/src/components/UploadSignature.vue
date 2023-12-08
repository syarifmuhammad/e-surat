<script setup>
import { ref } from 'vue'
import Modal from '@/components/Modal.vue'
import UploadFile from '@/components/UploadFile.vue'
import Loading from '@/components/Loading.vue'
import axios from 'axios'

const emit = defineEmits(['success'])

const url = import.meta.env.VITE_URL_API
const loading = ref(null)
const modal_upload_signature = ref(null)
const signature = ref(null)

function save_signature() {
    loading.value.open()
    const formData = new FormData()
    formData.append('signature', signature.value.files)
    formData.append('_method', 'PUT')
    axios.post(`${url}/signature`, formData, {
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    }).then(response => {
        modal_upload_signature.value.close()
        emit('success')
    }).catch(error => {
        console.log(error)
    }).finally(() => {
        loading.value.close()
    })
}

function open() {
    modal_upload_signature.value.open()
}

function close() {
    modal_upload_signature.value.close()
}

defineExpose({
    open,
    close
})
</script>
<template>
    <Loading ref="loading"></Loading>
    <Modal ref="modal_upload_signature" :customClass="'sm:max-w-[700px]'">
        <form @submit.prevent="save_signature">
            <div class="p-4 pb-0 sm:p-10 sm:pb-0 h-full flex flex-col">
                <div class="text-center mb-6">
                    <h3 class="mb-2 text-xl font-bold text-gray-800">
                        Form Upload Tanda Tangan
                    </h3>
                </div>
                <div class="mb-4 rounded-md overflow-hidden" style="box-shadow: 0 0.25rem 1rem #a1acb873;">
                    <label class="block text-sm font-medium p-4">File Tanda Tangan <span
                            class="text-red-400">*</span></label>
                    <UploadFile ref="signature" :accepted_file_type="['image/png', 'image/jpg', 'image/jpeg']"></UploadFile>
                </div>
            </div>
            <div class="border-t p-4 sm:px-10 flex justify-end">
                <button type="button" @click="modal_upload_signature.close()"
                    class="btn btn-outline-primary px-14 py-3 mr-6">Batal</button>
                <button class="btn btn-primary px-14 py-3">Simpan</button>
            </div>
        </form>
    </Modal>
</template>