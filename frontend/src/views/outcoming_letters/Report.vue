<script setup>
import { ref, reactive } from 'vue'
import { Icon } from '@iconify/vue'
import SubHeader from '@/components/SubHeader.vue'
import Loading from '@/components/Loading.vue';
import axios from 'axios'
import dayjs from 'dayjs'

const url = import.meta.env.VITE_URL_API
const loading = ref(null)

const form = reactive({
    jenis_surat: 'semua_surat',
    start_date: dayjs().startOf('month').format('YYYY-MM-DD'),
    end_date: dayjs().endOf('month').format('YYYY-MM-DD'),
})

const errors = reactive({
    jenis_surat: null,
    start_date: null,
    end_date: null,
})

function get_report() {
    let url = new URL(import.meta.env.VITE_URL_API.replace('/api', ''))
    url.pathname = '/outcoming-letters/report'
    url.searchParams.append('jenis_surat', form.jenis_surat)
    url.searchParams.append('start_date', form.start_date)
    url.searchParams.append('end_date', form.end_date)
    window.open(url.href, '_blank')
    // axios.get(`${url}/outcoming-letters/reports`, {
    //     params: {
    //         jenis_surat: form.jenis_surat,
    //         start_date: form.start_date,
    //         end_date: form.end_date,
    //     },
    //     responseType: 'blob',
    // }).then((res) => {
    //     loading.value.close()
    //     const url = window.URL.createObjectURL(new Blob([res.data]))
    //     const link = document.createElement('a')
    //     link.href = url
    //     link.setAttribute('download', 'report.pdf')
    //     document.body.appendChild(link)
    //     link.click()
    // }).catch((error) => {
    //     loading.value.close()
    //     console.log(error)
    // })
}
</script>
<template>
    <Loading ref="loading" />
    <!-- <SubHeader :title="``"></SubHeader> -->
    <div class="flex flex-col bg-white rounded-lg">
        <div class="px-8 py-5 min-w-full inline-block align-middle">
            <div class="flex justify-between mb-6">
                <h3 class="text-primary-400">Report Surat Keluar</h3>
            </div>
            <form @submit.prevent="get_report">
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Jenis Surat</label>
                    <select class="form-control" v-model="form.jenis_surat">
                        <option value="semua_surat">Semua Surat</option>
                        <option value="surat_keterangan_kerja">Surat Keterangan Kerja</option>
                        <option value="sk_rotasi_kepegawaian">Surat Keputusan Rotasi Kepegawaian</option>
                        <option value="sk_pemberhentian">Surat Keputusan Pemberhentian Dalam Jabatan</option>
                        <option value="sk_pengangkatan">Surat Keputusan Pengangkatan Dalam Jabatan</option>
                        <option value="sk_pemberhentian_dan_pengangkatan">Surat Keputusan Pemberhentian Dan Pengangkatan
                            Dalam Jabatan</option>
                        <option value="spk_magang">Surat Perjanjian Kerja Magang</option>
                        <option value="spk_dosen_luar_biasa">Surat Perjanjian Kerja Dosen Luar Biasa</option>
                        <option value="spk_dosen_full_time">Surat Perjanjian Kerja Dosen Full Time</option>
                    </select>
                    <p v-if="errors.jenis_surat" class="text-xs text-red-600 mt-2" id="letter-template-error">
                        {{ errors.jenis_surat }}
                    </p>
                </div>
                <div class="mb-4 flex gap-x-4">
                    <div class="w-full">
                        <label class="block text-sm font-medium mb-2">Dari Tanggal</label>
                        <input type="date" class="form-control" v-model="form.start_date">
                        <p v-if="errors.jenis_surat" class="text-xs text-red-600 mt-2" id="letter-template-error">
                            {{ errors.jenis_surat }}
                        </p>
                    </div>
                    <div class="w-full">
                        <label class="block text-sm font-medium mb-2">Sampai Tanggal</label>
                        <input type="date" class="form-control" v-model="form.end_date">
                        <p v-if="errors.jenis_surat" class="text-xs text-red-600 mt-2" id="letter-template-error">
                            {{ errors.jenis_surat }}
                        </p>
                    </div>
                </div>
                <div class="flex justify-end gap-x-6">
                    <router-link :to="{ name: 'surat_keterangan_kerja' }"
                        class="btn btn-outline border hover:border-primary-500 px-24 py-3">Kembali</router-link>
                    <button class="btn btn-primary px-24 py-3">Download Report</button>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped></style>