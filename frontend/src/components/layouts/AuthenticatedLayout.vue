<script setup>
import { ref, computed } from 'vue';
import { RouterLink, useRoute } from 'vue-router';
import NavLink from '../NavLink.vue';
import { Icon } from '@iconify/vue';
import { useUserStore } from '@/stores/user';
import { useAuthStore } from '@/stores/auth';
const route = useRoute()

const user = computed(() => {
    return useUserStore().user
})

const sidebar_menu_is_active = ref(false)

const sidebarMenu = ref([
    {
        title: 'Dashboard',
        name: 'dashboard',
        icon: 'material-symbols:team-dashboard-outline',
        child: [],
    },
    {
        title: 'Data Master',
        name: 'master',
        icon: 'material-symbols:data-table-outline',
        child: [
            {
                title: 'Posisi / Jabatan',
                name: 'positions',
                icon: 'solar:user-outline',
            },
            {
                title: 'Jurusan / Prodi',
                name: 'prodi',
                icon: 'ph:code',
            },
            {
                title: 'Unit Kerja',
                name: 'unit',
                icon: 'solar:user-outline',
            },
            {
                title: 'Pegawai',
                name: 'employees',
                icon: 'solar:user-outline',
            },
        ],
    },
    {
        title: 'Surat Masuk',
        name: 'incoming_letter',
        icon: 'iconoir:mail-in',
        child: [
            {
                title: 'Kategori Surat',
                name: 'category_incoming_letter',
                icon: 'ion:folder-outline',
            },
            {
                title: 'Arsip Surat Masuk',
                name: 'archive_incoming_letter',
                icon: 'iconoir:archive',
            },
        ]
    },
    {
        title: 'Surat Keluar',
        name: 'outcoming_letter',
        icon: 'iconoir:mail-out',
        child: [
            {
                title: 'Template Surat',
                name: 'letter_templates',
                icon: 'eos-icons:templates-outlined',
            },
            {
                title: 'Nomor Surat',
                name: 'reference_outcoming_letter',
                icon: 'fluent:document-page-number-24-regular',
            },
            {
                title: 'Report',
                name: 'maintenance',
                icon: 'carbon:report',
            },
            {
                title: 'Surat Keterangan Kerja',
                name: 'surat_keterangan_kerja',
                icon: 'gg:file-document',
            },
            {
                title: 'SK Rotasi Kepegawaian',
                name: 'surat_keputusan_rotasi_kepegawaian',
                icon: 'ph:user-switch-light',
            },
            {
                title: 'SK Pemberhentian Dalam Jabatan',
                name: 'surat_keputusan_pemberhentian',
                icon: 'gg:file-document',
            },
            {
                title: 'SK Pengangkatan Dalam Jabatan',
                name: 'surat_keputusan_pengangkatan',
                icon: 'gg:file-document',
            },
            {
                title: 'SK Pemberhentian Dan Pengangkatan Dalam Jabatan',
                name: 'surat_keputusan_pemberhentian_dan_pengangkatan',
                icon: 'gg:file-document',
            },
            {
                title: 'Surat Perjanjian Kerja Magang',
                name: 'surat_perjanjian_kerja_magang',
                icon: 'gg:file-document',
            },
            {
                title: 'Surat Perjanjian Kerja Dosen Luar Biasa',
                name: 'surat_perjanjian_kerja_dosen_luar_biasa',
                icon: 'gg:file-document',
            },
            {
                title: 'Surat Perjanjian Kerja Dosen Full Time',
                name: 'surat_perjanjian_kerja_dosen_full_time',
                icon: 'gg:file-document',
            },
        ]
    },

])
</script>

<template>
    <div id="container" class="min-h-[100vh]">
        <header id="header" class="bg-white overflow-hidden sticky top-0 flex items-center">
            <div class="px-10 w-full flex justify-between items-center lg:justify-end">
                <Icon icon="iconamoon:menu-burger-horizontal-bold" class="text-lg cursor-pointer lg:hidden"
                    @click="sidebar_menu_is_active = true"></Icon>
                <div class="hs-dropdown relative inline-flex">
                    <button id="hs-dropdown-custom-trigger" type="button"
                        class="hs-dropdown-toggle py-1 ps-1 pe-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-full border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none">
                        <img class="w-8 h-auto rounded-full"
                            src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=facearea&facepad=2&w=300&h=300&q=80"
                            alt="Maria">
                        <span class="text-gray-600 font-medium truncate max-w-[7.5rem]">{{ user.email }}</span>
                        <svg class="hs-dropdown-open:rotate-180 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="m6 9 6 6 6-6" />
                        </svg>
                    </button>

                    <div class="hs-dropdown-menu transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 hidden min-w-[15rem] bg-white shadow-md rounded-lg p-2 mt-2"
                        aria-labelledby="hs-dropdown-custom-trigger">
                        <div class="py-3 px-5 -m-2 bg-gray-100 rounded-t-lg">
                            <p class="text-sm text-gray-500">Signed in as</p>
                            <p class="text-sm font-medium text-gray-800">{{ user.email }}</p>
                        </div>
                        <a class="mt-4 flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100"
                            href="#">
                            <Icon class="flex-shrink-0 w-4 h-4" icon="solar:user-outline" />
                            Profil
                        </a>
                        <span @click="useAuthStore().logout()"
                            class="text-red-500 flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm hover:bg-gray-100 focus:outline-none focus:bg-gray-100 cursor-pointer">
                            <Icon class="flex-shrink-0 w-4 h-4" icon="solar:logout-3-line-duotone" />
                            Logout
                        </span>
                    </div>
                </div>
                <!-- <div class="flex items-center gap-x-4">
                    <small>Selamat datang, M. SYARIF HIDAYATULLAH</small>
                    <img class="inline-block h-[2.875rem] w-[2.875rem] rounded-full" src="https://images.unsplash.com/photo-1568602471122-7832951cc4c5?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=facearea&facepad=2&w=300&h=300&q=80" alt="Image Description">
                    
                </div> -->
            </div>
        </header>
        <aside id="sidebar" :class="{ active: sidebar_menu_is_active }"
            class="bg-white pt-7 pb-10 overflow-y-auto fixed left-0 top-0 h-[100vh] z-10 w-[300px] lg:sticky">
            <div class="px-6 flex justify-center items-center">
                <div class="flex items-center">
                    <RouterLink :to="'/'">
                        <h1 class="text-center">E-SURAT</h1>
                    </RouterLink>
                    <Icon icon="ep:close-bold" class="text-xl cursor-pointer ml-8 lg:hidden"
                        @click="sidebar_menu_is_active = false">
                    </Icon>
                </div>
            </div>

            <nav class="hs-accordion-group p-6 w-full flex flex-col flex-wrap">
                <ul class="space-y-1.5">
                    <template v-if="!useUserStore().is_empty()">
                        <NavLink v-for="menu in sidebarMenu" :key="menu.name" :title="menu.title" :name="menu.name"
                            :icon="menu.icon" :child="menu.child" />
                    </template>
                    <template v-else>
                        <div v-for="n in 4" class="w-full h-10 bg-gray-200 rounded-md animate-pulse"></div>
                    </template>
                </ul>
            </nav>
        </aside>
        <main class="main bg-slate-100 p-10 overflow-hidden">
            <slot></slot>
        </main>
        <footer id="footer" class="bg-primary-400 text-white flex justify-center items-center"></footer>
    </div>
</template>

<style scoped>
#container {
    --header-height: 75px;
    --footer-height: 45px;
    --footer-z-index: 1;
    display: grid;
    grid-template-areas: "header" "main" "footer";
    grid-template-columns: 1fr;
    grid-template-rows: var(--header-height) auto var(--footer-height);
}

#header {
    grid-area: header;
    overflow-y: auto;
}

#sidebar {
    grid-area: sidebar;
    transform: translateX(-300px);
    transition: transform .3s ease-in-out;
}

#sidebar.active {
    transform: translateX(0);
}

#main {
    grid-area: main;
}

#footer {
    grid-area: footer;
    z-index: var(--footer-z-index);
    align-self: stretch;
}

@media (min-width: 1024px) {
    #container {
        grid-template-areas: "sidebar header" "sidebar main" "sidebar footer";
        grid-template-columns: 300px auto;
        grid-template-rows: var(--header-height) auto var(--footer-height);
    }


    #sidebar {
        transform: translateX(0px);
    }
}
</style>
