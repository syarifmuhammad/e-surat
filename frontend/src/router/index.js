import { createRouter, createWebHistory } from 'vue-router'
const Register = () => import('@/views/Register.vue')
const AskVerification = () => import('@/views/AskVerification.vue')
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
const Categories = () => import('@/views/incoming_letters/Categories.vue')
const Archive = () => import('@/views/incoming_letters/Archive.vue')
const PreviewIncomingLetter = () => import('@/views/incoming_letters/Preview.vue')
const ReferenceNumberSettings = () => import('@/views/outcoming_letters/ReferenceNumberSettings.vue')
const LetterTemplates = () => import('@/views/outcoming_letters/LetterTemplates/Index.vue')
const FormLetterTemplates = () => import('@/views/outcoming_letters/LetterTemplates/Form.vue')
const Report = () => import('@/views/outcoming_letters/Report.vue')

//surat keterangan kerja
const SuratKeteranganKerja = () => import('@/views/outcoming_letters/SuratKeteranganKerja/Index.vue')
const PreviewSuratKeteranganKerja = () => import('@/views/outcoming_letters/SuratKeteranganKerja/Preview.vue')
const FormSuratKeteranganKerja = () => import('@/views/outcoming_letters/SuratKeteranganKerja/Form.vue')
// import SuratKeteranganKerja from '@/views/outcoming_letters/SuratKeteranganKerja/Index.vue'
// import PreviewSuratKeteranganKerja from '@/views/outcoming_letters/SuratKeteranganKerja/Preview.vue'
// import FormSuratKeteranganKerja from '@/views/outcoming_letters/SuratKeteranganKerja/Form.vue'

//surat keputusan rotasi kepegawaian
const SuratKeputusanRotasiKepegawaian = () => import('@/views/outcoming_letters/SuratKeputusanRotasiKepegawaian/Index.vue')
const PreviewSuratKeputusanRotasiKepegawaian = () => import('@/views/outcoming_letters/SuratKeputusanRotasiKepegawaian/Preview.vue')
const FormSuratKeputusanRotasiKepegawaian = () => import('@/views/outcoming_letters/SuratKeputusanRotasiKepegawaian/Form.vue')
// import SuratKeputusanRotasiKepegawaian from '@/views/outcoming_letters/SuratKeputusanRotasiKepegawaian/Index.vue'
// import PreviewSuratKeputusanRotasiKepegawaian from '@/views/outcoming_letters/SuratKeputusanRotasiKepegawaian/Preview.vue'
// import FormSuratKeputusanRotasiKepegawaian from '@/views/outcoming_letters/SuratKeputusanRotasiKepegawaian/Form.vue'

//surat keputusan pemberhentian
const SuratKeputusanPemberhentian = () => import('@/views/outcoming_letters/SuratKeputusanPemberhentian/Index.vue')
const PreviewSuratKeputusanPemberhentian = () => import('@/views/outcoming_letters/SuratKeputusanPemberhentian/Preview.vue')
const FormSuratKeputusanPemberhentian = () => import('@/views/outcoming_letters/SuratKeputusanPemberhentian/Form.vue')
// import SuratKeputusanPemberhentian from '@/views/outcoming_letters/SuratKeputusanPemberhentian/Index.vue'
// import PreviewSuratKeputusanPemberhentian from '@/views/outcoming_letters/SuratKeputusanPemberhentian/Preview.vue'
// import FormSuratKeputusanPemberhentian from '@/views/outcoming_letters/SuratKeputusanPemberhentian/Form.vue'

//surat keputusan pengangkatan
const SuratKeputusanPengangkatan = () => import('@/views/outcoming_letters/SuratKeputusanPengangkatan/Index.vue')
const PreviewSuratKeputusanPengangkatan = () => import('@/views/outcoming_letters/SuratKeputusanPengangkatan/Preview.vue')
const FormSuratKeputusanPengangkatan = () => import('@/views/outcoming_letters/SuratKeputusanPengangkatan/Form.vue')
// import SuratKeputusanPengangkatan from '@/views/outcoming_letters/SuratKeputusanPengangkatan/Index.vue'
// import PreviewSuratKeputusanPengangkatan from '@/views/outcoming_letters/SuratKeputusanPengangkatan/Preview.vue'
// import FormSuratKeputusanPengangkatan from '@/views/outcoming_letters/SuratKeputusanPengangkatan/Form.vue'

//surat keputusan pemberhenetian dan pengangkatan
const SuratKeputusanPemberhentianDanPengangkatan = () => import('@/views/outcoming_letters/SuratKeputusanPemberhentianDanPengangkatan/Index.vue')
const PreviewSuratKeputusanPemberhentianDanPengangkatan = () => import('@/views/outcoming_letters/SuratKeputusanPemberhentianDanPengangkatan/Preview.vue')
const FormSuratKeputusanPemberhentianDanPengangkatan = () => import('@/views/outcoming_letters/SuratKeputusanPemberhentianDanPengangkatan/Form.vue')
// import SuratKeputusanPemberhentianDanPengangkatan from '@/views/outcoming_letters/SuratKeputusanPemberhentianDanPengangkatan/Index.vue'
// import PreviewSuratKeputusanPemberhentianDanPengangkatan from '@/views/outcoming_letters/SuratKeputusanPemberhentianDanPengangkatan/Preview.vue'
// import FormSuratKeputusanPemberhentianDanPengangkatan from '@/views/outcoming_letters/SuratKeputusanPemberhentianDanPengangkatan/Form.vue'

//surat perjanjian kerja magang
const SuratPerjanjianKerjaMagang = () => import('@/views/outcoming_letters/SuratPerjanjianKerjaMagang/Index.vue')
const PreviewSuratPerjanjianKerjaMagang = () => import('@/views/outcoming_letters/SuratPerjanjianKerjaMagang/Preview.vue')
const FormSuratPerjanjianKerjaMagang = () => import('@/views/outcoming_letters/SuratPerjanjianKerjaMagang/Form.vue')
// import SuratPerjanjianKerjaMagang from '@/views/outcoming_letters/SuratPerjanjianKerjaMagang/Index.vue'
// import PreviewSuratPerjanjianKerjaMagang from '@/views/outcoming_letters/SuratPerjanjianKerjaMagang/Preview.vue'
// import FormSuratPerjanjianKerjaMagang from '@/views/outcoming_letters/SuratPerjanjianKerjaMagang/Form.vue'

//surat perjanjian kerja dosen luar biasa
const SuratPerjanjianKerjaDosenLuarBiasa = () => import('@/views/outcoming_letters/SuratPerjanjianKerjaDosenLuarBiasa/Index.vue')
const PreviewSuratPerjanjianKerjaDosenLuarBiasa = () => import('@/views/outcoming_letters/SuratPerjanjianKerjaDosenLuarBiasa/Preview.vue')
const FormSuratPerjanjianKerjaDosenLuarBiasa = () => import('@/views/outcoming_letters/SuratPerjanjianKerjaDosenLuarBiasa/Form.vue')
// import SuratPerjanjianKerjaDosenLuarBiasa from '@/views/outcoming_letters/SuratPerjanjianKerjaDosenLuarBiasa/Index.vue'
// import PreviewSuratPerjanjianKerjaDosenLuarBiasa from '@/views/outcoming_letters/SuratPerjanjianKerjaDosenLuarBiasa/Preview.vue'
// import FormSuratPerjanjianKerjaDosenLuarBiasa from '@/views/outcoming_letters/SuratPerjanjianKerjaDosenLuarBiasa/Form.vue'

//surat perjanjian kerja dosen full time
const SuratPerjanjianKerjaDosenFullTime = () => import('@/views/outcoming_letters/SuratPerjanjianKerjaDosenFullTime/Index.vue')
const PreviewSuratPerjanjianKerjaDosenFullTime = () => import('@/views/outcoming_letters/SuratPerjanjianKerjaDosenFullTime/Preview.vue')
const FormSuratPerjanjianKerjaDosenFullTime = () => import('@/views/outcoming_letters/SuratPerjanjianKerjaDosenFullTime/Form.vue')
// import SuratPerjanjianKerjaDosenFullTime from '@/views/outcoming_letters/SuratPerjanjianKerjaDosenFullTime/Index.vue'
// import PreviewSuratPerjanjianKerjaDosenFullTime from '@/views/outcoming_letters/SuratPerjanjianKerjaDosenFullTime/Preview.vue'
// import FormSuratPerjanjianKerjaDosenFullTime from '@/views/outcoming_letters/SuratPerjanjianKerjaDosenFullTime/Form.vue'

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
    {
      path: '/surat-keluar/templates',
      name: 'letter_templates',
      component: LetterTemplates,
      meta: {
        is_login_required: true,
        can_accessed: ['superadmin', 'admin_unit'],
        layout: 'AuthenticatedLayout',
      },
    },
    {
      path: '/surat-keluar/templates/create',
      name: 'create_letter_templates',
      component: FormLetterTemplates,
      meta: {
        is_login_required: true,
        can_accessed: ['admin_unit', 'superadmin'],
        layout: 'AuthenticatedLayout',
        parentName: 'letter_templates',
      },
    },
    {
      path: '/surat-keluar/templates/:id',
      name: 'update_letter_templates',
      component: FormLetterTemplates,
      meta: {
        is_login_required: true,
        can_accessed: ['admin_unit', 'superadmin'],
        layout: 'AuthenticatedLayout',
        parentName: 'letter_templates',
      },
    },
    {
      path: '/surat-keluar/nomor-surat',
      name: 'reference_outcoming_letter',
      component: ReferenceNumberSettings,
      meta: {
        is_login_required: true,
        can_accessed: ['superadmin', 'admin_sekretariat'],
        layout: 'AuthenticatedLayout',
      },
    },
    {
      path: '/surat-keluar/report',
      name: 'report_outcoming_letter',
      component: Report,
      meta: {
        is_login_required: true,
        can_accessed: ['superadmin', 'admin_sekretariat', 'admin_unit'],
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
        can_accessed: ['admin_unit', 'superadmin'],
        layout: 'AuthenticatedLayout',
        parentName: 'surat_keterangan_kerja',
      },
    },
    {
      path: '/surat-keluar/surat-keterangan-kerja/:id',
      name: 'update_surat_keterangan_kerja',
      component: FormSuratKeteranganKerja,
      meta: {
        is_login_required: true,
        can_accessed: ['admin_unit', 'superadmin'],
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
        can_accessed: ['admin_unit', 'superadmin'],
        layout: 'AuthenticatedLayout',
        parentName: 'surat_keputusan_rotasi_kepegawaian',
      },
    },
    {
      path: '/surat-keluar/surat-keputusan-rotasi-kepegawaian/:id',
      name: 'update_surat_keputusan_rotasi_kepegawaian',
      component: FormSuratKeputusanRotasiKepegawaian,
      meta: {
        is_login_required: true,
        can_accessed: ['admin_unit', 'superadmin'],
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
        can_accessed: ['admin_unit', 'superadmin'],
        layout: 'AuthenticatedLayout',
        parentName: 'surat_keputusan_pemberhentian',
      },
    },
    {
      path: '/surat-keluar/surat-keputusan-pemberhentian/:id',
      name: 'update_surat_keputusan_pemberhentian',
      component: FormSuratKeputusanPemberhentian,
      meta: {
        is_login_required: true,
        can_accessed: ['admin_unit', 'superadmin'],
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
        can_accessed: ['admin_unit', 'superadmin'],
        layout: 'AuthenticatedLayout',
        parentName: 'surat_keputusan_pengangkatan',
      },
    },
    {
      path: '/surat-keluar/surat-keputusan-pengangkatan/:id',
      name: 'update_surat_keputusan_pengangkatan',
      component: FormSuratKeputusanPengangkatan,
      meta: {
        is_login_required: true,
        can_accessed: ['admin_unit', 'superadmin'],
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
        can_accessed: ['admin_unit', 'superadmin'],
        layout: 'AuthenticatedLayout',
        parentName: 'surat_keputusan_pemberhentian_dan_pengangkatan',
      },
    },
    {
      path: '/surat-keluar/surat-keputusan-pemberhentian-dan-pengangkatan/:id',
      name: 'update_surat_keputusan_pemberhentian_dan_pengangkatan',
      component: FormSuratKeputusanPemberhentianDanPengangkatan,
      meta: {
        is_login_required: true,
        can_accessed: ['admin_unit', 'superadmin'],
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
        can_accessed: ['admin_unit', 'superadmin'],
        layout: 'AuthenticatedLayout',
        parentName: 'surat_perjanjian_kerja_magang',
      },
    },
    {
      path: '/surat-keluar/surat-perjanjian-kerja-magang/:id',
      name: 'update_surat_perjanjian_kerja_magang',
      component: FormSuratPerjanjianKerjaMagang,
      meta: {
        is_login_required: true,
        can_accessed: ['admin_unit', 'superadmin'],
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
        can_accessed: ['admin_unit', 'superadmin'],
        layout: 'AuthenticatedLayout',
        parentName: 'surat_perjanjian_kerja_dosen_luar_biasa',
      },
    },
    {
      path: '/surat-keluar/surat-perjanjian-kerja-dosen-luar-biasa/:id',
      name: 'update_surat_perjanjian_kerja_dosen_luar_biasa',
      component: FormSuratPerjanjianKerjaDosenLuarBiasa,
      meta: {
        is_login_required: true,
        can_accessed: ['admin_unit', 'superadmin'],
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
        can_accessed: ['admin_unit', 'superadmin'],
        layout: 'AuthenticatedLayout',
        parentName: 'surat_perjanjian_kerja_dosen_full_time',
      },
    },
    {
      path: '/surat-keluar/surat-perjanjian-kerja-dosen-full-time/:id',
      name: 'update_surat_perjanjian_kerja_dosen_full_time',
      component: FormSuratPerjanjianKerjaDosenFullTime,
      meta: {
        is_login_required: true,
        can_accessed: ['admin_unit', 'superadmin'],
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
