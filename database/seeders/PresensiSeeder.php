<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PresensiSeeder extends Seeder
{
    public function run(): void
    {
        $karyawanIds = range(1, 10); // 10 karyawan
        $tanggalList = [
            '2025-04-27',
            '2025-04-28',
            '2025-04-29',
            '2025-04-30',
            '2025-05-01',
            '2025-05-02',
            '2025-05-03',
            '2025-05-05',
            '2025-05-06',
            '2025-05-07',
            '2025-05-10',
            '2025-05-11',
            '2025-05-12',
            '2025-05-13',
            '2025-05-17',
            '2025-05-18',
            '2025-05-19',
            '2025-05-20',
            '2025-05-21',
            '2025-05-22',
            '2025-05-26',
            '2025-05-27',
            '2025-05-28',
            '2025-05-29',
            '2025-05-30',
            '2025-06-03',
            '2025-06-04',
            '2025-06-05',
            '2025-06-06',
            '2025-06-07',
            '2025-06-08',
            '2025-06-09',
        ];
        
        $kloterMapping = [
            '2025-04-27' => 1,
            '2025-04-28' => 1,
            '2025-04-29' => 1,
            '2025-04-30' => 1,
            '2025-05-01' => 2,
            '2025-05-02' => 2,
            '2025-05-03' => 2,
            '2025-05-05' => 2,
            '2025-05-06' => 2,
            '2025-05-07' => 2,
            '2025-05-10' => 3,
            '2025-05-11' => 3,
            '2025-05-12' => 3,
            '2025-05-13' => 3,
            '2025-05-17' => 3,
            '2025-05-18' => 3,
            '2025-05-19' => 4,
            '2025-05-20' => 4,
            '2025-05-21' => 4,
            '2025-05-22' => 4,
            '2025-05-26' => 4,
            '2025-05-27' => 4,
            '2025-05-28' => 4,
            '2025-05-29' => 5,
            '2025-05-30' => 5,
            '2025-06-03' => 5,
            '2025-06-04' => 5,
            '2025-06-05' => 5,
            '2025-06-06' => 5,
            '2025-06-07' => 6,
            '2025-06-08' => 6,
            '2025-06-09' => 6,
        ];

        $presensiData = [];

        foreach ($tanggalList as $tanggal) {
            foreach ($karyawanIds as $karyawanId) {
                // Simulasikan absensi sebagian orang di tanggal tertentu (optional)
                if (
                    ($tanggal === '2025-04-27' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-04-27' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-04-27' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-04-27' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-04-28' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-04-28' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-04-28' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-04-28' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-04-29' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-04-29' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-04-29' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-04-29' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-04-30' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-04-30' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-04-30' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-04-30' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-01' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-01' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-01' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-01' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-01' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-02' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-02' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-02' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-02' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-03' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-03' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-03' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-03' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-05' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-05' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-05' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-05' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-05' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-06' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-06' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-06' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-06' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-06' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-06' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-07' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-07' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-07' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-10' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-10' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-10' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-11' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-11' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-11' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-12' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-12' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-12' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-13' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-13' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-13' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-17' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-17' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-17' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-18' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-18' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-18' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-19' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-19' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-19' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-20' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-20' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-20' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-21' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-21' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-21' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-22' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-22' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-22' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-22' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-20' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-20' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-20' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-26' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-26' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-26' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-27' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-27' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-27' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-28' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-28' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-28' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-29' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-29' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-29' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-30' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-30' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-30' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-30' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-05-30' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-06-03' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-06-03' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-06-04' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-06-04' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-06-05' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-06-05' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-06-05' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-06-06' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-06-06' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-06-07' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-06-08' && $karyawanId == rand(1,10)) ||
                    ($tanggal === '2025-06-09' && $karyawanId == rand(1,10)) 
                ) {
                    continue; // skip, tidak hadir
                }

                $presensiData[] = [
                    'karyawan_id' => $karyawanId,
                    'kloter_id' => $kloterMapping[$tanggal],
                    'tanggal' => $tanggal,
                    'jam_masuk' => $this->getJamMasuk($karyawanId),
                    'jam_pulang' => $this->getJamPulang($karyawanId),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('presensis')->insert($presensiData);
    }

    // Bisa diatur berdasarkan ID untuk variasi jam masuk dan pulang
    private function getJamMasuk(int $karyawanId): string
    {
        return in_array($karyawanId, [1, 6]) ? '08:40:32' : '05:19:13';
    }

    private function getJamPulang(int $karyawanId): string
    {
        return in_array($karyawanId, [1, 6]) ? '17:12:28' : '18:24:42';
    }
}
