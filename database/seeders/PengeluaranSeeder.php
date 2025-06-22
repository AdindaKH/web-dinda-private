<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\Pengeluaran;

class PengeluaranSeeder extends Seeder
{
    public function run(): void
    {
        Pengeluaran::create(['kode' => 'KLR001']);
        // Pengeluaran::create(['kode' => 'KLR002']);
        // Pengeluaran::create(['kode' => 'KLR003']);
    }
}
