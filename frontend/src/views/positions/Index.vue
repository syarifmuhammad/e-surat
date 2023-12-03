<script setup>
import { ref, reactive } from 'vue'
import { Icon } from '@iconify/vue'
import SubHeader from '@/components/SubHeader.vue'
import CustomTable from '@/components/CustomTable.vue'
import Modal from '@/components/Modal.vue'
import Loading from '@/components/Loading.vue'
import axios from 'axios'


const url = import.meta.env.VITE_URL_API

const thead = [
    "#",
    "Nama",
    "Aksi",
]

const loading = ref(null)
const table = ref(null)

</script>

<template>
    <Loading ref="loading" />
    <div class="flex flex-col bg-white rounded-lg">
        <div class="px-8 py-5 min-w-full inline-block align-middle">
            <div class="flex justify-between mb-6">
                <h3 class="text-primary-400">List Data Jabatan</h3>
                <router-link :to="{ name: 'create_positions' }" class="btn btn-primary">
                    <Icon class="text-lg" icon="fluent:add-12-filled" /> Tambah Jabatan
                </router-link>
            </div>
            <CustomTable ref="table" :thead="thead" :url="`${url}/positions`" v-slot="item">
                <td :class="[item.defaultClass]" class="w-2">{{ item.key }}</td>
                <td :class="[item.defaultClass]">{{ item.name }}</td>
                <td :class="[item.defaultClass]">
                    <button class="btn btn-info">
                        <Icon class="text-lg" icon="cil:pencil" />
                    </button>
                    <button class="btn btn-danger">
                        <Icon class="text-lg" icon="jam:trash" />
                    </button>
                </td>
            </CustomTable>
        </div>
    </div>
</template>

<style scoped></style>