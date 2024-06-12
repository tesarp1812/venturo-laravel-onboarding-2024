<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Faker\Provider\Fakecar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call([]);
        $faker = Faker::create('id_ID');

        //faker user kepala
        for ($i = 0; $i < 5; $i++) {
            $userId = Str::uuid(); // Membuat UUID baru
        
            DB::table('m_user')->insert([
                'id' => $userId,
                'user_roles_id' => 1,
                'name' => $faker->firstName,
                'email' => $faker->email,
                'password' => bcrypt('test123'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $customerID = Str::uuid(); // Membuat UUID baru
        
            // Menggunakan UUID pengguna untuk membuat data pelanggan (customer)
            DB::table('m_customer')->insert([
                'id' => $customerID,
                'm_user_id' => $userId,
                'name' => $faker->firstName, // Menambahkan nama pelanggan
                // Masukkan kolom lainnya sesuai kebutuhan
            ]);
        }
    }
}
