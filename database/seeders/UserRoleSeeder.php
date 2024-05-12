<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Input data default untuk tabel m_user_role
        DB::table('m_user_roles')->insert([
            'id' => "1",
            'name' => 'Admin',
            'access' => 'Admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
