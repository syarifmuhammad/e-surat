<script setup>
import { ref, reactive, onMounted } from 'vue'
import { Icon } from '@iconify/vue'
import axios from 'axios'
import SubHeader from '@/components/SubHeader.vue'
import CustomTable from '@/components/CustomTable.vue';
import Modal from '@/components/Modal.vue';
import SearchInput from '@/components/SearchInput.vue';

const url = 'https://9056e12e-0ee3-4a34-aa6e-778790fa873f.mock.pstmn.io/api'

const thead = [
    "#",
    "Nomor Surat",
    "NIP",
    "Nama Pegawai",
    "Nama Penandatangan",
    "Tanggal Surat",
    "Aksi",
]

const loading = ref(null)
const modal_form_surat_keterangan_kerja = ref(null)

const form_surat_keterangan_kerja = reactive({
    id: "",
    employee: {
        id: "",
        nip: "",
        name: "",
        position: "",
    },
    signer: {
        id: "",
        nip: "",
        name: "",
        position: "",
    }
})

function reset_employee() {
    form_surat_keterangan_kerja.employee = {
        id: "",
        nip: "",
        name: "",
        position: "",
    }
}

function reset_signer() {
    form_surat_keterangan_kerja.signer = {
        id: "",
        nip: "",
        name: "",
        position: "",
    }
}

function open_modal_form_surat_keterangan_kerja(form_surat_keterangan_kerja_args = null) {
    if (form_surat_keterangan_kerja_args) {
        form_surat_keterangan_kerja.id = form_surat_keterangan_kerja_args.id
        form_surat_keterangan_kerja.employee = form_surat_keterangan_kerja_args.employee
        form_surat_keterangan_kerja.signer = form_surat_keterangan_kerja_args.signer
    }
    HSOverlay.open(modal_form_surat_keterangan_kerja.value.$el)
}

function save_surat_keterangan_kerja() {
    //check if form is valid
    if (!form_surat_keterangan_kerja.employee.id.trim()) {
        Swal.fire({
            icon: "error",
            title: "Gagal",
            text: "Pegawai tidak boleh kosong",
        });
        return
    }
    if (!form_surat_keterangan_kerja.signer.id.trim()) {
        Swal.fire({
            icon: "error",
            title: "Gagal",
            text: "Penandatangan tidak boleh kosong",
        });
        return
    }
    //end of check if form is valid

    HSOverlay.open(loading.value.$el)
    if (form_surat_keterangan_kerja.id.trim()) {
        // update
    } else {
        // create
        let payload = {
            name: form_surat_keterangan_kerja.name,
            show: form_surat_keterangan_kerja.show,
        }

        axios.post(`${url}/outcoming-letter/surat-keterangan-kerja`, payload)
            .then(res => {
                Swal.fire({
                    icon: "success",
                    title: "Berhasil",
                    text: res.data.message,
                });
            })
            .catch(err => {
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: err.data.message,
                });
            }).finally(() => {
                HSOverlay.close(loading.value.$el)
            })
    }
}


</script>

<template>
    <!-- <Loading ref="loading"></Loading> -->
    <SubHeader :title="`SK Rotasi Kepegawaian`">
        <button type="button" class="btn btn-outline-primary inline-flex gap-2 justify-center items-center">
            SK Rotasi Kepegawaian
            <Icon class="text-lg" icon="fe:sync" />
        </button>
    </SubHeader>
    <div class="flex flex-col bg-white rounded-lg">
        <div class="px-16 py-10 min-w-full inline-block align-middle">
            <div class="flex justify-between mb-6">
                <h3 class="text-primary-400">List SK Rotasi Kepegawaian</h3>
                <button @click="open_modal_form_surat_keterangan_kerja()" class="btn btn-primary">
                    <Icon class="text-lg" icon="fluent:add-12-filled" /> Tambah SK Rotasi Kepegawaian
                </button>
            </div>
            <CustomTable :thead="thead" :url="`${url}/outcoming-letters/surat-keterangan-kerja`" v-slot="item">
                <td :class="[item.defaultClass]">{{ item.key }}</td>
                <td :class="[item.defaultClass]">{{ item.reference_number }}</td>
                <td :class="[item.defaultClass]">{{ item.employee.nip }}</td>
                <td :class="[item.defaultClass]">{{ item.employee.name }}</td>
                <td :class="[item.defaultClass]">{{ item.signer.name }}</td>
                <td :class="[item.defaultClass]">15 November 2023 09:07</td>
                <td :class="[item.defaultClass]">
                    <button class="btn btn-info">
                        <Icon class="text-lg" icon="cil:pencil" />
                    </button>
                    <button class="btn btn-danger" v-if="item.quantity < 1">
                        <Icon class="text-lg" icon="jam:trash" />
                    </button>
                </td>
            </CustomTable>
        </div>
        <Modal ref="modal_form_surat_keterangan_kerja">
            <form @submit.prevent="save_surat_keterangan_kerja" class="h-full">
                <div class="p-4 pb-0 sm:p-10 sm:pb-0 h-full flex flex-col">
                    <div class="text-center mb-6">
                        <h3 class="mb-2 text-xl font-bold text-gray-800">
                            Form Tambah SK Rotasi Kepegawaian
                        </h3>
                        <p class="text-gray-500">
                            Buat SK rotasi kepegawaian dengan mengisi form dibawah ini
                        </p>
                    </div>
                    <div class="overflow-auto max-h-[400px]">
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Pegawai</label>
                            <search-input v-model="form_surat_keterangan_kerja.employee" :url="`${url}/employees`"
                                id="employee" placeholder="Cari Pegawai ...">
                                <template v-slot="{ data }">
                                    <small>{{ data.nip }}</small>
                                    <p class="mb-0">{{ data.name }}</p>
                                    <hr class="m-0" />
                                    <p class="mb-0">{{ data.position }}</p>
                                </template>
                            </search-input>
                            <div v-if="form_surat_keterangan_kerja.employee.id.trim() != ''"
                                class="form-control bg-primary-200/20  mt-2 flex justify-between items-center gap-x-4">
                                <div class="w-full">
                                    <small>{{ form_surat_keterangan_kerja.employee.nip }}</small>
                                    <p class="mb-0">{{ form_surat_keterangan_kerja.employee.name }}</p>
                                    <hr class="m-0" />
                                    <p class="mb-0">{{ form_surat_keterangan_kerja.employee.position }}</p>
                                </div>
                                <span @click="reset_employee"
                                    class="p-3 hover:bg-red-200 rounded-full cursor-pointer transition ease-in-out duration-500">
                                    <Icon icon="jam:trash" class="text-red-600 text-2xl" />
                                </span>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Penandatangan</label>
                            <search-input v-model="form_surat_keterangan_kerja.signer" :url="`${url}/employees`" id="signer"
                                placeholder="Cari Penandatangan ...">
                                <template v-slot="{ data }">
                                    <small>{{ data.nip }}</small>
                                    <p class="mb-0">{{ data.name }}</p>
                                    <hr class="m-0" />
                                    <p class="mb-0">{{ data.position }}</p>
                                </template>
                            </search-input>
                            <div v-if="form_surat_keterangan_kerja.signer.id.trim() != ''"
                                class="form-control bg-primary-200/20  mt-2 flex justify-between items-center gap-x-4">
                                <div class="w-full">
                                    <small>{{ form_surat_keterangan_kerja.signer.nip }}</small>
                                    <p class="mb-0">{{ form_surat_keterangan_kerja.signer.name }}</p>
                                    <hr class="m-0" />
                                    <p class="mb-0">{{ form_surat_keterangan_kerja.signer.position }}</p>
                                </div>
                                <span @click="reset_signer"
                                    class="p-3 hover:bg-red-200 rounded-full cursor-pointer transition ease-in-out duration-500">
                                    <Icon icon="jam:trash" class="text-red-600 text-2xl" />
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="border-t p-4 sm:px-10 flex justify-end">
                    <button class="btn btn-outline-primary px-14 py-3 mr-6">Batal</button>
                    <button class="btn btn-primary px-14 py-3">Simpan</button>
                </div>
            </form>
        </Modal>
    </div>
</template>