<script setup>
import { ref, reactive, onMounted } from 'vue'
import { Icon } from '@iconify/vue'
import axios from 'axios'
import SubHeader from '@/components/SubHeader.vue'
import SearchInput from '@/components/SearchInput.vue';
import CustomSelect from '@/components/CustomSelect.vue';
import AddRekening from '@/components/AddRekening.vue';
import ChooseEmployee from '@/components/ChooseEmployee.vue';
import { useRoute } from 'vue-router';

const route = useRoute()
const url = import.meta.env.VITE_URL_API
const NAMA_SURAT = "SURAT_PERJANJIAN_KERJA_MAGANG"

const loading = ref(null)
const search_input_rekening = ref(null)
const modal_choose_signer = ref(null)
const modal_choose_approval = ref(null)

const tugas = ref("")

const form_surat = reactive({
    id: "",
    is_private: false,
    letter_template_id: "",
    tanggal_surat: new Date().toISOString().slice(0, 10),
    masa_berlaku: {
        year: 0,
        month: 0,
        day: 0,
    },
    employee: {},
    rekening: {},
    mulai_berlaku: "",
    tempat_kerja: "",
    tugas: [],
    upah: 0,
    penanggung_pembayaran: "",
    signers: [],
    approvals: [],
    signature_type: "digital",
})

const errors = reactive({
    is_private: "",
    letter_template_id: "",
    tanggal_surat: "",
    masa_berlaku: "",
    "employee.id": "",
    rekening: "",
    mulai_berlaku: "",
    tempat_kerja: "",
    tugas: "",
    upah: "",
    penanggung_pembayaran: "",
    signers: "",
    approvals: "",
    signature_type: ""
})

const letter_templates = ref([])
const selected_employee = ref(null)
const selected_rekening = ref(null)
const modal_add_rekening = ref(null)

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

async function get_letter(id) {
    await axios.get(`${url}/outcoming-letters/surat-perjanjian-kerja-magang/${id}`)
        .then(res => {
            let data = res.data.data
            form_surat.id = data.id
            form_surat.is_private = data.is_private
            form_surat.letter_template_id = data.letter_template_id
            form_surat.tanggal_surat = data.tanggal_surat_raw
            form_surat.masa_berlaku = data.masa_berlaku
            form_surat.employee = data.employee
            form_surat.rekening = data.rekening
            form_surat.mulai_berlaku = data.mulai_berlaku
            form_surat.tempat_kerja = data.tempat_kerja
            form_surat.tugas = data.tugas
            form_surat.upah = data.upah
            form_surat.penanggung_pembayaran = data.penanggung_pembayaran
            form_surat.signers = res.data.data.signers
            form_surat.approvals = res.data.data.approvals
            form_surat.signature_type = data.signature_type
            selected_employee.value = data.employee
            selected_rekening.value = data.rekening
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

function reset_form() {
    form_surat.id = ""
    form_surat.is_private = false
    form_surat.letter_template_id = ""
    form_surat.tanggal_surat = new Date().toISOString().slice(0, 10)
    form_surat.masa_berlaku = {
        year: 0,
        month: 0,
        day: 0,
    }
    form_surat.mulai_berlaku = ""
    form_surat.tempat_kerja = ""
    form_surat.tugas = []
    form_surat.upah = 0
    form_surat.penanggung_pembayaran = ""
    form_surat.signers = []
    form_surat.approvals = []
    form_surat.signature_type = "digital"
    reset_employee()
    reset_rekening()
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
        is_private: form_surat.is_private,
        letter_template_id: form_surat.letter_template_id,
        tanggal_surat: form_surat.tanggal_surat,
        mulai_berlaku: form_surat.mulai_berlaku,
        masa_berlaku: form_surat.masa_berlaku,
        employee: {
            id: selected_employee.value.id,
        },
        rekening: selected_rekening.value.id,
        tempat_kerja: form_surat.tempat_kerja,
        tugas: form_surat.tugas,
        upah: form_surat.upah,
        penanggung_pembayaran: form_surat.penanggung_pembayaran,
        signers: form_surat.signers,
        approvals: form_surat.approvals,
        signature_type: form_surat.signature_type,
    }
    //mulai berlaku masih jadi PR

    if (form_surat.id != "") {
        // update
        axios.put(`${url}/outcoming-letters/surat-perjanjian-kerja-magang/${form_surat.id}`, payload)
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
                        text: "Silahkan periksa kembali form anda",
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
        axios.post(`${url}/outcoming-letters/surat-perjanjian-kerja-magang`, payload)
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
                        text: "Silahkan periksa kembali form anda",
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

function open_modal_add_rekening() {
    modal_add_rekening.value.open()
}

onMounted(async () => {
    loading.value.open()
    if (route.name == 'update_surat_perjanjian_kerja_magang') {
        await get_letter(route.params.id)
    }
    await get_letter_templates()
    loading.value.close()
})

</script>

<template>
    <Loading ref="loading"></Loading>
    <SubHeader
        :title="route.name == 'update_surat_perjanjian_kerja_magang' ? `Edit Surat Perjanjian Kerja Magang` : `Tambah Surat Perjanjian Kerja Magang`"
        :back_url="{ name: 'surat_perjanjian_kerja_magang' }" />
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
                    <label class="block text-sm font-medium mb-2">Sifat Surat : Rahasia atau tidak ?</label>
                    <input type="radio" name="is_private" value="false" @click="form_surat.is_private = false"
                        :checked="!form_surat.is_private"> Tidak Rahasia
                    <input class="ml-4" type="radio" name="is_private" value="true" @click="form_surat.is_private = true"
                        :checked="form_surat.is_private"> Rahasia
                    <p v-if="errors.is_private" class="text-xs text-red-600 mt-2" id="is-private-error">
                        {{ errors.is_private }}
                    </p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Tanggal Surat</label>
                    <input type="date" class="form-control" required v-model="form_surat.tanggal_surat">
                    <p v-if="errors['tanggal_surat']" class="text-xs text-red-600 mt-2">
                        {{ errors['tanggal_surat'] }}
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
                <div class="mb-4" v-if="selected_employee">
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
                <hr class="my-4" />
                <div class="mb-4">
                    <label class="block text-sm font-bold mb-2">MASA MAGANG, TUGAS, TANGGUNG JAWAB DAN TEMPAT
                        MAGANG</label>
                    <hr class="my-4" />
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Mulai Magang</label>
                        <input type="date" class="form-control" required v-model="form_surat.mulai_berlaku"
                            placeholder="Tanggal Mulai Magang">
                        <p v-if="errors.mulai_berlaku" class="text-xs text-red-600 mt-2">
                            {{ errors.mulai_berlaku }}
                        </p>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Masa Magang</label>
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
                            <input type="text" class="form-control" v-model="tugas" placeholder="Tugas Dan Tanggung Jawab">
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
                </div>
                <hr class="my-4" />
                <div class="mb-4">
                    <label class="block text-sm font-bold mb-2">FASILITAS</label>
                    <hr class="my-4" />
                    <div class="grid grid-cols-2 gap-x-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Upah</label>
                            <input type="number" class="form-control" required v-model="form_surat.upah" placeholder="Upah">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Penanggung Pembayaran</label>
                            <input type="text" class="form-control" required v-model="form_surat.penanggung_pembayaran"
                                placeholder="Penanggung Pembayaran">
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
                    <router-link :to="{ name: 'surat_perjanjian_kerja_magang' }"
                        class="btn btn-outline border hover:border-primary-500 px-24 py-3">Kembali</router-link>
                    <button class="btn btn-primary px-24 py-3">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <template v-if="selected_employee">
        <AddRekening ref="modal_add_rekening" :employee="selected_employee" @inserted="search_input_rekening.fetchData()" />
    </template>

    <ChooseEmployee ref="modal_choose_signer" :title="'Pilih Penandatangan'" @choosed="add_signer" />
    <ChooseEmployee ref="modal_choose_approval" :title="'Pilih Pegawai Yang Approval'" @choosed="add_approval" />
</template>