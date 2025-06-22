<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Pemasok;
use App\Models\Konsumen;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->query('keyword');
        $kategori = $request->query('kategori');

        $suppliers = Supplier::with(['pemasok', 'konsumen']);
        
        // filter berdasarkan keyword
        if ($keyword) {
            $suppliers = $suppliers->where(function ($query) use ($keyword) {
                $query->where('nama', 'like', '%' . $keyword . '%')
                    ->orWhere('alamat', 'like', '%' . $keyword . '%');
            });
        }
        
        // Filter berdasarkan kategori mitra
        if ($kategori === 'pemasok') {
            $suppliers = $suppliers->whereHas('pemasok');
        } elseif ($kategori === 'konsumen') {
            $suppliers = $suppliers->whereHas('konsumen');
        }

        $suppliers = $suppliers->get();

        return view('operator.supplier.index', compact('suppliers'));
    }
    
    public function create()
    {
        $pemasoks = Pemasok::all();
        $konsumens = Konsumen::all();
        
        return view('operator.supplier.create', compact('pemasoks', 'konsumens'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori' => 'required|in:pemasok,konsumen',
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'no_tlp' => 'required|string|max:15',
            'no_rekening' => 'nullable|string|max:50',
        ]);

        // 1. Buat supplier dulu
        $supplier = Supplier::create([
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'no_tlp' => $request->no_tlp,
            'no_rekening' => $request->no_rekening,
        ]);

        // 2. Buat entri di pemasoks / konsumens tergantung kategori
        $kode = $this->generateKode($request->kategori);

        if ($request->kategori === 'pemasok') {
            Pemasok::create([
                'kode' => $kode,
                'supplier_id' => $supplier->id,
            ]);
        } else {
            Konsumen::create([
                'kode' => $kode,
                'supplier_id' => $supplier->id,
            ]);
        }

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil ditambahkan.');
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'kategori' => 'required|in:pemasok,konsumen',
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'no_tlp' => 'required|string|max:15',
            'no_rekening' => 'nullable|string|max:50',
        ]);

        $supplier = Supplier::with(['pemasok', 'konsumen'])->findOrFail($id);

        // Update data utama
        $supplier->update([
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'no_tlp' => $request->no_tlp,
            'no_rekening' => $request->no_rekening,
        ]);

        $kategori_lama = $supplier->pemasok ? 'pemasok' : ($supplier->konsumen ? 'konsumen' : null);
        $kategori_baru = $request->kategori;

        // Jika kategori berubah, hapus relasi lama dan buat yang baru
        if ($kategori_lama !== $kategori_baru) {
            if ($kategori_lama === 'pemasok') {
                $supplier->pemasok->delete();
            } elseif ($kategori_lama === 'konsumen') {
                $supplier->konsumen->delete();
            }

            $kodeBaru = $this->generateKode($kategori_baru);

            if ($kategori_baru === 'pemasok') {
                Pemasok::create([
                    'supplier_id' => $supplier->id,
                    'kode' => $kodeBaru,
                ]);
            } elseif ($kategori_baru === 'konsumen') {
                Konsumen::create([
                    'supplier_id' => $supplier->id,
                    'kode' => $kodeBaru,
                ]);
            }
        }

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil diperbarui.');
    }



    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil dihapus.');
    }

    private function generateKode($kategori)
    {
        $prefix = $kategori === 'pemasok' ? 'PMK' : 'KSM';
        $model = $kategori === 'pemasok' ? Pemasok::class : Konsumen::class;

        $last = $model::orderBy('id', 'desc')->first();
        $lastNumber = $last ? (int)substr($last->kode, 3) : 0;

        return $prefix . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    }


}