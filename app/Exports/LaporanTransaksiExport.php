<?php

namespace App\Exports;

use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LaporanTransaksiExport implements FromCollection, WithHeadings
{
    protected $tanggalMulai, $tanggalAkhir, $q;

    public function __construct($tanggalMulai, $tanggalAkhir, $q = null)
    {
        $this->tanggalMulai = $tanggalMulai;
        $this->tanggalAkhir = $tanggalAkhir;
        $this->q = $q;
    }

    public function collection()
    {
        $query = Transaksi::with([
            'barang.produk',
            'barang.pendukung',
            'supplier',
            'pemasukan',
            'pengeluaran'
        ]);

        if ($this->tanggalMulai && $this->tanggalAkhir) {
            $query->whereBetween('waktu_transaksi', [
                $this->tanggalMulai . ' 00:00:00',
                $this->tanggalAkhir . ' 23:59:59'
            ]);
        }

        if ($this->q) {
            $search = $this->q;
            $query->where(function ($q) use ($search) {
                $q->whereHas('barang', fn($q1) => $q1->where('nama_barang', 'like', "%$search%"))
                ->orWhereHas('supplier', fn($q2) => $q2->where('nama', 'like', "%$search%"))
                ->orWhereHas('barang.produk', fn($q3) => $q3->where('kode', 'like', "%$search%"))
                ->orWhereHas('barang.pendukung', fn($q4) => $q4->where('kode', 'like', "%$search%"))
                ->orWhereHas('pemasukan', fn($q5) => $q5->where('kode', 'like', "%$search%"))
                ->orWhereHas('pengeluaran', fn($q6) => $q6->where('kode', 'like', "%$search%"));
            });
        }

        $transaksis = $query->orderBy('waktu_transaksi')->get();

        $totalSebelumnya = 0;

        return $transaksis->map(function ($trx) use (&$totalSebelumnya) {
            $kodeBarang = $trx->barang->produk->kode ?? $trx->barang->pendukung->kode ?? '-';
            $masuk = $trx->pemasukan_id ? $trx->jumlahRp : 0;
            $keluar = $trx->pengeluaran_id ? $trx->jumlahRp : 0;

            $totalSekarang = $totalSebelumnya + $masuk - $keluar;
            $totalSebelumnya = $totalSekarang;

            return [
                'Waktu' => $trx->waktu_transaksi,
                'Kode Transaksi' => $trx->pemasukan->kode ?? $trx->pengeluaran->kode ?? '-',
                'Kode Barang' => $kodeBarang,
                'Supplier' => $trx->supplier->nama ?? '-',
                'Nama Barang' => $trx->barang->nama_barang ?? '-',
                'Qty' => $trx->qtyHistori ?? 0,
                'Masuk' => $masuk,
                'Keluar' => $keluar,
                'Total' => $totalSekarang,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Waktu',
            'Kode Transaksi',
            'Kode Barang',
            'Supplier',
            'Nama Barang',
            'Qty',
            'Masuk',
            'Keluar',
            'Total'
        ];
    }
}
