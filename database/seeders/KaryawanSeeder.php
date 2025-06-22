<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KaryawanSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['nama' => 'Andi',     'jenis_kelamin' => 'L', 'no_telepon' => '081234567890'],
            ['nama' => 'Budi',     'jenis_kelamin' => 'L', 'no_telepon' => '081234567891'],
            ['nama' => 'Citra',    'jenis_kelamin' => 'P', 'no_telepon' => '081234567892'],
            ['nama' => 'Dewi',     'jenis_kelamin' => 'P', 'no_telepon' => '081234567893'],
            ['nama' => 'Eka',      'jenis_kelamin' => 'L', 'no_telepon' => '081234567894'],
            ['nama' => 'Fajar',    'jenis_kelamin' => 'L', 'no_telepon' => '081234567895'],
            ['nama' => 'Gita',     'jenis_kelamin' => 'P', 'no_telepon' => '081234567896'],
            ['nama' => 'Hana',     'jenis_kelamin' => 'P', 'no_telepon' => '085123456732'],
            ['nama' => 'Iwan',     'jenis_kelamin' => 'L',  'no_telepon' => '085123456712'],
            ['nama' => 'Joko',      'jenis_kelamin' => 'L',  'no_telepon' => '085123456129'],
        ];

        foreach ($data as $item) {
            DB::table('karyawans')->insert([
                'nama' => $item['nama'],
                'jenis_kelamin' => $item['jenis_kelamin'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
