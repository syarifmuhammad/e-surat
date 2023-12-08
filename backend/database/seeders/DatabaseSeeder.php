<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Position;
use App\Models\Employee;
use App\Models\EmployeePosition;
use App\Models\KeyPair;
use App\Models\LetterTemplate;
use App\Models\Prodi;
use App\Models\ReferenceNumberSetting;
use App\Models\Rekening;
use App\Models\User;
use League\CommonMark\Reference\Reference;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        //seeder to make positions
        $positions = [
            ['name' => 'Rektor'],
            ['name' => 'Wakil Rektor'],
            ['name' => 'Dosen Rekayasa Perangkat Lunak'],
            ['name' => 'Dosen Teknik Informatika'],
        ];
        foreach ($positions as $position) {
            Position::create($position);
        }

        //seeder to make prodi
        $prodi = [
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
            [
                'nama_prodi' => 'Sistem Informasi',
                'singkatan_prodi' => 'SI',
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
            ],
            [
                'nip' => '12345678',
                'name' => 'Admin SDM Dummy',
                'email' => 'admin_sdm@example.com',
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'email_verified_at' => now(),
                'roles' => 'admin_sdm',
                'position' => 'Wakil Rektor',
                'tempat_lahir' => "Bandung",
                'tanggal_lahir' => "1999-01-01",
                'alamat' => "Jl. Jalan",
                'npwp' => "12345678901234567891",
                'nama_bank' => "Bank BNI",
                'nomor_rekening' => "1234567891",
            ],
            [
                'nip' => '23456789',
                'name' => 'Admin Sekretariat Dummy',
                'email' => 'admin_sekretariat@example.com',
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'email_verified_at' => now(),
                'roles' => 'admin_sekretariat',
                'position' => 'Rektor',
                'tempat_lahir' => "Bandung",
                'tanggal_lahir' => "1999-01-01",
                'alamat' => "Jl. Jalan",
                'npwp' => "12345678901234567892",
                'nama_bank' => "Bank BCA",
                'nomor_rekening' => "1234567892",
            ],
            [
                'nip' => '34567890',
                'name' => 'Dosen Dummy',
                'email' => 'dosen1@example.com',
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'email_verified_at' => now(),
                'roles' => 'pegawai',
                'position' => "Dosen Rekayasa Perangkat Lunak",
                'tempat_lahir' => "Bandung",
                'tanggal_lahir' => "1999-01-01",
                'alamat' => "Jl. Jalan",
                'npwp' => "12345678901234567893",
                'nama_bank' => "Bank Mandiri",
                'nomor_rekening' => "1234567893",
            ]
        ];

        foreach ($employees as $employee) {
            $insert_employee = Employee::create([
                'nip' => $employee['nip'],
                'name' => $employee['name'],
                'email' => $employee['email'],
                'tempat_lahir' => $employee['tempat_lahir'],
                'tanggal_lahir' => $employee['tanggal_lahir'],
                'alamat' => $employee['alamat'],
                'npwp' => $employee['npwp'],
            ]);
            
            User::create([
                'id' => $insert_employee->id,
                'email' => $employee['email'],
                'email_verified_at' => now(),
                'password' => $employee['password'],
                'roles' => $employee['roles'],
            ]);

            KeyPair::storeKeys($insert_employee->id, $employee['password']);
            if (isset($employee['position'])) {
                EmployeePosition::create([
                    'employee_id' => $insert_employee->id,
                    'position' => $employee['position']
                ]);
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
            'name' => 'Template 1',
            'letter_type' => 'SURAT_KETERANGAN_KERJA',
            'file' => 'b81f3481-0d57-448e-817a-8d46d2af0565.docx',
        ]);
        LetterTemplate::create([
            'name' => 'Template 1',
            'letter_type' => 'SURAT_KEPUTUSAN_ROTASI_KEPEGAWAIAN',
            'file' => '693d726d-c3a6-40d3-9bd7-07fe11094959.docx',
        ]);
        LetterTemplate::create([
            'name' => 'Template 1',
            'letter_type' => 'SURAT_KEPUTUSAN_PEMBERHENTIAN',
            'file' => '402548d8-2857-4d05-a5c1-8e9ada4de791.docx',
        ]);
        LetterTemplate::create([
            'name' => 'Template 1',
            'letter_type' => 'SURAT_KEPUTUSAN_PENGANGKATAN',
            'file' => 'f1e522d7-a4d1-49d8-aff5-0a5e68eb422a.docx',
        ]);
        LetterTemplate::create([
            'name' => 'Template 1',
            'letter_type' => 'SURAT_KEPUTUSAN_PEMBERHENTIAN_DAN_PENGANGKATAN',
            'file' => 'f2369790-d402-4a01-b107-e6f9318b75ae.docx',
        ]);
        LetterTemplate::create([
            'name' => 'Template 1',
            'letter_type' => 'SURAT_PERJANJIAN_KERJA_DOSEN_FULL_TIME',
            'file' => 'bcc9c6f2-ab36-4988-8d77-f2461d7483b1.docx',
        ]);
        LetterTemplate::create([
            'name' => 'Template 1',
            'letter_type' => 'SURAT_PERJANJIAN_KERJA_DOSEN_LUAR_BIASA',
            'file' => 'edd8419e-2c5a-41b4-a261-380d971dff96.docx',
        ]);
        LetterTemplate::create([
            'name' => 'Template 1',
            'letter_type' => 'SURAT_PERJANJIAN_KERJA_MAGANG',
            'file' => '3aed34a1-8e66-4d59-990e-a4b13a918e00.docx',
        ]);


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
