<script setup>
import { ref, reactive, onMounted } from 'vue'
import { Icon } from '@iconify/vue'
import axios from 'axios'
import SubHeader from '@/components/SubHeader.vue'
import SearchInput from '@/components/SearchInput.vue';
import CustomSelect from '@/components/CustomSelect.vue';
import AddRekening from '@/components/AddRekening.vue';
import AddProdi from '@/components/AddProdi.vue';
import ChooseEmployee from '@/components/ChooseEmployee.vue';
import { useRoute } from 'vue-router';

const route = useRoute()
const url = import.meta.env.VITE_URL_API
const NAMA_SURAT = "SURAT_PERJANJIAN_KERJA_DOSEN_FULL_TIME"

const loading = ref(null)
const search_input_rekening = ref(null)
const search_input_prodi = ref(null)
const modal_choose_signer = ref(null)
const modal_choose_approval = ref(null)

const fasilitas_lainnya = ref("")

const form_surat = reactive({
    id: "",
    nomor_surat_sebelumnya: "",
    tanggal_surat_sebelumnya: "",
    letter_template_id: "",
    tanggal_surat: new Date().toISOString().slice(0, 10),
    masa_berlaku: {
        year: 0,
        month: 0,
        day: 0,
    },
    employee: {},
    jabatan_fungsional: "",
    mulai_berlaku: "",
    rekening: {},
    signers: [],
    approvals: [],
    signature_type: "digital",
    pertelaan_perjanjian_kerja: {
        pendidikan: "",
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
    tanggal_surat: "",
    masa_berlaku: "",
    nomor_surat_sebelumnya: "",
    tanggal_surat_sebelumnya: "",
    'employee.id': "",
    jabatan_fungsional: "",
    mulai_berlaku: "",
    rekening: "",
    prodi: "",
    signers: "",
    approvals: "",
    signature_type: "",
    "pertelaan_perjanjian_kerja.pendidikan": "",
    "pertelaan_perjanjian_kerja.tahun_satu": "",
    "pertelaan_perjanjian_kerja.tunjangan_dasar_satu": "",
    "pertelaan_perjanjian_kerja.tunjangan_fungsional_satu": "",
    "pertelaan_perjanjian_kerja.tunjangan_struktural_satu": "",
    "pertelaan_perjanjian_kerja.tunjangan_kemahalan_satu": "",
    "pertelaan_perjanjian_kerja.pendapatan_bulanan_satu": "",
    "pertelaan_perjanjian_kerja.tahun_dua": "",
    "pertelaan_perjanjian_kerja.tunjangan_dasar_dua": "",
    "pertelaan_perjanjian_kerja.tunjangan_fungsional_dua": "",
    "pertelaan_perjanjian_kerja.tunjangan_struktural_dua": "",
    "pertelaan_perjanjian_kerja.tunjangan_kemahalan_dua": "",
    "pertelaan_perjanjian_kerja.pendapatan_bulanan_dua": "",
    "pertelaan_perjanjian_kerja.fasilitas_lainnya": "",
})

const letter_templates = ref([])
const jabatan_fungsional = ref([])
const selected_employee = ref(null)
const selected_rekening = ref(null)
const selected_prodi = ref(null)
const modal_add_rekening = ref(null)
const modal_add_prodi = ref(null)

async function get_letter_templates() {
    await axios.get(`${url}/outcoming-letters/templates?letter_type=${NAMA_SURAT}&is_active=true`)
        .then(res => {
            letter_templates.value = res.data.data
            form_surat.letter_template_id = letter_templates.value[0].id
        })
        .catch(err => {
            console.log(err)
        })
}

async function get_jabatan_fungsional() {
    await axios.get(`${url}/positions?type=fungsional`)
        .then(res => {
            jabatan_fungsional.value = res.data.data.map(p => {
                return p.name
            })
        })
        .catch(err => {
            console.log(err)
        })
}

async function get_letter(id) {
    await axios.get(`${url}/outcoming-letters/surat-perjanjian-kerja-dosen-full-time/${id}`)
        .then(res => {
            let data = res.data.data
            form_surat.id = data.id
            form_surat.nomor_surat_sebelumnya = data.nomor_surat_sebelumnya
            form_surat.tanggal_surat_sebelumnya = data.tanggal_surat_sebelumnya
            form_surat.letter_template_id = data.letter_template_id
            form_surat.tanggal_surat = data.tanggal_surat_raw
            form_surat.masa_berlaku = data.masa_berlaku
            form_surat.employee = data.employee
            form_surat.jabatan_fungsional = data.jabatan_fungsional
            form_surat.mulai_berlaku = data.mulai_berlaku
            form_surat.rekening = data.rekening
            form_surat.prodi = data.prodi
            form_surat.signers = res.data.data.signers
            form_surat.approvals = res.data.data.approvals
            form_surat.signature_type = data.signature_type
            form_surat.pertelaan_perjanjian_kerja = data.pertelaan_perjanjian_kerja
            selected_employee.value = data.employee
            selected_rekening.value = data.rekening
            selected_prodi.value = data.prodi
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
    reset_rekening()
}

function reset_rekening() {
    selected_rekening.value = null
}

function reset_prodi() {
    selected_prodi.value = null
}

function reset_form() {
    form_surat.id = ""
    form_surat.nomor_surat_sebelumnya = ""
    form_surat.tanggal_surat_sebelumnya = ""
    form_surat.letter_template_id = ""
    form_surat.tanggal_surat = new Date().toISOString().slice(0, 10)
    form_surat.masa_berlaku = {
        year: 0,
        month: 0,
        day: 0,
    }
    form_surat.jabatan_fungsional = ""
    form_surat.mulai_berlaku = ""
    form_surat.signers = []
    form_surat.approvals = []
    form_surat.signature_type = "digital"
    form_surat.pertelaan_perjanjian_kerja = {
        pendidikan: "",
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
    reset_errors()
}

function open_modal_choose_signer() {
    modal_choose_signer.value.open()
}

function open_modal_choose_approval() {
    modal_choose_approval.value.open()
}

function add_signer(signer) {
    form_surat.signers.push(signer)
}
function add_approval(approval) {
    form_surat.approvals.push(approval)
}

function save_surat() {
    //check if form is valid
    if (!selected_employee.value) {
        Swal.fire({
            icon: "error",
            title: "Gagal",
            text: "Dosen tidak boleh kosong",
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
    if (form_surat.signers.length == 0) {
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

    let payload = {
        letter_template_id: form_surat.letter_template_id,
        tanggal_surat: form_surat.tanggal_surat,
        mulai_berlaku: form_surat.mulai_berlaku,
        masa_berlaku: form_surat.masa_berlaku,
        nomor_surat_sebelumnya: form_surat.nomor_surat_sebelumnya,
        tanggal_surat_sebelumnya: form_surat.tanggal_surat_sebelumnya,
        employee: {
            id: selected_employee.value.id,
        },
        jabatan_fungsional: form_surat.jabatan_fungsional,
        prodi: selected_prodi.value.id,
        rekening: selected_rekening.value.id,
        signers: form_surat.signers,
        approvals: form_surat.approvals,
        signature_type: form_surat.signature_type,
        pertelaan_perjanjian_kerja: form_surat.pertelaan_perjanjian_kerja
    }

    if (form_surat.id != "") {
        // update
        axios.put(`${url}/outcoming-letters/surat-perjanjian-kerja-dosen-full-time/${form_surat.id}`, payload)
            .then(res => {
                Swal.fire({
                    icon: "success",
                    title: "Berhasil",
                    text: res.data.message,
                });
            })
            .catch(err => {
                if (err.response?.status == 422) {
                    Object.entries(err.response.data.errors).forEach(entry => {
                        const [key, value] = entry;
                        errors[key] = value[0]
                    });
                } else {
                    console.log(err)
                }
            }).finally(() => {
                loading.value.close()
            })
    } else {
        // create
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
                if (err.response?.status == 422) {
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
                    Swal.fire({
                        icon: "error",
                        title: "Gagal",
                        text: err.response.data.message,
                    });
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
    loading.value.open()
    if (route.name == 'update_surat_perjanjian_kerja_dosen_full_time') {
        await get_letter(route.params.id)
    }
    await get_letter_templates()
    await get_jabatan_fungsional()
    loading.value.close()
})

</script>

<template>
    <Loading ref="loading"></Loading>
    <SubHeader
        :title="route.name == 'update_surat_perjanjian_kerja_dosen_full_time' ? `Edit Surat Perjanjian Kerja Dosen Full Time` : `Tambah Surat Perjanjian Kerja Dosen Full Time`"
        :back_url="{ name: 'surat_perjanjian_kerja_dosen_full_time' }" />
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
                    <input type="date" class="form-control" required v-model="form_surat.tanggal_surat">
                    <p v-if="errors['tanggal_surat']" class="text-xs text-red-600 mt-2">
                        {{ errors['tanggal_surat'] }}
                    </p>
                </div>
                <!-- <div class="mb-4 grid grid-cols-2 gap-x-8">
                    <div>
                        <label class="block text-sm font-medium mb-2">Nomor Surat Perjanjian Kerja Yang Diamandemen (Tidak
                            Wajib)</label>
                        <input type="text" class="form-control" v-model="form_surat.nomor_surat_sebelumnya"
                            placeholder="Nomor Surat Perjanjian Kerja Yang Diamandemen">
                        <p v-if="errors.nomor_surat_sebelumnya" class="text-xs text-red-600 mt-2">
                            {{ errors.nomor_surat_sebelumnya }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Tanggal Surat Perjanjian Kerja Yang Diamandemen (Tidak
                            Wajib)</label>
                        <input type="date" class="form-control" v-model="form_surat.tanggal_surat_sebelumnya">
                        <p v-if="errors.tanggal_surat_sebelumnya" class="text-xs text-red-600 mt-2">
                            {{ errors.tanggal_surat_sebelumnya }}
                        </p>
                    </div>
                </div> -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Dosen</label>
                    <search-input v-if="!selected_employee" v-model="selected_employee"
                        :url="`${url}/employees?profesi=dosen`" id="employee" placeholder="Cari Dosen ...">
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
                                <small>{{ data.nama_fakultas }} ( {{ data.singkatan_fakultas }})</small>
                                <p class="mb-0">{{ data.nama_prodi }} ({{ data.singkatan_prodi }})</p>
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
                                <small>{{ selected_prodi.nama_fakultas }} ( {{ selected_prodi.singkatan_fakultas }})</small>
                                <p class="mb-0">{{ selected_prodi.nama_prodi }} ( {{ selected_prodi.singkatan_prodi }})</p>
                            </div>
                            <span @click="reset_prodi"
                                class="p-3 hover:bg-red-200 rounded-full cursor-pointer transition ease-in-out duration-500">
                                <Icon icon="jam:trash" class="text-red-600 text-2xl" />
                            </span>
                        </div>
                    </div>
                    <div class="mb-4">
                        <!-- <div>
                            <label class="block text-sm font-medium mb-2">Profesi</label>
                            <input type="text" class="form-control" required v-model="form_surat.profesi"
                                placeholder="Profesi">
                            <p v-if="errors.profesi" class="text-xs text-red-600 mt-2">
                                {{ errors.profesi }}
                            </p>
                        </div>
                        <div> -->
                        <label class="block text-sm font-medium mb-2">Jabatan Fungsional</label>
                        <custom-select :required="true" v-model="form_surat.jabatan_fungsional" :data="jabatan_fungsional"
                            placeholder="Jabatan Fungsional"></custom-select>
                        <!-- <input type="text" class="form-control" required v-model="form_surat.jabatan_fungsional"
                                placeholder="Jabatan Fungsional"> -->
                        <p v-if="errors.jabatan_fungsional" class="text-xs text-red-600 mt-2">
                            {{ errors.jabatan_fungsional }}
                        </p>
                        <!-- </div> -->
                    </div>
                </template>
                <hr class="my-4" />
                <div class="mb-4">
                    <label class="block text-sm font-bold mb-2">JANGKA WAKTU KERJA</label>
                    <hr class="my-4" />
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Mulai Berlaku</label>
                        <input type="date" class="form-control" required v-model="form_surat.mulai_berlaku"
                            placeholder="Tanggal Mulai Magang">
                        <p v-if="errors.mulai_berlaku" class="text-xs text-red-600 mt-2">
                            {{ errors.mulai_berlaku }}
                        </p>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Masa Berlaku</label>
                        <div class="grid grid-cols-3 gap-x-4">
                            <div>
                                <label class="block text-xs font-medium mb-2">Tahun</label>
                                <input type="number" class="form-control" v-model="form_surat.masa_berlaku.year"
                                    placeholder="Tahun">
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-2">Bulan</label>
                                <input type="number" class="form-control" v-model="form_surat.masa_berlaku.month"
                                    placeholder="Bulan">
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-2">Hari</label>
                                <input type="number" class="form-control" v-model="form_surat.masa_berlaku.day"
                                    placeholder="Hari">
                            </div>
                        </div>
                        <p v-if="errors['masa_berlaku']" class="text-xs text-red-600 mt-2">
                            {{ errors['masa_berlaku'] }}
                        </p>
                    </div>
                </div>
                <hr class="my-4" />
                <div class="mb-4">
                    <label class="block text-sm font-bold mb-2">PERTELAAN PERJANJIAN KERJA</label>
                    <hr class="my-4" />
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Pendidikan</label>
                        <input type="text" class="form-control" required
                            v-model="form_surat.pertelaan_perjanjian_kerja.pendidikan">
                        <p v-if="errors['pertelaan_perjanjian_kerja.pendidikan']" class="text-xs text-red-600 mt-2">
                            {{ errors['pertelaan_perjanjian_kerja.pendidikan'] }}
                        </p>
                    </div>
                    <!-- <div class="grid grid-cols-2 gap-x-4 mb-4">
                    </div> -->
                    <div class="grid grid-cols-2 mb-4 divide-x">
                        <div class="pr-10">
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">Tahun Penggajian Pertama</label>
                                <input type="number" class="form-control"
                                    v-model="form_surat.pertelaan_perjanjian_kerja.tahun_satu" placeholder="cth : 2023">
                                <p v-if="errors['pertelaan_perjanjian_kerja.tahun_satu']" class="text-xs text-red-600 mt-2">
                                    {{ errors['pertelaan_perjanjian_kerja.tahun_satu'] }}
                                </p>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">Tunjangan Dasar</label>
                                <input type="number" class="form-control"
                                    v-model="form_surat.pertelaan_perjanjian_kerja.tunjangan_dasar_satu"
                                    placeholder="Tunjangan Dasar">
                                <p v-if="errors['pertelaan_perjanjian_kerja.tunjangan_dasar_satu']"
                                    class="text-xs text-red-600 mt-2">
                                    {{ errors['pertelaan_perjanjian_kerja.tunjangan_dasar_satu'] }}
                                </p>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">Tunjangan Fungsional</label>
                                <input type="number" class="form-control"
                                    v-model="form_surat.pertelaan_perjanjian_kerja.tunjangan_fungsional_satu"
                                    placeholder="Tunjangan Fungsional">
                                <p v-if="errors['pertelaan_perjanjian_kerja.tunjangan_fungsional_satu']"
                                    class="text-xs text-red-600 mt-2">
                                    {{ errors['pertelaan_perjanjian_kerja.tunjangan_fungsional_satu'] }}
                                </p>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">Tunjangan Struktural</label>
                                <input type="number" class="form-control"
                                    v-model="form_surat.pertelaan_perjanjian_kerja.tunjangan_struktural_satu"
                                    placeholder="Tunjangan Struktural">
                                <p v-if="errors['pertelaan_perjanjian_kerja.tunjangan_struktural_satu']"
                                    class="text-xs text-red-600 mt-2">
                                    {{ errors['pertelaan_perjanjian_kerja.tunjangan_struktural_satu'] }}
                                </p>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">Tunjangan Kemahalan</label>
                                <input type="number" class="form-control"
                                    v-model="form_surat.pertelaan_perjanjian_kerja.tunjangan_kemahalan_satu"
                                    placeholder="Tunjangan Kemahalan">
                                <p v-if="errors['pertelaan_perjanjian_kerja.tunjangan_kemahalan_satu']"
                                    class="text-xs text-red-600 mt-2">
                                    {{ errors['pertelaan_perjanjian_kerja.tunjangan_kemahalan_satu'] }}
                                </p>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">Pendapatan Bulanan</label>
                                <input type="number" class="form-control"
                                    v-model="form_surat.pertelaan_perjanjian_kerja.pendapatan_bulanan_satu"
                                    placeholder="Pendapatan Bulanan">
                                <p v-if="errors['pertelaan_perjanjian_kerja.pendapatan_bulanan_satu']"
                                    class="text-xs text-red-600 mt-2">
                                    {{ errors['pertelaan_perjanjian_kerja.pendapatan_bulanan_satu'] }}
                                </p>
                            </div>
                        </div>
                        <div class="pl-10">
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">Tahun Penggajian Kedua</label>
                                <input type="number" class="form-control"
                                    v-model="form_surat.pertelaan_perjanjian_kerja.tahun_dua" placeholder="cth : 2023">
                                <p v-if="errors['pertelaan_perjanjian_kerja.tahun_dua']" class="text-xs text-red-600 mt-2">
                                    {{ errors['pertelaan_perjanjian_kerja.tahun_dua'] }}
                                </p>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">Tunjangan Dasar</label>
                                <input type="number" class="form-control"
                                    v-model="form_surat.pertelaan_perjanjian_kerja.tunjangan_dasar_dua"
                                    placeholder="Tunjangan Dasar">
                                <p v-if="errors['pertelaan_perjanjian_kerja.tunjangan_dasar_dua']"
                                    class="text-xs text-red-600 mt-2">
                                    {{ errors['pertelaan_perjanjian_kerja.tunjangan_dasar_dua'] }}
                                </p>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">Tunjangan Fungsional</label>
                                <input type="number" class="form-control"
                                    v-model="form_surat.pertelaan_perjanjian_kerja.tunjangan_fungsional_dua"
                                    placeholder="Tunjangan Fungsional">
                                <p v-if="errors['pertelaan_perjanjian_kerja.tunjangan_fungsional_dua']"
                                    class="text-xs text-red-600 mt-2">
                                    {{ errors['pertelaan_perjanjian_kerja.tunjangan_fungsional_dua'] }}
                                </p>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">Tunjangan Struktural</label>
                                <input type="number" class="form-control"
                                    v-model="form_surat.pertelaan_perjanjian_kerja.tunjangan_struktural_dua"
                                    placeholder="Tunjangan Struktural">
                                <p v-if="errors['pertelaan_perjanjian_kerja.tunjangan_struktural_dua']"
                                    class="text-xs text-red-600 mt-2">
                                    {{ errors['pertelaan_perjanjian_kerja.tunjangan_struktural_dua'] }}
                                </p>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">Tunjangan Kemahalan</label>
                                <input type="number" class="form-control"
                                    v-model="form_surat.pertelaan_perjanjian_kerja.tunjangan_kemahalan_dua"
                                    placeholder="Tunjangan Kemahalan">
                                <p v-if="errors['pertelaan_perjanjian_kerja.tunjangan_kemahalan_dua']"
                                    class="text-xs text-red-600 mt-2">
                                    {{ errors['pertelaan_perjanjian_kerja.tunjangan_kemahalan_dua'] }}
                                </p>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">Pendapatan Bulanan</label>
                                <input type="number" class="form-control"
                                    v-model="form_surat.pertelaan_perjanjian_kerja.pendapatan_bulanan_dua"
                                    placeholder="Pendapatan Bulanan">
                                <p v-if="errors['pertelaan_perjanjian_kerja.pendapatan_bulanan_dua']"
                                    class="text-xs text-red-600 mt-2">
                                    {{ errors['pertelaan_perjanjian_kerja.pendapatan_bulanan_dua'] }}
                                </p>
                            </div>
                        </div>

                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Fasilitas Lainnya</label>
                        <div class="flex gap-x-4">
                            <input type="text" class="form-control" v-model="fasilitas_lainnya"
                                placeholder="Tugas Dan Tanggung Jawab">
                            <button type="button"
                                @click="form_surat.pertelaan_perjanjian_kerja.fasilitas_lainnya.push(fasilitas_lainnya), fasilitas_lainnya = ''"
                                class="btn bg-blue-500 text-white">Tambah</button>
                        </div>
                        <p v-if="errors['pertelaan_perjanjian_kerja.fasilitas_lainnya']" class="text-xs text-red-600 mt-2">
                            {{ errors['pertelaan_perjanjian_kerja.fasilitas_lainnya'] }}
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
                    <p class="text-center text-primary cursor-pointer w-full form-control hover:bg-primary-500/25"
                        @click="open_modal_choose_signer">
                        Pilih Penandatangan
                    </p>
                    <p v-if="errors.signers" class="text-xs text-red-600 mt-2" id="signers-error">{{
                        errors.signers }}</p>
                    <template v-if="form_surat.signers.length > 0">
                        <div v-for="(signer, index) in form_surat.signers" :key="index"
                            class="form-control bg-primary-200/20  mt-2 flex justify-between items-center gap-x-4">
                            <div class="w-full">
                                <p>{{ signer.employee.name }}</p>
                                <small>{{ signer.position }}</small>
                            </div>
                            <span @click="form_surat.signers.splice(index, 1)"
                                class="p-3 hover:bg-red-200 rounded-full cursor-pointer transition ease-in-out duration-500">
                                <Icon icon="jam:trash" class="text-red-600 text-2xl" />
                            </span>
                        </div>
                    </template>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Pegawai Yang Approval</label>
                    <p class="text-center text-primary cursor-pointer w-full form-control hover:bg-primary-500/25"
                        @click="open_modal_choose_approval">
                        Pilih Pegawai Yang Approval
                    </p>
                    <p v-if="errors.approvals" class="text-xs text-red-600 mt-2" id="approvals-error">{{
                        errors.approvals }}</p>
                    <template v-if="form_surat.approvals.length > 0">
                        <div v-for="(approval, index) in form_surat.approvals" :key="index"
                            class="form-control bg-primary-200/20  mt-2 flex justify-between items-center gap-x-4">
                            <div class="w-full">
                                <p>{{ approval.employee.name }}</p>
                                <small>{{ approval.position }}</small>
                            </div>
                            <span @click="form_surat.approvals.splice(index, 1)"
                                class="p-3 hover:bg-red-200 rounded-full cursor-pointer transition ease-in-out duration-500">
                                <Icon icon="jam:trash" class="text-red-600 text-2xl" />
                            </span>
                        </div>
                    </template>
                </div>
                <div class="flex justify-end gap-x-6">
                    <router-link :to="{ name: 'surat_perjanjian_kerja_dosen_full_time' }"
                        class="btn btn-outline border hover:border-primary-500 px-24 py-3">Kembali</router-link>
                    <button class="btn btn-primary px-24 py-3">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <template v-if="selected_employee">
        <AddRekening ref="modal_add_rekening" :employee="selected_employee" @inserted="search_input_rekening.fetchData()" />
        <AddProdi ref="modal_add_prodi" @inserted="search_input_prodi.fetchData()" />
    </template>

    <ChooseEmployee ref="modal_choose_signer" :title="'Pilih Penandatangan'" @choosed="add_signer" />
    <ChooseEmployee ref="modal_choose_approval" :title="'Pilih Pegawai Yang Approval'" @choosed="add_approval" />
</template>