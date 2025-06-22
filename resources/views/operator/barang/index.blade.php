@extends('layouts.app_operator')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">
        Data Barang 
        @if($filter === 'produk') - Stock Produk Jadi
        @elseif($filter === 'pendukung') - Hal-hal Pendukung
        @endif
    </h3>
    
    {{-- Tombol untuk membuka modal --}}
    @if(request('filter') == 'produk' || request('filter') == 'pendukung')
        <button type="button" class="btn btn-sm btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createModal">
            Tambah {{ ucfirst(request('filter')) }}
        </button>

        @include('operator.barang.create', ['filter' => request('filter')])
    @endif

    <div class="d-flex justify-content-end mb-3">
        <form id="filterForm" method="GET" action="{{ route('barang.index') }}" class="w-auto" style="width: fit-content;">
            <input type="hidden" name="filter" value="{{ request('filter') }}">
            <input type="text" name="nama" id="namaInput" value="{{ request('nama') }}" class="form-control form-control-sm" placeholder="Cari nama barang...">
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            @if(request('filter'))
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        {{-- <th>Kategori</th> --}}
                        {{-- <th>Qty</th> --}}
                        @if(request('filter') != 'pendukung')
                            <th>Exp</th>
                        @endif
                        <th>Harga</th>
                        <th>Update Stock</th> {{-- Tambahan --}}
                        <th>Aksi</th> {{-- Kolom edit & hapus --}} 
                    </tr>
                </thead>
                <tbody>
                    @foreach($barangs as $i => $barang)
                        @php
                            $kategori = '-';
                            $kode = '-';
                            if ($barang->produk) {
                                $kategori = 'Produk';
                                $kode = $barang->produk->kode;
                            } elseif ($barang->pendukung) {
                                $kategori = 'Pendukung';
                                $kode = $barang->pendukung->kode;
                            }
                        @endphp
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $kode }}</td>
                            <td>{{ \Illuminate\Support\Str::title($barang->nama_barang) }}</td>
                            {{-- <td>{{ $kategori }}</td> --}}
                            {{-- <td>{{ $barang->qty ?? '-' }}</td> --}}
                            @if(request('filter') != 'pendukung')
                                <td>{{ $barang->exp ? \Carbon\Carbon::parse($barang->exp)->format('d-m-Y') : '-' }}</td>
                            @endif
                            <td>Rp {{ number_format($barang->harga, 0, ',', '.') }}</td>
                            <td>
                                <form action="{{ route('barang.updateQty', $barang->id) }}" method="POST" class="d-flex">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number" name="qty" value="{{ $barang->qty }}" class="form-control form-control-sm me-1" style="width:80px">
                                    <button type="submit" class="btn btn-sm btn-warning">-</button>
                                </form>
                            </td>
                            <td>
                                <!-- Tombol untuk membuka modal edit -->
                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editModal{{ $barang->id }}">Edit</button>
                                <!-- Modal Edit Barang -->
                                @include('operator.barang.edit', ['barang' => $barang])
                                {{-- Hapus --}}
                                <form action="{{ route('barang.destroy', $barang->id) }}" method="POST" class="formDeleteBarang" data-id="{{ $barang->id }}" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>                            
                        </tr>
                    @endforeach
                </tbody>
            @else
                <thead class="table-dark">
                    <tr>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Exp</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($barangs as $barang)
                        @php
                            $kode = $barang->produk->kode ?? $barang->pendukung->kode ?? '-';
                        @endphp
                        <tr>
                            <td>{{ $kode }}</td>
                            <td>{{ $barang->nama_barang }}</td>
                            <td>{{ $barang->exp ? \Carbon\Carbon::parse($barang->exp)->format('d-m-Y') : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            @endif
        </table>
    </div>
    
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

<script src="{{ asset('js/barang.js') }}"></script>

@endsection
