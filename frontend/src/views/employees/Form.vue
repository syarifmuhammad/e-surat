<script setup>
import { reactive, ref } from 'vue'
import { Icon } from '@iconify/vue'
import SubHeader from '@/components/SubHeader.vue'
import SearchInput from '@/components/SearchInput.vue'
import Loading from '@/components/Loading.vue'
import axios from 'axios'

const url = import.meta.env.VITE_URL_API

const form = reactive({
    nip: "",
    name: "",
    email: "",
    positions: [],
})

const errors = reactive({
    nip: "",
    name: "",
    email: "",
    positions: "",
})

const loading = ref(null)

function reset_errors() {
    errors.nip = ""
    errors.name = ""
    errors.email = ""
    errors.positions = ""

}

function reset() {
    form.nip = ""
    form.name = ""
    form.email = ""
    form.positions = []
    reset_errors()
}

function save() {
    //check if form is valid
    if (form.positions.length < 1) {
        Swal.fire({
            icon: "error",
            title: "Gagal",
            text: "Jabatan tidak boleh kosong",
        });
        return
    }

    loading.value.open()
    axios.post(`${url}/employees`, form).then((res) => {
        if (res.status == 201) {
            Swal.fire({
                icon: "success",
                title: "Berhasil",
                text: res.data.message,
            });
            reset()
        }
    }).catch(e => {
        if (e.response.status == 422) {
            errors.nip = e.response.data.errors.nip[0]
            errors.name = e.response.data.errors.name[0]
            errors.email = e.response.data.errors.email[0]
            errors.positions = e.response.data.errors.positions[0]
        } else {
            console.log(e)
        }
    }).finally(() => {
        loading.value.close()
    })
}

</script>

<template>
    <loading ref="loading"></loading>
    <SubHeader :title="`Tambah Pegawai`">
        <!-- <button type="button" class="btn btn-outline-primary inline-flex gap-2 justify-center items-center">
            Pegawai
            <Icon class="text-lg" icon="fe:sync" />
        </button> -->
    </SubHeader>
    <div class="flex flex-col bg-white rounded-lg">
        <div class="px-8 py-5 min-w-full inline-block align-middle">
            <form @submit.prevent="save">
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">NIP <span class="text-red-400">*</span></label>
                    <input v-model="form.nip" type="text" class="form-control" :class="{ 'border-red-500': errors.nip }"
                        placeholder="NIP" required>
                    <p v-if="errors.nip" class="text-xs text-red-600 mt-2" id="nip-error">{{
                        errors.nip }}</p>
                </div>
                <div class="mb-4 grid grid-cols-2 gap-x-8">
                    <div>
                        <label class="block text-sm font-medium mb-2">Nama <span class="text-red-400">*</span></label>
                        <input v-model="form.name" type="text" class="form-control"
                            :class="{ 'border-red-500': errors.name }" placeholder="Nama" required>
                        <p v-if="errors.name" class="text-xs text-red-600 mt-2" id="name-error">{{
                            errors.name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Email <span class="text-red-400">*</span></label>
                        <input v-model="form.email" type="email" class="form-control"
                            :class="{ 'border-red-500': errors.name }" placeholder="Email" required>
                        <p v-if="errors.email" class="text-xs text-red-600 mt-2" id="email-error">{{
                            errors.email }}</p>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Jabatan <span class="text-red-400">*</span></label>
                    <search-input :url="`${url}/positions`" @selected="(data) => form.positions.push(data.name)"
                        @click_default_first="">
                        <template #default_first>
                            <p class="text-center"> + Tambah Data Jabatan</p>
                        </template>
                        <template v-slot="{ data }">
                            <p>{{ data.name }}</p>
                        </template>
                    </search-input>
                    <p v-if="errors.positions" class="text-xs text-red-600 mt-2" id="positions-error">{{
                        errors.positions }}</p>
                    <template v-if="form.positions.length > 0">
                        <div v-for="(position, index) in form.positions" :key="index"
                            class="form-control bg-primary-200/20  mt-2 flex justify-between items-center gap-x-4">
                            <div class="w-full">
                                <p>{{ position }}</p>
                            </div>
                            <span @click="form.positions.splice(index, 1)"
                                class="p-3 hover:bg-red-200 rounded-full cursor-pointer transition ease-in-out duration-500">
                                <Icon icon="jam:trash" class="text-red-600 text-2xl" />
                            </span>
                        </div>
                    </template>
                </div>
                <!-- <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Role User <span class="text-red-400">*</span></label>
                    <select v-model="form.roles" class="form-control" required>
                        <option value="pegawai">Pegawai</option>
                        <option value="admin_sdm">Admin Bagian SDM</option>
                        <option value="admin_sdm">Admin Bagian Sekretariat</option>
                    </select>
                </div> -->
                <div class="flex justify-end">
                    <button class="btn btn-primary px-24 py-3">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- <modal ref="modal_form_add_positions"></modal> -->
</template>

<style scoped></style>