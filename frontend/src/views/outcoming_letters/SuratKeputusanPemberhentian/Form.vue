<script setup>
import { ref, reactive, onMounted } from 'vue'
import { Icon } from '@iconify/vue'
import axios from 'axios'
import SubHeader from '@/components/SubHeader.vue'
import SearchInput from '@/components/SearchInput.vue';
import CustomSelect from '@/components/CustomSelect.vue';

const url = import.meta.env.VITE_URL_API
const NAMA_SURAT = "SURAT_KEPUTUSAN_PEMBERHENTIAN"

const loading = ref(null)

const form_surat = reactive({
    id: "",
    letter_template_id: "",
    nomor_berita_acara: "",
    tanggal_berita_acara: "",
    pemberhentian_dalam_jabatan: "",
    dikembalikan_ke_jabatan: "",
    keterangan_tambahan: "",
    signer: {
        position: "",
    },
    signature_type: "manual",
    tanggal_berlaku: "",
})

const errors = reactive({
    letter_template_id: "",
    nomor_berita_acara: "",
    tanggal_berita_acara: "",
    employee: {
        nip: "",
    },
    pemberhentian_dalam_jabatan: "",
    dikembalikan_ke_jabatan: "",
    keterangan_tambahan: "",
    signer: {
        nip: "",
        position: "",
    },
    signature_type: "",
    tanggal_berlaku: "",
})

const letter_templates = ref([])
const selected_employee = ref(null)
const selected_signer = ref(null)

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
    errors.nomor_berita_acara = ""
    errors.tanggal_berita_acara = ""
    errors.employee.nip = ""
    errors.pemberhentian_dalam_jabatan = ""
    errors.dikembalikan_ke_jabatan = ""
    errors.keterangan_tambahan = ""
    errors.signer.nip = ""
    errors.signer.position = ""
    errors.signature_type = ""
    errors.tanggal_berlaku = ""
}

function reset_employee() {
    selected_employee.value = null
    form_surat.employee = {
        nip: "",
        position: "",
    }
}

function reset_signer() {
    selected_signer.value = null
    form_surat.signer = {
        nip: "",
        position: "",
    }
}

function reset_form() {
    form_surat.id = ""
    form_surat.letter_template_id = ""
    form_surat.nomor_berita_acara = ""
    form_surat.tanggal_berita_acara = ""
    form_surat.pemberhentian_dalam_jabatan = ""
    form_surat.dikembalikan_ke_jabatan = ""
    form_surat.keterangan_tambahan = ""
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

    loading.value.open()
    if (form_surat.id.trim()) {
        // update
    } else {
        // create
        let payload = {
            letter_template_id: form_surat.letter_template_id,
            nomor_berita_acara: form_surat.nomor_berita_acara,
            tanggal_berita_acara: form_surat.tanggal_berita_acara,
            employee: {
                nip: selected_employee.value.nip,
            },
            pemberhentian_dalam_jabatan: form_surat.pemberhentian_dalam_jabatan,
            dikembalikan_ke_jabatan: form_surat.dikembalikan_ke_jabatan,
            keterangan_tambahan: form_surat.keterangan_tambahan,
            signer: {
                nip: selected_signer.value.nip,
                position: form_surat.signer.position
            },
            signature_type: form_surat.signature_type,
            tanggal_berlaku: form_surat.tanggal_berlaku,
        }

        axios.post(`${url}/outcoming-letters/surat-keputusan-pemberhentian`, payload)
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
                    errors.nomor_berita_acara = err.response.data.errors.nomor_berita_acara[0]
                    errors.tanggal_berita_acara = err.response.data.errors.tanggal_berita_acara[0]
                    errors.employee.nip = err.response.data.errors.employee.nip[0]
                    errors.pemberhentian_dalam_jabatan = err.response.data.errors.pemberhentian_dalam_jabatan[0]
                    errors.dikembalikan_ke_jabatan = err.response.data.errors.dikembalikan_ke_jabatan[0]
                    errors.keterangan_tambahan = err.response.data.errors.keterangan_tambahan[0]
                    errors.signer.nip = err.response.data.errors.signer.nip[0]
                    errors.signer.position = err.response.data.errors.signer.position[0]
                    errors.signature_type = err.response.data.errors.signature_type[0]
                    errors.tanggal_berlaku = err.response.data.errors.tanggal_berlaku[0]
                } else {
                    console.log(err)
                }
            }).finally(() => {
                loading.value.close()
            })
    }

}

onMounted(async () => {
    get_letter_templates()
})

</script>

<template>
    <Loading ref="loading"></Loading>
    <SubHeader :title="`Tambah Surat Keputusan Pemberhentian`" />
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
                    <p v-if="errors.employee.nip" class="text-xs text-red-600 mt-2" id="employee-error">
                        {{ errors.employee.nip }}
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
                        <label class="block text-sm font-medium mb-2">Diberhentikan dari jabatan</label>
                        <custom-select :required="true" v-model="form_surat.pemberhentian_dalam_jabatan"
                            :data="selected_employee.positions" placeholder="Diberhentikan dari jabatan"></custom-select>
                        <p v-if="errors.pemberhentian_dalam_jabatan" class="text-xs text-red-600 mt-2"
                            id="employee-jabatan_awal-error">
                            {{ errors.pemberhentian_dalam_jabatan }}
                        </p>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Dikembalikan fungsinya sebagai</label>
                        <custom-select :required="true" v-model="form_surat.dikembalikan_ke_jabatan"
                            :data="selected_employee.positions" placeholder="Dikembalikan ke jabatan"></custom-select>
                        <p v-if="errors.dikembalikan_ke_jabatan" class="text-xs text-red-600 mt-2"
                            id="employee-jabatan_awal-error">
                            {{ errors.dikembalikan_ke_jabatan }}
                        </p>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Keterangan Tambahan Untuk Putusan Poin 2</label>
                        <textarea class="form-control" v-model="form_surat.keterangan_tambahan"
                            placeholder="Cth: dengan menjalankan penugasan studi lanjut S3 sesuai dengan dokumen penetapan"></textarea>
                        <p v-if="errors.keterangan_tambahan" class="text-xs text-red-600 mt-2"
                            id="employee-jabatan_awal-error">
                            {{ errors.keterangan_tambahan }}
                        </p>
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
                    <p v-if="errors.signer.nip" class="text-xs text-red-600 mt-2" id="signer-nip-error">
                        {{ errors.signer.nip }}
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
</template>