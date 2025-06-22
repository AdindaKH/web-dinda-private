<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $nama = $request->query('nama');
        $gender = $request->query('gender');

        $karyawans = Karyawan::query();

        // Filter nama
        if ($nama) {
            $karyawans->where('nama', 'like', '%' . $nama . '%');
        }

        // Filter jenis kelamin
        if ($gender) {
            $karyawans->where('jenis_kelamin', $gender);
        }

        $karyawans = $karyawans->get();

        return view('operator.karyawan.index', compact('karyawans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'no_telepon' => 'nullable|string|max:20',
        ]);

        Karyawan::create([
            'nama' => $request->nama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'no_telepon' => $request->no_telepon,
        ]);

        return redirect()->route('karyawan.index')->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'no_telepon' => 'nullable|string|max:20',
        ]);

        $karyawan = Karyawan::findOrFail($id);
        $karyawan->update([
            'nama' => $request->nama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'no_telepon' => $request->no_telepon,
        ]);

        return redirect()->route('karyawan.index')->with('success', 'Karyawan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $karyawan->delete();

        return redirect()->route('karyawan.index')->with('success', 'Karyawan berhasil dihapus.');
    }
}
