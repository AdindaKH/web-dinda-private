<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Barang;
use App\Models\Supplier;
use App\Models\Karyawan;
use App\Models\Presensi;
use App\Models\Kloter;
use App\Models\TonIkan;
use App\Models\Transaksi;

use App\Exports\LaporanBarangExport;
use App\Exports\LaporanKaryawanExport;
use App\Exports\LaporanSupplierExport;
use App\Exports\LaporanTransaksiExport;

class LaporanController extends Controller
{
    public function barang(Request $request)
    {
        $filter = $request->query('filter');
        $nama = $request->query('nama');
        $isAjax = $request->ajax();
        $isExportExcel = $request->query('export') === 'excel';
        $isExportPdf = $request->query('export') === 'pdf';

        $barangs = Barang::with(['produk', 'pendukung']);

        if (in_array($filter, ['produk', 'pendukung'])) {
            $barangs->whereHas($filter);
        }

        if ($nama) {
            $barangs->where('nama_barang', 'like', '%' . $nama . '%');
        }

        $barangs = $barangs->get()->sortBy(function ($barang) {
            if ($barang->produk) {
                return '1' . $barang->produk->kode;
            } elseif ($barang->pendukung) {
                return '2' . $barang->pendukung->kode;
            } else {
                return '9';
            }
        })->values();

        if ($isExportExcel) {
            return Excel::download(new LaporanBarangExport($barangs), 'laporan-barang.xlsx');
        }

        if ($isExportPdf) {
            $pdf = PDF::loadView('pimpinan.laporan_barang.pdf', [
                'barangs' => $barangs,
                'filter' => $filter,
                'nama' => $nama
            ])->setPaper('a4', 'landscape');

            return $pdf->download('laporan-barang.pdf');
        }

        if ($isAjax) {
            return view('pimpinan.laporan_barang._table', compact('barangs'))->render();
        }

        return view('pimpinan.laporan_barang.index', compact('barangs', 'filter', 'nama'));
    }

    public function karyawan(Request $request)
    {
        $filter = $request->input('filter', 'minggu_ini');
        $nama = $request->input('nama');

        switch ($filter) {
            case 'kloter_terbaru':
                // Ambil kloter yang memiliki presensi dengan tanggal paling akhir
                $presensiTerbaru = Presensi::latest('tanggal')->first();

                if ($presensiTerbaru && $presensiTerbaru->kloter) {
                    $kloterTerbaru = $presensiTerbaru->kloter;

                    // Gunakan accessor dari model Kloter
                    $startDate = Carbon::parse($kloterTerbaru->tanggal_mulai);
                    $endDate = Carbon::parse($kloterTerbaru->tanggal_akhir);
                } else {
                    $startDate = $endDate = null;
                }
                break;
            case 'hari_ini':
                $startDate = Carbon::today();
                $endDate = Carbon::today();
                break;
            case 'bulan_ini':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'semua':
                $startDate = null;
                $endDate = null;
                break;
            case 'minggu_ini':
            default:
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
        }

        // Ambil semua kloter yang memiliki ton ikan
        $kloters = Kloter::with(['tonIkan', 'presensis'])
            ->get()
            ->filter(fn($kloter) => $kloter->tonIkan);

        // Hitung gaji per jam tiap kloter
        // $gajiPerJamKloter = [];

        $gajiPerHariKloter = [];

        foreach ($kloters as $kloter) {
            $tonIkan = $kloter->tonIkan;
            $jumlahTon = $tonIkan->jumlah_ton ?? 0;
            $hargaPerTon = $tonIkan->harga_ikan_per_ton ?? 0;

            $karyawanUnik = Presensi::where('kloter_id', $kloter->id)
                ->distinct('karyawan_id')
                ->pluck('karyawan_id');

            $jumlahPekerjaKloter = $karyawanUnik->count();

            $gajiPerHariKloter[$kloter->id] = $jumlahPekerjaKloter > 0
                ? ($jumlahTon * $hargaPerTon) / $jumlahPekerjaKloter
                : 0;
        }


        $karyawans = Karyawan::with('presensis.kloter')
            ->when($nama, fn($query) => $query->where('nama', 'like', '%' . $nama . '%'))
            ->get();

        $data = [];

        foreach ($karyawans as $karyawan) {
            $totalJamKerja = 0;
            $totalGaji = 0;

            // Ambil semua presensi karyawan, filter sesuai tanggal (misal filter minggu ini/hari ini)
            $presensis = $karyawan->presensis()
                ->when($startDate && $endDate, fn($query) => $query->whereBetween('tanggal', [$startDate, $endDate]))
                ->with('kloter')
                ->get();

            // Kelompokkan presensi per kloter
            $presensiPerKloter = $presensis->groupBy('kloter_id');

            $gajiPerKloter = []; // Hitung gaji per kloter

            foreach ($presensiPerKloter as $kloterId => $presensiKloter) {
                // Hitung total jam kerja karyawan di kloter ini
                $totalJamKerjaKloter = 0;
                foreach ($presensiKloter as $presensi) {
                    $jamMasuk = Carbon::parse($presensi->jam_masuk);
                    $jamPulang = Carbon::parse($presensi->jam_pulang);
                    $jamKerja = $jamPulang->diffInSeconds($jamMasuk) / 3600;
                    $totalJamKerjaKloter += $jamKerja;
                }

                $totalJamKerja += $totalJamKerjaKloter;

                // Gaji per hari kloter (dari hasil sebelumnya)
                $gajiPerHari = $gajiPerHariKloter[$kloterId] ?? 0;  // Variabel ini perlu kamu hitung sebelum loop karyawan

                // Hitung gaji per jam untuk karyawan di kloter ini
                $gajiPerJam = $totalJamKerjaKloter > 0 ? ($gajiPerHari / $totalJamKerjaKloter) : 0;

                // Hitung gaji kloter ini
                $gajiKloterIni = $gajiPerJam * $totalJamKerjaKloter;

                // Simpan detail per kloter
                $gajiPerKloter[] = [
                    'kloter_id' => $kloterId,
                    'total_jam' => round($totalJamKerjaKloter, 2),
                    'gaji' => round($gajiKloterIni, 0),
                ];

                // Tambah total gaji karyawan
                $totalGaji += $gajiKloterIni;
            }

            // Potong 20% jika karyawan perempuan
            if ($karyawan->jenis_kelamin === 'P') {
                $totalGaji *= 0.8;
            }

            $data[] = [
                'karyawan' => $karyawan,
                'total_jam_kerja' => round($totalJamKerja, 2),
                'gaji_per_kloter' => $gajiPerKloter,
                'total_gaji' => round($totalGaji, 0),
            ];
        }

        if ($request->ajax()) {
            return view('pimpinan.laporan_karyawan._table', compact('data'));
        }

        if ($request->has('export') && $request->export === 'excel') {
            return Excel::download(new LaporanKaryawanExport($data), 'laporan_karyawan.xlsx');
        }

        if ($request->has('export') && $request->export === 'pdf') {
            $pdf = Pdf::loadView('pimpinan.laporan_karyawan.pdf', [
                'data' => $data,
                'filter' => $filter,
                'nama' => $nama
            ])->setPaper('a4', 'landscape');

            return $pdf->download('laporan_karyawan.pdf');
        }


        return view('pimpinan.laporan_karyawan.index', compact('data', 'filter', 'nama'));
    }

    public function supplier(Request $request)
    {
        $query = Supplier::with(['pemasok', 'konsumen']);

        if ($request->filled('keyword')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->keyword . '%')
                ->orWhere('alamat', 'like', '%' . $request->keyword . '%');
            });
        }

        if ($request->filled('kategori')) {
            if ($request->kategori === 'pemasok') {
                $query->whereHas('pemasok');
            } elseif ($request->kategori === 'konsumen') {
                $query->whereHas('konsumen');
            }
        }

        $suppliers = $query->get();

        if ($request->has('export') && $request->export === 'excel') {
            return Excel::download(new LaporanSupplierExport($suppliers), 'laporan_supplier.xlsx');
        }
        
        if ($request->has('export') && $request->export === 'pdf') {
            $pdf = Pdf::loadView('pimpinan.laporan_supplier.pdf', [
                'suppliers' => $suppliers,
                'kategori' => $request->kategori,
                'keyword' => $request->keyword
            ])->setPaper('a4', 'landscape');

            return $pdf->download('laporan_supplier.pdf');
        }

        if ($request->ajax()) {
            return view('pimpinan.laporan_supplier._table', compact('suppliers'))->render();
        }

        return view('pimpinan.laporan_supplier.index', compact('suppliers'));
    }

    public function transaksi(Request $request)
    {
        $query = Transaksi::with([
            'barang.produk', 
            'barang.pendukung', 
            'supplier', 
            'pemasukan', 
            'pengeluaran'
        ]);

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->whereHas('barang', function ($q1) use ($search) {
                    $q1->where('nama_barang', 'like', '%' . $search . '%');
                })->orWhereHas('supplier', function ($q2) use ($search) {
                    $q2->where('nama', 'like', '%' . $search . '%');
                })->orWhereHas('barang.produk', function ($q3) use ($search) {
                    $q3->where('kode', 'like', '%' . $search . '%');
                })->orWhereHas('barang.pendukung', function ($q4) use ($search) {
                    $q4->where('kode', 'like', '%' . $search . '%');
                })->orWhereHas('pemasukan', function ($q5) use ($search) {
                    $q5->where('kode', 'like', '%' . $search . '%');
                })->orWhereHas('pengeluaran', function ($q6) use ($search) {
                    $q6->where('kode', 'like', '%' . $search . '%');
                });
            });
        }

        // Filter tanggal jika ada input
        $tanggalMulai = null;
        $tanggalAkhir = null;
        if ($request->filled('daterange')) {
            [$tanggalMulai, $tanggalAkhir] = explode(' - ', $request->daterange);

            // Ubah ke format YYYY-MM-DD
            $tanggalMulai = Carbon::createFromFormat('d-m-Y', trim($tanggalMulai))->format('Y-m-d');
            $tanggalAkhir = Carbon::createFromFormat('d-m-Y', trim($tanggalAkhir))->format('Y-m-d');

            $query->whereBetween('waktu_transaksi', [
                $tanggalMulai . ' 00:00:00',
                $tanggalAkhir . ' 23:59:59'
            ]);
        }

        $transaksis = $query->orderBy('waktu_transaksi')->get();

        $totalSebelumnya = 0;

        $data = $transaksis->map(function ($trx) use (&$totalSebelumnya) {
            $kodeBarang = $trx->barang->produk->kode ?? $trx->barang->pendukung->kode ?? '-';
            $masuk = $trx->pemasukan_id ? $trx->jumlahRp : 0;
            $keluar = $trx->pengeluaran_id ? $trx->jumlahRp : 0;

            $totalSekarang = $totalSebelumnya + $masuk - $keluar;
            $totalSebelumnya = $totalSekarang;

            return [
                'waktu' => $trx->waktu_transaksi,
                'kode_transaksi' => $trx->pemasukan->kode ?? $trx->pengeluaran->kode ?? '-',
                'kode_barang' => $kodeBarang,
                'supplier' => $trx->supplier->nama ?? '-',
                'nama_barang' => $trx->barang->nama_barang ?? '-',
                'qty' => $trx->qtyHistori ?? 0,
                'masuk' => $masuk,
                'keluar' => $keluar,
                'total' => $totalSekarang,
            ];
        })->values();

        if ($request->has('export')) {
            if ($request->export === 'excel') {
                return Excel::download(new LaporanTransaksiExport(
                    $tanggalMulai,
                    $tanggalAkhir,
                    $request->q
                ), 'laporan_transaksi.xlsx');
            }

            if ($request->export === 'pdf') {
                $daterange = $request->daterange ?? '-';
                $pdf = PDF::loadView('pimpinan.laporan_transaksi.pdf', compact('data', 'daterange'))
                        ->setPaper('A4', 'landscape');
                return $pdf->download('laporan_transaksi.pdf');
            }
        }
        
        if ($request->ajax()) {
            return view('pimpinan.laporan_transaksi._table', compact('data'))->render();
        }

        return view('pimpinan.laporan_transaksi.index', compact('data', 'tanggalMulai', 'tanggalAkhir'));
    }

}
