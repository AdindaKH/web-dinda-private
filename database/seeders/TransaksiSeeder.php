<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

use App\Models\Barang;
use App\Models\Supplier;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Models\Transaksi;

class TransaksiSeeder extends Seeder
{
    public function run(): void
    {
        $barang = Barang::all();
        $supplier = Supplier::all();
        $pemasukan = Pemasukan::all();
        $pengeluaran = Pengeluaran::all();

        $startDate = Carbon::now()->subYear();
        $endDate = Carbon::now();
        
        for ($i = 0; $i < 12; $i++) { // pilih bulan  dari 1 - 12
            $tipe = $i % 2 === 0 ? 'pemasukan' : 'pengeluaran'; // selang-seling
            $tanggal = Carbon::create(2024, 1, 1)
                ->addMonths($i)
                ->day(rand(1, 28)) // Acak tanggal antara 1 sampai 28 agar aman untuk semua bulan
                ->setTime(rand(0, 23), rand(0, 59), rand(0, 59)); // Acak waktu dalam hari

            Transaksi::create([
                'barang_id' => $barang->random()->id,
                'supplier_id' => $supplier->random()->id,
                'pemasukan_id' => $tipe === 'pemasukan' ? $pemasukan->random()->id : null,
                'pengeluaran_id' => $tipe === 'pengeluaran' ? $pengeluaran->random()->id : null,
                'jumlahRp' => rand(300000, 900000),
                'waktu_transaksi' => $tanggal,
            ]);
        }
    }
}
