@extends('layouts.app_operator')

@section('title', 'Dashboard Operator')

@section('content')
<div class="container mt-4">
    <div class="title-box">
        <h3 class="fw-bold m-0">Dashboard</h3>
    </div>

    <!-- Box Pendapatan -->
    <div class="dashboard-container" style="height: 100%;">
    <div class="info-box flex justify-end mb-6">
        <div class="bg-yellow-100 p-4 rounded shadow text-right w-64">
            <h2 class="font-semibold">Jumlah Total Barang</h2>
            <p class="text-lg font-bold"> {{ $jumlahBarang }} </p>
        </div>

        <div class="flex justify-end mb-4">
            <form action="{{ route('dashboard.uang-makan') }}" method="POST" onsubmit="return confirm('Tambah transaksi uang makan harian hari ini?')">
                @csrf
                <button type="submit" class="btn btn-success">Tambah Uang Makan Harian</button>
            </form>
        </div>
    </div>

    <!-- Aktivitas Terbaru -->
    <div class="subtitle-box bg-yellow-100 p-6 rounded shadow">
        {{-- <h2 class="font-bold mb-4">Aktivitas Terbaru</h2> --}}

        <div class="info-row">
            <div class="box-custom">
                <h3>Barang</h3>
                <table class="w-full text-sm bg-white rounded shadow">
                    <thead class="bg-blue-500 text-white">
                        <tr>
                            <th class="p-2 text-left">No</th>
                            <th class="p-2 text-left">Kode</th>
                            <th class="p-2 text-left">Nama Barang</th>
                            <th class="p-2 text-left">Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($barangTerbaru as $i => $brg)
                            @php
                                $kode = '-';
                                if ($brg->pendukung) {
                                    $kode = $brg->pendukung->kode;
                                } elseif ($brg->produk) {
                                    $kode = $brg->produk->kode;
                                }
                            @endphp
                            <tr>
                                <td class="p-2">{{ $i + 1 }}</td>
                                <td class="p-2">{{ $kode }}</td>
                                <td class="p-2">{{ $brg->nama_barang }}</td>
                                <td class="p-2">{{ number_format($brg->qty, 0, ',', '.') }} Kg</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <a href="{{ route('barang.index') }}" class="info-all text-blue-500 mt-2 inline-block">Selengkapnya</a>
            </div>

            <!-- Tabel Transaksi -->
            <div class="box-custom">
                <h3>Aktivitas Transaksi</h3>
                <table class="w-full text-sm bg-white rounded shadow">
                    <thead class="bg-blue-500 text-white">
                        <tr>
                            <th class="p-2 text-left">No</th>
                            <th class="p-2 text-left">Waktu</th>
                            <th class="p-2 text-left">Nama</th>
                            <th class="p-2 text-left">Kategori</th>
                            <th class="p-2 text-left">Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transaksiTerbaru as $i => $trx)
                            <tr>
                                <td class="p-2">{{ $i + 1 }}</td>
                                <td class="p-2">{{ \Carbon\Carbon::parse($trx['waktu'])->format('d-m-Y H:i') }}</td>
                                <td class="p-2">{{ $trx['nama_barang'] }}</td>
                                <td class="p-2">{{ $trx['kategori'] }}</td>
                                <td class="b-pri p-2">{{ $trx['harga'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <a href="{{ route('operator.transaksi.index') }}" class="info-all text-blue-500 mt-2 inline-block">Selengkapnya</a>
            </div>
        </div>

    </div>
</div>
@endsection
