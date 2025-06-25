@extends('layouts.app_pimpinan')

@section('title', 'Laporan Keuangan')

@section('content')
<div class="container">
    <h1 class="mb-4">Laporan Transaksi</h1>

    <form id="filterForm" method="GET" class="mb-4 flex items-center gap-2" style="text-align-last: end">
        <div style="width: auto; display: flex; flex-wrap: nowrap; justify-content: space-between; align-items: center;">
            <input type="text" name="daterange" id="daterange" placeholder="Pilih rentang tanggal" value="{{ request('daterange') }}" class="border px-2 py-1 rounded">
            
            <div class="col d-flex gap-2 mb-2" style="flex-wrap: nowrap; flex-direction: row; justify-content: flex-end; align-items: center;">
                <input type="text" name="q" id="q" placeholder="Cari kode, nama barang, atau Mitra..." value="{{ request('q') }}" class="border px-2 py-1 rounded" style="text-align-last: start;">

                <div class="col-md-2 d-flex gap-0" style="width: fit-content;">
                    <button type="submit" name="export" value="excel" class="btn btn-success" id="downloadExcel">Unduh Excel</button>
                    <button type="submit" name="export" value="pdf" class="btn btn-danger" id="downloadPDF">Unduh PDF</button>
                </div>
            </div>
        </div>
    </form>

    <div class="table-responsive" id="tabelTransaksi">
        @include('pimpinan.laporan_transaksi._table', ['data' => $data])
    </div>
</div>

<script src="{{ asset('js/laporanTransaksi.js') }}"></script>

@endsection
