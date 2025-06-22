{{-- @extends('layouts.app_operator')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">Edit Karyawan</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('karyawan.update', $karyawan->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nama" class="form-label">Nama Karyawan</label>
            <input type="text" name="nama" class="form-control" value="{{ old('nama', $karyawan->nama) }}" required>
        </div>

        <div class="mb-3">
            <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
            <select name="jenis_kelamin" class="form-control" required>
                <option value="L" {{ $karyawan->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                <option value="P" {{ $karyawan->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="no_telepon" class="form-label">No Telepon</label>
            <input type="text" name="no_telepon" class="form-control" value="{{ old('no_telepon', $karyawan->no_telepon) }}">
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('karyawan.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection --}}

<!-- Modal Edit Karyawan -->
<div class="modal fade" id="editModal{{ $karyawan->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('karyawan.update', $karyawan->id) }}" method="POST" class="modal-content">
        @csrf
        @method('PUT')

        <div class="modal-header">
            <h5 class="modal-title">Edit Karyawan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
            <div class="mb-3">
            <label for="nama" class="form-label">Nama</label>
            <input id="nama" type="text" name="nama" class="form-control" required value="{{ old('nama', $karyawan->nama) }}">
            </div>

            <div class="mb-3">
            <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
            <select id="jenis_kelamin" name="jenis_kelamin" class="form-control" required>
                <option value="L" {{ old('jenis_kelamin', $karyawan->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                <option value="P" {{ old('jenis_kelamin', $karyawan->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
            </select>
            </div>

            <div class="mb-3">
            <label for="no_telepon" class="form-label">No Telepon</label>
            <input id="no_telepon" type="text" name="no_telepon" class="form-control" value="{{ old('no_telepon', $karyawan->no_telepon) }}">
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Update</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        </div>
        </form>
    </div>
</div>
