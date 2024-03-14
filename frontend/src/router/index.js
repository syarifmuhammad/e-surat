import { createRouter, createWebHistory } from 'vue-router'
const Verify = () => import('@/views/Verify.vue')
const File = () => import('@/views/File.vue')
const Register = () => import('@/views/Register.vue')
const Login = () => import('@/views/Login.vue')
const Profile = () => import('@/views/Profile.vue')
const Dashboard = () => import('@/views/Dashboard.vue')
const Position = () => import('@/views/positions/Index.vue')
const FormPosition = () => import('@/views/positions/Form.vue')
const Prodi = () => import('@/views/prodi/Index.vue')
const FormProdi = () => import('@/views/prodi/Form.vue')
const Unit = () => import('@/views/unit/Index.vue')
const FormUnit = () => import('@/views/unit/Form.vue')
const Employees = () => import('@/views/employees/Index.vue')
const FormEmployees = () => import('@/views/employees/Form.vue')
const ReferenceNumberSettings = () => import('@/views/ReferenceNumberSettings.vue')
const Categories = () => import('@/views/incoming_letters/Categories.vue')
const Archive = () => import('@/views/incoming_letters/Archive.vue')
const PreviewIncomingLetter = () => import('@/views/incoming_letters/Preview.vue')

import Outcoming from './outcoming'
import IncomingInternal from './incoming-internal'

//errors page
import Forbidden from '@/views/errors/Forbidden.vue'
import Maintenance from '@/views/errors/Maintenance.vue'
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
      path: '/maintenance',
      name: 'maintenance',
      component: Maintenance,
      meta: {
        is_login_required: true,
        can_accessed: ['*'],
        layout: 'GuestLayout',
      }
    },
    {
      path: '/verify/:token',
      name: 'Verify',
      component: Verify,
      meta: {
        is_login_required: false,
        can_accessed: ['*'],
        layout: 'GuestLayout',
      }
    },
    {
      path: '/file/:token',
      name: 'File',
      component: File,
      meta: {
        is_login_required: false,
        can_accessed: ['*'],
        layout: 'BlankLayout',
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
      path: '/profile',
      name: 'profile',
      component: Profile,
      meta: {
        is_login_required: true,
        can_accessed: ['*'],
        layout: 'AuthenticatedLayout',
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
        can_accessed: ['superadmin', 'admin_unit'],
        layout: 'AuthenticatedLayout',
      },
    },
    {
      path: '/positions/create',
      name: 'create_positions',
      component: FormPosition,
      meta: {
        is_login_required: true,
        can_accessed: ['superadmin', 'admin_unit'],
        layout: 'AuthenticatedLayout',
        parentName: 'positions',
      },
    },
    {
      path: '/positions/:id',
      name: 'update_positions',
      component: FormPosition,
      meta: {
        is_login_required: true,
        can_accessed: ['superadmin', 'admin_unit'],
        layout: 'AuthenticatedLayout',
        parentName: 'positions',
      },
    },
    {
      path: '/prodi',
      name: 'prodi',
      component: Prodi,
      meta: {
        is_login_required: true,
        can_accessed: ['superadmin', 'admin_unit'],
        layout: 'AuthenticatedLayout',
      },
    },
    {
      path: '/prodi/create',
      name: 'create_prodi',
      component: FormProdi,
      meta: {
        is_login_required: true,
        can_accessed: ['superadmin', 'admin_unit'],
        layout: 'AuthenticatedLayout',
        parentName: 'prodi',
      },
    },
    {
      path: '/prodi/:id',
      name: 'update_prodi',
      component: FormProdi,
      meta: {
        is_login_required: true,
        can_accessed: ['superadmin', 'admin_unit'],
        layout: 'AuthenticatedLayout',
        parentName: 'prodi',
      },
    },
    {
      path: '/unit',
      name: 'unit',
      component: Unit,
      meta: {
        is_login_required: true,
        can_accessed: ['superadmin', 'admin_unit'],
        layout: 'AuthenticatedLayout',
      },
    },
    {
      path: '/unit/create',
      name: 'create_unit',
      component: FormUnit,
      meta: {
        is_login_required: true,
        can_accessed: ['superadmin', 'admin_unit'],
        layout: 'AuthenticatedLayout',
        parentName: 'unit',
      },
    },
    {
      path: '/unit/:id',
      name: 'update_unit',
      component: FormUnit,
      meta: {
        is_login_required: true,
        can_accessed: ['superadmin', 'admin_unit'],
        layout: 'AuthenticatedLayout',
        parentName: 'unit',
      },
    },
    {
      path: '/employees',
      name: 'employees',
      component: Employees,
      meta: {
        is_login_required: true,
        can_accessed: ['superadmin', 'admin_unit'],
        layout: 'AuthenticatedLayout',
      },
    },
    {
      path: '/employees/create',
      name: 'create_employees',
      component: FormEmployees,
      meta: {
        is_login_required: true,
        can_accessed: ['superadmin', 'admin_unit'],
        layout: 'AuthenticatedLayout',
        parentName: 'employees',
      },
    },
    {
      path: '/employees/:id',
      name: 'update_employees',
      component: FormEmployees,
      meta: {
        is_login_required: true,
        can_accessed: ['superadmin', 'admin_unit'],
        layout: 'AuthenticatedLayout',
        parentName: 'employees',
      },
    },
    {
      path: '/nomor-surat',
      name: 'reference_outcoming_letter',
      component: ReferenceNumberSettings,
      meta: {
          is_login_required: true,
          can_accessed: ['superadmin', 'admin_sekretariat'],
          layout: 'AuthenticatedLayout',
      },
  },
    {
      path: '/surat-masuk/kategori',
      name: 'category_incoming_letter',
      component: Categories,
      meta: {
        is_login_required: true,
        can_accessed: ['admin_unit', 'admin_sekretariat'],
        layout: 'AuthenticatedLayout',
      },
    },
    {
      path: '/surat-masuk/arsip',
      name: 'archive_incoming_letter',
      component: Archive,
      meta: {
        is_login_required: true,
        can_accessed: ['admin_unit', 'admin_sekretariat'],
        layout: 'AuthenticatedLayout',
      },
    },
    {
      path: '/surat-masuk/arsip/:id/file',
      name: 'file_archive_incoming_letter',
      component: PreviewIncomingLetter,
      meta: {
        is_login_required: true,
        can_accessed: ['*'],
        layout: 'BlankLayout',
      },
    },
    ...Outcoming,
    ...IncomingInternal,
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
