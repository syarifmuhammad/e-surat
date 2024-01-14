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
    nama_prodi: "",
    singkatan_prodi: "",
    nama_fakultas: "",
    singkatan_fakultas: "",
})

const errors = reactive({
    id: "",
    nama_prodi: "",
    singkatan_prodi: "",
    nama_fakultas: "",
    singkatan_fakultas: "",
})

const loading = ref(null)

async function get_data(id) {
    await axios.get(`${url}/prodi/${id}`)
        .then(res => {
            let data = res.data.data
            form.id = data.id
            form.nama_prodi = data.nama_prodi
            form.singkatan_prodi = data.singkatan_prodi
            form.nama_fakultas = data.nama_fakultas
            form.singkatan_fakultas = data.singkatan_fakultas
        })
        .catch(err => {
            console.log(err)
        })

}

function reset_errors() {
    errors.id = ""
    errors.nama_prodi = ""
    errors.singkatan_prodi = ""
    errors.nama_fakultas = ""
    errors.singkatan_fakultas = ""
}

function reset() {
    form.id = ""
    form.nama_prodi = ""
    form.singkatan_prodi = ""
    form.nama_fakultas = ""
    form.singkatan_fakultas = ""
    reset_errors()
}

function save() {
    loading.value.open()
    if (form.id != "") {
        axios.put(`${url}/prodi/${form.id}`, form).then((res) => {
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
        axios.post(`${url}/prodi`, form).then((res) => {
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
    if (route.name == 'update_prodi') {
        await get_data(route.params.id)
    }
    loading.value.close()
})

</script>

<template>
    <loading ref="loading"></loading>
    <SubHeader :title="route.name == 'update_prodi' ? `Edit Jurusan / Prodi` : `Tambah Jurusan / Prodi`"
        :back_url="{ name: 'prodi' }" />
    <div class="flex flex-col bg-white rounded-lg">
        <div class="px-8 py-5 min-w-full inline-block align-middle">
            <form @submit.prevent="save">
                <div class="grid grid-cols-2 gap-x-4 mb-4">
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Nama Fakultas <span
                                class="text-red-400">*</span></label>
                        <input class="form-control" required v-model="form.nama_fakultas"
                            placeholder="cth: Fakultas Teknologi Informasi Dan Bisnis">
                        <p v-if="errors.nama_fakultas" class="text-xs text-red-600 mt-2">
                            {{ errors.nama_fakultas }}
                        </p>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Nama Singkatan Fakultas <span
                                class="text-red-400">*</span></label>
                        <input class="form-control" required v-model="form.singkatan_fakultas" placeholder="cth: FTIB">
                        <p v-if="errors.singkatan_fakultas" class="text-xs text-red-600 mt-2">
                            {{ errors.singkatan_fakultas }}
                        </p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-x-4 mb-4">
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Nama Prodi <span class="text-red-400">*</span></label>
                        <input class="form-control" required v-model="form.nama_prodi"
                            placeholder="cth: Rekayasa Perangkat Lunak">
                        <p v-if="errors.nama_prodi" class="text-xs text-red-600 mt-2">
                            {{ errors.nama_prodi }}
                        </p>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Nama Singkatan Prodi <span
                                class="text-red-400">*</span></label>
                        <input class="form-control" required v-model="form.singkatan_prodi" placeholder="cth: RPL">
                        <p v-if="errors.singkatan_prodi" class="text-xs text-red-600 mt-2">
                            {{ errors.singkatan_prodi }}
                        </p>
                    </div>
                </div>
                <div class="flex justify-end gap-x-6">
                    <router-link :to="{ name: 'prodi' }"
                        class="btn btn-outline border hover:border-primary-500 px-24 py-3">Kembali</router-link>
                    <button class="btn btn-primary px-24 py-3">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped></style>