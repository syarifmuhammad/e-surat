<script setup>
import { ref, reactive } from 'vue'
import { Icon } from '@iconify/vue'
import SubHeader from '@/components/SubHeader.vue'
import CustomTable from '@/components/CustomTable.vue'
import Modal from '@/components/Modal.vue'
import Loading from '@/components/Loading.vue'
import axios from 'axios'
import { useUserStore } from '@/stores/user'
import { RouterLink, useRouter } from 'vue-router'

const router = useRouter()
const userStore = useUserStore()
const url = import.meta.env.VITE_URL_API

const thead = [
    "#",
    "NIP",
    "Nama",
    "Email",
    "Role",
    "Sudah punya akun?",
    "Aksi",
]

const loading = ref(null)
const table = ref(null)
const edit_roles = ref(null)

const form_edit_roles = reactive({
    id: '',
    roles: ''
})

function set_form_edit_roles(id, roles) {
    form_edit_roles.id = id
    form_edit_roles.roles = roles
}

function save_form_edit_roles() {
    loading.value.open()
    axios.put(`${url}/employees/${form_edit_roles.id}/roles`, {
        roles: form_edit_roles.roles
    }).then(res => {
        if (res.status == 200) {
            Swal.fire({
                icon: "success",
                title: "Berhasil",
                text: res.data.message,
            });
            edit_roles.value.close()
            table.value.getData()
        }
    }).catch(e => {
        if (e.response.status == 422) {
            Swal.fire({
                icon: "error",
                title: "Gagal",
                text: e.response.data.message,
            });
        } else {
            console.log(e)
        }
    }).finally(() => {
        loading.value.close()
    })
}

function delete_employee(id) {
    Swal.fire({
        title: 'Apakah anda yakin?',
        text: "Data pegawai akan dihapus secara permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            loading.value.open()
            axios.delete(`${url}/employees/${id}`).then(res => {
                if (res.status == 200) {
                    Swal.fire({
                        icon: "success",
                        title: "Berhasil",
                        text: res.data.message,
                    });
                    table.value.getData()
                }
            }).catch(e => {
                if (e.response.status == 422) {
                    Swal.fire({
                        icon: "error",
                        title: "Gagal",
                        text: e.response.data.message,
                    });
                } else {
                    console.log(e)
                    Swal.fire({
                        icon: "error",
                        title: "Gagal",
                        text: "Terjadi kesalahan, karena pegawai sudah memiliki data terkait",
                    })
                }
            }).finally(() => {
                loading.value.close()
            })
        }
    });
}

function check_routes(name, roles) {
    if (!router.hasRoute(name)) {
        return false
    }
    const theroute = router.resolve({ name: name })
    const can_accessed = theroute.meta.can_accessed.some(role => role === '*' || role === roles)
    if (!can_accessed) {
        return false
    }
    return true
}

</script>

<template>
    <Loading ref="loading" />
    <!-- <SubHeader :title="`Data Pegawai`">
        <button type="button" class="btn btn-outline-primary inline-flex gap-2 justify-center items-center">
            Pegawai
            <Icon class="text-lg" icon="fe:sync" />
        </button>
    </SubHeader> -->
    <div class="flex flex-col bg-white rounded-lg">
        <div class="px-8 py-5 min-w-full inline-block align-middle">
            <div class="flex justify-between mb-6">
                <h3 class="text-primary-400">List Data Pegawai</h3>
                <RouterLink v-if="check_routes('create_employees', userStore.user.roles)" :to="{ name: 'create_employees' }"
                    class="btn btn-primary">
                    <Icon class="text-lg" icon="fluent:add-12-filled" /> Tambah Pegawai
                </RouterLink>
            </div>
            <CustomTable ref="table" :thead="thead" :url="`${url}/employees`" v-slot="item">
                <td :class="[item.defaultClass]" class="w-2">{{ item.key }}</td>
                <td :class="[item.defaultClass]">{{ item.nip }}</td>
                <td :class="[item.defaultClass]">{{ item.name }}</td>
                <td :class="[item.defaultClass]">{{ item.email }}</td>
                <td :class="[item.defaultClass]">
                    <span class="badge-primary cursor-pointer"
                        @click="set_form_edit_roles(item.id, item.roles), edit_roles.open()">
                        {{ item.roles }}
                        <Icon icon="cil:pencil" />
                    </span>
                </td>
                <td :class="[item.defaultClass]">
                    <span v-if="item.is_registered" class="badge-success">Sudah</span>
                    <span v-else class="badge-danger">Belum</span>
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
                                <RouterLink :to="{ name: 'update_employees', params: { id: item.id } }"
                                    class="text-primary-500 cursor-pointer flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm hover:bg-gray-100 focus:outline-none focus:bg-gray-100">
                                    <Icon class="text-lg" icon="cil:pencil"></Icon>
                                    Edit
                                </RouterLink>
                                <span @click="delete_employee(item.id)"
                                    class="text-primary-500 cursor-pointer flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm hover:bg-gray-100 focus:outline-none focus:bg-gray-100">
                                    <Icon class="text-lg" icon="cil:trash"></Icon>
                                    Hapus
                                </span>
                            </div>
                        </div>
                    </div>
                </td>
            </CustomTable>
        </div>
    </div>
    <Modal ref="edit_roles">
        <form @submit.prevent="save_form_edit_roles">
            <div class="p-4 pb-0 sm:p-10 sm:pb-0 h-full flex flex-col">
                <div class="text-center mb-6">
                    <h3 class="mb-2 text-xl font-bold text-gray-800">
                        Form Edit Roles Pegawai
                    </h3>
                </div>
                <div class="overflow-auto max-h-[400px]">
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Roles <span class="text-red-400">*</span></label>
                        <select v-model="form_edit_roles.roles" class="form-control" required>
                            <option value="pegawai">Pegawai</option>
                            <option value="admin_unit">Admin Unit</option>
                            <option value="admin_sekretariat">Admin Sekretariat</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="border-t p-4 sm:px-10 flex justify-end">
                <button type="button" @click="set_form_edit_roles('', ''), edit_roles.close()"
                    class="btn btn-outline-primary px-14 py-3 mr-6">Batal</button>
                <button class="btn btn-primary px-14 py-3">Simpan</button>
            </div>
        </form>
    </Modal>
</template>

<style scoped></style>