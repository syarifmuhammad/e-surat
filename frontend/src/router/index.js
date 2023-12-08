import { createRouter, createWebHistory } from 'vue-router'
import Register from '@/views/Register.vue'
import AskVerification from '@/views/AskVerification.vue'
import Login from '@/views/Login.vue'
import Dashboard from '@/views/Dashboard.vue'
import Position from '@/views/positions/Index.vue'
import FormPosition from '@/views/positions/Form.vue'
import Employees from '@/views/employees/Index.vue'
import FormEmployees from '@/views/employees/Form.vue'
import Categories from '@/views/incoming_letters/Categories.vue'
import ReferenceNumberSettings from '@/views/outcoming_letters/ReferenceNumberSettings.vue'
import LetterTemplates from '@/views/outcoming_letters/LetterTemplates/Index.vue'

//surat keterangan kerja
import SuratKeteranganKerja from '@/views/outcoming_letters/SuratKeteranganKerja/Index.vue'
import PreviewSuratKeteranganKerja from '@/views/outcoming_letters/SuratKeteranganKerja/Preview.vue'
import FormSuratKeteranganKerja from '@/views/outcoming_letters/SuratKeteranganKerja/Form.vue'

//surat keputusan rotasi kepegawaian
import SuratKeputusanRotasiKepegawaian from '@/views/outcoming_letters/SuratKeputusanRotasiKepegawaian/Index.vue'
import PreviewSuratKeputusanRotasiKepegawaian from '@/views/outcoming_letters/SuratKeputusanRotasiKepegawaian/Preview.vue'
import FormSuratKeputusanRotasiKepegawaian from '@/views/outcoming_letters/SuratKeputusanRotasiKepegawaian/Form.vue'

//surat keputusan pemberhentian
import SuratKeputusanPemberhentian from '@/views/outcoming_letters/SuratKeputusanPemberhentian/Index.vue'
import PreviewSuratKeputusanPemberhentian from '@/views/outcoming_letters/SuratKeputusanPemberhentian/Preview.vue'
import FormSuratKeputusanPemberhentian from '@/views/outcoming_letters/SuratKeputusanPemberhentian/Form.vue'

//surat keputusan pengangkatan
import SuratKeputusanPengangkatan from '@/views/outcoming_letters/SuratKeputusanPengangkatan/Index.vue'
import PreviewSuratKeputusanPengangkatan from '@/views/outcoming_letters/SuratKeputusanPengangkatan/Preview.vue'
import FormSuratKeputusanPengangkatan from '@/views/outcoming_letters/SuratKeputusanPengangkatan/Form.vue'

//surat keputusan pemberhenetian dan pengangkatan
import SuratKeputusanPemberhentianDanPengangkatan from '@/views/outcoming_letters/SuratKeputusanPemberhentianDanPengangkatan/Index.vue'
import PreviewSuratKeputusanPemberhentianDanPengangkatan from '@/views/outcoming_letters/SuratKeputusanPemberhentianDanPengangkatan/Preview.vue'
import FormSuratKeputusanPemberhentianDanPengangkatan from '@/views/outcoming_letters/SuratKeputusanPemberhentianDanPengangkatan/Form.vue'

//surat perjanjian kerja magang
import SuratPerjanjianKerjaMagang from '@/views/outcoming_letters/SuratPerjanjianKerjaMagang/Index.vue'
import PreviewSuratPerjanjianKerjaMagang from '@/views/outcoming_letters/SuratPerjanjianKerjaMagang/Preview.vue'
import FormSuratPerjanjianKerjaMagang from '@/views/outcoming_letters/SuratPerjanjianKerjaMagang/Form.vue'

//surat perjanjian kerja dosen luar biasa
import SuratPerjanjianKerjaDosenLuarBiasa from '@/views/outcoming_letters/SuratPerjanjianKerjaDosenLuarBiasa/Index.vue'
import PreviewSuratPerjanjianKerjaDosenLuarBiasa from '@/views/outcoming_letters/SuratPerjanjianKerjaDosenLuarBiasa/Preview.vue'
import FormSuratPerjanjianKerjaDosenLuarBiasa from '@/views/outcoming_letters/SuratPerjanjianKerjaDosenLuarBiasa/Form.vue'

//surat perjanjian kerja dosen full time
import SuratPerjanjianKerjaDosenFullTime from '@/views/outcoming_letters/SuratPerjanjianKerjaDosenFullTime/Index.vue'
import PreviewSuratPerjanjianKerjaDosenFullTime from '@/views/outcoming_letters/SuratPerjanjianKerjaDosenFullTime/Preview.vue'
import FormSuratPerjanjianKerjaDosenFullTime from '@/views/outcoming_letters/SuratPerjanjianKerjaDosenFullTime/Form.vue'

//errors page
import Forbidden from '@/views/errors/Forbidden.vue'
import { useAuthStore } from '../stores/auth'
import { useUserStore } from '../stores/user'
import axios from 'axios'

const url = import.meta.env.VITE_URL_API

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/forbidden',
      name: 'forbidden',
      component: Forbidden,
      meta: {
        is_login_required: true,
        can_accessed: ['*'],
        layout: 'GuestLayout',
      }
    },
    {
      path: '/register',
      name: 'register',
      component: Register,
      meta: {
        is_login_required: false,
        can_accessed: ['*'],
        layout: 'GuestLayout',
      }
    },
    {
      path: '/login',
      name: 'login',
      component: Login,
      meta: {
        is_login_required: false,
        can_accessed: ['*'],
        layout: 'GuestLayout',
      }
    },
    {
      path: '/',
      name: 'dashboard',
      component: Dashboard,
      meta: {
        is_login_required: true,
        can_accessed: ['*'],
        layout: 'AuthenticatedLayout',
      },
    },
    {
      path: '/positions',
      name: 'positions',
      component: Position,
      meta: {
        is_login_required: true,
        can_accessed: ['superadmin', 'admin_sdm'],
        layout: 'AuthenticatedLayout',
      },
    },
    {
      path: '/positions/create',
      name: 'create_positions',
      component: FormPosition,
      meta: {
        is_login_required: true,
        can_accessed: ['superadmin', 'admin_sdm'],
        layout: 'AuthenticatedLayout',
      },
    },
    {
      path: '/employees',
      name: 'employees',
      component: Employees,
      meta: {
        is_login_required: true,
        can_accessed: ['superadmin', 'admin_sdm'],
        layout: 'AuthenticatedLayout',
      },
    },
    {
      path: '/employees/create',
      name: 'create_employees',
      component: FormEmployees,
      meta: {
        is_login_required: true,
        can_accessed: ['superadmin'],
        layout: 'AuthenticatedLayout',
      },
    },
    {
      path: '/surat-masuk/kategori',
      name: 'category_incoming_letter',
      component: Categories,
      meta: {
        is_login_required: true,
        can_accessed: ['*'],
        layout: 'AuthenticatedLayout',
      },
    },
    {
      path: '/surat-masuk/arsip',
      name: 'archive_incoming_letter',
      component: Employees,
      meta: {
        is_login_required: true,
        can_accessed: ['*'],
        layout: 'AuthenticatedLayout',
      },
    },
    {
      path: '/surat-keluar/templates',
      name: 'letter_templates',
      component: LetterTemplates,
      meta: {
        is_login_required: true,
        can_accessed: ['*'],
        layout: 'AuthenticatedLayout',
      },
    },
    {
      path: '/surat-keluar/templates/create',
      name: 'create_letter_templates',
      component: LetterTemplates,
      meta: {
        is_login_required: true,
        can_accessed: ['admin_sdm', 'superadmin'],
        layout: 'AuthenticatedLayout',
      },
    },
    {
      path: '/surat-keluar/nomor-surat',
      name: 'reference_outcoming_letter',
      component: ReferenceNumberSettings,
      meta: {
        is_login_required: true,
        can_accessed: ['*'],
        layout: 'AuthenticatedLayout',
      },
    },
    {
      path: '/surat-keluar/surat-keterangan-kerja',
      name: 'surat_keterangan_kerja',
      component: SuratKeteranganKerja,
      meta: {
        is_login_required: true,
        can_accessed: ['*'],
        layout: 'AuthenticatedLayout',
      },
    },
    {
      path: '/surat-keluar/surat-keterangan-kerja/:id/preview',
      name: 'preview_surat_keterangan_kerja',
      component: PreviewSuratKeteranganKerja,
      meta: {
        is_login_required: true,
        can_accessed: ['*'],
        layout: 'BlankLayout',
        parentName: 'surat_keterangan_kerja',
      },
    },
    {
      path: '/surat-keluar/surat-keterangan-kerja/create',
      name: 'create_surat_keterangan_kerja',
      component: FormSuratKeteranganKerja,
      meta: {
        is_login_required: true,
        can_accessed: ['admin_sdm', 'superadmin'],
        layout: 'AuthenticatedLayout',
        parentName: 'surat_keterangan_kerja',
      },
    },
    {
      path: '/surat-keluar/surat-keputusan-rotasi-kepegawaian',
      name: 'surat_keputusan_rotasi_kepegawaian',
      component: SuratKeputusanRotasiKepegawaian,
      meta: {
        is_login_required: true,
        can_accessed: ['*'],
        layout: 'AuthenticatedLayout',
      },
    },
    {
      path: '/surat-keluar/surat-keputusan-rotasi-kepegawaian/:id/preview',
      name: 'preview_surat_keputusan_rotasi_kepegawaian',
      component: PreviewSuratKeputusanRotasiKepegawaian,
      meta: {
        is_login_required: true,
        can_accessed: ['*'],
        layout: 'BlankLayout',
        parentName: 'surat_keputusan_rotasi_kepegawaian',
      },
    },
    {
      path: '/surat-keluar/surat-keputusan-rotasi-kepegawaian/create',
      name: 'create_surat_keputusan_rotasi_kepegawaian',
      component: FormSuratKeputusanRotasiKepegawaian,
      meta: {
        is_login_required: true,
        can_accessed: ['admin_sdm', 'superadmin'],
        layout: 'AuthenticatedLayout',
        parentName: 'surat_keputusan_rotasi_kepegawaian',
      },
    },
    {
      path: '/surat-keluar/surat-keputusan-pemberhentian',
      name: 'surat_keputusan_pemberhentian',
      component: SuratKeputusanPemberhentian,
      meta: {
        is_login_required: true,
        can_accessed: ['*'],
        layout: 'AuthenticatedLayout',
      },
    },
    {
      path: '/surat-keluar/surat-keputusan-pemberhentian/:id/preview',
      name: 'preview_surat_keputusan_pemberhentian',
      component: PreviewSuratKeputusanPemberhentian,
      meta: {
        is_login_required: true,
        can_accessed: ['*'],
        layout: 'BlankLayout',
        parentName: 'surat_keputusan_pemberhentian',
      },
    },
    {
      path: '/surat-keluar/surat-keputusan-pemberhentian/create',
      name: 'create_surat_keputusan_pemberhentian',
      component: FormSuratKeputusanPemberhentian,
      meta: {
        is_login_required: true,
        can_accessed: ['admin_sdm', 'superadmin'],
        layout: 'AuthenticatedLayout',
        parentName: 'surat_keputusan_pemberhentian',
      },
    },
    {
      path: '/surat-keluar/surat-keputusan-pengangkatan',
      name: 'surat_keputusan_pengangkatan',
      component: SuratKeputusanPengangkatan,
      meta: {
        is_login_required: true,
        can_accessed: ['*'],
        layout: 'AuthenticatedLayout',
      },
    },
    {
      path: '/surat-keluar/surat-keputusan-pengangkatan/:id/preview',
      name: 'preview_surat_keputusan_pengangkatan',
      component: PreviewSuratKeputusanPengangkatan,
      meta: {
        is_login_required: true,
        can_accessed: ['*'],
        layout: 'BlankLayout',
        parentName: 'surat_keputusan_pengangkatan',
      },
    },
    {
      path: '/surat-keluar/surat-keputusan-pengangkatan/create',
      name: 'create_surat_keputusan_pengangkatan',
      component: FormSuratKeputusanPengangkatan,
      meta: {
        is_login_required: true,
        can_accessed: ['admin_sdm', 'superadmin'],
        layout: 'AuthenticatedLayout',
        parentName: 'surat_keputusan_pengangkatan',
      },
    },
    {
      path: '/surat-keluar/surat-keputusan-pemberhentian-dan-pengangkatan',
      name: 'surat_keputusan_pemberhentian_dan_pengangkatan',
      component: SuratKeputusanPemberhentianDanPengangkatan,
      meta: {
        is_login_required: true,
        can_accessed: ['*'],
        layout: 'AuthenticatedLayout',
      },
    },
    {
      path: '/surat-keluar/surat-keputusan-pemberhentian-dan-pengangkatan/:id/preview',
      name: 'preview_surat_keputusan_pemberhentian_dan_pengangkatan',
      component: PreviewSuratKeputusanPemberhentianDanPengangkatan,
      meta: {
        is_login_required: true,
        can_accessed: ['*'],
        layout: 'BlankLayout',
        parentName: 'surat_keputusan_pemberhentian_dan_pengangkatan',
      },
    },
    {
      path: '/surat-keluar/surat-keputusan-pemberhentian-dan-pengangkatan/create',
      name: 'create_surat_keputusan_pemberhentian_dan_pengangkatan',
      component: FormSuratKeputusanPemberhentianDanPengangkatan,
      meta: {
        is_login_required: true,
        can_accessed: ['admin_sdm', 'superadmin'],
        layout: 'AuthenticatedLayout',
        parentName: 'surat_keputusan_pemberhentian_dan_pengangkatan',
      },
    },
    {
      path: '/surat-keluar/surat-perjanjian-kerja-magang',
      name: 'surat_perjanjian_kerja_magang',
      component: SuratPerjanjianKerjaMagang,
      meta: {
        is_login_required: true,
        can_accessed: ['*'],
        layout: 'AuthenticatedLayout',
      },
    },
    {
      path: '/surat-keluar/surat-perjanjian-kerja-magang/:id/preview',
      name: 'preview_surat_perjanjian_kerja_magang',
      component: PreviewSuratPerjanjianKerjaMagang,
      meta: {
        is_login_required: true,
        can_accessed: ['*'],
        layout: 'BlankLayout',
        parentName: 'surat_perjanjian_kerja_magang',
      },
    },
    {
      path: '/surat-keluar/surat-perjanjian-kerja-magang/create',
      name: 'create_surat_perjanjian_kerja_magang',
      component: FormSuratPerjanjianKerjaMagang,
      meta: {
        is_login_required: true,
        can_accessed: ['admin_sdm', 'superadmin'],
        layout: 'AuthenticatedLayout',
        parentName: 'surat_perjanjian_kerja_magang',
      },
    },
    {
      path: '/surat-keluar/surat-perjanjian-kerja-dosen-luar-biasa',
      name: 'surat_perjanjian_kerja_dosen_luar_biasa',
      component: SuratPerjanjianKerjaDosenLuarBiasa,
      meta: {
        is_login_required: true,
        can_accessed: ['*'],
        layout: 'AuthenticatedLayout',
      },
    },
    {
      path: '/surat-keluar/surat-perjanjian-kerja-dosen-luar-biasa/:id/preview',
      name: 'preview_surat_perjanjian_kerja_dosen_luar_biasa',
      component: PreviewSuratPerjanjianKerjaDosenLuarBiasa,
      meta: {
        is_login_required: true,
        can_accessed: ['*'],
        layout: 'BlankLayout',
        parentName: 'surat_perjanjian_kerja_dosen_luar_biasa',
      },
    },
    {
      path: '/surat-keluar/surat-perjanjian-kerja-dosen-luar-biasa/create',
      name: 'create_surat_perjanjian_kerja_dosen_luar_biasa',
      component: FormSuratPerjanjianKerjaDosenLuarBiasa,
      meta: {
        is_login_required: true,
        can_accessed: ['admin_sdm', 'superadmin'],
        layout: 'AuthenticatedLayout',
        parentName: 'surat_perjanjian_kerja_dosen_luar_biasa',
      },
    },
    {
      path: '/surat-keluar/surat-perjanjian-kerja-dosen-full-time',
      name: 'surat_perjanjian_kerja_dosen_full_time',
      component: SuratPerjanjianKerjaDosenFullTime,
      meta: {
        is_login_required: true,
        can_accessed: ['*'],
        layout: 'AuthenticatedLayout',
      },
    },
    {
      path: '/surat-keluar/surat-perjanjian-kerja-dosen-full-time/:id/preview',
      name: 'preview_surat_perjanjian_kerja_dosen_full_time',
      component: PreviewSuratPerjanjianKerjaDosenFullTime,
      meta: {
        is_login_required: true,
        can_accessed: ['*'],
        layout: 'BlankLayout',
        parentName: 'surat_perjanjian_kerja_dosen_full_time',
      },
    },
    {
      path: '/surat-keluar/surat-perjanjian-kerja-dosen-full-time/create',
      name: 'create_surat_perjanjian_kerja_dosen_full_time',
      component: FormSuratPerjanjianKerjaDosenFullTime,
      meta: {
        is_login_required: true,
        can_accessed: ['admin_sdm', 'superadmin'],
        layout: 'AuthenticatedLayout',
        parentName: 'surat_perjanjian_kerja_dosen_full_time',
      },
    },
  ]
})

router.beforeEach(async (to, from, next) => {
  let token = useAuthStore().token
  if (token && useUserStore().is_empty()) {
    try {
      const { data } = await axios.get(`${url}/me`)
      useUserStore().setUser(data.data)
    } catch (error) {
      token = null
    }
  }
  // const is_verified = useUserStore().user.is_verified
  const isRequiredLogin = to.matched.some(record => record.meta.is_login_required)
  if (isRequiredLogin && !token) {
    return next({ name: 'login' })
  }

  if (token && (to.name == 'login' || to.name == 'register')) {
    return next({ name: 'dashboard' })
  }

  const can_accessed = to.meta.can_accessed.some(roles => roles === '*' || roles === useUserStore().user.roles)
  if (!can_accessed) {
    return next({ name: 'forbidden' });
  }

  // if (!is_verified && to.name != 'verify_email') {
  //   return next({ name: 'verify_email' })
  // }

  next()
})

export default router
