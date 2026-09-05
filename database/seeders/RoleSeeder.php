<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'code'        => 'ADMIN',
                'label'       => 'Administrator',
                'description' => 'Application and user account management',
            ],
            [
                'code'        => 'SENIOR',
                'label'       => 'Senior Physician',
                'description' => 'Senior intensivist or responsible physician',
            ],
            [
                'code'        => 'JUNIOR',
                'label'       => 'Junior Physician',
                'description' => 'Junior physician working under supervision',
            ],
            [
                'code'        => 'INTERN',
                'label'       => 'Medical Intern',
                'description' => 'Intern with limited clinical access',
            ],
            [
                'code'        => 'NURSE',
                'label'       => 'ICU Nurse',
                'description' => 'Nursing staff with nursing workflow access',
            ],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['code' => $role['code']],
                array_merge($role, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
