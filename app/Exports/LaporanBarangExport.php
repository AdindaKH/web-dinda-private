<?php

namespace App\Exports;


use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LaporanBarangExport implements FromCollection, WithHeadings
{
    protected $barangs;

    public function __construct($barangs)
    {
        $this->barangs = $barangs;
    }

    public function collection()
    {
        return $this->barangs->map(function ($b) {
            return [
                'kode' => $b->produk->kode ?? $b->pendukung->kode ?? '-',
                'nama_barang' => $b->nama_barang,
                'exp' => optional($b->exp)->format('d-m-Y'),
                'harga' => $b->harga,
                'qty' => $b->qty,
            ];
        });
    }

    public function headings(): array
    {
        return ['Kode', 'Nama Barang', 'Exp', 'Harga', 'Qty'];
    }
}
