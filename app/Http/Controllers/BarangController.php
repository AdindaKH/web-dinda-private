<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangProduk;
use App\Models\BarangPendukung;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    // Menampilkan daftar barang
    public function index(Request $request)
    {
        $filter = $request->query('filter');
        $nama = $request->query('nama');

        $barangs = Barang::with(['produk', 'pendukung'])->get();

        $barangSudahAda = session('barangSudahAda', false);
        $namaBarang = session('nama_barang', '');

        // Filter hanya jika $filter ada dan valid
        if (in_array($filter, ['produk', 'pendukung'])) {
            $barangs = $barangs->filter(function ($barang) use ($filter) {
                return $barang->{$filter}; // hanya tampilkan yang memiliki relasi produk/pendukung
            });
            $newKode = $this->generateKodeBaru($filter);
        } else {
            // Jika tidak ada filter atau tidak valid, tampilkan semua data dan jangan generate kode
            $newKode = null;
        }

        // Filter berdasarkan nama (jika ada)
        if ($nama) {
            $barangs = $barangs->filter(function ($barang) use ($nama) {
                return stripos($barang->nama_barang, $nama) !== false;
            });
        }

        // Urutkan: produk (kode PRD) lebih dulu, lalu pendukung (kode PND)
         $barangs = $barangs->sortBy(function ($barang) {
            return $barang->produk ? '1' . $barang->produk->kode
                : ($barang->pendukung ? '2' . $barang->pendukung->kode : '9');
        })->values();

        return view('operator.barang.index', 
            compact('barangs', 'filter', 'newKode', 'barangSudahAda', 'namaBarang')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'filter' => 'required|in:produk,pendukung',
            'nama_barang' => 'required|string|max:255',
            'qty' => 'required|integer|min:0',
            'harga' => 'required|numeric|min:0',
            'exp' => $request->filter === 'produk' ? 'required|date|after:today' : 'nullable|date',
        ]);

        // Cari apakah barang dengan nama dan kategori yang sama sudah ada
        $existingBarang = Barang::whereRaw('LOWER(nama_barang) = ?', [strtolower($request->nama_barang)]) // case-insensitive search
            ->whereHas($request->filter) // produk atau pendukung
            ->first();

        $barangSudahAda = $existingBarang !== null;

        if ($existingBarang) {
            // Update qty dan harga terakhir
            $existingBarang->qty += $request->qty;
            $existingBarang->harga = $request->harga;
            $existingBarang->exp = $request->filter === 'produk' ? $request->exp : null;
            $existingBarang->save();

            return redirect()->route('barang.index', [
                    'filter' => $request->filter, 
                ])->with('warning', 'Barang sudah ada, data akan digabungkan!') // flash session
                ->with('barangSudahAda', true)
                ->with('nama_barang', $request->nama_barang);
        }
        
        $barang = Barang::create([
            'nama_barang' => $request->nama_barang,
            'qty' => $request->qty,
            'harga' => $request->harga,
            'exp' => $request->exp,
        ]);

        // Generate kode baru untuk produk atau pendukung
        $kodeBaru = $this->generateKodeBaru($request->filter);
        
        // Tambahkan sebagai produk dengan kode auto
        if ($request->filter === 'produk') {
            BarangProduk::create([
                'barang_id' => $barang->id,
                'kode' => $kodeBaru,
            ]);
        } else {
            BarangPendukung::create([
                'barang_id' => $barang->id,
                'kode' => $kodeBaru,
            ]);
        }

        return redirect()->route('barang.index', ['filter' => $request->filter])
            ->with('success', 'Barang ' . $request->filter . ' berhasil ditambahkan');

    }
    
    public function update(Request $request, $id)
    {
        $barang = Barang::with(['produk', 'pendukung'])->findOrFail($id);

        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kategori' => 'required|in:produk,pendukung',
            'exp' => 'nullable|date',
            'harga' => 'required|numeric|min:0',
        ]);

        $kategori_lama = $barang->produk ? 'produk' : 'pendukung';
        $kategori_baru = $request->kategori;

        // Cek apakah melibatkan 'produk'
        $melibatkanProduk = $kategori_lama === 'produk' || $kategori_baru === 'produk';

        // Jika kategori berubah dan melibatkan produk, tapi tidak ada konfirmasi, maka tolak
        if ($kategori_lama !== $kategori_baru && $melibatkanProduk && !$request->has('confirm_produk')) {
            return back()->with('error', 'Perpindahan kategori ke/dari Produk perlu konfirmasi.');
        }

        // Update barang utama
        $barang->nama_barang = $request->nama_barang;
        $barang->exp = $request->exp;
        $barang->harga = $request->harga;
        $barang->save();

        // Hapus relasi lama
        if ($kategori_lama === 'pendukung' && $barang->pendukung) {
            $barang->pendukung->delete();
        } elseif ($kategori_lama === 'produk' && $barang->produk) {
            $barang->produk->delete();
        }

        // Tambah relasi baru sesuai kategori
        $kodeBaru = $this->generateKodeBaru($kategori_baru);

        if ($kategori_baru === 'produk') {
            BarangProduk::create([
                'barang_id' => $barang->id,
                'kode' => $kodeBaru,
            ]);
        } else {
            BarangPendukung::create([
                'barang_id' => $barang->id,
                'kode' => $kodeBaru,
            ]);
        }

        return redirect()->route('barang.index', ['filter' => $kategori_baru])
                        ->with('success', 'Barang berhasil diperbarui.');
    }

    public function updateQty(Request $request, $id)
    {
        $request->validate([
            'qty' => 'required|integer|min:0',
        ]);

        $barang = Barang::findOrFail($id);
        $barang->qty = $request->qty;
        $barang->save();

        return back()->with('success', 'Stok barang berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $barang = Barang::with(['produk', 'pendukung'])->findOrFail($id);

        if ($barang->produk) {
            $barang->produk->delete();
        } elseif ($barang->pendukung) {
            $barang->pendukung->delete();
        }

        $barang->delete();

        return back()->with('success', 'Barang berhasil dihapus.');
    }

    public function stokPimpinan()
    {
        $barang = Barang::with(['produk', 'pendukung'])->get();
        return view('pimpinan.stock_barang.index', compact('barang'));
    }

    public function check(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string',
            'filter' => 'required|in:produk,pendukung',
        ]);

        $barang = Barang::whereRaw('LOWER(nama_barang) = ?', [strtolower($request->nama_barang)])
            ->whereHas($request->filter) // produk atau pendukung
            ->with($request->filter)
            ->first();

        if ($barang) {
            return response()->json([
                'barang_sudah_ada' => true,
                'nama_barang' => $barang->nama_barang,
                'kode' => $barang->{$request->filter}->kode ?? null,
            ]);
        } else {
            $kodeBaru = $this->generateKodeBaru($request->filter);
            return response()->json([
                'barang_sudah_ada' => false,
                'nama_barang' => $request->nama_barang,
                'kode' => $kodeBaru,
            ]);
        }
    }

    private function generateKodeBaru($kategori)
    {
        switch ($kategori) {
            case 'produk':
                $prefix = 'PRD';
                $model = BarangProduk::class;
                break;
            case 'pendukung':
                $prefix = 'PDN';
                $model = BarangPendukung::class;
                break;
            default:
                throw new \Exception("Kategori tidak valid");
        }

        $lastKode = $model::orderBy('id', 'desc')->first();
        $lastNumber = $lastKode ? (int)substr($lastKode->kode, 3) : 0;
        return $prefix . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    }
}
