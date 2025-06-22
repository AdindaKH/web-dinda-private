@extends('layouts.app_pimpinan')

@section('title', 'Laporan Keuangan')

@section('content')
<div class="container">
    <h1 class="mb-4">Laporan Transaksi</h1>

    <form id="filterForm" method="GET" class="mb-4 flex items-center gap-2" style="text-align-last: end">
        <input type="text" name="q" id="q" placeholder="Cari kode, nama barang, atau Mitra..." value="{{ request('q') }}" class="border px-2 py-1 rounded" style="text-align-last: start;">

        <input type="text" name="daterange" id="daterange" placeholder="Pilih rentang tanggal" value="{{ request('daterange') }}" class="border px-2 py-1 rounded">

        {{-- <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="border px-2 py-1 rounded">
        <span>s/d</span>
        <input type="date" name="tanggal_akhir" id="tanggal_akhir" value="{{ request('tanggal_akhir') }}" class="border px-2 py-1 rounded"> --}}

        <button type="submit" name="export" value="excel" class="btn btn-success" id="downloadExcel">
            Unduh Excel
        </button>
        <button type="submit" name="export" value="pdf" class="btn btn-danger" id="downloadPDF">
            Unduh PDF
        </button>
    </form>

    <div class="table-responsive" id="tabelTransaksi">
        @include('pimpinan.laporan_transaksi._table', ['data' => $data])
    </div>
</div>

<script src="{{ asset('js/laporanTransaksi.js') }}"></script>

@endsection
