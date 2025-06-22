<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\TonIkan;

class TonIkanController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jumlah_ton' => 'required|numeric|min:0',
            'harga_ikan_per_ton' => 'required|numeric|min:0',
        ]);

        TonIkan::updateOrCreate(
            ['tanggal' => $request->tanggal],
            ['jumlah_ton' => $request->jumlah_ton],
            ['harga_ikan_per_ton' => $request->harga_ikan_per_ton]
        );

        return redirect()->back()->with('success', 'Jumlah ton ikan berhasil disimpan.');
    }

}
