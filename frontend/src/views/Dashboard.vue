<script setup>
import { ref, computed, onMounted } from 'vue'
import { Icon } from '@iconify/vue'
import Loading from '@/components/Loading.vue';
import axios from 'axios'
import { useUserStore } from '@/stores/user';
import { RouterLink } from 'vue-router';

const user = computed(() => {
  return useUserStore().user
})

const url = import.meta.env.VITE_URL_API
const loading = ref(null)
const analytics = ref({
  surat_keterangan_kerja: {
    count: 0,
    need_reference_number: 0,
    need_signed: 0,
  },
  sk_rotasi_kepegawaian: {
    count: 0,
    need_reference_number: 0,
    need_signed: 0,
  },
  sk_pemberhentian: {
    count: 0,
    need_reference_number: 0,
    need_signed: 0,
  },
  sk_pengangkatan: {
    count: 0,
    need_reference_number: 0,
    need_signed: 0,
  },
  sk_pemberhentian_dan_pengangkatan: {
    count: 0,
    need_reference_number: 0,
    need_signed: 0,
  },
  spk_magang: {
    count: 0,
    need_reference_number: 0,
    need_signed: 0,
  },
  spk_dosen_luar_biasa: {
    count: 0,
    need_reference_number: 0,
    need_signed: 0,
  },
  spk_dosen_full_time: {
    count: 0,
    need_reference_number: 0,
    need_signed: 0,
  },
  
})

function is_need_reference_number(letter) {
  return analytics.value[letter].need_reference_number > 0
}

function is_need_signed(letter) {
  return analytics.value[letter].need_signed > 0
}

function is_need_to_check(letter) {
  return is_need_reference_number(letter) || is_need_signed(letter)
}

async function get_analytics() {
  await axios.get(`${url}/analytics`).then(res => {
    analytics.value = res.data
  })
}

onMounted(async () => {
  await get_analytics()
})
</script>
<template>
  <Loading ref="loading" />
  <!-- <SubHeader :title="``"></SubHeader> -->
  <main>
    <div class="bg-white rounded-lg px-8 py-5 mb-4">
      <h3 class="text-primary">Selamat Datang {{ user.name }}! 🎉</h3>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <RouterLink :to="{ name: 'surat_keterangan_kerja' }" class="rounded-lg bg-white p-6">
        <div class="flex justify-between items-center mb-2">
          <Icon class="text-4xl text-gray-500" icon="gg:file-document" />
          <span v-if="is_need_to_check('surat_keterangan_kerja')" class="text-red-500">Perlu tindak lanjut</span>
        </div>
        <span class="font-semibold text-gray-500">Surat Keterangan Kerja</span>
        <h1 class="mb-2 text-4xl">{{analytics.surat_keterangan_kerja.count}}</h1>
        <div class="flex gap-x-4 mb-4">
          <div class="flex items-center text-red-500 text-2xl">
            <Icon icon="bx:task-x" />
            <span class="ms-1">{{ 
              analytics.surat_keterangan_kerja.need_signed }}</span>
          </div>
          <div class="flex items-center text-green-500 text-2xl">
            <Icon icon="bx:task" />
            <span class="ms-1">{{ analytics.surat_keterangan_kerja.count - analytics.surat_keterangan_kerja.need_signed
            }}</span>
          </div>
        </div>
        <div>
          <button class="btn btn-primary font-semibold flex items-center">
            <Icon class="text-lg" icon="bx:chart" />
            <span class="ms-1">Lihat Statistik</span>
          </button>
        </div>
      </RouterLink>
      <RouterLink :to="{ name: 'surat_keputusan_rotasi_kepegawaian' }" class="rounded-lg bg-white p-6">
        <div class="flex justify-between items-center mb-2">
          <Icon class="text-4xl text-gray-500" icon="gg:file-document" />
          <span v-if="is_need_to_check('sk_rotasi_kepegawaian')" class="text-red-500">Perlu tindak lanjut</span>
        </div>
        <span class="font-semibold text-gray-500">Surat Keputusan Rotasi Kepegawaian</span>
        <h1 class="mb-2 text-4xl">{{analytics.sk_rotasi_kepegawaian.count}}</h1>
        <div class="flex gap-x-4 mb-4">
          <div class="flex items-center text-red-500 text-2xl">
            <Icon icon="bx:task-x" />
            <span class="ms-1">{{ 
              analytics.sk_rotasi_kepegawaian.need_signed }}</span>
          </div>
          <div class="flex items-center text-green-500 text-2xl">
            <Icon icon="bx:task" />
            <span class="ms-1">{{ analytics.sk_rotasi_kepegawaian.count -
               analytics.sk_rotasi_kepegawaian.need_signed
            }}</span>
          </div>
        </div>
        <div>
          <button class="btn btn-primary font-semibold flex items-center">
            <Icon class="text-lg" icon="bx:chart" />
            <span class="ms-1">Lihat Statistik</span>
          </button>
        </div>
      </RouterLink>
      <RouterLink :to="{ name: 'surat_keputusan_pemberhentian' }" class="rounded-lg bg-white p-6">
        <div class="flex justify-between items-center mb-2">
          <Icon class="text-4xl text-gray-500" icon="gg:file-document" />
          <span v-if="is_need_to_check('sk_pemberhentian')" class="text-red-500">Perlu tindak lanjut</span>
        </div>
        <span class="font-semibold text-gray-500">Surat Keputusan Pemberhentian Dalam Jabatan</span>
        <h1 class="mb-2 text-4xl">{{analytics.sk_pemberhentian.count}}</h1>
        <div class="flex gap-x-4 mb-4">
          <div class="flex items-center text-red-500 text-2xl">
            <Icon icon="bx:task-x" />
            <span class="ms-1">{{ 
              analytics.sk_pemberhentian.need_signed }}</span>
          </div>
          <div class="flex items-center text-green-500 text-2xl">
            <Icon icon="bx:task" />
            <span class="ms-1">{{ analytics.sk_pemberhentian.count -
               analytics.sk_pemberhentian.need_signed
            }}</span>
          </div>
        </div>
        <div>
          <button class="btn btn-primary font-semibold flex items-center">
            <Icon class="text-lg" icon="bx:chart" />
            <span class="ms-1">Lihat Statistik</span>
          </button>
        </div>
      </RouterLink>
      <RouterLink :to="{ name: 'surat_keputusan_pengangkatan' }" class="rounded-lg bg-white p-6">
        <div class="flex justify-between items-center mb-2">
          <Icon class="text-4xl text-gray-500" icon="gg:file-document" />
          <span v-if="is_need_to_check('sk_pengangkatan')" class="text-red-500">Perlu tindak lanjut</span>
        </div>
        <span class="font-semibold text-gray-500">Surat Keputusan Pengangkatan Dalam Jabatan</span>
        <h1 class="mb-2 text-4xl">{{analytics.sk_pengangkatan.count}}</h1>
        <div class="flex gap-x-4 mb-4">
          <div class="flex items-center text-red-500 text-2xl">
            <Icon icon="bx:task-x" />
            <span class="ms-1">{{ 
              analytics.sk_pengangkatan.need_signed }}</span>
          </div>
          <div class="flex items-center text-green-500 text-2xl">
            <Icon icon="bx:task" />
            <span class="ms-1">{{ analytics.sk_pengangkatan.count -
               analytics.sk_pengangkatan.need_signed
            }}</span>
          </div>
        </div>
        <div>
          <button class="btn btn-primary font-semibold flex items-center">
            <Icon class="text-lg" icon="bx:chart" />
            <span class="ms-1">Lihat Statistik</span>
          </button>
        </div>
      </RouterLink>
      <RouterLink :to="{ name: 'surat_keputusan_pemberhentian_dan_pengangkatan' }" class="rounded-lg bg-white p-6">
        <div class="flex justify-between items-center mb-2">
          <Icon class="text-4xl text-gray-500" icon="gg:file-document" />
          <span v-if="is_need_to_check('sk_pemberhentian_dan_pengangkatan')" class="text-red-500">Perlu tindak lanjut</span>
        </div>
        <span class="font-semibold text-gray-500">Surat Keputusan Pemberhentian Dan Pengangkatan Dalam Jabatan</span>
        <h1 class="mb-2 text-4xl">{{analytics.sk_pemberhentian_dan_pengangkatan.count}}</h1>
        <div class="flex gap-x-4 mb-4">
          <div class="flex items-center text-red-500 text-2xl">
            <Icon icon="bx:task-x" />
            <span class="ms-1">{{ 
              analytics.sk_pemberhentian_dan_pengangkatan.need_signed }}</span>
          </div>
          <div class="flex items-center text-green-500 text-2xl">
            <Icon icon="bx:task" />
            <span class="ms-1">{{ analytics.sk_pemberhentian_dan_pengangkatan.count -
               analytics.sk_pemberhentian_dan_pengangkatan.need_signed
            }}</span>
          </div>
        </div>
        <div>
          <button class="btn btn-primary font-semibold flex items-center">
            <Icon class="text-lg" icon="bx:chart" />
            <span class="ms-1">Lihat Statistik</span>
          </button>
        </div>
      </RouterLink>
      <RouterLink :to="{ name: 'surat_perjanjian_kerja_magang' }" class="rounded-lg bg-white p-6">
        <div class="flex justify-between items-center mb-2">
          <Icon class="text-4xl text-gray-500" icon="gg:file-document" />
          <span v-if="is_need_to_check('spk_magang')" class="text-red-500">Perlu tindak lanjut</span>
        </div>
        <span class="font-semibold text-gray-500">Surat Perjanjian Kerja Magang</span>
        <h1 class="mb-2 text-4xl">{{analytics.spk_magang.count}}</h1>
        <div class="flex gap-x-4 mb-4">
          <div class="flex items-center text-red-500 text-2xl">
            <Icon icon="bx:task-x" />
            <span class="ms-1">{{ 
              analytics.spk_magang.need_signed }}</span>
          </div>
          <div class="flex items-center text-green-500 text-2xl">
            <Icon icon="bx:task" />
            <span class="ms-1">{{ analytics.spk_magang.count -
               analytics.spk_magang.need_signed
            }}</span>
          </div>
        </div>
        <div>
          <button class="btn btn-primary font-semibold flex items-center">
            <Icon class="text-lg" icon="bx:chart" />
            <span class="ms-1">Lihat Statistik</span>
          </button>
        </div>
      </RouterLink>
      <RouterLink :to="{ name: 'surat_perjanjian_kerja_dosen_luar_biasa' }" class="rounded-lg bg-white p-6">
        <div class="flex justify-between items-center mb-2">
          <Icon class="text-4xl text-gray-500" icon="gg:file-document" />
          <span v-if="is_need_to_check('spk_dosen_luar_biasa')" class="text-red-500">Perlu tindak lanjut</span>
        </div>
        <span class="font-semibold text-gray-500">Surat Perjanjian Kerja Dosen Luar Biasa</span>
        <h1 class="mb-2 text-4xl">{{analytics.spk_dosen_luar_biasa.count}}</h1>
        <div class="flex gap-x-4 mb-4">
          <div class="flex items-center text-red-500 text-2xl">
            <Icon icon="bx:task-x" />
            <span class="ms-1">{{ 
              analytics.spk_dosen_luar_biasa.need_signed }}</span>
          </div>
          <div class="flex items-center text-green-500 text-2xl">
            <Icon icon="bx:task" />
            <span class="ms-1">{{ analytics.spk_dosen_luar_biasa.count -
               analytics.spk_dosen_luar_biasa.need_signed
            }}</span>
          </div>
        </div>
        <div>
          <button class="btn btn-primary font-semibold flex items-center">
            <Icon class="text-lg" icon="bx:chart" />
            <span class="ms-1">Lihat Statistik</span>
          </button>
        </div>
      </RouterLink>
      <RouterLink :to="{ name: 'surat_perjanjian_kerja_dosen_full_time' }" class="rounded-lg bg-white p-6">
        <div class="flex justify-between items-center mb-2">
          <Icon class="text-4xl text-gray-500" icon="gg:file-document" />
          <span v-if="is_need_to_check('spk_dosen_full_time')" class="text-red-500">Perlu tindak lanjut</span>
        </div>
        <span class="font-semibold text-gray-500">Surat Perjanjian Kerja Dosen Full Time</span>
        <h1 class="mb-2 text-4xl">{{analytics.spk_dosen_full_time.count}}</h1>
        <div class="flex gap-x-4 mb-4">
          <div class="flex items-center text-red-500 text-2xl">
            <Icon icon="bx:task-x" />
            <span class="ms-1">{{ 
              analytics.spk_dosen_full_time.need_signed }}</span>
          </div>
          <div class="flex items-center text-green-500 text-2xl">
            <Icon icon="bx:task" />
            <span class="ms-1">{{ analytics.spk_dosen_full_time.count -
               analytics.spk_dosen_full_time.need_signed
            }}</span>
          </div>
        </div>
        <div>
          <button class="btn btn-primary font-semibold flex items-center">
            <Icon class="text-lg" icon="bx:chart" />
            <span class="ms-1">Lihat Statistik</span>
          </button>
        </div>
      </RouterLink>
    </div>
  </main>
</template>

<style scoped></style>