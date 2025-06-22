<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;

class LaporanSupplierExport implements FromCollection
{
    protected $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data->map(function ($supplier) {
            return [
                'Nama' => $supplier->nama,
                'Kategori' => $supplier->pemasok ? 'Pemasok' : 'Konsumen',
                'Alamat' => $supplier->alamat,
                'No Telepon' => $supplier->no_tlp,
                'No Rekening' => $supplier->no_rekening ?? '-',
            ];
        });
    }
}
