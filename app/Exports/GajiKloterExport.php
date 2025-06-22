<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GajiKloterExport implements FromCollection, WithHeadings
{
    protected $data;
    protected $tanggalUnik;

    public function __construct($data, $tanggalUnik)
    {
        $this->data = $data;
        $this->tanggalUnik = $tanggalUnik;
    }

    public function collection()
    {
        return collect($this->data)->map(function ($item) {
            $row = [
                'Nama' => $item['karyawan']->nama,
                'Jenis Kelamin' => $item['karyawan']->jenis_kelamin,
            ];

            foreach ($this->tanggalUnik as $tanggal) {
                $key = \Carbon\Carbon::parse($tanggal)->format('Y-m-d');
                $label = \Carbon\Carbon::parse($tanggal)->format('d-m-Y');
                $row[$label] = number_format($item['jam_per_tanggal'][$key] ?? 0, 2);
            }

            $row['Total Jam Kerja'] = number_format($item['total_jam'], 2);
            $row['Gaji per Jam'] = $item['gaji_per_jam'];
            $row['Total Gaji'] = $item['total_gaji'];

            return $row;
        });
    }

    public function headings(): array
    {
        $headings = ['Nama', 'Jenis Kelamin'];
        foreach ($this->tanggalUnik as $tanggal) {
            $headings[] = \Carbon\Carbon::parse($tanggal)->format('d-m-Y');
        }
        return array_merge($headings, ['Total Jam Kerja', 'Gaji per Jam', 'Total Gaji']);
    }
}
