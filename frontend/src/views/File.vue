<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, RouterLink } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const url = import.meta.env.VITE_URL_API

const loading = ref(false)
const pdf = ref(null)
const is_private = ref(false)

function get_pdf() {
    loading.value = true
    axios.get(`${url}/file/${route.params.token}`, {
        responseType: 'blob',
    }).then(res => {
        const url_pdf = window.URL.createObjectURL(res.data);
        pdf.value = url_pdf
    }).catch(err => {
        // loading.value.close()
        Swal.fire({
            title: 'Gagal!',
            text: 'Surat gagal diunduh!',
            icon: 'error',
        })

        if (err.response.status == 403) {
            is_private.value = true
        }
    }).finally(() => {
        loading.value = false
    })
}

onMounted(() => {
    get_pdf()
})

</script>
<template>
    <iframe v-if="!is_private && !loading" style="height: 100vh; width: 100%;" :src="pdf"></iframe>
    <div v-else-if="is_private && !loading" class="flex flex-col items-center justify-center h-full">
        <div class="flex flex-col items-center justify-center">
            <h1 class="text-2xl font-bold text-gray-700">Surat ini bersifat pribadi</h1>
            <p class="text-gray-500">Silahkan <RouterLink :to="{ name: 'login' }">login</RouterLink> untuk melihat surat</p>
        </div>
    </div>
    <div v-else class="flex flex-col items-center justify-center h-full">
        <div class="flex flex-col items-center justify-center">
            <h1 class="text-2xl font-bold text-gray-700">Loading...</h1>
        </div>
    </div>
</template>