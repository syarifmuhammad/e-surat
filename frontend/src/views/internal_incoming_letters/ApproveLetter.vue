<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import Loading from '@/components/Loading.vue'
import SubHeader from '@/components/SubHeader.vue'
import Modal from '@/components/Modal.vue';

const route = useRoute()
const router = useRouter()
const url = import.meta.env.VITE_URL_API

const loading = ref(null)
const loading_pdf = ref(true)
const pdf = ref(null)
const modal_approve = ref(null)

function get_pdf(id) {
    loading_pdf.value = true
    axios.get(`${url}/incoming-letters/${route.meta.api_letter}/${id}/download/pdf`, {
        responseType: 'blob',
    }).then(res => {
        const url_pdf = window.URL.createObjectURL(res.data);
        pdf.value = url_pdf
    }).catch(err => {
        loading_pdf.value = false
        Swal.fire({
            title: 'Gagal!',
            text: 'Surat gagal diunduh!',
            icon: 'error',
        })
    }).finally(() => {
        loading_pdf.value = false
    })
}

function open_modal_approve() {
    modal_approve.value.open()
}

function sign() {
    loading.value.open()
    let payload = {
        _method: 'PUT',
    }
    axios.put(`${url}/incoming-letters/${route.meta.api_letter}/${route.params.id}/approve`, payload).then(res => {
        loading.value.close()
        Swal.fire({
            title: 'Berhasil!',
            text: 'Surat berhasil disetujui!',
            icon: 'success',
        })
        router.push({ name: route.meta.parentName })
    }).catch(err => {
        loading.value.close()
        Swal.fire({
            title: 'Gagal!',
            text: 'Surat gagal disetujui!',
            icon: 'error',
        })
    })

}

onMounted(() => {
    get_pdf(route.params.id)
})

</script>
<template>
    <Loading ref="loading"></Loading>
    <SubHeader :title="route.meta.title" :back_url="{ name: route.meta.parentName }" />
    <div class="flex flex-col bg-white rounded-lg">
        <div class="min-w-full inline-block align-middle">
            <div class="flex justify-end px-5 py-5">
                <button :disabled="loading_pdf" class="btn btn-primary" @click="open_modal_approve">Setujui Surat</button>
            </div>
            <p v-if="loading_pdf" class="text-center animate-pulse mb-8">Surat sedang diunduh ...</p>
            <iframe v-else class="w-full min-h-[800px] h-full" :src="pdf"></iframe>
        </div>
    </div>

    <Modal ref="modal_approve" :customClass="'sm:max-w-[700px]'">
        <form @submit.prevent="sign">
            <div class="p-4 pb-0 sm:p-10 sm:pb-0 h-full flex flex-col">
                <div class="text-center mb-6">
                    <h3 class="mb-2 text-xl font-bold text-gray-800">
                        Setujui Surat
                    </h3>
                </div>
                <p class="text-center mb-4 text-blue-500">Surat akan dilanjutkan ke pemeriksa / penandatangan selanjutnya !
                </p>
                <div class="border-t p-4 sm:px-10 flex justify-center">
                    <button type="button" @click="modal_approve.close()"
                        class="btn btn-outline-primary px-14 py-3 mr-6">Batal</button>
                    <button class="btn btn-primary px-14 py-3">Setujui</button>
                </div>
            </div>
        </form>
    </Modal>
</template>