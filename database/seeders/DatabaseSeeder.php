<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'username' => 'admin',
            'name' => 'Admin',
            'email' => 'admin@argon.com',
            'password' => bcrypt('secret'),
            'role' => 'superadmin',
            'last_login' => Carbon::now(),
            'verified_at' => Carbon::now(),
        ]);

        DB::table('landing_pages')->insert([
            'judul' => 'Metode Topsis',
            'deskripsi' => 'Ini deskripsi'
        ]);

        DB::table('kriterias')->insert([
            [
                'kode' => 'KR001',
                'nama' => 'Kecepatan'
            ],
            [
                'kode' => 'KR002',
                'nama' => 'Jumlah Perangkat'
            ],
            [
                'kode' => 'KR003',
                'nama' => 'Jenis IP'
            ],
            [
                'kode' => 'KR004',
                'nama' => 'Jenis Layanan'
            ],
            [
                'kode' => 'KR005',
                'nama' => 'Rekomendasi Perangkat'
            ],
            [
                'kode' => 'KR006',
                'nama' => 'Rasio Down/Up'
            ],
            [
                'kode' => 'KR007',
                'nama' => 'Harga'
            ],
        ]);
    }
}
