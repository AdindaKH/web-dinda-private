<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Kloter; 
use Illuminate\Support\Facades\DB;

class KloterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('kloters')->insert([
            [
                'nama_kloter' => 'Kloter-4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kloter' => 'Kloter-5',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kloter' => 'Kloter-6',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);  
    }
}
