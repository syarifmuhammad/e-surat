<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Position;
use App\Models\Employee;
use App\Models\EmployeePosition;
use App\Models\LetterTemplate;
use App\Models\User;

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

        //create employee and user data superadmin
        $employees = [
            [
                'nip' => '000000000000000000',
                'name' => 'Super Admin',
                'email' => 'admin@example.com',
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'email_verified_at' => now(),
                'roles' => 'superadmin',
            ],
            [
                'nip' => '12345678',
                'name' => 'Admin SDM Dummy',
                'email' => 'admin_sdm@example.com',
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'email_verified_at' => now(),
                'roles' => 'admin_sdm',
                'position' => 'Wakil Rektor',
            ],
            [
                'nip' => '23456789',
                'name' => 'Admin Sekretariat Dummy',
                'email' => 'admin_sekretariat@example.com',
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'email_verified_at' => now(),
                'roles' => 'admin_sekretariat',
                'position' => 'Rektor',
            ],
            [
                'nip' => '34567890',
                'name' => 'Dosen Dummy',
                'email' => 'dosen1@example.com',
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'email_verified_at' => now(),
                'roles' => 'pegawai',
                'position' => "Dosen Rekayasa Perangkat Lunak",
            ]
        ];

        foreach ($employees as $employee) {
            Employee::create([
                'nip' => $employee['nip'],
                'name' => $employee['name'],
                'email' => $employee['email'],
            ]);
            
            User::create([
                'nip' => $employee['nip'],
                'email' => $employee['email'],
                'email_verified_at' => now(),
                'password' => $employee['password'],
                'roles' => $employee['roles'],
            ]);

            if (isset($employee['position'])) {
                EmployeePosition::create([
                    'nip' => $employee['nip'],
                    'position' => $employee['position']
                ]);
            }
        }

        //create default letter template
        LetterTemplate::create([
            'name' => 'Template 1',
            'letter_type' => 'SURAT_KETERANGAN_KERJA',
            'file' => 'b81f3481-0d57-448e-817a-8d46d2af0565.docx',
        ]);

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
