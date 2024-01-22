<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import Loading from '@/components/Loading.vue'

const route = useRoute()
const url = import.meta.env.VITE_URL_API

const loading = ref(null)
const pdf = ref(null)

function get_pdf(id) {
    loading.value.open()
    axios.get(`${url}/outcoming-letters/surat-keputusan-pemberhentian/${id}/download/pdf`, {
        responseType: 'blob',
    }).then(res => {
        const url_pdf = window.URL.createObjectURL(res.data);
        pdf.value = url_pdf
    }).catch(err => {
        loading.value.close()
        Swal.fire({
            title: 'Gagal!',
            text: 'Surat gagal diunduh!',
            icon: 'error',
        })
    }).finally(() => {
        loading.value.close()
    })
}

onMounted(() => {
    get_pdf(route.params.id)
})

</script>
<template>
    <Loading ref="loading"></Loading>
    <iframe style="height: 100vh; width: 100%;"
        :src="pdf"></iframe>
</template>