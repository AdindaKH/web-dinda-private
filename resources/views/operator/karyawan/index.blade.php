@extends('layouts.app_operator')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">Daftar Karyawan</h3>

    <div class="filter-x mb-3 d-flex justify-content-between align-items-center">
        <form id="filterForm" method="GET" action="{{ route('karyawan.index') }}" class="d-flex align-items-center mb-0">
            <input type="text" name="nama" id="namaInput" value="{{ request('nama') }}" class="form-control" placeholder="Cari nama karyawan..." style="max-width: 220px;">
            <input type="hidden" name="gender" id="genderInput" value="{{ request('gender') }}">
            <button type="button" id="toggleGenderBtn"
                class="btn btn-sm ms-2 {{ request('gender') === 'L' ? 'btn-outline-primary' : (request('gender') === 'P' ? 'btn-outline-danger' : 'btn-outline-secondary') }}"
                style="margin-left:10px;">
                {{ request('gender') ?? 'Semua' }}
            </button>
        </form>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
            Tambah Karyawan
        </button>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Jenis Kelamin</th>
                <th>No Telepon</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($karyawans as $i => $k)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $k->nama }}</td>
                <td>{{ $k->jenis_kelamin }}</td>
                <td>{{ $k->no_telepon ?? '-' }}</td>
                <td>
                    {{-- Tombol Edit --}}
                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editModal{{ $k->id }}">
                        Edit
                    </button>

                    {{-- Modal Edit --}}
                    @include('operator.karyawan.edit', ['karyawan' => $k])

                    {{-- Tombol Hapus --}}
                    <form action="{{ route('karyawan.destroy', $k->id) }}" method="POST" class="d-inline formDelete">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Modal Create --}}
@include('operator.karyawan.create')

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
<script src="{{ asset('js/karyawan.js') }}"></script>
@endsection
