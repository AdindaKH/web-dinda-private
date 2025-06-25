@extends('layouts.app_pimpinan')

@section('title', 'Laporan Karyawan')

@section('content')
<div class="container mt-4">
    <h3>Laporan Barang</h3>

    <div style="width: auto; display: flex; flex-wrap: nowrap; justify-content: space-between; align-items: center;">
        <div class="col-md-3 d-flex gap-0">
            <button class="btn btn-outline-primary filter-btn {{ request('filter') == 'produk' ? 'active' : '' }}" data-filter="produk">Produk</button>
            <button class="btn btn-outline-primary filter-btn {{ request('filter') == 'pendukung' ? 'active' : '' }}" data-filter="pendukung">Pendukung</button>
            <input type="hidden" id="filter" value="{{ request('filter') }}">
        </div>

        <div class="col d-flex gap-2 mb-2" style="flex-wrap: nowrap; flex-direction: row; justify-content: flex-end; align-items: center;">
            <div class="col-md-4" style="width: auto">
                <input type="text" id="nama" class="form-control" placeholder="Cari nama barang..." value="{{ request('nama') }}">
            </div>
            <div class="col-md-2 d-flex gap-0" style="width: fit-content;">
                <button id="downloadExcel" class="btn btn-success">Unduh Excel</button>
                <button id="downloadPDF" class="btn btn-danger">Unduh PDF</button>
            </div>
        </div>
    </div>

    <div id="dataContainer">
        @include('pimpinan.laporan_barang._table', ['barangs' => $barangs])
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterSelect = document.getElementById('filter');
    const filterButtons = document.querySelectorAll('.filter-btn');
    const namaInput = document.getElementById('nama');
    const downloadBtn = document.getElementById('downloadExcel');
    const downloadBtnPDF = document.getElementById('downloadPDF');
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

    filterButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const current = document.getElementById('filter').value;
            const selected = this.dataset.filter;

            if (current === selected) {
                // Nonaktifkan jika tombol aktif ditekan lagi
                document.getElementById('filter').value = '';
                filterButtons.forEach(b => b.classList.remove('active'));
            } else {
                document.getElementById('filter').value = selected;
                filterButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            }

            fetchData();
        });
    });

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
    downloadBtnPDF.addEventListener('click', function (e) {
        e.preventDefault();
        const urlParams = new URLSearchParams({
            filter: filterSelect.value,
            nama: namaInput.value,
            export: 'pdf'
        });
        window.location.href = "{{ route('laporan.barang') }}" + '?' + urlParams.toString();
    });
});
</script>


@endsection
