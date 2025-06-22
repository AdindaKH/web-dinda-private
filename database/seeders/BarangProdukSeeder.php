<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BarangProduk;

class BarangProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BarangProduk::insert([
            ['barang_id' => 1, 'kode' => 'PRD001'],
            ['barang_id' => 2, 'kode' => 'PRD002'],
            ['barang_id' => 3, 'kode' => 'PRD003'],
            ['barang_id' => 4, 'kode' => 'PRD004'],
            ['barang_id' => 5, 'kode' => 'PRD005'],
        ]);        
    }
}
