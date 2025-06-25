@extends('layouts.app_pimpinan')

@section('title', 'Laporan Supplier')

@section('content')
<div class="container mt-4">
    <h4>Laporan Mitra Bisnis</h4>

    <!-- Filter -->
    <div class="mb-3" style="width: auto; display: flex; flex-wrap: nowrap; justify-content: space-between; align-items: center;">
        <div class="col-md-4 d-flex gap-0">
            <button class="btn btn-outline-primary kategori-btn" data-kategori="pemasok">Pemasok</button>
            <button class="btn btn-outline-primary kategori-btn" data-kategori="konsumen">Konsumen</button>
            <input type="hidden" id="kategori" value="">
        </div>
        <div class="col d-flex gap-2 mb-2" style="flex-wrap: nowrap; flex-direction: row; justify-content: flex-end; align-items: center;">
            <div class="col-md-4">
                <input type="text" id="keyword" class="form-control" placeholder="Cari Nama atau Alamat Mitra">
            </div>
            <div class="col-md-2 d-flex gap-0" style="width: fit-content;">
                <button id="unduhExcel" class="btn btn-success">Unduh Excel</button>
                <button id="unduhPDF" class="btn btn-danger">Unduh PDF</button>
            </div>
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
        const kategoriButtons = document.querySelectorAll('.kategori-btn');
        const kategoriInput = document.getElementById('kategori');
        const unduhBtn = document.getElementById('unduhExcel');
        const unduhBtnPDF = document.getElementById('unduhPDF');
        const container = document.getElementById('dataContainer');

        async function fetchData() {
            const params = new URLSearchParams({
                keyword: keywordInput.value,
                kategori: kategoriInput.value,
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

        kategoriButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const currentValue = kategoriInput.value;
                const newValue = this.dataset.kategori;

                if (currentValue === newValue) {
                    kategoriInput.value = '';
                    kategoriButtons.forEach(b => b.classList.remove('active'));
                } else {
                    kategoriInput.value = newValue;
                    kategoriButtons.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                }

                fetchData();
            });
        });

        keywordInput.addEventListener('input', () => {
            clearTimeout(window.keywordTimeout);
            window.keywordTimeout = setTimeout(fetchData, 500);
        });

        unduhBtn.addEventListener('click', function () {
            const params = new URLSearchParams({
                keyword: keywordInput.value,
                kategori: kategoriInput.value,
                export: 'excel'
            });

            window.location.href = "{{ route('laporan.supplier') }}" + '?' + params.toString();
        });
        unduhBtnPDF.addEventListener('click', function () {
            const params = new URLSearchParams({
                keyword: keywordInput.value,
                kategori: kategoriInput.value,
                export: 'pdf'
            });

            window.location.href = "{{ route('laporan.supplier') }}" + '?' + params.toString();
        });
    });


</script>
@endsection
