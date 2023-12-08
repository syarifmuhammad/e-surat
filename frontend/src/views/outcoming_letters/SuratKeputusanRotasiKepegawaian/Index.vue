<script setup>
import { ref } from 'vue'
import { RouterLink } from 'vue-router'
import { Icon } from '@iconify/vue'
import axios from 'axios'
import SubHeader from '@/components/SubHeader.vue'
import CustomTable from '@/components/CustomTable.vue';
import Modal from '@/components/Modal.vue';
import UploadFile from '@/components/UploadFile.vue'
import { useUserStore } from '@/stores/user'

const userStore = useUserStore()
const url = import.meta.env.VITE_URL_API

const thead = [
    "#",
    "Nomor Surat",
    "NIP",
    "Nama Pegawai",
    "Status",
    "",
]

const loading = ref(null)
const table = ref(null)
const modal_upload_signed_file = ref(null)

const verified_file = ref(null)
const letter_id = ref(null)

function open_sweetalert_confirm_give_reference_number(id) {
    Swal.fire({
        title: 'Berikan nomor surat?',
        text: "*Catatan : Surat tidak akan bisa diedit setelah diberikan nomor surat!",
        icon: 'warning',
        showCancelButton: true,
    }).then((result) => {
        if (result.isConfirmed) {
            give_reference_number(id)
        }
    })
}

function give_reference_number(id) {
    loading.value = true
    axios.post(`${url}/outcoming-letters/surat-keputusan-rotasi-kepegawaian/${id}/reference-number`, {
        _method: 'PUT',
    }).then(res => {
        loading.value = false
        Swal.fire({
            title: 'Berhasil!',
            text: 'Nomor surat berhasil diberikan!',
            icon: 'success',
        })
        table.value.getData()
    }).catch(err => {
        loading.value = false
        Swal.fire({
            title: 'Gagal!',
            text: 'Nomor surat gagal diberikan!',
            icon: 'error',
        })
    })
}

function open_modal_upload_signed_file(id) {
    modal_upload_signed_file.value.open()
    letter_id.value = id
}

function upload_signed_file() {
    if (!letter_id.value) {
        Swal.fire({
            title: 'Gagal!',
            text: 'Terjadi kesalahan!',
            icon: 'error',
        })
        return
    }
    if (!verified_file.value.files) {
        Swal.fire({
            title: 'Gagal!',
            text: 'File tidak boleh kosong!',
            icon: 'error',
        })
        return
    }
    loading.value = true
    let request = new FormData()
    request.append('signed_file', verified_file.value.files)
    request.append('_method', 'PUT')
    axios.post(`${url}/outcoming-letters/surat-keputusan-rotasi-kepegawaian/${letter_id.value}/upload-signed-file`, request).then(res => {
        loading.value = false
        Swal.fire({
            title: 'Berhasil!',
            text: 'File berhasil diupload!',
            icon: 'success',
        })
        table.value.getData()
    }).catch(err => {
        loading.value = false
        Swal.fire({
            title: 'Gagal!',
            text: 'File gagal diupload!',
            icon: 'error',
        })
    })
}
</script>

<template>
    <Loading ref="loading"></Loading>
    <!-- <SubHeader :title="`Surat Keputusan Rotasi Kepegawaian`">
        <button type="button" class="btn btn-outline-primary inline-flex gap-2 justify-center items-center">
            Surat Keputusan Rotasi Kepegawaian
            <Icon class="text-lg" icon="fe:sync" />
        </button>
    </SubHeader> -->
    <div class="flex flex-col bg-white rounded-lg">
        <div class="px-8 py-5 min-w-full inline-block align-middle">
            <div class="flex justify-between mb-6">
                <h3 class="text-primary-400">List Surat Keputusan Rotasi Kepegawaian</h3>
                <RouterLink v-if="userStore.user.roles === 'superadmin' || userStore.user.roles === 'admin_sdm'" :to="{ name: 'create_surat_keputusan_rotasi_kepegawaian' }" class="btn btn-primary">
                    <Icon class="text-lg" icon="fluent:add-12-filled" /> Tambah Data
                </RouterLink>
            </div>
            <CustomTable ref="table" :thead="thead" :url="`${url}/outcoming-letters/surat-keputusan-rotasi-kepegawaian`" v-slot="item">
                <td :class="[item.defaultClass]">{{ item.key }}</td>
                <td :class="[item.defaultClass]">
                    {{ item.reference_number }}
                </td>
                <td :class="[item.defaultClass]">{{ item.employee.nip }}</td>
                <td :class="[item.defaultClass]">{{ item.employee.name }}</td>
                <td :class="[item.defaultClass]">
                    <template v-if="item.status == 'waiting_for_reference_number'">
                        <span class="badge badge-danger text-center">Pending</span>
                        <br>
                        <small class="text-red-500 ">Catatan : Menunggu nomor surat</small>
                    </template>
                    <template v-if="item.status == 'waiting_for_signed'">
                        <span class="badge badge-warning text-center">Pending</span>
                        <br>
                        <small class="text-yellow-500 ">Catatan : Menunggu ditandatangani</small>
                    </template>
                    <template v-if="item.status == 'signed'">
                        <span class="badge badge-success text-center">Sudah Ditandatangani</span>
                        <!-- <br>
                        <small class="text-yellow-500 ">Catatan : Menunggu ditandatangani</small> -->
                    </template>
                    <!-- <span v-if="item.status == 'approved'" class="badge badge-success">Disetujui</span>
                    <span v-if="item.status == 'rejected'" class="badge badge-danger">Ditolak</span> -->
                </td>
                <td :class="[item.defaultClass]">
                    <div class="hs-dropdown relative inline-flex">
                        <button id="hs-dropdown-with-icons" type="button"
                            class="hs-dropdown-toggle py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none">
                            Aksi
                            <svg class="hs-dropdown-open:rotate-180 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </button>

                        <div class="z-10 hs-dropdown-menu transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 hidden min-w-[15rem] bg-white shadow-md rounded-lg p-2 mt-2 divide-y divide-gray-200"
                            aria-labelledby="hs-dropdown-with-icons">
                            <div class="py-2 first:pt-0 last:pb-0">
                                <RouterLink target="_blank" :to="{ name: 'preview_surat_keputusan_rotasi_kepegawaian', params: { id: item.id } }"
                                    class="text-primary-500 cursor-pointer flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm hover:bg-gray-100 focus:outline-none focus:bg-gray-100">
                                    <Icon class="text-lg" icon="gg:file-document"></Icon>
                                    Lihat Surat
                                </RouterLink>
                                <span v-if="item.can_give_reference_number"
                                    @click="open_sweetalert_confirm_give_reference_number(item.id)"
                                    class="text-primary-500 cursor-pointer flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm hover:bg-gray-100 focus:outline-none focus:bg-gray-100">
                                    <Icon class="text-lg" icon="fluent:document-page-number-24-regular"></Icon>
                                    Berikan Nomor Surat
                                </span>
                                <span v-if="item.can_upload_verified_file" @click="open_modal_upload_signed_file(item.id)"
                                    class="text-primary-500 cursor-pointer flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm hover:bg-gray-100 focus:outline-none focus:bg-gray-100">
                                    <Icon class="text-lg" icon="octicon:upload-16"></Icon>
                                    Upload Surat Bertanda Tangan
                                </span>
                                <span v-if="!item.can_upload_verified_file && item.can_signed"
                                    @click="open_sweetalert_confirm_give_reference_number(item.id)"
                                    class="text-primary-500 cursor-pointer flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm hover:bg-gray-100 focus:outline-none focus:bg-gray-100">
                                    <Icon class="text-lg" icon="fluent:signed-24-regular"></Icon>
                                    Tanda Tangani Surat
                                </span>
                                <span v-if="item.can_edit" @click="open_sweetalert_confirm_give_reference_number(item.id)"
                                    class="text-primary-500 cursor-pointer flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm hover:bg-gray-100 focus:outline-none focus:bg-gray-100">
                                    <Icon class="text-lg" icon="cil:pencil"></Icon>
                                    Edit Surat
                                </span>
                            </div>
                        </div>
                    </div>
                </td>
            </CustomTable>
        </div>
    </div>
    <Modal ref="modal_upload_signed_file" :customClass="'sm:max-w-[700px]'">
        <form @submit.prevent="upload_signed_file">
            <div class="p-4 pb-0 sm:p-10 sm:pb-0 h-full flex flex-col">
                <div class="text-center mb-6">
                    <h3 class="mb-2 text-xl font-bold text-gray-800">
                        Form Upload Surat Yang Bertanda Tangan
                    </h3>
                </div>
                <div class="mb-4 rounded-md overflow-hidden" style="box-shadow: 0 0.25rem 1rem #a1acb873;">
                    <label class="block text-sm font-medium p-4">File Bertanda Tangan <span class="text-red-400">*</span></label>
                    <UploadFile ref="verified_file"></UploadFile>
                </div>
            </div>
            <div class="border-t p-4 sm:px-10 flex justify-end">
                <button type="button" @click="modal_upload_signed_file.close()"
                    class="btn btn-outline-primary px-14 py-3 mr-6">Batal</button>
                <button class="btn btn-primary px-14 py-3">Simpan</button>
            </div>
        </form>
    </Modal>
</template>