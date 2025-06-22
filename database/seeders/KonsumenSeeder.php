<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Konsumen;

class KonsumenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Konsumen::insert([
            ['supplier_id' => 1, 'kode' => 'KSM001'],
        ]);
    }
}
