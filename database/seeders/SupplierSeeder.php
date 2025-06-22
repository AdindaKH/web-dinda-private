<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Supplier::create(['nama' => 'PT. Sumber Pangan', 'alamat' => 'Surabaya', 'no_tlp' => '081234567890', 'no_rekening' => '1234567890']);
        Supplier::create(['nama' => 'CV. Berkah Usaha', 'alamat' => 'Malang', 'no_tlp' => '082223334444', 'no_rekening' => '0987654321']);
        Supplier::create(['nama' => 'Toko Makmur', 'alamat' => 'Gresik', 'no_tlp' => '083345678900', 'no_rekening' => '1122334455']);
        Supplier::create(['nama' => 'PT. Sumber Berkah', 'alamat' => 'Sidoarjo', 'no_tlp' => '084456789012', 'no_rekening' => '2233445566']);
    }
}
