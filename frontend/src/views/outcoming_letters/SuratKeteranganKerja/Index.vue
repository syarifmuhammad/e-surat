<script setup>
import { ref } from 'vue'
import { RouterLink } from 'vue-router'
import { Icon } from '@iconify/vue'
import SubHeader from '@/components/SubHeader.vue'
import CustomTable from '@/components/CustomTable.vue';
import axios from 'axios'

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
    axios.post(`${url}/outcoming-letters/surat-keterangan-kerja/${id}/reference-number`, {
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

function download(id) {
    loading.value = true
    axios.get(`${url}/outcoming-letters/surat-keterangan-kerja/${id}/download`, {
        responseType: 'blob',
    }).then(res => {
        loading.value = false
        const url = window.URL.createObjectURL(new Blob([res.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `Surat Keterangan Kerja ${id}.docx`);
        document.body.appendChild(link);
        link.click();
    }).catch(err => {
        loading.value = false
        Swal.fire({
            title: 'Gagal!',
            text: 'Surat gagal diunduh!',
            icon: 'error',
        })
    })
}
</script>

<template>
    <Loading ref="loading"></Loading>
    <SubHeader :title="`Surat Keterangan Kerja`">
        <button type="button" class="btn btn-outline-primary inline-flex gap-2 justify-center items-center">
            Surat Keterangan Kerja
            <Icon class="text-lg" icon="fe:sync" />
        </button>
    </SubHeader>
    <div class="flex flex-col bg-white rounded-lg">
        <div class="px-16 py-10 min-w-full inline-block align-middle">
            <div class="flex justify-between mb-6">
                <h3 class="text-primary-400">List Surat Keterangan Kerja</h3>
                <RouterLink :to="{ name: 'create_surat_keterangan_kerja' }" class="btn btn-primary">
                    <Icon class="text-lg" icon="fluent:add-12-filled" /> Tambah Surat Keterangan Kerja
                </RouterLink>
            </div>
            <CustomTable ref="table" :thead="thead" :url="`${url}/outcoming-letters/surat-keterangan-kerja`" v-slot="item">
                <td :class="[item.defaultClass]">{{ item.key }}</td>
                <td :class="[item.defaultClass]">
                    {{ item.reference_number }}
                </td>
                <td :class="[item.defaultClass]">{{ item.employee.nip }}</td>
                <td :class="[item.defaultClass]">{{ item.employee.name }}</td>
                <td :class="[item.defaultClass]">
                    <template v-if="item.status == 'waiting_for_reference_number'">
                        <span class="badge badge-warning">Pending</span>
                        <br>
                        <small class="text-yellow-500 ">Catatan : Menunggu nomor surat</small>
                    </template>
                    <template v-if="item.status == 'waiting_for_signed'">
                        <span class="badge badge-warning">Pending</span>
                        <br>
                        <small class="text-yellow-500 ">Catatan : Menunggu ditandatangani</small>
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

                        <div class="hs-dropdown-menu transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 hidden min-w-[15rem] bg-white shadow-md rounded-lg p-2 mt-2 divide-y divide-gray-200"
                            aria-labelledby="hs-dropdown-with-icons">
                            <div class="py-2 first:pt-0 last:pb-0">
                                <RouterLink :to="{ name: 'preview_surat_keterangan_kerja', params: { id: item.id } }"
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
                                <span v-if="item.can_upload_verified_file"
                                    @click="open_sweetalert_confirm_give_reference_number(item.id)"
                                    class="text-primary-500 cursor-pointer flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm hover:bg-gray-100 focus:outline-none focus:bg-gray-100">
                                    <Icon class="text-lg" icon="octicon:upload-16"></Icon>
                                    Upload Surat Bertanda Tangan
                                </span>
                                <span v-if="!item.can_upload_verified_file && item.can_signed" @click="open_sweetalert_confirm_give_reference_number(item.id)"
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
</template>