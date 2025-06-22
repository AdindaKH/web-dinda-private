@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

@extends('layouts.app_operator')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">Presensi Harian Pekerja</h3>
    
    {{-- Pilih Kloter --}}
    <form id="formKloter" class="mb-3">
        <label for="kloter_id">Pilih Kloter</label>
        <div class="input-group i-g">
            <select name="kloter_id" id="kloter_id" class="form-control">
                @foreach($kloters as $k)
                    {{-- kode untuk --}}
                    <option value="{{ $k->id }}" {{ $selectedKloter->id == $k->id ? 'selected' : '' }}>
                        {{ $k->nama_kloter }}
                    </option>
                @endforeach
            </select>
            <!-- Tombol untuk membuka Modal -->
            {{-- <button type="button" class="btn btn-success">Buat Kloter Baru</button> --}}
            <form method="GET" class="mb-3" action="">
                <input type="hidden" name="buat_kloter" value="1">
                <button class="btn btn-success">Buat Kloter</button>
            </form>
            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editKloterModal">Edit Kloter</button>
            <button type="button" class="btn btn-outline-primary" id="btnLihatKloter">Lihat</button>
            @include('operator.presensi.edit')
        </div>
        <div class="filter-x mb-3 d-flex justify-content-between align-items-center">
            <input type="text" id="searchKaryawan" class="form-control mb-3" style="width: fit-content" placeholder="Cari nama karyawan...">
            
            <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#pilihKaryawanModal">
                Pilih Karyawan untuk Kloter Ini
            </button>
        </div>
        @include('operator.presensi._pilihKaryawan')
    </form>

    

    {{-- checkbox fitur otomatis --}}
    <div class="form-check form-switch mb-3">
        <input class="form-check-input" type="checkbox" id="modeOtomatis">
        <label class="form-check-label" for="modeOtomatis">Mode Otomatis (Gunakan Waktu Saat Ini)</label>
    </div>

    @if($karyawans->isEmpty())
        <div class="alert alert-warning">Belum ada karyawan yang dipilih untuk kloter ini.</div>
    @else
        {{-- Tabel Presensi --}}
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    {{-- <th>Jenis Kelamin</th> --}}
                    <th>Aksi Masuk</th>
                    <th>Jam Masuk</th>
                    <th>Aksi Pulang</th>
                    <th>Jam Pulang</th>
                    <th>Total Jam</th>
                </tr>
            </thead>
            <tbody>
                @foreach($karyawans as $i => $k)
                @php
                    $p = $presensis->get($k->id);
                    $totalJam = 0;
                    if ($p && $p->jam_masuk && $p->jam_pulang) {
                        $totalJam = \Carbon\Carbon::parse($p->jam_masuk)->diffInMinutes(\Carbon\Carbon::parse($p->jam_pulang)) / 60;
                    }
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $k->nama }}</td>
                    {{-- Aksi Masuk --}}
                    <td>
                        @if(!$p || !$p->jam_masuk)
                            <form method="POST" action="{{ route('presensi.masuk', $k->id) }}" class="formPresensi">
                                @csrf
                                <input type="hidden" name="kloter_id" value="{{ $selectedKloter->id }}">
                                <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                                <input type="hidden" name="jam" class="inputJam">
                                <input type="time" class="form-control inputManualJam d-none" name="jam_manual">
                                <button class="btn btn-success btn-sm btnSubmitMasuk mt-1">✓ Masuk</button>
                            </form>
                        @else
                            <span class="text-success">✓</span>
                        @endif
                    </td>

                    {{-- Jam Masuk --}}
                    <td>{{ $p->jam_masuk ?? '-' }}</td>

                    {{-- Aksi Pulang --}}
                    <td>
                        @if($p && !$p->jam_pulang)
                            <form method="POST" action="{{ route('presensi.pulang', $k->id) }}" class="formPresensi">
                                @csrf
                                <input type="hidden" name="kloter_id" value="{{ $selectedKloter->id }}">
                                <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                                <input type="hidden" name="jam" class="inputJam">
                                <input type="time" class="form-control inputManualJam d-none" name="jam_manual">
                                <button class="btn btn-warning btn-sm btnSubmitPulang mt-1">✓ Pulang</button>
                            </form>
                        @elseif($p)
                            <span class="text-warning">✓</span>
                        @else
                            <span>-</span>
                        @endif
                    </td>

                    {{-- Jam Pulang --}}
                    <td>{{ $p->jam_pulang ?? '-' }}</td>

                    {{-- Total Jam --}}
                    <td>{{ number_format($totalJam, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // SweetAlert sukses
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: @json(session('success')),
            showConfirmButton: false,
            timer: 2000
        });
    @endif

    // SweetAlert error validasi
    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Terjadi Kesalahan',
            html: `{!! implode('<br>', $errors->all()) !!}`,
        });
    @endif
</script>

<script src="{{ asset('js/presensi.js') }}"></script>
@endsection
