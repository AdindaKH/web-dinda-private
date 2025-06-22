<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TonIkanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tonIkanData = [
            [
                'kloter_id' => 3,
                'jumlah_ton' => 200.00,
                'harga_ikan_per_ton' => 1000000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kloter_id' => 3,
                'jumlah_ton' => 50.00,
                'harga_ikan_per_ton' => 1000000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kloter_id' => 4,
                'jumlah_ton' => 50.00,
                'harga_ikan_per_ton' => 1000000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kloter_id' => 5,
                'jumlah_ton' => 50.00,
                'harga_ikan_per_ton' => 1000000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kloter_id' => 6,
                'jumlah_ton' => 50.00,
                'harga_ikan_per_ton' => 1000000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('ton_ikans')->insert($tonIkanData);
    }
}
