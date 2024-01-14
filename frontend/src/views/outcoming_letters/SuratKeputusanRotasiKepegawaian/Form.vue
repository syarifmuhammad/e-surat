<script setup>
import { ref, reactive, onMounted } from 'vue'
import { Icon } from '@iconify/vue'
import axios from 'axios'
import SubHeader from '@/components/SubHeader.vue'
import SearchInput from '@/components/SearchInput.vue';
import CustomSelect from '@/components/CustomSelect.vue';
import { useRoute } from 'vue-router';

const route = useRoute()
const url = import.meta.env.VITE_URL_API
const NAMA_SURAT = "SURAT_KEPUTUSAN_ROTASI_KEPEGAWAIAN"

const loading = ref(null)

const form_surat = reactive({
    id: "",
    letter_template_id: "",
    tanggal_surat: new Date().toISOString().slice(0, 10),
    nomor_berita_acara: "",
    tanggal_berita_acara: "",
    employee: {
        status_awal: "",
        jabatan_awal: "",
        status_akhir: "",
        jabatan_akhir: "",
    },
    signer: {
        position: "",
    },
    signature_type: "manual",
    tanggal_berlaku: "",
})

const errors = reactive({
    letter_template_id: "",
    tanggal_surat: "",
    nomor_berita_acara: "",
    tanggal_berita_acara: "",
    "employee.id": "",
    "employee.status_awal": "",
    "employee.jabatan_awal": "",
    "employee.status_akhir": "",
    "employee.jabatan_akhir": "",
    "signer.id": "",
    "signer.position": "",
    signature_type: "",
    tanggal_berlaku: "",
})

const letter_templates = ref([])
const selected_employee = ref(null)
const selected_signer = ref(null)

async function get_letter_templates() {
    await axios.get(`${url}/outcoming-letters/templates?letter_type=${NAMA_SURAT}`)
        .then(res => {
            letter_templates.value = res.data.data
            form_surat.letter_template_id = letter_templates.value[0].id
        })
        .catch(err => {
            console.log(err)
        })
}

async function get_letter(id) {
    await axios.get(`${url}/outcoming-letters/surat-keputusan-rotasi-kepegawaian/${id}`)
        .then(res => {
            let data = res.data.data
            form_surat.id = data.id
            form_surat.letter_template_id = data.letter_template_id
            form_surat.tanggal_surat = data.tanggal_surat_raw
            form_surat.nomor_berita_acara = data.nomor_berita_acara
            form_surat.tanggal_berita_acara = data.tanggal_berita_acara
            form_surat.employee.status_awal = data.employee.status_awal
            form_surat.employee.jabatan_awal = data.employee.jabatan_awal
            form_surat.employee.status_akhir = data.employee.status_akhir
            form_surat.employee.jabatan_akhir = data.employee.jabatan_akhir
            form_surat.signer.position = data.signer.position
            form_surat.signature_type = data.signature_type
            form_surat.tanggal_berlaku = data.tanggal_berlaku

            selected_employee.value = data.employee
            selected_signer.value = data.signer
        })
        .catch(err => {
            console.log(err)
        })
}

function reset_errors() {
    Object.keys(errors).forEach(key => {
        errors[key] = ""
    })
}

function reset_employee() {
    selected_employee.value = null
    form_surat.employee = {
        id: "",
        position: "",
    }
}

function reset_signer() {
    selected_signer.value = null
    form_surat.signer = {
        id: "",
        position: "",
    }
}

function reset_form() {
    form_surat.id = ""
    form_surat.letter_template_id = ""
    form_surat.tanggal_surat = new Date().toISOString().slice(0, 10)
    form_surat.nomor_berita_acara = ""
    form_surat.tanggal_berita_acara = ""
    form_surat.employee = {
        status_awal: "",
        jabatan_awal: "",
        status_akhir: "",
        jabatan_akhir: "",
    }
    form_surat.signer = {
        position: "",
    }
    form_surat.signature_type = "manual"
    form_surat.tanggal_berlaku = ""

    reset_employee()
    reset_signer()
    reset_errors()

}


function save_surat() {
    //check if form is valid
    if (!selected_employee.value) {
        Swal.fire({
            icon: "error",
            title: "Gagal",
            text: "Pegawai tidak boleh kosong",
        });
        return
    }
    if (!selected_signer.value) {
        Swal.fire({
            icon: "error",
            title: "Gagal",
            text: "Penandatangan tidak boleh kosong",
        });
        return
    }
    //end of check if form is valid
    reset_errors()

    loading.value.open()
    if (form_surat.id != "") {
        // update
        let payload = {
            letter_template_id: form_surat.letter_template_id,
            tanggal_surat: form_surat.tanggal_surat,
            nomor_berita_acara: form_surat.nomor_berita_acara,
            tanggal_berita_acara: form_surat.tanggal_berita_acara,
            employee: {
                id: selected_employee.value.id,
                status_awal: form_surat.employee.status_awal,
                jabatan_awal: form_surat.employee.jabatan_awal,
                status_akhir: form_surat.employee.status_akhir,
                jabatan_akhir: form_surat.employee.jabatan_akhir,
            },
            signer: {
                id: selected_signer.value.id,
                position: form_surat.signer.position
            },
            signature_type: form_surat.signature_type,
            tanggal_berlaku: form_surat.tanggal_berlaku,
        }

        axios.put(`${url}/outcoming-letters/surat-keputusan-rotasi-kepegawaian/${form_surat.id}`, payload)
            .then(res => {
                Swal.fire({
                    icon: "success",
                    title: "Berhasil",
                    text: res.data.message,
                });
            })
            .catch(err => {
                if (err.response.status == 422) {
                    Object.entries(err.response.data.errors).forEach(entry => {
                        const [key, value] = entry;
                        errors[key] = value[0]
                    });
                    Swal.fire({
                        icon: "error",
                        title: "Gagal",
                        text: "Pastikan semua inputan sudah terisi dengan benar",
                    });
                } else {
                    console.log(err)
                    Swal.fire({
                        icon: "error",
                        title: "Gagal",
                        text: "Terjadi kesalahan",
                    });
                }
            }).finally(() => {
                loading.value.close()
            })
    } else {
        // create
        let payload = {
            letter_template_id: form_surat.letter_template_id,
            tanggal_surat: form_surat.tanggal_surat,
            nomor_berita_acara: form_surat.nomor_berita_acara,
            tanggal_berita_acara: form_surat.tanggal_berita_acara,
            employee: {
                id: selected_employee.value.id,
                status_awal: form_surat.employee.status_awal,
                jabatan_awal: form_surat.employee.jabatan_awal,
                status_akhir: form_surat.employee.status_akhir,
                jabatan_akhir: form_surat.employee.jabatan_akhir,
            },
            signer: {
                id: selected_signer.value.id,
                position: form_surat.signer.position
            },
            signature_type: form_surat.signature_type,
            tanggal_berlaku: form_surat.tanggal_berlaku,
        }

        axios.post(`${url}/outcoming-letters/surat-keputusan-rotasi-kepegawaian`, payload)
            .then(res => {
                Swal.fire({
                    icon: "success",
                    title: "Berhasil",
                    text: res.data.message,
                });
                reset_form()
            })
            .catch(err => {
                if (err.response.status == 422) {
                    Object.entries(err.response.data.errors).forEach(entry => {
                        const [key, value] = entry;
                        errors[key] = value[0]
                    });
                    Swal.fire({
                        icon: "error",
                        title: "Gagal",
                        text: "Pastikan semua inputan sudah terisi dengan benar",
                    });
                } else {
                    console.log(err)
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
    loading.value.open()
    if (route.name == 'update_surat_keputusan_rotasi_kepegawaian') {
        await get_letter(route.params.id)
    }
    await get_letter_templates()
    loading.value.close()
})

</script>

<template>
    <Loading ref="loading"></Loading>
    <SubHeader
        :title="route.name == 'update_surat_keputusan_rotasi_kepegawaian' ? `Edit Surat Keputusan Rotasi Kepegawaian` : `Tambah Surat Keputusan Rotasi Kepegawaian`"
        :back_url="{ name: 'surat_keputusan_rotasi_kepegawaian' }" />
    <div class="flex flex-col bg-white rounded-lg">
        <div class="px-8 py-5 min-w-full inline-block align-middle">
            <form @submit.prevent="save_surat">
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Template Surat</label>
                    <select class="form-control" required v-model="form_surat.letter_template_id"
                        placeholder="Template Surat">
                        <option v-for="letter_template in letter_templates" :value="letter_template.id">{{
                            letter_template.name }}</option>
                    </select>
                    <p v-if="errors.letter_template_id" class="text-xs text-red-600 mt-2" id="letter-template-error">
                        {{ errors.letter_template_id }}
                    </p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Tanggal Surat</label>
                    <input type="date" class="form-control" required
                        v-model="form_surat.tanggal_surat">
                    <p v-if="errors['tanggal_surat']"
                        class="text-xs text-red-600 mt-2">
                        {{ errors['tanggal_surat'] }}
                    </p>
                </div>
                <hr class="my-3 border-2" />
                <div class="grid grid-cols-2 gap-x-4">
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Nomor Berita Acara</label>
                        <input class="form-control" type="text" required v-model="form_surat.nomor_berita_acara"
                            placeholder="Nomor Berita Acara" />
                        <p v-if="errors.nomor_berita_acara" class="text-xs text-red-600 mt-2" id="nomor_berita_acara-error">
                            {{ errors.nomor_berita_acara }}
                        </p>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Tanggal Berita Acara</label>
                        <input class="form-control" type="date" required v-model="form_surat.tanggal_berita_acara"
                            placeholder="Tanggal Berita Acara" />
                        <p v-if="errors.tanggal_berita_acara" class="text-xs text-red-600 mt-2"
                            id="tanggal_berita_acara-error">
                            {{ errors.tanggal_berita_acara }}
                        </p>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Berlaku sejak ?</label>
                    <input class="form-control" type="date" required v-model="form_surat.tanggal_berlaku"
                        placeholder="Tanggal Berlaku" />
                    <p v-if="errors.tanggal_berlaku" class="text-xs text-red-600 mt-2" id="tanggal_berlaku-error">
                        {{ errors.tanggal_berlaku }}
                    </p>
                </div>
                <hr class="my-3 border-2" />
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Pegawai</label>
                    <search-input v-if="!selected_employee" v-model="selected_employee" :url="`${url}/employees`"
                        id="employee" placeholder="Cari Pegawai ...">
                        <template v-slot="{ data }">
                            <small>{{ data.nip }}</small>
                            <p class="mb-0">{{ data.name }}</p>
                            <hr class="m-0" />
                            <small class="mb-0">
                                <template v-for="(position, i) in data.positions">
                                    {{ position }}
                                    <template v-if="i != data.positions.length - 1"> | </template>
                                </template>
                            </small>
                        </template>
                    </search-input>
                    <p v-if="errors['employee.id']" class="text-xs text-red-600 mt-2" id="employee-error">
                        {{ errors['employee.id'] }}
                    </p>
                    <div v-if="selected_employee"
                        class="form-control bg-primary-200/20  mt-2 flex justify-between items-center gap-x-4">
                        <div class="w-full">
                            <small>{{ selected_employee.nip }}</small>
                            <p class="mb-0">{{ selected_employee.name }}</p>
                        </div>
                        <span @click="reset_employee"
                            class="p-3 hover:bg-red-200 rounded-full cursor-pointer transition ease-in-out duration-500">
                            <Icon icon="jam:trash" class="text-red-600 text-2xl" />
                        </span>
                    </div>
                </div>
                <template v-if="selected_employee">
                    <div class="mb-4 grid grid-cols-2 gap-x-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Jabatan Sebelumnya</label>
                            <custom-select :required="true" v-model="form_surat.employee.jabatan_awal"
                                :data="selected_employee.positions" placeholder="Jabatan pegawai"></custom-select>
                            <p v-if="errors['employee.jabatan_awal']" class="text-xs text-red-600 mt-2"
                                id="employee-jabatan_awal-error">
                                {{ errors['employee.jabatan_awal'] }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Status Sebelumnya</label>
                            <input class="form-control" type="text" required v-model="form_surat.employee.status_awal"
                                placeholder="Status pegawai sebelumnya" />
                            <p v-if="errors['employee.status_awal']" class="text-xs text-red-600 mt-2"
                                id="employee-status_awal-error">
                                {{ errors['employee.status_awal'] }}
                            </p>
                        </div>
                    </div>
                    <div class="mb-4 grid grid-cols-2 gap-x-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Jabatan Terbaru</label>
                            <search-input @inputValue="(data) => { form_surat.employee.jabatan_akhir = data }"
                                @selected="(data) => { form_surat.employee.jabatan_akhir = data.name }"
                                :defaultValue="form_surat.employee.jabatan_akhir" :url="`${url}/positions`"
                                id="search_input_jabatan" placeholder="Cari Jabatan ...">
                                <template v-slot="{ data }">
                                    <p class="mb-0">{{ data.name }}</p>
                                </template>
                            </search-input>
                            <p v-if="errors['employee.jabatan_akhir']" class="text-xs text-red-600 mt-2"
                                id="employee-jabatan_akhir-error">
                                {{ errors['employee.jabatan_akhir'] }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Status Terbaru</label>
                            <input class="form-control" type="text" required v-model="form_surat.employee.status_akhir"
                                placeholder="Status terbaru" />
                            <p v-if="errors['employee.status_akhir']" class="text-xs text-red-600 mt-2"
                                id="employee-status_akhir-error">
                                {{ errors['employee.status_akhir'] }}
                            </p>
                        </div>
                    </div>
                </template>
                <hr class="my-3 border-2" />
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Penandatangan</label>
                    <search-input v-if="!selected_signer" v-model="selected_signer" :url="`${url}/employees`" id="signer"
                        placeholder="Cari Penandatangan ...">
                        <template v-slot="{ data }">
                            <small>{{ data.nip }}</small>
                            <p class="mb-0">{{ data.name }}</p>
                            <hr class="m-0" />
                            <small class="mb-0">
                                <template v-for="(position, i) in data.positions">
                                    {{ position }}
                                    <template v-if="i != data.positions.length - 1"> | </template>
                                </template>
                            </small>
                        </template>
                    </search-input>
                    <p v-if="errors['signer.id']" class="text-xs text-red-600 mt-2" id="signer-id-error">
                        {{ errors['signer.id'] }}
                    </p>
                    <div v-if="selected_signer"
                        class="form-control bg-primary-200/20  mt-2 flex justify-between items-center gap-x-4">
                        <div class="w-full">
                            <small>{{ selected_signer.nip }}</small>
                            <p class="mb-0">{{ selected_signer.name }}</p>
                        </div>
                        <span @click="reset_signer"
                            class="p-3 hover:bg-red-200 rounded-full cursor-pointer transition ease-in-out duration-500">
                            <Icon icon="jam:trash" class="text-red-600 text-2xl" />
                        </span>
                    </div>
                </div>
                <div class="mb-4" v-if="selected_signer">
                    <label class="block text-sm font-medium mb-2">Pilih Jabatan Penandatangan</label>
                    <custom-select :required="true" v-model="form_surat.signer.position" :data="selected_signer.positions"
                        placeholder="Jabatan penandatangan"></custom-select>
                    <p v-if="errors['signer.position']" class="text-xs text-red-600 mt-2" id="signer-position-error">
                        {{ errors['signer.position'] }}
                    </p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Jenis Tanda Tangan</label>
                    <select class="form-control" required v-model="form_surat.signature_type"
                        placeholder="Jenis Tanda Tangan">
                        <option value="manual">Tanda Tangan Manual</option>
                        <option value="digital">Tanda Tangan Digital</option>
                        <option value="gambar tanda tangan">Tanda Tangan Berupa Gambar</option>
                    </select>
                    <p v-if="errors.signature_type" class="text-xs text-red-600 mt-2" id="signatur-type-error">
                        {{ errors.signature_type }}
                    </p>
                </div>
                <div class="flex justify-end gap-x-6">
                    <router-link :to="{ name: 'surat_keputusan_rotasi_kepegawaian' }"
                        class="btn btn-outline border hover:border-primary-500 px-24 py-3">Kembali</router-link>
                    <button class="btn btn-primary px-24 py-3">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</template>