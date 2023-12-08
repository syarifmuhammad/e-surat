<script setup>
import { ref, reactive, onMounted } from 'vue'
import { Icon } from '@iconify/vue'
import axios from 'axios'
import SubHeader from '@/components/SubHeader.vue'
import SearchInput from '@/components/SearchInput.vue';
import CustomSelect from '@/components/CustomSelect.vue';
import AddRekening from '@/components/AddRekening.vue';

const url = import.meta.env.VITE_URL_API
const NAMA_SURAT = "SURAT_PERJANJIAN_KERJA_DOSEN_LUAR_BIASA"

const loading = ref(null)
const search_input_rekening = ref(null)

const tugas = ref("")

const form_surat = reactive({
    id: "",
    letter_template_id: "",
    jabatan_fungsional: "",
    nidn: "",
    mata_kuliah: "",
    tahun_ajaran: "",
    semester: "ganjil",
    upah: 0,
    transportasi: 0,
    signer: {
        position: "",
    },
    signature_type: "manual",
})

const errors = reactive({
    letter_template_id: "",
    employee: {
        id: "",
    },
    jabatan_fungsional: "",
    nidn: "",
    mata_kuliah: "",
    tahun_ajaran: "",
    semester: "",
    rekening: "",
    upah: "",
    transportasi: "",
    signer: {
        id: "",
        position: "",
    },
    signature_type: ""
})

const letter_templates = ref([])
const selected_employee = ref(null)
const selected_rekening = ref(null)
const selected_signer = ref(null)
const modal_add_rekening = ref(null)

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
    errors.employee.id = ""
    errors.jabatan_fungsional = ""
    errors.nidn = ""
    errors.mata_kuliah = ""
    errors.tahun_ajaran = ""
    errors.semester = ""
    errors.rekening = ""
    errors.upah = ""
    errors.transportasi = ""
    errors.signer.id = ""
    errors.signer.position = ""
    errors.signature_type = ""
}

function reset_employee() {
    selected_employee.value = null
}

function reset_rekening() {
    selected_rekening.value = null
}

function reset_signer() {
    selected_signer.value = null
    form_surat.signer = {
        position: "",
    }
}

function reset_form() {
    form_surat.id = ""
    form_surat.letter_template_id = ""
    form_surat.jabatan_fungsional = ""
    form_surat.nidn = ""
    form_surat.mata_kuliah = ""
    form_surat.tahun_ajaran = ""
    form_surat.semester = ""
    form_surat.upah = 0
    form_surat.transportasi = 0
    form_surat.signature_type = "manual"
    reset_employee()
    reset_rekening()
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
            employee: {
                id: selected_employee.value.id,
            },
            jabatan_fungsional: form_surat.jabatan_fungsional,
            nidn: form_surat.nidn,
            mata_kuliah: form_surat.mata_kuliah,
            tahun_ajaran: form_surat.tahun_ajaran,
            semester: form_surat.semester,
            rekening: selected_rekening.value.id,
            upah: form_surat.upah,
            transportasi: form_surat.transportasi,
            signer: {
                id: selected_signer.value.id,
                position: form_surat.signer.position
            },
            signature_type: form_surat.signature_type,
        }

        axios.post(`${url}/outcoming-letters/surat-perjanjian-kerja-dosen-luar-biasa`, payload)
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
                    errors.employee.id = err.response.data.errors.employee.id[0]
                    errors.jabatan_fungsional = err.response.data.errors.jabatan_fungsional[0]
                    errors.nidn = err.response.data.errors.nidn[0]
                    errors.mata_kuliah = err.response.data.errors.mata_kuliah[0]
                    errors.tahun_ajaran = err.response.data.errors.tahun_ajaran[0]
                    errors.semester = err.response.data.errors.semester[0]
                    errors.rekening = err.response.data.errors.rekening[0]
                    errors.upah = err.response.data.errors.upah[0]
                    errors.transportasi = err.response.data.errors.transportasi[0]
                    errors.signer.id = err.response.data.errors.signer.id[0]
                    errors.signer.position = err.response.data.errors.signer.position[0]
                    errors.signature_type = err.response.data.errors.signature_type[0]
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

onMounted(async () => {
    get_letter_templates()
})

</script>

<template>
    <Loading ref="loading"></Loading>
    <SubHeader :title="`Tambah Surat Perjanjian Kerja Dosen Luar Biasa`" />
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
                    <div class="grid grid-cols-2 gap-x-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">NIDN</label>
                            <input type="text" class="form-control" required v-model="form_surat.nidn"
                                placeholder="NIDN">
                            <p v-if="errors.nidn" class="text-xs text-red-600 mt-2">
                                {{ errors.nidn }}
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
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Mata Kuliah</label>
                        <input type="text" class="form-control" required v-model="form_surat.mata_kuliah"
                            placeholder="Mata Kuliah">
                        <p v-if="errors.mata_kuliah" class="text-xs text-red-600 mt-2">
                            {{ errors.mata_kuliah }}
                        </p>
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
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Tugas Dan Tanggung Jawab</label>
                        <div class="flex gap-x-4">
                            <input type="text" class="form-control" v-model="tugas"
                                placeholder="Tugas Dan Tanggung Jawab">
                            <button type="button" @click="form_surat.tugas.push(tugas), tugas = ''"
                                class="btn bg-blue-500 text-white">Tambah</button>
                        </div>
                        <p v-if="errors.tugas" class="text-xs text-red-600 mt-2">
                            {{ errors.tugas }}
                        </p>
                        <div v-if="form_surat.tugas.length > 0" class="mt-4">
                            <div v-for="(t, index) in form_surat.tugas" :key="index"
                                class="form-control bg-primary-200/20  mt-2 flex justify-between items-center gap-x-4">
                                <div class="w-full">
                                    <p>{{ t }}</p>
                                </div>
                                <span @click="form_surat.tugas.splice(index, 1)"
                                    class="p-3 hover:bg-red-200 rounded-full cursor-pointer transition ease-in-out duration-500">
                                    <Icon icon="jam:trash" class="text-red-600 text-2xl" />
                                </span>
                            </div>
                        </div>
                    </div>
                </div> -->
                <hr class="my-4" />
                <div class="mb-4">
                    <label class="block text-sm font-bold mb-2">JANGKA WAKTU KERJA</label>
                    <hr class="my-4" />
                    <div class="grid grid-cols-2 gap-x-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Tahun Ajaran</label>
                            <input type="text" class="form-control" required v-model="form_surat.tahun_ajaran" placeholder="cth : 2023/2024">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Semester</label>
                            <select class="form-control" required v-model="form_surat.semester">
                                <option value="ganjil">Ganjil</option>
                                <option value="genap">Genap</option>
                            </select>
                        </div>
                    </div>
                </div>
                <hr class="my-4" />
                <div class="mb-4">
                    <label class="block text-sm font-bold mb-2">UPAH DAN TRANSPORTASI</label>
                    <hr class="my-4" />
                    <div class="grid grid-cols-2 gap-x-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Upah</label>
                            <input type="number" class="form-control" required v-model="form_surat.upah" placeholder="Upah">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Transportasi</label>
                            <input type="number" class="form-control" required v-model="form_surat.transportasi" placeholder="Transportasi">
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
    </template>
</template>