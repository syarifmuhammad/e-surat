<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRoute, RouterLink } from 'vue-router'
import axios from 'axios'
import { Icon } from '@iconify/vue';

const url = import.meta.env.VITE_URL_API
const route = useRoute()
const loading = ref(false)

const not_valid = ref(false)
const letter = reactive({
    is_private: false,
    reference_number: '',
    valid_until_date: '',
    valid: false,
    signers: []
})

async function getLetter() {
    await axios.get(`${url}/verify/${route.params.token}`)
        .then(res => {
            letter.is_private = res.data.data.is_private
            letter.reference_number = res.data.data.reference_number
            letter.valid_until_date = res.data.data.valid_until_date
            letter.valid = res.data.data.valid
            letter.signers = res.data.data.signers
        })
        .catch(err => {
            not_valid.value = true
            console.log(err)
        })
}

onMounted(async () => {
    loading.value = true
    await getLetter()
    loading.value = false
})
</script>
<template>
    <div class="md:w-7/12 w-full m-auto" v-if="!not_valid && !loading">
        <div class="text-center mb-5">
            <img class="w-[150px] mx-auto"
                src="https://surabaya.telkomuniversity.ac.id/wp-content/uploads/2023/09/TELKOM-UNIVERSITY-LOGO-HORIZONTAL.png">
            <h3 class="my-4">
                Verifikasi Tanda Tangan Elektronik
                <small class="text-sm block font-light italic text-gray-400 mt-1">Digital Signature Verification</small>
            </h3>
            <h5>
                Telkom University Surabaya menyatakan bahwa:
                <small class="text-sm block font-light italic text-gray-400 mt-1">Telkom University Surabaya states
                    that:</small>
            </h5>
        </div>
        <div class="bg-white p-12 rounded-md shadow-md mb-4">
            <h3 v-if="letter.valid" class="text-center text-green-500"><Icon class="inline text-green-500 mb-1" icon="octicon:verified-16" /> Masih Berlaku</h3>
            <h3 v-else class="text-center text-red-500"><Icon class="inline text-red-500 mb-1" icon="octicon:x-circle-16" /> Sudah Tidak Berlaku</h3>
            <span>
                Nomor Surat:
                <small class="text-sm block font-light italic text-gray-400 mb-1">Reference Number:</small>
            </span>
            <p class="font-bold mb-4">
                {{ letter.reference_number }}
            </p>
            <template v-if="letter.valid_until_date">
                <span>
                    Berlaku Sampai Tanggal:
                    <small class="text-sm block font-light italic text-gray-400 mb-1">Valid Until Date:</small>
                </span>
                <p class="font-bold mb-4">
                    {{ letter.valid_until_date }}
                </p>
            </template>
            <p class="mb-2">
                Telah ditandatangani oleh pengguna E-Surat sebagai berikut:
                <small class="text-sm block font-light italic text-gray-400">Has been signed by E-Surat user as
                    follows:</small>
            </p>
            <div v-for="signer in letter.signers" class="mt-3 px-3 py-4 bg-gray-200 rounded-lg w-full ">
                <h3>
                    {{ signer.name }}
                </h3>
                <div>
                    <span class="text-gray-400 block font-weight-normal h-300">Timestamp:</span>
                    <p>
                        {{ signer.timestamp }}
                    </p>
                </div>
            </div>
            <div class="text-center mt-5">
                <h4 class="font-weight-bolder h-500 mb-4">
                    <span class="inline">
                        <Icon class="inline text-green-500" icon="octicon:verified-16" /> Adalah benar dan tercatat dalam audit trail kami.
                    </span>
                    <small class="text-sm block font-light italic text-gray-400">That is true and it is recorded in our
                        audit
                        trail.</small>
                </h4>
                <button class="btn btn-primary mx-auto my-3 px-12 py-3" v-if="!letter.is_private">Lihat Surat</button>
                <RouterLink class="btn btn-primary mx-auto my-3 px-12 py-3" :to="{ name: 'login' }" v-else>Login Untuk melihat surat</RouterLink>
                <p class="text-sm small mb-0">
                    Untuk memastikan kebenaran pernyataan ini pastikan URL pada browser anda adalah https://localhost:5173/
                    <small class="block font-light italic text-gray-400">If you wish to check the validity of this
                        statement,
                        please ensure the URL of your browser is https://localhost:5173/</small>
                </p>
            </div>
        </div>
    </div>
    <div v-else-if="loading" class="w-full h-full flex justify-center items-center">
        <h1 class="text-primary-500 animate-pulse">Loading ...</h1>
    </div>
    <div v-else class="w-full h-full flex justify-center items-center">
        <h1 class="text-primary-500">Dokumen tidak valid</h1>
    </div>
</template>