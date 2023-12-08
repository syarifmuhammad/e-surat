<script setup>
import { ref, reactive } from 'vue'
import { Icon } from '@iconify/vue'
import SubHeader from '@/components/SubHeader.vue'
import CustomTable from '@/components/CustomTable.vue'
import Modal from '@/components/Modal.vue'
import Loading from '@/components/Loading.vue'
import axios from 'axios'
import { useUserStore } from '@/stores/user'

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
                <router-link v-if="userStore.user.roles === 'superadmin'" :to="{ name: 'create_employees' }" class="btn btn-primary">
                    <Icon class="text-lg" icon="fluent:add-12-filled" /> Tambah Pegawai
                </router-link>
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
                    <button class="btn btn-info">
                        <Icon class="text-lg" icon="cil:pencil" />
                    </button>
                    <button class="btn btn-danger" v-if="item.quantity < 1">
                        <Icon class="text-lg" icon="jam:trash" />
                    </button>
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
                            <option value="admin_sdm">Admin SDM</option>
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