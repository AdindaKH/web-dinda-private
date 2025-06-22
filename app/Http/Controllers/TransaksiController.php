<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\Transaksi;
use App\Models\Barang;
use App\Models\BarangProduk;
use App\Models\BarangPendukung;
use App\Models\Supplier;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;

use App\Exports\LaporanTransaksiExport;
use Maatwebsite\Excel\Facades\Excel;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaksi::with([
            'barang.produk', 
            'barang.pendukung', 
            'supplier', 
            'pemasukan', 
            'pengeluaran',
            'historyGajiKloter',
        ]);

        // filter tanggal jika diisi
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('waktu_transaksi', [
                $request->tanggal_mulai . ' 00:00:00',
                $request->tanggal_akhir . ' 23:59:59'
            ]);
        // lanjutkan kebawah nya jika ingin menganti default dengan bulan ini
        // } else {
        //     // Default: bulan ini
        //     $query->whereBetween('waktu_transaksi', [
        //         now()->startOfMonth(),
        //         now()->endOfMonth()
        //     ]);
        }

        $transaksis = $query->orderBy('waktu_transaksi')->get(); // Di urut dari terlama ke terbaru
        // $transaksis = $query->latest('waktu_transaksi')->get();  // Di urut dari terbaru ke terlama

        $totalSebelumnya = 0;

        $data = $transaksis->map(function ($trx) use (&$totalSebelumnya) {
            $kodeBarang = $trx->barang->produk->kode ?? $trx->barang->pendukung->kode ?? '-';
            $masuk = $trx->pemasukan_id ? $trx->jumlahRp : 0;
            $keluar = $trx->pengeluaran_id ? $trx->jumlahRp : 0;

            $totalSekarang = $totalSebelumnya + $masuk - $keluar;
            $totalSebelumnya = $totalSekarang;

            // Jika transaksi berasal dari history gaji kloter
            $namaTransaksi = $trx->historyGajiKloter
                ? 'Pembayaran Gaji Kloter #' . $trx->historyGajiKloter->id
                : ($trx->barang->nama_barang ?? '-');

            return [
                'id' => $trx->id,
                'barang_id' => $trx->barang_id,
                // 'transaksi_id' => $trx->id,
                'supplier_id' => $trx->supplier_id,
                'pemasukan_id' => $trx->pemasukan_id,
                'pengeluaran_id' => $trx->pengeluaran_id,
                'kategori' => $trx->kategori,
                'waktu' => $trx->waktu_transaksi,
                'kode_transaksi' => $trx->pemasukan->kode ?? $trx->pengeluaran->kode ?? '-',
                'kode_barang' => $kodeBarang,
                'supplier' => $trx->supplier->nama ?? '-',
                'barangs' => $namaTransaksi,
                'nama_barang' => $namaTransaksi,
                'qty' => $trx->qtyHistori ?? 0,     // akan mengambil dari qtyHistori pada tb transaksi
                'masuk' => $masuk,
                'keluar' => $keluar,
                'total' => $totalSekarang,
                'jumlahRp' => $trx->jumlahRp,
                'satuan' => $trx->satuan,
                'waktu_transaksi' => $trx->waktu_transaksi,
            ];
        })->reverse()->values(); //akan membalik urutan perhitungan total data transaksi

        // kategori dan tipe barang
        $kategori = $request->input('kategori', 'pengeluaran'); // default ke pemasukan
        $tipe = $kategori === 'pengeluaran' ? 'pendukung' : 'produk'; // otomatis
        // $barangs = Barang::with($tipe)->whereHas($tipe)->get(); // ambil semua barang sesuai tipe
        $barangs = Barang::with(['produk', 'pendukung'])->get()->map(function ($b) {
            // Tentukan tipe berdasarkan relasi yang ada
            if ($b->produk) {
                $b->tipe = 'produk';
            } elseif ($b->pendukung) {
                $b->tipe = 'pendukung';
            } else {
                $b->tipe = null;
            }
            return $b;
        });
        $suppliers = Supplier::with(['pemasok', 'konsumen'])->get(); // ambil semua supplier
        $pemasoks = Supplier::whereHas('pemasok')->get();
        $konsumens = Supplier::whereHas('konsumen')->get();

        $kardusList = Barang::with('kardus')->get()->flatMap(function ($barang) {
            return $barang->kardus;
        });


        // dd($kategori, $tipe, $barangs->pluck('nama_barang')); // debug untuk melihat kategori, tipe, dan nama barang

        return view('operator.transaksi.index', [
            'data' => $data,
            'barangs' => $barangs, 
            'suppliers' => $suppliers, 
            'pemasoks' => $pemasoks,
            'konsumens' => $konsumens,
            'kardusList' => $kardusList,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori' => 'required|in:pemasukan,pengeluaran',
            'barang_id' => 'required|exists:barangs,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'qtyHistori' => 'required|numeric|min:1',
            'jumlahRp' => 'nullable|numeric|min:0',
            'satuan' => 'required|in:ton,kg,g,liter,paket',
            'jenis_kardus' => 'nullable|exists:barangs,id',
            'jumlah_kardus' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $barang = Barang::findOrFail($request->barang_id);
            $kategori = $request->kategori;
            $qtyHistori = $request->qtyHistori;
            $jumlahRp = $request->jumlahRp;
            $satuan = $request->satuan;
            $waktu = Carbon::now();

            // Konversi satuan ke KG
            $qty_kg = match($satuan) {
                'ton' => $qtyHistori * 1000,
                'kg' => $qtyHistori,
                'g' => $qtyHistori / 1000,
                default => $qtyHistori // liter dan paket tidak dikonversi
            };

            $hargaKardus = 0;
            if ($request->filled('jenis_kardus') && $request->jumlah_kardus > 0) {
                $kardus = Barang::find($request->jenis_kardus);
                if ($kardus) {
                    $hargaKardus = $kardus->harga * $request->jumlah_kardus;

                    // Kurangi stok kardus
                    $kardus->qty -= $request->jumlah_kardus;
                    $kardus->save();
                }
            }

            // Variabel untuk ID
            $pemasukan_id = null;
            $pengeluaran_id = null;

            if ($kategori === 'pemasukan') {
                // Buat kode pemasukan
                $last = Pemasukan::latest('id')->first();
                $nextNumber = $last ? $last->id + 1 : 1;
                $kode = 'MSK' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
                $pemasukan = Pemasukan::create(['kode' => $kode]);
                $pemasukan_id = $pemasukan->id;

                // Barang dikurangi
                $barang->qty -= $qty_kg;
                $barang->save();

                // Cek stok cukup
                if ($barang->qty < $qty_kg) {
                    return back()->withInput()->withErrors(['qtyHistori' => 'Stok barang tidak mencukupi.']);
                }

                // Hitung jumlahRp jika kosong
                $jumlahRp = $jumlahRp ?: ($barang->harga * $qty_kg);
                $jumlahRp += $hargaKardus;

            } elseif ($kategori === 'pengeluaran') {
                // Buat kode pengeluaran
                $last = Pengeluaran::latest('id')->first();
                $nextNumber = $last ? $last->id + 1 : 1;
                $kode = 'KLR' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
                $pengeluaran = Pengeluaran::create(['kode' => $kode]);
                $pengeluaran_id = $pengeluaran->id;

                // Barang dikurangi
                $barang->qty += $qty_kg;
                $barang->save();

                // if ($jumlahRp && $qty_kg > 0) {
                //     $barang->harga = $jumlahRp / $qty_kg;
                //     $barang->save();
                // }

                if ($jumlahRp && $qty_kg > 0) {
                    $hargaBarangSaja = $jumlahRp - $hargaKardus;

                    if ($hargaBarangSaja > 0) {
                        $barang->harga = $hargaBarangSaja / $qty_kg;
                        $barang->save();
                    }
                }
            }

            // Simpan transaksi
            Transaksi::create([
                'kategori' => $kategori,
                'barang_id' => $barang->id,
                'supplier_id' => $request->supplier_id,
                'pemasukan_id' => $pemasukan_id,
                'pengeluaran_id' => $pengeluaran_id,
                'qtyHistori' => $qty_kg,
                'jumlahRp' => $jumlahRp,
                'satuan' => $satuan,
                'waktu_transaksi' => $waktu,
                'keterangan' => json_encode([
                    'jenis_kardus_id' => $request->jenis_kardus,
                    'jumlah_kardus' => $request->jumlah_kardus,
                ]),
            ]);

            DB::commit();
            return redirect()->route('operator.transaksi.index')->with('success', 'Transaksi berhasil disimpan.');
            
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Gagal menyimpan transaksi: ' . $th->getMessage()]);
        }
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'kategori' => 'required|in:pemasukan,pengeluaran',
            'barang_id' => 'required|exists:barangs,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'qtyHistori' => 'required|numeric|min:1',
            'jumlahRp' => 'nullable|numeric|min:0',
            'waktu_transaksi' => 'nullable|date',
            'satuan' => 'required|in:ton,kg,g,liter,paket',
            'jenis_kardus' => 'nullable|exists:barangs,id',
            'jumlah_kardus' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $transaksi = Transaksi::findOrFail($id);
            $barang = Barang::findOrFail($request->barang_id);

            // Kembalikan stok barang sebelumnya
            if ($transaksi->kategori === 'pengeluaran') {
                $barang->qty -= $transaksi->qtyHistori;
            } else {
                $barang->qty += $transaksi->qtyHistori;
            }

            // Ambil data kardus lama dari keterangan dan kembalikan stok
            $keteranganLama = json_decode($transaksi->keterangan, true) ?? [];
            $jenisKardusLamaId = $keteranganLama['jenis_kardus_id'] ?? null;
            $jumlahKardusLama = $keteranganLama['jumlah_kardus'] ?? 0;

            if ($jenisKardusLamaId && $jumlahKardusLama > 0) {
                Barang::where('id', $jenisKardusLamaId)->increment('qty', $jumlahKardusLama);
            }

            // Hitung qty dalam kg
            $qty = $request->qtyHistori;
            $satuan = $request->satuan;
            $qty_kg = match($satuan) {
                'ton' => $qty * 1000,
                'kg' => $qty,
                'g' => $qty / 1000,
                default => $qty,
            };

            // Hitung harga kardus baru
            $hargaKardus = 0;
            if ($request->filled('jenis_kardus') && $request->jumlah_kardus > 0) {
                $kardusBaru = Barang::find($request->jenis_kardus);
                if ($kardusBaru) {
                    $hargaKardus = $kardusBaru->harga * $request->jumlah_kardus;
                    $kardusBaru->qty -= $request->jumlah_kardus;
                    $kardusBaru->save();
                }
            }

            // Hitung ulang stok & harga barang
            if ($request->kategori === 'pengeluaran') {
                $barang->qty += $qty_kg;
            } else {
                if ($barang->qty < $qty_kg) {
                    return back()->withErrors(['qtyHistori' => 'Stok barang tidak mencukupi untuk pemasukan.']);
                }
                $barang->qty -= $qty_kg;

                // Hitung harga barang (tidak termasuk kardus)
                if ($request->jumlahRp && $qty_kg > 0) {
                    $hargaBarangSaja = $request->jumlahRp - $hargaKardus;
                    if ($hargaBarangSaja > 0) {
                        $barang->harga = $hargaBarangSaja / $qty_kg;
                    }
                }
            }

            $barang->save();

            // Hitung total jumlah Rp termasuk kardus
            $jumlahRp = $request->jumlahRp ?: ($barang->harga * $qty_kg);
            $jumlahRp += $hargaKardus;

            // Simpan perubahan transaksi
            $transaksi->update([
                'barang_id' => $barang->id,
                'supplier_id' => $request->supplier_id,
                'kategori' => $request->kategori,
                'jumlahRp' => $jumlahRp,
                'qtyHistori' => $qty,
                'satuan' => $satuan,
                'waktu_transaksi' => $request->waktu_transaksi,
                'keterangan' => json_encode([
                    'jenis_kardus_id' => $request->jenis_kardus,
                    'jumlah_kardus' => $request->jumlah_kardus,
                ])
            ]);

            DB::commit();
            return redirect()->route('operator.transaksi.index')->with('success', 'Transaksi berhasil diperbarui.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Gagal memperbarui transaksi: ' . $th->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->delete();

        return redirect()->route('operator.transaksi.index')->with('success', 'Data transaksi berhasil dihapus.');
    }

    public function exportExcel(Request $request)
    {
        $tanggalMulai = $request->tanggal_mulai;
        $tanggalAkhir = $request->tanggal_akhir;
        $q = $request->q; // Optional, untuk pencarian

        $filename = 'laporan_transaksi_operator_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new LaporanTransaksiExport($tanggalMulai, $tanggalAkhir, $q), $filename);
    }

}

