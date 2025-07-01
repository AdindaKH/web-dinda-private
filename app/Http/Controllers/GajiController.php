<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use Carbon\CarbonPeriod;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\Karyawan;
use App\Models\Kloter;
use App\Models\Presensi;
use App\Models\TonIkan;
use App\Models\HistoryGajiKloter;
use App\Models\Transaksi;
use App\Models\Pengeluaran;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GajiKloterExport;

class GajiController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->input('tahun');
        $status = $request->input('status');

        // $klotersQuery = Kloter::with(['presensis', 'tonIkan'])->orderBy('id', 'desc');
        $klotersQuery = Kloter::with(['presensis', 'tonIkan'])->orderByDesc('id');
        $kloterSelesaiIds = HistoryGajiKloter::pluck('kloter_id')->toArray();

        if ($tahun) {
            $klotersQuery->whereHas('presensis', function ($query) use ($tahun) {
                $query->whereYear('tanggal', $tahun);
            });
        }

        $kloters = $klotersQuery->get();

        // Filter manual untuk status
        if ($status === 'selesai') {
            $kloters = $kloters->filter(fn($kloter) =>
                HistoryGajiKloter::where('kloter_id', $kloter->id)->exists()
            );
        } elseif ($status === 'belum') {
            $kloters = $kloters->filter(fn($kloter) =>
                !HistoryGajiKloter::where('kloter_id', $kloter->id)->exists()
            );
        }

        $tahunList = Presensi::selectRaw('YEAR(tanggal) as tahun')->distinct()->pluck('tahun');

        return view('operator.gaji.index', 
            compact('kloters', 'tahun', 'tahunList', 'status', 'kloterSelesaiIds')
        );
    }

    public function detail($id)
    {
        $search = request('search');

        $kloter = Kloter::with(['presensis.karyawan', 'tonIkan'])->findOrFail($id);
        $tanggalUnik = $kloter->presensis->pluck('tanggal')->unique()->sort()->values();
        $dataKaryawan = $kloter->presensis->groupBy('karyawan_id');
        $banyakPekerja = $dataKaryawan->count();

        $jumlahTon = $kloter->tonIkan->jumlah_ton ?? 0;
        $hargaPerTon = $kloter->tonIkan->harga_ikan_per_ton ?? 1000000;
        $gajiPerJam = $banyakPekerja > 0 ? ($jumlahTon * $hargaPerTon) / $banyakPekerja : 0;

        $karyawanWithGaji = [];

        foreach ($dataKaryawan as $karyawanId => $presensis) {
            $karyawan = $presensis->first()->karyawan;
            $jamPerTanggal = [];
            $totalJam = 0;

            foreach ($tanggalUnik as $tanggal) {
                $presensi = $presensis->firstWhere('tanggal', $tanggal);
                $jamKerja = 0;

                if ($presensi && $presensi->jam_masuk && $presensi->jam_pulang) {
                    $jamMasuk = strtotime($presensi->jam_masuk);
                    $jamPulang = strtotime($presensi->jam_pulang);
                    $jamKerja = ($jamPulang - $jamMasuk) / 3600;
                }

                $jamPerTanggal[$tanggal] = $jamKerja;
                $totalJam += $jamKerja;
            }

            $totalGaji = $gajiPerJam * $totalJam;
            if ($karyawan->jenis_kelamin === 'P') {
                $totalGaji *= 0.6; // potongan 40%
            }

            $karyawanWithGaji[] = [
                'karyawan' => $karyawan,
                'jam_per_tanggal' => $jamPerTanggal,
                'total_jam' => $totalJam,
                'gaji_per_jam' => $gajiPerJam,
                'total_gaji' => $totalGaji,
            ];
        }

        if ($search) {
            $karyawanWithGaji = array_filter($karyawanWithGaji, function ($data) use ($search) {
                return stripos($data['karyawan']->nama, $search) !== false;
            });
        }

        return view('operator.gaji.detailGaji', compact(
            'kloter',
            'tanggalUnik',
            'dataKaryawan',
            'gajiPerJam',
            'jumlahTon',
            'hargaPerTon',
            'karyawanWithGaji'
        ))->with('selectedKloter', $kloter);
    }

    public function kloterSelesai($id)
    {
        $kloter = Kloter::with(['presensis.karyawan', 'tonIkan'])->findOrFail($id);

        if (HistoryGajiKloter::where('kloter_id', $kloter->id)->exists()) {
            return redirect()->route('gaji.index')->with('error', 'Kloter ini sudah ditandai selesai sebelumnya.');
        }

        // Ambil data tanggal awal dan akhir dari presensi kloter
        $tanggalUnik = $kloter->presensis->pluck('tanggal')->unique()->sort()->values();
        $tanggalAwal = $tanggalUnik->first();
        $tanggalAkhir = $tanggalUnik->last();

        $dataKaryawan = $kloter->presensis->groupBy('karyawan_id');
        $banyakPekerja = $dataKaryawan->count();

        $jumlahTon = $kloter->tonIkan->jumlah_ton ?? 0;
        $hargaPerTon = $kloter->tonIkan->harga_ikan_per_ton ?? 1000000;
        $gajiPerJam = $banyakPekerja > 0 ? ($jumlahTon * $hargaPerTon) / $banyakPekerja : 0;

        $totalGaji = 0;

        foreach ($dataKaryawan as $presensis) {
            $karyawan = $presensis->first()->karyawan;

            $totalJam = $presensis->sum(function ($presensi) {
                return (strtotime($presensi->jam_pulang) - strtotime($presensi->jam_masuk)) / 3600;
            });

            $gaji = $gajiPerJam * $totalJam;

            if ($karyawan->jenis_kelamin === 'P') {
                $gaji *= 0.6; // potongan 40%
            }

            $totalGaji += $gaji;
        }

        // 1. Simpan ke History Gaji Kloter
         $historyGaji = HistoryGajiKloter::create([
            'kloter_id' => $kloter->id,
            'kode' => 'GK-' . strtoupper(uniqid()),
            'jml_karyawan' => $banyakPekerja,
            'total_gaji' => $totalGaji,
            'waktu' => now(),
            'tanggal_awal' => $tanggalAwal,
            'tanggal_akhir' => $tanggalAkhir,
        ]);

        // 2. Buat entri Pengeluaran jika belum ada (PGJxxx)
        $lastKode = Pengeluaran::where('kode', 'like', 'PGJ%')->orderByDesc('id')->value('kode');
        $nextNumber = $lastKode && preg_match('/PGJ(\d+)/', $lastKode, $m)
            ? str_pad($m[1] + 1, 3, '0', STR_PAD_LEFT)
            : '001';

        $kodePengeluaran = 'PGJ' . $nextNumber;

        $pengeluaran = Pengeluaran::create([
            'kode' => $kodePengeluaran,
            'nama' => 'Gaji Karyawan',
            'keterangan' => 'Pembayaran gaji untuk kloter #' . $kloter->id,
            'tanggal' => now(),
        ]);

        // 3. Simpan ke Transaksi, kaitkan dengan history_gaji_kloter dan pengeluaran
        Transaksi::create([
            'pengeluaran_id' => $pengeluaran->id,
            'history_gaji_kloter_id' => $historyGaji->id,
            'qtyHistori' => 1,
            'satuan' => 'kloter',
            'jumlahRp' => $totalGaji,
            'waktu_transaksi' => now(),
        ]);

        return redirect()->route('gaji.kloter')->with('success', 'Kloter berhasil diselesaikan dan transaksi gaji dicatat.');
    }

    public function export($id)
    {
        $kloter = \App\Models\Kloter::with('tonIkan')->findOrFail($id);

        $presensis = \App\Models\Presensi::with('karyawan')
            ->where('kloter_id', $id)
            ->get();

        $tanggalUnik = $presensis->pluck('tanggal')->unique()->sort()->values();

        $gajiPerJam = 0;
        $jumlahPekerja = $presensis->pluck('karyawan_id')->unique()->count();
        
        $jumlahTon = $kloter->tonIkan->jumlah_ton ?? 0;
        $hargaPerTon = $kloter->tonIkan->harga_ikan_per_ton ?? 1000000;

        if ($jumlahPekerja > 0) {
            $gajiPerJam = ($jumlahTon * $hargaPerTon) / $jumlahPekerja;
        }

        $data = [];

        foreach ($presensis->groupBy('karyawan_id') as $karyawanId => $presensiKaryawan) {
            $karyawan = $presensiKaryawan->first()->karyawan;
            $jamPerTanggal = [];

            foreach ($presensiKaryawan as $p) {
                $key = \Carbon\Carbon::parse($p->tanggal)->format('Y-m-d');

                $jamKerja = 0;
                if ($p->jam_masuk && $p->jam_pulang) {
                    $jamMasuk = strtotime($p->jam_masuk);
                    $jamPulang = strtotime($p->jam_pulang);
                    $jamKerja = ($jamPulang - $jamMasuk) / 3600;
                }

                $jamPerTanggal[$key] = $jamKerja;
            }

            $totalJam = array_sum($jamPerTanggal);

            $gajiPerJamKaryawan = $karyawan->jenis_kelamin == 'Perempuan'
                ? $gajiPerJam * 0.6
                : $gajiPerJam;

            $totalGaji = $gajiPerJamKaryawan * $totalJam;

            $data[] = [
                'karyawan' => $karyawan,
                'jam_per_tanggal' => $jamPerTanggal,
                'total_jam' => $totalJam,
                'gaji_per_jam' => round($gajiPerJamKaryawan),
                'total_gaji' => round($totalGaji),
            ];

            // dd($data, $tanggalUnik);

        }

        return Excel::download(new GajiKloterExport($data, $tanggalUnik), 'detail_gaji_' . $kloter->nama_kloter . '.xlsx');
    }

    public function unduhSlipGaji($id)
    {
        $kloter = Kloter::with(['presensis.karyawan', 'tonIkan'])->findOrFail($id);

        $tanggalUnik = $kloter->presensis
            ->pluck('tanggal')
            ->unique()
            ->sort()
            ->map(fn ($tgl) => Carbon::parse($tgl))
            ->values();

        $dataKaryawan = $kloter->presensis->groupBy('karyawan_id');
        $banyakPekerja = $dataKaryawan->count();

        $jumlahTon = $kloter->tonIkan->jumlah_ton ?? 0;
        $hargaPerTon = $kloter->tonIkan->harga_ikan_per_ton ?? 1000000;

        $gajiPerJamGlobal = $banyakPekerja > 0 ? ($jumlahTon * $hargaPerTon) / $banyakPekerja : 0;

        $slipData = [];

        foreach ($dataKaryawan as $karyawanId => $presensis) {
            $karyawan = $presensis->first()->karyawan;

            $jamPerTanggal = [];
            $totalJam = 0;

            foreach ($tanggalUnik as $tanggal) {
                $presensi  = $presensis->firstWhere('tanggal', $tanggal->toDateString());
                $jamKerja = 0;

                if ($presensi && $presensi->jam_masuk && $presensi->jam_pulang) {
                    $jamMasuk = strtotime($presensi->jam_masuk);
                    $jamPulang = strtotime($presensi->jam_pulang);
                    $jamKerja = ($jamPulang - $jamMasuk) / 3600;
                }

                $jamPerTanggal[$tanggal->format('d-M-Y')] = $jamKerja;
                $totalJam += $jamKerja;
            }

            $gajiPerJam = $karyawan->jenis_kelamin === 'P'
                ? $gajiPerJamGlobal * 0.6
                : $gajiPerJamGlobal;

            $totalGaji = $gajiPerJam * $totalJam;

            $slipData[] = [
                'karyawan' => $karyawan,
                'jam_per_tanggal' => $jamPerTanggal,
                'total_jam' => $totalJam,
                'gaji_per_jam' => round($gajiPerJam),
                'total_gaji' => round($totalGaji),
            ];
        }

        $tanggalCetak = now()->translatedFormat('d F Y');

        $pdf = Pdf::loadView('operator.gaji.slip', [
                    'kloter'        => $kloter,
                    'tanggalCetak'  => $tanggalCetak,
                    'slipData'      => $slipData,
                    // bila template butuh periode:
                    'tanggalMulai'  => Carbon::parse($kloter->tanggal_mulai),
                    'tanggalAkhir'  => Carbon::parse($kloter->tanggal_akhir),
                ])
                ->setPaper('A4', 'portrait');

        // Untuk mengunduh file PDF
        return $pdf->download('slip_gaji_kloter_'.$kloter->id.'.pdf');

        // Jika ingin menampilkan di browser, gunakan:
        // return view('operator.gaji.slip', [
        //     'kloter'        => $kloter,
        //     'tanggalCetak'  => $tanggalCetak,
        //     'slipData'      => $slipData,
        //     // bila template butuh periode:
        //     'tanggalMulai'  => Carbon::parse($kloter->tanggal_mulai),
        //     'tanggalAkhir'  => Carbon::parse($kloter->tanggal_akhir),
        // ]);
    }

}
