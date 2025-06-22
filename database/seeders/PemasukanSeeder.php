<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\Pemasukan;

class PemasukanSeeder extends Seeder
{
    public function run(): void
    {
        Pemasukan::create(['kode' => 'MSK001']);
        Pemasukan::create(['kode' => 'MSK002']);
        // Pemasukan::create(['kode' => 'MSK003']);
    }
}
