<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LaporanKaryawanExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data)->map(function ($item, $index) {
            $gajiPerKloter = collect($item['gaji_per_kloter'])->map(function ($gpk) {
                return "Kloter ID {$gpk['kloter_id']}: Rp " . number_format($gpk['gaji'], 0, ',', '.') . " ({$gpk['total_jam']} jam)";
            })->implode("\n"); // dipisah baris di Excel

            return [
                'No' => $index + 1,
                'Nama' => $item['karyawan']->nama,
                'Jenis Kelamin' => $item['karyawan']->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
                'No Telepon' => $item['karyawan']->no_telepon,
                'Total Jam Kerja' => $item['total_jam_kerja'] . ' Jam',
                'Gaji per Kloter' => $gajiPerKloter,
                'Total Gaji' => 'Rp ' . number_format($item['total_gaji'], 0, ',', '.'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Pekerja',
            'Jenis Kelamin',
            'No Telepon',
            'Total Jam Kerja',
            'Gaji per Kloter',
            'Total Gaji',
        ];
    }
}
