<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\CategoryIncomingLetter;
use Illuminate\Database\Seeder;
use App\Models\Position;
use App\Models\Employee;
use App\Models\EmployeePosition;
use App\Models\LetterTemplate;
use App\Models\Prodi;
use App\Models\ReferenceNumberSetting;
use App\Models\Rekening;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        //seeder to make positions
        $positions = [
            ['name' => 'Rektor', 'type' => 'struktural',],
            ['name' => 'Wakil Rektor', 'type' => 'struktural',],
            ['name' => 'Sekretaris Rektorat', 'type' => 'struktural',],
            ['name' => 'Dosen Rekayasa Perangkat Lunak', 'type' => 'struktural',],
            ['name' => 'Dosen Teknik Informatika', 'type' => 'struktural',],
            ['name' => 'Dosen Sistem Informasi', 'type' => 'struktural',],
            ['name' => 'Dosen Teknologi Informasi', 'type' => 'struktural',],
            ['name' => 'Dosen Sains Data', 'type' => 'struktural',],
            ['name' => 'Dosen Teknik Industri', 'type' => 'struktural',],
            ['name' => 'Dosen Teknik Elektro', 'type' => 'struktural',],
            ['name' => 'Dosen Teknik Komputer', 'type' => 'struktural',],
            ['name' => 'Dosen Teknik Telekomunikasi', 'type' => 'struktural',],
            ['name' => 'Dosen Bisnis Digital', 'type' => 'struktural',],
            ['name' => 'Dosen Luar Biasa', 'type' => 'struktural',],
            ['name' => 'NJAD. 2', 'type' => 'fungsional',],
            ['name' => 'NJAD. 3', 'type' => 'fungsional',],
            ['name' => 'AA. 2', 'type' => 'fungsional',],
            ['name' => 'AA. 3', 'type' => 'fungsional',],
            ['name' => 'L. 2', 'type' => 'fungsional',],
            ['name' => 'L. 3', 'type' => 'fungsional',],
            ['name' => 'LK. 2', 'type' => 'fungsional',],
            ['name' => 'LK. 3', 'type' => 'fungsional',],
            ['name' => 'GB. 3', 'type' => 'fungsional',],
            ['name' => 'MUDA. 1', 'type' => 'fungsional',],
            ['name' => 'MUDA. 2', 'type' => 'fungsional',],
            ['name' => 'MADYA. 1', 'type' => 'fungsional',],
            ['name' => 'MADYA. 2', 'type' => 'fungsional',],
            ['name' => 'MADYA. 3', 'type' => 'fungsional',],
            ['name' => 'AHLI. 1', 'type' => 'fungsional',],
            ['name' => 'AHLI. 2', 'type' => 'fungsional',],
            ['name' => 'AHLI. 3', 'type' => 'fungsional',],
            ['name' => 'UTAMA. 1', 'type' => 'fungsional',],
            ['name' => 'UTAMA. 2', 'type' => 'fungsional',],
            ['name' => 'UTAMA. 3', 'type' => 'fungsional',],
        ];
        foreach ($positions as $position) {
            Position::create($position);
        }

        //seeder to make prodi
        $prodi = [
            [
                'nama_prodi' => 'Sistem Informasi',
                'singkatan_prodi' => 'SI',
                'nama_fakultas' => "Fakultas Teknologi Informasi Dan Bisnis",
                'singkatan_fakultas' => "FTIB",
            ],
            [
                'nama_prodi' => 'Rekayasa Perangkat Lunak',
                'singkatan_prodi' => 'RPL',
                'nama_fakultas' => "Fakultas Teknologi Informasi Dan Bisnis",
                'singkatan_fakultas' => "FTIB",
            ],
            [
                'nama_prodi' => 'Teknik Informatika',
                'singkatan_prodi' => 'IF',
                'nama_fakultas' => "Fakultas Teknologi Informasi Dan Bisnis",
                'singkatan_fakultas' => "FTIB",
            ],
        ];
        foreach ($prodi as $p) {
            Prodi::create($p);
        }

        //create employee and user data superadmin
        $employees = [
            [
                'nip' => '000000000000000000',
                'nik' => '000000000000000000',
                'name' => 'Super Admin',
                'email' => 'admin@example.com',
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'email_verified_at' => now(),
                'roles' => 'superadmin',
                'tempat_lahir' => "Bandung",
                'tanggal_lahir' => "1999-01-01",
                'alamat' => "Jl. Jalan",
                'npwp' => "12345678901234567890",
                'nama_bank' => "Bank BRI",
                'nomor_rekening' => "1234567890",
                'profesi' => "tpa",
            ],
            [
                'nip' => '12345678',
                'nik' => '12345678',
                'name' => 'Admin Unit Dummy',
                'email' => 'admin_unit@example.com',
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'email_verified_at' => now(),
                'roles' => 'admin_unit',
                'tempat_lahir' => "Bandung",
                'tanggal_lahir' => "1999-01-01",
                'alamat' => "Jl. Jalan",
                'npwp' => "12345678901234567891",
                'nama_bank' => "Bank BNI",
                'nomor_rekening' => "1234567891",
                'created_by' => 1,
                'profesi' => 'tpa',
                'positions' => [
                    [
                        'position' => 'Sekretaris SDM',
                        'type' => 'struktural',
                    ]
                ],
            ],
            [
                'nip' => '23456789',
                'nik' => '23456789',
                'name' => 'Admin Sekretariat Dummy',
                'email' => 'admin_sekretariat@example.com',
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'email_verified_at' => now(),
                'roles' => 'admin_sekretariat',
                'tempat_lahir' => "Bandung",
                'tanggal_lahir' => "1999-01-01",
                'alamat' => "Jl. Jalan",
                'npwp' => "12345678901234567892",
                'nama_bank' => "Bank BCA",
                'nomor_rekening' => "1234567892",
                'created_by' => 1,
                'profesi' => 'tpa',
                'positions' => [
                    [
                        'position' => 'Sekretaris Rektorat',
                        'type' => 'struktural',
                    ]
                ],
            ],
            [
                'nip' => '99999999999',
                'nik' => '99999999999',
                'name' => 'Rektor',
                'email' => 'rektor@example.com',
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'email_verified_at' => now(),
                'roles' => 'pegawai',
                'tempat_lahir' => "Bandung",
                'tanggal_lahir' => "1999-01-01",
                'alamat' => "Jl. Jalan",
                'npwp' => "12345678901234567893",
                'nama_bank' => "Bank Mandiri",
                'nomor_rekening' => "1234567893",
                'created_by' => 2,
                'profesi' => 'tpa',
                'positions' => [
                    [
                        'position' => "Rektor",
                        'type' => 'struktural',
                    ]
                ],
            ],
            [
                'nip' => '34567890',
                'nik' => '34567890',
                'name' => 'Dosen Dummy',
                'email' => 'dosen1@example.com',
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'email_verified_at' => now(),
                'roles' => 'pegawai',
                'tempat_lahir' => "Bandung",
                'tanggal_lahir' => "1999-01-01",
                'alamat' => "Jl. Jalan",
                'npwp' => "12345678901234567893",
                'nama_bank' => "Bank Mandiri",
                'nomor_rekening' => "1234567893",
                'created_by' => 2,
                'profesi' => 'dosen',
                'positions' => [
                    [
                        'position' => "Dosen Rekayasa Perangkat Lunak",
                        'type' => 'struktural',
                    ]
                ],
            ],
            [
                'nip' => '643824376437',
                'nik' => '97351743743',
                'name' => 'Dosen Luar Biasa Dummy',
                'email' => 'dosenluarbiasa@example.com',
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'email_verified_at' => now(),
                'roles' => 'pegawai',
                'tempat_lahir' => "Bandung",
                'tanggal_lahir' => "1999-01-01",
                'alamat' => "Jl. Jalan",
                'npwp' => "12345678901234567893",
                'nama_bank' => "Bank Mandiri",
                'nomor_rekening' => "1234567893",
                'created_by' => 2,
                'profesi' => 'dosen',
                'positions' => [
                    [
                        'position' => "Dosen Luar Biasa",
                        'type' => 'struktural',
                    ]
                ],
            ]
        ];

        foreach ($employees as $employee) {
            $insert_employee = Employee::create([
                'nip' => $employee['nip'],
                'nik' => $employee['nik'],
                'name' => $employee['name'],
                'email' => $employee['email'],
                'tempat_lahir' => $employee['tempat_lahir'],
                'tanggal_lahir' => $employee['tanggal_lahir'],
                'alamat' => $employee['alamat'],
                'npwp' => $employee['npwp'],
                'created_by' => $employee['created_by'] ?? null,
                'profesi' => $employee['profesi'],
            ]);

            User::create([
                'id' => $insert_employee->id,
                'email' => $employee['email'],
                'email_verified_at' => now(),
                'password' => $employee['password'],
                'roles' => $employee['roles'],
            ]);

            if (isset($employee['positions'])) {
                foreach ($employee['positions'] as $p) {
                    EmployeePosition::create([
                        'employee_id' => $insert_employee->id,
                        'position' => $p['position'],
                        'type' => $p['type'],
                    ]);
                }
            }

            Rekening::create([
                'employee_id' => $insert_employee->id,
                'atas_nama' => $employee['name'],
                'nama_bank' => $employee['nama_bank'],
                'nomor_rekening' => $employee['nomor_rekening'],
            ]);
        }

        //create default letter template
        LetterTemplate::create([
            'name' => 'Template Surat Keterangan Kerja',
            'letter_type' => 'SURAT_KETERANGAN_KERJA',
            'file' => 'b81f3481-0d57-448e-817a-8d46d2af0565.docx',
        ]);
        LetterTemplate::create([
            'name' => 'Template Surat Keputusan Rotasi Kepegawaian',
            'letter_type' => 'SURAT_KEPUTUSAN_ROTASI_KEPEGAWAIAN',
            'file' => '693d726d-c3a6-40d3-9bd7-07fe11094959.docx',
        ]);
        LetterTemplate::create([
            'name' => 'Template Keputusan Pemberhentian Dalam Jabatan',
            'letter_type' => 'SURAT_KEPUTUSAN_PEMBERHENTIAN',
            'file' => '402548d8-2857-4d05-a5c1-8e9ada4de791.docx',
        ]);
        LetterTemplate::create([
            'name' => 'Template Keputusan Pengangkatan Dalam Jabatan',
            'letter_type' => 'SURAT_KEPUTUSAN_PENGANGKATAN',
            'file' => 'f1e522d7-a4d1-49d8-aff5-0a5e68eb422a.docx',
        ]);
        LetterTemplate::create([
            'name' => 'Template Keputusan Pemberhentian dan Pengangkatan Dalam Jabatan',
            'letter_type' => 'SURAT_KEPUTUSAN_PEMBERHENTIAN_DAN_PENGANGKATAN',
            'file' => 'f2369790-d402-4a01-b107-e6f9318b75ae.docx',
        ]);
        LetterTemplate::create([
            'name' => 'Template Surat Perjanjian Kerja Dosen Full Time',
            'letter_type' => 'SURAT_PERJANJIAN_KERJA_DOSEN_FULL_TIME',
            'file' => 'bcc9c6f2-ab36-4988-8d77-f2461d7483b1.docx',
        ]);
        LetterTemplate::create([
            'name' => 'Template Surat Perjanjian Kerja Dosen Luar Biasa',
            'letter_type' => 'SURAT_PERJANJIAN_KERJA_DOSEN_LUAR_BIASA',
            'file' => 'edd8419e-2c5a-41b4-a261-380d971dff96.docx',
        ]);
        LetterTemplate::create([
            'name' => 'Template Surat Perjanjian Kerja Magang',
            'letter_type' => 'SURAT_PERJANJIAN_KERJA_MAGANG',
            'file' => '3aed34a1-8e66-4d59-990e-a4b13a918e00.docx',
        ]);


        // create fake categories for incoming letters

        $categories = [
            ['name' => 'Surat Undangan'],
            ['name' => 'Surat Pemberitahuan'],
            ['name' => 'Surat Pernyataan'],
        ];

        foreach ($categories as $category) {
            CategoryIncomingLetter::create($category);
        }


        // create fake ReferenceNumberSetting
        // ReferenceNumberSetting::create([
        //     'letter_type' => 'SURAT_KETERANGAN_KERJA',
        //     'prefix' => '',
        //     'suffix' => '/SDM11/WRII/{bln}/{thn}',
        // ]);

        // $length_of_employee_want_to_make = 4;

        // // create the fake employees with position like above
        // Employee::factory($length_of_employee_want_to_make)->create();
        // $employees = Employee::get()->take($length_of_employee_want_to_make);

        // // create position for employees
        // $positions = Position::all()->map(function($p) {
        //     return $p->name;
        // });
        // foreach ($employees as $employee) {
        //     EmployeePosition::create([
        //         'nip' => $employee->nip,
        //         'position' => fake()->randomElement($positions)
        //     ]);
        // }

        // // create the fake users with nip and email from above employees factory
        // foreach ($employees as $employee) {
        //     User::create([
        //         'nip' => $employee->nip,
        //         'email' => $employee->email,
        //         'email_verified_at' => now(),
        //         'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        //     ]);
        // }
    }
}
