<script setup>
import { ref, reactive, onMounted } from 'vue'
import { Icon } from '@iconify/vue'
import axios from 'axios'
import SubHeader from '@/components/SubHeader.vue'
import SearchInput from '@/components/SearchInput.vue';
import CustomSelect from '@/components/CustomSelect.vue';

const url = import.meta.env.VITE_URL_API
const NAMA_SURAT = "SURAT_KETERANGAN_KERJA"

const loading = ref(null)

const form_surat = reactive({
    id: "",
    letter_template_id: "",
    employee: {
        position: "",
    },
    signer: {
        position: "",
    },
    signature_type: "manual",
})

const errors = reactive({
    letter_template_id: "",
    employee: {
        nip: "",
        position: "",
    },
    signer: {
        nip: "",
        position: "",
    },
    signature_type: ""
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
    errors.employee.nip = ""
    errors.employee.position = ""
    errors.signer.nip = ""
    errors.signer.position = ""
    errors.signature_type = ""
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
    form_surat.employee = {
        nip: "",
        position: "",
    }
    form_surat.signer = {
        nip: "",
        position: "",
    }
    form_surat.signature_type = "manual"
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
            employee: {
                nip: selected_employee.value.nip,
                position: form_surat.employee.position
            },
            signer: {
                nip: selected_signer.value.nip,
                position: form_surat.signer.position
            },
            signature_type: form_surat.signature_type,
        }

        axios.post(`${url}/outcoming-letters/surat-keterangan-kerja`, payload)
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
                    errors.employee.nip = err.response.data.errors.employee.nip[0]
                    errors.signer.nip = err.response.data.errors.signer.nip[0]
                    errors.employee.position = err.response.data.errors.employee.position[0]
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

onMounted(async () => {
    get_letter_templates()
})

</script>

<template>
    <Loading ref="loading"></Loading>
    <SubHeader :title="`Tambah Surat Keterangan Kerja`" />
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
                <div class="mb-4" v-if="selected_employee">
                    <label class="block text-sm font-medium mb-2">Pilih Jabatan Pegawai</label>
                    <custom-select :required="true" v-model="form_surat.employee.position"
                        :data="selected_employee.positions" placeholder="Jabatan pegawai"></custom-select>
                    <p v-if="errors.employee.position" class="text-xs text-red-600 mt-2" id="employee-position-error">
                        {{ errors.employee.position }}
                    </p>
                </div>
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