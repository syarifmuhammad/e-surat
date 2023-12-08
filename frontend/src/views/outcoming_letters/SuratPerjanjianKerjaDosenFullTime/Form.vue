<script setup>
import { ref, reactive, onMounted } from 'vue'
import { Icon } from '@iconify/vue'
import axios from 'axios'
import SubHeader from '@/components/SubHeader.vue'
import SearchInput from '@/components/SearchInput.vue';
import CustomSelect from '@/components/CustomSelect.vue';
import AddRekening from '@/components/AddRekening.vue';
import AddProdi from '@/components/AddProdi.vue';

const url = import.meta.env.VITE_URL_API
const NAMA_SURAT = "SURAT_PERJANJIAN_KERJA_DOSEN_FULL_TIME"

const loading = ref(null)
const search_input_rekening = ref(null)
const search_input_prodi = ref(null)

const fasilitas_lainnya = ref("")

const form_surat = reactive({
    id: "",
    nomor_surat_sebelumnya: "",
    letter_template_id: "",
    profesi: "",
    jabatan_fungsional: "",
    mulai_berlaku: "",
    akhir_berlaku: "",
    signer: {
        position: "",
    },
    signature_type: "manual",
    pertelaan_perjanjian_kerja: {
        pendidikan: "",
        jangka_waktu: "",
        tahun_satu: "",
        tunjangan_dasar_satu: 0,
        tunjangan_fungsional_satu: 0,
        tunjangan_struktural_satu: 0,
        tunjangan_kemahalan_satu: 0,
        pendapatan_bulanan_satu: 0,
        tahun_dua: "",
        tunjangan_dasar_dua: 0,
        tunjangan_fungsional_dua: 0,
        tunjangan_struktural_dua: 0,
        tunjangan_kemahalan_dua: 0,
        pendapatan_bulanan_dua: 0,
        fasilitas_lainnya: [
            'Tunjangan BPJS Kesehatan',
            'BPJS Ketenagakerjaan',
        ]
    }
})

const errors = reactive({
    letter_template_id: "",
    nomor_surat_sebelumnya: "",
    employee: {
        id: "",
    },
    profesi: "",
    jabatan_fungsional: "",
    mulai_berlaku: "",
    akhir_berlaku: "",
    rekening: "",
    prodi: "",
    signer: {
        id: "",
        position: "",
    },
    signature_type: "",
    pertelaan_perjanjian_kerja: {
        pendidikan: "",
        jangka_waktu: "",
        tahun_satu: "",
        tunjangan_dasar_satu: "",
        tunjangan_fungsional_satu: "",
        tunjangan_struktural_satu: "",
        tunjangan_kemahalan_satu: "",
        pendapatan_bulanan_satu: "",
        tahun_dua: "",
        tunjangan_dasar_dua: "",
        tunjangan_fungsional_dua: "",
        tunjangan_struktural_dua: "",
        tunjangan_kemahalan_dua: "",
        pendapatan_bulanan_dua: "",
        fasilitas_lainnya: "",
    }
})

const letter_templates = ref([])
const selected_employee = ref(null)
const selected_rekening = ref(null)
const selected_prodi = ref(null)
const selected_signer = ref(null)
const modal_add_rekening = ref(null)
const modal_add_prodi = ref(null)

function get_letter_templates() {
    loading.value.open()
    axios.get(`${url}/outcoming-letters/templates?letter_type=${NAMA_SURAT}`)
        .then(res => {
            letter_templates.value = res.data.data
            form_surat.letter_template_id = letter_templates.value[0].id
        })
        .catch(err => {
            console.log(err)
        }).finally(() => {
            loading.value.close()
        })
}

function reset_errors() {
    errors.letter_template_id = ""
    errors.nomor_surat_sebelumnya = ""
    errors.employee.id = ""
    errors.profesi = ""
    errors.jabatan_fungsional = ""
    errors.mulai_berlaku = ""
    errors.akhir_berlaku = ""
    errors.prodi = ""
    errors.rekening = ""
    errors.signer.id = ""
    errors.signer.position = ""
    errors.signature_type = ""
    errors.pertelaan_perjanjian_kerja.pendidikan = ""
    errors.pertelaan_perjanjian_kerja.jangka_waktu = ""
    errors.pertelaan_perjanjian_kerja.tahun_satu = ""
    errors.pertelaan_perjanjian_kerja.tunjangan_dasar_satu = ""
    errors.pertelaan_perjanjian_kerja.tunjangan_fungsional_satu = ""
    errors.pertelaan_perjanjian_kerja.tunjangan_struktural_satu = ""
    errors.pertelaan_perjanjian_kerja.tunjangan_kemahalan_satu = ""
    errors.pertelaan_perjanjian_kerja.pendapatan_bulanan_satu = ""
    errors.pertelaan_perjanjian_kerja.tahun_dua = ""
    errors.pertelaan_perjanjian_kerja.tunjangan_dasar_dua = ""
    errors.pertelaan_perjanjian_kerja.tunjangan_fungsional_dua = ""
    errors.pertelaan_perjanjian_kerja.tunjangan_struktural_dua = ""
    errors.pertelaan_perjanjian_kerja.tunjangan_kemahalan_dua = ""
    errors.pertelaan_perjanjian_kerja.pendapatan_bulanan_dua = ""
    errors.pertelaan_perjanjian_kerja.fasilitas_lainnya = ""
}

function reset_employee() {
    selected_employee.value = null
}

function reset_rekening() {
    selected_rekening.value = null
}

function reset_prodi() {
    selected_prodi.value = null
}

function reset_signer() {
    selected_signer.value = null
    form_surat.signer = {
        position: "",
    }
}

function reset_form() {
    form_surat.id = ""
    form_surat.nomor_surat_sebelumnya = ""
    form_surat.letter_template_id = ""
    form_surat.profesi = ""
    form_surat.jabatan_fungsional = ""
    form_surat.mulai_berlaku = ""
    form_surat.akhir_berlaku = ""
    form_surat.signature_type = "manual"
    form_surat.pertelaan_perjanjian_kerja = {
        pendidikan: "",
        jangka_waktu: "",
        tahun_satu: "",
        tunjangan_dasar_satu: 0,
        tunjangan_fungsional_satu: 0,
        tunjangan_struktural_satu: 0,
        tunjangan_kemahalan_satu: 0,
        pendapatan_bulanan_satu: 0,
        tahun_dua: "",
        tunjangan_dasar_dua: 0,
        tunjangan_fungsional_dua: 0,
        tunjangan_struktural_dua: 0,
        tunjangan_kemahalan_dua: 0,
        pendapatan_bulanan_dua: 0,
        fasilitas_lainnya: [
            'Tunjangan BPJS Kesehatan',
            'BPJS Ketenagakerjaan',
        ]
    }
    reset_employee()
    reset_rekening()
    reset_prodi()
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
    if (!selected_rekening.value) {
        Swal.fire({
            icon: "error",
            title: "Gagal",
            text: "Rekening tidak boleh kosong",
        });
        return
    }
    if (!selected_prodi.value) {
        Swal.fire({
            icon: "error",
            title: "Gagal",
            text: "Prodi tidak boleh kosong",
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

    loading.value.open()
    if (form_surat.id.trim()) {
        // update
    } else {
        // create
        let payload = {
            letter_template_id: form_surat.letter_template_id,
            nomor_surat_sebelumnya: form_surat.nomor_surat_sebelumnya,
            employee: {
                id: selected_employee.value.id,
            },
            profesi: form_surat.profesi,
            jabatan_fungsional: form_surat.jabatan_fungsional,
            mulai_berlaku: form_surat.mulai_berlaku,
            akhir_berlaku: form_surat.akhir_berlaku,
            prodi: selected_prodi.value.id, 
            rekening: selected_rekening.value.id,
            signer: {
                id: selected_signer.value.id,
                position: form_surat.signer.position
            },
            signature_type: form_surat.signature_type,
            pertelaan_perjanjian_kerja: {
                pendidikan: form_surat.pertelaan_perjanjian_kerja.pendidikan,
                jangka_waktu: form_surat.pertelaan_perjanjian_kerja.jangka_waktu,
                tahun_satu: form_surat.pertelaan_perjanjian_kerja.tahun_satu,
                tunjangan_dasar_satu: form_surat.pertelaan_perjanjian_kerja.tunjangan_dasar_satu,
                tunjangan_fungsional_satu: form_surat.pertelaan_perjanjian_kerja.tunjangan_fungsional_satu,
                tunjangan_struktural_satu: form_surat.pertelaan_perjanjian_kerja.tunjangan_struktural_satu,
                tunjangan_kemahalan_satu: form_surat.pertelaan_perjanjian_kerja.tunjangan_kemahalan_satu,
                pendapatan_bulanan_satu: form_surat.pertelaan_perjanjian_kerja.pendapatan_bulanan_satu,
                tahun_dua: form_surat.pertelaan_perjanjian_kerja.tahun_dua,
                tunjangan_dasar_dua: form_surat.pertelaan_perjanjian_kerja.tunjangan_dasar_dua,
                tunjangan_fungsional_dua: form_surat.pertelaan_perjanjian_kerja.tunjangan_fungsional_dua,
                tunjangan_struktural_dua: form_surat.pertelaan_perjanjian_kerja.tunjangan_struktural_dua,
                tunjangan_kemahalan_dua: form_surat.pertelaan_perjanjian_kerja.tunjangan_kemahalan_dua,
                pendapatan_bulanan_dua: form_surat.pertelaan_perjanjian_kerja.pendapatan_bulanan_dua,
                fasilitas_lainnya: form_surat.pertelaan_perjanjian_kerja.fasilitas_lainnya,
            }
        }

        axios.post(`${url}/outcoming-letters/surat-perjanjian-kerja-dosen-full-time`, payload)
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
                    errors.letter_template_id = err.response.data.errors.letter_template_id[0]
                    errors.nomor_surat_sebelumnya = err.response.data.errors.nomor_surat_sebelumnya[0]
                    errors.employee.id = err.response.data.errors.employee.id[0]
                    errors.profesi = err.response.data.errors.profesi[0]
                    errors.jabatan_fungsional = err.response.data.errors.jabatan_fungsional[0]
                    errors.mulai_berlaku = err.response.data.errors.mulai_berlaku[0]
                    errors.akhir_berlaku = err.response.data.errors.akhir_berlaku[0]
                    errors.prodi = err.response.data.errors.prodi[0]
                    errors.rekening = err.response.data.errors.rekening[0]
                    errors.signer.id = err.response.data.errors.signer.id[0]
                    errors.signer.position = err.response.data.errors.signer.position[0]
                    errors.signature_type = err.response.data.errors.signature_type[0]
                    errors.pertelaan_perjanjian_kerja.pendidikan = err.response.data.errors.pertelaan_perjanjian_kerja.pendidikan[0]
                    errors.pertelaan_perjanjian_kerja.jangka_waktu = err.response.data.errors.pertelaan_perjanjian_kerja.jangka_waktu[0]
                    errors.pertelaan_perjanjian_kerja.tahun_satu = err.response.data.errors.pertelaan_perjanjian_kerja.tahun_satu[0]
                    errors.pertelaan_perjanjian_kerja.tunjangan_dasar_satu = err.response.data.errors.pertelaan_perjanjian_kerja.tunjangan_dasar_satu[0]
                    errors.pertelaan_perjanjian_kerja.tunjangan_fungsional_satu = err.response.data.errors.pertelaan_perjanjian_kerja.tunjangan_fungsional_satu[0]
                    errors.pertelaan_perjanjian_kerja.tunjangan_struktural_satu = err.response.data.errors.pertelaan_perjanjian_kerja.tunjangan_struktural_satu[0]
                    errors.pertelaan_perjanjian_kerja.tunjangan_kemahalan_satu = err.response.data.errors.pertelaan_perjanjian_kerja.tunjangan_kemahalan_satu[0]
                    errors.pertelaan_perjanjian_kerja.pendapatan_bulanan_satu = err.response.data.errors.pertelaan_perjanjian_kerja.pendapatan_bulanan_satu[0]
                    errors.pertelaan_perjanjian_kerja.tahun_dua = err.response.data.errors.pertelaan_perjanjian_kerja.tahun_dua[0]
                    errors.pertelaan_perjanjian_kerja.tunjangan_dasar_dua = err.response.data.errors.pertelaan_perjanjian_kerja.tunjangan_dasar_dua[0]
                    errors.pertelaan_perjanjian_kerja.tunjangan_fungsional_dua = err.response.data.errors.pertelaan_perjanjian_kerja.tunjangan_fungsional_dua[0]
                    errors.pertelaan_perjanjian_kerja.tunjangan_struktural_dua = err.response.data.errors.pertelaan_perjanjian_kerja.tunjangan_struktural_dua[0]
                    errors.pertelaan_perjanjian_kerja.tunjangan_kemahalan_dua = err.response.data.errors.pertelaan_perjanjian_kerja.tunjangan_kemahalan_dua[0]
                    errors.pertelaan_perjanjian_kerja.pendapatan_bulanan_dua = err.response.data.errors.pertelaan_perjanjian_kerja.pendapatan_bulanan_dua[0]
                    errors.pertelaan_perjanjian_kerja.fasilitas_lainnya = err.response.data.errors.pertelaan_perjanjian_kerja.fasilitas_lainnya[0]
                } else {
                    console.log(err)
                }
            }).finally(() => {
                loading.value.close()
            })
    }

}

function open_modal_add_rekening() {
    modal_add_rekening.value.open()
}
function open_modal_add_prodi() {
    modal_add_prodi.value.open()
}

onMounted(async () => {
    get_letter_templates()
})

</script>

<template>
    <Loading ref="loading"></Loading>
    <SubHeader :title="`Tambah Surat Perjanjian Kerja Dosen Full Time`" />
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
                    <label class="block text-sm font-medium mb-2">Nomor Surat Perjanjian Kerja Yang Diamandemen (Tidak Wajib)</label>
                    <input type="text" class="form-control" v-model="form_surat.nomor_surat_sebelumnya" placeholder="Nomor Surat Perjanjian Kerja Yang Diamandemen">
                    <p v-if="errors.nomor_surat_sebelumnya" class="text-xs text-red-600 mt-2">
                        {{ errors.nomor_surat_sebelumnya }}
                    </p>
                </div>
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
                    <p v-if="errors.employee.id" class="text-xs text-red-600 mt-2" id="employee-error">
                        {{ errors.employee.id }}
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
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Rekening</label>
                        <search-input ref="search_input_rekening" v-if="!selected_rekening" v-model="selected_rekening"
                            :url="`${url}/employees/${selected_employee.id}/rekening`" placeholder="Cari Rekening ...">
                            <template v-slot="{ data }">
                                <small>{{ data.nama_bank }}</small>
                                <p class="mb-0">{{ data.atas_nama }}</p>
                                <p class="mb-0">{{ data.nomor_rekening }}</p>
                            </template>
                            <template #default_first>
                                <p class="text-center text-primary cursor-pointer" @click="open_modal_add_rekening">
                                    + Tambah Data Rekening
                                </p>
                            </template>
                        </search-input>
                        <p v-if="errors.rekening" class="text-xs text-red-600 mt-2" id="rekening-error">
                            {{ errors.rekening }}
                        </p>
                        <div v-if="selected_rekening"
                            class="form-control bg-primary-200/20  mt-2 flex justify-between items-center gap-x-4">
                            <div class="w-full">
                                <small>{{ selected_rekening.nama_bank }}</small>
                                <p class="mb-0">{{ selected_rekening.atas_nama }}</p>
                                <p class="mb-0">{{ selected_rekening.nomor_rekening }}</p>
                            </div>
                            <span @click="reset_rekening"
                                class="p-3 hover:bg-red-200 rounded-full cursor-pointer transition ease-in-out duration-500">
                                <Icon icon="jam:trash" class="text-red-600 text-2xl" />
                            </span>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Fakultas & Prodi</label>
                        <search-input ref="search_input_prodi" v-if="!selected_prodi" v-model="selected_prodi"
                            :url="`${url}/prodi`" placeholder="Cari Fakultas / Prodi ...">
                            <template v-slot="{ data }">
                                <small>{{ data.nama_fakultas  }} ( {{ data.singkatan_fakultas }})</small>
                                <p class="mb-0">{{ data.nama_prodi  }} ({{ data.singkatan_prodi }})</p>
                            </template>
                            <template #default_first>
                                <p class="text-center text-primary cursor-pointer" @click="open_modal_add_prodi">
                                    + Tambah Data Prodi
                                </p>
                            </template>
                        </search-input>
                        <p v-if="errors.prodi" class="text-xs text-red-600 mt-2" id="prodi-error">
                            {{ errors.prodi }}
                        </p>
                        <div v-if="selected_prodi"
                            class="form-control bg-primary-200/20  mt-2 flex justify-between items-center gap-x-4">
                            <div class="w-full">
                                <small>{{ selected_prodi.nama_fakultas  }} ( {{ selected_prodi.singkatan_fakultas }})</small>
                                <p class="mb-0">{{ selected_prodi.nama_prodi  }} ( {{ selected_prodi.singkatan_prodi }})</p>
                            </div>
                            <span @click="reset_prodi"
                                class="p-3 hover:bg-red-200 rounded-full cursor-pointer transition ease-in-out duration-500">
                                <Icon icon="jam:trash" class="text-red-600 text-2xl" />
                            </span>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-x-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Profesi</label>
                            <input type="text" class="form-control" required v-model="form_surat.profesi" placeholder="Profesi">
                            <p v-if="errors.profesi" class="text-xs text-red-600 mt-2">
                                {{ errors.profesi }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Jabatan Fungsional</label>
                            <input type="text" class="form-control" required v-model="form_surat.jabatan_fungsional"
                                placeholder="Jabatan Fungsional">
                            <p v-if="errors.jabatan_fungsional" class="text-xs text-red-600 mt-2">
                                {{ errors.jabatan_fungsional }}
                            </p>
                        </div>
                    </div>
                </template>
                <hr class="my-4" />
                <!-- <div class="mb-4">
                    <label class="block text-sm font-bold mb-2">MASA MAGANG, TUGAS, TANGGUNG JAWAB DAN TEMPAT
                        MAGANG</label>
                    <hr class="my-4" />
                    <div class="grid grid-cols-2 gap-x-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Mulai Magang</label>
                            <input type="date" class="form-control" required v-model="form_surat.mulai_berlaku"
                                placeholder="Tanggal Mulai Magang">
                            <p v-if="errors.mulai_berlaku" class="text-xs text-red-600 mt-2">
                                {{ errors.mulai_berlaku }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Akhir Magang</label>
                            <input type="date" class="form-control" required v-model="form_surat.akhir_berlaku"
                                placeholder="Tanggal Akhir Magang">
                            <p v-if="errors.akhir_berlaku" class="text-xs text-red-600 mt-2">
                                {{ errors.akhir_berlaku }}
                            </p>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Tempat Kerja</label>
                        <input type="text" class="form-control" required v-model="form_surat.tempat_kerja"
                            placeholder="Tempat Kerja">
                        <p v-if="errors.tempat_kerja" class="text-xs text-red-600 mt-2">
                            {{ errors.tempat_kerja }}
                        </p>
                    </div>
                    
                </div> -->
                <!-- <hr class="my-4" /> -->
                <div class="mb-4">
                    <label class="block text-sm font-bold mb-2">JANGKA WAKTU KERJA</label>
                    <hr class="my-4" />
                    <div class="grid grid-cols-2 gap-x-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Mulai Berlaku</label>
                            <input type="date" class="form-control" required v-model="form_surat.mulai_berlaku">
                            <p v-if="errors.mulai_berlaku" class="text-xs text-red-600 mt-2">
                                {{ errors.mulai_berlaku }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Akhir Berlaku</label>
                            <input type="date" class="form-control" required v-model="form_surat.akhir_berlaku">
                            <p v-if="errors.akhir_berlaku" class="text-xs text-red-600 mt-2">
                                {{ errors.akhir_berlaku }}
                            </p>
                        </div>
                    </div>
                </div>
                <hr class="my-4" />
                <div class="mb-4">
                    <label class="block text-sm font-bold mb-2">PERTELAAN PERJANJIAN KERJA</label>
                    <hr class="my-4" />
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Pendidikan</label>
                        <input type="text" class="form-control" required v-model="form_surat.pertelaan_perjanjian_kerja.pendidikan">
                        <p v-if="errors.pertelaan_perjanjian_kerja.pendidikan" class="text-xs text-red-600 mt-2">
                            {{ errors.pertelaan_perjanjian_kerja.pendidikan }}
                        </p>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Jangka Waktu</label>
                        <input type="text" class="form-control" required v-model="form_surat.pertelaan_perjanjian_kerja.jangka_waktu">
                        <p v-if="errors.pertelaan_perjanjian_kerja.jangka_waktu" class="text-xs text-red-600 mt-2">
                            {{ errors.pertelaan_perjanjian_kerja.jangka_waktu }}
                        </p>
                    </div>
                    <!-- <div class="grid grid-cols-2 gap-x-4 mb-4">
                    </div> -->
                    <div class="grid grid-cols-2 mb-4 divide-x">
                        <div class="pr-10">
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">Tahun Penggajian Pertama</label>
                                <input type="number" class="form-control" required v-model="form_surat.pertelaan_perjanjian_kerja.tahun_satu" placeholder="cth : 2023">
                                <p v-if="errors.pertelaan_perjanjian_kerja.tahun_satu" class="text-xs text-red-600 mt-2">
                                    {{ errors.pertelaan_perjanjian_kerja.tahun_satu }}
                                </p>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">Tunjangan Dasar</label>
                                <input type="number" class="form-control" required v-model="form_surat.pertelaan_perjanjian_kerja.tunjangan_dasar_satu" placeholder="Tunjangan Dasar">
                                <p v-if="errors.pertelaan_perjanjian_kerja.tunjangan_dasar_satu" class="text-xs text-red-600 mt-2">
                                    {{ errors.pertelaan_perjanjian_kerja.tunjangan_dasar_satu }}
                                </p>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">Tunjangan Fungsional</label>
                                <input type="number" class="form-control" required v-model="form_surat.pertelaan_perjanjian_kerja.tunjangan_fungsional_satu" placeholder="Tunjangan Fungsional">
                                <p v-if="errors.pertelaan_perjanjian_kerja.tunjangan_fungsional_satu" class="text-xs text-red-600 mt-2">
                                    {{ errors.pertelaan_perjanjian_kerja.tunjangan_fungsional_satu }}
                                </p>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">Tunjangan Struktural</label>
                                <input type="number" class="form-control" required v-model="form_surat.pertelaan_perjanjian_kerja.tunjangan_struktural_satu" placeholder="Tunjangan Struktural">
                                <p v-if="errors.pertelaan_perjanjian_kerja.tunjangan_struktural_satu" class="text-xs text-red-600 mt-2">
                                    {{ errors.pertelaan_perjanjian_kerja.tunjangan_struktural_satu }}
                                </p>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">Tunjangan Kemahalan</label>
                                <input type="number" class="form-control" required v-model="form_surat.pertelaan_perjanjian_kerja.tunjangan_kemahalan_satu" placeholder="Tunjangan Kemahalan">
                                <p v-if="errors.pertelaan_perjanjian_kerja.tunjangan_kemahalan_satu" class="text-xs text-red-600 mt-2">
                                    {{ errors.pertelaan_perjanjian_kerja.tunjangan_kemahalan_satu }}
                                </p>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">Pendapatan Bulanan</label>
                                <input type="number" class="form-control" required v-model="form_surat.pertelaan_perjanjian_kerja.pendapatan_bulanan_satu" placeholder="Pendapatan Bulanan">
                                <p v-if="errors.pertelaan_perjanjian_kerja.pendapatan_bulanan_satu" class="text-xs text-red-600 mt-2">
                                    {{ errors.pertelaan_perjanjian_kerja.pendapatan_bulanan_satu }}
                                </p>
                            </div>
                        </div>
                        <div class="pl-10">
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">Tahun Penggajian Kedua</label>
                                <input type="number" class="form-control" required v-model="form_surat.pertelaan_perjanjian_kerja.tahun_dua" placeholder="cth : 2023">
                                <p v-if="errors.pertelaan_perjanjian_kerja.tahun_dua" class="text-xs text-red-600 mt-2">
                                    {{ errors.pertelaan_perjanjian_kerja.tahun_dua }}
                                </p>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">Tunjangan Dasar</label>
                                <input type="number" class="form-control" required v-model="form_surat.pertelaan_perjanjian_kerja.tunjangan_dasar_dua" placeholder="Tunjangan Dasar">
                                <p v-if="errors.pertelaan_perjanjian_kerja.tunjangan_dasar_dua" class="text-xs text-red-600 mt-2">
                                    {{ errors.pertelaan_perjanjian_kerja.tunjangan_dasar_dua }}
                                </p>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">Tunjangan Fungsional</label>
                                <input type="number" class="form-control" required v-model="form_surat.pertelaan_perjanjian_kerja.tunjangan_fungsional_dua" placeholder="Tunjangan Fungsional">
                                <p v-if="errors.pertelaan_perjanjian_kerja.tunjangan_fungsional_dua" class="text-xs text-red-600 mt-2">
                                    {{ errors.pertelaan_perjanjian_kerja.tunjangan_fungsional_dua }}
                                </p>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">Tunjangan Struktural</label>
                                <input type="number" class="form-control" required v-model="form_surat.pertelaan_perjanjian_kerja.tunjangan_struktural_dua" placeholder="Tunjangan Struktural">
                                <p v-if="errors.pertelaan_perjanjian_kerja.tunjangan_struktural_dua" class="text-xs text-red-600 mt-2">
                                    {{ errors.pertelaan_perjanjian_kerja.tunjangan_struktural_dua }}
                                </p>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">Tunjangan Kemahalan</label>
                                <input type="number" class="form-control" required v-model="form_surat.pertelaan_perjanjian_kerja.tunjangan_kemahalan_dua" placeholder="Tunjangan Kemahalan">
                                <p v-if="errors.pertelaan_perjanjian_kerja.tunjangan_kemahalan_dua" class="text-xs text-red-600 mt-2">
                                    {{ errors.pertelaan_perjanjian_kerja.tunjangan_kemahalan_dua }}
                                </p>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">Pendapatan Bulanan</label>
                                <input type="number" class="form-control" required v-model="form_surat.pertelaan_perjanjian_kerja.pendapatan_bulanan_dua" placeholder="Pendapatan Bulanan">
                                <p v-if="errors.pertelaan_perjanjian_kerja.pendapatan_bulanan_dua" class="text-xs text-red-600 mt-2">
                                    {{ errors.pertelaan_perjanjian_kerja.pendapatan_bulanan_dua }}
                                </p>
                            </div>
                        </div>
                        
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Fasilitas Lainnya</label>
                        <div class="flex gap-x-4">
                            <input type="text" class="form-control" v-model="fasilitas_lainnya"
                                placeholder="Tugas Dan Tanggung Jawab">
                            <button type="button" @click="form_surat.pertelaan_perjanjian_kerja.fasilitas_lainnya.push(fasilitas_lainnya), fasilitas_lainnya = ''"
                                class="btn bg-blue-500 text-white">Tambah</button>
                        </div>
                        <p v-if="errors.pertelaan_perjanjian_kerja.fasilitas_lainnya" class="text-xs text-red-600 mt-2">
                            {{ errors.pertelaan_perjanjian_kerja.fasilitas_lainnya }}
                        </p>
                        <div v-if="form_surat.pertelaan_perjanjian_kerja.fasilitas_lainnya.length > 0" class="mt-4">
                            <div v-for="(f, index) in form_surat.pertelaan_perjanjian_kerja.fasilitas_lainnya" :key="index"
                                class="form-control bg-primary-200/20  mt-2 flex justify-between items-center gap-x-4">
                                <div class="w-full">
                                    <p>{{ f }}</p>
                                </div>
                                <span @click="form_surat.pertelaan_perjanjian_kerja.fasilitas_lainnya.splice(index, 1)"
                                    class="p-3 hover:bg-red-200 rounded-full cursor-pointer transition ease-in-out duration-500">
                                    <Icon icon="jam:trash" class="text-red-600 text-2xl" />
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="my-4" />
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
                    <p v-if="errors.signer.id" class="text-xs text-red-600 mt-2" id="signer-id-error">
                        {{ errors.signer.id }}
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
                    <p v-if="errors.signer.position" class="text-xs text-red-600 mt-2" id="signer-position-error">
                        {{ errors.signer.position }}
                    </p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Jenis Tanda Tangan</label>
                    <select class="form-control" required v-model="form_surat.signature_type"
                        placeholder="Jenis Tanda Tangan">
                        <option value="manual">Tanda Tangan Manual</option>
                        <option value="qrcode">Tanda Tangan QR Code</option>
                        <option value="digital">Tanda Tangan Digital</option>
                    </select>
                    <p v-if="errors.signature_type" class="text-xs text-red-600 mt-2" id="signatur-type-error">
                        {{ errors.signature_type }}
                    </p>
                </div>
                <div class="flex justify-end">
                    <button class="btn btn-primary px-24 py-3">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <template v-if="selected_employee">
        <AddRekening ref="modal_add_rekening" :employee="selected_employee" @inserted="search_input_rekening.fetchData()" />
        <AddProdi ref="modal_add_prodi" @inserted="search_input_prodi.fetchData()" />
    </template>
</template>