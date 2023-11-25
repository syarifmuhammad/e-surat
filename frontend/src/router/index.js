import { createRouter, createWebHistory } from 'vue-router'
import Register from '@/views/Register.vue'
import AskVerification from '@/views/AskVerification.vue'
import Login from '@/views/Login.vue'
import Dashboard from '@/views/Dashboard.vue'
import Employees from '@/views/employees/Index.vue'
import FormEmployees from '@/views/employees/Form.vue'
import Categories from '@/views/incoming_letters/Categories.vue'
import ReferenceNumberSettings from '@/views/outcoming_letters/ReferenceNumberSettings.vue'
import SuratKeteranganKerja from '@/views/outcoming_letters/SuratKeteranganKerja/Index.vue'
import PreviewSuratKeteranganKerja from '@/views/outcoming_letters/SuratKeteranganKerja/Preview.vue'
import FormSuratKeteranganKerja from '@/views/outcoming_letters/SuratKeteranganKerja/Form.vue'
import SuratKeputusanRotasiKepegawaian from '@/views/outcoming_letters/SuratKeputusanRotasiKepegawaian.vue'

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
      path: '/employees',
      name: 'employees',
      component: Employees,
      meta: {
        is_login_required: true,
        can_accessed: ['superadmin'],
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
      path: '/surat-keluar/template',
      name: 'template_outcoming_letter',
      component: Employees,
      meta: {
        is_login_required: true,
        can_accessed: ['*'],
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
      },
    },
    {
      path: '/surat-keluar/surat-keterangan-kerja/create',
      name: 'create_surat_keterangan_kerja',
      component: FormSuratKeteranganKerja,
      meta: {
        is_login_required: true,
        can_accessed: ['admin_sdm', 'admin_sekretariat', 'superadmin'],
        layout: 'AuthenticatedLayout',
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
  ]
})

router.beforeEach(async (to, from, next) => {
  const token = useAuthStore().token
  if (token && useUserStore().is_empty()) {
    try {
      const { data } = await axios.get(`${url}/me`)
      useUserStore().setUser(data.data)
    } catch (error) {
      console.log(error)
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
    return next('forbidden');
  }

  // if (!is_verified && to.name != 'verify_email') {
  //   return next({ name: 'verify_email' })
  // }

  next()
})

export default router
