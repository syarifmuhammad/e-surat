<script setup>
import { ref } from 'vue'
import { RouterLink } from 'vue-router'
import { Icon } from '@iconify/vue'
import axios from 'axios'
import CustomTable from '@/components/CustomTable.vue';
import Loading from '@/components/Loading.vue'
import { useUserStore } from '@/stores/user';

const userStore = useUserStore()
const url = import.meta.env.VITE_URL_API
const loading = ref(null)

const thead = [
    "#",
    "Nomor Surat",
    "NIP",
    "Nama Pegawai",
    "Diberhentikan Dalam Jabatan",
    "Nama Penandatangan",
    "Tanggal Surat",
    "Status",
    "",
]

const table = ref(null)


function download_docx(id, nama) {
    loading.value.open()
    axios.get(`${url}/outcoming-letters/surat-keputusan-pemberhentian/${id}/download/docx`, {
        responseType: 'blob',
    }).then(res => {
        const url = window.URL.createObjectURL(new Blob([res.data]))
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', `Surat Keputusan Pemberhentian Atas Nama ${nama}.docx`)
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

function delete_letter(id) {
    Swal.fire({
        title: 'Apakah anda yakin?',
        text: "Surat akan dihapus secara permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            loading.value.open()
            axios.delete(`${url}/outcoming-letters/surat-keputusan-pemberhentian/${id}`)
                .then(res => {
                    loading.value.close()
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Surat berhasil dihapus!',
                    })
                    table.value.getData()
                })
                .catch(err => {
                    loading.value.close()
                    if (err.response.status == 422) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: err.response.data.message,
                        })
                    } else {
                        console.log(err.response)
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Terjadi kesalahan, silahkan coba lagi!',
                        })
                    }
                })
        }
    })
}
</script>

<template>
    <Loading ref="loading"></Loading>
    <div class="flex flex-col bg-white rounded-lg">
        <div class="px-8 py-5 min-w-full inline-block align-middle">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-primary-400">List Surat Keputusan Pemberhentian Dalam Jabatan</h3>
                <RouterLink v-if="userStore.user.roles === 'superadmin' || userStore.user.roles === 'admin_unit'"
                    :to="{ name: 'create_surat_keputusan_pemberhentian' }" class="btn btn-primary">
                    <Icon class="text-lg" icon="fluent:add-12-filled" /> Tambah Surat
                </RouterLink>
            </div>
            <CustomTable ref="table" :thead="thead" :url="`${url}/outcoming-letters/surat-keputusan-pemberhentian`"
                v-slot="item">
                <td :class="[item.defaultClass]">{{ item.key }}</td>
                <td :class="[item.defaultClass]">
                    {{ item.reference_number }}
                </td>
                <td :class="[item.defaultClass]">{{ item.employee.nip }}</td>
                <td :class="[item.defaultClass]">{{ item.employee.name }}</td>
                <td :class="[item.defaultClass]">{{ item.pemberhentian_dalam_jabatan }}</td>
                <td :class="[item.defaultClass]">
                    <template v-for="(signer, key) in item.signers">
                        {{ signer.employee.name }} {{ key != item.signers.length - 1 ? ', ' : '' }}
                    </template>
                </td>
                <td :class="[item.defaultClass]">{{ item.tanggal_surat }}</td>
                <!-- <td :class="[item.defaultClass]">{{ item.signature_type.toString().toUpperCase() }}</td> -->
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
                                    :to="{ name: 'preview_surat_keputusan_pemberhentian', params: { id: item.id } }"
                                    class="text-primary-500 cursor-pointer flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm hover:bg-gray-100 focus:outline-none focus:bg-gray-100">
                                    <Icon class="text-lg" icon="gg:file-document"></Icon>
                                    Lihat Surat (.PDF)
                                </RouterLink>
                                <span @click="download_docx(item.id, item.employee.name)"
                                    class="text-primary-500 cursor-pointer flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm hover:bg-gray-100 focus:outline-none focus:bg-gray-100">
                                    <Icon class="text-lg" icon="gg:file-document"></Icon>
                                    Lihat Surat (.DOCX)
                                </span>
                                <RouterLink v-if="item.can_edit"
                                    :to="{ name: 'update_surat_keputusan_pemberhentian', params: { id: item.id } }"
                                    class="text-primary-500 cursor-pointer flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm hover:bg-gray-100 focus:outline-none focus:bg-gray-100">
                                    <Icon class="text-lg" icon="cil:pencil"></Icon>
                                    Edit Surat
                                </RouterLink>
                                <span v-if="item.can_edit" @click="delete_letter(item.id)"
                                    class="text-primary-500 cursor-pointer flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm hover:bg-gray-100 focus:outline-none focus:bg-gray-100">
                                    <Icon class="text-lg" icon="cil:trash"></Icon>
                                    Hapus Surat
                                </span>
                            </div>
                        </div>
                    </div>
                </td>
            </CustomTable>
        </div>
    </div>
</template>