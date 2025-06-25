<?php
// app/Http/Controllers/PresensiController.php

namespace App\Http\Controllers;

use App\Models\Kloter;
use App\Models\Karyawan;
use App\Models\Presensi;
use App\Models\TonIkan;
use App\Models\KloterKaryawan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PresensiController extends Controller
{
    public function index(Request $request, $kloter_id = null)
    {
        $tanggal = now()->toDateString();
        $kloters = Kloter::orderBy('id', 'desc')->get();

        // Tombol buat kloter baru
        if ($request->get('buat_kloter')) {
            $selectedKloter = Kloter::create([
                'nama_kloter' => 'Kloter-' . (Kloter::count() + 1)
            ]);
            return redirect()->route('presensi.index', ['kloter_id' => $selectedKloter->id]);
        }

        // Tombol pilih kloter
        $selectedKloter = $kloter_id
            ? Kloter::with('tonIkan')->find($kloter_id)
            : Kloter::latest()->first();

        if (!$selectedKloter) {
            $selectedKloter = Kloter::create([
                'nama_kloter' => 'Kloter-' . (Kloter::count() + 1)
            ]);
        }

        $semuaKaryawan = Karyawan::all();

        $karyawanIds = KloterKaryawan::where('kloter_id', $selectedKloter->id)
            ->pluck('karyawan_id');
        
        $karyawans = Karyawan::whereIn('id', $karyawanIds)->get();

        $presensis = Presensi::where('kloter_id', $selectedKloter->id)
            ->whereDate('tanggal', $tanggal)
            ->get()
            ->keyBy('karyawan_id');

        $jumlahTonHariIni = TonIkan::where('kloter_id', $selectedKloter->id)
            ->value('jumlah_ton') ?? 0;

        $hargaIkanPerTon = TonIkan::where('kloter_id', $selectedKloter->id)
            ->value('harga_ikan_per_ton') ?? 1000000;

        return view('operator.presensi.index', 
            compact('karyawans', 'presensis', 'tanggal', 'kloters', 'selectedKloter', 'jumlahTonHariIni', 'hargaIkanPerTon', 'semuaKaryawan')
        );
    }

    public function inputMasuk(Request $request, $id)
    {
        $kloterId = $request->input('kloter_id');
        $tanggal = now()->toDateString();
        $jamInput = $request->input('jam') ?? Carbon::now()->format('H:i:s'); // <- ambil jam dari request jika ada

        // Validasi: jam tidak boleh lebih dari waktu sekarang
        $sekarang = now()->format('H:i:s');
        if ($jamInput > $sekarang) {
            return redirect()->back()->with('error', 'Jam masuk tidak boleh lebih dari waktu saat ini.');
        }

        $presensi = Presensi::where('karyawan_id', $id)
            ->whereDate('tanggal', $tanggal)
            ->first();

        if (!$presensi) {
            $presensi = Presensi::create([
                'karyawan_id' => $id,
                'kloter_id' => $kloterId,
                'tanggal' => $tanggal,
                'jam_masuk' => $jamInput
            ]);
        } elseif (!$presensi->jam_masuk) {
            $presensi->jam_masuk = $jamInput;
            $presensi->kloter_id = $kloterId;
            $presensi->save();
        }

        return redirect()->back()->with('success', 'Jam masuk berhasil disimpan.');
    }

    public function inputPulang(Request $request, $id)
    {
        $kloterId = $request->input('kloter_id');
        $tanggal = now()->toDateString();
        $jamInput = $request->input('jam') ?? Carbon::now()->format('H:i:s');

        // Validasi: jam tidak boleh lebih dari waktu sekarang
        $sekarang = now()->format('H:i:s');
        if ($jamInput > $sekarang) {
            return redirect()->back()->with('error', 'Jam pulang tidak boleh lebih dari waktu saat ini.');
        }

        $presensi = Presensi::where('karyawan_id', $id)
            ->whereDate('tanggal', $tanggal)
            ->first();

        if ($presensi && !$presensi->jam_pulang) {
            $presensi->jam_pulang = $jamInput;
            $presensi->kloter_id = $kloterId;
            $presensi->save();
        }

        return redirect()->back()->with('success', 'Jam pulang berhasil disimpan.');
    }

    public function simpanTonIkan(Request $request)
    {
        $request->validate([
            'kloter_id' => 'required|exists:kloters,id',
            'jumlah_ton' => 'required|numeric',
            'harga_ikan_per_ton' => 'required|numeric',
        ]);

        TonIkan::updateOrCreate(
            ['kloter_id' => $request->kloter_id/*, 'tanggal' => $request->tanggal*/],
            [
                'jumlah_ton' => $request->jumlah_ton ?? 0,
                'harga_ikan_per_ton' => $request->harga_ikan_per_ton
            ],
        );

        return redirect()->back()->with('success', 'Data ton ikan berhasil disimpan.');
    }

    public function pilihKaryawan(Request $request)
    {
        $request->validate([
            'kloter_id' => 'required|exists:kloters,id',
            'karyawan_ids' => 'array'
        ]);

        // Hapus dulu data lama
        KloterKaryawan::where('kloter_id', $request->kloter_id)->delete();

        // Simpan baru
        foreach ($request->karyawan_ids ?? [] as $id) {
            KloterKaryawan::create([
                'kloter_id' => $request->kloter_id,
                'karyawan_id' => $id
            ]);
        }

        return redirect()->route('presensi.index', ['kloter_id' => $request->kloter_id])
            ->with('success', 'Karyawan berhasil dipilih untuk kloter ini.');
    }

    public function updateJamMasukAjax(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:presensis,id',
            'jam_masuk' => 'required|date_format:H:i',
        ]);

        $presensi = Presensi::find($request->id);
        $presensi->jam_masuk = $request->jam_masuk;
        $presensi->save();

        return response()->json(['message' => 'Berhasil disimpan']);
    }

    public function updateJamPulangAjax(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:presensis,id',
            'jam_pulang' => 'required|date_format:H:i',
        ]);

        $presensi = Presensi::find($request->id);
        $presensi->jam_pulang = $request->jam_pulang;
        $presensi->save();

        return response()->json(['message' => 'Berhasil disimpan']);
    }

}
