<script setup>
import { onMounted, reactive, ref } from 'vue'
import { Icon } from '@iconify/vue'
import SubHeader from '@/components/SubHeader.vue'
import SearchInput from '@/components/SearchInput.vue'
import Loading from '@/components/Loading.vue'
import axios from 'axios'
import AddRekening from '@/components/AddRekening.vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const url = import.meta.env.VITE_URL_API
const modal_add_rekening = ref(null)

const form = reactive({
    id: "",
    nip: "",
    nik: "",
    name: "",
    email: "",
    profesi: "",
    tempat_lahir: "",
    tanggal_lahir: "",
    alamat: "",
    npwp: "",
    rekening: [],
    positions: [],
})

const errors = reactive({
    nip: "",
    nik: "",
    name: "",
    email: "",
    profesi: "",
    tempat_lahir: "",
    tanggal_lahir: "",
    alamat: "",
    npwp: "",
    rekening: "",
    positions: "",
})

const loading = ref(null)

async function get_letter(id) {
    await axios.get(`${url}/employees/${id}`)
        .then(res => {
            let data = res.data.data
            form.id = data.id
            form.nip = data.nip
            form.nik = data.nik
            form.name = data.name
            form.email = data.email
            form.profesi = data.profesi
            form.tempat_lahir = data.tempat_lahir
            form.tanggal_lahir = data.tanggal_lahir
            form.alamat = data.alamat
            form.npwp = data.npwp
            form.rekening = data.rekening
            form.positions = data.positions
        })
        .catch(err => {
            console.log(err)
        })

}

function open_modal_add_rekening() {
    modal_add_rekening.value.open()
}

function reset_errors() {
    Object.keys(errors).forEach(key => {
        errors[key] = ""
    })
}

function reset() {
    form.id = ""
    form.nip = ""
    form.nik = ""
    form.name = ""
    form.email = ""
    form.profesi = ""
    form.tempat_lahir = ""
    form.tanggal_lahir = ""
    form.alamat = ""
    form.npwp = ""
    form.rekening = []
    form.positions = []
    reset_errors()
}

function add_rekening(rekening) {
    form.rekening.push({
        nama_bank: rekening.nama_bank,
        atas_nama: rekening.atas_nama,
        nomor_rekening: rekening.nomor_rekening,
    })
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

    if (form.rekening.length < 1) {
        Swal.fire({
            icon: "error",
            title: "Gagal",
            text: "Rekening tidak boleh kosong",
        });
        return
    }

    const checkSet = new Set(form.positions);
    if (checkSet.size !== form.positions.length) {
        Swal.fire({
            icon: "error",
            title: "Gagal",
            text: "Jabatan tidak boleh sama",
        });
        return
    }

    reset_errors()

    loading.value.open()

    if (route.name == 'update_employees') {
        axios.put(`${url}/employees/${form.id}`, form).then((res) => {
            if (res.status == 200) {
                Swal.fire({
                    icon: "success",
                    title: "Berhasil",
                    text: res.data.message,
                });
            }
        }).catch(e => {
            if (e.response.status == 422) {
                Object.entries(e.response.data.errors).forEach(([key, value]) => {
                    errors[key] = value[0]
                })
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: "Silahkan periksa kembali form anda"
                });
            } else {
                console.log(e)
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: "Terjadi kesalahan",
                });
            }
        }).finally(() => {
            loading.value.close()
        })
        return
    } else {
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
                Object.entries(e.response.data.errors).forEach(([key, value]) => {
                    errors[key] = value[0]
                })
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: "Silahkan periksa kembali form anda"
                });
            } else {
                console.log(e)
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: "Terjadi kesalahan",
                });
            }
        }).finally(() => {
            loading.value.close()
        })
    }
}

onMounted(async () => {
    if (route.name == 'update_employees') {
        loading.value.open()
        await get_letter(route.params.id)
        loading.value.close()
    }
})

</script>

<template>
    <loading ref="loading"></loading>
    <SubHeader :title="`Tambah Pegawai`" :back_url="{ name: 'employees' }">
        <!-- <button type="button" class="btn btn-outline-primary inline-flex gap-2 justify-center items-center">
            Pegawai
            <Icon class="text-lg" icon="fe:sync" />
        </button> -->
    </SubHeader>
    <div class="flex flex-col bg-white rounded-lg">
        <div class="px-8 py-5 min-w-full inline-block align-middle">
            <form @submit.prevent="save">
                <div class="grid grid-cols-2 mb-4 gap-x-8">
                    <div>
                        <label class="block text-sm font-medium mb-2">NIP <span class="text-red-400">*</span></label>
                        <input v-model="form.nip" type="text" class="form-control" :class="{ 'border-red-500': errors.nip }"
                            placeholder="NIP" required>
                        <p v-if="errors.nip" class="text-xs text-red-600 mt-2" id="nip-error">{{
                            errors.nip }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">NIK <span class="text-red-400">*</span></label>
                        <input v-model="form.nik" type="text" class="form-control" :class="{ 'border-red-500': errors.nik }"
                            placeholder="NIK" required>
                        <p v-if="errors.nik" class="text-xs text-red-600 mt-2" id="nik-error">{{
                            errors.nik }}</p>
                    </div>
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
                            :class="{ 'border-red-500': errors.email }" placeholder="Email" required>
                        <p v-if="errors.email" class="text-xs text-red-600 mt-2" id="email-error">{{
                            errors.email }}</p>
                    </div>
                </div>
                <div class="mb-4 grid grid-cols-2 gap-x-8">
                    <div>
                        <label class="block text-sm font-medium mb-2">Tempat Lahir <span
                                class="text-red-400">*</span></label>
                        <input v-model="form.tempat_lahir" type="text" class="form-control"
                            :class="{ 'border-red-500': errors.tempat_lahir }" placeholder="Tempat Lahir" required>
                        <p v-if="errors.tempat_lahir" class="text-xs text-red-600 mt-2" id="tempat_lahir-error">{{
                            errors.tempat_lahir }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Tanggal Lahir <span
                                class="text-red-400">*</span></label>
                        <input v-model="form.tanggal_lahir" type="date" class="form-control"
                            :class="{ 'border-red-500': errors.tanggal_lahir }" placeholder="Tanggal Lahir" required>
                        <p v-if="errors.tanggal_lahir" class="text-xs text-red-600 mt-2" id="tanggal_lahir-error">{{
                            errors.tanggal_lahir }}</p>
                    </div>
                </div>
                <div class="mb-4 grid grid-cols-2 gap-x-8">
                    <div>
                        <label class="block text-sm font-medium mb-2">Profesi <span class="text-red-400">*</span></label>
                        <select v-model="form.profesi" class="form-control" :class="{ 'border-red-500': errors.profesi }"
                            required>
                            <option value="dosen">DOSEN</option>
                            <option value="tpa">TPA</option>
                        </select>
                        <p v-if="errors.profesi" class="text-xs text-red-600 mt-2" id="profesi-error">{{
                            errors.profesi }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">NPWP <span class="text-red-400">*</span></label>
                        <input v-model="form.npwp" type="text" class="form-control"
                            :class="{ 'border-red-500': errors.npwp }" placeholder="NPWP" required>
                        <p v-if="errors.npwp" class="text-xs text-red-600 mt-2" id="npwp-error">{{
                            errors.npwp }}</p>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Alamat <span class="text-red-400">*</span></label>
                    <textarea v-model="form.alamat" class="form-control" :class="{ 'border-red-500': errors.alamat }"
                        placeholder="Alamat" required></textarea>
                    <p v-if="errors.alamat" class="text-xs text-red-600 mt-2" id="alamat-error">{{
                        errors.alamat }}</p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Rekening <span class="text-red-400">*</span></label>
                    <p class="text-center text-primary cursor-pointer w-full form-control" @click="open_modal_add_rekening">
                        + Tambah Data Rekening
                    </p>
                    <p v-if="errors.rekening" class="text-xs text-red-600 mt-2" id="positions-error">{{
                        errors.rekening }}</p>
                    <template v-if="form.rekening.length > 0">
                        <div v-for="(rekening, index) in form.rekening" :key="index"
                            class="form-control bg-primary-200/20  mt-2 flex justify-between items-center gap-x-4">
                            <div class="w-full">
                                <small>{{ rekening.nama_bank }}</small>
                                <p class="mb-0">{{ rekening.atas_nama }}</p>
                                <p class="mb-0">{{ rekening.nomor_rekening }}</p>
                            </div>
                            <span @click="form.rekening.splice(index, 1)"
                                class="p-3 hover:bg-red-200 rounded-full cursor-pointer transition ease-in-out duration-500">
                                <Icon icon="jam:trash" class="text-red-600 text-2xl" />
                            </span>
                        </div>
                    </template>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Jabatan <span class="text-red-400">*</span></label>
                    <search-input :url="`${url}/positions`" placeholder="Jabatan"
                        @selected="(data) => form.positions.push(data.name)" @click_default_first="">
                        <!-- <template #default_first>
                            <p class="text-center"> + Tambah Data Jabatan</p>
                        </template> -->
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
                            <span v-if="form.positions.length > 1" @click="form.positions.splice(index, 1)"
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
                        <option value="admin_unit">Admin Bagian SDM</option>
                        <option value="admin_unit">Admin Bagian Sekretariat</option>
                    </select>
                </div> -->
                <div class="flex justify-end gap-x-6">
                    <router-link :to="{ name: 'employees' }"
                        class="btn btn-outline border hover:border-primary-500 px-24 py-3">Kembali</router-link>
                    <button class="btn btn-primary px-24 py-3">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    <AddRekening ref="modal_add_rekening" :save_to_api="false" @inserted="add_rekening" />
    <!-- <modal ref="modal_form_add_positions"></modal> -->
</template>

<style scoped></style>