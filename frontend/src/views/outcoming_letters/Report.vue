<script setup>
import { ref, reactive } from 'vue'
import { Icon } from '@iconify/vue'
import SubHeader from '@/components/SubHeader.vue'
import CustomTable from '@/components/CustomTable.vue';
import Modal from '@/components/Modal.vue';
import Loading from '@/components/Loading.vue';
import axios from 'axios'

const url = import.meta.env.VITE_URL_API
const loading = ref(null)
const table = ref(null)
const modal_form_category = ref(null)

const thead = [
    "#",
    "Tanggal",
    "Jumlah Surat Belum Selesai",
    "Jumlah Surat Selesai",
    "Aksi",
]
</script>
<template>
    <Loading ref="loading" />
    <!-- <SubHeader :title="``"></SubHeader> -->
    <div class="flex flex-col bg-white rounded-lg">
        <div class="px-8 py-5 min-w-full inline-block align-middle">
            <div class="flex justify-between mb-6">
                <h3 class="text-primary-400">List Report Surat Keluar</h3>
                <!-- <button @click="open_modal_form_category()" class="btn btn-primary">
                    <Icon class="text-lg" icon="fluent:add-12-filled" /> Tambah Kategori
                </button> -->
            </div>
            <CustomTable ref="table" :thead="thead" :url="`${url}/incoming-letters/categories`" v-slot="item">
                <td :class="[item.defaultClass, { disabled: item.show }]">{{ item.key }}</td>
                <td :class="[item.defaultClass, { disabled: item.show }]">{{ item.name }}</td>
                <td :class="[item.defaultClass, { disabled: item.show }]">{{ item.count }}</td>
            </CustomTable>
        </div>
    </div>
</template>

<style scoped></style>