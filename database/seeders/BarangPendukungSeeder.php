<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BarangPendukung;

class BarangPendukungSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BarangPendukung::insert([
            ['barang_id' => 6, 'kode' => 'PDN001'],
            ['barang_id' => 7, 'kode' => 'PDN002'],
            ['barang_id' => 8, 'kode' => 'PDN003'],
            ['barang_id' => 9, 'kode' => 'PDN004'],
            ['barang_id' => 10, 'kode' => 'PDN005'],
        ]);
    }
}
