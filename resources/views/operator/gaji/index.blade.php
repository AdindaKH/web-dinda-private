@extends('layouts.app_operator')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">Daftar Kloter Gaji</h3>

    <form method="GET" class="mb-3">
        <div class="row g-2 align-items-center" style="justify-content: end;">
            <div class="col-auto">
                <select name="tahun" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Tahun</option>
                    @foreach($tahunList as $t)
                        <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="selesai" {{ $status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="belum" {{ $status == 'belum' ? 'selected' : '' }}>Belum</option>
                </select>
            </div>
        </div>
    </form>

    
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Nama Kloter</th>
                    <th>Ton Ikan</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Akhir</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kloters as $i => $kloter)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            <a href="{{ route('gaji.kloter.detail', $kloter->id) }}">
                                {{ $kloter->nama_kloter }}
                            </a>
                        </td>
                        <td>{{ number_format($kloter->tonIkan->jumlah_ton ?? 0, 2) }} Kg</td>
                        <td>
                            {{ $kloter->presensis->min('tanggal') 
                                ? \Carbon\Carbon::parse($kloter->presensis->min('tanggal'))->format('d-m-Y') 
                                : '-' }}
                        </td>
                        <td>
                            {{ $kloter->presensis->max('tanggal') 
                                ? \Carbon\Carbon::parse($kloter->presensis->max('tanggal'))->format('d-m-Y') 
                                : '-' }}
                        </td>
                        <td class="text-center">
                            @php
                                $isSelesai = in_array($kloter->id, $kloterSelesaiIds);
                            @endphp

                            @if ($isSelesai)
                                <span class="badge bg-success">Selesai</span>
                            @else
                                <form action="{{ route('gaji.kloter.selesai', $kloter->id) }}" method="POST" class="d-inline form-kloter-selesai">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm">Proses Gaji</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">Belum ada data Kloter.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/gaji.js') }}"></script>

@endsection