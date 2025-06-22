@extends('layouts.app_pimpinan')

@section('title', 'Laporan Karyawan')

@section('content')
<div class="container">
    <h1 class="mb-4">Laporan Gaji Karyawan</h1>

    <form id="filterForm" class="mb-4">
        {{-- @csrf --}}
        <div class="row g-2 align-items-end">
            <div class="col-auto">
                <label for="filter" class="form-label">Filter Tanggal:</label>
                <select name="filter" id="filter" class="form-select">
                    <option value="minggu_ini">Minggu Ini</option>
                    <option value="hari_ini">Hari Ini</option>
                    <option value="bulan_ini">Bulan Ini</option>
                    <option value="kloter_terbaru">Kloter Terbaru</option>
                    <option value="semua">Semua</option>
                </select>
            </div>

            <div class="col-auto">
                <label for="nama" class="form-label">Cari Nama:</label>
                <input type="text" name="nama" id="nama" class="form-control" placeholder="Ketik nama...">
            </div>

            <div class="col-auto mt-4">
                <a id="downloadExcel" class="btn btn-success">Unduh Excel</a>
            </div>
        </div>
    </form>

    <div class="table-responsive" id="dataContainer">
        @include('pimpinan.laporan_karyawan._table', ['data' => $data])
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
            const response = await fetch("{{ route('laporan.karyawan') }}?" + params.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) throw new Error('Gagal memuat data');

            const html = await response.text();
            if (container) {
                container.innerHTML = html;
            }
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
        window.location.href = "{{ route('laporan.karyawan') }}" + '?' + urlParams.toString();
    });
});
</script>
@endsection
