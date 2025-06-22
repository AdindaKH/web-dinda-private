@extends('layouts.app_pimpinan')

@section('title', 'Laporan Karyawan')

@section('content')
<div class="container mt-4">
    <h3>Laporan Barang</h3>

    <div class="row mb-3">
        <div class="col-md-3">
            <select id="filter" class="form-select">
                <option value="">-- Semua Kategori --</option>
                <option value="produk" {{ request('filter') == 'produk' ? 'selected' : '' }}>Produk</option>
                <option value="pendukung" {{ request('filter') == 'pendukung' ? 'selected' : '' }}>Pendukung</option>
            </select>
        </div>
        <div class="col-md-4">
            <input type="text" id="nama" class="form-control" placeholder="Cari nama barang..." value="{{ request('nama') }}">
        </div>
        <div class="col-md-2">
            <button id="downloadExcel" class="btn btn-success w-100">Unduh Excel</button>
        </div>
    </div>

    <div id="dataContainer">
        @include('pimpinan.laporan_barang._table', ['barangs' => $barangs])
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterSelect = document.getElementById('filter');
    const namaInput = document.getElementById('nama');
    const downloadBtn = document.getElementById('downloadExcel');
    const container = document.getElementById('dataContainer');

    async function fetchData() {
        const params = new URLSearchParams({
            filter: filterSelect.value,
            nama: namaInput.value,
            ajax: 1
        });

        try {
            const response = await fetch("{{ route('laporan.barang') }}?" + params.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!response.ok) throw new Error('Gagal memuat data');

            const html = await response.text();
            if (container) container.innerHTML = html;
        } catch (error) {
            alert("Gagal memuat data: " + error.message);
        }
    }

    filterSelect.addEventListener('change', fetchData);

    let timeout = null;
    namaInput.addEventListener('input', function () {
        clearTimeout(timeout);
        timeout = setTimeout(fetchData, 500);
    });

    downloadBtn.addEventListener('click', function (e) {
        e.preventDefault();
        const urlParams = new URLSearchParams({
            filter: filterSelect.value,
            nama: namaInput.value,
            export: 'excel'
        });
        window.location.href = "{{ route('laporan.barang') }}" + '?' + urlParams.toString();
    });
});
</script>


@endsection
