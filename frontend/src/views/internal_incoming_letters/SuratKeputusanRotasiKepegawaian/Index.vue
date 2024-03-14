<script setup>
import { ref } from 'vue'
import { RouterLink } from 'vue-router'
import { Icon } from '@iconify/vue'
import axios from 'axios'
import CustomTable from '@/components/CustomTable.vue';
import Modal from '@/components/Modal.vue';
import UploadFile from '@/components/UploadFile.vue'
import Loading from '@/components/Loading.vue'
import UploadSignature from '@/components/UploadSignature.vue'
import { useUserStore } from '@/stores/user'

const userStore = useUserStore()
const url = import.meta.env.VITE_URL_API
const loading = ref(null)

const thead = [
    "#",
    "Nomor Surat",
    "NIP",
    "Nama Pegawai",
    "Nama Penandatangan",
    "Tanggal Surat",
    "Status",
    "",
]

const table = ref(null)
const modal_upload_signed_file = ref(null)
const modal_sign = ref(null)
const modal_update_signature = ref(null)

const verified_file = ref(null)
const letter_id = ref(null)
const letter_signature_type = ref("")
const signature = ref(null)

function download_docx(id, nama) {
    loading.value.open()
    axios.get(`${url}/incoming-letters/surat-keputusan-rotasi-kepegawaian/${id}/download/docx`, {
        responseType: 'blob',
    }).then(res => {
        const url = window.URL.createObjectURL(new Blob([res.data]))
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', `Surat Keputusan Rotasi Kepegawaian Atas Nama ${nama}.docx`)
        document.body.appendChild(link)
        link.click()
    }).catch(err => {
        // loading.value.close()
        Swal.fire({
            title: 'Gagal!',
            text: 'Surat gagal diunduh!',
            icon: 'error',
        })
    }).finally(() => {
        loading.value.close()
    })
}

function open_sweetalert_confirm_give_reference_number(id) {
    Swal.fire({
        title: 'Berikan nomor surat?',
        icon: 'warning',
        showCancelButton: true,
    }).then((result) => {
        if (result.isConfirmed) {
            give_reference_number(id)
        }
    })
}

function give_reference_number(id) {
    loading.value.open()
    axios.post(`${url}/incoming-letters/surat-keputusan-rotasi-kepegawaian/${id}/reference-number`, {
        _method: 'PUT',
    }).then(res => {
        loading.value.close()
        Swal.fire({
            title: 'Berhasil!',
            text: 'Nomor surat berhasil diberikan!',
            icon: 'success',
        })
        table.value.getData()
    }).catch(err => {
        loading.value.close()
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
    loading.value.open()
    let request = new FormData()
    request.append('signed_file', verified_file.value.files)
    request.append('_method', 'PUT')
    axios.post(`${url}/incoming-letters/surat-keputusan-rotasi-kepegawaian/${letter_id.value}/upload-signed-file`, request).then(res => {
        loading.value = false
        Swal.fire({
            title: 'Berhasil!',
            text: 'File berhasil diupload!',
            icon: 'success',
        })
        table.value.getData()
    }).catch(err => {
        loading.value.close()
        Swal.fire({
            title: 'Gagal!',
            text: 'File gagal diupload!',
            icon: 'error',
        })
    })
}

function open_modal_sign(id, signature_type) {
    if (signature_type == 'digital') {
        modal_sign.value.open()
        letter_id.value = id
        letter_signature_type.value = signature_type
    } else {
        loading.value.open()
        axios.get(`${url}/signature`, {
            responseType: 'blob',
        }).then(res => {
            if (res.data.type != 'application/json') {
                signature.value = window.URL.createObjectURL(new Blob([res.data]));
            } else {
                signature.value = null
            }
            modal_sign.value.open()
            letter_id.value = id
            letter_signature_type.value = signature_type
        }).catch(err => {
            loading.value.close()
            Swal.fire({
                title: 'Gagal!',
                text: 'File tanda tangan gagal didapatkan!',
                icon: 'error',
            })
        }).finally(() => {
            loading.value.close()
        })
    }
}

function sign() {
    if (!letter_id.value) {
        Swal.fire({
            title: 'Gagal!',
            text: 'Terjadi kesalahan!',
            icon: 'error',
        })
        return
    }
    loading.value.open()
    let payload = {
        _method: 'PUT',
    }
    axios.put(`${url}/incoming-letters/surat-keputusan-rotasi-kepegawaian/${letter_id.value}/sign`, payload).then(res => {
        loading.value.close()
        Swal.fire({
            title: 'Berhasil!',
            text: 'Surat berhasil ditandatangani!',
            icon: 'success',
        })
        table.value.getData()
    }).catch(err => {
        loading.value.close()
        Swal.fire({
            title: 'Gagal!',
            text: 'Surat gagal ditandatangani!',
            icon: 'error',
        })
    })

}

</script>

<template>
    <Loading ref="loading"></Loading>
    <div class="flex flex-col bg-white rounded-lg">
        <div class="px-8 py-5 min-w-full inline-block align-middle">
            <div class="flex justify-between mb-6">
                <h3 class="text-primary-400">List Surat Keputusan Rotasi Kepegawaian</h3>
            </div>
            <CustomTable ref="table" :thead="thead" :url="`${url}/incoming-letters/surat-keputusan-rotasi-kepegawaian`"
                v-slot="item">
                <td :class="[item.defaultClass]">{{ item.key }}</td>
                <td :class="[item.defaultClass]">
                    {{ item.reference_number }}
                </td>
                <td :class="[item.defaultClass]">{{ item.employee.nip }}</td>
                <td :class="[item.defaultClass]">{{ item.employee.name }}</td>
                <td :class="[item.defaultClass]">
                    <template v-for="(signer, key) in item.signers">
                        {{ signer.employee.name }} {{ key != item.signers.length - 1 ? ', ' : '' }}
                    </template>
                </td>
                <td :class="[item.defaultClass]">{{ item.tanggal_surat }}</td>
                <td :class="[item.defaultClass]">
                    <template v-if="item.status == 'waiting_for_reference_number'">
                        <span class="badge badge-danger text-center">Pending</span>
                        <br>
                        <small class="text-red-500 ">Catatan : Menunggu nomor surat</small>
                    </template>
                    <template v-if="item.status == 'waiting_for_approval'">
                        <span class="badge badge-warning text-center">Pending</span>
                        <br>
                        <small class="text-yellow-500 ">Catatan : {{ item.can_approved ? "Perlu persetujuan Anda" :
                            "Menunggu disetujui oleh " + item.current_approval.employee.name }}</small>
                    </template>
                    <template v-if="item.status == 'waiting_for_signed'">
                        <span class="badge badge-warning text-center">Pending</span>
                        <br>
                        <small class="text-yellow-500 ">Catatan : {{ item.can_signed ? "Perlu Tanda Tangan Anda" :
                            "Menunggu ditandatangani" }}</small>
                    </template>
                    <template v-if="item.status == 'signed'">
                        <span class="badge badge-success text-center">Sudah Ditandatangani</span>
                    </template>
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
                                <RouterLink target="_blank"
                                    :to="{ name: 'preview_surat_keputusan_rotasi_kepegawaian_internal', params: { id: item.id } }"
                                    class="text-primary-500 cursor-pointer flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm hover:bg-gray-100 focus:outline-none focus:bg-gray-100">
                                    <Icon class="text-lg" icon="gg:file-document"></Icon>
                                    Lihat Surat (.PDF)
                                </RouterLink>
                                <span @click="download_docx(item.id, item.employee.name)"
                                    class="text-primary-500 cursor-pointer flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm hover:bg-gray-100 focus:outline-none focus:bg-gray-100">
                                    <Icon class="text-lg" icon="gg:file-document"></Icon>
                                    Lihat Surat (.DOCX)
                                </span>
                                <span v-if="item.can_give_reference_number"
                                    @click="open_sweetalert_confirm_give_reference_number(item.id)"
                                    class="text-primary-500 cursor-pointer flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm hover:bg-gray-100 focus:outline-none focus:bg-gray-100">
                                    <Icon class="text-lg" icon="fluent:document-page-number-24-regular"></Icon>
                                    Berikan Nomor Surat
                                </span>
                                <RouterLink v-if="item.can_approved"
                                    :to="{ name: 'approve_surat_keputusan_rotasi_kepegawaian_internal', params: { id: item.id } }"
                                    class="text-primary-500 cursor-pointer flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm hover:bg-gray-100 focus:outline-none focus:bg-gray-100">
                                    <Icon class="text-lg" icon="fluent:signed-24-regular"></Icon>
                                    Setujui Surat
                                </RouterLink>
                                <RouterLink v-if="!item.can_upload_verified_file && item.can_signed"
                                    :to="{ name: 'tanda_tangan_surat_keputusan_rotasi_kepegawaian_internal', params: { id: item.id } }"
                                    class="text-primary-500 cursor-pointer flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm hover:bg-gray-100 focus:outline-none focus:bg-gray-100">
                                    <Icon class="text-lg" icon="fluent:signed-24-regular"></Icon>
                                    Tanda Tangani Surat
                                </RouterLink>
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
                    <label class="block text-sm font-medium p-4">File Bertanda Tangan <span
                            class="text-red-400">*</span></label>
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
    <Modal ref="modal_sign" :customClass="'sm:max-w-[700px]'">
        <form @submit.prevent="sign">
            <div class="p-4 pb-0 sm:p-10 sm:pb-0 h-full flex flex-col">
                <div class="text-center mb-6">
                    <h3 class="mb-2 text-xl font-bold text-gray-800">
                        Tanda Tangan Surat
                    </h3>
                </div>
                <template v-if="letter_signature_type == 'gambar tanda tangan'">
                    <img v-if="signature" :src="signature" class="w-1/2 mx-auto">
                    <div class="mb-4 flex flex-col items-center">
                        <p class="text-center text-yellow-500 mb-4">Catatan : Upload tanda tangan tanpa background / latar
                        </p>
                        <button type="button" @click="modal_update_signature.open()" class="btn bg-blue-500 text-white ">{{
                            signature ? "Ganti Tanda Tangan" : "Upload Tanda Tangan" }}</button>
                    </div>
                </template>
                <template v-else-if="letter_signature_type == 'digital'">
                    <p class="text-center mb-4 text-blue-500">Surat akan ditanda tangani secara digital menggunakan QRCODE !
                    </p>
                </template>
                <div class="border-t p-4 sm:px-10 flex justify-center">
                    <button type="button" @click="modal_sign.close()"
                        class="btn btn-outline-primary px-14 py-3 mr-6">Batal</button>
                    <button class="btn btn-primary px-14 py-3"
                        :disabled="!signature && letter_signature_type == 'gambar tanda tangan'">Tanda Tangan</button>
                </div>
            </div>
        </form>
    </Modal>
    <UploadSignature ref="modal_update_signature" @close="open_modal_sign(letter_id, letter_signature_type)">
    </UploadSignature>
</template>