@extends('layouts.app_pimpinan')

@section('title', 'Laporan Supplier')

@section('content')
<div class="container mt-4">
    <h4>Laporan Mitra Bisnis</h4>

    <!-- Filter -->
    <div class="row mb-3">
        <div class="col-md-4">
            <input type="text" id="keyword" class="form-control" placeholder="Cari Nama atau Alamat Mitra">
        </div>
        <div class="col-md-4">
            <select id="kategori" class="form-select">
                <option value="">Semua Kategori</option>
                <option value="pemasok">Pemasok</option>
                <option value="konsumen">Konsumen</option>
            </select>
        </div>
        <div class="col-md-4">
            <button id="unduhExcel" class="btn btn-success">Unduh Excel</button>
        </div>
    </div>

    <!-- Kontainer Data -->
    <div id="dataContainer">
        @include('pimpinan.laporan_supplier._table', ['suppliers' => $suppliers])
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    const keywordInput = document.getElementById('keyword');
    const kategoriSelect = document.getElementById('kategori');
    const unduhBtn = document.getElementById('unduhExcel');
    const container = document.getElementById('dataContainer');

    async function fetchData() {
        const params = new URLSearchParams({
            keyword: keywordInput.value,
            kategori: kategoriSelect.value,
            ajax: 1
        });

        try {
            const response = await fetch("{{ route('laporan.supplier') }}?" + params.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!response.ok) throw new Error('Gagal memuat data');

            const html = await response.text();
            container.innerHTML = html;
        } catch (err) {
            alert('Error: ' + err.message);
        }
    }

    keywordInput.addEventListener('input', () => {
        clearTimeout(window.keywordTimeout);
        window.keywordTimeout = setTimeout(fetchData, 500);
    });

    kategoriSelect.addEventListener('change', fetchData);

    unduhBtn.addEventListener('click', function () {
        const params = new URLSearchParams({
            keyword: keywordInput.value,
            kategori: kategoriSelect.value,
            export: 'excel'
        });

        window.location.href = "{{ route('laporan.supplier') }}" + '?' + params.toString();
    });
});

</script>
@endsection
