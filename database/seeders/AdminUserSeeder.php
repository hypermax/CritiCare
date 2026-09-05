<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRoleId = DB::table('roles')->where('code', 'ADMIN')->value('id');

        DB::table('users')->updateOrInsert(
            ['email' => 'admin@criticare.local'],
            [
                'name'       => 'Administrateur CritiCare',
                'role_id'    => $adminRoleId,
                'password'   => Hash::make('CritiCare2026!'),
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
