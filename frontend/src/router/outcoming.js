const LetterTemplates = () => import('@/views/outcoming_letters/LetterTemplates/Index.vue')
const FormLetterTemplates = () => import('@/views/outcoming_letters/LetterTemplates/Form.vue')
const Report = () => import('@/views/outcoming_letters/Report.vue')

const Preview = () => import('@/views/Preview.vue')

//surat keterangan kerja
const SuratKeteranganKerja = () => import('@/views/outcoming_letters/SuratKeteranganKerja/Index.vue')
const FormSuratKeteranganKerja = () => import('@/views/outcoming_letters/SuratKeteranganKerja/Form.vue')

//surat keputusan rotasi kepegawaian
const SuratKeputusanRotasiKepegawaian = () => import('@/views/outcoming_letters/SuratKeputusanRotasiKepegawaian/Index.vue')
const FormSuratKeputusanRotasiKepegawaian = () => import('@/views/outcoming_letters/SuratKeputusanRotasiKepegawaian/Form.vue')

//surat keputusan pemberhentian
const SuratKeputusanPemberhentian = () => import('@/views/outcoming_letters/SuratKeputusanPemberhentian/Index.vue')
const FormSuratKeputusanPemberhentian = () => import('@/views/outcoming_letters/SuratKeputusanPemberhentian/Form.vue')

//surat keputusan pengangkatan
const SuratKeputusanPengangkatan = () => import('@/views/outcoming_letters/SuratKeputusanPengangkatan/Index.vue')
const FormSuratKeputusanPengangkatan = () => import('@/views/outcoming_letters/SuratKeputusanPengangkatan/Form.vue')

//surat keputusan pemberhenetian dan pengangkatan
const SuratKeputusanPemberhentianDanPengangkatan = () => import('@/views/outcoming_letters/SuratKeputusanPemberhentianDanPengangkatan/Index.vue')
const FormSuratKeputusanPemberhentianDanPengangkatan = () => import('@/views/outcoming_letters/SuratKeputusanPemberhentianDanPengangkatan/Form.vue')

//surat perjanjian kerja magang
const SuratPerjanjianKerjaMagang = () => import('@/views/outcoming_letters/SuratPerjanjianKerjaMagang/Index.vue')
const FormSuratPerjanjianKerjaMagang = () => import('@/views/outcoming_letters/SuratPerjanjianKerjaMagang/Form.vue')

//surat perjanjian kerja dosen luar biasa
const SuratPerjanjianKerjaDosenLuarBiasa = () => import('@/views/outcoming_letters/SuratPerjanjianKerjaDosenLuarBiasa/Index.vue')
const FormSuratPerjanjianKerjaDosenLuarBiasa = () => import('@/views/outcoming_letters/SuratPerjanjianKerjaDosenLuarBiasa/Form.vue')

//surat perjanjian kerja dosen full time
const SuratPerjanjianKerjaDosenFullTime = () => import('@/views/outcoming_letters/SuratPerjanjianKerjaDosenFullTime/Index.vue')
const FormSuratPerjanjianKerjaDosenFullTime = () => import('@/views/outcoming_letters/SuratPerjanjianKerjaDosenFullTime/Form.vue')


let routes = [
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
            can_accessed: ['superadmin', 'admin_unit'],
            layout: 'AuthenticatedLayout',
        },
    },
    {
        path: '/surat-keluar/surat-keterangan-kerja/:id/preview',
        name: 'preview_surat_keterangan_kerja',
        component: Preview,
        meta: {
            is_login_required: true,
            can_accessed: ['superadmin', 'admin_unit'],

            layout: 'BlankLayout',
            parentName: 'surat_keterangan_kerja',
            api_letter: 'surat-keterangan-kerja',
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
            can_accessed: ['superadmin', 'admin_unit'],

            layout: 'AuthenticatedLayout',
        },
    },
    {
        path: '/surat-keluar/surat-keputusan-rotasi-kepegawaian/:id/preview',
        name: 'preview_surat_keputusan_rotasi_kepegawaian',
        component: Preview,
        meta: {
            is_login_required: true,
            can_accessed: ['superadmin', 'admin_unit'],

            layout: 'BlankLayout',
            parentName: 'surat_keputusan_rotasi_kepegawaian',
            api_letter: 'surat-keputusan-rotasi-kepegawaian',
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
            can_accessed: ['superadmin', 'admin_unit'],

            layout: 'AuthenticatedLayout',
        },
    },
    {
        path: '/surat-keluar/surat-keputusan-pemberhentian/:id/preview',
        name: 'preview_surat_keputusan_pemberhentian',
        component: Preview,
        meta: {
            is_login_required: true,
            can_accessed: ['superadmin', 'admin_unit'],

            layout: 'BlankLayout',
            parentName: 'surat_keputusan_pemberhentian',
            api_letter: 'surat-keputusan-pemberhentian',

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
            can_accessed: ['superadmin', 'admin_unit'],

            layout: 'AuthenticatedLayout',
        },
    },
    {
        path: '/surat-keluar/surat-keputusan-pengangkatan/:id/preview',
        name: 'preview_surat_keputusan_pengangkatan',
        component: Preview,
        meta: {
            is_login_required: true,
            can_accessed: ['superadmin', 'admin_unit'],

            layout: 'BlankLayout',
            parentName: 'surat_keputusan_pengangkatan',
            api_letter: 'surat-keputusan-pengangkatan',
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
            can_accessed: ['superadmin', 'admin_unit'],

            layout: 'AuthenticatedLayout',
        },
    },
    {
        path: '/surat-keluar/surat-keputusan-pemberhentian-dan-pengangkatan/:id/preview',
        name: 'preview_surat_keputusan_pemberhentian_dan_pengangkatan',
        component: Preview,
        meta: {
            is_login_required: true,
            can_accessed: ['superadmin', 'admin_unit'],

            layout: 'BlankLayout',
            parentName: 'surat_keputusan_pemberhentian_dan_pengangkatan',
            api_letter: 'surat-keputusan-pemberhentian-dan-pengangkatan',
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
            can_accessed: ['superadmin', 'admin_unit'],

            layout: 'AuthenticatedLayout',
        },
    },
    {
        path: '/surat-keluar/surat-perjanjian-kerja-magang/:id/preview',
        name: 'preview_surat_perjanjian_kerja_magang',
        component: Preview,
        meta: {
            is_login_required: true,
            can_accessed: ['superadmin', 'admin_unit'],

            layout: 'BlankLayout',
            parentName: 'surat_perjanjian_kerja_magang',
            api_letter: 'surat-perjanjian-kerja-magang',
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
            can_accessed: ['superadmin', 'admin_unit'],

            layout: 'AuthenticatedLayout',
        },
    },
    {
        path: '/surat-keluar/surat-perjanjian-kerja-dosen-luar-biasa/:id/preview',
        name: 'preview_surat_perjanjian_kerja_dosen_luar_biasa',
        component: Preview,
        meta: {
            is_login_required: true,
            can_accessed: ['superadmin', 'admin_unit'],

            layout: 'BlankLayout',
            parentName: 'surat_perjanjian_kerja_dosen_luar_biasa',
            api_letter: 'surat-perjanjian-kerja-dosen-luar-biasa',
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
            can_accessed: ['superadmin', 'admin_unit'],

            layout: 'AuthenticatedLayout',
        },
    },
    {
        path: '/surat-keluar/surat-perjanjian-kerja-dosen-full-time/:id/preview',
        name: 'preview_surat_perjanjian_kerja_dosen_full_time',
        component: Preview,
        meta: {
            is_login_required: true,
            can_accessed: ['superadmin', 'admin_unit'],

            layout: 'BlankLayout',
            parentName: 'surat_perjanjian_kerja_dosen_full_time',
            api_letter: 'surat-perjanjian-kerja-dosen-full-time',
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

export default routes