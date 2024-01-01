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
    nama: "",
    singkatan: "",
})

const errors = reactive({
    id: "",
    nama: "",
    singkatan: "",
})

const loading = ref(null)

async function get_data(id) {
    await axios.get(`${url}/unit/${id}`)
        .then(res => {
            let data = res.data.data
            form.id = data.id
            form.nama = data.nama
            form.singkatan = data.singkatan
        })
        .catch(err => {
            console.log(err)
        })

}

function reset_errors() {
    errors.id = ""
    errors.nama = ""
    errors.singkatan = ""
}

function reset() {
    form.id = ""
    form.nama = ""
    form.singkatan = ""
    reset_errors()
}

function save() {
    loading.value.open()
    if (form.id != "") {
        axios.put(`${url}/unit/${form.id}`, form).then((res) => {
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
        axios.post(`${url}/unit`, form).then((res) => {
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
    if (route.name == 'update_unit') {
        await get_data(route.params.id)
    }
    loading.value.close()
})

</script>

<template>
    <loading ref="loading"></loading>
    <SubHeader :title="route.name == 'update_unit' ? `Edit Unit Kerja` : `Tambah Unit Kerja`"
        :back_url="{ name: 'unit' }" />
    <div class="flex flex-col bg-white rounded-lg">
        <div class="px-8 py-5 min-w-full inline-block align-middle">
            <form @submit.prevent="save">
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Nama Unit Kerja <span class="text-red-400">*</span></label>
                    <input class="form-control" required v-model="form.nama"
                        placeholder="cth: Lembaga Penelitian & Pengabdian Masyarakat">
                    <p v-if="errors.nama" class="text-xs text-red-600 mt-2">
                        {{ errors.nama }}
                    </p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Nama Singkatan Unit Kerja <span
                            class="text-red-400">*</span></label>
                    <input class="form-control" required v-model="form.singkatan" placeholder="cth: LPPM">
                    <p v-if="errors.singkatan" class="text-xs text-red-600 mt-2">
                        {{ errors.singkatan }}
                    </p>
                </div>
                <div class="flex justify-end">
                    <button class="btn btn-primary px-24 py-3">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped></style>