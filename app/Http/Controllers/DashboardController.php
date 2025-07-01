<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Carbon\CarbonPeriod;

use App\Models\Barang;
use App\Models\BarangPendukung;
use App\Models\BarangProduk;
use App\Models\Transaksi;
use App\Models\Pengeluaran;
use App\Models\HistoryGajiKloter;

class DashboardController extends Controller
{
    // Menampilkan dashboard pimpinan.
    public function pimpinan(Request $request)
    {
        [$start, $end] = $this->getDateRange($request);
        $kloters = $this->getAllKloters();
        $kloterData = $this->getKloterChartData($request->get('kloter'), $kloters);
        $grafikBulanan = $this->getMonthlyChartData($start, $end);
        $keuangan = $this->getTotalPendapatanPengeluaran($start, $end);
        $bulanAktif = $start->translatedFormat('d M Y') . ' - ' . $end->translatedFormat('d M Y');
        $listKloter = HistoryGajiKloter::orderBy('id', 'asc')->get();

        return view('dashboard.pimpinan', array_merge(
            $grafikBulanan,
            ['keuangan' => $keuangan],
            ['bulanAktif' => $bulanAktif],
            ['kloters' => $kloters],
            ['listKloter' => $listKloter],
            $kloterData,
            [
                'startDate' => $start->toDateString(),
                'endDate' => $end->toDateString()
            ]
        ));
    }

    // fungsi untuk mendapatkan rentang tanggal awal dan akhir
    protected function getDateRange(Request $request)
    {
        $bulan = $request->get('bulan') ?? now()->month;
        $tahun = $request->get('tahun') ?? now()->year;

        // Ambil transaksi terakhir di bulan dan tahun yang dipilih
        $lastTransactionDate = Transaksi::whereYear('waktu_transaksi', $tahun)
            ->whereMonth('waktu_transaksi', $bulan)
            ->orderBy('waktu_transaksi', 'desc')
            ->value('waktu_transaksi');

        if ($lastTransactionDate) {
            $end = Carbon::parse($lastTransactionDate)->endOfDay();
            $start = $end->copy()->subMonth()->startOfDay();
        } else {
            // Jika tidak ada transaksi, gunakan awal dan akhir bulan
            $start = Carbon::create($tahun, $bulan, 1)->startOfMonth();
            $end = Carbon::create($tahun, $bulan, 1)->endOfMonth();
        }

        return [$start, $end];
    }


    // fungsi untuk mendapatkan semua kloter yang memiliki tanggal awal dan akhir
    protected function getAllKloters()
    {
        return HistoryGajiKloter::whereNotNull('tanggal_awal')
            ->whereNotNull('tanggal_akhir')
            ->orderBy('id', 'desc')
            ->get();
    }

    // fungsi untuk mendapatkan data chart berdasarkan kloter
    protected function getKloterChartData($kloterId, $kloters)
    {
        $labelsKloter = [];
        $pendapatanKloter = [];
        $pengeluaranKloter = [];

        $kloterList = $kloterId 
            ? [$kloters->where('id', $kloterId)->first()] 
            : $kloters;

        foreach ($kloterList as $kloter) {
            if (!$kloter) continue;

            $labelsKloter[] = $kloter->nama ?? 'Kloter ' . $kloter->id;

            $pendapatanKloter[] = Transaksi::whereBetween('waktu_transaksi', [
                    $kloter->tanggal_awal,
                    $kloter->tanggal_akhir
                ])
                ->whereNotNull('pemasukan_id')
                ->sum('jumlahRp');

            $pengeluaranKloter[] = Transaksi::whereBetween('waktu_transaksi', [
                    $kloter->tanggal_awal,
                    $kloter->tanggal_akhir
                ])
                ->where(function ($q) {
                    $q->whereNotNull('pengeluaran_id')
                    ->orWhereNotNull('history_gaji_kloter_id');
                })
                ->sum('jumlahRp');
        }

        return compact('labelsKloter', 'pendapatanKloter', 'pengeluaranKloter');
    }

    // fungsi untuk mendapatkan data chart bulanan
    protected function getMonthlyChartData($start, $end)
    {
        $dates = CarbonPeriod::create($start, $end);
        $labels = [];
        $pendapatanBulanan = [];
        $pengeluaranBulanan = [];

        foreach ($dates as $date) {
            $labels[] = $date->format('d M');

            $pendapatanBulanan[] = Transaksi::whereDate('waktu_transaksi', $date)
                ->whereNotNull('pemasukan_id')
                ->sum('jumlahRp');

            $pengeluaranBulanan[] = Transaksi::whereDate('waktu_transaksi', $date)
                ->where(function ($q) {
                    $q->whereNotNull('pengeluaran_id')
                    ->orWhereNotNull('history_gaji_kloter_id');
                })
                ->sum('jumlahRp');
        }

        return compact('labels', 'pendapatanBulanan', 'pengeluaranBulanan');
    }

    // fungsi untuk mendapatkan total pendapatan dan pengeluaran dalam rentang tanggal
    protected function getTotalPendapatanPengeluaran($start, $end)
    {
        $totalPendapatan = Transaksi::whereBetween('waktu_transaksi', [$start, $end])
            ->whereNotNull('pemasukan_id')
            ->sum('jumlahRp');

        $totalPengeluaran = Transaksi::whereBetween('waktu_transaksi', [$start, $end])
            ->where(function ($q) {
                $q->whereNotNull('pengeluaran_id')
                ->orWhereNotNull('history_gaji_kloter_id');
            })
            ->sum('jumlahRp');

        return [
            'pendapatan' => $totalPendapatan,
            'pengeluaran' => $totalPengeluaran
        ];
    }

    public function ajaxData(Request $request)
    {
        $start = Carbon::parse($request->get('start_date'))->startOfDay();
        $end = Carbon::parse($request->get('end_date'))->endOfDay();

        $kloterId = $request->get('kloter_id');
        $labelsKloter = [];
        $pendapatanKloter = [];
        $pengeluaranKloter = [];

        if ($kloterId) {
            $selectedKloter = HistoryGajiKloter::find($kloterId);
            if ($selectedKloter) {
                $labelsKloter[] = $selectedKloter->nama ?? 'Kloter ' . $selectedKloter->id;

                $pendapatanKloter[] = Transaksi::whereBetween('waktu_transaksi', [
                        $selectedKloter->tanggal_awal,
                        $selectedKloter->tanggal_akhir
                    ])
                    ->whereNotNull('pemasukan_id')
                    ->sum('jumlahRp');

                $pengeluaranKloter[] = Transaksi::whereBetween('waktu_transaksi', [
                        $selectedKloter->tanggal_awal,
                        $selectedKloter->tanggal_akhir
                    ])
                    ->where(function ($q) {
                        $q->whereNotNull('pengeluaran_id')
                        ->orWhereNotNull('history_gaji_kloter_id');
                    })
                    ->sum('jumlahRp');
            }
        }

        $dates = CarbonPeriod::create($start, $end);
        $labels = [];
        $pendapatanBulanan = [];
        $pengeluaranBulanan = [];

        foreach ($dates as $date) {
            $labels[] = $date->format('d M');
            $pendapatanBulanan[] = Transaksi::whereDate('waktu_transaksi', $date)
                ->whereNotNull('pemasukan_id')
                ->sum('jumlahRp');

            $pengeluaranBulanan[] = Transaksi::whereDate('waktu_transaksi', $date)
                ->where(function ($q) {
                    $q->whereNotNull('pengeluaran_id')
                    ->orWhereNotNull('history_gaji_kloter_id');
                })
                ->sum('jumlahRp');
        }

        return response()->json([
            'labels' => $labels,
            'pendapatanBulanan' => $pendapatanBulanan,
            'pengeluaranBulanan' => $pengeluaranBulanan,
            'labelsKloter' => $labelsKloter,
            'pendapatanKloter' => $pendapatanKloter,
            'pengeluaranKloter' => $pengeluaranKloter,
            'keuangan' => $this->getTotalPendapatanPengeluaran($start, $end)
        ]);
    }


    // Menampilkan dashboard operator
    public function operator()
    {
        // Ambil 5 data barang terbaru
        $barangTerbaru = Barang::with(['produk', 'pendukung'])
            ->latest()
            ->take(5)
            ->get();

        // Ambil 5 transaksi terbaru
        $transaksiTerbaru = Transaksi::with(['barang', 'pemasukan', 'pengeluaran',  'historyGajiKloter'])
        ->orderBy('waktu_transaksi', 'desc')
        ->take(5)
        ->get()
        ->map(function ($trx) {
            $kategori = $trx->pengeluaran_id === null ? 'Masuk' : 'Keluar';
            $harga = ($kategori === 'Masuk' ? '+ ' : '- ') . 'Rp ' . number_format($trx->jumlahRp, 0, ',', '.');

            // Tentukan nama transaksi
            $namaTransaksi = $trx->historyGajiKloter
                ? 'Pembayaran Gaji Kloter #' . $trx->historyGajiKloter->id
                : ($trx->barang->nama_barang ?? '-');

            return [
                'waktu' => $trx->waktu_transaksi,
                'nama_barang' => $namaTransaksi,
                'kategori' => $kategori,
                'harga' => $harga,
            ];
        });

        // Jumlah total barang
        $jBarang = Barang::sum('qty');
        $jumlahBarang = number_format($jBarang, 0, ',', '.') . ' kg';

        return view('dashboard.operator', compact('barangTerbaru', 'transaksiTerbaru', 'jumlahBarang'));
    }

    public function tambahUangMakanHarian()
    {
        try {
            DB::beginTransaction();

            // Ambil barang uang makan
            $barang = Barang::where('nama_barang', 'like', '%uang makan%')->first();

            if (!$barang) {
                return back()->with('error', 'Barang uang makan tidak ditemukan.');
            }

            // Cek apakah stok mencukupi
            if ($barang->qty < 1) {
                return back()->with('error', 'Stok uang makan tidak mencukupi.');
            }

            // Kurangi stok
            $barang->qty -= 1;
            $barang->save();

            $pengeluaran = Pengeluaran::create([
                'kode' => 'KLR' . str_pad(Pengeluaran::max('id') + 1, 3, '0', STR_PAD_LEFT),
            ]);

            Transaksi::create([
                'barang_id' => $barang->id,
                'pengeluaran_id' => $pengeluaran->id,
                'qtyHistori' => 1,
                'satuan' => 'paket', // satuan statis
                'jumlahRp' => $barang->harga,
                'waktu_transaksi' => now(),
                'supplier_id' => null,
            ]);

            DB::commit();

            return back()->with('success', 'Transaksi uang makan berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan uang makan: ' . $e->getMessage());
        }
    }

    
}
