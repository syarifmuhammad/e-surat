const ApproveLetter = () => import('@/views/internal_incoming_letters/ApproveLetter.vue')
const SignedLetter = () => import('@/views/internal_incoming_letters/SignedLetter.vue')

const SuratKeteranganKerja = () => import('@/views/internal_incoming_letters/SuratKeteranganKerja/Index.vue')
const SuratKeputusanRotasiKepegawaian = () => import('@/views/internal_incoming_letters/SuratKeputusanRotasiKepegawaian/Index.vue')
const SuratKeputusanPemberhentian = () => import('@/views/internal_incoming_letters/SuratKeputusanPemberhentian/Index.vue')
const SuratKeputusanPengangkatan = () => import('@/views/internal_incoming_letters/SuratKeputusanPengangkatan/Index.vue')
const SuratKeputusanPemberhentianDanPengangkatan = () => import('@/views/internal_incoming_letters/SuratKeputusanPemberhentianDanPengangkatan/Index.vue')
const SuratPerjanjianKerjaMagang = () => import('@/views/internal_incoming_letters/SuratPerjanjianKerjaMagang/Index.vue')
const SuratPerjanjianKerjaDosenLuarBiasa = () => import('@/views/internal_incoming_letters/SuratPerjanjianKerjaDosenLuarBiasa/Index.vue')
const SuratPerjanjianKerjaDosenFullTime = () => import('@/views/internal_incoming_letters/SuratPerjanjianKerjaDosenFullTime/Index.vue')
const Preview = () => import('@/views/Preview.vue')

let routes = [
    {
        path: '/surat-masuk-internal/surat-keterangan-kerja',
        name: 'surat_keterangan_kerja_internal',
        component: SuratKeteranganKerja,
        meta: {
            is_login_required: true,
            can_accessed: ['*'],
            layout: 'AuthenticatedLayout',
        },
    },
    {
        path: '/surat-masuk-internal/surat-keterangan-kerja/:id/preview',
        name: 'preview_surat_keterangan_kerja_internal',
        component: Preview,
        meta: {
            is_login_required: true,
            can_accessed: ['*'],
            layout: 'BlankLayout',
            parentName: 'surat_keterangan_kerja_internal',
            api_letter: 'surat-keterangan-kerja',
        },
    },
    {
        path: '/surat-masuk-internal/surat-keterangan-kerja/:id/approve',
        name: 'approve_surat_keterangan_kerja_internal',
        component: ApproveLetter,
        meta: {
            is_login_required: true,
            can_accessed: ['*'],
            layout: 'AuthenticatedLayout',
            parentName: 'surat_keterangan_kerja_internal',
            api_letter: 'surat-keterangan-kerja',
            title: "Setujui Surat Keterangan Kerja",
        },
    },
    {
        path: '/surat-masuk-internal/surat-keterangan-kerja/:id/tanda-tangan',
        name: 'tanda_tangan_surat_keterangan_kerja_internal',
        component: SignedLetter,
        meta: {
            is_login_required: true,
            can_accessed: ['*'],
            layout: 'AuthenticatedLayout',
            parentName: 'surat_keterangan_kerja_internal',
            api_letter: 'surat-keterangan-kerja',
            title: "Tanda Tangan Surat Keterangan Kerja",
        },
    },
    {
        path: '/surat-masuk-internal/surat-keputusan-rotasi-kepegawaian',
        name: 'surat_keputusan_rotasi_kepegawaian_internal',
        component: SuratKeputusanRotasiKepegawaian,
        meta: {
            is_login_required: true,
            can_accessed: ['*'],
            layout: 'AuthenticatedLayout',
        },
    },
    {
        path: '/surat-masuk-internal/surat-keputusan-rotasi-kepegawaian/:id/preview',
        name: 'preview_surat_keputusan_rotasi_kepegawaian_internal',
        component: Preview,
        meta: {
            is_login_required: true,
            can_accessed: ['*'],
            layout: 'BlankLayout',
            parentName: 'surat_keputusan_rotasi_kepegawaian_internal',
            api_letter: 'surat-keputusan-rotasi-kepegawaian',
        },
    },
    {
        path: '/surat-masuk-internal/surat-keputusan-rotasi-kepegawaian/:id/approve',
        name: 'approve_surat_keputusan_rotasi_kepegawaian_internal',
        component: ApproveLetter,
        meta: {
            is_login_required: true,
            can_accessed: ['*'],
            layout: 'AuthenticatedLayout',
            parentName: 'surat_keputusan_rotasi_kepegawaian_internal',
            api_letter: 'surat-keputusan-rotasi-kepegawaian',
            title: "Setujui Surat Keputusan Rotasi Kepegawaian",
        },
    },
    {
        path: '/surat-masuk-internal/surat-keputusan-rotasi-kepegawaian/:id/tanda-tangan',
        name: 'tanda_tangan_surat_keputusan_rotasi_kepegawaian_internal',
        component: SignedLetter,
        meta: {
            is_login_required: true,
            can_accessed: ['*'],
            layout: 'AuthenticatedLayout',
            parentName: 'surat_keputusan_rotasi_kepegawaian_internal',
            api_letter: 'surat-keputusan-rotasi-kepegawaian',
            title: "Tanda Tangan Surat Keputusan Rotasi Kepegawaian",
        },
    },
    {
        path: '/surat-masuk-internal/surat-keputusan-pemberhentian',
        name: 'surat_keputusan_pemberhentian_internal',
        component: SuratKeputusanPemberhentian,
        meta: {
            is_login_required: true,
            can_accessed: ['*'],
            layout: 'AuthenticatedLayout',
        },
    },
    {
        path: '/surat-masuk-internal/surat-keputusan-pemberhentian/:id/preview',
        name: 'preview_surat_keputusan_pemberhentian_internal',
        component: Preview,
        meta: {
            is_login_required: true,
            can_accessed: ['*'],
            layout: 'BlankLayout',
            parentName: 'surat_keputusan_pemberhentian_internal',
            api_letter: 'surat-keputusan-pemberhentian',

        },
    },
    {
        path: '/surat-masuk-internal/surat-keputusan-pemberhentian/:id/approve',
        name: 'approve_surat_keputusan_pemberhentian_internal',
        component: ApproveLetter,
        meta: {
            is_login_required: true,
            can_accessed: ['*'],
            layout: 'AuthenticatedLayout',
            parentName: 'surat_keputusan_pemberhentian_internal',
            api_letter: 'surat-keputusan-pemberhentian',
            title: "Setujui Surat Keputusan Pemberhentian",
        },
    },
    {
        path: '/surat-masuk-internal/surat-keputusan-pemberhentian/:id/tanda-tangan',
        name: 'tanda_tangan_surat_keputusan_pemberhentian_internal',
        component: SignedLetter,
        meta: {
            is_login_required: true,
            can_accessed: ['*'],
            layout: 'AuthenticatedLayout',
            parentName: 'surat_keputusan_pemberhentian_internal',
            api_letter: 'surat-keputusan-pemberhentian',
            title: "Tanda Tangan Surat Keputusan Pemberhentian",
        },
    },
    {
        path: '/surat-masuk-internal/surat-keputusan-pengangkatan',
        name: 'surat_keputusan_pengangkatan_internal',
        component: SuratKeputusanPengangkatan,
        meta: {
            is_login_required: true,
            can_accessed: ['*'],
            layout: 'AuthenticatedLayout',
        },
    },
    {
        path: '/surat-masuk-internal/surat-keputusan-pengangkatan/:id/preview',
        name: 'preview_surat_keputusan_pengangkatan_internal',
        component: Preview,
        meta: {
            is_login_required: true,
            can_accessed: ['*'],
            layout: 'BlankLayout',
            parentName: 'surat_keputusan_pengangkatan_internal',
            api_letter: 'surat-keputusan-pengangkatan',
        },
    },
    {
        path: '/surat-masuk-internal/surat-keputusan-pengangkatan/:id/approve',
        name: 'approve_surat_keputusan_pengangkatan_internal',
        component: ApproveLetter,
        meta: {
            is_login_required: true,
            can_accessed: ['*'],
            layout: 'AuthenticatedLayout',
            parentName: 'surat_keputusan_pengangkatan_internal',
            api_letter: 'surat-keputusan-pengangkatan',
            title: "Setujui Surat Keputusan Pengangkatan",
        },
    },
    {
        path: '/surat-masuk-internal/surat-keputusan-pengangkatan/:id/tanda-tangan',
        name: 'tanda_tangan_surat_keputusan_pengangkatan_internal',
        component: SignedLetter,
        meta: {
            is_login_required: true,
            can_accessed: ['*'],
            layout: 'AuthenticatedLayout',
            parentName: 'surat_keputusan_pengangkatan_internal',
            api_letter: 'surat-keputusan-pengangkatan',
            title: "Tanda Tangan Surat Keputusan Pengangkatan",
        },
    },
    {
        path: '/surat-masuk-internal/surat-keputusan-pemberhentian-dan-pengangkatan',
        name: 'surat_keputusan_pemberhentian_dan_pengangkatan_internal',
        component: SuratKeputusanPemberhentianDanPengangkatan,
        meta: {
            is_login_required: true,
            can_accessed: ['*'],
            layout: 'AuthenticatedLayout',
        },
    },
    {
        path: '/surat-masuk-internal/surat-keputusan-pemberhentian-dan-pengangkatan/:id/preview',
        name: 'preview_surat_keputusan_pemberhentian_dan_pengangkatan_internal',
        component: Preview,
        meta: {
            is_login_required: true,
            can_accessed: ['*'],
            layout: 'BlankLayout',
            parentName: 'surat_keputusan_pemberhentian_dan_pengangkatan_internal',
            api_letter: 'surat-keputusan-pemberhentian-dan-pengangkatan',
        },
    },
    {
        path: '/surat-masuk-internal/surat-keputusan-pemberhentian-dan-pengangkatan/:id/approve',
        name: 'approve_surat_keputusan_pemberhentian_dan_pengangkatan_internal',
        component: ApproveLetter,
        meta: {
            is_login_required: true,
            can_accessed: ['*'],
            layout: 'AuthenticatedLayout',
            parentName: 'surat_keputusan_pemberhentian_dan_pengangkatan_internal',
            api_letter: 'surat-keputusan-pemberhentian-dan-pengangkatan',
            title: "Setujui Surat Keputusan Pemberhentian Dan Pengangkatan",
        },
    },
    {
        path: '/surat-masuk-internal/surat-keputusan-pemberhentian-dan-pengangkatan/:id/tanda-tangan',
        name: 'tanda_tangan_surat_keputusan_pemberhentian_dan_pengangkatan_internal',
        component: SignedLetter,
        meta: {
            is_login_required: true,
            can_accessed: ['*'],
            layout: 'AuthenticatedLayout',
            parentName: 'surat_keputusan_pemberhentian_dan_pengangkatan_internal',
            api_letter: 'surat-keputusan-pemberhentian-dan-pengangkatan',
            title: "Tanda Tangan Surat Keputusan Pemberhentian Dan Pengangkatan",
        },
    },
    {
        path: '/surat-masuk-internal/surat-perjanjian-kerja-magang',
        name: 'surat_perjanjian_kerja_magang_internal',
        component: SuratPerjanjianKerjaMagang,
        meta: {
            is_login_required: true,
            can_accessed: ['*'],
            layout: 'AuthenticatedLayout',
        },
    },
    {
        path: '/surat-masuk-internal/surat-perjanjian-kerja-magang/:id/preview',
        name: 'preview_surat_perjanjian_kerja_magang_internal',
        component: Preview,
        meta: {
            is_login_required: true,
            can_accessed: ['*'],
            layout: 'BlankLayout',
            parentName: 'surat_perjanjian_kerja_magang_internal',
            api_letter: 'surat-perjanjian-kerja-magang',
        },
    },
    {
        path: '/surat-masuk-internal/surat-perjanjian-kerja-magang/:id/approve',
        name: 'approve_surat_perjanjian_kerja_magang_internal',
        component: ApproveLetter,
        meta: {
            is_login_required: true,
            can_accessed: ['*'],
            layout: 'AuthenticatedLayout',
            parentName: 'surat_perjanjian_kerja_magang_internal',
            api_letter: 'surat-perjanjian-kerja-magang',
            title: "Setujui Surat Perjanjian Kerja Magang",
        },
    },
    {
        path: '/surat-masuk-internal/surat-perjanjian-kerja-magang/:id/tanda-tangan',
        name: 'tanda_tangan_surat_perjanjian_kerja_magang_internal',
        component: SignedLetter,
        meta: {
            is_login_required: true,
            can_accessed: ['*'],
            layout: 'AuthenticatedLayout',
            parentName: 'surat_perjanjian_kerja_magang_internal',
            api_letter: 'surat-perjanjian-kerja-magang',
            title: "Tanda Tangan Surat Perjanjian Kerja Magang",
        },
    },
    {
        path: '/surat-masuk-internal/surat-perjanjian-kerja-dosen-luar-biasa',
        name: 'surat_perjanjian_kerja_dosen_luar_biasa_internal',
        component: SuratPerjanjianKerjaDosenLuarBiasa,
        meta: {
            is_login_required: true,
            can_accessed: ['*'],
            layout: 'AuthenticatedLayout',
        },
    },
    {
        path: '/surat-masuk-internal/surat-perjanjian-kerja-dosen-luar-biasa/:id/preview',
        name: 'preview_surat_perjanjian_kerja_dosen_luar_biasa_internal',
        component: Preview,
        meta: {
            is_login_required: true,
            can_accessed: ['*'],
            layout: 'BlankLayout',
            parentName: 'surat_perjanjian_kerja_dosen_luar_biasa_internal',
            api_letter: 'surat-perjanjian-kerja-dosen-luar-biasa',
        },
    },
    {
        path: '/surat-masuk-internal/surat-perjanjian-kerja-dosen-luar-biasa/:id/approve',
        name: 'approve_surat_perjanjian_kerja_dosen_luar_biasa_internal',
        component: ApproveLetter,
        meta: {
            is_login_required: true,
            can_accessed: ['*'],
            layout: 'AuthenticatedLayout',
            parentName: 'surat_perjanjian_kerja_dosen_luar_biasa_internal',
            api_letter: 'surat-perjanjian-kerja-dosen-luar-biasa',
            title: "Setujui Surat Perjanjian Kerja Dosen Luar Biasa",
        },
    },
    {
        path: '/surat-masuk-internal/surat-perjanjian-kerja-dosen-luar-biasa/:id/tanda-tangan',
        name: 'tanda_tangan_surat_perjanjian_kerja_dosen_luar_biasa_internal',
        component: SignedLetter,
        meta: {
            is_login_required: true,
            can_accessed: ['*'],
            layout: 'AuthenticatedLayout',
            parentName: 'surat_perjanjian_kerja_dosen_luar_biasa_internal',
            api_letter: 'surat-perjanjian-kerja-dosen-luar-biasa',
            title: "Tanda Tangan Surat Perjanjian Kerja Dosen Luar Biasa",
        },
    },
    {
        path: '/surat-masuk-internal/surat-perjanjian-kerja-dosen-full-time',
        name: 'surat_perjanjian_kerja_dosen_full_time_internal',
        component: SuratPerjanjianKerjaDosenFullTime,
        meta: {
            is_login_required: true,
            can_accessed: ['*'],
            layout: 'AuthenticatedLayout',
        },
    },
    {
        path: '/surat-masuk-internal/surat-perjanjian-kerja-dosen-full-time/:id/preview',
        name: 'preview_surat_perjanjian_kerja_dosen_full_time_internal',
        component: Preview,
        meta: {
            is_login_required: true,
            can_accessed: ['*'],
            layout: 'BlankLayout',
            parentName: 'surat_perjanjian_kerja_dosen_full_time_internal',
            api_letter: 'surat-perjanjian-kerja-dosen-full-time',
        },
    },
    {
        path: '/surat-masuk-internal/surat-perjanjian-kerja-dosen-full-time/:id/approve',
        name: 'approve_surat_perjanjian_kerja_dosen_full_time_internal',
        component: ApproveLetter,
        meta: {
            is_login_required: true,
            can_accessed: ['*'],
            layout: 'AuthenticatedLayout',
            parentName: 'surat_perjanjian_kerja_dosen_full_time_internal',
            api_letter: 'surat-perjanjian-kerja-dosen-full-time',
            title: "Setujui Surat Perjanjian Kerja Dosen Full Time",
        },
    },
    {
        path: '/surat-masuk-internal/surat-perjanjian-kerja-dosen-full-time/:id/tanda-tangan',
        name: 'tanda_tangan_surat_perjanjian_kerja_dosen_full_time_internal',
        component: SignedLetter,
        meta: {
            is_login_required: true,
            can_accessed: ['*'],
            layout: 'AuthenticatedLayout',
            parentName: 'surat_perjanjian_kerja_dosen_full_time_internal',
            api_letter: 'surat-perjanjian-kerja-dosen-full-time',
            title: "Tanda Tangan Surat Perjanjian Kerja Dosen Full Time",
        },
    },
]

export default routes