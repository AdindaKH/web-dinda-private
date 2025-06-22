<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Barang;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Barang::insert([
            [
                'nama_barang' => 'JKS T', 
                'qty' => 200, 
                'exp' => '2025-06-01', 
                'harga' => 36000
            ],
            [
                'nama_barang' => 'JKS B', 
                'qty' => 150, 
                'exp' => '2025-03-01', 
                'harga' => 40000
            ],
            [
                'nama_barang' => 'LEMET T', 
                'qty' => 150, 
                'exp' => '2025-06-01', 
                'harga' => 36000
            ],
            [
                'nama_barang' => 'LEMET B', 
                'qty' => 200, 
                'exp' => '2025-03-01', 
                'harga' => 40000
            ],
            [
                'nama_barang' => 'PETEK B', 
                'qty' => 200, 
                'exp' => '2025-06-01', 
                'harga' => 40000
            ],
            [
                'nama_barang' => 'Garam', 
                'qty' => 950, 
                'exp' => '2025-03-01', 
                'harga' => 10000
            ],
            [
                'nama_barang' => 'Kayu', 
                'qty' => 400, 
                'exp' => '2025-06-01', 
                'harga' => 950000
            ],
            [
                'nama_barang' => 'Batu Es', 
                'qty' => 160, 
                'exp' => '2025-03-01', 
                'harga' => 10000
            ],
            [
                'nama_barang' => 'Kardus', 
                'qty' => 3500, 
                'exp' => '2025-06-01', 
                'harga' => 1400
            ],
            [
                'nama_barang' => 'Uang Makan', 
                'qty' => 1, 
                'exp' => NULL, 
                'harga' => 600000
            ],
        ]);        
    }
}
