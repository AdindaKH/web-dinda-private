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
            ['barang_id' => 1, 'kode' => 'PDN001'],
            ['barang_id' => 2, 'kode' => 'PDN002'],
            ['barang_id' => 3, 'kode' => 'PDN003'],
            ['barang_id' => 4, 'kode' => 'PDN004'],
            ['barang_id' => 5, 'kode' => 'PDN005'],
        ]);
    }
}
