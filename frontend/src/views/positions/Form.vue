<script setup>
import { onMounted, reactive, ref } from 'vue'
import SubHeader from '@/components/SubHeader.vue'
import Loading from '@/components/Loading.vue'
import axios from 'axios'
import { useRoute } from 'vue-router';

const url = import.meta.env.VITE_URL_API
const route = useRoute()

const form = reactive({
    id: "",
    name: "",
    type: "struktural",
})

const errors = reactive({
    id: "",
    name: "",
    type: "",
})

const loading = ref(null)

async function get_data(id) {
    await axios.get(`${url}/positions/${id}`)
        .then(res => {
            let data = res.data.data
            form.id = data.id
            form.name = data.name
            form.type = data.type
        })
        .catch(err => {
            console.log(err)
        })

}

function reset_errors() {
    errors.id = ""
    errors.name = ""
    errors.type = ""

}

function reset() {
    form.id = ""
    form.name = ""
    form.type = "struktural"
    reset_errors()
}

function save() {
    loading.value.open()
    if (form.id != "") {
        axios.put(`${url}/positions/${form.id}`, form).then((res) => {
            loading.value.close()
            Swal.fire({
                icon: "success",
                title: "Berhasil",
                text: res.data.message,
            });
            // reset()
        }).catch(e => {
            loading.value.close()
            if (e.response.status == 422) {
                Object.entries(e.response.data.errors).forEach(entry => {
                    const [key, value] = entry;
                    errors[key] = value[0]
                });
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: "Silahkan isi form dengan benar",
                });
            } else {
                reset()
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: "Terjadi kesalahan, hubungi admin",
                });
                console.log(e)
            }
        })
    } else {
        axios.post(`${url}/positions`, form).then((res) => {
            loading.value.close()
            Swal.fire({
                icon: "success",
                title: "Berhasil",
                text: res.data.message,
            });
            reset()
        }).catch(e => {
            loading.value.close()
            if (e.response.status == 422) {
                Object.entries(e.response.data.errors).forEach(entry => {
                    const [key, value] = entry;
                    errors[key] = value[0]
                });
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: "Silahkan isi form dengan benar",
                });
            } else {
                reset()
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: "Terjadi kesalahan, hubungi admin",
                });
                console.log(e)
            }
        })
    }
}

onMounted(async () => {
    loading.value.open()
    if (route.name == 'update_positions') {
        await get_data(route.params.id)
    }
    loading.value.close()
})

</script>

<template>
    <loading ref="loading"></loading>
    <SubHeader :title="route.name == 'update_positions' ? `Edit Posisi / Jabatan` : `Tambah Posisi / Jabatan`"
        :back_url="{ name: 'positions' }" />
    <div class="flex flex-col bg-white rounded-lg">
        <div class="px-8 py-5 min-w-full inline-block align-middle">
            <form @submit.prevent="save">
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Nama <span class="text-red-400">*</span></label>
                    <input v-model="form.name" type="text" class="form-control" :class="{ 'border-red-500': errors.name }"
                        placeholder="Nama" required>
                    <p v-if="errors.name" class="text-xs text-red-600 mt-2" id="name-error">{{
                        errors.name }}</p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Jenis Jabatan <span class="text-red-400">*</span></label>
                    <select v-model="form.type" class="form-control" :class="{ 'border-red-500': errors.type }" required>
                        <option value="struktural">Struktural</option>
                        <option value="fungsional">Fungsional</option>
                    </select>
                    <p v-if="errors.type" class="text-xs text-red-600 mt-2" id="type-error">{{
                        errors.type }}</p>
                </div>
                <div class="flex justify-end gap-x-6">
                    <router-link :to="{ name: 'positions' }"
                        class="btn btn-outline border hover:border-primary-500 px-24 py-3">Kembali</router-link>
                    <button class="btn btn-primary px-24 py-3">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped></style>